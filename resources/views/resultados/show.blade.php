@extends('layouts.app')

@section('title', 'Resultados - ' . $servicioExamen->examen->nombre)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-file-medical-alt me-2"></i>Resultados del Examen
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('servicios.show', $servicioExamen->servicio_id) }}">{{ $servicioExamen->servicio->numero_orden }}</a></li>
                    <li class="breadcrumb-item active">Resultados</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('servicios.show', $servicioExamen->servicio_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            @if (in_array($servicioExamen->estado, ['VALIDADO', 'ENTREGADO']))
                <a type="button" class="btn btn-danger" href="{{ route('servicios.examen.resultados-pdf', [$servicioExamen->servicio, $servicioExamen]) }}" target="_blank">
                    <i class="fas fa-file-pdf me-2"></i>Generar PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Información del Paciente y Examen -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Paciente</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="40%">Nombre:</th>
                            <td>{{ $servicioExamen->servicio->cliente->nombre_completo }}</td>
                        </tr>
                        <tr>
                            <th>Documento:</th>
                            <td>{{ $servicioExamen->servicio->cliente->tipo_documento }}: {{ $servicioExamen->servicio->cliente->documento }}</td>
                        </tr>
                        <tr>
                            <th>Edad/Género:</th>
                            <td>{{ $servicioExamen->servicio->cliente->edad }} años - {{ $servicioExamen->servicio->cliente->genero }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-vial me-2"></i>Examen</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="40%">Nombre:</th>
                            <td><strong>{{ $servicioExamen->examen->nombre }}</strong></td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td>{{ $servicioExamen->examen->codigo }}</td>
                        </tr>
                        <tr>
                            <th>Categoría:</th>
                            <td><span class="badge bg-secondary">{{ $servicioExamen->examen->categoria->categoria }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Estado</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="40%">Estado:</th>
                            <td>
                                @switch($servicioExamen->estado)
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
                        </tr>
                        <tr>
                            <th>Profesional:</th>
                            <td>{{ $servicioExamen->profesional->nombre }} {{ $servicioExamen->profesional->apellido }}</td>
                        </tr>
                        <tr>
                            <th>F. Resultado:</th>
                            <td>{{ $servicioExamen->fecha_resultado ? $servicioExamen->fecha_resultado->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Resultados del Examen</h5>
        </div>
        <div class="card-body">
            @if ($servicioExamen->examen->tipo_resultado === 'TEXTO_DESCRIPTIVO')
                {{-- Mostrar resultados descriptivos --}}
                @php
                    $resultado = $servicioExamen->resultados->first();
                @endphp
                @if ($resultado)
                    @if ($resultado->observaciones)
                        <div class="mb-4">
                            <h6 class="text-primary"><i class="fas fa-eye me-2"></i>Observaciones</h6>
                            <p class="ms-3">{{ $resultado->observaciones }}</p>
                        </div>
                    @endif

                    @if ($resultado->interpretacion)
                        <div class="mb-4">
                            <h6 class="text-primary"><i class="fas fa-microscope me-2"></i>Interpretación</h6>
                            <p class="ms-3">{{ $resultado->interpretacion }}</p>
                        </div>
                    @endif

                    @if ($resultado->conclusiones)
                        <div class="mb-4">
                            <h6 class="text-primary"><i class="fas fa-check-circle me-2"></i>Conclusiones</h6>
                            <p class="ms-3">{{ $resultado->conclusiones }}</p>
                        </div>
                    @endif
                @else
                    <p class="text-muted">No hay resultados capturados.</p>
                @endif
            @else
                {{-- Mostrar resultados tabulares --}}
                @foreach ($resultadosAgrupados as $seccion => $resultados)
                    @if ($resultadosAgrupados->count() > 1)
                        <h5 class="text-primary border-bottom pb-2 mt-3">
                            <i class="fas fa-folder me-2"></i>{{ $seccion }}
                        </h5>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Parámetro</th>
                                    <th width="20%">Valor</th>
                                    <th width="20%">Rango Referencia</th>
                                    <th width="15%">Estado</th>
                                    <th width="15%">Capturado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resultados as $resultado)
                                    <tr>
                                        <td><strong>{{ $resultado->parametro->nombre_parametro }}</strong></td>
                                        <td>
                                            <span class="{{ $resultado->clase_bootstrap }}">
                                                {{ $resultado->valor_formateado }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $resultado->rango_referencia ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $resultado->color_alerta }}">
                                                {{ $resultado->icono_alerta }} {{ $resultado->tipo_alerta }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $resultado->capturadoPor->name ?? '-' }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

                {{-- Resumen de alertas --}}
                @php
                    $totalResultados = $servicioExamen->resultados->count();
                    $criticos = $servicioExamen->resultados->where('tipo_alerta', 'CRITICO')->count();
                    $altos = $servicioExamen->resultados->where('tipo_alerta', 'ALTO')->count();
                    $bajos = $servicioExamen->resultados->where('tipo_alerta', 'BAJO')->count();
                @endphp

                @if ($criticos > 0 || $altos > 0 || $bajos > 0)
                    <div class="alert alert-warning mt-3">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Alertas Detectadas:</strong>
                        <ul class="mb-0 mt-2">
                            @if ($criticos > 0)
                                <li><span class="badge bg-danger">{{ $criticos }}</span> valores críticos</li>
                            @endif
                            @if ($altos > 0)
                                <li><span class="badge bg-warning text-dark">{{ $altos }}</span> valores altos</li>
                            @endif
                            @if ($bajos > 0)
                                <li><span class="badge bg-info">{{ $bajos }}</span> valores bajos</li>
                            @endif
                        </ul>
                    </div>
                @endif
            @endif

            {{-- Opinión Profesional --}}
            @if ($servicioExamen->observaciones)
                <div class="card bg-light mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-user-md me-2"></i>Opinión Profesional
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $servicioExamen->observaciones }}</p>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>{{ $servicioExamen->profesional->nombre }} {{ $servicioExamen->profesional->apellido }}
                            @if ($servicioExamen->fecha_resultado)
                                | <i class="fas fa-clock me-1"></i>{{ $servicioExamen->fecha_resultado->format('d/m/Y H:i') }}
                            @endif
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Galería de Adjuntos --}}
    @include('resultados.partials.galeria-adjuntos')
</div>
@endsection
