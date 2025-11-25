<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Inertia\Inertia;

class ClienteController extends Controller
{

    public function index(Request $request)
    {
       $query = Cliente::query();

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        // Filtro de género
        if ($request->genero) {
            $query->where('genero', $request->genero);
        }

        // Filtro de marketing
        if ($request->marketing !== null) {
            $query->where('acepta_marketing', $request->marketing);
        }

        // Ordenar
        $query->orderBy('created_at', 'desc');

        $clientes = $query->paginate(10)->through(function ($cliente) {
            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'nombre_completo' => $cliente->nombre_completo,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'fecha_nacimiento' => $cliente->fecha_nacimiento?->format('d/m/Y'),
                'genero' => $cliente->genero,
                'acepta_marketing' => $cliente->acepta_marketing,
                'puntos_fidelidad' => $cliente->puntos_fidelidad,
                'ultima_visita' => $cliente->ultima_visita?->format('d/m/Y'),
                'notas' => $cliente->notas,
                'created_at' => $cliente->created_at->format('d/m/Y'),
            ];
        });

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'genero', 'marketing']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'nullable|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'nullable|in:masculino,femenino,otro,prefiero_no_decir',
            'acepta_marketing' => 'boolean',
            'notas' => 'nullable|string|max:500',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Cliente $cliente)
    {
        // Cargar relaciones
        $cliente->load(['agendas.servicios', 'agendas.empleado', 'tickets']);

        return Inertia::render('Clientes/Show', [
            'cliente' => [
                'id' => $cliente->id,
                'nombre_completo' => $cliente->nombre_completo,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'fecha_nacimiento' => $cliente->fecha_nacimiento?->format('d/m/Y'),
                'edad' => $cliente->fecha_nacimiento ? $cliente->fecha_nacimiento->age : null,
                'genero' => $cliente->genero,
                'acepta_marketing' => $cliente->acepta_marketing,
                'puntos_fidelidad' => $cliente->puntos_fidelidad,
                'ultima_visita' => $cliente->ultima_visita?->format('d/m/Y'),
                'notas' => $cliente->notas,
                'created_at' => $cliente->created_at->format('d/m/Y H:i'),
                
                // Estadísticas
                'total_citas' => $cliente->agendas->count(),
                'citas_completadas' => $cliente->agendas->where('estado', 'completada')->count(),
                'total_gastado' => $cliente->tickets->sum('total'),
                
                // Últimas citas
                'ultimas_citas' => $cliente->agendas()
                    ->with(['servicios', 'empleado'])
                    ->orderBy('fecha_hora_inicio', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn($agenda) => [
                        'id' => $agenda->id,
                        'fecha' => $agenda->fecha_hora_inicio->format('d/m/Y H:i'),
                        'empleado' => $agenda->empleado->nombre_completo,
                        'servicios' => $agenda->servicios->pluck('nombre')->implode(', '),
                        'estado' => $agenda->estado,
                        'total' => number_format($agenda->total, 2),
                    ]),
            ],
        ]);
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clientes/Edit', [
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'apellido' => $cliente->apellido,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'fecha_nacimiento' => $cliente->fecha_nacimiento?->format('Y-m-d'),
                'genero' => $cliente->genero,
                'acepta_marketing' => $cliente->acepta_marketing,
                'puntos_fidelidad' => $cliente->puntos_fidelidad,
                'notas' => $cliente->notas,
            ],
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'nullable|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'genero' => 'nullable|in:masculino,femenino,otro,prefiero_no_decir',
            'acepta_marketing' => 'boolean',
            'puntos_fidelidad' => 'nullable|integer|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        // Verificar si tiene citas o tickets
        if ($cliente->agendas()->count() > 0 || $cliente->tickets()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene citas o tickets asociados. Use la eliminación suave en su lugar.');
        }

        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Agregar puntos de fidelidad
     */
    public function agregarPuntos(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'puntos' => 'required|integer|min:1|max:1000',
        ]);

        $cliente->agregarPuntos($validated['puntos']);

        return back()->with('success', "Se agregaron {$validated['puntos']} puntos al cliente.");
    }

    /**
     * Exportar clientes a CSV
     */
    public function exportar(Request $request)
    {
        $query = Cliente::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clientes = $query->get();

        $filename = 'clientes_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($clientes) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 
                'Dirección', 'Fecha Nacimiento', 'Género', 
                'Acepta Marketing', 'Puntos Fidelidad', 'Última Visita'
            ]);

            // Datos
            foreach ($clientes as $cliente) {
                fputcsv($file, [
                    $cliente->id,
                    $cliente->nombre,
                    $cliente->apellido,
                    $cliente->email,
                    $cliente->telefono,
                    $cliente->direccion,
                    $cliente->fecha_nacimiento?->format('d/m/Y'),
                    $cliente->genero,
                    $cliente->acepta_marketing ? 'Sí' : 'No',
                    $cliente->puntos_fidelidad,
                    $cliente->ultima_visita?->format('d/m/Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
