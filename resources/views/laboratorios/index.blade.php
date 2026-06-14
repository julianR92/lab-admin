@extends('layouts.app')

@section('title', 'Laboratorios - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-building me-2"></i>Laboratorios de Remisión</h2>
            <p class="text-muted">Laboratorios externos a los que se remiten exámenes</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('laboratorios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Laboratorio
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
                <table id="labsTable" class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th width="140">NIT</th>
                            <th width="120">Ciudad</th>
                            <th>Correo</th>
                            <th class="text-center" width="90">Exámenes</th>
                            <th class="text-center" width="80">Estado</th>
                            <th class="text-center" width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de eliminar este laboratorio? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" class="d-inline">
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#labsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('laboratorios.index') }}',
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataSrc: 'data'
        },
        columns: [
            { data: 'nombre' },
            { data: 'nit' },
            { data: 'ciudad' },
            { data: 'email' },
            {
                data: 'examenes_count',
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function(data) {
                    return data
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-secondary">Inactivo</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <a href="/laboratorios/${row.id}" class="btn btn-sm btn-primary" title="Ver exámenes">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/laboratorios/${row.id}/edit" class="btn btn-sm btn-warning" title="Editar datos">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${row.id}, ${row.servicios_examen_count})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 25
    });
});

function confirmDelete(id, serviciosCount) {
    if (serviciosCount > 0) {
        alert('No se puede eliminar: el laboratorio tiene ' + serviciosCount + ' examen(es) de servicio asociados.');
        return;
    }
    document.getElementById('deleteForm').action = '/laboratorios/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
