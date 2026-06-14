@extends('layouts.app')

@section('title', 'Editar Laboratorio - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>Editar Laboratorio
                    </h4>
                    <a href="{{ route('laboratorios.show', $laboratorio) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Ver exámenes
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('laboratorios.update', $laboratorio) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre"
                                    value="{{ old('nombre', $laboratorio->nombre) }}"
                                    required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nit" class="form-label">NIT</label>
                                <input type="text"
                                    class="form-control @error('nit') is-invalid @enderror"
                                    id="nit" name="nit"
                                    value="{{ old('nit', $laboratorio->nit) }}">
                                @error('nit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text"
                                    class="form-control @error('ciudad') is-invalid @enderror"
                                    id="ciudad" name="ciudad"
                                    value="{{ old('ciudad', $laboratorio->ciudad) }}">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono"
                                    value="{{ old('telefono', $laboratorio->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', $laboratorio->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="contacto" class="form-label">Persona de Contacto</label>
                                <input type="text"
                                    class="form-control @error('contacto') is-invalid @enderror"
                                    id="contacto" name="contacto"
                                    value="{{ old('contacto', $laboratorio->contacto) }}">
                                @error('contacto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="status" name="status" value="1"
                                        {{ old('status', $laboratorio->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Laboratorio activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('laboratorios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
