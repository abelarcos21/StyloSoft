<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empleado;
use Inertia\Inertia;


class EmpleadoController extends Controller
{
    public function index(Request $request){
        /* $empleados = Empleado::orderBy('id', 'desc')->get();
        return Inertia::render('Empleados/Index', compact('empleados')); */

        $search = $request->input('search');
        $estado = $request->input('estado');

        $empleados = \App\Models\Empleado::query()
            ->when($search, fn($q) =>
                $q->where('nombre', 'like', "%$search%")
                ->orWhere('apellido', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
            )
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return inertia('Empleados/Index', [
            'empleados' => $empleados,
            'filters' => [
                'search' => $search,
                'estado' => $estado,
            ]
        ]);
    }

    public function create(){
        return Inertia::render('Empleados/Create');
    }

    public function store(Request $request){
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email|unique:empleados,email,' . ($empleado->id ?? 'NULL'),
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'puesto' => 'required|string|max:255',
            'salario' => 'nullable|numeric',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        return Inertia::render('Empleados/Edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email|unique:empleados,email,' . ($empleado->id ?? 'NULL'),
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'puesto' => 'required|string|max:255',
            'salario' => 'nullable|numeric',
            'fecha_ingreso' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
