<?php

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('puede crear un cliente', function () {
    $cliente = Cliente::factory()->create([
        'nombre' => 'Test',
        'apellido' => 'Usuario',
        'documento' => '9876543210',
    ]);

    expect($cliente->nombre)->toBe('Test')
        ->and($cliente->apellido)->toBe('Usuario')
        ->and($cliente->documento)->toBe('9876543210');
});

test('nombre completo retorna nombre y apellido concatenados', function () {
    $cliente = Cliente::factory()->create([
        'nombre' => 'María',
        'apellido' => 'González',
    ]);

    expect($cliente->nombre_completo)->toBe('María González');
});

test('edad se calcula correctamente desde fecha de nacimiento', function () {
    $cliente = Cliente::factory()->create([
        'fecha_nacimiento' => now()->subYears(30)->format('Y-m-d'),
    ]);

    expect($cliente->edad)->toBe(30);
});

test('scope buscar encuentra clientes por nombre', function () {
    Cliente::factory()->create(['nombre' => 'Carlos', 'apellido' => 'Rodríguez']);
    Cliente::factory()->create(['nombre' => 'Ana', 'apellido' => 'López']);

    $resultados = Cliente::buscar('Carlos')->get();

    expect($resultados)->toHaveCount(1)
        ->and($resultados->first()->nombre)->toBe('Carlos');
});

test('scope buscar encuentra clientes por apellido', function () {
    Cliente::factory()->create(['nombre' => 'Pedro', 'apellido' => 'Martínez']);
    Cliente::factory()->create(['nombre' => 'Luis', 'apellido' => 'García']);

    $resultados = Cliente::buscar('Martínez')->get();

    expect($resultados)->toHaveCount(1)
        ->and($resultados->first()->apellido)->toBe('Martínez');
});

test('scope buscar encuentra clientes por documento', function () {
    Cliente::factory()->create(['documento' => '1122334455']);
    Cliente::factory()->create(['documento' => '6677889900']);

    $resultados = Cliente::buscar('112233')->get();

    expect($resultados)->toHaveCount(1)
        ->and($resultados->first()->documento)->toBe('1122334455');
});

test('scope por documento encuentra cliente específico', function () {
    Cliente::factory()->create(['documento' => '1010101010']);

    $cliente = Cliente::porDocumento('1010101010')->first();

    expect($cliente)->not->toBeNull()
        ->and($cliente->documento)->toBe('1010101010');
});

test('documento debe ser único', function () {
    Cliente::factory()->create(['documento' => '5555555555']);

    expect(fn () => Cliente::factory()->create(['documento' => '5555555555']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('fecha nacimiento se convierte a carbon date', function () {
    $cliente = Cliente::factory()->create([
        'fecha_nacimiento' => '1995-06-15',
    ]);

    expect($cliente->fecha_nacimiento)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('factory crea clientes con datos válidos', function () {
    $cliente = Cliente::factory()->create();

    expect($cliente->nombre)->not->toBeEmpty()
        ->and($cliente->apellido)->not->toBeEmpty()
        ->and($cliente->documento)->not->toBeEmpty()
        ->and($cliente->genero)->toBeIn(['M', 'F', 'O'])
        ->and($cliente->tipo_documento)->toBeIn(['CC', 'TI', 'CE', 'PA', 'RC'])
        ->and($cliente->edad)->toBeGreaterThan(0);
});

test('factory estado masculino crea cliente masculino', function () {
    $cliente = Cliente::factory()->masculino()->create();

    expect($cliente->genero)->toBe('M');
});

test('factory estado femenino crea cliente femenino', function () {
    $cliente = Cliente::factory()->femenino()->create();

    expect($cliente->genero)->toBe('F');
});

test('factory estado joven crea cliente entre 18 y 30 años', function () {
    $cliente = Cliente::factory()->joven()->create();

    expect($cliente->edad)->toBeGreaterThanOrEqual(18)
        ->and($cliente->edad)->toBeLessThanOrEqual(30);
});

test('factory estado adulto mayor crea cliente mayor de 60 años', function () {
    $cliente = Cliente::factory()->adultoMayor()->create();

    expect($cliente->edad)->toBeGreaterThanOrEqual(60);
});
