{{-- Vista para captura estándar (NUMERICO_SIMPLE, NUMERICO_CATEGORIZADO, CUALITATIVO_SIMPLE) --}}
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th width="30%">Parámetro</th>
                <th width="20%">Valor</th>
                <th width="15%">Unidad</th>
                <th width="25%">Rango Referencia</th>
                <th width="10%">Categoría</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($servicioExamen->examen->parametros as $parametro)
                @php
                    $resultadoExistente = $servicioExamen->resultados->where('parametro_id', $parametro->id)->first();
                    // Obtener TODOS los valores de referencia para este parámetro
                    $valoresReferencia = $parametro->valoresReferencia()
                        ->where('status', true)
                        ->orderBy('orden')
                        ->get();
                    $esCategorizado = $valoresReferencia->first() && $valoresReferencia->first()->tipo_referencia === 'CATEGORIZADO';
                @endphp
                <tr data-parametro-id="{{ $parametro->id }}">
                    <td>
                        <strong>{{ $parametro->nombre_parametro }}</strong>
                        @if ($parametro->requerido)
                            <span class="text-danger">*</span>
                        @endif
                    </td>
                    <td>
                        @if ($parametro->tipo_dato === 'SELECT' && $parametro->opciones_select)
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
                            <input type="text"
                                   name="resultados[{{ $parametro->id }}][valor]"
                                   class="form-control form-control-sm valor-input valor-numerico"
                                   data-parametro-id="{{ $parametro->id }}"
                                   data-es-categorizado="{{ $esCategorizado ? '1' : '0' }}"
                                   data-tipo-dato="{{ $parametro->tipo_dato }}"
                                   data-decimales="{{ $parametro->decimales ?? 2 }}"
                                   value="{{ $resultadoExistente ? number_format($resultadoExistente->valor_numerico, 2, '.', ',') : '' }}"
                                   placeholder="0.00"
                                   autocomplete="off"
                                   {{ $parametro->requerido ? 'required' : '' }}>
                        @elseif ($parametro->tipo_dato === 'TEXTO')
                            <input type="text"
                                   name="resultados[{{ $parametro->id }}][valor]"
                                   class="form-control form-control-sm"
                                   value="{{ $resultadoExistente ? $resultadoExistente->valor_texto : '' }}"
                                   {{ $parametro->requerido ? 'required' : '' }}>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted">{{ $parametro->unidad_medida ?? '-' }}</span>
                    </td>
                    <td>
                        <small class="text-muted rango-referencia" id="rango-{{ $parametro->id }}">
                            @if ($esCategorizado)
                                {{-- Mostrar todos los rangos para tipo CATEGORIZADO --}}
                                @foreach ($valoresReferencia as $vr)
                                    <div class="mb-1">
                                        <strong>{{ $vr->categoria }}:</strong>
                                        @if ($vr->valor_min && $vr->valor_max)
                                            {{ $vr->valor_min }} - {{ $vr->valor_max }}
                                        @elseif ($vr->valor_min)
                                            {{ $vr->valor_min }} +
                                        @elseif ($vr->valor_max)
                                            0 - {{ $vr->valor_max }}
                                        @endif
                                    </div>
                                @endforeach
                            @elseif ($parametro->valorReferenciaAplicable)
                                {{-- Mostrar rango único para tipo RANGO --}}
                                @if ($parametro->valorReferenciaAplicable->tipo_referencia === 'RANGO')
                                    {{ $parametro->valorReferenciaAplicable->valor_min }} - {{ $parametro->valorReferenciaAplicable->valor_max }}
                                @elseif ($parametro->valorReferenciaAplicable->tipo_referencia === 'CUALITATIVO')
                                    {{ $parametro->valorReferenciaAplicable->valor_cualitativo }}
                                @endif
                            @else
                                -
                            @endif
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-secondary categoria-badge" id="categoria-{{ $parametro->id }}">
                            -
                        </span>
                        @if ($esCategorizado)
                            {{-- Almacenar datos de rangos en JSON para JavaScript --}}
                            <script type="application/json" id="rangos-{{ $parametro->id }}">
                                {!! json_encode($valoresReferencia->map(function($vr) {
                                    return [
                                        'categoria' => $vr->categoria,
                                        'valor_min' => $vr->valor_min,
                                        'valor_max' => $vr->valor_max,
                                        'operador' => $vr->operador,
                                    ];
                                })->values()) !!}
                            </script>
                        @endif
                    </td>


                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
// Formateo elegante de números con validación
function limpiarNumero(valor) {
    // Remover todo excepto números, punto y coma
    return valor.replace(/[^0-9.,]/g, '');
}

function formatearConSeparadorMiles(numero, decimales) {
    const partes = numero.toString().split('.');
    const parteEntera = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    const parteDecimal = partes[1] ? '.' + partes[1].substring(0, decimales) : '';
    return parteEntera + parteDecimal;
}

function obtenerValorNumerico(input) {
    // Convertir el valor formateado a número puro
    let valor = input.value.replace(/,/g, ''); // Quitar comas
    valor = valor.replace(/,/g, '.'); // Normalizar separador decimal
    return parseFloat(valor) || 0;
}

