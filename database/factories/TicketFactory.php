<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente;
use App\Models\Empleado;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'empleado_id' => Empleado::factory(),
            'total' => 0, // se puede actualizar después de crear detalles
            'metodo_pago' => $this->faker->randomElement(['efectivo', 'tarjeta']),
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
