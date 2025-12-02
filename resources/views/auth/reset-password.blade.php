@extends('layouts.auth')

@section('title', 'Restablecer Contraseña - Sistema de Laboratorio')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1>Restablecer Contraseña</h1>
        <p>Ingrese su nueva contraseña para recuperar acceso a su cuenta</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('reset-password') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

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
                <i class="fas fa-lock me-2"></i>Nueva Contraseña
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
            <small class="text-muted d-block mt-2" style="font-size: 12px;">
                <i class="fas fa-info-circle me-1"></i>
                Mínimo 8 caracteres
            </small>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-2"></i>Confirmar Contraseña
            </label>
            <input
                type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="••••••••"
                required
            >
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-check-circle me-2"></i>Restablecer Contraseña
        </button>

        <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 mt-3" style="border-radius: 10px; font-weight: 600;">
            <i class="fas fa-arrow-left me-2"></i>Volver a Iniciar Sesión
        </a>
    </form>

    <div class="auth-footer" style="margin-top: 25px;">
        <p><small class="text-muted">¿Necesita ayuda? <a href="mailto:soporte@laboratorio.com">Contacte al soporte</a></small></p>
    </div>
</div>
@endsection
