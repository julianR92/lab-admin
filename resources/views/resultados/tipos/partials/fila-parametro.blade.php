@php
    $resultadoExistente = $servicioExamen->resultados->where('parametro_id', $parametro->id)->first();
    $esCalculado = $parametro->es_calculado;
@endphp

<tr class="{{ $esCalculado ? 'table-secondary' : '' }}">
    <td>
        <strong>{{ $parametro->nombre_parametro }}</strong>
        @if ($parametro->requerido && !$esCalculado)
            <span class="text-danger">*</span>
        @endif
        @if ($esCalculado)
            <span class="badge bg-info badge-sm ms-2">Calculado</span>
        @endif
    </td>
    <td>
        @if ($esCalculado)
            <input type="text"
                   name="resultados[{{ $parametro->id }}][valor]"
                   class="form-control form-control-sm parametro-calculado"
                   data-parametro-id="{{ $parametro->id }}"
                   data-codigo="{{ $parametro->codigo_parametro }}"
                   value="{{ $resultadoExistente ? $resultadoExistente->valor_numerico : '' }}"
                   readonly
                   style="background-color: #e9ecef;"
                   placeholder="Se calculará automáticamente">
        @elseif ($parametro->tipo_dato === 'SELECT' && $parametro->opciones_select)
            <select name="resultados[{{ $parametro->id }}][valor]"
                    class="form-select form-select-sm"
                    {{ $parametro->requerido ? 'required' : '' }}>
                <option value="">-- Seleccione --</option>
                @foreach ($parametro->opciones_select as $opcion)
                    <option value="{{ $opcion }}"
                            {{ $resultadoExistente && $resultadoExistente->valor_cualitativo == $opcion ? 'selected' : '' }}>
                        {{ $opcion }}
                    </option>
                @endforeach
            </select>
        @elseif (in_array($parametro->tipo_dato, ['DECIMAL', 'INTEGER']))
            <input type="number"
                   name="resultados[{{ $parametro->id }}][valor]"
                   class="form-control form-control-sm parametro-manual"
                   data-parametro-id="{{ $parametro->id }}"
                   data-codigo="{{ $parametro->codigo_parametro }}"
                   step="{{ $parametro->tipo_dato === 'DECIMAL' ? '0.' . str_repeat('0', ($parametro->decimales ?? 2) - 1) . '1' : '1' }}"
                   value="{{ $resultadoExistente ? $resultadoExistente->valor_numerico : '' }}"
                   {{ $parametro->requerido ? 'required' : '' }}>
        @elseif ($parametro->tipo_dato === 'TEXTO')
            <input type="text"
                   name="resultados[{{ $parametro->id }}][valor]"
                   class="form-control form-control-sm"
                   value="{{ $resultadoExistente ? $resultadoExistente->valor_texto : '' }}"
                   {{ $parametro->requerido ? 'required' : '' }}>
        @elseif ($parametro->tipo_dato === 'FECHA')
            <input type="date"
                   name="resultados[{{ $parametro->id }}][valor]"
                   class="form-control form-control-sm"
                   value="{{ $resultadoExistente && $resultadoExistente->valor_fecha ? $resultadoExistente->valor_fecha->format('Y-m-d') : '' }}"
                   {{ $parametro->requerido ? 'required' : '' }}>
        @elseif ($parametro->tipo_dato === 'HORA')
            <input type="time"
                   name="resultados[{{ $parametro->id }}][valor]"
                   class="form-control form-control-sm"
                   value="{{ $resultadoExistente ? $resultadoExistente->valor_hora : '' }}"
                   {{ $parametro->requerido ? 'required' : '' }}>
        @endif
    </td>
    <td>
        <span class="text-muted small">{{ $parametro->unidad_medida ?? '-' }}</span>
    </td>
    <td>
        @if ($parametro->valorReferenciaAplicable)
            <small class="text-muted">
                @if ($parametro->valorReferenciaAplicable->tipo_referencia === 'RANGO')
                    {{ $parametro->valorReferenciaAplicable->valor_min }} - {{ $parametro->valorReferenciaAplicable->valor_max }}
                @elseif ($parametro->valorReferenciaAplicable->tipo_referencia === 'CUALITATIVO')
                    {{ $parametro->valorReferenciaAplicable->valor_cualitativo }}
                @else
                    {{ $parametro->valorReferenciaAplicable->categoria }}
                @endif
            </small>
        @else
            <small class="text-muted">-</small>
        @endif
    </td>
    @if (isset($mostrarEstado) && $mostrarEstado)
    <td>
        @if ($resultadoExistente)
            <span class="badge bg-{{ $resultadoExistente->color_alerta }}">
                {{ $resultadoExistente->tipo_alerta }}
            </span>
        @else
            <span class="badge bg-secondary">Pendiente</span>
        @endif
    </td>
    @endif
</tr>
