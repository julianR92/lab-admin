@extends('layouts.app')

@section('title', 'Caja - Sistema de Laboratorio')

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .metric-card {
        border-radius: 12px;
        border: none;
    }
    .metric-card .metric-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .metric-card .metric-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .metric-card .metric-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }
    .table td { vertical-align: middle; font-size: 14px; }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 6px 14px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 5px 10px;
    }
    .periodo-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        padding: 4px 16px;
        font-size: 0.82rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-cash-register me-2" style="color: #667eea;"></i>Caja
            </h1>
            <p class="text-muted mb-0">Resumen financiero del laboratorio</p>
        </div>
        <span class="periodo-badge">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
        </span>
    </div>

    {{-- Filtros de fechas --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('caja.index') }}" id="filtroForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Fecha desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('caja.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tarjetas métricas principales --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #e8f4fd;">
                        <i class="fas fa-file-invoice" style="color: #3498db;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Órdenes</div>
                        <div class="metric-value">{{ number_format($totalOrdenes, 0, ',', '.') }}</div>
                        <small class="text-muted">{{ number_format($totalExamenes, 0, ',', '.') }} exámenes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #eaf7ec;">
                        <i class="fas fa-dollar-sign" style="color: #27ae60;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Valor Facturado</div>
                        <div class="metric-value">${{ number_format($valorFacturado, 0, ',', '.') }}</div>
                        <small class="text-muted">Total cobrado al paciente</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #fef5e7;">
                        <i class="fas fa-shipping-fast" style="color: #e67e22;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Costo Remisiones</div>
                        <div class="metric-value">${{ number_format($costoRemisiones, 0, ',', '.') }}</div>
                        <small class="text-muted">{{ number_format($totalRemitidos, 0, ',', '.') }} exámen(es) remitido(s)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card metric-card shadow-sm h-100" style="border-left: 4px solid #667eea !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #f0eeff;">
                        <i class="fas fa-chart-line" style="color: #667eea;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Ganancia Real Neta</div>
                        <div class="metric-value" style="color: #667eea;">${{ number_format($gananciaNeta, 0, ',', '.') }}</div>
                        <small class="text-muted">Facturado − costo remisiones</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tarjetas métricas secundarias --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #e8f8f5;">
                        <i class="fas fa-money-bill-wave" style="color: #1abc9c;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Valor Cobrado</div>
                        <div class="metric-value">${{ number_format($valorCobrado, 0, ',', '.') }}</div>
                        <small class="text-muted">Pagos recibidos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #fdf2f8;">
                        <i class="fas fa-hourglass-half" style="color: #e91e8c;"></i>
                    </div>
                    <div>
                        <div class="metric-label">Saldo Pendiente</div>
                        <div class="metric-value @if($saldoPendiente > 0) text-danger @endif">
                            ${{ number_format($saldoPendiente, 0, ',', '.') }}
                        </div>
                        <small class="text-muted">Por cobrar</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card metric-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background: #fff8e1;">
                        <i class="fas fa-exchange-alt" style="color: #ffc107;"></i>
                    </div>
                    <div>
                        <div class="metric-label">% Remisiones</div>
                        @php
                            $porcentajeRemision = $valorFacturado > 0
                                ? round(($costoRemisiones / $valorFacturado) * 100, 1)
                                : 0;
                        @endphp
                        <div class="metric-value">{{ $porcentajeRemision }}%</div>
                        <small class="text-muted">Del facturado en remisiones</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de detalle por orden --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Detalle por Orden
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="cajaTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Orden</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th class="text-center">Exámenes</th>
                            <th class="text-center">Remitidos</th>
                            <th class="text-end">Facturado</th>
                            <th class="text-end">Costo Rem.</th>
                            <th class="text-end">Ganancia</th>
                            <th>Estado Pago</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="fw-bold bg-light">
                            <td colspan="3">Totales del período</td>
                            <td id="pie-examenes" class="text-center"></td>
                            <td id="pie-remitidos" class="text-center"></td>
                            <td id="pie-facturado" class="text-end"></td>
                            <td id="pie-costo" class="text-end"></td>
                            <td id="pie-ganancia" class="text-end"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    var table = $('#cajaTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route('caja.index') }}' + window.location.search,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        },
        columns: [
            {
                data: 'numero_orden',
                render: function (data, type, row) {
                    return '<a href="/servicios/' + row.servicio_id + '" class="text-decoration-none fw-bold">' + data + '</a>';
                }
            },
            { data: 'fecha' },
            { data: 'cliente_nombre' },
            {
                data: 'total_examenes',
                className: 'text-center',
                render: function (data) {
                    return '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            {
                data: 'total_remitidos',
                className: 'text-center',
                render: function (data) {
                    if (data === 0) { return '<span class="text-muted">—</span>'; }
                    return '<span class="badge bg-warning text-dark">' + data + '</span>';
                }
            },
            {
                data: 'valor_facturado',
                className: 'text-end',
                render: function (data) {
                    return '$' + Number(data).toLocaleString('es-CO', { minimumFractionDigits: 0 });
                }
            },
            {
                data: 'costo_remision',
                className: 'text-end',
                render: function (data) {
                    if (data === 0) { return '<span class="text-muted">$0</span>'; }
                    return '<span class="text-danger">$' + Number(data).toLocaleString('es-CO', { minimumFractionDigits: 0 }) + '</span>';
                }
            },
            {
                data: 'ganancia_neta',
                className: 'text-end',
                render: function (data) {
                    return '<span class="fw-semibold text-success">$' + Number(data).toLocaleString('es-CO', { minimumFractionDigits: 0 }) + '</span>';
                }
            },
            { data: 'estado_pago', orderable: false },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[1, 'desc']],
        pageLength: 25,
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            var totalExamenes   = api.column(3, { page: 'all' }).data().reduce(function (a, b) { return a + Number($(b).text() || b); }, 0);
            var totalRemitidos  = api.column(4, { page: 'all' }).data().reduce(function (a, b) { var n = Number($(b).text() || 0); return a + (isNaN(n) ? 0 : n); }, 0);
            var totalFacturado  = api.column(5, { search: 'applied' }).data().reduce(function (a, b) { return a + Number($(b).text().replace(/[^0-9]/g, '') || b); }, 0);
            var totalCosto      = api.column(6, { search: 'applied' }).data().reduce(function (a, b) { return a + Number($(b).text().replace(/[^0-9]/g, '') || b); }, 0);
            var totalGanancia   = api.column(7, { search: 'applied' }).data().reduce(function (a, b) { return a + Number($(b).text().replace(/[^0-9]/g, '') || b); }, 0);

            // Usamos los valores de raw data para mayor precisión
            var rawFacturado = 0; var rawCosto = 0; var rawGanancia = 0;
            api.rows({ page: 'all' }).data().each(function (d) {
                rawFacturado += Number(d.valor_facturado || 0);
                rawCosto     += Number(d.costo_remision || 0);
                rawGanancia  += Number(d.ganancia_neta || 0);
            });

            var fmt = function (n) { return '$' + Number(n).toLocaleString('es-CO', { minimumFractionDigits: 0 }); };

            $('#pie-examenes').text(totalExamenes);
            $('#pie-remitidos').text(totalRemitidos);
            $('#pie-facturado').text(fmt(rawFacturado));
            $('#pie-costo').text(fmt(rawCosto));
            $('#pie-ganancia').text(fmt(rawGanancia));
        },
    });
});
</script>
@endpush
