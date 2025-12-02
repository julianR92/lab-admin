@extends('layouts.app')

@section('title', 'Nuevo Cliente - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Cliente
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('clientes.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-12 mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-id-card me-2 text-primary"></i>Información Personal
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">
                                    <i class="fas fa-user me-1"></i>Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                       id="apellido" name="apellido" value="{{ old('apellido') }}" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tipo_documento" class="form-label">
                                    <i class="fas fa-id-card-alt me-1"></i>Tipo Documento <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('tipo_documento') is-invalid @enderror"
                                        id="tipo_documento" name="tipo_documento" required>
                                    <option value="">Seleccione...</option>
                                    <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="TI" {{ old('tipo_documento') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                    <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="PA" {{ old('tipo_documento') == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="RC" {{ old('tipo_documento') == 'RC' ? 'selected' : '' }}>Registro Civil</option>
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="documento" class="form-label">
                                    <i class="fas fa-fingerprint me-1"></i>Número Documento <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('documento') is-invalid @enderror"
                                       id="documento" name="documento" value="{{ old('documento') }}" required>
                                @error('documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="genero" class="form-label">
                                    <i class="fas fa-venus-mars me-1"></i>Género
                                </label>
                                <select class="form-select @error('genero') is-invalid @enderror"
                                        id="genero" name="genero">
                                    <option value="">Seleccione...</option>
                                    <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="O" {{ old('genero') == 'O' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('genero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Fecha de Nacimiento <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required max="{{ date('Y-m-d') }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Información de Contacto -->
                            <div class="col-12 mt-3 mb-4">
                                <h5 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-address-book me-2 text-success"></i>Información de Contacto
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Teléfono
                                </label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="3001234567">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Dirección
                                </label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                       id="direccion" name="direccion" value="{{ old('direccion') }}" placeholder="Calle 123 # 45-67">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ciudad" class="form-label">
                                    <i class="fas fa-city me-1"></i>Ciudad
                                </label>
                                <input type="text" class="form-control @error('ciudad') is-invalid @enderror"
                                       id="ciudad" name="ciudad" value="{{ old('ciudad') }}" placeholder="Bogotá">
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="eps" class="form-label">
                                    <i class="fas fa-hospital me-1"></i>EPS
                                </label>
                                <input type="text" class="form-control @error('eps') is-invalid @enderror"
                                       id="eps" name="eps" value="{{ old('eps') }}" placeholder="Sura, Salud Total, etc.">
                                @error('eps')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
