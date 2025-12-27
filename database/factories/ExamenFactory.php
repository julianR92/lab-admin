<?php

namespace Database\Factories;

use App\Models\CategoriaExamen;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_id' => CategoriaExamen::factory(),
            'codigo' => 'EX-'.fake()->unique()->numberBetween(1000, 9999),
            'nombre' => fake()->words(3, true),
            'tipo_resultado' => fake()->randomElement([
                'NUMERICO_SIMPLE',
                'NUMERICO_CATEGORIZADO',
                'CUALITATIVO_SIMPLE',
                'CUALITATIVO_REACTIVO',
                'CUALITATIVO_MULTIPLE_OPCIONES',
                'MULTIPLE_CALCULADO',
                'TABLA_HEMATOLOGIA',
                'TEXTO_DESCRIPTIVO',
            ]),
            'unidad_medida' => fake()->randomElement(['mg/dL', 'mmol/L', 'UI/mL', '%', 'mm³', null]),
            'tecnica' => fake()->sentence(),
            'muestra_requerida' => fake()->randomElement(['Sangre', 'Orina', 'Heces', 'Suero']),
            'valor_total' => fake()->numberBetween(30000, 200000),
            'valor_remision' => fake()->numberBetween(5000, 50000),
            'tiempo_entrega' => fake()->numberBetween(1, 5),
            'requiere_ayuno' => fake()->boolean(30),
            'instrucciones_paciente' => fake()->optional()->sentence(),
            'status' => 1,
        ];
    }
}
