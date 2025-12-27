<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfesionalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'documento' => fake()->unique()->numerify('##########'),
            'profesion' => fake()->randomElement(['Bacteriólogo', 'Médico', 'Químico']),
            'registro_profesional' => fake()->numerify('TP-####'),
            'especialidad' => fake()->optional()->words(2, true),
            'firma_digital' => null,
            'status' => 1,
        ];
    }
}
