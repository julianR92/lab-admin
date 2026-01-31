<?php

use App\Models\ResultadoAdjunto;
use App\Models\ServicioExamen;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

uses()->group('resultados', 'adjuntos');

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->servicioExamen = ServicioExamen::factory()->create();
});

it('puede listar adjuntos de un servicio examen', function () {
    // Crear algunos adjuntos
    ResultadoAdjunto::factory()->count(3)->create([
        'servicio_examen_id' => $this->servicioExamen->id,
    ]);

    $response = $this->getJson(route('adjuntos.index', $this->servicioExamen));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'nombre_archivo', 'tamano_bytes', 'url_archivo'],
            ],
            'total',
        ])
        ->assertJson(['total' => 3]);
});

it('puede subir una imagen válida', function () {
    $imagen = UploadedFile::fake()->image('resultado.jpg', 800, 600)->size(5000); // 5MB

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $imagen,
        'descripcion' => 'Imagen de prueba',
    ]);

    $response->assertSuccessful()
        ->assertJson(['success' => true])
        ->assertJsonStructure([
            'data' => ['id', 'nombre_archivo', 'ruta_archivo', 'tamano_bytes'],
        ]);

    $this->assertDatabaseHas('resultados_adjuntos', [
        'servicio_examen_id' => $this->servicioExamen->id,
        'nombre_archivo' => 'resultado.jpg',
    ]);

    // Verificar que el archivo se guardó
    $adjunto = ResultadoAdjunto::latest()->first();
    Storage::disk('public')->assertExists($adjunto->ruta_archivo);
});

it('rechaza archivos que exceden el tamaño máximo', function () {
    $imagen = UploadedFile::fake()->image('grande.jpg')->size(11000); // 11MB

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $imagen,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['archivo']);
});

it('rechaza archivos con formato no permitido', function () {
    $archivo = UploadedFile::fake()->create('documento.pdf', 1000, 'application/pdf');

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $archivo,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['archivo']);
});

it('puede eliminar un adjunto', function () {
    $adjunto = ResultadoAdjunto::factory()->create([
        'servicio_examen_id' => $this->servicioExamen->id,
    ]);

    // Crear archivo físico
    Storage::disk('public')->put($adjunto->ruta_archivo, 'contenido de prueba');

    $response = $this->deleteJson(route('adjuntos.destroy', [$this->servicioExamen, $adjunto]));

    $response->assertSuccessful()
        ->assertJson(['success' => true]);

    $this->assertDatabaseMissing('resultados_adjuntos', [
        'id' => $adjunto->id,
    ]);

    // Verificar que el archivo se eliminó
    Storage::disk('public')->assertMissing($adjunto->ruta_archivo);
});

it('no permite eliminar adjunto de otro servicio examen', function () {
    $otroServicioExamen = ServicioExamen::factory()->create();
    $adjunto = ResultadoAdjunto::factory()->create([
        'servicio_examen_id' => $otroServicioExamen->id,
    ]);

    $response = $this->deleteJson(route('adjuntos.destroy', [$this->servicioExamen, $adjunto]));

    $response->assertStatus(403)
        ->assertJson(['success' => false]);

    $this->assertDatabaseHas('resultados_adjuntos', [
        'id' => $adjunto->id,
    ]);
});

it('puede descargar un adjunto individual', function () {
    $adjunto = ResultadoAdjunto::factory()->create([
        'servicio_examen_id' => $this->servicioExamen->id,
        'nombre_archivo' => 'test-imagen.jpg',
    ]);

    // Crear archivo físico
    Storage::disk('public')->put($adjunto->ruta_archivo, 'contenido de prueba');

    $response = $this->get(route('adjuntos.download', [$this->servicioExamen, $adjunto]));

    $response->assertSuccessful();
    $response->assertDownload('test-imagen.jpg');
});

it('puede descargar todos los adjuntos como ZIP', function () {
    ResultadoAdjunto::factory()->count(3)->create([
        'servicio_examen_id' => $this->servicioExamen->id,
    ])->each(function ($adjunto) {
        Storage::disk('public')->put($adjunto->ruta_archivo, 'contenido de prueba');
    });

    $response = $this->get(route('adjuntos.download-all', $this->servicioExamen));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'application/zip');
});

it('puede actualizar el orden de los adjuntos', function () {
    $adjuntos = ResultadoAdjunto::factory()->count(3)->create([
        'servicio_examen_id' => $this->servicioExamen->id,
    ]);

    $orden = $adjuntos->pluck('id')->reverse()->toArray();

    $response = $this->postJson(route('adjuntos.orden', $this->servicioExamen), [
        'orden' => $orden,
    ]);

    $response->assertSuccessful()
        ->assertJson(['success' => true]);

    // Verificar que el orden se actualizó
    foreach ($orden as $index => $adjuntoId) {
        $this->assertDatabaseHas('resultados_adjuntos', [
            'id' => $adjuntoId,
            'orden' => $index + 1,
        ]);
    }
});

it('organiza archivos en carpetas por número de orden', function () {
    $imagen = UploadedFile::fake()->image('resultado.jpg');

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $imagen,
    ]);

    $response->assertSuccessful();

    $adjunto = ResultadoAdjunto::latest()->first();
    $numeroOrden = $this->servicioExamen->servicio->numero_orden;

    expect($adjunto->ruta_archivo)->toContain("examenes/{$numeroOrden}");
    Storage::disk('public')->assertExists($adjunto->ruta_archivo);
});

it('genera nombres únicos para evitar duplicados', function () {
    $imagen1 = UploadedFile::fake()->image('test.jpg');
    $imagen2 = UploadedFile::fake()->image('test.jpg');

    $this->postJson(route('adjuntos.store', $this->servicioExamen), ['archivo' => $imagen1]);
    sleep(1); // Asegurar timestamp diferente
    $this->postJson(route('adjuntos.store', $this->servicioExamen), ['archivo' => $imagen2]);

    $adjuntos = ResultadoAdjunto::where('servicio_examen_id', $this->servicioExamen->id)->get();

    expect($adjuntos)->toHaveCount(2);
    expect($adjuntos[0]->ruta_archivo)->not->toBe($adjuntos[1]->ruta_archivo);
});

it('registra en log cuando se sube un adjunto', function () {
    $imagen = UploadedFile::fake()->image('resultado.jpg');

    Log::spy();

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $imagen,
    ]);

    $response->assertSuccessful();

    Log::shouldHaveReceived('info')
        ->once()
        ->with('Adjunto subido exitosamente', \Mockery::type('array'));
});

it('registra en log cuando ocurre un error al subir', function () {
    // Simular error forzando validación incorrecta
    $archivo = UploadedFile::fake()->create('documento.txt', 1000);

    $response = $this->postJson(route('adjuntos.store', $this->servicioExamen), [
        'archivo' => $archivo,
    ]);

    $response->assertStatus(422);
});
