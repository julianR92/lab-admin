{{-- Vista para captura de múltiples parámetros (agrupados o no) --}}

{{-- Botón para calcular parámetros calculados --}}
@php
    $tieneCalculados = $servicioExamen->examen->parametros->where('es_calculado', true)->count() > 0;
@endphp

@if ($tieneCalculados)
    <div class="alert alert-info mb-3">
        <i class="fas fa-calculator me-2"></i>
        <strong>Este examen tiene parámetros calculados.</strong>
        Ingrese los valores manuales y haga clic en el botón para calcular automáticamente.
        <button type="button" class="btn btn-sm btn-primary ms-3" id="btnCalcularParametros">
            <i class="fas fa-calculator me-1"></i>Calcular Ahora
        </button>
    </div>
@endif

@if ($parametrosAgrupados->count() > 1 && $parametrosAgrupados->keys()->first() !== '')
    {{-- Parámetros agrupados por sección --}}
    @foreach ($parametrosAgrupados as $seccion => $parametros)
        <div class="mb-4">
            <h5 class="text-primary border-bottom pb-2">
                <i class="fas fa-folder me-2"></i>{{ $seccion ?: 'General' }}
            </h5>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th width="30%">Parámetro</th>
                            <th width="20%">Valor</th>
                            <th width="15%">Unidad</th>
                            <th width="25%">Rango Referencia</th>
                            <th width="10%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parametros as $parametro)
                            @include('resultados.tipos.partials.fila-parametro', ['parametro' => $parametro])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@else
    {{-- Parámetros sin agrupar --}}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="30%">Parámetro</th>
                    <th width="20%">Valor</th>
                    <th width="15%">Unidad</th>
                    <th width="25%">Rango Referencia</th>
                    <th width="10%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicioExamen->examen->parametros as $parametro)
                    @include('resultados.tipos.partials.fila-parametro', ['parametro' => $parametro])
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if ($tieneCalculados)
    @push('scripts')
    <script>
    // Configuración de fórmulas de cálculo
    const formulasCalculo = {!! json_encode($servicioExamen->examen->parametros->where('es_calculado', true)->map(function($p) {
        return [
            'parametro_id' => $p->id,
            'codigo' => $p->codigo_parametro,
            'formula' => $p->formula_calculo['formula'] ?? null,
            'parametros' => $p->formula_calculo['parametros'] ?? [],
            'input_selector' => "input[name='resultados[" . $p->id . "][valor]']"
        ];
    })->values()) !!};

    // Mapeo de códigos a inputs (manuales + calculados, para soportar fórmulas encadenadas)
    const codigoAInput = {!! json_encode($servicioExamen->examen->parametros->mapWithKeys(function($p) {
        return [$p->codigo_parametro => "input[name='resultados[" . $p->id . "][valor]']"];
    })) !!};

    document.addEventListener('DOMContentLoaded', function() {
        const btnCalcular = document.getElementById('btnCalcularParametros');

        if (btnCalcular) {
            btnCalcular.addEventListener('click', function() {
                calcularParametros();
            });

            // Calcular automáticamente cuando cambian los valores
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('change', function() {
                    calcularParametros();
                });
            });
        }
    });

    function calcularParametros() {
        // Resolución iterativa: en cada pasada se calculan los parámetros
        // cuyas dependencias ya tienen valor. Si una fórmula depende de otra
        // calculada, en la pasada siguiente ya la encontrará lista.
        const MAX_PASADAS = formulasCalculo.length + 1;
        const resueltosEnSesion = new Set();
        let cambioEnPasada = true;
        let pasadas = 0;

        while (cambioEnPasada && pasadas < MAX_PASADAS) {
            cambioEnPasada = false;
            pasadas++;

            formulasCalculo.forEach(formula => {
                if (!formula.formula || !formula.parametros || formula.parametros.length === 0) {
                    return;
                }
                if (resueltosEnSesion.has(formula.codigo)) {
                    return;
                }

                const inputCalculado = document.querySelector(formula.input_selector);
                if (!inputCalculado) return;

                const valores = {};
                let todosTienenValor = true;

                formula.parametros.forEach(codigo => {
                    const inputSelector = codigoAInput[codigo];
                    if (!inputSelector) {
                        console.warn(`No se encontró input para código: ${codigo}`);
                        todosTienenValor = false;
                        return;
                    }

                    const input = document.querySelector(inputSelector);
                    if (!input || !input.value || input.value.trim() === '' || isNaN(parseFloat(input.value))) {
                        todosTienenValor = false;
                        return;
                    }

                    valores[codigo] = parseFloat(input.value);
                });

                if (!todosTienenValor) {
                    // Solo limpiar al final del proceso, para no borrar resultados
                    // intermedios que esperan ser usados por otra fórmula.
                    return;
                }

                // Reemplazar códigos en la fórmula
                let expresion = formula.formula;
                Object.keys(valores).forEach(codigo => {
                    const regex = new RegExp(`\\{${codigo}\\}`, 'g');
                    expresion = expresion.replace(regex, valores[codigo]);
                });

                try {
                    const resultado = evaluarExpresion(expresion);

                    if (!isNaN(resultado)) {
                        const nuevoValor = resultado.toFixed(4);
                        if (inputCalculado.value !== nuevoValor) {
                            inputCalculado.value = nuevoValor;
                            inputCalculado.style.backgroundColor = '#d1e7dd';
                            cambioEnPasada = true;
                        }
                        resueltosEnSesion.add(formula.codigo);
                    }
                } catch (error) {
                    console.error('Error al calcular fórmula:', error);
                    inputCalculado.value = 'ERROR';
                    inputCalculado.style.backgroundColor = '#f8d7da';
                    resueltosEnSesion.add(formula.codigo); // no reintentar
                }
            });
        }

        // Pasada final: marcar en amarillo los calculados que nunca se resolvieron
        // (dependencias incompletas o ciclo). Resetear color de los que sí se resolvieron.
        formulasCalculo.forEach(formula => {
            const inputCalculado = document.querySelector(formula.input_selector);
            if (!inputCalculado) return;

            if (!resueltosEnSesion.has(formula.codigo)) {
                if (inputCalculado.value !== '' && inputCalculado.value !== 'ERROR') {
                    // El valor ya estaba antes (resultado guardado previamente); no borrar.
                    return;
                }
                inputCalculado.value = '';
                inputCalculado.style.backgroundColor = '#fff3cd';
            } else {
                setTimeout(() => {
                    inputCalculado.style.backgroundColor = '#e9ecef';
                }, 2000);
            }
        });
    }

    // Evaluador de expresiones matemáticas seguro
    function evaluarExpresion(expresion) {
        console.log('Expresión a evaluar:', expresion);
        // Eliminar espacios
        expresion = expresion.replace(/\s/g, '');

        // Validar que solo contenga números y operadores permitidos
        if (!/^[0-9+\-*/().]+$/.test(expresion)) {
            throw new Error('Expresión inválida');
        }

        // Evaluar usando Function (más seguro que eval)
        return Function('"use strict"; return (' + expresion + ')')();
    }
    </script>
    @endpush
@endif
