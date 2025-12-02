@extends('layouts.app')

@section('title', 'Profesionales - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-user-md me-2"></i>Profesionales</h2>
            <p class="text-muted">Gestión de personal médico y técnico</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('profesionales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Profesional
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
                <table id="profesionalesTable" class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th width="120">Documento</th>
                            <th>Profesión</th>
                            <th width="120">Registro</th>
                            <th width="150">Contacto</th>
                            <th class="text-center" width="90">Servicios</th>
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
                ¿Está seguro de eliminar este profesional?
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

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#profesionalesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('profesionales.index') }}',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataSrc: 'data'
        },
        columns: [
            { data: 'nombre_completo' },
            { data: 'documento' },
            { data: 'profesion' },
            { data: 'registro_profesional' },
            {
                data: null,
                render: function(data) {
                    let html = '';
                    if (data.telefono) html += '<i class="fas fa-phone text-success me-1"></i>' + data.telefono + '<br>';
                    if (data.email) html += '<i class="fas fa-envelope text-primary me-1"></i>' + data.email;
                    return html || '<span class="text-muted">Sin contacto</span>';
                }
            },
            {
                data: 'servicios_count',
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
                render: function(data, type, row) {
                    if (row.deleted_at) {
                        return '<span class="text-muted">Sin acciones</span>';
                    }

                    return `
                        <a href="/profesionales/${row.id}" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/profesionales/${row.id}/edit" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${row.id})" title="Eliminar">
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

function confirmDelete(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/profesionales/${id}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
