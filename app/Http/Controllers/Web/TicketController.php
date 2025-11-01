<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\DetalleTicket;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['cliente', 'empleado', 'detalleTickets'])->get();
        return Inertia::render('Tickets/Index', compact('tickets'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $empleados = Empleado::all();
        $productos = Producto::where('activo', true)->get();
        $servicios = Servicio::where('activo', true)->get();

        return Inertia::render('Tickets/Create', compact('clientes', 'empleados', 'productos', 'servicios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'empleado_id' => 'nullable|exists:empleados,id',
            'metodo_pago' => 'nullable|string',
            'fecha' => 'required|date',
            'productos' => 'array',
            'servicios' => 'array',
        ]);

        $ticket = Ticket::create([
            'cliente_id' => $data['cliente_id'] ?? null,
            'empleado_id' => $data['empleado_id'] ?? null,
            'metodo_pago' => $data['metodo_pago'] ?? null,
            'fecha' => $data['fecha'],
            'total' => 0,
        ]);

        $total = 0;

        // Agregar productos
        if(isset($data['productos'])) {
            foreach($data['productos'] as $item){
                $producto = Producto::find($item['id']);
                if($producto){
                    $subtotal = $producto->precio_venta * $item['cantidad'];
                    DetalleTicket::create([
                        'ticket_id' => $ticket->id,
                        'vendible_type' => Producto::class,
                        'vendible_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }
            }
        }

        // Agregar servicios
        if(isset($data['servicios'])) {
            foreach($data['servicios'] as $item){
                $servicio = Servicio::find($item['id']);
                if($servicio){
                    $subtotal = $servicio->precio * $item['cantidad'];
                    DetalleTicket::create([
                        'ticket_id' => $ticket->id,
                        'vendible_type' => Servicio::class,
                        'vendible_id' => $servicio->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $servicio->precio,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }
            }
        }

        $ticket->update(['total' => $total]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado correctamente.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['cliente', 'empleado', 'detalleTickets.vendible']);
        return Inertia::render('Tickets/Show', compact('ticket'));

        /* $ticket->load([
            'cliente',
            'empleado',
            'detalleTickets.vendible'
        ]); */

        /* return Inertia::render('Tickets/Show', [
            'ticket' => $ticket->toArray(), // convierte todo a array
        ]); */
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load('detalleTickets');
        $clientes = Cliente::all();
        $empleados = Empleado::all();
        $productos = Producto::where('activo', true)->get();
        $servicios = Servicio::where('activo', true)->get();

        return Inertia::render('Tickets/Edit', compact('ticket', 'clientes', 'empleados', 'productos', 'servicios'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'empleado_id' => 'nullable|exists:empleados,id',
            'metodo_pago' => 'nullable|string',
            'fecha' => 'required|date',
            'productos' => 'array',
            'servicios' => 'array',
        ]);

        $ticket->update([
            'cliente_id' => $data['cliente_id'] ?? null,
            'empleado_id' => $data['empleado_id'] ?? null,
            'metodo_pago' => $data['metodo_pago'] ?? null,
            'fecha' => $data['fecha'],
        ]);

        // Eliminar detalles anteriores
        $ticket->detalleTickets()->delete();

        $total = 0;

        // Reagregar productos y servicios (igual que en store)
        if(isset($data['productos'])){
            foreach($data['productos'] as $item){
                $producto = Producto::find($item['id']);
                if($producto){
                    $subtotal = $producto->precio_venta * $item['cantidad'];
                    DetalleTicket::create([
                        'ticket_id' => $ticket->id,
                        'vendible_type' => Producto::class,
                        'vendible_id' => $producto->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }
            }
        }

        if(isset($data['servicios'])){
            foreach($data['servicios'] as $item){
                $servicio = Servicio::find($item['id']);
                if($servicio){
                    $subtotal = $servicio->precio * $item['cantidad'];
                    DetalleTicket::create([
                        'ticket_id' => $ticket->id,
                        'vendible_type' => Servicio::class,
                        'vendible_id' => $servicio->id,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $servicio->precio,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }
            }
        }

        $ticket->update(['total' => $total]);

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado correctamente.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado correctamente.');
    }
}
