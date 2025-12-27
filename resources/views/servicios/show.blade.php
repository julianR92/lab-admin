@extends('layouts.app')

@section('title', 'Detalle del Servicio - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-file-medical me-2"></i>Orden {{ $servicio->numero_orden }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                    <li class="breadcrumb-item active">{{ $servicio->numero_orden }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('servicios.descargar-orden', $servicio) }}" class="btn btn-success me-2" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Descargar Orden
            </a>
            <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash me-2"></i>Eliminar
            </button>
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

    <div class="row">
        <!-- Información del Servicio -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Número Orden:</th>
                            <td><strong>{{ $servicio->numero_orden }}</strong></td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $servicio->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Cliente:</th>
                            <td>{{ $servicio->cliente->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <th>Documento:</th>
                            <td>{{ $servicio->cliente->tipo_documento }}: {{ $servicio->cliente->documento }}</td>
                        </tr>
                        <tr>
                            <th>Edad/Género:</th>
                            <td>{{ $servicio->cliente->edad }} años - {{ $servicio->cliente->genero }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $servicio->cliente->telefono ?? 'No registrado' }}</td>
                        </tr>
                        <tr>
                            <th>EPS:</th>
                            <td>{{ $servicio->cliente->eps ?? 'No registrado' }}</td>
                        </tr>
                        @if ($servicio->observaciones)
                        <tr>
                            <th>Observaciones:</th>
                            <td>{{ $servicio->observaciones }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Información de Pago</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Valor Total:</th>
                            <td><h4 class="mb-0 text-success">${{ number_format($servicio->valor_total, 0, ',', '.') }}</h4></td>
                        </tr>
                        <tr>
                            <th>Valor Pagado:</th>
                            <td><strong>${{ number_format($servicio->valor_pagado, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Saldo Pendiente:</th>
                            <td>
                                <strong class="{{ $servicio->saldo_pendiente > 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($servicio->saldo_pendiente, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Medio de Pago:</th>
                            <td>{{ $servicio->medio_pago ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <th>Estado Pago:</th>
                            <td>
                                @if ($servicio->estado_pago == 'PENDIENTE')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif ($servicio->estado_pago == 'PARCIAL')
                                    <span class="badge bg-info">Parcial</span>
                                @else
                                    <span class="badge bg-success">Pagado</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if ($servicio->estado_pago != 'PAGADO')
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#pagoModal">
                            <i class="fas fa-plus me-2"></i>Registrar Pago
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Exámenes del Servicio -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-vials me-2"></i>Exámenes Solicitados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Examen</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Profesional</th>
                            <th>F. Toma Muestra</th>
                            <th>F. Resultado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicio->serviciosExamen as $servicioExamen)
                            <tr>
                                <td><strong>{{ $servicioExamen->examen->codigo }}</strong></td>
                                <td>{{ $servicioExamen->examen->nombre }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $servicioExamen->examen->categoria->categoria }}</span>
                                </td>
                                <td>
                                    @switch($servicioExamen->estado)
                                        @case('PENDIENTE')
                                            <span class="badge bg-secondary">Pendiente</span>
                                            @break
                                        @case('EN_PROCESO')
                                            <span class="badge bg-primary">En Proceso</span>
                                            @break
                                        @case('COMPLETADO')
                                            <span class="badge bg-info">Completado</span>
                                            @break
                                        @case('VALIDADO')
                                            <span class="badge bg-success">Validado</span>
                                            @break
                                        @case('ENTREGADO')
                                            <span class="badge bg-dark">Entregado</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if ($servicioExamen->profesional)
                                        {{ $servicioExamen->profesional->nombre }} {{ $servicioExamen->profesional->apellido }}
                                    @else
                                        <form method="POST" action="{{ route('servicios.asignar-profesional', $servicioExamen) }}" class="d-inline">
                                            @csrf
                                            <select name="profesional_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="">Asignar...</option>
                                                @foreach ($profesionales as $profesional)
                                                    <option value="{{ $profesional->id }}">
                                                        {{ $profesional->nombre }} {{ $profesional->apellido }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td>{{ $servicioExamen->fecha_toma_muestra ? $servicioExamen->fecha_toma_muestra->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $servicioExamen->fecha_resultado ? $servicioExamen->fecha_resultado->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if (in_array($servicioExamen->estado, ['PENDIENTE', 'EN_PROCESO']))
                                            <button type="button" class="btn btn-primary" title="Capturar Resultados">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        @if ($servicioExamen->estado == 'COMPLETADO')
                                            <form method="POST" action="{{ route('servicios.cambiar-estado', $servicioExamen) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="estado" value="VALIDADO">
                                                <button type="submit" class="btn btn-success" title="Validar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if ($servicioExamen->estado == 'VALIDADO')
                                            <button type="button" class="btn btn-danger" title="Imprimir PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            <form method="POST" action="{{ route('servicios.cambiar-estado', $servicioExamen) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="estado" value="ENTREGADO">
                                                <button type="submit" class="btn btn-dark" title="Marcar Entregado">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if (in_array($servicioExamen->estado, ['COMPLETADO', 'VALIDADO', 'ENTREGADO']))
                                            <button type="button" class="btn btn-info" title="Ver Resultados">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Pago -->
<div class="modal fade" id="pagoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('servicios.registrar-pago', $servicio) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Saldo Pendiente:</strong> ${{ number_format($servicio->saldo_pendiente, 0, ',', '.') }}
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto a Pagar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="monto" name="monto"
                               min="0.01" step="0.01" max="{{ $servicio->saldo_pendiente }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="medio_pago_modal" class="form-label">Medio de Pago <span class="text-danger">*</span></label>
                        <select class="form-select" id="medio_pago_modal" name="medio_pago" required>
                            <option value="">-- Seleccione --</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta débito">Tarjeta débito</option>
                            <option value="Tarjeta crédito">Tarjeta crédito</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Nequi">Nequi</option>
                            <option value="Daviplata">Daviplata</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminación -->
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
                <form method="POST" action="{{ route('servicios.destroy', $servicio) }}" style="display: inline;">
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
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
