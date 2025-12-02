@extends('layouts.app')

@section('title', 'Clientes - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-users me-2"></i>Gestión de Clientes
            </h1>
            <p class="text-muted">Administre los pacientes del laboratorio</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Cliente
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="clientesTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Género</th>
                            <th>Edad</th>
                            <th>Teléfono</th>
                            <th>Ciudad</th>
                            <th>EPS</th>
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
                <p>¿Está seguro de que desea eliminar este cliente?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
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

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
        font-size: 14px;
    }

    .badge {
        font-size: 12px;
        padding: 6px 12px;
    }

    .btn-action {
        padding: 6px 12px;
        font-size: 13px;
        margin: 0 2px;
    }

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
<script>
$(document).ready(function() {
    var table = $('#clientesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('clientes.index') }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        },
        columns: [
            { data: 'id' },
            { data: 'nombre_completo' },
            { data: 'documento' },
            {
                data: 'genero',
                render: function(data) {
                    if (data === 'M') return '<span class="badge bg-primary">Masculino</span>';
                    if (data === 'F') return '<span class="badge bg-danger">Femenino</span>';
                    return '<span class="badge bg-secondary">Otro</span>';
                }
            },
            { data: 'edad' },
            { data: 'telefono' },
            { data: 'ciudad' },
            { data: 'eps' },
            {
                data: 'acciones',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <a href="/clientes/${data}" class="btn btn-sm btn-info btn-action" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/clientes/${data}/edit" class="btn btn-sm btn-warning btn-action" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-action" onclick="confirmDelete(${data})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25
    });
});

function confirmDelete(id) {
    var deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '/clientes/' + id;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
@endsection
