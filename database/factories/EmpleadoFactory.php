<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empleado>
 */
class EmpleadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->phoneNumber,
            'direccion' => $this->faker->address,
            'puesto' => $this->faker->randomElement(['barber', 'estilista', 'masajista']),
            'salario' => $this->faker->randomFloat(2, 3000, 15000),
            'fecha_ingreso' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
