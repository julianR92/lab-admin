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
           @if($servicio->serviciosExamen->whereIn('estado', ['VALIDADO', 'ENTREGADO'])->where('es_remitido', false)->count() > 0)
            <a href="{{ route('servicios.resultados-pdf', $servicio) }}" class="btn btn-info me-2" target="_blank" title="Descargar resultados validados">
                <i class="fas fa-file-medical-alt me-2"></i>Resultados PDF
            </a>
            @endif
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
                            @php
                            $telefono = $servicio->cliente->telefono;
                            $digitos = strlen(preg_replace('/[^0-9]/', '', $telefono));
                            @endphp
                            <th>Teléfono:</th>
                            @if ($digitos >8 )
                                <td><span>{{ $servicio->cliente->telefono }} <a href="https://wa.me/+57{{ $servicio->cliente->telefono }}" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="31.76" height="32" viewBox="0 0 256 258"><!-- Icon from SVG Logos by Gil Barbara - https://raw.githubusercontent.com/gilbarbara/logos/master/LICENSE.txt --><defs><linearGradient id="SVGBRLHCcSy" x1="50%" x2="50%" y1="100%" y2="0%"><stop offset="0%" stop-color="#1FAF38"/><stop offset="100%" stop-color="#60D669"/></linearGradient><linearGradient id="SVGHW6lecxh" x1="50%" x2="50%" y1="100%" y2="0%"><stop offset="0%" stop-color="#F9F9F9"/><stop offset="100%" stop-color="#FFF"/></linearGradient></defs><path fill="url(#SVGBRLHCcSy)" d="M5.463 127.456c-.006 21.677 5.658 42.843 16.428 61.499L4.433 252.697l65.232-17.104a123 123 0 0 0 58.8 14.97h.054c67.815 0 123.018-55.183 123.047-123.01c.013-32.867-12.775-63.773-36.009-87.025c-23.23-23.25-54.125-36.061-87.043-36.076c-67.823 0-123.022 55.18-123.05 123.004"/><path fill="url(#SVGHW6lecxh)" d="M1.07 127.416c-.007 22.457 5.86 44.38 17.014 63.704L0 257.147l67.571-17.717c18.618 10.151 39.58 15.503 60.91 15.511h.055c70.248 0 127.434-57.168 127.464-127.423c.012-34.048-13.236-66.065-37.3-90.15C194.633 13.286 162.633.014 128.536 0C58.276 0 1.099 57.16 1.071 127.416m40.24 60.376l-2.523-4.005c-10.606-16.864-16.204-36.352-16.196-56.363C22.614 69.029 70.138 21.52 128.576 21.52c28.3.012 54.896 11.044 74.9 31.06c20.003 20.018 31.01 46.628 31.003 74.93c-.026 58.395-47.551 105.91-105.943 105.91h-.042c-19.013-.01-37.66-5.116-53.922-14.765l-3.87-2.295l-40.098 10.513z"/><path fill="#FFF" d="M96.678 74.148c-2.386-5.303-4.897-5.41-7.166-5.503c-1.858-.08-3.982-.074-6.104-.074c-2.124 0-5.575.799-8.492 3.984c-2.92 3.188-11.148 10.892-11.148 26.561s11.413 30.813 13.004 32.94c1.593 2.123 22.033 35.307 54.405 48.073c26.904 10.609 32.379 8.499 38.218 7.967c5.84-.53 18.844-7.702 21.497-15.139c2.655-7.436 2.655-13.81 1.859-15.142c-.796-1.327-2.92-2.124-6.105-3.716s-18.844-9.298-21.763-10.361c-2.92-1.062-5.043-1.592-7.167 1.597c-2.124 3.184-8.223 10.356-10.082 12.48c-1.857 2.129-3.716 2.394-6.9.801c-3.187-1.598-13.444-4.957-25.613-15.806c-9.468-8.442-15.86-18.867-17.718-22.056c-1.858-3.184-.199-4.91 1.398-6.497c1.431-1.427 3.186-3.719 4.78-5.578c1.588-1.86 2.118-3.187 3.18-5.311c1.063-2.126.531-3.986-.264-5.579c-.798-1.593-6.987-17.343-9.819-23.64"/></svg> </span> </a></td>
                            @else
                            <td>{{ 'No registrado' }}</td>
                            @endif
                            <td>{{ $servicio->cliente->telefono ?? 'No registrado' }}</td>
                        </tr>
                        <tr>
                            <th>EPS:</th>
                            <td>{{ $servicio->cliente->eps ?? 'No registrado' }}</td>
                        </tr>
                        @if($servicio->cliente->ips)
                        <tr>
                            <th>IPS / Empresa:</th>
                            <td>{{ $servicio->cliente->ips->razon_social }}</td>
                        </tr>
                        @endif
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
                            <th>Canal de Difusión:</th>
                            <td>{{ $servicio->canal_difusion ?? 'No especificado' }}</td>
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
                                <td>
                                    {{ $servicioExamen->examen->nombre }}
                                    @if ($servicioExamen->es_remitido)
                                        <span class="badge bg-warning text-dark ms-1">Remitido</span>
                                    @endif
                                </td>
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
                                <td>
                                    @if($servicioExamen->puedeEditarse())
                                        <form method="POST" action="{{ route('servicios.actualizar-fecha-toma', $servicioExamen) }}" class="d-flex align-items-center gap-1">
                                            @csrf
                                            <input
                                                type="datetime-local"
                                                name="fecha_toma_muestra"
                                                class="form-control form-control-sm"
                                                style="min-width: 170px"
                                                value="{{ $servicioExamen->fecha_toma_muestra ? $servicioExamen->fecha_toma_muestra->format('Y-m-d\TH:i') : '' }}"
                                            >
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Guardar fecha">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{ $servicioExamen->fecha_toma_muestra ? $servicioExamen->fecha_toma_muestra->format('d/m/Y H:i') : '-' }}
                                    @endif
                                </td>
                                <td>{{ $servicioExamen->fecha_resultado ? $servicioExamen->fecha_resultado->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if ($servicioExamen->es_remitido)
                                            {{-- Examen remitido: sin captura ni validación --}}
                                            @if ($servicioExamen->estado !== 'ENTREGADO')
                                                @if ($servicioExamen->pdf_remision)
                                                    <a href="{{ route('remision.download', $servicioExamen) }}"
                                                       class="btn btn-danger" title="Descargar PDF remisión" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('remision.destroy', $servicioExamen) }}"
                                                          class="d-inline" onsubmit="return confirm('¿Eliminar el PDF de remisión?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar PDF remisión">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('remision.store', $servicioExamen) }}"
                                                          enctype="multipart/form-data" class="d-inline">
                                                        @csrf
                                                        <div class="input-group input-group-sm">
                                                            <input type="file" name="archivo"
                                                                   class="form-control form-control-sm"
                                                                   accept="application/pdf" required>
                                                            <button type="submit" class="btn btn-outline-primary" title="Subir PDF remisión">
                                                                <i class="fas fa-upload"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('servicios.cambiar-estado', $servicioExamen) }}"
                                                      class="d-inline ms-1">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="ENTREGADO">
                                                    <button type="submit" class="btn btn-dark" title="Marcar Entregado"
                                                            onclick="return confirm('¿Marcar este examen como entregado?')">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Entregado: solo descarga si tiene PDF --}}
                                                @if ($servicioExamen->pdf_remision)
                                                    <a href="{{ route('remision.download', $servicioExamen) }}"
                                                       class="btn btn-danger" title="Descargar PDF remisión" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            {{-- Examen normal: flujo completo --}}
                                            @if (in_array($servicioExamen->estado, ['PENDIENTE', 'EN_PROCESO']))
                                                <a href="{{ route('resultados.create', $servicioExamen) }}" class="btn btn-primary" title="Capturar Resultados">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            @if ($servicioExamen->estado == 'COMPLETADO')
                                                <a href="{{ route('resultados.create', $servicioExamen) }}" class="btn btn-warning" title="Editar Resultados">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('servicios.cambiar-estado', $servicioExamen) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="VALIDADO">
                                                    <button type="submit" class="btn btn-success" title="Validar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($servicioExamen->estado == 'VALIDADO')
                                                <a href="{{ route('servicios.examen.resultados-pdf', [$servicio, $servicioExamen]) }}" class="btn btn-danger" title="Imprimir PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('resultados.show', $servicioExamen) }}" class="btn btn-info" title="Ver Resultados">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" action="{{ route('servicios.cambiar-estado', $servicioExamen) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="estado" value="ENTREGADO">
                                                    <button type="submit" class="btn btn-dark" title="Marcar Entregado">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($servicioExamen->estado == 'ENTREGADO')
                                                <a href="{{ route('servicios.examen.resultados-pdf', [$servicio, $servicioExamen]) }}" class="btn btn-danger" title="Imprimir PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('resultados.show', $servicioExamen) }}" class="btn btn-info" title="Ver Resultados">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
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
                <p class="text-danger fw-semibold">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción es irreversible. Se eliminarán permanentemente todos los resultados,
                    imágenes adjuntas y PDFs asociados a esta orden.
                </p>
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
