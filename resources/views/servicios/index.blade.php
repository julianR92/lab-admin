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
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label for="estado_pago" class="form-label">Estado Pago</label>
                    <select class="form-select" id="estado_pago" name="estado_pago">
                        <option value="">Todos</option>
                        <option value="PENDIENTE" {{ request('estado_pago') == 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                        <option value="PARCIAL" {{ request('estado_pago') == 'PARCIAL' ? 'selected' : '' }}>Parcial</option>
                        <option value="PAGADO" {{ request('estado_pago') == 'PAGADO' ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" placeholder="Cliente u orden..." value="{{ request('buscar') }}">
                </div>
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
                <table class="table table-hover">
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
                    <tbody>
                        @forelse ($servicios as $servicio)
                            <tr>
                                <td>
                                    <a href="{{ route('servicios.show', $servicio) }}" class="text-decoration-none fw-bold">
                                        {{ $servicio->numero_orden }}
                                    </a>
                                </td>
                                <td>{{ $servicio->fecha->format('d/m/Y') }}</td>
                                <td>{{ $servicio->cliente->nombre_completo }}</td>
                                <td>{{ $servicio->cliente->documento }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $servicio->serviciosExamen->count() }}</span>
                                </td>
                                <td>${{ number_format($servicio->valor_total, 0, ',', '.') }}</td>
                                <td>
                                    @if ($servicio->estado_pago == 'PENDIENTE')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif ($servicio->estado_pago == 'PARCIAL')
                                        <span class="badge bg-info">Parcial</span>
                                    @else
                                        <span class="badge bg-success">Pagado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('servicios.descargar-orden', $servicio) }}" class="btn btn-success" title="Descargar Orden" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $servicio->id }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No hay servicios registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $servicios->links() }}
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
                <p class="text-danger"><small>Se eliminarán todos los exámenes asociados que estén pendientes.</small></p>
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

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/servicios/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
