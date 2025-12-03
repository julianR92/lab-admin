@extends('layouts.app')

@section('title', 'Exámenes - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-flask me-2"></i>Catálogo de Exámenes</h2>
            <p class="text-muted">Gestión de exámenes y pruebas diagnósticas</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('examenes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Examen
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="examenesTable" class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Código</th>
                            <th>Nombre</th>
                            <th width="150">Categoría</th>
                            <th width="180">Tipo Resultado</th>
                            <th class="text-end" width="100">Valor</th>
                            <th class="text-center" width="80">Entrega</th>
                            <th class="text-center" width="80">Ayuno</th>
                            <th class="text-center" width="80">Params</th>
                            <th class="text-center" width="100">Estado</th>
                            <th class="text-center" width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este examen? Esta acción no se puede deshacer.
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
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#examenesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('examenes.index') }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataSrc: 'data'
        },
        columns: [
            { data: 'codigo' },
            { data: 'nombre' },
            { data: 'categoria' },
            {
                data: 'tipo_resultado_label',
                render: function(data, type, row) {
                    const badges = {
                        'NUMERICO_SIMPLE': 'primary',
                        'NUMERICO_CATEGORIZADO': 'info',
                        'CUALITATIVO_SIMPLE': 'success',
                        'CUALITATIVO_REACTIVO': 'warning',
                        'CUALITATIVO_MULTIPLE_OPCIONES': 'secondary',
                        'MULTIPLE_CALCULADO': 'dark',
                        'TABLA_HEMATOLOGIA': 'danger',
                        'TEXTO_DESCRIPTIVO': 'light text-dark'
                    };
                    const badge = badges[row.tipo_resultado] || 'secondary';
                    return `<span class="badge bg-${badge} text-wrap">${data}</span>`;
                }
            },
            {
                data: 'valor_total',
                className: 'text-end',
                render: function(data, type, row) {
                    return `$${data}`;
                }
            },
            {
                data: 'tiempo_entrega',
                className: 'text-center',
                render: function(data) {
                    return `${data}h`;
                }
            },
            {
                data: 'requiere_ayuno',
                className: 'text-center',
                render: function(data) {
                    return data
                        ? '<span class="badge bg-warning">SÍ</span>'
                        : '<span class="badge bg-secondary">NO</span>';
                }
            },
            {
                data: 'parametros_count',
                className: 'text-center',
                render: function(data) {
                    return data > 0
                        ? `<span class="badge bg-info">${data}</span>`
                        : '<span class="text-muted">0</span>';
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    return data
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <a href="/examenes/${row.id}" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/examenes/${row.id}/edit" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${row.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'asc']],
        pageLength: 25
    });
});

function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/examenes/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
