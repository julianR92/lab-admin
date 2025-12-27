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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
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
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Parámetros del Examen ({{ $examen->parametros->count() }})</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#parametroModal" onclick="resetParametroModal()">
                        <i class="fas fa-plus me-2"></i>Nuevo Parámetro
                    </button>
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
                                        <th width="80" class="text-center">Requerido</th>
                                        <th width="80" class="text-center">Estado</th>
                                        <th width="100" class="text-center">Acciones</th>
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
                                                    <i class="fas fa-calculator text-primary" title="Calculado"></i>
                                                @else
                                                    <i class="fas fa-keyboard text-muted" title="Manual"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($parametro->requerido)
                                                    <i class="fas fa-check-circle text-success" title="Requerido"></i>
                                                @else
                                                    <i class="fas fa-minus-circle text-muted" title="Opcional"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($parametro->status)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editParametro({{ $parametro->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteParametro({{ $parametro->id }})" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-ruler me-2"></i>Valores de Referencia ({{ $examen->valoresReferencia->count() }})</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#valorReferenciaModal">
                        <i class="fas fa-plus me-2"></i>Agregar Valor de Referencia
                    </button>
                </div>
                <div class="card-body">
                    @if($examen->valoresReferencia->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">Orden</th>
                                        <th width="80">Parámetro</th>
                                        <th>Tipo</th>
                                        <th width="80">Género</th>
                                        <th width="80">Edad Min</th>
                                        <th width="80">Edad Max</th>
                                        <th>Rango / Valor</th>
                                        <th width="80" class="text-center">Estado</th>
                                        <th width="100" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examen->valoresReferencia->sortBy('orden') as $valor)
                                        <tr>
                                            <td class="text-center">{{ $valor->orden }}</td>
                                            <td>
                                                @if($valor->parametro)
                                                    <code class="small">{{ $valor->parametro->codigo_parametro }}</code>
                                                @else
                                                    <span class="text-muted small">General</span>
                                                @endif
                                            </td>
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
                                                    <strong>{{ $valor->valor_min }} - {{ $valor->valor_max }}</strong>
                                                    @if($valor->parametro && $valor->parametro->unidad_medida)
                                                        <span class="text-muted">{{ $valor->parametro->unidad_medida }}</span>
                                                    @endif
                                                @elseif($valor->tipo_referencia === 'CUALITATIVO')
                                                    <span class="badge bg-secondary">{{ $valor->valor_cualitativo }}</span>
                                                @elseif($valor->tipo_referencia === 'CATEGORIZADO')
                                                    <strong>{{ $valor->categoria }}:</strong> {{ $valor->valor_min }} - {{ $valor->valor_max }}
                                                    @if($valor->operador)
                                                        <span class="text-muted">({{ $valor->operador }})</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ Str::limit($valor->descripcion, 50) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($valor->status)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-info" onclick="editValorReferencia({{ $valor->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('examen-valores-referencia.destroy', $valor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este valor de referencia?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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

<!-- Modal Crear/Editar Parámetro -->
<div class="modal fade" id="parametroModal" tabindex="-1" aria-labelledby="parametroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="parametroForm" method="POST" action="{{ route('examen-parametros.store') }}">
                @csrf
                <input type="hidden" name="_method" id="parametroMethod" value="POST">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="parametro_id" id="parametroId">
                <input type="hidden" name="_form_type" value="parametro">

                <div class="modal-header">
                    <h5 class="modal-title" id="parametroModalLabel">
                        <i class="fas fa-plus-circle me-2"></i><span id="modalTitle">Nuevo Parámetro</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->any() && old('_form_type') === 'parametro')
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Errores de Validación:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_parametro" class="form-label">Nombre del Parámetro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_parametro" name="nombre_parametro" required maxlength="255" placeholder="Ej: Glucosa, Hemoglobina">
                            </div>

                            <div class="mb-3">
                                <label for="seccion" class="form-label">Sección / Subtítulo</label>
                                <input type="text" class="form-control" id="seccion" name="seccion" maxlength="100" placeholder="Ej: Hemograma, Perfil Lipídico">
                                <small class="text-muted">Opcional - Agrupa parámetros bajo un subtítulo</small>
                            </div>

                            <div class="mb-3">
                                <label for="codigo_parametro" class="form-label">Código del Parámetro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo_parametro" name="codigo_parametro" required maxlength="50" placeholder="Ej: GLUCOSA, HGB">
                                <small class="text-muted">Código único dentro del examen (sin espacios, mayúsculas)</small>
                            </div>

                            <div class="mb-3">
                                <label for="tipo_dato" class="form-label">Tipo de Dato <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_dato" name="tipo_dato" required onchange="toggleTipoDatoFields()">
                                    <option value="">Seleccione...</option>
                                    <option value="DECIMAL">Decimal (con decimales)</option>
                                    <option value="INTEGER">Entero (sin decimales)</option>
                                    <option value="TEXT">Texto libre</option>
                                    <option value="SELECT">Opciones predefinidas (SELECT)</option>
                                </select>
                            </div>

                            <div class="mb-3" id="field_unidad_medida">
                                <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" maxlength="50" placeholder="Ej: mg/dL, g/dL, %">
                            </div>

                            <div class="mb-3" id="field_decimales" style="display:none;">
                                <label for="decimales" class="form-label">Cantidad de Decimales</label>
                                <input type="number" class="form-control" id="decimales" name="decimales" min="0" max="4" value="2">
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden de Presentación <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="orden" name="orden" required min="1" value="{{ $examen->parametros->count() + 1 }}">
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="es_calculado" name="es_calculado" onchange="toggleCalculadoFields()">
                                    <label class="form-check-label" for="es_calculado">
                                        <i class="fas fa-calculator me-1"></i>Es Calculado Automáticamente
                                    </label>
                                </div>
                                <small class="text-muted">Se calcula con fórmula, no se captura manualmente</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="requerido" name="requerido" checked>
                                    <label class="form-check-label" for="requerido">
                                        Campo Requerido
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                    <label class="form-check-label" for="status">
                                        Estado Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campo Fórmula (solo si es calculado) -->
                    <div id="field_formula" style="display:none;">
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-calculator me-2"></i>Configuración de Fórmula</h6>
                        <div class="mb-3">
                            <label for="formula_formula" class="form-label">Fórmula Matemática <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="formula_formula" name="formula_calculo[formula]" placeholder="Ej: (CREAT_URINARIA * VOLUMEN_24H) / (CREAT_SERICA * 1440)">
                            <small class="text-muted">Use códigos de otros parámetros en mayúsculas</small>
                        </div>
                        <div class="mb-3">
                            <label for="formula_parametros" class="form-label">Parámetros Usados (separados por coma) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="formula_parametros" name="formula_parametros_input" placeholder="Ej: CREAT_URINARIA, VOLUMEN_24H, CREAT_SERICA">
                            <small class="text-muted">Códigos de los parámetros que usa la fórmula</small>
                        </div>
                        <div class="mb-3">
                            <label for="formula_descripcion" class="form-label">Descripción de la Fórmula</label>
                            <textarea class="form-control" id="formula_descripcion" name="formula_calculo[descripcion]" rows="2" placeholder="Ej: Clearance de creatinina en mL/min"></textarea>
                        </div>
                    </div>

                    <!-- Campo Opciones SELECT -->
                    <div id="field_opciones_select" style="display:none;">
                        <hr>
                        <h6 class="text-success"><i class="fas fa-list-ul me-2"></i>Opciones del SELECT</h6>
                        <div class="mb-3">
                            <label for="opciones_select_input" class="form-label">Opciones (una por línea) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="opciones_select_input" name="opciones_select_input" rows="6" placeholder="Negativo&#10;Positivo&#10;No reactivo&#10;Reactivo"></textarea>
                            <small class="text-muted">Escriba cada opción en una línea diferente</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i><span id="submitButtonText">Guardar Parámetro</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Valor de Referencia -->
<div class="modal fade" id="valorReferenciaModal" tabindex="-1" aria-labelledby="valorReferenciaModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="valorReferenciaForm" method="POST" action="{{ route('examen-valores-referencia.store') }}">
                @csrf
                <input type="hidden" name="_method" id="valorReferenciaMethod" value="POST">
                <input type="hidden" name="examen_id" value="{{ $examen->id }}">
                <input type="hidden" name="valor_referencia_id" id="valorReferenciaId" value="">
                <input type="hidden" name="_form_type" value="valor_referencia">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="valorReferenciaModalTitle">
                        <i class="fas fa-ruler me-2"></i><span id="modalTitleVR">Nuevo Valor de Referencia</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="resetValorReferenciaModal()"></button>
                </div>

                <div class="modal-body">
                    <!-- Mostrar errores de validación -->
                    @if ($errors->any() && old('_form_type') === 'valor_referencia')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Errores de validación:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parametro_id" class="form-label">Parámetro (opcional)</label>
                                <select class="form-select" id="parametro_id" name="parametro_id">
                                    <option value="">General (aplica a todo el examen)</option>
                                    @foreach($examen->parametros->sortBy('orden') as $parametro)
                                        <option value="{{ $parametro->id }}">{{ $parametro->codigo_parametro }} - {{ $parametro->nombre_parametro }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Dejar vacío para valores generales del examen</small>
                            </div>

                            <div class="mb-3">
                                <label for="tipo_referencia" class="form-label">Tipo de Referencia <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipo_referencia" name="tipo_referencia" required onchange="toggleTipoReferenciaFields()">
                                    <option value="">Seleccione...</option>
                                    <option value="RANGO">Rango (Min-Max)</option>
                                    <option value="CUALITATIVO">Cualitativo</option>
                                    <option value="CATEGORIZADO">Categorizado</option>
                                    <option value="INFORMATIVO">Informativo</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden de Evaluación <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="orden_vr" name="orden" min="1" max="999" required>
                                <small class="text-muted">Los valores con menor orden se evalúan primero</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="fas fa-filter me-2"></i>Contexto Clínico (opcional)</h6>

                            <div class="mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-select" id="genero" name="genero">
                                    <option value="">Ambos</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="edad_min" class="form-label">Edad Mínima</label>
                                        <input type="number" class="form-control" id="edad_min" name="edad_min" min="0" max="120" placeholder="Años">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="edad_max" class="form-label">Edad Máxima</label>
                                        <input type="number" class="form-control" id="edad_max" name="edad_max" min="0" max="120" placeholder="Años">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="condicion_especial" class="form-label">Condición Especial</label>
                                <input type="text" class="form-control" id="condicion_especial" name="condicion_especial" placeholder="Ej: Embarazo, Diabetes, etc.">
                            </div>
                        </div>
                    </div>

                    <!-- Campos según tipo_referencia -->
                    <div id="field_rango" style="display:none;">
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-arrows-alt-h me-2"></i>Valores de Rango</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_min" class="form-label">Valor Mínimo <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control" id="valor_min" name="valor_min">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valor_max" class="form-label">Valor Máximo <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control" id="valor_max" name="valor_max">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="field_cualitativo" style="display:none;">
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-check-circle me-2"></i>Valor Cualitativo</h6>
                        <div class="mb-3">
                            <label for="valor_cualitativo" class="form-label">Valor Esperado <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="valor_cualitativo" name="valor_cualitativo" placeholder="Ej: Negativo, Positivo, Reactivo, etc.">
                        </div>
                    </div>

                    <div id="field_categorizado" style="display:none;">
                        <hr>
                        <h6 class="text-primary"><i class="fas fa-layer-group me-2"></i>Valores Categorizados</h6>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoria" name="categoria" placeholder="Ej: Óptimo, Normal, Alto, Crítico">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_min_cat" class="form-label">Valor Mínimo <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control" id="valor_min_cat" name="valor_min">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valor_max_cat" class="form-label">Valor Máximo <span class="text-danger">*</span></label>
                                    <input type="number" step="0.0001" class="form-control" id="valor_max_cat" name="valor_max">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="operador" class="form-label">Operador</label>
                                    <select class="form-select" id="operador" name="operador">
                                        <option value="">Ninguno</option>
                                        <option value="<"><</option>
                                        <option value="<="><=</option>
                                        <option value=">">></option>
                                        <option value=">=">>=</option>
                                        <option value="==">==</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción / Notas</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Información adicional sobre este valor de referencia"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status_vr" name="status" checked>
                            <label class="form-check-label" for="status_vr">
                                Estado Activo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetValorReferenciaModal()">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i><span id="submitButtonTextVR">Guardar Valor de Referencia</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('¿Está seguro de que desea eliminar este examen?\n\nEsta acción no se puede deshacer.')) {
        document.getElementById('deleteForm').submit();
    }
}

// ========== FUNCIONES PARÁMETROS ==========

// Resetear modal para nuevo parámetro
function resetParametroModal() {
    document.getElementById('parametroForm').reset();
    document.getElementById('parametroMethod').value = 'POST';
    document.getElementById('parametroForm').action = '{{ route("examen-parametros.store") }}';
    document.getElementById('modalTitle').textContent = 'Nuevo Parámetro';
    document.getElementById('submitButtonText').textContent = 'Guardar Parámetro';
    document.getElementById('parametroId').value = '';
    document.getElementById('orden').value = {{ $examen->parametros->count() + 1 }};

    // Resetear campos condicionales
    document.getElementById('field_decimales').style.display = 'none';
    document.getElementById('field_formula').style.display = 'none';
    document.getElementById('field_opciones_select').style.display = 'none';
}

// Toggle campos según tipo de dato
function toggleTipoDatoFields() {
    const tipoDato = document.getElementById('tipo_dato').value;

    // Campo decimales solo para DECIMAL
    document.getElementById('field_decimales').style.display = tipoDato === 'DECIMAL' ? 'block' : 'none';

    // Campo opciones SELECT solo para SELECT
    document.getElementById('field_opciones_select').style.display = tipoDato === 'SELECT' ? 'block' : 'none';

    // Unidad de medida no aplica para TEXT
    document.getElementById('field_unidad_medida').style.display = tipoDato === 'TEXT' ? 'none' : 'block';
}

// Toggle campos de fórmula
function toggleCalculadoFields() {
    const esCalculado = document.getElementById('es_calculado').checked;
    document.getElementById('field_formula').style.display = esCalculado ? 'block' : 'none';

    // Si NO es calculado, limpiar los campos de fórmula
    if (!esCalculado) {
        document.getElementById('formula_formula').value = '';
        document.getElementById('formula_parametros').value = '';
        document.getElementById('formula_descripcion').value = '';
    }

    // Si es calculado, no puede ser requerido (se calcula automáticamente)
    if (esCalculado) {
        document.getElementById('requerido').checked = false;
        document.getElementById('requerido').disabled = true;
    } else {
        document.getElementById('requerido').disabled = false;
    }
}

// Convertir código de parámetro a mayúsculas y eliminar espacios
document.getElementById('codigo_parametro').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase().replace(/\s/g, '');
});

// Editar parámetro
function editParametro(parametroId) {
    fetch(`/examen-parametros/${parametroId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const parametro = data.parametro;

            // Cambiar a modo edición
            document.getElementById('parametroMethod').value = 'PUT';
            document.getElementById('parametroForm').action = `/examen-parametros/${parametroId}`;
            document.getElementById('modalTitle').textContent = 'Editar Parámetro';
            document.getElementById('submitButtonText').textContent = 'Actualizar Parámetro';
            document.getElementById('parametroId').value = parametro.id;

            // Llenar campos
            document.getElementById('nombre_parametro').value = parametro.nombre_parametro;
            document.getElementById('seccion').value = parametro.seccion || '';
            document.getElementById('codigo_parametro').value = parametro.codigo_parametro;
            document.getElementById('tipo_dato').value = parametro.tipo_dato;
            document.getElementById('unidad_medida').value = parametro.unidad_medida || '';
            document.getElementById('decimales').value = parametro.decimales || 2;
            document.getElementById('orden').value = parametro.orden;
            document.getElementById('es_calculado').checked = parametro.es_calculado;
            document.getElementById('requerido').checked = parametro.requerido;
            document.getElementById('status').checked = parametro.status;

            // Formula
            if (parametro.formula_calculo && parametro.es_calculado) {
                document.getElementById('formula_formula').value = parametro.formula_calculo.formula || '';
                document.getElementById('formula_parametros').value = parametro.formula_calculo.parametros ? parametro.formula_calculo.parametros.join(', ') : '';
                document.getElementById('formula_descripcion').value = parametro.formula_calculo.descripcion || '';
            }

            // Opciones SELECT
            if (parametro.opciones_select && parametro.tipo_dato === 'SELECT') {
                document.getElementById('opciones_select_input').value = parametro.opciones_select.join('\n');
            }

            // Actualizar visibilidad de campos
            toggleTipoDatoFields();
            toggleCalculadoFields();

            // Mostrar modal
            new bootstrap.Modal(document.getElementById('parametroModal')).show();
        } else {
            alert('Error al cargar el parámetro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar el parámetro');
    });
}

// Confirmar eliminación
function confirmDeleteParametro(parametroId) {
    if (confirm('¿Está seguro de que desea eliminar este parámetro?\n\nSe eliminarán también sus valores de referencia asociados.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/examen-parametros/${parametroId}`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Procesar formulario antes de enviar
document.getElementById('parametroForm').addEventListener('submit', function(e) {
    // Convertir opciones_select de textarea a array
    const opcionesInput = document.getElementById('opciones_select_input');
    if (opcionesInput.value.trim() && document.getElementById('tipo_dato').value === 'SELECT') {
        const opciones = opcionesInput.value.split('\n').filter(opt => opt.trim() !== '');
        opciones.forEach((opcion, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `opciones_select[${index}]`;
            input.value = opcion.trim();
            this.appendChild(input);
        });
    }

    // Convertir parametros de formula a array
    const parametrosInput = document.getElementById('formula_parametros');
    if (parametrosInput.value.trim() && document.getElementById('es_calculado').checked) {
        const parametros = parametrosInput.value.split(',').map(p => p.trim()).filter(p => p !== '');
        parametros.forEach((param, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `formula_calculo[parametros][${index}]`;
            input.value = param;
            this.appendChild(input);
        });
    }
});

// Abrir modal de parámetro si hay errores de validación y restaurar datos
@if ($errors->any() && old('_form_type') === 'parametro')
    document.addEventListener('DOMContentLoaded', function() {
        // Determinar si es edición o creación según el método
        const method = '{{ old("_method") }}';
        const parametroId = '{{ old("parametro_id") }}';

        if (method === 'PUT' && parametroId) {
            // Es una edición
            document.getElementById('parametroMethod').value = 'PUT';
            document.getElementById('parametroForm').action = `/examen-parametros/${parametroId}`;
            document.getElementById('modalTitle').textContent = 'Editar Parámetro';
            document.getElementById('submitButtonText').textContent = 'Actualizar Parámetro';
            document.getElementById('parametroId').value = parametroId;
        } else {
            // Es una creación
            document.getElementById('parametroMethod').value = 'POST';
            document.getElementById('parametroForm').action = '{{ route("examen-parametros.store") }}';
            document.getElementById('modalTitle').textContent = 'Nuevo Parámetro';
            document.getElementById('submitButtonText').textContent = 'Guardar Parámetro';
        }

        // Restaurar valores del formulario
        document.getElementById('nombre_parametro').value = '{{ old("nombre_parametro") }}';
        document.getElementById('seccion').value = '{{ old("seccion") }}';
        document.getElementById('codigo_parametro').value = '{{ old("codigo_parametro") }}';
        document.getElementById('tipo_dato').value = '{{ old("tipo_dato") }}';
        document.getElementById('unidad_medida').value = '{{ old("unidad_medida") }}';
        document.getElementById('decimales').value = '{{ old("decimales", 2) }}';
        document.getElementById('orden').value = '{{ old("orden") }}';
        document.getElementById('es_calculado').checked = {{ old("es_calculado") ? 'true' : 'false' }};
        document.getElementById('requerido').checked = {{ old("requerido") ? 'true' : 'false' }};
        document.getElementById('status').checked = {{ old("status", true) ? 'true' : 'false' }};

        // Restaurar fórmula si existe
        @if(old('formula_calculo.formula'))
            document.getElementById('formula_formula').value = '{{ old("formula_calculo.formula") }}';
        @endif
        @if(old('formula_calculo.descripcion'))
            document.getElementById('formula_descripcion').value = `{!! str_replace(["\r\n", "\n", "\r"], "\\n", old("formula_calculo.descripcion", "")) !!}`;
        @endif
        @php
            $oldParametros = old('formula_calculo.parametros');
            $parametrosText = '';
            if ($oldParametros && is_array($oldParametros)) {
                $parametrosText = implode(', ', $oldParametros);
            } elseif (old('formula_parametros_input')) {
                $parametrosText = old('formula_parametros_input');
            }
        @endphp
        @if($parametrosText)
            document.getElementById('formula_parametros').value = '{{ $parametrosText }}';
        @endif

        // Restaurar opciones SELECT si existen
        @php
            $oldOpciones = old('opciones_select');
            $opcionesText = '';
            if ($oldOpciones && is_array($oldOpciones)) {
                $opcionesText = implode("\n", $oldOpciones);
            } elseif (old('opciones_select_input')) {
                $opcionesText = old('opciones_select_input');
            }
        @endphp
        @if($opcionesText)
            document.getElementById('opciones_select_input').value = `{!! str_replace("\n", "\\n", $opcionesText) !!}`;
        @endif

        // Actualizar visibilidad de campos según tipo de dato y calculado
        toggleTipoDatoFields();
        toggleCalculadoFields();

        // Mostrar modal
        new bootstrap.Modal(document.getElementById('parametroModal')).show();
    });
@endif

// ========== FUNCIONES VALORES DE REFERENCIA ==========

// Resetear modal para nuevo valor de referencia
function resetValorReferenciaModal() {
    document.getElementById('valorReferenciaForm').reset();
    document.getElementById('valorReferenciaMethod').value = 'POST';
    document.getElementById('valorReferenciaForm').action = '{{ route("examen-valores-referencia.store") }}';
    document.getElementById('modalTitleVR').textContent = 'Nuevo Valor de Referencia';
    document.getElementById('submitButtonTextVR').textContent = 'Guardar Valor de Referencia';
    document.getElementById('valorReferenciaId').value = '';
    document.getElementById('orden_vr').value = {{ $examen->valoresReferencia->count() + 1 }};

    // Resetear campos condicionales y habilitar todos
    document.getElementById('field_rango').style.display = 'none';
    document.getElementById('field_cualitativo').style.display = 'none';
    document.getElementById('field_categorizado').style.display = 'none';

    // Habilitar todos los campos (por si quedaron deshabilitados)
    document.getElementById('valor_min').disabled = false;
    document.getElementById('valor_max').disabled = false;
    document.getElementById('valor_cualitativo').disabled = false;
    document.getElementById('categoria').disabled = false;
    document.getElementById('valor_min_cat').disabled = false;
    document.getElementById('valor_max_cat').disabled = false;
    document.getElementById('operador').disabled = false;
}

// Mostrar/ocultar campos según tipo de referencia
function toggleTipoReferenciaFields() {
    const tipoReferencia = document.getElementById('tipo_referencia').value;

    // Ocultar y deshabilitar todos los campos
    document.getElementById('field_rango').style.display = 'none';
    document.getElementById('field_cualitativo').style.display = 'none';
    document.getElementById('field_categorizado').style.display = 'none';

    // Deshabilitar todos los inputs para que no se envíen
    document.getElementById('valor_min').disabled = true;
    document.getElementById('valor_max').disabled = true;
    document.getElementById('valor_cualitativo').disabled = true;
    document.getElementById('categoria').disabled = true;
    document.getElementById('valor_min_cat').disabled = true;
    document.getElementById('valor_max_cat').disabled = true;
    document.getElementById('operador').disabled = true;

    // Mostrar y habilitar campos según tipo
    switch(tipoReferencia) {
        case 'RANGO':
            document.getElementById('field_rango').style.display = 'block';
            document.getElementById('valor_min').disabled = false;
            document.getElementById('valor_max').disabled = false;
            // Limpiar campos no usados
            document.getElementById('valor_cualitativo').value = '';
            document.getElementById('categoria').value = '';
            document.getElementById('operador').value = '';
            document.getElementById('valor_min_cat').value = '';
            document.getElementById('valor_max_cat').value = '';
            break;
        case 'CUALITATIVO':
            document.getElementById('field_cualitativo').style.display = 'block';
            document.getElementById('valor_cualitativo').disabled = false;
            // Limpiar campos no usados
            document.getElementById('valor_min').value = '';
            document.getElementById('valor_max').value = '';
            document.getElementById('categoria').value = '';
            document.getElementById('operador').value = '';
            document.getElementById('valor_min_cat').value = '';
            document.getElementById('valor_max_cat').value = '';
            break;
        case 'CATEGORIZADO':
            document.getElementById('field_categorizado').style.display = 'block';
            document.getElementById('categoria').disabled = false;
            document.getElementById('valor_min_cat').disabled = false;
            document.getElementById('valor_max_cat').disabled = false;
            document.getElementById('operador').disabled = false;
            // Limpiar campos no usados
            document.getElementById('valor_cualitativo').value = '';
            document.getElementById('valor_min').value = '';
            document.getElementById('valor_max').value = '';
            break;
        case 'INFORMATIVO':
            // Solo descripción, limpiar todos los valores
            document.getElementById('valor_min').value = '';
            document.getElementById('valor_max').value = '';
            document.getElementById('valor_cualitativo').value = '';
            document.getElementById('categoria').value = '';
            document.getElementById('operador').value = '';
            document.getElementById('valor_min_cat').value = '';
            document.getElementById('valor_max_cat').value = '';
            break;
    }
}

// Editar valor de referencia
function editValorReferencia(valorReferenciaId) {
    fetch(`/examen-valores-referencia/${valorReferenciaId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const valor = data.valorReferencia;

            // Cambiar a modo edición
            document.getElementById('valorReferenciaMethod').value = 'PUT';
            document.getElementById('valorReferenciaForm').action = `/examen-valores-referencia/${valorReferenciaId}`;
            document.getElementById('modalTitleVR').textContent = 'Editar Valor de Referencia';
            document.getElementById('submitButtonTextVR').textContent = 'Actualizar Valor de Referencia';
            document.getElementById('valorReferenciaId').value = valor.id;

            // Llenar campos básicos
            document.getElementById('parametro_id').value = valor.parametro_id || '';
            document.getElementById('tipo_referencia').value = valor.tipo_referencia;
            document.getElementById('orden_vr').value = valor.orden;
            document.getElementById('genero').value = valor.genero || '';
            document.getElementById('edad_min').value = valor.edad_min || '';
            document.getElementById('edad_max').value = valor.edad_max || '';
            document.getElementById('condicion_especial').value = valor.condicion_especial || '';
            document.getElementById('descripcion').value = valor.descripcion || '';
            document.getElementById('status_vr').checked = valor.status;

            // Llenar campos según tipo
            if (valor.tipo_referencia === 'RANGO') {
                document.getElementById('valor_min').value = valor.valor_min || '';
                document.getElementById('valor_max').value = valor.valor_max || '';
            } else if (valor.tipo_referencia === 'CUALITATIVO') {
                document.getElementById('valor_cualitativo').value = valor.valor_cualitativo || '';
            } else if (valor.tipo_referencia === 'CATEGORIZADO') {
                document.getElementById('categoria').value = valor.categoria || '';
                document.getElementById('valor_min_cat').value = valor.valor_min || '';
                document.getElementById('valor_max_cat').value = valor.valor_max || '';
                document.getElementById('operador').value = valor.operador || '';
            }

            // Actualizar visibilidad de campos
            toggleTipoReferenciaFields();

            // Mostrar modal
            new bootstrap.Modal(document.getElementById('valorReferenciaModal')).show();
        } else {
            alert('Error al cargar el valor de referencia: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar el valor de referencia');
    });
}

// Auto-abrir modal de valores de referencia si hay errores de validación
@if ($errors->any() && old('_form_type') === 'valor_referencia')
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Abriendo modal de valores de referencia por errores de validación');

        // Es un valor de referencia, no un parámetro
        document.getElementById('valorReferenciaMethod').value = '{{ old("_method", "POST") }}';

        @if (old('_method') === 'PUT' && old('valor_referencia_id'))
            document.getElementById('valorReferenciaForm').action = `/examen-valores-referencia/{{ old('valor_referencia_id') }}`;
            document.getElementById('modalTitleVR').textContent = 'Editar Valor de Referencia';
            document.getElementById('submitButtonTextVR').textContent = 'Actualizar Valor de Referencia';
            document.getElementById('valorReferenciaId').value = '{{ old("valor_referencia_id") }}';
        @endif

        // Restaurar campos básicos
        document.getElementById('parametro_id').value = '{{ old("parametro_id") }}';
        document.getElementById('tipo_referencia').value = '{{ old("tipo_referencia") }}';
        document.getElementById('orden_vr').value = '{{ old("orden") }}';
        document.getElementById('genero').value = '{{ old("genero") }}';
        document.getElementById('edad_min').value = '{{ old("edad_min") }}';
        document.getElementById('edad_max').value = '{{ old("edad_max") }}';
        document.getElementById('condicion_especial').value = '{{ old("condicion_especial") }}';
        document.getElementById('descripcion').value = `{!! str_replace(["\r\n", "\n", "\r"], "\\n", old("descripcion", "")) !!}`;
        document.getElementById('status_vr').checked = {{ old("status", true) ? 'true' : 'false' }};

        // Actualizar visibilidad y habilitar campos según tipo ANTES de restaurar valores
        toggleTipoReferenciaFields();

        // Restaurar valores específicos DESPUÉS de que los campos estén habilitados
        @if(old('tipo_referencia') === 'RANGO')
            document.getElementById('valor_min').value = '{{ old("valor_min") }}';
            document.getElementById('valor_max').value = '{{ old("valor_max") }}';
        @elseif(old('tipo_referencia') === 'CUALITATIVO')
            document.getElementById('valor_cualitativo').value = '{{ old("valor_cualitativo") }}';
        @elseif(old('tipo_referencia') === 'CATEGORIZADO')
            document.getElementById('categoria').value = '{{ old("categoria") }}';
            document.getElementById('valor_min_cat').value = '{{ old("valor_min") }}';
            document.getElementById('valor_max_cat').value = '{{ old("valor_max") }}';
            document.getElementById('operador').value = '{{ old("operador") }}';
        @endif

        // Mostrar modal
        new bootstrap.Modal(document.getElementById('valorReferenciaModal')).show();
    });
@endif
</script>
@endpush
