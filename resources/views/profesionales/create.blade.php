@extends('layouts.app')

@section('title', 'Nuevo Profesional - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Profesional
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profesionales.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Información Personal -->
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2 text-primary"></i>Información Personal
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ old('nombre') }}"
                                    required
                                >
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">
                                    <i class="fas fa-user me-1"></i>Apellido <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('apellido') is-invalid @enderror"
                                    id="apellido"
                                    name="apellido"
                                    value="{{ old('apellido') }}"
                                    required
                                >
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="documento" class="form-label">
                                    <i class="fas fa-id-card me-1"></i>Documento <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('documento') is-invalid @enderror"
                                    id="documento"
                                    name="documento"
                                    value="{{ old('documento') }}"
                                    required
                                >
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información Profesional -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-graduation-cap me-2 text-success"></i>Información Profesional
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="profesion" class="form-label">
                                    <i class="fas fa-stethoscope me-1"></i>Profesión <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('profesion') is-invalid @enderror"
                                    id="profesion"
                                    name="profesion"
                                    value="{{ old('profesion') }}"
                                    placeholder="Ej: Bacteriólogo"
                                    required
                                >
                                @error('profesion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registro_profesional" class="form-label">
                                    <i class="fas fa-certificate me-1"></i>Registro Profesional <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('registro_profesional') is-invalid @enderror"
                                    id="registro_profesional"
                                    name="registro_profesional"
                                    value="{{ old('registro_profesional') }}"
                                    placeholder="Ej: RM-12345"
                                    required
                                >
                                @error('registro_profesional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="especialidad" class="form-label">
                                    <i class="fas fa-award me-1"></i>Especialidad
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('especialidad') is-invalid @enderror"
                                    id="especialidad"
                                    name="especialidad"
                                    value="{{ old('especialidad') }}"
                                    placeholder="Ej: Microbiología Clínica"
                                >
                                @error('especialidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="firma_digital" class="form-label">
                                    <i class="fas fa-signature me-1"></i>Firma Digital
                                </label>
                                <input
                                    type="file"
                                    class="form-control @error('firma_digital') is-invalid @enderror"
                                    id="firma_digital"
                                    name="firma_digital"
                                    accept="image/png,image/jpeg,image/jpg"
                                >
                                @error('firma_digital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">PNG, JPG o JPEG. Máximo 2MB</small>
                            </div>
                        </div>

                        <!-- Información de Contacto -->
                        <h5 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-address-book me-2 text-info"></i>Información de Contacto
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Teléfono
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono"
                                    name="telefono"
                                    value="{{ old('telefono') }}"
                                >
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>Estado <span class="text-danger">*</span>
                            </label>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input @error('status') is-invalid @enderror"
                                    type="checkbox"
                                    id="status"
                                    name="status"
                                    value="1"
                                    {{ old('status', '1') == '1' ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="status">
                                    Profesional activo
                                </label>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profesionales.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Profesional
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
