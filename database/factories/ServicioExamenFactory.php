<?php

namespace Database\Factories;

use App\Models\Examen;
use App\Models\Profesional;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioExamenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'servicio_id' => Servicio::factory(),
            'examen_id' => Examen::factory(),
            'profesional_id' => null,
            'estado' => 'PENDIENTE',
            'fecha_toma_muestra' => null,
            'fecha_resultado' => null,
            'fecha_validacion' => null,
            'fecha_entrega' => null,
        ];
    }

    public function enProceso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'EN_PROCESO',
            'fecha_toma_muestra' => now(),
            'profesional_id' => Profesional::factory(),
        ]);
    }

    public function completado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'COMPLETADO',
            'fecha_toma_muestra' => now()->subHours(2),
            'fecha_resultado' => now(),
            'profesional_id' => Profesional::factory(),
        ]);
    }

    public function validado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'VALIDADO',
            'fecha_toma_muestra' => now()->subHours(3),
            'fecha_resultado' => now()->subHours(1),
            'fecha_validacion' => now(),
            'profesional_id' => Profesional::factory(),
        ]);
    }

    public function entregado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'ENTREGADO',
            'fecha_toma_muestra' => now()->subDays(2),
            'fecha_resultado' => now()->subDays(1),
            'fecha_validacion' => now()->subHours(12),
            'fecha_entrega' => now(),
            'profesional_id' => Profesional::factory(),
        ]);
    }
}
