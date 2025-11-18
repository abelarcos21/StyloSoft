<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;
use Inertia\Inertia;

class AgendaController extends Controller
{
    public function index(Request $request)
    {

        $query = Agenda::with(['cliente', 'empleado', 'servicios']);

        if ($request->filled('search')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$request->search}%")
                                                    ->orWhere('apellido', 'like', "%{$request->search}%"))
                ->orWhereHas('empleado', fn($q) => $q->where('nombre', 'like', "%{$request->search}%")
                                                        ->orWhere('apellido', 'like', "%{$request->search}%"));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        //filtros por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $agendas = $query->orderBy('fecha_hora', 'desc')->paginate(10)->withQueryString();

        return inertia('Agendas/Index', [
            'agendas' => $agendas,
            'filters' => $request->only(['search', 'estado', 'tipo']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Agendas/Create', [
            'clientes' => Cliente::select('id', 'nombre')->get(),
            'empleados' => Empleado::select('id', 'nombre')->get(),
            'servicios' => Servicio::select('id', 'nombre', 'precio')->get(),
        ]);
    }

    public function store(Request $request){

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'servicios'   => 'required|array|min:1',
            'servicios.*' => 'exists:servicios,id',
            'tipo' => 'required|in:flash,local',
            'fecha_hora' => 'nullable|date',
            'estado' => 'required'
        ]);

        // Si es FLASH, poner fecha actual
        if ($validated['tipo'] === 'flash') {
            $validated['fecha_hora'] = now();
        }

        $agenda = Agenda::create([
            'cliente_id' => $validated['cliente_id'],
            'empleado_id' => $validated['empleado_id'],
            'fecha_hora' => $validated['tipo'] === 'flash' ? now() : $validated['fecha_hora'],
            'estado' => $validated['estado'],
            'tipo' => $validated['tipo']
        ]);

        // Relación muchos a muchos
        /*  foreach ($validated['servicios'] as $id) {
            $agenda->servicios()->attach($id);
        } */
        $agenda->servicios()->attach($validated['servicios']);

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda creada correctamente.');
    }

    public function show(Agenda $agenda){

        $agenda->load([
            'cliente:id,nombre',
            'empleado:id,nombre',
            'servicios:id,nombre,precio'
        ]);

        return Inertia::render('Agendas/Show', [
            'agenda' => $agenda
        ]);
    }

    public function edit(Agenda $agenda){

        // Cargar relaciones necesarias
        $agenda->load(['cliente:id,nombre', 'empleado:id,nombre', 'servicios:id,nombre,precio']);

        // Listas para selects
        $clientes = Cliente::select('id', 'nombre')->orderBy('nombre')->get();
        $empleados = Empleado::select('id', 'nombre')->orderBy('nombre')->get();
        $servicios = Servicio::select('id', 'nombre', 'precio')->orderBy('nombre')->get();

        return Inertia::render('Agendas/Edit', [
            'agenda' => $agenda,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'servicios' => $servicios,
        ]);
    }


    public function update(Request $request, Agenda $agenda){

        $validated = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'empleado_id'  => 'required|exists:empleados,id',
            'servicios'    => 'required|array|min:1',
            'servicios.*'  => 'exists:servicios,id',
            'fecha_hora'   => 'required|date',
            'estado'       => 'required|in:pendiente,confirmada,cancelada,completada',
            'tipo'         => 'required|in:flash,local',
        ]);

        $agenda->update([
            'cliente_id' => $validated['cliente_id'],
            'empleado_id' => $validated['empleado_id'],
            'fecha_hora' => $validated['tipo'] === 'flash' ? now() : $validated['fecha_hora'],
            'estado' => $validated['estado'],
            'tipo' => $validated['tipo']
        ]);

        // Sincroniza servicios: agrega nuevos y elimina los que quitaron
        $agenda->servicios()->sync($validated['servicios']);

        return redirect()
            ->route('agendas.index')
            ->with('success', 'Agenda actualizada correctamente.');
    }



    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return redirect()->route('agendas.index')
            ->with('success', 'Agenda eliminada correctamente.');
    }
}
