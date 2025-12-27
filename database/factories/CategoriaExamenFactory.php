<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaExamenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria' => fake()->unique()->words(2, true),
            'descripcion' => fake()->sentence(),
            'status' => 1,
            'orden' => fake()->numberBetween(1, 100),
        ];
    }
}
