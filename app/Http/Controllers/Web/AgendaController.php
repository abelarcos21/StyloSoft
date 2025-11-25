<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\DetalleTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Inertia\Inertia;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
       $query = Agenda::with(['cliente', 'empleado', 'servicios'])
            ->orderBy('fecha_hora_inicio', 'desc');

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('cliente', function ($subQ) use ($search) {
                    $subQ->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('telefono', 'like', "%{$search}%");
                })
                ->orWhereHas('empleado', function ($subQ) use ($search) {
                    $subQ->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%");
                });
            });
        }

        // Filtro de estado
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        // Filtro de tipo
        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro de fecha (opcional)
        if ($request->fecha) {
            $query->whereDate('fecha_hora_inicio', $request->fecha);
        }

        $agendas = $query->paginate(10)->through(function ($agenda) {
            return [
                'id' => $agenda->id,
                'cliente' => [
                    'id' => $agenda->cliente->id,
                    'nombre' => $agenda->cliente->nombre,
                    'apellido' => $agenda->cliente->apellido,
                    'telefono' => $agenda->cliente->telefono,
                    'nombre_completo' => $agenda->cliente->nombre_completo,
                ],
                'empleado' => [
                    'id' => $agenda->empleado->id,
                    'nombre' => $agenda->empleado->nombre,
                    'apellido' => $agenda->empleado->apellido,
                    'puesto' => $agenda->empleado->puesto,
                    'nombre_completo' => $agenda->empleado->nombre_completo,
                ],
                'servicios' => $agenda->servicios->map(function ($servicio) {
                    return [
                        'id' => $servicio->id,
                        'nombre' => $servicio->nombre,
                        'precio' => number_format($servicio->pivot->precio, 2),
                        'duracion_minutos' => $servicio->pivot->duracion_minutos,
                    ];
                }),
                'fecha_hora_inicio' => $agenda->fecha_hora_inicio->format('Y-m-d H:i:s'),
                'fecha_hora_fin' => $agenda->fecha_hora_fin->format('Y-m-d H:i:s'),
                'fecha_formato' => $agenda->fecha_hora_inicio->format('d/m/Y H:i'),
                'tipo' => $agenda->tipo,
                'estado' => $agenda->estado,
                'total' => number_format($agenda->total, 2),
                'duracion_total' => $agenda->duracion_total,
                'notas' => $agenda->notas,
            ];
        });

        return Inertia::render('Agendas/Index', [
            'agendas' => $agendas,
            'filters' => $request->only(['search', 'estado', 'tipo', 'fecha']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Agendas/Create', [

            'clientes' => Cliente::activos()
                ->orderBy('nombre')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'nombre_completo' => $c->nombre_completo,
                    'telefono' => $c->telefono,
                ]),
            'empleados' => Empleado::activos()
                ->orderBy('nombre')
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'nombre_completo' => $e->nombre_completo,
                    'puesto' => $e->puesto,
                ]),
            'servicios' => Servicio::activos()
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'precio' => $s->precio,
                    'duracion_minutos' => $s->duracion_minutos,
                    'categoria' => $s->categoria,
                ]),
        ]);
    }

    public function store(Request $request){

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_hora_inicio' => 'required|date|after:today',
            'tipo' => 'required|in:express,estandar',
            'notas' => 'nullable|string|max:500',
            'servicios' => 'required|array|min:1',
            'servicios.*' => 'required|exists:servicios,id',
        ]);

        // Calcular fecha_hora_fin basado en duración de servicios y FORZAMOS convertir a número entero 
        $duracionTotal = (int) Servicio::whereIn('id', $validated['servicios'])
            ->sum('duracion_minutos');

        $fechaInicio = Carbon::parse($validated['fecha_hora_inicio']);
        $fechaFin = $fechaInicio->copy()->addMinutes($duracionTotal);

        $agenda = Agenda::create([
            'cliente_id' => $validated['cliente_id'],
            'empleado_id' => $validated['empleado_id'],
            'fecha_hora_inicio' => $fechaInicio,
            'fecha_hora_fin' => $fechaFin,
            'tipo' => $validated['tipo'],
            'estado' => 'pendiente',
            'notas' => $validated['notas'],
        ]);

        // Adjuntar servicios con sus precios y duraciones históricas
        foreach ($validated['servicios'] as $servicioId) {
            $servicio = Servicio::find($servicioId);
            $agenda->servicios()->attach($servicio->id, [
                'precio' => (float) $servicio->precio,
                'duracion_minutos' => (int) $servicio->duracion_minutos,
                'orden' => 0,
            ]);
        }

        // Verificar conflictos de horario
        if ($agenda->hayConflictoHorario()) {
            return back()->with('warning', 'Advertencia: Ya existe una cita en ese horario para el empleado seleccionado.');
        }

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda creada exitosamente.');
        
    }

    public function show(Agenda $agenda){

        $agenda->load(['cliente', 'empleado', 'servicios']);

        return Inertia::render('Agendas/Show', [
            'agenda' => [
                'id' => $agenda->id,
                'cliente' => [
                    'id' => $agenda->cliente->id,
                    'nombre_completo' => $agenda->cliente->nombre_completo,
                    'telefono' => $agenda->cliente->telefono,
                    'email' => $agenda->cliente->email,
                ],
                'empleado' => [
                    'id' => $agenda->empleado->id,
                    'nombre_completo' => $agenda->empleado->nombre_completo,
                    'puesto' => $agenda->empleado->puesto,
                    'telefono' => $agenda->empleado->telefono,
                ],
                'servicios' => $agenda->servicios->map(function ($servicio) {
                    return [
                        'id' => $servicio->id,
                        'nombre' => $servicio->nombre,
                        'precio' => number_format($servicio->pivot->precio, 2),
                        'duracion_minutos' => $servicio->pivot->duracion_minutos,
                    ];
                }),
                'fecha_hora_inicio' => $agenda->fecha_hora_inicio->format('Y-m-d H:i:s'),
                'fecha_hora_fin' => $agenda->fecha_hora_fin->format('Y-m-d H:i:s'),
                'fecha_formato' => $agenda->fecha_hora_inicio->format('d/m/Y'),
                'hora_inicio' => $agenda->fecha_hora_inicio->format('H:i'),
                'hora_fin' => $agenda->fecha_hora_fin->format('H:i'),
                'tipo' => $agenda->tipo,
                'estado' => $agenda->estado,
                'total' => number_format($agenda->total, 2),
                'duracion_total' => $agenda->duracion_total,
                'notas' => $agenda->notas,
                'cancelado_por' => $agenda->cancelado_por,
                'razon_cancelacion' => $agenda->razon_cancelacion,
                'created_at' => $agenda->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function edit(Agenda $agenda){

        $agenda->load(['cliente', 'empleado', 'servicios']);

        return Inertia::render('Agendas/Edit', [
            'agenda' => [
                'id' => $agenda->id,
                'cliente_id' => $agenda->cliente_id,
                'empleado_id' => $agenda->empleado_id,
                'fecha_hora_inicio' => $agenda->fecha_hora_inicio->format('Y-m-d\TH:i'),
                'tipo' => $agenda->tipo,
                'estado' => $agenda->estado,
                'notas' => $agenda->notas,
                'servicios_ids' => $agenda->servicios->pluck('id'),
                'servicios' => $agenda->servicios->map(fn($s) => [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'precio' => $s->pivot->precio,
                    'duracion_minutos' => $s->pivot->duracion_minutos,
                ]),
            ],
            'clientes' => Cliente::activos()
                ->orderBy('nombre')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->id,
                    'nombre_completo' => $c->nombre_completo,
                    'telefono' => $c->telefono,
                ]),
            'empleados' => Empleado::activos()
                ->orderBy('nombre')
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'nombre_completo' => $e->nombre_completo,
                    'puesto' => $e->puesto,
                ]),
            'servicios' => Servicio::activos()
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'precio' => $s->precio,
                    'duracion_minutos' => $s->duracion_minutos,
                    'categoria' => $s->categoria,
                ]),
        ]);
    }


    public function update(Request $request, Agenda $agenda){

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_hora_inicio' => 'required|date',
            'tipo' => 'required|in:express,estandar',
            'estado' => 'required|in:pendiente,confirmada,en_proceso,completada,cancelada,no_asistio',
            'notas' => 'nullable|string|max:500',
            'servicios' => 'required|array|min:1',
            'servicios.*' => 'required|exists:servicios,id',
        ]);

        // Calcular fecha_hora_fin (FIX aplicado INT)
        $duracionTotal = (int) Servicio::whereIn('id', array_column($validated['servicios'], 'id'))
            ->sum('duracion_minutos');

        $fechaInicio = Carbon::parse($validated['fecha_hora_inicio']);
        $fechaFin = $fechaInicio->copy()->addMinutes($duracionTotal);

        $agenda->update([
            'cliente_id' => $validated['cliente_id'],
            'empleado_id' => $validated['empleado_id'],
            'fecha_hora_inicio' => $fechaInicio,
            'fecha_hora_fin' => $fechaFin,
            'tipo' => $validated['tipo'],
            'estado' => $validated['estado'],
            'notas' => $validated['notas'],
        ]);

        // Sincronizar servicios
        $serviciosSync = [];
        foreach ($validated['servicios'] as $index => $servicioId) {
            $servicio = Servicio::find($servicioId);
            $serviciosSync[$servicioId] = [
                'precio' => $servicio->precio,
                'duracion_minutos' => $servicio->duracion_minutos,
                'orden' => $index,
            ];
        }
        $agenda->servicios()->sync($serviciosSync);

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda actualizada exitosamente.');

    }

    /**
     * Cancelar una agenda
     */
    public function cancelar(Request $request, Agenda $agenda)
    {
        $request->validate([
            'razon' => 'nullable|string|max:500',
        ]);

        $agenda->cancelar(
            $request->razon ?? 'Sin razón especificada',
            $request->cancelado_por ?? 'sistema'
        );

        return back()->with('success', 'Agenda cancelada exitosamente.');
    }

    /**
     * Confirmar una agenda
     */
    public function confirmar(Agenda $agenda)
    {
        $agenda->confirmar();
        return back()->with('success', 'Agenda confirmada exitosamente.');
    }

    /**
     * Completar una agenda
     */
    public function completar(Agenda $agenda)
    {
        $agenda->completar();
        return back()->with('success', 'Agenda completada exitosamente.');
    }

    /**
     * Marcar como no asistió
     */
    public function noAsistio(Agenda $agenda)
    {
        $agenda->marcarNoAsistio();
        return back()->with('success', 'Marcado como no asistió.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return redirect()->route('agendas.index')
            ->with('success', 'Agenda eliminada exitosamente.');
    }


}
