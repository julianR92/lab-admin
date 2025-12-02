@extends('layouts.app')

@section('title', 'Detalle del Cliente - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user me-2"></i>Detalle del Cliente
                        </h4>
                        <div>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
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
                            <h6>{{ $cliente->nombre_completo }}</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Documento</label>
                            <h6>
                                <span class="badge bg-info">{{ $cliente->tipo_documento }}</span>
                                {{ $cliente->documento }}
                            </h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Género</label>
                            <h6>
                                @if($cliente->genero == 'M')
                                    <span class="badge bg-primary">Masculino</span>
                                @elseif($cliente->genero == 'F')
                                    <span class="badge bg-danger">Femenino</span>
                                @else
                                    <span class="badge bg-secondary">Otro</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Fecha de Nacimiento</label>
                            <h6>{{ $cliente->fecha_nacimiento->format('d/m/Y') }}</h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Edad</label>
                            <h6>{{ $cliente->edad }} años</h6>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-address-book me-2 text-success"></i>Información de Contacto
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Teléfono</label>
                            <h6>
                                @if($cliente->telefono)
                                    <i class="fas fa-phone me-2 text-success"></i>{{ $cliente->telefono }}
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Correo Electrónico</label>
                            <h6>
                                @if($cliente->email)
                                    <i class="fas fa-envelope me-2 text-primary"></i>{{ $cliente->email }}
                                @else
                                    <span class="text-muted">No registrado</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Dirección</label>
                            <h6>
                                @if($cliente->direccion)
                                    <i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $cliente->direccion }}
                                @else
                                    <span class="text-muted">No registrada</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Ciudad</label>
                            <h6>
                                @if($cliente->ciudad)
                                    <i class="fas fa-city me-2 text-info"></i>{{ $cliente->ciudad }}
                                @else
                                    <span class="text-muted">No registrada</span>
                                @endif
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">EPS</label>
                            <h6>
                                @if($cliente->eps)
                                    <i class="fas fa-hospital me-2 text-warning"></i>{{ $cliente->eps }}
                                @else
                                    <span class="text-muted">No registrada</span>
                                @endif
                            </h6>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2 text-secondary"></i>Información del Sistema
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fecha de Registro</label>
                            <h6>{{ $cliente->created_at->format('d/m/Y H:i:s') }}</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Última Actualización</label>
                            <h6>{{ $cliente->updated_at->format('d/m/Y H:i:s') }}</h6>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Este cliente tiene <strong>0</strong> servicios registrados.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
