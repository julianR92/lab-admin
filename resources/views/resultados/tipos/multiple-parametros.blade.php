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

    // Mapeo de códigos a inputs
    const codigoAInput = {!! json_encode($servicioExamen->examen->parametros->where('es_calculado', false)->pluck('codigo_parametro')->mapWithKeys(function($codigo) use ($servicioExamen) {
        $param = $servicioExamen->examen->parametros->firstWhere('codigo_parametro', $codigo);
        return [$codigo => "input[name='resultados[" . $param->id . "][valor]']"];
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
        formulasCalculo.forEach(formula => {
            if (!formula.formula || !formula.parametros || formula.parametros.length === 0) {
                return;
            }

            // Obtener valores de los parámetros necesarios
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
                if (!input || !input.value || input.value.trim() === '') {
                    todosTienenValor = false;
                    return;
                }

                valores[codigo] = parseFloat(input.value);
            });

            // Si faltan valores, limpiar el campo calculado
            const inputCalculado = document.querySelector(formula.input_selector);
            if (!todosTienenValor) {
                if (inputCalculado) {
                    inputCalculado.value = '';
                    inputCalculado.style.backgroundColor = '#fff3cd'; // Amarillo de advertencia
                }
                return;
            }

            // Reemplazar códigos en la fórmula
            let expresion = formula.formula;
            Object.keys(valores).forEach(codigo => {
                expresion = expresion.replace(new RegExp(codigo, 'g'), valores[codigo]);
            });

            try {
                // Evaluar expresión de forma segura
                const resultado = evaluarExpresion(expresion);

                if (inputCalculado && !isNaN(resultado)) {
                    inputCalculado.value = resultado.toFixed(4);
                    inputCalculado.style.backgroundColor = '#d1e7dd'; // Verde de éxito

                    // Resetear color después de 2 segundos
                    setTimeout(() => {
                        inputCalculado.style.backgroundColor = '#e9ecef';
                    }, 2000);
                }
            } catch (error) {
                console.error('Error al calcular fórmula:', error);
                if (inputCalculado) {
                    inputCalculado.value = 'ERROR';
                    inputCalculado.style.backgroundColor = '#f8d7da'; // Rojo de error
                }
            }
        });
    }

    // Evaluador de expresiones matemáticas seguro
    function evaluarExpresion(expresion) {
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
