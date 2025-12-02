@extends('layouts.app')

@section('title', 'Detalle Categoría - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-list-alt me-2"></i>Detalle de Categoría
                        </h4>
                        <div>
                            <a href="{{ route('categorias-examen.edit', $categoriasExaman) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <a href="{{ route('categorias-examen.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="text-muted small">Nombre de la Categoría</label>
                            <h5>{{ $categoriasExaman->categoria }}</h5>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-muted small">Orden</label>
                            <h5><span class="badge bg-secondary">{{ $categoriasExaman->orden }}</span></h5>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="text-muted small">Estado</label>
                            <h5>
                                @if($categoriasExaman->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Descripción</label>
                            <p class="mb-0">
                                @if($categoriasExaman->descripcion)
                                    {{ $categoriasExaman->descripcion }}
                                @else
                                    <span class="text-muted">Sin descripción</span>
                                @endif
                            </p>
                        </div>

                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-flask me-2 text-primary"></i>Exámenes Asociados
                            </h5>
                        </div>

                        <div class="col-12">
                            @if($categoriasExaman->examenes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Tipo Resultado</th>
                                                <th>Valor</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categoriasExaman->examenes as $examen)
                                                <tr>
                                                    <td><code>{{ $examen->codigo }}</code></td>
                                                    <td>{{ $examen->nombre }}</td>
                                                    <td><span class="badge bg-info">{{ $examen->tipo_resultado }}</span></td>
                                                    <td>${{ number_format($examen->valor_total, 0) }}</td>
                                                    <td>
                                                        @if($examen->status)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No hay exámenes asociados a esta categoría.
                                </div>
                            @endif
                        </div>

                        <div class="col-12 mt-3 mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2 text-secondary"></i>Información del Sistema
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fecha de Registro</label>
                            <h6>{{ $categoriasExaman->created_at->format('d/m/Y H:i:s') }}</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Última Actualización</label>
                            <h6>{{ $categoriasExaman->updated_at->format('d/m/Y H:i:s') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
