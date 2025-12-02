@extends('layouts.auth')

@section('title', 'Recuperar Contraseña - Sistema de Laboratorio')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-key"></i>
        </div>
        <h1>Recuperar Contraseña</h1>
        <p>Ingrese su correo electrónico para recibir el enlace de recuperación</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('send-reset-link') }}" method="POST" novalidate>
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
            <small class="text-muted d-block mt-2" style="font-size: 12px;">
                <i class="fas fa-info-circle me-1"></i>
                Recibirá un enlace de recuperación en su correo
            </small>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-paper-plane me-2"></i>Enviar Enlace
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
