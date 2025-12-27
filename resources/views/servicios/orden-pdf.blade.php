<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio - {{ $servicio->numero_orden }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; color: #000; line-height: 1.4; padding: 20px; }
        .header { border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header-content { display: table; width: 100%; }
        .logo-section { display: table-cell; width: 25%; vertical-align: middle; }
        .logo-section img { max-width: 100px; max-height: 70px; }
        .company-info { display: table-cell; width: 50%; vertical-align: middle; padding-left: 15px; }
        .company-info h2 { font-size: 14px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .company-info p { font-size: 9px; margin: 2px 0; line-height: 1.4; }
        .orden-info { display: table-cell; width: 25%; vertical-align: middle; text-align: right; }
        .orden-box { border: 2px solid #000; padding: 8px; margin-bottom: 5px; }
        .orden-box .label { font-size: 9px; font-weight: normal; }
        .orden-box .value { font-size: 12px; font-weight: bold; margin-top: 3px; }
        .date-box { font-size: 9px; margin-top: 8px; }
        .section-title { background: #000; color: #fff; padding: 6px 10px; font-size: 11px; font-weight: bold; margin-top: 15px; margin-bottom: 8px; text-transform: uppercase; }
        .info-grid { width: 100%; margin-bottom: 15px; border: 1px solid #000; }
        .info-row { display: table; width: 100%; }
        .info-cell { display: table-cell; padding: 8px 10px; border-right: 1px solid #000; vertical-align: top; font-size: 9px; }
        .info-cell:last-child { border-right: none; }
        .info-cell.left { width: 50%; border-bottom: 1px solid #000; }
        .info-cell.right { width: 50%; border-bottom: 1px solid #000; }
        .info-label { font-weight: bold; text-transform: uppercase; font-size: 8px; margin-bottom: 3px; }
        .info-value { font-size: 10px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th { background: #000; color: #fff; font-weight: bold; padding: 8px; text-align: left; border: 1px solid #000; font-size: 9px; text-transform: uppercase; }
        table td { padding: 8px; border: 1px solid #000; font-size: 10px; }
        table tr:nth-child(even) { background: #f5f5f5; }
        .total-section { width: 45%; float: right; border: 2px solid #000; padding: 10px; margin-top: 10px; }
        .total-row { display: table; width: 100%; margin-bottom: 5px; font-size: 10px; }
        .total-label { display: table-cell; font-weight: bold; }
        .total-value { display: table-cell; text-align: right; }
        .total-final { border-top: 2px solid #000; padding-top: 8px; margin-top: 8px; }
        .total-final .total-label { font-size: 12px; }
        .total-final .total-value { font-size: 12px; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border: 1px solid #000; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .observations { border: 1px solid #000; padding: 10px; margin-top: 15px; font-size: 9px; clear: both; }
        .observations strong { display: block; margin-bottom: 5px; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 2px solid #000; padding: 10px 20px; font-size: 8px; background: #fff; }
        .footer-content { display: table; width: 100%; }
        .footer-left { display: table-cell; width: 60%; vertical-align: top; }
        .footer-right { display: table-cell; width: 40%; text-align: right; vertical-align: top; }
        .footer p { margin: 2px 0; }
        .clearfix::after { content: ""; display: table; clear: both; }
        @page { margin: 100px 25px 80px 25px; }
        .content-wrapper { margin-bottom: 80px; }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    @if($empresa->logo)
                        <img src="{{ public_path('storage/' . $empresa->logo) }}" alt="Logo">
                    @else
                        <div style="width: 100px; height: 70px; border: 1px solid #000; display: flex; align-items: center; justify-content: center;"><span style="font-size: 8px;">LOGO</span></div>
                    @endif
                </div>
                <div class="company-info">
                    <h2>{{ $empresa->razon_social }}</h2>
                    <p><strong>NIT:</strong> {{ $empresa->nit }}</p>
                    <p><strong>Dirección:</strong> {{ $empresa->direccion }}, {{ $empresa->ciudad }}</p>
                    <p><strong>Teléfono:</strong> {{ $empresa->telefono_uno }}@if($empresa->telefono_dos) / {{ $empresa->telefono_dos }}@endif</p>
                    <p><strong>Email:</strong> {{ $empresa->email }}</p>
                </div>
                <div class="orden-info">
                    <div class="orden-box">
                        <div class="label">ORDEN DE SERVICIO</div>
                        <div class="value">{{ $servicio->numero_orden }}</div>
                    </div>
                    <div class="date-box">
                        <strong>Fecha:</strong> {{ $servicio->fecha->format('d/m/Y') }}<br>
                        <strong>Hora:</strong> {{ $servicio->created_at->format('h:i A') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="section-title">Datos del Paciente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell left"><div class="info-label">Nombre Completo</div><div class="info-value">{{ $servicio->cliente->nombre }} {{ $servicio->cliente->apellido }}</div></div>
                <div class="info-cell right"><div class="info-label">Documento</div><div class="info-value">{{ $servicio->cliente->tipo_documento }}: {{ $servicio->cliente->documento }}</div></div>
            </div>
            <div class="info-row">
                <div class="info-cell left"><div class="info-label">Género / Edad</div><div class="info-value">{{ $servicio->cliente->genero }} - {{ \Carbon\Carbon::parse($servicio->cliente->fecha_nacimiento)->age }} años ({{ \Carbon\Carbon::parse($servicio->cliente->fecha_nacimiento)->format('d/m/Y') }})</div></div>
                <div class="info-cell right"><div class="info-label">Teléfono</div><div class="info-value">{{ $servicio->cliente->telefono ?? 'No registrado' }}</div></div>
            </div>
            <div class="info-row">
                <div class="info-cell left" style="border-bottom: none;"><div class="info-label">Ciudad</div><div class="info-value">{{ $servicio->cliente->ciudad ?? 'No registrado' }}</div></div>
                <div class="info-cell right" style="border-bottom: none;"><div class="info-label">EPS</div><div class="info-value">{{ $servicio->cliente->eps ?? 'No registrado' }}</div></div>
            </div>
        </div>
        <div class="section-title">Detalle del Servicio </div>
        <table>
            <thead><tr><th style="width: 8%">Cant</th><th style="width: 15%">Cod</th><th style="width: 57%">Descripción</th><th style="width: 20%; text-align: right">Valor</th></tr></thead>
            <tbody>
                @foreach($servicio->serviciosExamen as $servicioExamen)
                <tr><td style="text-align: center">1</td><td>{{ $servicioExamen->examen->codigo ?? 'N/A' }}</td><td><strong>{{ $servicioExamen->examen->nombre }}</strong></td><td style="text-align: right">${{ number_format($servicioExamen->examen->valor_total, 0, ',', '.') }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-section">
            <div class="total-row"><div class="total-label">Subtotal:</div><div class="total-value">${{ number_format($servicio->valor_total, 0, ',', '.') }}</div></div>
            @if($servicio->valor_pagado > 0)
            <div class="total-row"><div class="total-label">Valor Pagado:</div><div class="total-value">${{ number_format($servicio->valor_pagado, 0, ',', '.') }}</div></div>
            @endif
            @if($servicio->saldo_pendiente > 0)
            <div class="total-row"><div class="total-label">Saldo Pendiente:</div><div class="total-value">${{ number_format($servicio->saldo_pendiente, 0, ',', '.') }}</div></div>
            @endif
            <div class="total-row total-final"><div class="total-label">TOTAL:</div><div class="total-value">${{ number_format($servicio->valor_total, 0, ',', '.') }}</div></div>
            <div style="margin-top: 10px; font-size: 9px;"><strong>Estado:</strong> @if($servicio->estado_pago === 'PAGADO')<span class="badge">PAGADO</span>@elseif($servicio->estado_pago === 'PARCIAL')<span class="badge">PAGO PARCIAL</span>@else<span class="badge">PENDIENTE</span>@endif @if($servicio->medio_pago)<br><strong>Medio:</strong> {{ $servicio->medio_pago }}@endif</div>
        </div>
        <div class="clearfix"></div>
        @if($servicio->observaciones)
        <div class="observations"><strong>Observaciones:</strong> {{ $servicio->observaciones }}</div>
        @endif
    </div>
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <p><strong>{{ $empresa->razon_social }}</strong> - NIT: {{ $empresa->nit }}</p>
                <p>{{ $empresa->direccion }}, {{ $empresa->ciudad }}</p>
                <p>Tel: {{ $empresa->telefono_uno }}@if($empresa->telefono_dos) / {{ $empresa->telefono_dos }}@endif - Email: {{ $empresa->email }}</p>
            </div>
            <div class="footer-right">
                <p><strong>Documento generado el:</strong></p>
                <p>{{ now()->format('d/m/Y h:i A') }}</p>
                <p style="margin-top: 5px; font-size: 7px;">Conserve este documento para la entrega de resultados</p>
            </div>
        </div>
    </div>
</body>


