{{-- Vista para exámenes descriptivos --}}
@php
    $resultadoExistente = $servicioExamen->resultados->first();
    // Para exámenes descriptivos, usar el primer parámetro o crear uno ficticio
    $parametroId = $servicioExamen->examen->parametros->first()?->id ?? 'descriptivo';
@endphp

<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Nota:</strong> Los resultados descriptivos siempre requieren revisión y validación profesional.
</div>

@if ($servicioExamen->examen->parametros->count() > 0)
    {{-- Campos estructurados si existen --}}
    <div class="row mb-4">
        @foreach ($servicioExamen->examen->parametros as $parametro)
            @php
                $resultadoParam = $servicioExamen->resultados->where('parametro_id', $parametro->id)->first();
            @endphp
            @if ($parametro->tipo_dato === 'SELECT' && $parametro->opciones_select)
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <strong>{{ $parametro->nombre_parametro }}</strong>
                        @if ($parametro->requerido)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <select name="resultados[{{ $parametro->id }}][valor]"
                            class="form-select"
                            {{ $parametro->requerido ? 'required' : '' }}>
                        <option value="">-- Seleccione --</option>
                        @foreach ($parametro->opciones_select as $opcion)
                            <option value="{{ $opcion }}" {{ $resultadoParam && $resultadoParam->valor_cualitativo == $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif (in_array($parametro->tipo_dato, ['TEXT', 'TEXTO']))
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <strong>{{ $parametro->nombre_parametro }}</strong>
                        @if ($parametro->requerido)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text"
                           name="resultados[{{ $parametro->id }}][valor]"
                           class="form-control"
                           value="{{ $resultadoParam ? $resultadoParam->valor_texto : '' }}"
                           {{ $parametro->requerido ? 'required' : '' }}>
                </div>
            @endif
        @endforeach
    </div>
@endif

{{-- Áreas de texto para descripción --}}
<input type="hidden" name="resultados[{{ $parametroId }}][tipo]" value="TEXTO_DESCRIPTIVO">

<div class="row">
    {{-- <div class="col-12 mb-3">
        <label class="form-label">
            <strong><i class="fas fa-eye me-2"></i>Observaciones</strong>
            <small class="text-muted">(Descripción de lo observado, características de la muestra)</small>
        </label>
        <textarea name="resultados[{{ $parametroId }}][observaciones]"
                  class="form-control"
                  rows="4"
                  placeholder="Describa las características observadas en la muestra...">{{ $resultadoExistente ? $resultadoExistente->observaciones : '' }}</textarea>
    </div> --}}

    <div class="col-12 mb-3">
        <label class="form-label">
            <strong><i class="fas fa-microscope me-2"></i>Interpretación</strong>
            <small class="text-muted">(Hallazgos significativos, descripción microscópica detallada)</small>
            <span class="text-danger">*</span>
        </label>
        <textarea name="resultados[{{ $parametroId }}][interpretacion]"
                  class="form-control"
                  rows="6"
                  placeholder="Describa los hallazgos microscópicos y su interpretación..."
                  required>{{ $resultadoExistente ? $resultadoExistente->interpretacion : '' }}</textarea>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">
            <strong><i class="fas fa-check-circle me-2"></i>Conclusiones</strong>
            <small class="text-muted">(Resultado final, recomendaciones)</small>
            <span class="text-danger">*</span>
        </label>
        <textarea name="resultados[{{ $parametroId }}][conclusiones]"
                  class="form-control"
                  rows="4"
                  placeholder="Escriba las conclusiones y recomendaciones finales..."
                  required>{{ $resultadoExistente ? $resultadoExistente->conclusiones : '' }}</textarea>
    </div>
</div>
