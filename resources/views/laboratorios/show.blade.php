@extends('layouts.app')

@section('title', 'Laboratorio — Exámenes - Sistema de Laboratorio')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">

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

            {{-- Encabezado del laboratorio --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>{{ $laboratorio->nombre }}</h4>
                        <small class="text-muted">
                            @if($laboratorio->nit) NIT: {{ $laboratorio->nit }} &nbsp;·&nbsp; @endif
                            @if($laboratorio->ciudad) {{ $laboratorio->ciudad }} @endif
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('laboratorios.edit', $laboratorio) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar datos
                        </a>
                        <a href="{{ route('laboratorios.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>

            {{-- Exámenes configurados --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-vials me-2"></i>Exámenes Configurados
                    </h5>
                    <span class="badge bg-info fs-6">{{ $examenesConfigurados->count() }} examen(es)</span>
                </div>
                <div class="card-body p-0">
                    @if($examenesConfigurados->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="fas fa-info-circle me-1"></i>No hay exámenes configurados para este laboratorio.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Examen</th>
                                        <th width="130">Categoría</th>
                                        <th class="text-end" width="170">Valor Remisión</th>
                                        <th class="text-center" width="80">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examenesConfigurados as $le)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $le->examen->nombre }}</span>
                                                <small class="text-muted d-block">{{ $le->examen->codigo }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $le->examen->categoria?->categoria ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                ${{ number_format($le->valor_remision, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <form method="POST"
                                                    action="{{ route('laboratorios.examenes.destroy', [$laboratorio, $le->examen]) }}"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Quitar este examen del laboratorio?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Quitar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Formulario para agregar examen --}}
                    @if($examenesDisponibles->isNotEmpty())
                        <div class="border-top p-3">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-plus-circle me-1"></i>Agregar Examen
                            </h6>
                            <form action="{{ route('laboratorios.examenes.store', $laboratorio) }}" method="POST" id="formAgregarExamen">
                                @csrf
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label small">Examen</label>
                                        <select name="examen_id"
                                            class="form-select form-select-sm @error('examen_id') is-invalid @enderror"
                                            required>
                                            <option value="">— Seleccionar examen —</option>
                                            @foreach($examenesDisponibles as $examen)
                                                <option value="{{ $examen->id }}" @selected(old('examen_id') == $examen->id)>
                                                    {{ $examen->nombre }} ({{ $examen->codigo }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('examen_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Valor Remisión ($)</label>
                                        <input type="text"
                                            id="inputValorDisplay"
                                            class="form-control form-control-sm @error('valor_remision') is-invalid @enderror"
                                            placeholder="0"
                                            value="{{ old('valor_remision') ? number_format((float) old('valor_remision'), 0, ',', '.') : '' }}"
                                            autocomplete="off"
                                            inputmode="numeric"
                                            required>
                                        <input type="hidden" name="valor_remision" id="inputValorRaw"
                                            value="{{ old('valor_remision') }}">
                                        @error('valor_remision')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <i class="fas fa-plus me-1"></i>Agregar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0 border-top">
                            <small>Todos los exámenes activos ya están configurados para este laboratorio.</small>
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var display = document.getElementById('inputValorDisplay');
    var raw = document.getElementById('inputValorRaw');

    if (!display) return;

    function formatCOP(value) {
        var digits = value.replace(/\D/g, '');
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    display.addEventListener('input', function () {
        var cursor = this.selectionStart;
        var prevLen = this.value.length;
        var formatted = formatCOP(this.value);
        this.value = formatted;
        var diff = formatted.length - prevLen;
        this.setSelectionRange(cursor + diff, cursor + diff);
        raw.value = formatted.replace(/\./g, '');
    });

    document.getElementById('formAgregarExamen').addEventListener('submit', function () {
        raw.value = display.value.replace(/\./g, '');
    });
})();
</script>
@endpush
