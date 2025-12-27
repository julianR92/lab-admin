<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Rutas de Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de Recuperación de Contraseña
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('send-reset-link');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('reset-password');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('reset-password');

Route::get('/', function () {
    $empresa = \App\Models\Empresa::first();
    return view('welcome', compact('empresa'));
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $totalPacientes = \App\Models\Cliente::count();
        $examenesPendientes = \App\Models\ServicioExamen::where('estado', 'PENDIENTE')->count();
        $resultadosValidados = \App\Models\ServicioExamen::where('estado', 'VALIDADO')->count();
        $ingresosHoy = \App\Models\Servicio::whereDate('fecha', today())->sum('valor_pagado');

        return view('dashboard', compact('totalPacientes', 'examenesPendientes', 'resultadosValidados', 'ingresosHoy'));
    })->name('dashboard');

    // Rutas de Clientes
    Route::resource('clientes', \App\Http\Controllers\ClienteController::class);

    // Rutas de Categorías de Exámenes
    Route::resource('categorias-examen', \App\Http\Controllers\CategoriaExamenController::class);

    // Rutas de Profesionales
    Route::resource('profesionales', \App\Http\Controllers\ProfesionalController::class);

    // Rutas de Exámenes
    Route::resource('examenes', \App\Http\Controllers\ExamenController::class);

    // Rutas de Parámetros de Exámenes (CRUD anidado)
    Route::post('examen-parametros', [\App\Http\Controllers\ExamenParametroController::class, 'store'])->name('examen-parametros.store');
    Route::get('examen-parametros/{examenParametro}/edit', [\App\Http\Controllers\ExamenParametroController::class, 'edit'])->name('examen-parametros.edit');
    Route::put('examen-parametros/{examenParametro}', [\App\Http\Controllers\ExamenParametroController::class, 'update'])->name('examen-parametros.update');
    Route::delete('examen-parametros/{examenParametro}', [\App\Http\Controllers\ExamenParametroController::class, 'destroy'])->name('examen-parametros.destroy');

    // Rutas de Valores de Referencia de Exámenes (CRUD anidado)
    Route::post('examen-valores-referencia', [\App\Http\Controllers\ExamenValorReferenciaController::class, 'store'])->name('examen-valores-referencia.store');
    Route::get('examen-valores-referencia/{examenValorReferencia}/edit', [\App\Http\Controllers\ExamenValorReferenciaController::class, 'edit'])->name('examen-valores-referencia.edit');
    Route::put('examen-valores-referencia/{examenValorReferencia}', [\App\Http\Controllers\ExamenValorReferenciaController::class, 'update'])->name('examen-valores-referencia.update');
    Route::delete('examen-valores-referencia/{examenValorReferencia}', [\App\Http\Controllers\ExamenValorReferenciaController::class, 'destroy'])->name('examen-valores-referencia.destroy');

    // Rutas de Empresa (solo edit y update)
    Route::get('/empresa/configuracion', [\App\Http\Controllers\EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa/configuracion', [\App\Http\Controllers\EmpresaController::class, 'update'])->name('empresa.update');
    Route::delete('/empresa/logo', [\App\Http\Controllers\EmpresaController::class, 'deleteLogo'])->name('empresa.delete-logo');

    // Rutas de Servicios
    Route::resource('servicios', \App\Http\Controllers\ServicioController::class);
    Route::get('servicios/{servicio}/orden-pdf', [\App\Http\Controllers\ServicioController::class, 'descargarOrden'])->name('servicios.descargar-orden');
    Route::post('servicios/{servicio}/pago', [\App\Http\Controllers\ServicioController::class, 'registrarPago'])->name('servicios.registrar-pago');
    Route::post('servicio-examen/{servicioExamen}/profesional', [\App\Http\Controllers\ServicioController::class, 'asignarProfesional'])->name('servicios.asignar-profesional');
    Route::post('servicio-examen/{servicioExamen}/estado', [\App\Http\Controllers\ServicioController::class, 'cambiarEstado'])->name('servicios.cambiar-estado');

    // Rutas de Perfil
    Route::get('/perfil', [\App\Http\Controllers\PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [\App\Http\Controllers\PerfilController::class, 'update'])->name('perfil.update');
});

// Rutas API
Route::prefix('api')->group(function () {
    Route::get('/clientes/buscar', [\App\Http\Controllers\Api\ClienteController::class, 'buscar'])->name('api.clientes.buscar');
});
