<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    public function definition(): array
    {
        $valorTotal = $this->faker->numberBetween(50000, 500000);
        $valorPagado = $this->faker->numberBetween(0, $valorTotal);

        return [
            'cliente_id' => Cliente::factory(),
            'numero_orden' => 'ORD-'.now()->format('Ymd').'-'.str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'fecha' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'valor_total' => $valorTotal,
            'valor_pagado' => $valorPagado,
            'medio_pago' => $this->faker->randomElement(['Efectivo', 'Tarjeta débito', 'Tarjeta crédito', 'Transferencia', 'Nequi', 'Daviplata']),
            'estado_pago' => $this->calcularEstadoPago($valorTotal, $valorPagado),
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }

    private function calcularEstadoPago(float $total, float $pagado): string
    {
        if ($pagado == 0) {
            return 'PENDIENTE';
        } elseif ($pagado < $total) {
            return 'PARCIAL';
        } else {
            return 'PAGADO';
        }
    }
}
