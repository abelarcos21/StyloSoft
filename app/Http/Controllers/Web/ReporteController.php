<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use App\Models\Empleado;
use App\Models\Servicio;
use App\Models\Producto;
use App\Models\Agenda;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class ReporteController extends Controller
{
    //
    public function index()
    {
        $empleados = Empleado::activos()
            ->orderBy('nombre')
            ->get()
            ->map(fn($e) => ['id' => $e->id, 'text' => $e->nombre_completo]);

        return Inertia::render('Reportes/Index', [
            'empleados' => $empleados,
        ]);
    }

    /**
     * Reporte de Ventas (Tickets)
     */
    public function ventas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'empleado_id' => 'nullable|exists:empleados,id',
            'metodo_pago' => 'nullable|in:efectivo,tarjeta,transferencia,mixto',
        ]);

        $query = Ticket::whereBetween('fecha', [
            $request->fecha_inicio . ' 00:00:00',
            $request->fecha_fin . ' 23:59:59'
        ]);

        if ($request->empleado_id) {
            $query->where('empleado_id', $request->empleado_id);
        }

        if ($request->metodo_pago) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        $tickets = $query->with(['cliente', 'empleado'])
            ->orderBy('fecha', 'desc')
            ->get();

        // Estadísticas
        $totalVentas = $tickets->whereIn('estado_pago', ['pagado'])->sum('total');
        $totalTickets = $tickets->count();
        $ticketPromedio = $totalTickets > 0 ? $totalVentas / $totalTickets : 0;

        // Por método de pago
        $porMetodoPago = $tickets->groupBy('metodo_pago')->map(function ($group) {
            return [
                'cantidad' => $group->count(),
                'total' => $group->whereIn('estado_pago', ['pagado'])->sum('total'),
            ];
        });

        // Por estado de pago
        $porEstadoPago = $tickets->groupBy('estado_pago')->map(function ($group) {
            return [
                'cantidad' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        // Ventas por día
        $ventasPorDia = $tickets->groupBy(function ($ticket) {
            return Carbon::parse($ticket->fecha)->format('Y-m-d');
        })->map(function ($group, $fecha) {
            return [
                'fecha' => Carbon::parse($fecha)->format('d/m/Y'),
                'cantidad' => $group->count(),
                'total' => $group->whereIn('estado_pago', ['pagado'])->sum('total'),
            ];
        })->values();

        return response()->json([
            'resumen' => [
                'total_ventas' => $totalVentas,
                'total_tickets' => $totalTickets,
                'ticket_promedio' => $ticketPromedio,
            ],
            'por_metodo_pago' => $porMetodoPago,
            'por_estado_pago' => $porEstadoPago,
            'ventas_por_dia' => $ventasPorDia,
            'tickets' => $tickets->map(fn($t) => [
                'id' => $t->id,
                'numero_ticket' => $t->numero_ticket,
                'cliente' => $t->cliente?->nombre_completo ?? 'Cliente General',
                'empleado' => $t->empleado?->nombre_completo ?? 'N/A',
                'fecha' => $t->fecha->format('d/m/Y H:i'),
                'metodo_pago' => $t->metodo_pago,
                'estado_pago' => $t->estado_pago,
                'subtotal' => number_format($t->subtotal, 2),
                'total' => number_format($t->total, 2),
            ]),
        ]);
    }

    /**
     * Reporte de Servicios
     */
    public function servicios(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $servicios = DetalleTicket::where('vendible_type', 'App\\Models\\Servicio')
            ->whereHas('ticket', function ($q) use ($request) {
                $q->whereBetween('fecha', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ])->whereIn('estado_pago', ['pagado']);
            })
            ->select(
                'vendible_id',
                DB::raw('COUNT(*) as cantidad'),
                DB::raw('SUM(subtotal) as total'),
                DB::raw('AVG(precio_unitario) as precio_promedio')
            )
            ->groupBy('vendible_id')
            ->orderByDesc('cantidad')
            ->get()
            ->map(function ($detalle) {
                $servicio = Servicio::find($detalle->vendible_id);
                if (!$servicio) return null;

                return [
                    'nombre' => $servicio->nombre,
                    'cantidad' => $detalle->cantidad,
                    'precio_promedio' => number_format($detalle->precio_promedio, 2),
                    'total' => number_format($detalle->total, 2),
                    'duracion' => $servicio->duracion,
                ];
            })
            ->filter()
            ->values();

        $totalServicios = $servicios->sum('cantidad');
        $totalIngresos = $servicios->sum(fn($s) => (float) str_replace(',', '', $s['total']));

        return response()->json([
            'resumen' => [
                'total_servicios' => $totalServicios,
                'total_ingresos' => $totalIngresos,
            ],
            'servicios' => $servicios,
        ]);
    }

    /**
     * Reporte de Productos
     */
    public function productos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $productos = DetalleTicket::where('vendible_type', 'App\\Models\\Producto')
            ->whereHas('ticket', function ($q) use ($request) {
                $q->whereBetween('fecha', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ])->whereIn('estado_pago', ['pagado']);
            })
            ->select(
                'vendible_id',
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('SUM(subtotal) as total'),
                DB::raw('AVG(precio_unitario) as precio_promedio')
            )
            ->groupBy('vendible_id')
            ->orderByDesc('cantidad')
            ->get()
            ->map(function ($detalle) {
                $producto = Producto::find($detalle->vendible_id);
                if (!$producto) return null;

                return [
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'marca' => $producto->marca,
                    'cantidad' => $detalle->cantidad,
                    'precio_promedio' => number_format($detalle->precio_promedio, 2),
                    'total' => number_format($detalle->total, 2),
                    'stock_actual' => $producto->stock,
                ];
            })
            ->filter()
            ->values();

        $totalProductos = $productos->sum('cantidad');
        $totalIngresos = $productos->sum(fn($p) => (float) str_replace(',', '', $p['total']));

        return response()->json([
            'resumen' => [
                'total_productos' => $totalProductos,
                'total_ingresos' => $totalIngresos,
            ],
            'productos' => $productos,
        ]);
    }

    /**
     * Reporte de Empleados
     */
    public function empleados(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $empleados = Empleado::activos()
            ->with(['tickets' => function ($q) use ($request) {
                $q->whereBetween('fecha', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ])->whereIn('estado_pago', ['pagado']);
            }])
            ->get()
            ->map(function ($empleado) use ($request) {
                $tickets = $empleado->tickets;
                $agendas = Agenda::where('empleado_id', $empleado->id)
                    ->whereBetween('fecha_hora_inicio', [
                        $request->fecha_inicio . ' 00:00:00',
                        $request->fecha_fin . ' 23:59:59'
                    ])
                    ->whereIn('estado', ['completada'])
                    ->count();

                return [
                    'nombre' => $empleado->nombre_completo,
                    'puesto' => $empleado->puesto,
                    'total_tickets' => $tickets->count(),
                    'total_ventas' => number_format($tickets->sum('total'), 2),
                    'ticket_promedio' => $tickets->count() > 0 
                        ? number_format($tickets->avg('total'), 2) 
                        : '0.00',
                    'agendas_completadas' => $agendas,
                ];
            })
            ->sortByDesc(fn($e) => (float) str_replace(',', '', $e['total_ventas']))
            ->values();

        return response()->json([
            'empleados' => $empleados,
        ]);
    }
}
