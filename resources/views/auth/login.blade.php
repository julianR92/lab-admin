@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Sistema de Laboratorio')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fas fa-flask"></i>
        </div>
        <h1>Lab Admin</h1>
        <p>Sistema de Gestión de Laboratorio</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error de autenticación</strong>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Correo Electrónico
            </label>
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="correo@ejemplo.com"
                required
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-2"></i>Contraseña
            </label>
            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="••••••••"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="recuerdame"
                    name="recuerdame"
                    value="true"
                >
                <label class="form-check-label" for="recuerdame">
                    Recuérdame
                </label>
            </div>
            <a href="{{ route('forgot-password') }}" class="text-decoration-none" style="color: #667eea; font-size: 13px; font-weight: 600;">
                ¿Olvidó su contraseña?
            </a>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
        </button>
    </form>

    <div class="auth-footer">
        <p><a href="#">¿No tiene cuenta? Solicite acceso aquí</a></p>
    </div>
</div>
@endsection
