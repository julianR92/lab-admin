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
    return view('welcome');
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas de Clientes
    Route::resource('clientes', \App\Http\Controllers\ClienteController::class);

    // Rutas de Categorías de Exámenes
    Route::resource('categorias-examen', \App\Http\Controllers\CategoriaExamenController::class);

    // Rutas de Profesionales
    Route::resource('profesionales', \App\Http\Controllers\ProfesionalController::class);

    // Rutas de Empresa (solo edit y update)
    Route::get('/empresa/configuracion', [\App\Http\Controllers\EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/empresa/configuracion', [\App\Http\Controllers\EmpresaController::class, 'update'])->name('empresa.update');
    Route::delete('/empresa/logo', [\App\Http\Controllers\EmpresaController::class, 'deleteLogo'])->name('empresa.delete-logo');
});
