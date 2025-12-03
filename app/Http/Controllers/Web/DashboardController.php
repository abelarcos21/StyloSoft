<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use App\Models\Servicio;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //DATOS ESTADISTICOS DEL DASHBOARD
    public function index(){

        // Turnos/Agendas para hoy (usando fecha_hora_inicio)
        $turnosHoy = Agenda::whereDate('fecha_hora_inicio', today())
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->count();

        // Tickets pendientes de pago
        $ticketsPendientes = Ticket::where('estado_pago', 'pendiente')
            ->orWhere('estado_pago', 'parcial')
            ->count();

        // Ingresos del día (tickets pagados)
        $ingresosHoy = Ticket::whereDate('fecha', today())
            ->whereIn('estado_pago', ['pagado'])
            ->sum('total');

        // Ingresos del mes
        $ingresosMes = Ticket::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->whereIn('estado_pago', ['pagado'])
            ->sum('total');

        // Últimas 5 agendas (con el campo correcto fecha_hora_inicio)
        $ultimasAgendas = Agenda::with(['cliente', 'empleado', 'servicios'])
            ->orderBy('fecha_hora_inicio', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($agenda) {
                return [
                    'id' => $agenda->id,
                    'cliente' => $agenda->cliente->nombre_completo ?? 'Sin nombre',
                    'empleado' => $agenda->empleado->nombre_completo ?? 'Sin asignar',
                    'fecha' => Carbon::parse($agenda->fecha_hora_inicio)->format('d/m/Y'),
                    'hora' => Carbon::parse($agenda->fecha_hora_inicio)->format('H:i'),
                    'estado' => $agenda->estado,
                    'estado_texto' => ucfirst(str_replace('_', ' ', $agenda->estado)),
                    'total' => $agenda->servicios->sum('pivot.precio'),
                ];
            });

        // Servicios más vendidos (últimos 30 días)
        $serviciosMasVendidos = DetalleTicket::where('vendible_type', 'App\\Models\\Servicio')
            ->whereHas('ticket', function ($q) {
                $q->where('fecha', '>=', now()->subDays(30))
                  ->whereIn('estado_pago', ['pagado']);
            })
            ->select(
                'vendible_id',
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('SUM(subtotal) as total')
            )
            ->groupBy('vendible_id')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get()
            ->map(function ($detalle) {
                $servicio = Servicio::find($detalle->vendible_id);
                if (!$servicio) {
                    return null;
                }
                return [
                    'nombre' => $servicio->nombre,
                    'precio' => $servicio->precio,
                    'cantidad' => $detalle->cantidad,
                    'total' => $detalle->total,
                ];
            })
            ->filter() // Elimina servicios nulos (eliminados)
            ->values();

        // Productos más vendidos (últimos 30 días)
        $productosMasVendidos = DetalleTicket::where('vendible_type', 'App\\Models\\Producto')
            ->whereHas('ticket', function ($q) {
                $q->where('fecha', '>=', now()->subDays(30))
                  ->whereIn('estado_pago', ['pagado']);
            })
            ->select(
                'vendible_id',
                DB::raw('SUM(cantidad) as cantidad'),
                DB::raw('SUM(subtotal) as total')
            )
            ->groupBy('vendible_id')
            ->orderByDesc('cantidad')
            ->limit(10)
            ->get()
            ->map(function ($detalle) {
                $producto = Producto::find($detalle->vendible_id);
                if (!$producto) {
                    return null;
                }
                return [
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio_venta,
                    'cantidad' => $detalle->cantidad,
                    'total' => $detalle->total,
                    'stock' => $producto->stock,
                    'codigo' => $producto->codigo,
                    'marca' => $producto->marca,
                ];
            })
            ->filter() // Elimina productos nulos (eliminados)
            ->values();

        return Inertia::render('Dashboard', [
            'turnosHoy' => $turnosHoy,
            'ticketsPendientes' => $ticketsPendientes,
            'ingresosHoy' => (float) $ingresosHoy,
            'ingresosMes' => (float) $ingresosMes,
            'ultimasAgendas' => $ultimasAgendas,
            'serviciosMasVendidos' => $serviciosMasVendidos,
            'productosMasVendidos' => $productosMasVendidos,
        ]);
    }
}
