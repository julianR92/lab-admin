<?php

namespace Database\Factories;

use App\Models\Profesional;
use App\Models\ServicioExamen;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResultadoAdjunto>
 */
class ResultadoAdjuntoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $servicioExamen = ServicioExamen::factory()->create();
        $numeroOrden = $servicioExamen->servicio->numero_orden;
        $nombreArchivo = $this->faker->word().'.jpg';
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        $rutaArchivo = "examenes/{$numeroOrden}/{$timestamp}_{$random}_{$nombreArchivo}";

        return [
            'servicio_examen_id' => $servicioExamen->id,
            'tipo_archivo' => 'IMAGEN',
            'nombre_archivo' => $nombreArchivo,
            'ruta_archivo' => $rutaArchivo,
            'mime_type' => 'image/jpeg',
            'tamano_bytes' => $this->faker->numberBetween(500000, 5000000),
            'descripcion' => $this->faker->optional()->sentence(),
            'orden' => $this->faker->numberBetween(1, 10),
            'subido_por' => Profesional::factory(),
        ];
    }

    /**
     * Estado para imágenes PNG
     */
    public function png(): self
    {
        return $this->state(fn (array $attributes) => [
            'nombre_archivo' => $this->faker->word().'.png',
            'mime_type' => 'image/png',
        ]);
    }

    /**
     * Estado para archivos grandes
     */
    public function grande(): self
    {
        return $this->state(fn (array $attributes) => [
            'tamano_bytes' => $this->faker->numberBetween(8000000, 10000000),
        ]);
    }
}
