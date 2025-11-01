<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use App\Models\Producto;
use App\Models\Servicio;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleTicket>
 */
class DetalleTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vendibleClass = $this->faker->randomElement([Producto::class, Servicio::class]);
        $vendible = $vendibleClass::factory()->create();
        $cantidad = $this->faker->numberBetween(1, 5);
        $precio = $vendibleClass === Producto::class ? $vendible->precio_venta : $vendible->precio;
        
        return [
            'ticket_id' => Ticket::factory(),
            'vendible_type' => $vendibleClass,
            'vendible_id' => $vendible->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $cantidad * $precio,
        ];
    }
}
