{{-- Vista para captura CUALITATIVO_MULTIPLE_OPCIONES (Uroanalisis, Coprologico, Frotis) --}}

@if ($servicioExamen->examen->parametros->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>No hay parámetros configurados para este examen.</strong>
        <p class="mb-0">Debe configurar los parámetros del examen antes de capturar resultados.</p>
    </div>
@elseif ($parametrosAgrupados->count() > 1 || $parametrosAgrupados->keys()->first() !== '')
    {{-- Mostrar accordion si hay secciones --}}
    <div class="accordion" id="accordionParametros">
        @foreach ($parametrosAgrupados as $seccion => $parametros)
            @php
                $seccionId = str_replace(' ', '_', strtolower($seccion ?? 'general'));
            @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $seccionId }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $seccionId }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="collapse-{{ $seccionId }}">
                        <strong>{{ $seccion ?: 'General' }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $parametros->count() }} parámetros</span>
                    </button>
                </h2>
                <div id="collapse-{{ $seccionId }}"
                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                     aria-labelledby="heading-{{ $seccionId }}"
                     data-bs-parent="#accordionParametros">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="30%">Parámetro</th>
                                        <th width="50%">Valor</th>
                                        <th width="20%">Valor Normal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parametros as $parametro)
                                    @php
                                        $resultadoExistente = $servicioExamen->resultados->where('parametro_id', $parametro->id)->first();
                                        $valorReferencia = $parametro->valorReferenciaAplicable;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $parametro->nombre_parametro }}</strong>
                                            @if ($parametro->requerido)
                                                <span class="text-danger">*</span>
                                            @endif
                                            @if ($parametro->descripcion)
                                                <br><small class="text-muted">{{ $parametro->descripcion }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($parametro->opciones_select && is_array($parametro->opciones_select))
                                                {{-- SELECT con opciones predefinidas --}}
                                                <select name="resultados[{{ $parametro->id }}][valor]"
                                                        class="form-select form-select-sm"
                                                        {{ $parametro->requerido ? 'required' : '' }}>
                                                    <option value="">-- Seleccione --</option>
                                                    @foreach ($parametro->opciones_select as $opcion)
                                                        <option value="{{ $opcion }}"
                                                                {{ $resultadoExistente && ($resultadoExistente->valor_texto == $opcion || $resultadoExistente->valor_cualitativo == $opcion) ? 'selected' : '' }}
                                                                {{ !$resultadoExistente && $parametro->valor_defecto == $opcion ? 'selected' : '' }}>
                                                            {{ $opcion }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif ($parametro->tipo_dato === 'TEXTO_LARGO')
                                                {{-- TEXTAREA para texto extenso --}}
                                                <textarea name="resultados[{{ $parametro->id }}][valor]"
                                                          class="form-control form-control-sm"
                                                          rows="2"
                                                          placeholder="Escriba su observación..."
                                                          {{ $parametro->requerido ? 'required' : '' }}>{{ $resultadoExistente ? ($resultadoExistente->valor_texto ?? '') : ($parametro->valor_defecto ?? '') }}</textarea>
                                            @elseif (in_array($parametro->tipo_dato, ['DECIMAL', 'INTEGER']))
                                                {{-- INPUT NUMERICO (ej: pH) --}}
                                                <input type="number"
                                                       name="resultados[{{ $parametro->id }}][valor]"
                                                       class="form-control form-control-sm"
                                                       step="{{ $parametro->tipo_dato === 'DECIMAL' ? '0.1' : '1' }}"
                                                       value="{{ $resultadoExistente ? $resultadoExistente->valor_numerico : ($parametro->valor_defecto ?? '') }}"
                                                       {{ $parametro->requerido ? 'required' : '' }}>
                                            @else
                                                {{-- INPUT TEXTO libre --}}
                                                <input type="text"
                                                       name="resultados[{{ $parametro->id }}][valor]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $resultadoExistente ? ($resultadoExistente->valor_texto ?? $resultadoExistente->valor_cualitativo ?? '') : ($parametro->valor_defecto ?? '') }}"
                                                       placeholder="Escriba el valor..."
                                                       {{ $parametro->requerido ? 'required' : '' }}>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                @if ($valorReferencia && $valorReferencia->tipo_referencia === 'CUALITATIVO')
                                                    <span class="badge bg-success">{{ $valorReferencia->valor_cualitativo }}</span>
                                                @elseif ($parametro->valor_defecto)
                                                    <span class="badge bg-secondary">{{ $parametro->valor_defecto }}</span>
                                                @else
                                                    -
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
@else
    {{-- Tabla simple sin secciones --}}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="30%">Parámetro</th>
                    <th width="50%">Valor</th>
                    <th width="20%">Valor Normal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicioExamen->examen->parametros as $parametro)
                    @php
                        $resultadoExistente = $servicioExamen->resultados->where('parametro_id', $parametro->id)->first();
                        $valorReferencia = $parametro->valorReferenciaAplicable;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $parametro->nombre_parametro }}</strong>
                            @if ($parametro->requerido)
                                <span class="text-danger">*</span>
                            @endif
                        </td>
                        <td>
                            @if ($parametro->opciones_select && is_array($parametro->opciones_select))
                                <select name="resultados[{{ $parametro->id }}][valor]"
                                        class="form-select form-select-sm"
                                        {{ $parametro->requerido ? 'required' : '' }}>
                                    <option value="">-- Seleccione --</option>
                                    @foreach ($parametro->opciones_select as $opcion)
                                        <option value="{{ $opcion }}"
                                                {{ $resultadoExistente && ($resultadoExistente->valor_texto == $opcion || $resultadoExistente->valor_cualitativo == $opcion) ? 'selected' : '' }}
                                                {{ !$resultadoExistente && $parametro->valor_defecto == $opcion ? 'selected' : '' }}>
                                            {{ $opcion }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif ($parametro->tipo_dato === 'TEXTO_LARGO')
                                <textarea name="resultados[{{ $parametro->id }}][valor]"
                                          class="form-control form-control-sm"
                                          rows="2"
                                          {{ $parametro->requerido ? 'required' : '' }}>{{ $resultadoExistente ? ($resultadoExistente->valor_texto ?? '') : ($parametro->valor_defecto ?? '') }}</textarea>
                            @elseif (in_array($parametro->tipo_dato, ['DECIMAL', 'INTEGER']))
                                <input type="number"
                                       name="resultados[{{ $parametro->id }}][valor]"
                                       class="form-control form-control-sm"
                                       step="{{ $parametro->tipo_dato === 'DECIMAL' ? '0.1' : '1' }}"
                                       value="{{ $resultadoExistente ? $resultadoExistente->valor_numerico : ($parametro->valor_defecto ?? '') }}"
                                       {{ $parametro->requerido ? 'required' : '' }}>
                            @else
                                <input type="text"
                                       name="resultados[{{ $parametro->id }}][valor]"
                                       class="form-control form-control-sm"
                                       value="{{ $resultadoExistente ? ($resultadoExistente->valor_texto ?? $resultadoExistente->valor_cualitativo ?? '') : ($parametro->valor_defecto ?? '') }}"
                                       {{ $parametro->requerido ? 'required' : '' }}>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                @if ($valorReferencia && $valorReferencia->tipo_referencia === 'CUALITATIVO')
                                    <span class="badge bg-success">{{ $valorReferencia->valor_cualitativo }}</span>
                                @elseif ($parametro->valor_defecto)
                                    <span class="badge bg-secondary">{{ $parametro->valor_defecto }}</span>
                                @else
                                    -
                                @endif
                            </small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
