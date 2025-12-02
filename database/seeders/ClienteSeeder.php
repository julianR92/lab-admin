<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 50 clientes aleatorios
        Cliente::factory(50)->create();

        // Crear algunos clientes con estados específicos
        Cliente::factory(10)->masculino()->create();
        Cliente::factory(10)->femenino()->create();
        Cliente::factory(5)->joven()->create();
        Cliente::factory(5)->adultoMayor()->create();

        // Cliente de prueba específico
        Cliente::factory()->create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'tipo_documento' => 'CC',
            'documento' => '1234567890',
            'genero' => 'M',
            'fecha_nacimiento' => '1990-01-15',
            'telefono' => '3001234567',
            'email' => 'juan.perez@ejemplo.com',
            'ciudad' => 'Bogotá',
            'eps' => 'Sura',
        ]);
    }
}
