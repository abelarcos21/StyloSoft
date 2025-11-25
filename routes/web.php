<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Web\EmpleadoController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\ServicioController;
use App\Http\Controllers\Web\ProductoController;
use App\Http\Controllers\Web\TicketController;
use App\Http\Controllers\Web\AgendaController;

// Ruta principal - redirige al login o dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Empleados
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
    Route::get('/empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');

    // Rutas adicionales
    Route::get('empleados-exportar', [EmpleadoController::class, 'exportar'])->name('empleados.exportar');
    Route::get('empleados/{empleado}/disponibilidad', [EmpleadoController::class, 'disponibilidad'])->name('empleados.disponibilidad');

});

// Clientes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // Rutas adicionales para clientes
    Route::post('clientes/{cliente}/puntos', [ClienteController::class, 'agregarPuntos'])->name('clientes.puntos');
    Route::get('clientes-exportar', [ClienteController::class, 'exportar'])->name('clientes.exportar');

});

// Servicios
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/create', [ServicioController::class, 'create'])->name('servicios.create');
    Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
    Route::get('/servicios/{servicio}', [ServicioController::class, 'show'])->name('servicios.show');
    Route::get('/servicios/{servicio}/edit', [ServicioController::class, 'edit'])->name('servicios.edit');
    Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');

    // Rutas adicionales
    Route::post('servicios-reordenar', [ServicioController::class, 'reordenar'])->name('servicios.reordenar');
    Route::get('servicios-exportar', [ServicioController::class, 'exportar'])->name('servicios.exportar');
    Route::post('servicios/{servicio}/duplicar', [ServicioController::class, 'duplicar'])->name('servicios.duplicar');

});

// Productos
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

    // Rutas adicionales
    Route::post('productos/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])->name('productos.ajustar-stock');
    Route::get('productos-exportar', [ProductoController::class, 'exportar'])->name('productos.exportar');
    Route::get('productos-bajo-stock', [ProductoController::class, 'bajoStock'])->name('productos.bajo-stock');

});

// Tickets
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Rutas adicionales
    Route::get('tickets/{ticket}/imprimir', [TicketController::class, 'imprimir'])->name('tickets.imprimir');
    Route::get('tickets-estadisticas', [TicketController::class, 'estadisticas'])->name('tickets.estadisticas');
});

// Agendas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/agendas', [AgendaController::class, 'index'])->name('agendas.index');
    Route::get('/agendas/create', [AgendaController::class, 'create'])->name('agendas.create');
    Route::post('/agendas', [AgendaController::class, 'store'])->name('agendas.store');
    Route::get('/agendas/{agenda}', [AgendaController::class, 'show'])->name('agendas.show');
    Route::get('/agendas/{agenda}/edit', [AgendaController::class, 'edit'])->name('agendas.edit');
    Route::put('/agendas/{agenda}', [AgendaController::class, 'update'])->name('agendas.update');


    // Rutas adicionales para cambios de estado
    Route::put('agendas/{agenda}/cancelar', [AgendaController::class, 'cancelar'])->name('agendas.cancelar');
    Route::put('agendas/{agenda}/confirmar', [AgendaController::class, 'confirmar'])->name('agendas.confirmar');
    Route::put('agendas/{agenda}/completar', [AgendaController::class, 'completar'])->name('agendas.completar');
    Route::put('agendas/{agenda}/no-asistio', [AgendaController::class, 'noAsistio'])->name('agendas.noAsistio');
});

require __DIR__.'/auth.php';
