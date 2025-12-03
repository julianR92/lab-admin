@extends('layouts.app')

@section('title', 'Crear Examen - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-flask me-2"></i>Crear Nuevo Examen</h2>
            <p class="text-muted">Registra un nuevo examen diagnóstico en el catálogo</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('examenes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Error de Validación</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('examenes.store') }}" method="POST" id="examenForm">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <!-- Información Básica -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('codigo') is-invalid @enderror"
                                       id="codigo"
                                       name="codigo"
                                       value="{{ old('codigo') }}"
                                       placeholder="QC001">
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre del Examen <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre"
                                       name="nombre"
                                       value="{{ old('nombre') }}"
                                       placeholder="GLICEMIA BASAL">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select class="form-select @error('categoria_id') is-invalid @enderror"
                                        id="categoria_id"
                                        name="categoria_id">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->categoria }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_resultado" class="form-label">Tipo de Resultado <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipo_resultado') is-invalid @enderror"
                                        id="tipo_resultado"
                                        name="tipo_resultado">
                                    <option value="">Seleccione un tipo</option>
                                    @foreach($tiposResultado as $valor => $label)
                                        <option value="{{ $valor }}" {{ old('tipo_resultado') == $valor ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_resultado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Define cómo se capturan y validan los resultados</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                <input type="text"
                                       class="form-control @error('unidad_medida') is-invalid @enderror"
                                       id="unidad_medida"
                                       name="unidad_medida"
                                       value="{{ old('unidad_medida') }}"
                                       placeholder="mg/dL, g/dL, etc.">
                                @error('unidad_medida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="muestra_requerida" class="form-label">Muestra Requerida</label>
                                <input type="text"
                                       class="form-control @error('muestra_requerida') is-invalid @enderror"
                                       id="muestra_requerida"
                                       name="muestra_requerida"
                                       value="{{ old('muestra_requerida') }}"
                                       placeholder="Sangre venosa, orina, etc.">
                                @error('muestra_requerida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tecnica" class="form-label">Técnica</label>
                            <input type="text"
                                   class="form-control @error('tecnica') is-invalid @enderror"
                                   id="tecnica"
                                   name="tecnica"
                                   value="{{ old('tecnica') }}"
                                   placeholder="Colorimetría enzimática, etc.">
                            @error('tecnica')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="instrucciones_paciente" class="form-label">Instrucciones al Paciente</label>
                            <textarea class="form-control @error('instrucciones_paciente') is-invalid @enderror"
                                      id="instrucciones_paciente"
                                      style="resize: none;"
                                      name="instrucciones_paciente"
                                      rows="3"
                                      placeholder="Indicaciones de preparación previa al examen...">{{ old('instrucciones_paciente') }}</textarea>
                            @error('instrucciones_paciente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Información Comercial -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Información Comercial</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="valor_total" class="form-label">Valor Total <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text"
                                       class="form-control @error('valor_total') is-invalid @enderror"
                                       id="valor_total_display"
                                       placeholder="0">
                                <input type="hidden" id="valor_total" name="valor_total" value="{{ old('valor_total') }}">
                                @error('valor_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="valor_remision" class="form-label">Valor Remisión</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text"
                                       class="form-control @error('valor_remision') is-invalid @enderror"
                                       id="valor_remision_display"
                                       placeholder="0">
                                <input type="hidden" id="valor_remision" name="valor_remision" value="{{ old('valor_remision') }}">
                                @error('valor_remision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Valor si se remite a otro laboratorio</small>
                        </div>

                        <div class="mb-3">
                            <label for="tiempo_entrega" class="form-label">Tiempo de Entrega (horas) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('tiempo_entrega') is-invalid @enderror"
                                   id="tiempo_entrega"
                                   name="tiempo_entrega"
                                   value="{{ old('tiempo_entrega', 4) }}"
                                   min="1"
                                   max="720"
                                   placeholder="4">
                            @error('tiempo_entrega')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="requiere_ayuno"
                                       name="requiere_ayuno"
                                       {{ old('requiere_ayuno') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requiere_ayuno">
                                    Requiere Ayuno
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="status"
                                       name="status"
                                       {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Información</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            Los parámetros y valores de referencia se configuran después de crear el examen.
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            El tipo de resultado determina cómo se capturan los datos del examen.
                        </p>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Guardar Examen
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Formatear número como pesos colombianos
function formatCurrency(value) {
    if (!value) return '';
    return parseInt(value).toLocaleString('es-CO');
}

// Remover formato y obtener número puro
function unformatCurrency(value) {
    if (!value) return '';
    return value.replace(/[^0-9]/g, '');
}

// Inicializar campos al cargar
window.addEventListener('DOMContentLoaded', function() {
    // Valor Total
    const valorTotalDisplay = document.getElementById('valor_total_display');
    const valorTotalHidden = document.getElementById('valor_total');

    if (valorTotalHidden.value) {
        valorTotalDisplay.value = formatCurrency(valorTotalHidden.value);
    }

    valorTotalDisplay.addEventListener('input', function(e) {
        const cleaned = unformatCurrency(e.target.value);
        valorTotalHidden.value = cleaned;
        e.target.value = formatCurrency(cleaned);
    });

    // Valor Remisión
    const valorRemisionDisplay = document.getElementById('valor_remision_display');
    const valorRemisionHidden = document.getElementById('valor_remision');

    if (valorRemisionHidden.value) {
        valorRemisionDisplay.value = formatCurrency(valorRemisionHidden.value);
    }

    valorRemisionDisplay.addEventListener('input', function(e) {
        const cleaned = unformatCurrency(e.target.value);
        valorRemisionHidden.value = cleaned;
        e.target.value = formatCurrency(cleaned);
    });

    // Validar antes de enviar el formulario
    const form = document.getElementById('examenForm');
    form.addEventListener('submit', function(e) {
        console.log('Valor Total Hidden:', valorTotalHidden.value);
        console.log('Valor Remision Hidden:', valorRemisionHidden.value);

        // Si valor_total está vacío, poner 0
        if (!valorTotalHidden.value || valorTotalHidden.value === '') {
            valorTotalHidden.value = '0';
        }

        // Si valor_remision está vacío, dejarlo vacío (es nullable)
        if (!valorRemisionHidden.value || valorRemisionHidden.value === '') {
            valorRemisionHidden.value = '';
        }
    });
});
</script>
@endpush
