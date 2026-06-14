@extends('layouts.app')

@section('title', 'Nuevo Laboratorio - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>Registrar Laboratorio de Remisión
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('laboratorios.store') }}" method="POST">
                        @csrf

                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Información del Laboratorio
                        </h5>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre"
                                    value="{{ old('nombre') }}"
                                    placeholder="Ej: Laboratorio Clínico Central"
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
                                    value="{{ old('nit') }}"
                                    placeholder="Ej: 900123456-7">
                                @error('nit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label">Ciudad</label>
                                <input type="text"
                                    class="form-control @error('ciudad') is-invalid @enderror"
                                    id="ciudad" name="ciudad"
                                    value="{{ old('ciudad') }}"
                                    placeholder="Ej: Bogotá">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text"
                                    class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono"
                                    value="{{ old('telefono') }}"
                                    placeholder="Ej: 601-1234567">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email') }}"
                                    placeholder="contacto@laboratorio.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="contacto" class="form-label">Persona de Contacto</label>
                                <input type="text"
                                    class="form-control @error('contacto') is-invalid @enderror"
                                    id="contacto" name="contacto"
                                    value="{{ old('contacto') }}"
                                    placeholder="Nombre del responsable">
                                @error('contacto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        id="status" name="status" value="1"
                                        {{ old('status', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Laboratorio activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('laboratorios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Laboratorio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
