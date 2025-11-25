<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Producto;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['cliente', 'empleado']);

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_ticket', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($subQ) use ($search) {
                        $subQ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro de estado de pago
        if ($request->estado_pago) {
            $query->where('estado_pago', $request->estado_pago);
        }

        // Filtro de método de pago
        if ($request->metodo_pago) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        // Filtro de fecha
        if ($request->fecha_inicio && $request->fecha_fin) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        } elseif ($request->fecha_inicio) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        } elseif ($request->fecha_fin) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        // Ordenar por fecha descendente
        $query->orderBy('fecha', 'desc');

        $tickets = $query->paginate(15)->through(function ($ticket) {
            return [
                'id' => $ticket->id,
                'numero_ticket' => $ticket->numero_ticket,
                'cliente' => $ticket->cliente ? [
                    'id' => $ticket->cliente->id,
                    'nombre_completo' => $ticket->cliente->nombre_completo,
                ] : null,
                'empleado' => $ticket->empleado ? [
                    'id' => $ticket->empleado->id,
                    'nombre_completo' => $ticket->empleado->nombre_completo,
                ] : null,
                'subtotal' => number_format($ticket->subtotal, 2),
                'descuento' => number_format($ticket->descuento, 2),
                'impuesto' => number_format($ticket->impuesto, 2),
                'total' => number_format($ticket->total, 2),
                'metodo_pago' => $ticket->metodo_pago,
                'estado_pago' => $ticket->estado_pago,
                'fecha' => $ticket->fecha->format('d/m/Y H:i'),
                'created_at' => $ticket->created_at->format('d/m/Y'),
            ];
        });

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'estado_pago', 'metodo_pago', 'fecha_inicio', 'fecha_fin']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'clientes' => Cliente::activos()->orderBy('nombre')->get()
                ->map(fn($c) => ['id' => $c->id, 'text' => $c->nombre_completo . ' - ' . $c->telefono]),
            'empleados' => Empleado::activos()->orderBy('nombre')->get()
                ->map(fn($e) => ['id' => $e->id, 'text' => $e->nombre_completo . ' (' . $e->puesto . ')']),
            'servicios' => Servicio::activos()->orderBy('nombre')->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'text' => $s->nombre . ' - $' . number_format($s->precio, 2),
                    'precio' => $s->precio,
                    'tipo' => 'servicio',
                ]),
            'productos' => Producto::activos()->conStock()->orderBy('nombre')->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'text' => $p->nombre . ' - $' . number_format($p->precio_venta, 2) . ' (Stock: ' . $p->stock . ')',
                    'precio' => $p->precio_venta,
                    'stock' => $p->stock,
                    'tipo' => 'producto',
                ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'items' => 'required|array|min:1',
            'items.*.tipo' => 'required|in:servicio,producto',
            'items.*.id' => 'required|integer',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Crear ticket
            $ticket = Ticket::create([
                'cliente_id' => $validated['cliente_id'],
                'empleado_id' => $validated['empleado_id'],
                'subtotal' => 0,
                'descuento' => $validated['descuento'] ?? 0,
                'impuesto' => 0,
                'total' => 0,
                'metodo_pago' => $validated['metodo_pago'],
                'estado_pago' => 'pagado',
                'fecha' => now(),
                'notas' => $validated['notas'],
            ]);

            // Agregar detalles
            foreach ($validated['items'] as $item) {
                $tipo = $item['tipo'] === 'servicio' ? Servicio::class : Producto::class;
                
                DetalleTicket::create([
                    'ticket_id' => $ticket->id,
                    'vendible_type' => $tipo,
                    'vendible_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento' => 0,
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                    'empleado_id' => $validated['empleado_id'],
                ]);
            }

            // Calcular totales
            $ticket->calcularImpuesto(16); // IVA 16%

            DB::commit();

            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }
        

    public function show(Ticket $ticket)
    {
        $ticket->load(['cliente', 'empleado', 'detalles.vendible', 'detalles.empleado']);

        return Inertia::render('Tickets/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'numero_ticket' => $ticket->numero_ticket,
                'cliente' => $ticket->cliente ? [
                    'id' => $ticket->cliente->id,
                    'nombre_completo' => $ticket->cliente->nombre_completo,
                    'telefono' => $ticket->cliente->telefono,
                    'email' => $ticket->cliente->email,
                ] : null,
                'empleado' => $ticket->empleado ? [
                    'id' => $ticket->empleado->id,
                    'nombre_completo' => $ticket->empleado->nombre_completo,
                    'puesto' => $ticket->empleado->puesto,
                ] : null,
                'detalles' => $ticket->detalles->map(fn($detalle) => [
                    'id' => $detalle->id,
                    'tipo' => $detalle->tipo_vendible,
                    'nombre' => $detalle->vendible->nombre,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => number_format($detalle->precio_unitario, 2),
                    'descuento' => number_format($detalle->descuento, 2),
                    'subtotal' => number_format($detalle->subtotal, 2),
                ]),
                'subtotal' => number_format($ticket->subtotal, 2),
                'descuento' => number_format($ticket->descuento, 2),
                'impuesto' => number_format($ticket->impuesto, 2),
                'total' => number_format($ticket->total, 2),
                'metodo_pago' => $ticket->metodo_pago,
                'estado_pago' => $ticket->estado_pago,
                'fecha' => $ticket->fecha->format('d/m/Y H:i'),
                'notas' => $ticket->notas,
                'created_at' => $ticket->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load(['detalles.vendible']);

        return Inertia::render('Tickets/Edit', [
            'ticket' => [
                'id' => $ticket->id,
                'numero_ticket' => $ticket->numero_ticket,
                'cliente_id' => $ticket->cliente_id,
                'empleado_id' => $ticket->empleado_id,
                'metodo_pago' => $ticket->metodo_pago,
                'estado_pago' => $ticket->estado_pago,
                'descuento' => $ticket->descuento,
                'notas' => $ticket->notas,
                'items' => $ticket->detalles->map(fn($detalle) => [
                    'tipo' => $detalle->es_servicio ? 'servicio' : 'producto',
                    'id' => $detalle->vendible_id,
                    'nombre' => $detalle->vendible->nombre,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                ]),
            ],
            'clientes' => Cliente::activos()->orderBy('nombre')->get()
                ->map(fn($c) => ['id' => $c->id, 'text' => $c->nombre_completo . ' - ' . $c->telefono]),
            'empleados' => Empleado::activos()->orderBy('nombre')->get()
                ->map(fn($e) => ['id' => $e->id, 'text' => $e->nombre_completo . ' (' . $e->puesto . ')']),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'estado_pago' => 'required|in:pendiente,pagado,parcial',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        $ticket->update([
            'metodo_pago' => $validated['metodo_pago'],
            'estado_pago' => $validated['estado_pago'],
            'notas' => $validated['notas'],
        ]);

        if (isset($validated['descuento'])) {
            $ticket->aplicarDescuento($validated['descuento']);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado exitosamente.');
    }

    public function destroy(Ticket $ticket)
    { 
        $ticket->delete();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }

   /*  public function pdf(Ticket $ticket){

        $pdf = Pdf::loadView('tickets.pdf', compact('ticket'))
                  ->setPaper('58mm');

        return $pdf->stream("ticket-{$ticket->id}.pdf");
    } */

    /**
     * Imprimir ticket
     */
    public function imprimir(Ticket $ticket)
    {
        $ticket->load(['cliente', 'empleado', 'detalles.vendible']);

        return Inertia::render('Tickets/Imprimir', [
            'ticket' => [
                'numero_ticket' => $ticket->numero_ticket,
                'cliente' => $ticket->cliente?->nombre_completo ?? 'Cliente General',
                'empleado' => $ticket->empleado->nombre_completo,
                'detalles' => $ticket->detalles->map(fn($detalle) => [
                    'nombre' => $detalle->vendible->nombre,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => number_format($detalle->precio_unitario, 2),
                    'subtotal' => number_format($detalle->subtotal, 2),
                ]),
                'subtotal' => number_format($ticket->subtotal, 2),
                'descuento' => number_format($ticket->descuento, 2),
                'impuesto' => number_format($ticket->impuesto, 2),
                'total' => number_format($ticket->total, 2),
                'metodo_pago' => $ticket->metodo_pago,
                'fecha' => $ticket->fecha->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Estadísticas de ventas
     */
    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth();
        $fechaFin = $request->fecha_fin ?? now()->endOfMonth();

        $tickets = Ticket::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'pagado')
            ->get();

        return response()->json([
            'total_ventas' => $tickets->sum('total'),
            'total_tickets' => $tickets->count(),
            'promedio_ticket' => $tickets->avg('total'),
            'por_metodo_pago' => $tickets->groupBy('metodo_pago')->map->sum('total'),
        ]);
    }
}
