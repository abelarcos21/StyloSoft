<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Producto;
use App\Models\Agenda;
use App\Models\Ticket;
use App\Models\DetalleTicket;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Empleados y clientes
        Empleado::factory(10)->create();
        Cliente::factory(20)->create();

        // Servicios y productos
        Servicio::factory(15)->create();
        Producto::factory(20)->create();

        // Agendas
        Agenda::factory(30)->create();

        // Tickets y detalles
        Ticket::factory(15)->create()->each(function ($ticket) {
            DetalleTicket::factory(rand(1, 5))->create([
                'ticket_id' => $ticket->id
            ]);

            // Actualizar total
            $total = $ticket->detalleTickets->sum('subtotal');
            $ticket->update(['total' => $total]);
        });

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
