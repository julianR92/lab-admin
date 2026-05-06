@extends('layouts.app')

@section('title', 'IPS - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-hospital me-2"></i>IPS</h2>
            <p class="text-muted">Gestión de Instituciones Prestadoras de Salud</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('ips.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nueva IPS
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
                <table id="ipsTable" class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Logo</th>
                            <th>Razón Social</th>
                            <th width="150">NIT</th>
                            <th>Correo Electrónico</th>
                            <th class="text-center" width="100">Pacientes</th>
                            <th class="text-center" width="120">Acciones</th>
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
                ¿Está seguro de eliminar esta IPS? Esta acción no se puede deshacer.
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
    $('#ipsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('ips.index') }}',
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'logo',
                orderable: false,
                render: function(data) {
                    if (data) {
                        return '<img src="/storage/' + data + '" alt="Logo" style="height:36px;width:auto;object-fit:contain;border-radius:4px;">';
                    }
                    return '<span class="text-muted"><i class="fas fa-image"></i></span>';
                }
            },
            { data: 'razon_social' },
            { data: 'nit' },
            { data: 'correo_electronico' },
            {
                data: 'clientes_count',
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <a href="/ips/${row.id}/edit" class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${row.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 25
    });
});

function confirmDelete(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/ips/${id}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