document.addEventListener('DOMContentLoaded', function() {
    // Validación y formato de inputs numéricos
    const inputsNumericos = document.querySelectorAll('.valor-numerico');

    inputsNumericos.forEach(input => {
        const decimales = parseInt(input.dataset.decimales) || 2;

        // Permitir escritura libre, solo validar caracteres
        input.addEventListener('input', function(e) {
            const cursorPos = this.selectionStart;
            const valorAnterior = this.value;

            // Solo permitir números, punto y coma
            let valorLimpio = limpiarNumero(this.value);

            // Solo un separador decimal
            const puntos = (valorLimpio.match(/[.,]/g) || []).length;
            if (puntos > 1) {
                valorLimpio = valorLimpio.replace(/[.,](?=.*[.,])/g, '');
            }

            // Limitar decimales mientras escribe
            if (valorLimpio.includes('.') || valorLimpio.includes(',')) {
                const partes = valorLimpio.split(/[.,]/);
                if (partes[1] && partes[1].length > decimales) {
                    partes[1] = partes[1].substring(0, decimales);
                    valorLimpio = partes.join('.');
                }
            }

            this.value = valorLimpio;

            // Restaurar posición del cursor si cambió el valor
            if (valorAnterior !== valorLimpio) {
                this.setSelectionRange(cursorPos, cursorPos);
            }
        });

        // Formatear con separador de miles al perder el foco
        input.addEventListener('blur', function() {
            if (!this.value) return;

            let valor = this.value.replace(/,/g, '');
            const numero = parseFloat(valor);

            if (!isNaN(numero)) {
                this.value = formatearConSeparadorMiles(numero, decimales);
            }
        });

        // Quitar formato al enfocar para facilitar edición
        input.addEventListener('focus', function() {
            if (this.value) {
                this.value = this.value.replace(/,/g, '');
            }
        });

        // Formatear valor inicial si existe
        if (input.value) {
            const numero = parseFloat(input.value.replace(/,/g, ''));
            if (!isNaN(numero)) {
                input.value = formatearConSeparadorMiles(numero, decimales);
            }
        }
    });

    // Evaluación en tiempo real de categorías
    const valoresInputs = document.querySelectorAll('.valor-input[data-es-categorizado="1"]');

    valoresInputs.forEach(input => {
        input.addEventListener('input', function() {
            const parametroId = this.dataset.parametroId;
            const valor = obtenerValorNumerico(this);
            const rangosElement = document.getElementById('rangos-' + parametroId);
            const categoriaBadge = document.getElementById('categoria-' + parametroId);

            if (!rangosElement || !categoriaBadge || isNaN(valor) || valor === 0) {
                if (categoriaBadge) {
                    categoriaBadge.textContent = '-';
                    categoriaBadge.className = 'badge bg-secondary categoria-badge';
                }
                return;
            }

            const rangos = JSON.parse(rangosElement.textContent);
            let categoriaEncontrada = null;

            // Buscar en qué categoría cae el valor
            for (const rango of rangos) {
                let dentroDelRango = false;

                if (rango.operador) {
                    switch (rango.operador) {
                        case '<':
                            dentroDelRango = valor < rango.valor_max;
                            break;
                        case '<=':
                            dentroDelRango = valor <= rango.valor_max;
                            break;
                        case '>':
                            dentroDelRango = valor > rango.valor_min;
                            break;
                        case '>=':
                            dentroDelRango = valor >= rango.valor_min;
                            break;
                        case '==':
                            dentroDelRango = valor == rango.valor_min;
                            break;
                    }
                } else {
                    // Sin operador, usar rango min-max
                    const min = rango.valor_min !== null ? rango.valor_min : -Infinity;
                    const max = rango.valor_max !== null ? rango.valor_max : Infinity;
                    dentroDelRango = valor >= min && valor <= max;
                }

                if (dentroDelRango) {
                    categoriaEncontrada = rango;
                    break;
                }
            }

            if (categoriaEncontrada) {
                categoriaBadge.textContent = categoriaEncontrada.categoria;

                // Determinar color según categoría
                const catLower = categoriaEncontrada.categoria.toLowerCase();
                if (catLower.includes('óptimo') || catLower.includes('optimo') || catLower.includes('normal')) {
                    categoriaBadge.className = 'badge bg-success categoria-badge';
                } else if (catLower.includes('alto') || catLower.includes('elevado') || catLower.includes('intermedio')) {
                    categoriaBadge.className = 'badge bg-warning text-dark categoria-badge';
                } else if (catLower.includes('muy alto') || catLower.includes('crítico') || catLower.includes('critico')) {
                    categoriaBadge.className = 'badge bg-danger categoria-badge';
                } else if (catLower.includes('bajo') || catLower.includes('límite') || catLower.includes('limite')) {
                    categoriaBadge.className = 'badge bg-info categoria-badge';
                } else {
                    categoriaBadge.className = 'badge bg-primary categoria-badge';
                }
            } else {
                categoriaBadge.textContent = 'Fuera de rango';
                categoriaBadge.className = 'badge bg-dark categoria-badge';
            }
        });

        // Evaluar al cargar si ya hay valor
        if (input.value) {
            input.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endpush
