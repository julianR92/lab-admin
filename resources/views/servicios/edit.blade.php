@extends('layouts.app')

@section('title', 'Editar Servicio - Sistema de Laboratorio')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2">
                <i class="fas fa-edit me-2"></i>Editar Orden {{ $servicio->numero_orden }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('servicios.show', $servicio) }}">{{ $servicio->numero_orden }}</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Errores en el formulario:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('servicios.update', $servicio) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Servicio</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Cliente</label>
                                <input type="text" class="form-control" value="{{ $servicio->cliente->nombre_completo }}" disabled>
                                <small class="text-muted">No se puede cambiar el cliente con exámenes procesados</small>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                       id="fecha" name="fecha" value="{{ old('fecha', $servicio->fecha->format('Y-m-d')) }}">
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror"
                                      id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $servicio->observaciones) }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Valor Total</label>
                            <input type="text" class="form-control" value="${{ number_format($servicio->valor_total, 0, ',', '.') }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="valor_pagado" class="form-label">Valor Pagado</label>
                            <input type="number" class="form-control @error('valor_pagado') is-invalid @enderror"
                                   id="valor_pagado" name="valor_pagado" value="{{ old('valor_pagado', $servicio->valor_pagado) }}"
                                   min="0" step="0.01">
                            @error('valor_pagado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="medio_pago" class="form-label">Medio de Pago</label>
                            <select class="form-select @error('medio_pago') is-invalid @enderror" id="medio_pago" name="medio_pago">
                                <option value="">-- Seleccione --</option>
                                <option value="Efectivo" {{ old('medio_pago', $servicio->medio_pago) == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Tarjeta débito" {{ old('medio_pago', $servicio->medio_pago) == 'Tarjeta débito' ? 'selected' : '' }}>Tarjeta débito</option>
                                <option value="Tarjeta crédito" {{ old('medio_pago', $servicio->medio_pago) == 'Tarjeta crédito' ? 'selected' : '' }}>Tarjeta crédito</option>
                                <option value="Transferencia" {{ old('medio_pago', $servicio->medio_pago) == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="Nequi" {{ old('medio_pago', $servicio->medio_pago) == 'Nequi' ? 'selected' : '' }}>Nequi</option>
                                <option value="Daviplata" {{ old('medio_pago', $servicio->medio_pago) == 'Daviplata' ? 'selected' : '' }}>Daviplata</option>
                            </select>
                            @error('medio_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Actualizar Servicio
                        </button>
                        <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
