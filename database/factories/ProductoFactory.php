<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word,
            'codigo' => $this->faker->unique()->ean8,
            'descripcion' => $this->faker->sentence,
            'precio_venta' => $this->faker->randomFloat(2, 20, 1000),
            'precio_compra' => $this->faker->randomFloat(2, 10, 800),
            'stock' => $this->faker->numberBetween(0, 50),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
