@extends('layouts.app')

@section('title', 'Capturar Resultados - ' . $servicioExamen->examen->nombre)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-microscope me-2"></i>Capturar Resultados
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('servicios.show', $servicioExamen->servicio_id) }}">{{ $servicioExamen->servicio->numero_orden }}</a></li>
                    <li class="breadcrumb-item active">Capturar Resultados</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('servicios.show', $servicioExamen->servicio_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Información del Paciente y Examen -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Paciente</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Nombre:</th>
                            <td>{{ $servicioExamen->servicio->cliente->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <th>Documento:</th>
                            <td>{{ $servicioExamen->servicio->cliente->tipo_documento }}: {{ $servicioExamen->servicio->cliente->documento }}</td>
                        </tr>
                        <tr>
                            <th>Edad/Género:</th>
                            <td>{{ $contexto['edad'] }} años - {{ $contexto['genero'] }}</td>
                        </tr>
                        <tr>
                            <th>EPS:</th>
                            <td>{{ $servicioExamen->servicio->cliente->eps ?? 'No registrado' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-vial me-2"></i>Información del Examen</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Examen:</th>
                            <td><strong>{{ $servicioExamen->examen->nombre }}</strong></td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td>{{ $servicioExamen->examen->codigo }}</td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td><span class="badge bg-info">{{ str_replace('_', ' ', $servicioExamen->examen->tipo_resultado) }}</span></td>
                        </tr>
                        <tr>
                            <th>Profesional:</th>
                            <td>{{ $servicioExamen->profesional->nombre }} {{ $servicioExamen->profesional->apellido }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Captura -->
    <form id="formResultados" method="POST" action="{{ route('resultados.store', $servicioExamen) }}">
        @csrf

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-flask me-2"></i>Parámetros del Examen</h5>
            </div>
            <div class="card-body">
                @if ($servicioExamen->examen->tipo_resultado === 'TEXTO_DESCRIPTIVO')
                    <!-- Captura para exámenes descriptivos -->
                    @include('resultados.tipos.texto-descriptivo')
                @elseif ($servicioExamen->examen->tipo_resultado === 'TABLA_HEMATOLOGICA')
                    <!-- Captura para tabla hematológica -->
                    @include('resultados.tipos.tabla-hematologica')
                @elseif ($servicioExamen->examen->tipo_resultado === 'CUALITATIVO_MULTIPLE_OPCIONES')
                    <!-- Captura para cualitativos múltiples opciones -->
                    @include('resultados.tipos.cualitativo-multiple')
                @elseif (in_array($servicioExamen->examen->tipo_resultado, ['MULTIPLE_SIMPLE', 'MULTIPLE_CALCULADO']))
                    <!-- Captura para múltiples parámetros -->
                    @include('resultados.tipos.multiple-parametros')
                @else
                    <!-- Captura estándar -->
                    @include('resultados.tipos.estandar')
                @endif

                <!-- Opinión Profesional (para todos los tipos de examen) -->
                <div class="card bg-light mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-user-md me-2"></i>Opinión Profesional
                            <small class="text-muted">(Opcional)</small>
                        </h6>
                    </div>
                    <div class="card-body">
                        <label for="observaciones_profesional" class="form-label">
                            <strong>Observaciones del Bacteriólogo</strong>
                            <small class="text-muted">(Máximo 500 caracteres)</small>
                        </label>
                        <textarea
                            name="observaciones_profesional"
                            id="observaciones_profesional"
                            class="form-control"
                            rows="4"
                            maxlength="500"
                            placeholder="Escriba aquí cualquier observación, interpretación o recomendación adicional sobre los resultados...">{{ $servicioExamen->observaciones ?? '' }}</textarea>
                        <div class="form-text">
                            <span id="contador-caracteres">0</span> / 500 caracteres
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('servicios.show', $servicioExamen->servicio_id) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar Resultados
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Contador de caracteres para opinión profesional
document.getElementById('observaciones_profesional').addEventListener('input', function() {
    document.getElementById('contador-caracteres').textContent = this.value.length;
});

// Actualizar contador al cargar
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('observaciones_profesional');
    document.getElementById('contador-caracteres').textContent = textarea.value.length;
});

// Función para mostrar toast
function showToast(message, type = 'success', warnings = []) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Crear toast
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : 'bg-danger';
    const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle';

    let warningsHtml = '';
    if (warnings.length > 0) {
        warningsHtml = '<ul class="mb-0 mt-2 small">';
        warnings.forEach(warning => {
            warningsHtml += `<li>${warning.mensaje}</li>`;
        });
        warningsHtml += '</ul>';
    }

    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="d-flex">
                <div class="toast-body">
                    <strong><i class="fas fa-${icon} me-2"></i>${message}</strong>
                    ${warningsHtml}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);

    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Auto-cerrar después de 5 segundos si es éxito
    if (type === 'success') {
        setTimeout(() => {
            toast.hide();
            setTimeout(() => toastElement.remove(), 300);
        }, 5000);
    }
}

document.getElementById('formResultados').addEventListener('submit', function(e) {
    e.preventDefault();

    // Limpiar formato de números antes de enviar
    const inputsNumericos = this.querySelectorAll('.valor-numerico');
    inputsNumericos.forEach(input => {
        if (input.value) {
            // Remover comas y convertir a número puro
            const valorLimpio = input.value.replace(/,/g, '');
            input.setAttribute('data-valor-original', input.value);
            input.value = valorLimpio;
        }
    });

    const formData = new FormData(this);

    // DEBUG: Mostrar lo que se va a enviar
    console.log('=== DATOS DEL FORMULARIO ===');
    for (let [key, value] of formData.entries()) {
        console.log(key, '=', value);
    }
    console.log('===========================');

    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar warnings si existen
            if (data.warnings && data.warnings.length > 0) {
                showToast('Resultados guardados con alertas', 'warning', data.warnings);
            } else {
                showToast(data.message, 'success');
            }

            // Redirigir después de 2 segundos
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            throw new Error(data.message || 'Error al guardar resultados');
        }
    })
    .catch(error => {
        // Restaurar formato de números si falla
        inputsNumericos.forEach(input => {
            if (input.hasAttribute('data-valor-original')) {
                input.value = input.getAttribute('data-valor-original');
                input.removeAttribute('data-valor-original');
            }
        });

        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Resultados';
        showToast(error.message, 'danger');
    });
});
</script>
@endpush

