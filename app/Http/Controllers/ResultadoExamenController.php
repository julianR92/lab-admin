<?php

namespace App\Http\Controllers;

use App\Models\ExamenParametro;
use App\Models\ExamenValorReferencia;
use App\Models\ResultadoExamen;
use App\Models\ServicioExamen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultadoExamenController extends Controller
{
    /**
     * Mostrar formulario de captura de resultados
     */
    public function create(ServicioExamen $servicioExamen)
    {
        // Validar que haya profesional asignado
        if (! $servicioExamen->profesional_id) {
            return back()->with('error', 'No se puede capturar resultados. Debe asignar un profesional al examen primero.');
        }

        // Validar estado del examen
        if (! in_array($servicioExamen->estado, ['EN_PROCESO', 'COMPLETADO'])) {
            if ($servicioExamen->estado === 'PENDIENTE') {
                // Cambiar automáticamente a EN_PROCESO
                $servicioExamen->update(['estado' => 'EN_PROCESO']);
            } elseif ($servicioExamen->estado === 'ENTREGADO') {
                return back()->with('error', 'No se puede modificar un examen ya entregado.');
            }
        }

        // Cargar relaciones necesarias
        $servicioExamen->load([
            'examen.parametros' => function ($query) {
                $query->where('status', true)->orderBy('orden');
            },
            'servicio.cliente',
            'profesional',
            'resultados.parametro',
        ]);

        // Obtener contexto del paciente
        $contexto = [
            'genero' => $servicioExamen->servicio->cliente->genero,
            'edad' => $servicioExamen->servicio->cliente->edad,
        ];

        // Cargar valores de referencia aplicables
        foreach ($servicioExamen->examen->parametros as $parametro) {
            $parametro->valorReferenciaAplicable = $this->obtenerValorReferenciaAplicable($parametro, $contexto);
        }

        // Agrupar parámetros por sección si existe
        $parametrosAgrupados = $servicioExamen->examen->parametros->groupBy('seccion');

        // return [
        //     'servicioExamen' => $servicioExamen,
        //     'contexto' => $contexto,
        //     'parametrosAgrupados' => $parametrosAgrupados  ];

        return view('resultados.create', compact('servicioExamen', 'contexto', 'parametrosAgrupados'));
    }

    /**
     * Guardar resultados capturados
     */
    public function store(Request $request, ServicioExamen $servicioExamen)
    {
        // Validar que haya profesional asignado
        if (! $servicioExamen->profesional_id) {
            return response()->json([
                'success' => false,
                'error' => 'PROFESIONAL_NO_ASIGNADO',
                'message' => 'No se puede capturar resultados. Debe asignar un profesional al examen primero.',
            ], 422);
        }

        // Validar estado
        if (! in_array($servicioExamen->estado, ['EN_PROCESO', 'COMPLETADO'])) {
            return response()->json([
                'success' => false,
                'message' => 'El examen debe estar EN_PROCESO para capturar resultados.',
            ], 422);
        }

        // Validar datos de entrada
        $resultados = $request->input('resultados', []);


        // Log para debugging
        Log::info('Iniciando guardado de resultados', [
            'servicio_examen_id' => $servicioExamen->id,
            'examen' => $servicioExamen->examen->nombre,
            'tipo_resultado' => $servicioExamen->examen->tipo_resultado,
            'total_parametros_enviados' => count($resultados),
        ]);

        // Validar que se envíen resultados
        if (empty($resultados) && $servicioExamen->examen->tipo_resultado !== 'TEXTO_DESCRIPTIVO') {
            return response()->json([
                'success' => false,
                'message' => 'No se recibieron resultados para guardar. Por favor complete al menos un parámetro.',
            ], 422);
        }

        // Cargar parámetros del examen con sus configuraciones
        $parametrosExamen = $servicioExamen->examen->parametros()
            ->where('status', true)
            ->get();

        // Validar campos requeridos
        $erroresValidacion = [];
        foreach ($parametrosExamen as $parametro) {
            if ($parametro->requerido && !$parametro->es_calculado) {
                $valorEnviado = $resultados[$parametro->id]['valor'] ?? null;

                if (is_null($valorEnviado) || $valorEnviado === '') {
                    $erroresValidacion[] = "El campo '{$parametro->nombre_parametro}' es obligatorio.";
                }
            }
        }

        if (!empty($erroresValidacion)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor complete todos los campos obligatorios marcados con *',
                'errors' => $erroresValidacion,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $warnings = [];
            $contexto = [
                'genero' => $servicioExamen->servicio->cliente->genero,
                'edad' => $servicioExamen->servicio->cliente->edad,
            ];

            $contadorGuardados = 0;

            foreach ($resultados as $parametroId => $data) {
                // Manejo especial para TEXTO_DESCRIPTIVO
                if (isset($data['tipo']) && $data['tipo'] === 'TEXTO_DESCRIPTIVO') {
                    // Si el parámetro es 'descriptivo', usar el primer parámetro del examen o crear resultado único
                    if ($parametroId === 'descriptivo') {
                        $primerParametro = $servicioExamen->examen->parametros->first();
                        if (! $primerParametro) {
                            // Crear parámetro temporal si no existe
                            $primerParametro = ExamenParametro::create([
                                'examen_id' => $servicioExamen->examen_id,
                                'nombre_parametro' => 'Descripción',
                                'codigo_parametro' => 'DESC',
                                'tipo_dato' => 'TEXT',
                                'orden' => 1,
                                'requerido' => true,
                                'status' => true,
                            ]);
                        }
                        $parametroId = $primerParametro->id;
                    }

                    // Guardar campos descriptivos
                    $resultado = ResultadoExamen::firstOrNew([
                        'servicio_examen_id' => $servicioExamen->id,
                        'parametro_id' => $parametroId,
                    ]);

                    // Guardar el valor si existe
                    if (isset($data['valor'])) {
                        $resultado->valor_texto = $data['valor'];
                    }

                    $resultado->observaciones = $data['observaciones'] ?? null;
                    $resultado->interpretacion = $data['interpretacion'] ?? null;
                    $resultado->conclusiones = $data['conclusiones'] ?? null;
                    $resultado->capturado_por = Auth::id();
                    $resultado->save();

                    $contadorGuardados++;

                    continue;
                }

                $parametro = ExamenParametro::find($parametroId);

                if (! $parametro) {
                    Log::warning("Parámetro no encontrado: {$parametroId}");
                    continue;
                }

                if ($parametro->es_calculado) {
                    continue; // Los calculados se procesan después
                }

                try {
                    // Verificar si ya existe el resultado
                    $resultado = ResultadoExamen::firstOrNew([
                        'servicio_examen_id' => $servicioExamen->id,
                        'parametro_id' => $parametroId,
                    ]);

                    // Asignar valores según tipo de dato
                    $this->asignarValor($resultado, $parametro, $data);

                    $resultado->capturado_por = Auth::id();
                    $resultado->save();

                    $contadorGuardados++;

                    // Evaluar automáticamente
                    $resultado->evaluar($contexto);
                    $resultado->save();

                    // Verificar alertas críticas
                    if ($resultado->tipo_alerta === ResultadoExamen::ALERTA_CRITICO) {
                        $warnings[] = [
                            'tipo' => 'VALOR_CRITICO',
                            'mensaje' => "⚠ Valor crítico detectado en {$parametro->nombre_parametro}: {$resultado->valor_formateado}",
                            'parametro' => $parametro->nombre_parametro,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Error guardando parámetro {$parametro->nombre_parametro}", [
                        'parametro_id' => $parametroId,
                        'data' => $data,
                        'error' => $e->getMessage(),
                    ]);
                    throw new \Exception("Error al guardar {$parametro->nombre_parametro}: {$e->getMessage()}");
                }
            }

            // Verificar que se hayan guardado resultados
            if ($contadorGuardados === 0 && $servicioExamen->examen->tipo_resultado !== 'TEXTO_DESCRIPTIVO') {
                throw new \Exception('No se guardó ningún resultado. Verifique los datos enviados.');
            }

            // Calcular parámetros calculados
            $this->calcularParametrosCalculados($servicioExamen, $contexto);

            // Guardar observaciones profesionales en el servicio_examen
            if ($request->has('observaciones_profesional')) {
                $servicioExamen->observaciones = $request->input('observaciones_profesional');
            }

            // Actualizar fecha de resultado
            $servicioExamen->update([
                'fecha_resultado' => now(),
                'estado' => 'COMPLETADO',
            ]);

            DB::commit();

            Log::info('Resultados guardados exitosamente', [
                'servicio_examen_id' => $servicioExamen->id,
                'resultados_guardados' => $contadorGuardados,
                'warnings' => count($warnings),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Resultados guardados correctamente ({$contadorGuardados} parámetros)",
                'warnings' => $warnings,
                'redirect' => route('servicios.show', $servicioExamen->servicio_id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar resultados', [
                'servicio_examen_id' => $servicioExamen->id,
                'usuario' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar resultados: '.$e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Ver resultados capturados
     */
    public function show(ServicioExamen $servicioExamen)
    {
        $servicioExamen->load([
            'servicio',
            'examen.parametros',
            'servicio.cliente',
            'profesional',
            'resultados.parametro',
            'resultados.valorReferencia',
            'resultados.capturadoPor',
        ]);

        // Agrupar resultados por sección
        $resultadosAgrupados = $servicioExamen->resultados->groupBy(function ($resultado) {
            return $resultado->parametro->seccion ?? 'General';
        });

        return view('resultados.show', compact('servicioExamen', 'resultadosAgrupados'));
    }

    /**
     * Asignar valor al resultado según tipo de dato
     */
    private function asignarValor(ResultadoExamen $resultado, ExamenParametro $parametro, $data)
    {
        // Log para debugging
        Log::debug("Asignando valor a parámetro", [
            'parametro' => $parametro->nombre_parametro,
            'tipo_dato' => $parametro->tipo_dato,
            'valor_recibido' => $data['valor'] ?? null,
            'tiene_opciones' => !empty($parametro->opciones_select),
        ]);

        switch ($parametro->tipo_dato) {
            case 'DECIMAL':
            case 'INTEGER':
                $resultado->valor_numerico = $data['valor'] ?? null;
                $resultado->unidad_medida = $parametro->unidad_medida;
                break;

            case 'SELECT':
            case 'CUALITATIVO':
                $resultado->valor_cualitativo = $data['valor'] ?? null;
                break;

            case 'TEXT':
                // Si tiene opciones_select, es un SELECT disfrazado de TEXTO
                $opcionesSelect = $parametro->opciones_select;

                // Convertir JSON string a array si es necesario
                if (is_string($opcionesSelect)) {
                    $opcionesSelect = json_decode($opcionesSelect, true);
                }

                if ($opcionesSelect && is_array($opcionesSelect) && !empty($opcionesSelect)) {
                    $resultado->valor_cualitativo = $data['valor'] ?? null;
                } else {
                    $resultado->valor_texto = $data['valor'] ?? null;
                }
                break;

            case 'TEXTO_LARGO':
                if (isset($data['observaciones'])) {
                    $resultado->observaciones = $data['observaciones'];
                }
                if (isset($data['interpretacion'])) {
                    $resultado->interpretacion = $data['interpretacion'];
                }
                if (isset($data['conclusiones'])) {
                    $resultado->conclusiones = $data['conclusiones'];
                }
                $resultado->requiere_revision = true;
                break;

            case 'FECHA':
                $resultado->valor_fecha = $data['valor'] ?? null;
                break;

            case 'HORA':
                $resultado->valor_hora = $data['valor'] ?? null;
                break;

            default:
                // Por defecto, guardar como texto
                Log::warning("Tipo de dato no reconocido: {$parametro->tipo_dato}, guardando como texto");
                $resultado->valor_texto = $data['valor'] ?? null;
                break;
        }
    }

    /**
     * Calcular parámetros calculados
     */
    private function calcularParametrosCalculados(ServicioExamen $servicioExamen, array $contexto)
    {
        $parametrosCalculados = $servicioExamen->examen->parametros()
            ->where('es_calculado', true)
            ->where('status', true)
            ->get();

        foreach ($parametrosCalculados as $parametro) {
            if (! $parametro->formula_calculo) {
                continue;
            }

            $formula = $parametro->formula_calculo['formula'] ?? null;
            $parametrosNecesarios = $parametro->formula_calculo['parametros'] ?? [];

            if (! $formula || empty($parametrosNecesarios)) {
                continue;
            }

            // Obtener valores de los parámetros necesarios
            $valores = [];
            foreach ($parametrosNecesarios as $codigo) {
                $resultado = ResultadoExamen::whereHas('parametro', function ($q) use ($codigo) {
                    $q->where('codigo_parametro', $codigo);
                })
                    ->where('servicio_examen_id', $servicioExamen->id)
                    ->first();

                if (! $resultado || $resultado->valor_numerico === null) {
                    continue 2; // Saltar este parámetro calculado
                }

                $valores[$codigo] = $resultado->valor_numerico;
            }

            // Reemplazar en la fórmula
            $expresion = $formula;
            foreach ($valores as $codigo => $valor) {
                $expresion = str_replace($codigo, $valor, $expresion);
            }

            try {
                // Evaluar expresión (usar eval con cuidado, mejor usar una librería)
                $valorCalculado = eval("return {$expresion};");

                // Guardar resultado calculado
                $resultado = ResultadoExamen::firstOrNew([
                    'servicio_examen_id' => $servicioExamen->id,
                    'parametro_id' => $parametro->id,
                ]);

                $resultado->valor_numerico = round($valorCalculado, $parametro->decimales ?? 2);
                $resultado->unidad_medida = $parametro->unidad_medida;
                $resultado->capturado_por = Auth::id();
                $resultado->save();

                // Evaluar
                $resultado->evaluar($contexto);
                $resultado->save();

            } catch (\Exception $e) {
                // Log error pero continuar
                Log::error("Error calculando parámetro {$parametro->nombre_parametro}: ".$e->getMessage());
            }
        }
    }

    /**
     * Obtener valor de referencia aplicable
     */
    private function obtenerValorReferenciaAplicable(ExamenParametro $parametro, array $contexto)
    {
        $query = ExamenValorReferencia::where('parametro_id', $parametro->id)
            ->where('status', true);

        // Filtrar por género
        if (isset($contexto['genero'])) {
            $query->where(function ($q) use ($contexto) {
                $q->whereNull('genero')
                    ->orWhere('genero', $contexto['genero']);
            });
        }

        // Filtrar por edad
        if (isset($contexto['edad'])) {
            $query->where(function ($q) use ($contexto) {
                $q->where(function ($subQ) use ($contexto) {
                    $subQ->whereNull('edad_min')
                        ->orWhere('edad_min', '<=', $contexto['edad']);
                })
                    ->where(function ($subQ) use ($contexto) {
                        $subQ->whereNull('edad_max')
                            ->orWhere('edad_max', '>=', $contexto['edad']);
                    });
            });
        }

        return $query->orderBy('orden')->first();
    }
}
