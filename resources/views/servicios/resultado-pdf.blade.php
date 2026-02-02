<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Resultado de Laboratorio</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;

            }
        }

        @page {
            margin: 20px 20px 20px 15px;
        }
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, Geneva, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.4;
            margin-top: 120px;


        }
         header {
            position: fixed;
            left: 0px;
            top: 0;
            right: 0px;
            height: 10px;
            text-align: center;
            float: left;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            padding: 3px 0;
        }

        /* Línea en el footer */
        .footer::before {
            content: "";
            display: block;
            width: 100%;
            height: 1px;
            background-color: #000;
            margin-bottom: 2px;
        }

        /* .contenedor-principal {
            width: 100%;
            position: relative;
            top: 150px;

        } */


        .header-table {
            width: 100%;
            border-bottom: 1.5px solid #333;
            padding-bottom: 6px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 80px;
            text-align: center;
            padding: 0 5px;
        }

        .logo-cell img {
            width: 70px;
            height: auto;
        }

        .info-cell {
            text-align: center;
            padding: 0 10px;
        }

        .empresa-nombre {
            font-size: 10pt;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 1px;
            letter-spacing: 0.3px;
        }

        .empresa-nit {
            font-size: 7.5pt;
            margin-bottom: 2px;
        }

        .empresa-datos {
            font-size: 6.5pt;
            color: #333;
            line-height: 1.4;
        }

        /* ============ DATOS PACIENTE ============ */
        .paciente-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            border: 1px solid #444;
        }

        .paciente-table td {
            padding: 5px 10px;
            font-size: 8.5pt;
            border: 1px solid #444;
        }

        .paciente-table .label {
            background-color: #e8e8e8;
            font-weight: bold;
            width: 22%;
            color: #1a1a1a;
        }

        .paciente-table .value {
            width: 28%;
        }

        /* ============ TÍTULO RESULTADOS ============ */
        .titulo-resultados {
            text-align: center;
            padding: 10px 0;
            font-size: 11pt;
            font-weight: bold;
            margin: 15px 0 12px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            letter-spacing: 1px;
            color: #1a1a1a;
        }

        /* ============ CATEGORÍA/EXAMEN ============ */
        .categoria-titulo {
            background-color: #333;
            color: #fff;
            padding: 6px 12px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        /* ============ TABLA DE RESULTADOS ============ */
        .tabla-resultados {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .tabla-resultados thead {
            background-color: #f0f0f0;
        }

        .tabla-resultados th {
            padding: 7px 10px;
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            border: 1px solid #444;
            color: #1a1a1a;
        }

        .tabla-resultados td {
            padding: 5px 10px;
            font-size: 8.5pt;
            border: 1px solid #444;
        }

        .tabla-resultados .col-parametro {
            text-align: left;
            width: 35%;
        }

        .tabla-resultados .col-resultado {
            text-align: center;
            width: 20%;
            font-weight: bold;
        }

        .tabla-resultados .col-unidad {
            text-align: center;
            width: 15%;
        }

        .tabla-resultados .col-referencia {
            text-align: center;
            width: 30%;
        }

        .tabla-resultados tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Alertas */
        .valor-alto {
            color: #c0392b;
            font-weight: bold;
        }

        .valor-bajo {
            color: #d35400;
            font-weight: bold;
        }

        /* ============ TEXTOS ============ */
        .texto-bloque {
            margin: 10px 0;
            padding: 8px 12px;
            font-size: 8.5pt;
            text-align: justify;
            line-height: 1.6;
            background-color: #fafafa;
            border-left: 3px solid #666;
        }

        .texto-bloque strong {
            display: block;
            margin-bottom: 4px;
            color: #1a1a1a;
        }

        .tecnica-info {
            font-size: 8pt;
            /* margin: 8px 0;
            padding: 5px 10px; */
            color: #444;
        }

        /* ============ RESULTADOS CUALITATIVOS ============ */
        .resultado-cualitativo {
            margin: 15px 0;
            padding: 15px;
            background-color: #fafafa;
            border: 1px solid #ddd;
        }

        .resultado-cualitativo .titulo-examen {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
        }

        .resultado-cualitativo .seccion-titulo {
            font-size: 9pt;
            font-weight: bold;
            margin: 12px 0 8px 0;
            color: #1a1a1a;
        }

        .tabla-cualitativa {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        .tabla-cualitativa td {
            padding: 4px 10px;
            font-size: 9pt;
            border: none;
        }

        .tabla-cualitativa .parametro-nombre {
            font-weight: normal;
            color: #1a1a1a;
            width: 20%;
            text-align: left;
        }

        .tabla-cualitativa .parametro-valor {
            font-weight: bold;
            text-align: left;
            width: 80%;
        }

        /* ============ IMÁGENES ADJUNTAS ============ */
        .imagenes-container {
            margin: 15px 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .imagenes-row {
            width: 100%;
            text-align: center;
        }

        .imagen-item {
            display: inline-block;
            width: 30%;
            margin: 8px 1%;
            vertical-align: top;
        }

        .imagen-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border: 1px solid #999;
        }

        .imagen-descripcion {
            font-size: 7pt;
            color: #666;
            margin-top: 3px;
            font-style: italic;
        }

        /* ============ NOTA ============ */
        .nota-final {
            margin-top: 20px;
            font-size: 7.5pt;
            font-style: italic;
            text-align: center;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        /* ============ FIRMA ============ */
        .firma-section {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .firma-container {
            text-align: center;
            width: 250px;
            margin: 0 auto;
        }

        .firma-imagen {
            margin-bottom: 8px;
        }

        .firma-imagen img {
            max-width: 130px;
            max-height: 45px;
        }

        .firma-linea {
            border-top: 1px solid #333;
            padding-top: 8px;
        }

        .firma-datos {
            font-size: 8pt;
            text-align: center;
            line-height: 1.5;
            color: #1a1a1a;
        }

        .firma-datos .profesion {
            font-weight: bold;
            font-size: 8.5pt;
        }

        /* ============ SEPARADOR ============ */
        .separador {
            border-top: 1px dashed #999;
            margin: 25px 0;
        }

    </style>
</head>
<body>
    <!-- ============ ENCABEZADO FIXED (SE REPITE EN CADA PÁGINA) ============ -->
    <header>
        <table class="header-table">
            <tr>
                @if($empresa && $empresa->logo)
                <td class="logo-cell">
                    <img src="{{ public_path('storage/' . $empresa->logo) }}" alt="Logo">
                </td>
                @endif
                <td class="info-cell">
                    <div class="empresa-nombre">{{ strtoupper($empresa->razon_social ?? 'LABORATORIO CLÍNICO') }}</div>
                    <div class="empresa-nit">{{ $empresa->nit ?? '' }}</div>
                    <div class="empresa-datos">
                        {{ $empresa->direccion ?? '' }}{{ $empresa->barrio ? ' – ' . $empresa->barrio : '' }}
                        <br> Tel: {{ $empresa->telefono_uno ?? '' }}{{ $empresa->telefono_dos ? ' – ' . $empresa->telefono_dos : '' }}
                        <br> {{ $empresa->email ?? '' }} <br>{{ $empresa->ciudad ?? '' }}
                    </div>
                </td>
            </tr>
        </table>
    </header>
    <div class="footer">
        <span style="font-size:8px">Generado {{ date('Y-m-d H:i:s') }}</span>
    </div>

    <div class="contenedor-principal">
    <!-- ============ DATOS DEL PACIENTE ============ -->
    <table class="paciente-table">
        <tr>
            <td class="label">ID de Paciente:</td>
            <td class="value">{{ $servicio->cliente->documento }}</td>
            <td class="label">Sede:</td>
            <td class="value">{{ $empresa->barrio ?? 'Principal' }}</td>
        </tr>
        <tr>
            <td class="label">Paciente:</td>
            <td class="value">{{ $servicio->cliente->nombre }} {{ $servicio->cliente->apellido }}</td>
            <td class="label">Sexo:</td>
            <td class="value">{{ $servicio->cliente->genero == 'M' ? 'Masculino' : ($servicio->cliente->genero == 'F' ? 'Femenino' : $servicio->cliente->genero) }}</td>
        </tr>
        <tr>
            <td class="label">Identificación:</td>
            <td class="value">{{ number_format((float) preg_replace('/[^0-9]/', '', $servicio->cliente->documento), 0, '', '.') }}</td>
            <td class="label">Teléfono:</td>
            <td class="value">{{ $servicio->cliente->telefono ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de nacimiento:</td>
            <td class="value">{{ \Carbon\Carbon::parse($servicio->cliente->fecha_nacimiento)->format('d-m-Y') }}</td>
            <td class="label">Fecha de reporte:</td>
            <td class="value">{{ \Carbon\Carbon::parse($servicio->fecha)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Edad:</td>
            <td class="value">{{ $servicio->cliente->edad }} años</td>
            <td class="label">Ciudad:</td>
            <td class="value">{{ $servicio->cliente->ciudad ?? 'No registrado' }}</td>
        </tr>
    </table>

    <!-- ============ TÍTULO RESULTADOS ============ -->
    <div class="titulo-resultados">
        RESULTADOS CLÍNICOS DE LABORATORIO
    </div>

    <!-- ============ RESULTADOS POR CATEGORÍA/EXAMEN ============ -->
    @php
        $examenesAgrupados = $servicio->serviciosExamen->groupBy(function($se) {
            return $se->examen->categoria->categoria ?? 'OTROS';
        });
    @endphp

    @foreach($examenesAgrupados as $categoria => $examenes)
        <!-- Título de Categoría -->
        <div class="categoria-titulo">
            {{ strtoupper($categoria) }}
        </div>

        @foreach($examenes as $servicioExamen)
            @php
                $tipoResultado = $servicioExamen->examen->tipo_resultado;
                $resultados = $servicioExamen->resultados;
                $esCualitativoLista = in_array($tipoResultado, ['CUALITATIVO_SIMPLE', 'CUALITATIVO_MULTIPLE_OPCIONES']);
                $esTextoDescriptivo = $tipoResultado === 'TEXTO_DESCRIPTIVO';
                $hayResultadosTabla = in_array($tipoResultado, ['NUMERICO_SIMPLE', 'NUMERICO_CATEGORIZADO', 'TABLA_HEMATOLOGIA', 'MULTIPLE_CALCULADO', 'CUALITATIVO_REACTIVO']);
            @endphp

            <!-- Nombre del Examen (para tablas tipo hematología, numérico, etc.) -->
            @if($hayResultadosTabla && $resultados->count() > 0)
                <div style="font-weight: bold; font-size: 9pt; margin: 12px 0 5px 0; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 3px;">
                    {{ strtoupper($servicioExamen->examen->nombre) }}
                </div>
            @endif

            @if($esTextoDescriptivo)
                <!-- FORMATO TEXTO DESCRIPTIVO -->
                <div class="resultado-cualitativo">
                    <div class="titulo-examen">{{ strtoupper($servicioExamen->examen->nombre) }}</div>

                    @php
                        $observaciones = $resultados->whereNotNull('observaciones')->first()?->observaciones;
                        $interpretacion = $resultados->whereNotNull('interpretacion')->first()?->interpretacion;
                        $conclusiones = $resultados->whereNotNull('conclusiones')->first()?->conclusiones;
                    @endphp

                    <table class="tabla-cualitativa">
                        @if($interpretacion)
                            <tr>
                                <td class="parametro-nombre">INTERPRETACIÓN:</td>
                                <td class="parametro-valor" style="text-align: justify;">{{ $interpretacion }}</td>
                            </tr>
                        @endif

                        @if($observaciones)
                            <tr>
                                <td class="parametro-nombre">OBSERVACIONES:</td>
                                <td class="parametro-valor" style="text-align: justify;">{{ $observaciones }}</td>
                            </tr>
                        @endif

                        @if($conclusiones)
                            <tr>
                                <td class="parametro-nombre">CONCLUSIÓN:</td>
                                <td class="parametro-valor" style="text-align: justify;">{{ $conclusiones }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @elseif($esCualitativoLista && $resultados->count() > 0)
                <!-- FORMATO CUALITATIVO (LISTA) -->
                <div class="resultado-cualitativo">
                    <div class="titulo-examen">{{ strtoupper($servicioExamen->examen->nombre) }}</div>

                    @php
                        // Agrupar resultados por sección
                        $resultadosAgrupados = $resultados->groupBy(function($resultado) {
                            return $resultado->parametro->seccion ?? '';
                        });

                        // Separar los que tienen sección de los que no
                        $sinSeccion = $resultadosAgrupados->pull('');
                        $conSeccion = $resultadosAgrupados->sortKeys();

                        // Si hay resultados sin sección, agregarlos al final
                        if ($sinSeccion && $sinSeccion->isNotEmpty()) {
                            $conSeccion->put('', $sinSeccion);
                        }

                        $resultadosOrdenados = $conSeccion;
                    @endphp

                    @foreach($resultadosOrdenados as $seccion => $resultadosSeccion)
                        @if($seccion !== '')
                            <div class="seccion-titulo">{{ strtoupper($seccion) }}</div>
                        @endif

                        <table class="tabla-cualitativa">
                            @foreach($resultadosSeccion as $resultado)
                                <tr>
                                    <td class="parametro-nombre">{{ $resultado->parametro->nombre_parametro }}:</td>
                                    <td class="parametro-valor">
                                        @if($resultado->valor_numerico !== null)
                                            {{ number_format($resultado->valor_numerico, $resultado->parametro->decimales ?? 2, '.', '') }}
                                            {{ $resultado->unidad_medida ?? $resultado->parametro->unidad_medida }}
                                        @elseif($resultado->valor_cualitativo !== null)
                                            {{ $resultado->valor_cualitativo }}
                                        @else
                                            {{ $resultado->valor_texto }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endforeach
                </div>
            @elseif($hayResultadosTabla && $resultados->count() > 0)
                <!-- TABLA DE RESULTADOS -->
                <table class="tabla-resultados">
                    <thead>
                        <tr>
                            <th class="col-parametro">PARÁMETRO</th>
                            <th class="col-resultado">RESULTADO</th>
                            <th class="col-unidad">UNIDADES</th>
                            <th class="col-referencia">VALORES REFERENCIA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultados as $resultado)
                            @php
                                $valorMostrar = '';
                                if ($resultado->valor_numerico !== null) {
                                    $decimales = $resultado->parametro->decimales ?? 2;
                                    $valorMostrar = number_format($resultado->valor_numerico, $decimales, '.', '');
                                } elseif ($resultado->valor_cualitativo !== null) {
                                    $valorMostrar = $resultado->valor_cualitativo;
                                } elseif ($resultado->valor_texto !== null) {
                                    $valorMostrar = $resultado->valor_texto;
                                }

                                $claseAlerta = '';
                                $simbolo = '';
                                if ($resultado->fuera_rango) {
                                    if ($resultado->tipo_alerta === 'BAJO') {
                                        $claseAlerta = 'valor-bajo';
                                        $simbolo = '*';
                                    } elseif (in_array($resultado->tipo_alerta, ['ALTO', 'CRITICO'])) {
                                        $claseAlerta = 'valor-alto';
                                        $simbolo = '*';
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="col-parametro">{{ $resultado->parametro->nombre_parametro }}</td>
                                <td class="col-resultado {{ $claseAlerta }}">{{ $valorMostrar }}{{ $simbolo }}</td>
                                <td class="col-unidad">{{ $resultado->unidad_medida ?? $resultado->parametro->unidad_medida }}</td>
                                <td class="col-referencia">{{ $resultado->rango_referencia ?? '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

             @if($servicioExamen->observaciones)
                <div class="tecnica-info"  style="margin-top: 25px;">
                    <strong>Observacion Medica:</strong> {{ $servicioExamen->observaciones }}
                </div>
            @endif

            <!-- Técnica -->
            @if($servicioExamen->examen->tecnica)
                <div class="tecnica-info">
                    <strong>Técnica:</strong> {{ $servicioExamen->examen->tecnica }}
                </div>
            @endif
            @if($servicioExamen->fecha_resultado)
                <div class="tecnica-info">
                    <strong>Fecha:</strong> {{ $servicioExamen->fecha_resultado }}
                </div>
            @endif

            {{-- <!-- Textos Descriptivos (solo para NO TEXTO_DESCRIPTIVO) -->
            @if(!$esTextoDescriptivo)
                @php
                    $observaciones = $resultados->whereNotNull('observaciones')->first()?->observaciones;
                    $interpretacion = $resultados->whereNotNull('interpretacion')->first()?->interpretacion;
                    $conclusiones = $resultados->whereNotNull('conclusiones')->first()?->conclusiones;
                @endphp

                @if($interpretacion)
                    <div class="texto-bloque">
                        <strong>INTERPRETACIÓN:</strong>
                        {{ $interpretacion }}
                    </div>
                @endif

                @if($observaciones)
                    <div class="texto-bloque">
                        <strong>OBSERVACIONES:</strong>
                        {{ $observaciones }}
                    </div>
                @endif

                @if($conclusiones)
                    <div class="texto-bloque">
                        <strong>CONCLUSIÓN:</strong>
                        {{ $conclusiones }}
                    </div>
                @endif
            @endif --}}

            <!-- IMÁGENES ADJUNTAS (máx 3 por fila) -->
            @if($servicioExamen->adjuntos && $servicioExamen->adjuntos->count() > 0)
                <div class="imagenes-container">
                    @php
                        $adjuntosImagenes = $servicioExamen->adjuntos->filter(function($adj) {
                            $ruta = storage_path('app/public/' . $adj->ruta_archivo);
                            return file_exists($ruta) && in_array(strtolower(pathinfo($ruta, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                        });
                        $chunks = $adjuntosImagenes->chunk(3);
                    @endphp

                    @foreach($chunks as $chunk)
                        <div class="imagenes-row">
                            @foreach($chunk as $adjunto)
                                @php
                                    $rutaArchivo = storage_path('app/public/' . $adjunto->ruta_archivo);
                                @endphp
                                <div class="imagen-item">
                                    <img src="{{ $rutaArchivo }}" alt="{{ $adjunto->nombre_archivo }}">
                                    @if($adjunto->descripcion)
                                        <div class="imagen-descripcion">{{ $adjunto->descripcion }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Firma del Profesional (una por cada examen) -->
            @if($servicioExamen->profesional)
                <div class="firma-section">
                    <div class="firma-container">
                        <div class="firma-imagen">
                            @if($servicioExamen->profesional->firma_digital)
                                @php
                                    $rutaFirma = storage_path('app/public/' . $servicioExamen->profesional->firma_digital);
                                @endphp
                                @if(file_exists($rutaFirma))
                                    <img src="{{ $rutaFirma }}" alt="Firma">
                                @endif
                            @endif
                        </div>
                        <div class="firma-linea">
                            <div class="firma-datos">
                                <div class="profesion">{{ strtoupper($servicioExamen->profesional->profesion ?? 'PROFESIONAL') }}</div>
                                {{ strtoupper($servicioExamen->profesional->nombre) }} {{ strtoupper($servicioExamen->profesional->apellido) }}<br>
                                {{ $servicioExamen->profesional->documento }}{{ $servicioExamen->profesional->especialidad ? ' – ' . $servicioExamen->profesional->especialidad : '' }} <br>
                                {{ $servicioExamen->fecha_resultado ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if(!$loop->last)
            <div class="separador"></div>
        @endif
    @endforeach

    <!-- ============ NOTA FINAL ============ -->
    <div class="nota-final">
        <em>Nota: Se sugiere correlacionar con historial médico y sintomatología clínica</em>
    </div>
    </div><!-- Fin contenedor-principal -->

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 7;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 28;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>
</body>
</html>
