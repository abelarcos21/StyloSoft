<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Web\EmpleadoController;
use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\ServicioController;
use App\Http\Controllers\Web\ProductoController;
use App\Http\Controllers\Web\TicketController;
use App\Http\Controllers\Web\AgendaController;



// Rutas Empleados
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/create', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

// Rutas Clientes
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

});

// Rutas Servicios
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/create', [ServicioController::class, 'create'])->name('servicios.create');
    Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
    Route::get('/servicios/{servicio}/edit', [ServicioController::class, 'edit'])->name('servicios.edit');
    Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
});

// Rutas Productos
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});

// Rutas Tickets
Route::middleware(['auth', 'verified'])->group(function () {
    // Listado de tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    // Crear ticket
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    // Mostrar ticket
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Editar ticket
    Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');

    // Eliminar ticket
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

});

//Rutas Agendas
Route::middleware(['auth', 'verified'])->group(function () {
    // 📅 Agendas
    Route::get('/agendas', [AgendaController::class, 'index'])->name('agendas.index');
    Route::get('/agendas/create', [AgendaController::class, 'create'])->name('agendas.create');
    Route::post('/agendas', [AgendaController::class, 'store'])->name('agendas.store');
    Route::get('/agendas/{agenda}', [AgendaController::class, 'show'])->name('agendas.show');
    Route::get('/agendas/{agenda}/edit', [AgendaController::class, 'edit'])->name('agendas.edit');
    Route::put('/agendas/{agenda}', [AgendaController::class, 'update'])->name('agendas.update');
    Route::delete('/agendas/{agenda}', [AgendaController::class, 'destroy'])->name('agendas.destroy');
});


/* Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
}); */

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
