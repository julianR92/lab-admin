<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('tipo_documento', ['CC', 'TI', 'CE', 'PA', 'RC'])->default('CC');
            $table->string('documento', 20)->unique();
            $table->enum('genero', ['M', 'F', 'O'])->nullable();
            $table->date('fecha_nacimiento');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('eps', 150)->nullable();
            $table->timestamps();

            $table->index('documento');
            $table->index('nombre');
            $table->index('apellido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
