<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Solo crear si no existe ningún registro
        if (Empresa::count() === 0) {
            Empresa::create([
                'nit' => '900123456-7',
                'razon_social' => 'Laboratorio Clínico San Rafael',
                'direccion' => 'Calle 45 #23-15',
                'barrio' => 'Centro',
                'ciudad' => 'Bogotá',
                'telefono_uno' => '(601) 234-5678',
                'telefono_dos' => '(601) 234-5679',
                'email' => 'info@labsanrafael.com',
                'logo' => null, // Se cargará posteriormente desde la interfaz
            ]);
        }
    }
}
