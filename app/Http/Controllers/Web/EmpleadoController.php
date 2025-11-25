<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empleado;
use Inertia\Inertia;


class EmpleadoController extends Controller
{
    public function index(Request $request){
        $query = Empleado::query();

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('puesto', 'like', "%{$search}%");
            });
        }

        // Filtro de estado
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        // Filtro de puesto
        if ($request->puesto) {
            $query->where('puesto', $request->puesto);
        }

        // Ordenar
        $query->orderBy('created_at', 'desc');

        $empleados = $query->paginate(15)->through(function ($empleado) {
            return [
                'id' => $empleado->id,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'nombre_completo' => $empleado->nombre_completo,
                'email' => $empleado->email,
                'telefono' => $empleado->telefono,
                'puesto' => $empleado->puesto,
                'hora_entrada' => $empleado->hora_entrada?->format('H:i'),
                'hora_salida' => $empleado->hora_salida?->format('H:i'),
                'dias_laborales' => $empleado->dias_laborales,
                'salario' => number_format($empleado->salario, 2),
                'comision_porcentaje' => $empleado->comision_porcentaje,
                'fecha_ingreso' => $empleado->fecha_ingreso?->format('d/m/Y'),
                'estado' => $empleado->estado,
                'created_at' => $empleado->created_at->format('d/m/Y'),
            ];
        });

        // Obtener puestos únicos para filtro
        $puestos = Empleado::select('puesto')
            ->distinct()
            ->pluck('puesto');

        return Inertia::render('Empleados/Index', [
            'empleados' => $empleados,
            'filters' => $request->only(['search', 'estado', 'puesto']),
            'puestos' => $puestos,
        ]);
    }

    public function create()
    {
        return Inertia::render('Empleados/Create', [
            'dias_semana' => [
                'lunes', 'martes', 'miercoles', 'jueves', 
                'viernes', 'sabado', 'domingo'
            ],
        ]);
    }

    public function store(Request $request){

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'nullable|email|unique:empleados,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'puesto' => 'required|string|max:50',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'dias_laborales' => 'nullable|array',
            'dias_laborales.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'salario' => 'nullable|numeric|min:0',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_ingreso' => 'nullable|date|before_or_equal:today',
            'especialidades' => 'nullable|string',
        ]);

        // ORDEN CORRECTO DE LOS DÍAS
        $diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        if (!empty($validated['dias_laborales'])) {
            $validated['dias_laborales'] = collect($validated['dias_laborales'])
                ->sortBy(fn($dia) => array_search($dia, $diasOrden))
                ->values()
                ->toArray();
        }

        Empleado::create($validated);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    public function show(Empleado $empleado)
    {
        // Cargar relaciones
        $empleado->load(['agendas.cliente', 'agendas.servicios', 'tickets']);

        return Inertia::render('Empleados/Show', [
            'empleado' => [
                'id' => $empleado->id,
                'nombre_completo' => $empleado->nombre_completo,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'email' => $empleado->email,
                'telefono' => $empleado->telefono,
                'direccion' => $empleado->direccion,
                'puesto' => $empleado->puesto,
                'hora_entrada' => $empleado->hora_entrada?->format('H:i'),
                'hora_salida' => $empleado->hora_salida?->format('H:i'),
                'dias_laborales' => $empleado->dias_laborales,
                'salario' => number_format($empleado->salario, 2),
                'comision_porcentaje' => $empleado->comision_porcentaje,
                'fecha_ingreso' => $empleado->fecha_ingreso?->format('d/m/Y'),
                'fecha_salida' => $empleado->fecha_salida?->format('d/m/Y'),
                'estado' => $empleado->estado,
                'foto' => $empleado->foto,
                'especialidades' => $empleado->especialidades,
                'created_at' => $empleado->created_at->format('d/m/Y H:i'),
                
                // Estadísticas
                'total_citas' => $empleado->agendas->count(),
                'citas_completadas' => $empleado->agendas->where('estado', 'completada')->count(),
                'total_ventas' => $empleado->tickets->sum('total'),
                'comisiones_ganadas' => $empleado->tickets->sum(function ($ticket) use ($empleado) {
                    return $empleado->calcularComision($ticket->total);
                }),
                
                // Últimas citas
                'ultimas_citas' => $empleado->agendas()
                    ->with(['cliente', 'servicios'])
                    ->orderBy('fecha_hora_inicio', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn($agenda) => [
                        'id' => $agenda->id,
                        'fecha' => $agenda->fecha_hora_inicio->format('d/m/Y H:i'),
                        'cliente' => $agenda->cliente->nombre_completo,
                        'servicios' => $agenda->servicios->pluck('nombre')->implode(', '),
                        'estado' => $agenda->estado,
                        'total' => number_format($agenda->total, 2),
                    ]),
            ],
        ]);
    }

    public function edit(Empleado $empleado)
    {
        return Inertia::render('Empleados/Edit', [
            'empleado' => [
                'id' => $empleado->id,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'email' => $empleado->email,
                'telefono' => $empleado->telefono,
                'direccion' => $empleado->direccion,
                'puesto' => $empleado->puesto,
                'hora_entrada' => $empleado->hora_entrada?->format('H:i'),
                'hora_salida' => $empleado->hora_salida?->format('H:i'),
                'dias_laborales' => $empleado->dias_laborales ?? [],
                'salario' => $empleado->salario,
                'comision_porcentaje' => $empleado->comision_porcentaje,
                'fecha_ingreso' => $empleado->fecha_ingreso?->format('Y-m-d'),
                'fecha_salida' => $empleado->fecha_salida?->format('Y-m-d'),
                'estado' => $empleado->estado,
                'especialidades' => $empleado->especialidades,
            ],
            'dias_semana' => [
                'lunes', 'martes', 'miercoles', 'jueves', 
                'viernes', 'sabado', 'domingo'
            ],
        ]);
    }

    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'nullable|email|unique:empleados,email,' . $empleado->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'puesto' => 'required|string|max:50',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i|after:hora_entrada',
            'dias_laborales' => 'nullable|array',
            'dias_laborales.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'salario' => 'nullable|numeric|min:0',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_ingreso' => 'nullable|date|before_or_equal:today',
            'fecha_salida' => 'nullable|date|after:fecha_ingreso',
            'estado' => 'required|in:activo,inactivo,vacaciones',
            'especialidades' => 'nullable|string',
        ]);

        // ORDEN CORRECTO DE LOS DÍAS
        $diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

        if (!empty($validated['dias_laborales'])) {
            $validated['dias_laborales'] = collect($validated['dias_laborales'])
                ->sortBy(fn($dia) => array_search($dia, $diasOrden))
                ->values()
                ->toArray();
        }


        $empleado->update($validated);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // Verificar si tiene citas o tickets
        if ($empleado->agendas()->count() > 0 || $empleado->tickets()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el empleado porque tiene citas o tickets asociados. Use la eliminación suave en su lugar.');
        }

        $empleado->delete();
        
        return redirect()->route('empleados.index')
            ->with('success', 'Empleado eliminado exitosamente.');
    }

    /**
     * Obtener disponibilidad del empleado por día
     */
    public function disponibilidad(Request $request, Empleado $empleado)
    {
        $request->validate([
            'dia' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
        ]);

        $disponible = $empleado->estaDisponible($request->dia);

        return response()->json([
            'disponible' => $disponible,
            'empleado' => [
                'id' => $empleado->id,
                'nombre_completo' => $empleado->nombre_completo,
                'hora_entrada' => $empleado->hora_entrada?->format('H:i'),
                'hora_salida' => $empleado->hora_salida?->format('H:i'),
            ],
        ]);
    }

    /**
     * Exportar empleados a CSV
     */
    public function exportar(Request $request)
    {
        $query = Empleado::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('puesto', 'like', "%{$search}%");
            });
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $empleados = $query->get();

        $filename = 'empleados_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($empleados) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 
                'Puesto', 'Hora Entrada', 'Hora Salida', 'Días Laborales',
                'Salario', 'Comisión %', 'Fecha Ingreso', 'Estado'
            ]);

            // Datos
            foreach ($empleados as $empleado) {
                fputcsv($file, [
                    $empleado->id,
                    $empleado->nombre,
                    $empleado->apellido,
                    $empleado->email,
                    $empleado->telefono,
                    $empleado->puesto,
                    $empleado->hora_entrada?->format('H:i'),
                    $empleado->hora_salida?->format('H:i'),
                    $empleado->dias_laborales ? implode(', ', $empleado->dias_laborales) : '',
                    $empleado->salario,
                    $empleado->comision_porcentaje,
                    $empleado->fecha_ingreso?->format('d/m/Y'),
                    $empleado->estado,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }




}
