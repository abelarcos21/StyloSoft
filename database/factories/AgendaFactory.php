<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Servicio;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agenda>
 */
class AgendaFactory extends Factory
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
            'servicio_id' => Servicio::factory(),
            'fecha_hora' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'estado' => $this->faker->randomElement(['pendiente', 'confirmada', 'cancelada', 'completada']),
        ];
    }
}
