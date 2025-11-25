<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use Inertia\Inertia;

class ProductoController extends Controller
{

    public function index(Request $request)
    {
        $query = Producto::query();

        // Filtro de búsqueda
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
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

        // Filtro de stock
        if ($request->stock === 'bajo') {
            $query->bajoStock();
        } elseif ($request->stock === 'sin_stock') {
            $query->where('stock', 0);
        }

        // Ordenar
        $query->orderBy('nombre');

        $productos = $query->paginate(15)->through(function ($producto) {
            return [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'marca' => $producto->marca,
                'descripcion' => $producto->descripcion,
                'precio_venta' => number_format($producto->precio_venta, 2),
                'precio_compra' => number_format($producto->precio_compra, 2),
                'precio_mayoreo' => $producto->precio_mayoreo ? number_format($producto->precio_mayoreo, 2) : null,
                'stock' => $producto->stock,
                'stock_minimo' => $producto->stock_minimo,
                'categoria' => $producto->categoria,
                'unidad_medida' => $producto->unidad_medida,
                'activo' => $producto->activo,
                'requiere_reorden' => $producto->requiere_reorden,
                'margen_ganancia' => $producto->margen_ganancia ? number_format($producto->margen_ganancia, 2) : null,
                'created_at' => $producto->created_at->format('d/m/Y'),
            ];
        });

        // Obtener categorías únicas
        $categorias = Producto::select('categoria')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria');

        return Inertia::render('Productos/Index', [
            'productos' => $productos,
            'filters' => $request->only(['search', 'categoria', 'activo', 'stock']),
            'categorias' => $categorias,
        ]);
    }

    public function create()
    {
        // Generar código sugerido
        $ultimoCodigo = Producto::latest('id')->first()?->codigo ?? 'PROD-000';
        $numero = intval(substr($ultimoCodigo, 5)) + 1;
        $codigoSugerido = 'PROD-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

        return Inertia::render('Productos/Create', [
            'codigo_sugerido' => $codigoSugerido,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'marca' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_mayoreo' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:50',
            'unidad_medida' => 'required|string|max:20',
            'activo' => 'boolean',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Producto $producto)
    {
        // Cargar tickets donde se ha vendido
        $producto->load(['detalleTickets.ticket']);

        return Inertia::render('Productos/Show', [
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'marca' => $producto->marca,
                'descripcion' => $producto->descripcion,
                'precio_venta' => number_format($producto->precio_venta, 2),
                'precio_compra' => $producto->precio_compra ? number_format($producto->precio_compra, 2) : null,
                'precio_mayoreo' => $producto->precio_mayoreo ? number_format($producto->precio_mayoreo, 2) : null,
                'stock' => $producto->stock,
                'stock_minimo' => $producto->stock_minimo,
                'categoria' => $producto->categoria,
                'unidad_medida' => $producto->unidad_medida,
                'activo' => $producto->activo,
                'requiere_reorden' => $producto->requiere_reorden,
                'margen_ganancia' => $producto->margen_ganancia ? number_format($producto->margen_ganancia, 2) : null,
                'created_at' => $producto->created_at->format('d/m/Y H:i'),
                
                // Estadísticas
                'total_vendido' => $producto->detalleTickets->sum('cantidad'),
                'ventas_generadas' => $producto->detalleTickets->sum('subtotal'),
                
                // Últimas ventas
                'ultimas_ventas' => $producto->detalleTickets()
                    ->with(['ticket.cliente'])
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(fn($detalle) => [
                        'id' => $detalle->ticket->id,
                        'fecha' => $detalle->ticket->fecha->format('d/m/Y H:i'),
                        'cliente' => $detalle->ticket->cliente?->nombre_completo ?? 'Cliente general',
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => number_format($detalle->precio_unitario, 2),
                        'subtotal' => number_format($detalle->subtotal, 2),
                    ]),
            ],
        ]);
    }


    public function edit(Producto $producto)
    {
        return Inertia::render('Productos/Edit', [
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo' => $producto->codigo,
                'marca' => $producto->marca,
                'descripcion' => $producto->descripcion,
                'precio_venta' => $producto->precio_venta,
                'precio_compra' => $producto->precio_compra,
                'precio_mayoreo' => $producto->precio_mayoreo,
                'stock' => $producto->stock,
                'stock_minimo' => $producto->stock_minimo,
                'categoria' => $producto->categoria,
                'unidad_medida' => $producto->unidad_medida,
                'activo' => $producto->activo,
            ],
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $producto->id,
            'marca' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_mayoreo' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:50',
            'unidad_medida' => 'required|string|max:20',
            'activo' => 'boolean',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Ajustar stock del producto
     */
    public function ajustarStock(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:aumentar,reducir,ajustar',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:200',
        ]);

        $stockAnterior = $producto->stock;

        switch ($validated['tipo']) {
            case 'aumentar':
                $producto->aumentarStock($validated['cantidad']);
                break;
            case 'reducir':
                if (!$producto->reducirStock($validated['cantidad'])) {
                    return back()->with('error', 'Stock insuficiente.');
                }
                break;
            case 'ajustar':
                $producto->stock = $validated['cantidad'];
                $producto->save();
                break;
        }

        return back()->with('success', "Stock actualizado de {$stockAnterior} a {$producto->stock}.");
    }

    /**
     * Exportar productos a CSV
     */
    public function exportar(Request $request)
    {
        $query = Producto::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        $productos = $query->get();

        $filename = 'productos_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($productos) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'ID', 'Código', 'Nombre', 'Marca', 'Categoría', 
                'Precio Venta', 'Precio Compra', 'Stock', 'Stock Mínimo',
                'Unidad Medida', 'Activo', 'Margen %'
            ]);

            // Datos
            foreach ($productos as $producto) {
                fputcsv($file, [
                    $producto->id,
                    $producto->codigo,
                    $producto->nombre,
                    $producto->marca,
                    $producto->categoria,
                    $producto->precio_venta,
                    $producto->precio_compra,
                    $producto->stock,
                    $producto->stock_minimo,
                    $producto->unidad_medida,
                    $producto->activo ? 'Sí' : 'No',
                    $producto->margen_ganancia ? round($producto->margen_ganancia, 2) . '%' : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtener productos con bajo stock
     */
    public function bajoStock()
    {
        $productos = Producto::bajoStock()
            ->activos()
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'stock' => $producto->stock,
                    'stock_minimo' => $producto->stock_minimo,
                    'diferencia' => $producto->stock_minimo - $producto->stock,
                ];
            });

        return response()->json([
            'productos' => $productos,
            'total' => $productos->count(),
        ]);
    }

    public function destroy(Producto $producto)
    {
        // Verificar si tiene ventas asociadas
        if ($producto->detalleTickets()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas.');
        }

        $producto->delete();
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
