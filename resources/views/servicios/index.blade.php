@extends('layouts.app')

@section('title', 'Servicios - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-file-medical me-2"></i>Órdenes de Servicio
            </h1>
            <p class="text-muted">Gestión de órdenes y exámenes solicitados</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nueva Orden
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.index') }}" class="row g-3">
                {{-- Fila 1: fechas + documento + nombre --}}
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <label for="documento" class="form-label">N° Documento Paciente</label>
                    <input type="text" class="form-control" id="documento" name="documento"
                           placeholder="Buscar por CC, TI..." value="{{ request('documento') }}">
                </div>
                <div class="col-md-2">
                    <label for="buscar" class="form-label">Nombre / Apellido</label>
                    <input type="text" class="form-control" id="buscar" name="buscar"
                           placeholder="Buscar por nombre..." value="{{ request('buscar') }}">
                </div>
                 <div class="col-md-4">
                    <label for="estado_examen" class="form-label">Estado del Examen</label>
                    <select name="estado_examen" id="estado_examen" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach ($estadosExamen as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ request('estado_examen') === $valor ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fila 2: exámenes (select2 múltiple) + estado examen --}}
                <div class="col-md-8">
                    <label for="examenes_filtro" class="form-label">Exámenes</label>
                    <select name="examenes[]" id="examenes_filtro" class="form-select" multiple>
                        @foreach ($examenesDisponibles as $examen)
                            <option value="{{ $examen->id }}"
                                {{ in_array($examen->id, request('examenes', [])) ? 'selected' : '' }}>
                                {{ $examen->codigo }} — {{ $examen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Fila 3: botones --}}
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="serviciosTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número Orden</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Total Exámenes</th>
                            <th>Valor Total</th>
                            <th>Estado Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este servicio?</p>
                <p class="text-danger fw-semibold">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción es irreversible. Se eliminarán permanentemente todos los resultados,
                    imágenes adjuntas y PDFs asociados a esta orden.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }
    .table td { vertical-align: middle; font-size: 14px; }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 8px 16px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 6px 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('#examenes_filtro').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione uno o más exámenes...',
        allowClear: true,
        language: {
            noResults: function () { return 'No se encontraron exámenes'; },
            searching: function () { return 'Buscando...'; },
        },
    });

    $('#serviciosTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('servicios.index') }}' + window.location.search,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        },
        columns: [
            {
                data: 'numero_orden',
                render: function (data, type, row) {
                    return '<a href="/servicios/' + row.acciones + '" class="text-decoration-none fw-bold">' + data + '</a>';
                }
            },
            { data: 'fecha' },
            { data: 'cliente_nombre' },
            { data: 'documento' },
            {
                data: 'total_examenes',
                render: function (data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            { data: 'valor_total' },
            { data: 'estado_pago', orderable: false },
            {
                data: 'acciones',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <a href="/servicios/${data}" class="btn btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="/servicios/${data}/orden-pdf" class="btn btn-success" title="Descargar Orden" target="_blank"><i class="fas fa-file-pdf"></i></a>
                            <a href="/servicios/${data}/edit" class="btn btn-primary" title="Editar"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger" onclick="confirmDelete(${data})" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </div>`;
                }
            },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'desc']],
        pageLength: 25,
    });
});

function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/servicios/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
