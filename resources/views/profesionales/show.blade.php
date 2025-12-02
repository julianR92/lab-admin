@extends('layouts.app')

@section('title', 'Detalle Profesional - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-md me-2"></i>Detalle del Profesional
                        </h4>
                        <div>
                            <a href="{{ route('profesionales.edit', $profesionale) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('profesionales.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Información Personal -->
                        <div class="col-12 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-id-card me-2 text-primary"></i>Información Personal
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nombre Completo</label>
                            <h6>{{ $profesionale->nombre_completo }}</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Documento</label>
                            <h6>{{ $profesionale->documento }}</h6>
                        </div>

                        <!-- Información Profesional -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-graduation-cap me-2 text-success"></i>Información Profesional
                            </h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Profesión</label>
                            <h6>{{ $profesionale->profesion }}</h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Registro Profesional</label>
                            <h6><code>{{ $profesionale->registro_profesional }}</code></h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Estado</label>
                            <h6>
                                @if($profesionale->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Especialidad</label>
                            <h6>
                                @if($profesionale->especialidad)
                                    {{ $profesionale->especialidad }}
                                @else
                                    <span class="text-muted">No especificada</span>
                                @endif
                            </h6>
                        </div>

                        @if($profesionale->firma_digital)
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">Firma Digital</label>
                                <div>
                                    <img src="{{ asset('storage/' . $profesionale->firma_digital) }}" alt="Firma" class="img-thumbnail" style="max-height: 150px; background: #f8f9fa;">
                                </div>
                            </div>
                        @endif

                        <!-- Información de Contacto -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-address-book me-2 text-info"></i>Información de Contacto
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Teléfono</label>
                            <h6>
                                @if($profesionale->telefono)
                                    <i class="fas fa-phone me-2 text-success"></i>{{ $profesionale->telefono }}
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Correo Electrónico</label>
                            <h6>
                                @if($profesionale->email)
                                    <i class="fas fa-envelope me-2 text-primary"></i>{{ $profesionale->email }}
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </h6>
                        </div>

                        <!-- Estadísticas -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar me-2 text-warning"></i>Estadísticas
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-1">{{ $profesionale->servicios_examen_count }}</h3>
                                    <p class="text-muted small mb-0">Servicios Asociados</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-1">{{ $profesionale->resultados_validados_count }}</h3>
                                    <p class="text-muted small mb-0">Resultados Validados</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2 text-secondary"></i>Información del Sistema
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fecha de Registro</label>
                            <h6>{{ $profesionale->created_at->format('d/m/Y H:i:s') }}</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Última Actualización</label>
                            <h6>{{ $profesionale->updated_at->format('d/m/Y H:i:s') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
