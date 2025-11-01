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
    public function index()
    {
        $agendas = Agenda::with(['cliente', 'empleado', 'servicio'])
            ->latest()
            ->get();

        return Inertia::render('Agendas/Index', [
            'agendas' => $agendas
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        Agenda::create($validated);

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda creada correctamente.');
    }

    public function show(Agenda $agenda)
    {
        $agenda->load(['cliente', 'empleado', 'servicio']);
        return Inertia::render('Agendas/Show', [
            'agenda' => $agenda
        ]);
    }

    public function edit(Agenda $agenda)
    {
        $agenda->load(['cliente', 'empleado', 'servicio']);

        $clientes = Cliente::all(['id', 'nombre']);
        $empleados = Empleado::all(['id', 'nombre']);
        $servicios = Servicio::all(['id', 'nombre', 'precio']);

        return Inertia::render('Agendas/Edit', [
            'agenda' => $agenda,
            'clientes' => $clientes,
            'empleados' => $empleados,
            'servicios' => $servicios,
        ]);
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'empleado_id' => 'required|exists:empleados,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,confirmada,cancelada,completada',
        ]);

        $agenda->update($validated);

        return redirect()->route('agendas.index')
            ->with('success', 'Agenda actualizada correctamente.');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return redirect()->route('agendas.index')
            ->with('success', 'Agenda eliminada correctamente.');
    }
}
