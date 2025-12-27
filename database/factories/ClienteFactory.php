<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genero = fake()->randomElement(['M', 'F']);
        $fechaNacimiento = fake()->dateTimeBetween('-80 years', '-18 years');

        return [
            'nombre' => fake()->firstName($genero === 'M' ? 'male' : 'female'),
            'apellido' => fake()->lastName(),
            'tipo_documento' => fake()->randomElement(['CC', 'TI', 'CE', 'PA']),
            'documento' => fake()->unique()->numerify('##########'),
            'genero' => $genero,
            'fecha_nacimiento' => $fechaNacimiento,
            'telefono' => fake()->numerify('3#########'),
            'email' => fake()->unique()->safeEmail(),
            'ciudad' => fake()->randomElement([
                'Bogotá',
                'Medellín',
                'Cali',
                'Barranquilla',
                'Cartagena',
                'Bucaramanga',
                'Pereira',
                'Manizales',
                'Ibagué',
                'Pasto',
            ]),
            'eps' => fake()->randomElement([
                'Sura',
                'Salud Total',
                'Compensar',
                'Sanitas',
                'Nueva EPS',
                'Famisanar',
                'Coomeva',
                'Medimás',
                'Capital Salud',
                'Cruz Blanca',
            ]),
        ];
    }

    /**
     * Estado para clientes masculinos
     */
    public function masculino(): static
    {
        return $this->state(fn (array $attributes) => [
            'genero' => 'M',
            'nombre' => fake()->firstNameMale(),
        ]);
    }

    /**
     * Estado para clientes femeninos
     */
    public function femenino(): static
    {
        return $this->state(fn (array $attributes) => [
            'genero' => 'F',
            'nombre' => fake()->firstNameFemale(),
        ]);
    }

    /**
     * Estado para clientes jóvenes (18-30 años)
     */
    public function joven(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-30 years', '-18 years'),
        ]);
    }

    /**
     * Estado para clientes adultos mayores (60+ años)
     */
    public function adultoMayor(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-80 years', '-60 years'),
        ]);
    }
}
