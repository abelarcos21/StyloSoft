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

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro de categoría
        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        // Filtro de estado
        if ($request->activo !== null) {
            $query->where('activo', $request->activo);
        }

        // Filtro de duración
        if ($request->duracion) {
            switch ($request->duracion) {
                case 'corta':
                    $query->where('duracion_minutos', '<=', 30);
                    break;
                case 'media':
                    $query->whereBetween('duracion_minutos', [31, 60]);
                    break;
                case 'larga':
                    $query->where('duracion_minutos', '>', 60);
                    break;
            }
        }

        // Ordenar por orden y nombre
        $query->orderBy('orden')->orderBy('nombre');

        $servicios = $query->paginate(15)->through(function ($servicio) {
            return [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'descripcion' => $servicio->descripcion,
                'precio' => number_format($servicio->precio, 2),
                'duracion_minutos' => $servicio->duracion_minutos,
                'categoria' => $servicio->categoria,
                'orden' => $servicio->orden,
                'requiere_deposito' => $servicio->requiere_deposito,
                'deposito_minimo' => $servicio->deposito_minimo ? number_format($servicio->deposito_minimo, 2) : null,
                'activo' => $servicio->activo,
                'created_at' => $servicio->created_at->format('d/m/Y'),
            ];
        });

        // Obtener categorías únicas
        $categorias = Servicio::select('categoria')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria');

        return Inertia::render('Servicios/Index', [
            'servicios' => $servicios,
            'filters' => $request->only(['search', 'categoria', 'activo', 'duracion']),
            'categorias' => $categorias,
        ]);
    }

    public function create()
    {
        // Obtener el último orden
        $ultimoOrden = Servicio::max('orden') ?? 0;

        return Inertia::render('Servicios/Create', [
            'orden_sugerido' => $ultimoOrden + 1,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'requiere_deposito' => 'boolean',
            'deposito_minimo' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        // Si no se proporciona orden, asignar el siguiente
        if (!isset($validated['orden'])) {
            $validated['orden'] = Servicio::max('orden') + 1;
        }

        Servicio::create($validated);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado exitosamente.');
    }

    public function show(Servicio $servicio)
    {
        // Cargar agendas donde se ha usado el servicio
        $servicio->load(['agendas.cliente', 'agendas.empleado']);

        return Inertia::render('Servicios/Show', [
            'servicio' => [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'descripcion' => $servicio->descripcion,
                'precio' => number_format($servicio->precio, 2),
                'duracion_minutos' => $servicio->duracion_minutos,
                'duracion_horas' => floor($servicio->duracion_minutos / 60) . 'h ' . ($servicio->duracion_minutos % 60) . 'min',
                'categoria' => $servicio->categoria,
                'orden' => $servicio->orden,
                'requiere_deposito' => $servicio->requiere_deposito,
                'deposito_minimo' => $servicio->deposito_minimo ? number_format($servicio->deposito_minimo, 2) : null,
                'activo' => $servicio->activo,
                'created_at' => $servicio->created_at->format('d/m/Y H:i'),
                
                // Estadísticas
                'total_agendado' => $servicio->agendas->count(),
                'total_completado' => $servicio->agendas->where('estado', 'completada')->count(),
                'ingresos_generados' => $servicio->agendas()
                    ->where('estado', 'completada')
                    ->get()
                    ->sum(function ($agenda) use ($servicio) {
                        $pivot = $agenda->servicios->where('id', $servicio->id)->first()?->pivot;
                        return $pivot ? $pivot->precio : 0;
                    }),
                
                // Últimas agendas
                'ultimas_agendas' => $servicio->agendas()
                    ->with(['cliente', 'empleado'])
                    ->latest('fecha_hora_inicio')
                    ->take(10)
                    ->get()
                    ->map(fn($agenda) => [
                        'id' => $agenda->id,
                        'fecha' => $agenda->fecha_hora_inicio->format('d/m/Y H:i'),
                        'cliente' => $agenda->cliente->nombre_completo,
                        'empleado' => $agenda->empleado->nombre_completo,
                        'estado' => $agenda->estado,
                        'precio' => number_format($agenda->servicios->where('id', $servicio->id)->first()?->pivot->precio ?? 0, 2),
                    ]),
            ],
        ]);
    }


    public function edit(Servicio $servicio)
    {
       return Inertia::render('Servicios/Edit', [
            'servicio' => [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'descripcion' => $servicio->descripcion,
                'precio' => $servicio->precio,
                'duracion_minutos' => $servicio->duracion_minutos,
                'categoria' => $servicio->categoria,
                'orden' => $servicio->orden,
                'requiere_deposito' => $servicio->requiere_deposito,
                'deposito_minimo' => $servicio->deposito_minimo,
                'activo' => $servicio->activo,
            ],
        ]);
    }

    public function update(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'categoria' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'requiere_deposito' => 'boolean',
            'deposito_minimo' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);

        $servicio->update($validated);

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio actualizado exitosamente.');
    }

    public function destroy(Servicio $servicio)
    {
        // Verificar si tiene agendas asociadas
        if ($servicio->agendas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el servicio porque tiene agendas asociadas.');
        }

        $servicio->delete();
        
        return redirect()->route('servicios.index')
            ->with('success', 'Servicio eliminado exitosamente.');
    }

    /**
     * Reordenar servicios
     */
    public function reordenar(Request $request)
    {
        $validated = $request->validate([
            'servicios' => 'required|array',
            'servicios.*.id' => 'required|exists:servicios,id',
            'servicios.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($validated['servicios'] as $servicioData) {
            Servicio::where('id', $servicioData['id'])
                ->update(['orden' => $servicioData['orden']]);
        }

        return back()->with('success', 'Orden actualizado exitosamente.');
    }

    /**
     * Exportar servicios a CSV
     */
    public function exportar(Request $request)
    {
        $query = Servicio::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        $servicios = $query->orderBy('orden')->orderBy('nombre')->get();

        $filename = 'servicios_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($servicios) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID', 'Nombre', 'Categoría', 'Precio', 
                'Duración (min)', 'Requiere Depósito', 'Depósito Mínimo',
                'Orden', 'Activo'
            ]);

            // Datos
            foreach ($servicios as $servicio) {
                fputcsv($file, [
                    $servicio->id,
                    $servicio->nombre,
                    $servicio->categoria,
                    $servicio->precio,
                    $servicio->duracion_minutos,
                    $servicio->requiere_deposito ? 'Sí' : 'No',
                    $servicio->deposito_minimo,
                    $servicio->orden,
                    $servicio->activo ? 'Sí' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Duplicar servicio
     */
    public function duplicar(Servicio $servicio)
    {
        $nuevoServicio = $servicio->replicate();
        $nuevoServicio->nombre = $servicio->nombre . ' (Copia)';
        $nuevoServicio->orden = Servicio::max('orden') + 1;
        $nuevoServicio->save();

        return redirect()->route('servicios.edit', $nuevoServicio)
            ->with('success', 'Servicio duplicado exitosamente.');
    }

}
