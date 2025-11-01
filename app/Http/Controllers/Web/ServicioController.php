<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicio;
use Inertia\Inertia;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::query();

        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%")
                ->orWhere('descripcion', 'like', "%{$request->search}%");
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado);
        }

        $servicios = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return inertia('Servicios/Index', [
            'servicios' => $servicios,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Servicios/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        Servicio::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'duracion_minutos' => $request->duracion_minutos,
            'activo' => $request->activo ?? true,
        ]);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado correctamente');
    }

    public function edit(Servicio $servicio)
    {
        return Inertia::render('Servicios/Edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        $servicio->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'duracion_minutos' => $request->duracion_minutos,
            'activo' => $request->activo ?? true,
        ]);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado correctamente');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado correctamente');
    }
}
