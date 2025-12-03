@extends('layouts.app')

@section('title', 'Detalle del Examen - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-flask me-2"></i>{{ $examen->nombre }}</h2>
            <p class="text-muted">Código: <strong>{{ $examen->codigo }}</strong></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('examenes.edit', $examen) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <a href="{{ route('examenes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Información General -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">CÓDIGO</label>
                            <p class="mb-0">{{ $examen->codigo }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">CATEGORÍA</label>
                            <p class="mb-0">{{ $examen->categoria?->categoria ?? 'Sin categoría' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">NOMBRE DEL EXAMEN</label>
                            <p class="mb-0 h5">{{ $examen->nombre }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">TIPO DE RESULTADO</label>
                            <p class="mb-0">
                                @php
                                    $badges = [
                                        'NUMERICO_SIMPLE' => 'primary',
                                        'NUMERICO_CATEGORIZADO' => 'info',
                                        'CUALITATIVO_SIMPLE' => 'success',
                                        'CUALITATIVO_REACTIVO' => 'warning',
                                        'CUALITATIVO_MULTIPLE_OPCIONES' => 'secondary',
                                        'MULTIPLE_CALCULADO' => 'dark',
                                        'TABLA_HEMATOLOGIA' => 'danger',
                                        'TEXTO_DESCRIPTIVO' => 'light text-dark'
                                    ];
                                    $tiposLabels = [
                                        'NUMERICO_SIMPLE' => 'Numérico Simple',
                                        'NUMERICO_CATEGORIZADO' => 'Numérico Categorizado',
                                        'CUALITATIVO_SIMPLE' => 'Cualitativo Simple',
                                        'CUALITATIVO_REACTIVO' => 'Cualitativo Reactivo',
                                        'CUALITATIVO_MULTIPLE_OPCIONES' => 'Cualitativo Múltiples Opciones',
                                        'MULTIPLE_CALCULADO' => 'Múltiple Calculado',
                                        'TABLA_HEMATOLOGIA' => 'Tabla Hematología',
                                        'TEXTO_DESCRIPTIVO' => 'Texto Descriptivo'
                                    ];
                                    $badge = $badges[$examen->tipo_resultado] ?? 'secondary';
                                    $label = $tiposLabels[$examen->tipo_resultado] ?? $examen->tipo_resultado;
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $label }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">ESTADO</label>
                            <p class="mb-0">
                                @if($examen->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">UNIDAD DE MEDIDA</label>
                            <p class="mb-0">{{ $examen->unidad_medida ?? 'No especificada' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-muted small">MUESTRA REQUERIDA</label>
                            <p class="mb-0">{{ $examen->muestra_requerida ?? 'No especificada' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="fw-bold text-muted small">TÉCNICA</label>
                            <p class="mb-0">{{ $examen->tecnica ?? 'No especificada' }}</p>
                        </div>
                    </div>

                    @if($examen->instrucciones_paciente)
                        <div class="row">
                            <div class="col-md-12">
                                <label class="fw-bold text-muted small">INSTRUCCIONES AL PACIENTE</label>
                                <p class="mb-0 text-justify">{{ $examen->instrucciones_paciente }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Parámetros del Examen -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Parámetros del Examen ({{ $examen->parametros->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($examen->parametros->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">Orden</th>
                                        <th>Parámetro</th>
                                        <th width="100">Código</th>
                                        <th width="100">Tipo</th>
                                        <th width="100">Unidad</th>
                                        <th width="80" class="text-center">Calculado</th>
                                        <th width="80" class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examen->parametros->sortBy('orden') as $parametro)
                                        <tr>
                                            <td class="text-center">{{ $parametro->orden }}</td>
                                            <td>{{ $parametro->nombre_parametro }}</td>
                                            <td><code>{{ $parametro->codigo_parametro }}</code></td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $parametro->tipo_dato }}</span>
                                            </td>
                                            <td>{{ $parametro->unidad_medida ?? '-' }}</td>
                                            <td class="text-center">
                                                @if($parametro->es_calculado)
                                                    <i class="fas fa-calculator text-primary"></i>
                                                @else
                                                    <i class="fas fa-keyboard text-muted"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($parametro->status)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">
                            <i class="fas fa-info-circle me-2"></i>Este examen aún no tiene parámetros configurados.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Valores de Referencia -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-ruler me-2"></i>Valores de Referencia ({{ $examen->valoresReferencia->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($examen->valoresReferencia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th width="80">Género</th>
                                        <th width="100">Edad Min</th>
                                        <th width="100">Edad Max</th>
                                        <th>Rango / Valor</th>
                                        <th width="80" class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examen->valoresReferencia->sortBy('orden') as $valor)
                                        <tr>
                                            <td><span class="badge bg-info">{{ $valor->tipo_referencia }}</span></td>
                                            <td>
                                                @if($valor->genero)
                                                    <span class="badge bg-{{ $valor->genero == 'M' ? 'primary' : 'danger' }}">
                                                        {{ $valor->genero }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $valor->edad_min ?? '-' }}</td>
                                            <td>{{ $valor->edad_max ?? '-' }}</td>
                                            <td>
                                                @if($valor->tipo_referencia === 'RANGO')
                                                    {{ $valor->valor_min }} - {{ $valor->valor_max }}
                                                @elseif($valor->tipo_referencia === 'CUALITATIVO')
                                                    {{ $valor->valor_cualitativo }}
                                                @else
                                                    {{ $valor->descripcion }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($valor->status)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">
                            <i class="fas fa-info-circle me-2"></i>Este examen aún no tiene valores de referencia configurados.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Información Comercial -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Información Comercial</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">VALOR TOTAL</label>
                        <p class="mb-0 h4 text-success">${{ number_format($examen->valor_total, 0, ',', '.') }}</p>
                    </div>

                    @if($examen->valor_remision)
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">VALOR REMISIÓN</label>
                            <p class="mb-0 h5 text-info">${{ number_format($examen->valor_remision, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="fw-bold text-muted small">TIEMPO DE ENTREGA</label>
                        <p class="mb-0">{{ $examen->tiempo_entrega }} horas</p>
                    </div>

                    <div class="mb-0">
                        <label class="fw-bold text-muted small">REQUIERE AYUNO</label>
                        <p class="mb-0">
                            @if($examen->requiere_ayuno)
                                <span class="badge bg-warning">SÍ</span>
                            @else
                                <span class="badge bg-secondary">NO</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Parámetros</small>
                            <p class="mb-0 h4">{{ $examen->parametros->count() }}</p>
                        </div>
                        <i class="fas fa-list fa-2x text-primary"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <small class="text-muted">Valores de Referencia</small>
                            <p class="mb-0 h4">{{ $examen->valoresReferencia->count() }}</p>
                        </div>
                        <i class="fas fa-ruler fa-2x text-info"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Servicios Realizados</small>
                            <p class="mb-0 h4">{{ $examen->serviciosExamen->count() }}</p>
                        </div>
                        <i class="fas fa-vial fa-2x text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('examenes.edit', $examen) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Examen
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Eliminar Examen
                        </button>
                    </div>

                    <form id="deleteForm" action="{{ route('examenes.destroy', $examen) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('¿Está seguro de que desea eliminar este examen?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
