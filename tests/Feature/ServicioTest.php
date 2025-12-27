<?php

use App\Models\Cliente;
use App\Models\Examen;
use App\Models\Servicio;
use App\Models\ServicioExamen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('puede listar servicios', function () {
    Servicio::factory()->count(5)->create();

    $response = $this->get(route('servicios.index'));

    $response->assertOk();
    $response->assertViewIs('servicios.index');
    $response->assertViewHas('servicios');
});

it('puede mostrar formulario de creación', function () {
    Examen::factory()->count(3)->create();

    $response = $this->get(route('servicios.create'));

    $response->assertOk();
    $response->assertViewIs('servicios.create');
    $response->assertViewHas('examenes');
});

it('puede crear un servicio correctamente', function () {
    $cliente = Cliente::factory()->create();
    $examenes = Examen::factory()->count(2)->create();

    $data = [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'valor_pagado' => 0,
        'medio_pago' => 'Efectivo',
        'observaciones' => 'Observaciones de prueba',
        'examenes' => json_encode($examenes->pluck('id')->toArray()),
        'precios' => json_encode([50000, 80000]),
    ];

    $response = $this->post(route('servicios.store'), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('servicio', [
        'cliente_id' => $cliente->id,
        'valor_total' => 130000,
        'valor_pagado' => 0,
        'estado_pago' => 'PENDIENTE',
    ]);

    $servicio = Servicio::latest()->first();
    expect($servicio->serviciosExamen)->toHaveCount(2);
});

it('genera número de orden automáticamente', function () {
    $cliente = Cliente::factory()->create();
    $examen = Examen::factory()->create();

    $data = [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'valor_pagado' => 0,
        'examenes' => json_encode([$examen->id]),
        'precios' => json_encode([50000]),
    ];

    $this->post(route('servicios.store'), $data);

    $servicio = Servicio::latest()->first();
    expect($servicio->numero_orden)->toStartWith('ORD-'.now()->format('Ymd'));
});

it('calcula el estado de pago correctamente', function () {
    $cliente = Cliente::factory()->create();
    $examen = Examen::factory()->create();

    // Pago pendiente
    $this->post(route('servicios.store'), [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'valor_pagado' => 0,
        'examenes' => json_encode([$examen->id]),
        'precios' => json_encode([100000]),
    ]);

    $servicio = Servicio::latest()->first();
    expect($servicio->estado_pago)->toBe('PENDIENTE');

    // Pago parcial
    $this->post(route('servicios.store'), [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'valor_pagado' => 50000,
        'medio_pago' => 'Efectivo',
        'examenes' => json_encode([$examen->id]),
        'precios' => json_encode([100000]),
    ]);

    $servicio = Servicio::latest()->first();
    expect($servicio->estado_pago)->toBe('PARCIAL');

    // Pago completo
    $this->post(route('servicios.store'), [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'valor_pagado' => 100000,
        'medio_pago' => 'Efectivo',
        'examenes' => json_encode([$examen->id]),
        'precios' => json_encode([100000]),
    ]);

    $servicio = Servicio::latest()->first();
    expect($servicio->estado_pago)->toBe('PAGADO');
});

it('puede mostrar detalle de servicio', function () {
    $servicio = Servicio::factory()
        ->has(ServicioExamen::factory()->count(2))
        ->create();

    $response = $this->get(route('servicios.show', $servicio));

    $response->assertOk();
    $response->assertViewIs('servicios.show');
    $response->assertViewHas('servicio');
});

it('puede actualizar un servicio', function () {
    $servicio = Servicio::factory()->create([
        'observaciones' => 'Observación original',
        'valor_pagado' => 0,
    ]);

    $data = [
        'fecha' => $servicio->fecha->format('Y-m-d'),
        'observaciones' => 'Observación actualizada',
        'valor_pagado' => 50000,
        'medio_pago' => 'Tarjeta crédito',
    ];

    $response = $this->put(route('servicios.update', $servicio), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('servicio', [
        'id' => $servicio->id,
        'observaciones' => 'Observación actualizada',
        'valor_pagado' => 50000,
    ]);
});

it('no puede eliminar servicio con exámenes en proceso', function () {
    $servicio = Servicio::factory()->create();
    ServicioExamen::factory()->create([
        'servicio_id' => $servicio->id,
        'estado' => 'EN_PROCESO',
    ]);

    $response = $this->delete(route('servicios.destroy', $servicio));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('servicio', ['id' => $servicio->id]);
});

it('puede eliminar servicio con exámenes pendientes', function () {
    $servicio = Servicio::factory()->create();
    ServicioExamen::factory()->create([
        'servicio_id' => $servicio->id,
        'estado' => 'PENDIENTE',
    ]);

    $response = $this->delete(route('servicios.destroy', $servicio));

    $response->assertRedirect(route('servicios.index'));
    $this->assertDatabaseMissing('servicio', ['id' => $servicio->id]);
});

it('puede registrar un pago adicional', function () {
    $servicio = Servicio::factory()->create([
        'valor_total' => 100000,
        'valor_pagado' => 30000,
        'estado_pago' => 'PARCIAL',
    ]);

    $data = [
        'monto' => 20000,
        'medio_pago' => 'Efectivo',
    ];

    $response = $this->post(route('servicios.registrar-pago', $servicio), $data);

    $response->assertRedirect();
    $this->assertDatabaseHas('servicio', [
        'id' => $servicio->id,
        'valor_pagado' => 50000,
        'estado_pago' => 'PARCIAL',
    ]);
});

it('no permite pago que excede el valor total', function () {
    $servicio = Servicio::factory()->create([
        'valor_total' => 100000,
        'valor_pagado' => 80000,
    ]);

    $data = [
        'monto' => 30000,
        'medio_pago' => 'Efectivo',
    ];

    $response = $this->post(route('servicios.registrar-pago', $servicio), $data);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('valida que el cliente sea requerido', function () {
    $examen = Examen::factory()->create();

    $data = [
        'fecha' => now()->format('Y-m-d'),
        'examenes' => json_encode([$examen->id]),
        'precios' => json_encode([50000]),
    ];

    $response = $this->post(route('servicios.store'), $data);

    $response->assertSessionHasErrors(['cliente_id']);
});

it('valida que se agregue al menos un examen', function () {
    $cliente = Cliente::factory()->create();

    $data = [
        'cliente_id' => $cliente->id,
        'fecha' => now()->format('Y-m-d'),
        'examenes' => json_encode([]),
        'precios' => json_encode([]),
    ];

    $response = $this->post(route('servicios.store'), $data);

    $response->assertSessionHasErrors(['examenes']);
});
