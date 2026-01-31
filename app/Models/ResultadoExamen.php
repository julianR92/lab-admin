<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ResultadoExamen extends Model
{
    use HasFactory;

    protected $table = 'resultados_examen';

    protected $fillable = [
        'servicio_examen_id',
        'parametro_id',
        // Valores capturados
        'valor_numerico',
        'valor_texto',
        'valor_cualitativo',
        'valor_fecha',
        'valor_hora',
        'unidad_medida',
        // Evaluación automática
        'fuera_rango',
        'tipo_alerta',
        'categoria_asignada',
        // Valores de referencia
        'valor_referencia_id',
        'rango_referencia',
        // Interpretación profesional
        'observaciones',
        'interpretacion',
        'conclusiones',
        // Control de calidad
        'requiere_revision',
        'revisado_por',
        'fecha_revision',
        'comentario_revision',
        // Validación
        'validado_por',
        'fecha_validacion',
        // Auditoría
        'capturado_por',
    ];

    protected $casts = [
        'valor_numerico' => 'decimal:4',
        'valor_fecha' => 'date',
        'fuera_rango' => 'boolean',
        'requiere_revision' => 'boolean',
        'fecha_revision' => 'datetime',
        'fecha_validacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ========================================
     * CONSTANTES
     * ========================================
     */
    public const ALERTA_NORMAL = 'NORMAL';

    public const ALERTA_BAJO = 'BAJO';

    public const ALERTA_ALTO = 'ALTO';

    public const ALERTA_CRITICO = 'CRITICO';

    /**
     * ========================================
     * RELACIONES
     * ========================================
     */
    public function servicioExamen(): BelongsTo
    {
        return $this->belongsTo(ServicioExamen::class, 'servicio_examen_id');
    }

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(ExamenParametro::class, 'parametro_id');
    }

    public function valorReferencia(): BelongsTo
    {
        return $this->belongsTo(ExamenValorReferencia::class, 'valor_referencia_id');
    }

    public function capturadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'capturado_por');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function validadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */
    public function scopeFueraDeRango($query)
    {
        return $query->where('fuera_rango', true);
    }

    public function scopePorAlerta($query, $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
    }

    public function scopeCriticos($query)
    {
        return $query->where('tipo_alerta', self::ALERTA_CRITICO);
    }

    public function scopeAnomalos($query)
    {
        return $query->whereIn('tipo_alerta', [self::ALERTA_ALTO, self::ALERTA_BAJO, self::ALERTA_CRITICO]);
    }

    public function scopePendientesRevision($query)
    {
        return $query->where('requiere_revision', true)
            ->whereNull('revisado_por');
    }

    public function scopeRevisados($query)
    {
        return $query->whereNotNull('revisado_por');
    }

    public function scopeValidados($query)
    {
        return $query->whereNotNull('validado_por');
    }

    public function scopePorServicioExamen($query, $servicioExamenId)
    {
        return $query->where('servicio_examen_id', $servicioExamenId);
    }

    /**
     * ========================================
     * ACCESSORS (Getters)
     * ========================================
     */

    /**
     * Obtener el valor principal según el tipo de parámetro
     */
    public function getValorPrincipalAttribute()
    {
        if ($this->valor_numerico !== null) {
            return $this->valor_numerico;
        }

        if ($this->valor_cualitativo !== null) {
            return $this->valor_cualitativo;
        }

        if ($this->valor_texto !== null) {
            return $this->valor_texto;
        }

        if ($this->valor_fecha !== null) {
            return $this->valor_fecha->format('d/m/Y');
        }

        if ($this->valor_hora !== null) {
            return $this->valor_hora;
        }

        return null;
    }

    /**
     * Formatear el valor según el tipo de parámetro
     */
    public function getValorFormateadoAttribute(): string
    {
        $parametro = $this->parametro;

        // Valores numéricos
        if ($this->valor_numerico !== null) {
            $decimales = $parametro ? ($parametro->decimales ?? 2) : 2;
            $valor = number_format($this->valor_numerico, $decimales, '.', ',');

            // Agregar unidad de medida
            $unidad = $this->unidad_medida ?? ($parametro ? $parametro->unidad_medida : null);
            if ($unidad) {
                $valor .= " {$unidad}";
            }

            // Agregar indicador de alerta
            if ($this->fuera_rango) {
                $valor .= ' '.$this->icono_alerta;
            }

            return $valor;
        }

        // Valores cualitativos
        if ($this->valor_cualitativo) {
            return $this->valor_cualitativo;
        }

        // Valores de texto
        if ($this->valor_texto) {
            return $this->valor_texto;
        }

        // Valores de fecha
        if ($this->valor_fecha) {
            return $this->valor_fecha->format('d/m/Y');
        }

        // Valores de hora
        if ($this->valor_hora) {
            return date('H:i', strtotime($this->valor_hora));
        }

        return 'N/A';
    }

    /**
     * Obtener ícono de alerta
     */
    public function getIconoAlertaAttribute(): string
    {
        return match ($this->tipo_alerta) {
            self::ALERTA_NORMAL => '✓',
            self::ALERTA_BAJO => '↓',
            self::ALERTA_ALTO => '↑',
            self::ALERTA_CRITICO => '⚠',
            default => '',
        };
    }

    /**
     * Obtener color de alerta para UI
     */
    public function getColorAlertaAttribute(): string
    {
        return match ($this->tipo_alerta) {
            self::ALERTA_NORMAL => 'success',
            self::ALERTA_BAJO => 'info',
            self::ALERTA_ALTO => 'warning',
            self::ALERTA_CRITICO => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Obtener clase CSS de Bootstrap según alerta
     */
    public function getClaseBootstrapAttribute(): string
    {
        return match ($this->tipo_alerta) {
            self::ALERTA_NORMAL => 'text-success',
            self::ALERTA_BAJO => 'text-info',
            self::ALERTA_ALTO => 'text-warning',
            self::ALERTA_CRITICO => 'text-danger fw-bold',
            default => 'text-secondary',
        };
    }

    /**
     * Verificar si está revisado
     */
    public function getEstaRevisadoAttribute(): bool
    {
        return $this->revisado_por !== null;
    }

    /**
     * Verificar si está validado
     */
    public function getEstaValidadoAttribute(): bool
    {
        return $this->validado_por !== null;
    }

    /**
     * Verificar si es anómalo
     */
    public function getEsAnomaloAttribute(): bool
    {
        return in_array($this->tipo_alerta, [
            self::ALERTA_BAJO,
            self::ALERTA_ALTO,
            self::ALERTA_CRITICO,
        ]);
    }

    /**
     * Verificar si es crítico
     */
    public function getEsCriticoAttribute(): bool
    {
        return $this->tipo_alerta === self::ALERTA_CRITICO;
    }

    /**
     * Obtener texto descriptivo del resultado (para TEXTO_DESCRIPTIVO)
     */
    public function getTextoCompletoAttribute(): string
    {
        $partes = [];

        if ($this->observaciones) {
            $partes[] = 'Observaciones: '.$this->observaciones;
        }

        if ($this->interpretacion) {
            $partes[] = 'Interpretación: '.$this->interpretacion;
        }

        if ($this->conclusiones) {
            $partes[] = 'Conclusiones: '.$this->conclusiones;
        }

        return implode("\n\n", $partes) ?: 'Sin descripción';
    }

    /**
     * ========================================
     * MUTATORS (Setters)
     * ========================================
     */

    /**
     * Al establecer valor numérico, redondear según decimales del parámetro
     */
    public function setValorNumericoAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['valor_numerico'] = null;

            return;
        }

        $parametro = $this->parametro;
        $decimales = $parametro ? ($parametro->decimales ?? 2) : 2;

        $this->attributes['valor_numerico'] = round($value, $decimales);
    }

    /**
     * ========================================
     * MÉTODOS AUXILIARES
     * ========================================
     */

    /**
     * Evaluar el resultado contra valores de referencia
     */
    public function evaluar(array $contexto = []): void
    {
        $parametro = $this->parametro;

        if (! $parametro || $this->valor_principal === null) {
            return;
        }

        // Obtener valores de referencia aplicables
        $valorReferencia = $this->obtenerValorReferenciaAplicable($contexto);

        if (! $valorReferencia) {
            $this->fuera_rango = false;
            $this->tipo_alerta = self::ALERTA_NORMAL;

            return;
        }

        // Evaluar según tipo de referencia
        $evaluacion = $valorReferencia->evaluarValor($this->valor_principal);

        // Actualizar campos
        $this->valor_referencia_id = $valorReferencia->id;
        $this->rango_referencia = $valorReferencia->rango_texto;
        $this->fuera_rango = ! $evaluacion['dentro_rango'];
        $this->tipo_alerta = $evaluacion['tipo_alerta'];

        if ($evaluacion['categoria']) {
            $this->categoria_asignada = $evaluacion['categoria'];
        }

        // Marcar para revisión si es crítico
        if ($this->tipo_alerta === self::ALERTA_CRITICO) {
            $this->requiere_revision = true;
        }
    }

    /**
     * Obtener valor de referencia aplicable según contexto
     */
    private function obtenerValorReferenciaAplicable(array $contexto): ?ExamenValorReferencia
    {
        $query = ExamenValorReferencia::where('parametro_id', $this->parametro_id)
            ->where('status', true);

        // Aplicar contexto si existe
        if (! empty($contexto)) {
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

            // Filtrar por condición especial si existe
            if (isset($contexto['condicion'])) {
                $query->where(function ($q) use ($contexto) {
                    $q->whereNull('condicion_especial')
                        ->orWhere('condicion_especial', $contexto['condicion']);
                });
            }
        }

        return $query->orderBy('orden')->first();
    }

    /**
     * Marcar para revisión
     */
    public function marcarParaRevision(?string $motivo = null): void
    {
        $this->update([
            'requiere_revision' => true,
            'observaciones' => $motivo ?
                ($this->observaciones ? $this->observaciones."\n".$motivo : $motivo) :
                $this->observaciones,
        ]);
    }

    /**
     * Revisar resultado
     */
    public function revisar(?int $userId = null, ?string $comentario = null): void
    {
        $this->update([
            'revisado_por' => $userId ?? Auth::id(),
            'fecha_revision' => now(),
            'comentario_revision' => $comentario,
            'requiere_revision' => false,
        ]);
    }

    /**
     * Validar resultado
     */
    public function validar(?int $userId = null): void
    {
        $this->update([
            'validado_por' => $userId ?? Auth::id(),
            'fecha_validacion' => now(),
        ]);
    }

    /**
     * Comparar con resultado anterior del paciente
     */
    public function compararConAnterior(): ?array
    {
        $servicioExamen = $this->servicioExamen;
        if (! $servicioExamen) {
            return null;
        }

        $servicio = $servicioExamen->servicio;
        if (! $servicio) {
            return null;
        }

        // Buscar resultado anterior del mismo parámetro para el mismo paciente
        $resultadoAnterior = self::where('parametro_id', $this->parametro_id)
            ->whereHas('servicioExamen.servicio', function ($q) use ($servicio) {
                $q->where('paciente_id', $servicio->paciente_id)
                    ->where('id', '<', $servicio->id);
            })
            ->whereNotNull('valor_numerico')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $resultadoAnterior) {
            return null;
        }

        $diferencia = $this->valor_numerico - $resultadoAnterior->valor_numerico;
        $porcentaje = $resultadoAnterior->valor_numerico != 0
            ? ($diferencia / $resultadoAnterior->valor_numerico) * 100
            : 0;

        return [
            'valor_anterior' => $resultadoAnterior->valor_numerico,
            'valor_actual' => $this->valor_numerico,
            'diferencia' => $diferencia,
            'porcentaje_cambio' => round($porcentaje, 2),
            'fecha_anterior' => $resultadoAnterior->created_at,
            'tendencia' => $diferencia > 0 ? 'AUMENTO' : ($diferencia < 0 ? 'DISMINUCIÓN' : 'ESTABLE'),
        ];
    }

    /**
     * ========================================
     * EVENTOS DEL MODELO
     * ========================================
     */
    protected static function booted()
    {
        // Al crear, registrar quién capturó
        static::creating(function ($resultado) {
            if (! $resultado->capturado_por && Auth::check()) {
                $resultado->capturado_por = Auth::id();
            }
        });

        // Al actualizar valor, re-evaluar si es necesario
        static::updating(function ($resultado) {
            $dirty = $resultado->getDirty();

            // Si cambió el valor principal, re-evaluar
            if (isset($dirty['valor_numerico']) ||
                isset($dirty['valor_cualitativo']) ||
                isset($dirty['valor_texto'])) {

                // Solo auto-evaluar si no se están estableciendo manualmente los campos de evaluación
                if (! isset($dirty['fuera_rango']) && ! isset($dirty['tipo_alerta'])) {
                    // Aquí podrías llamar a evaluar() si tienes el contexto disponible
                    // $resultado->evaluar();
                }
            }
        });
    }

    /**
     * ========================================
     * MÉTODOS ESTÁTICOS DE UTILIDAD
     * ========================================
     */

    /**
     * Crear resultado y evaluarlo automáticamente
     */
    public static function crearYEvaluar(array $data, array $contexto = []): self
    {
        $resultado = self::create($data);
        $resultado->evaluar($contexto);
        $resultado->save();

        return $resultado;
    }

    /**
     * Obtener estadísticas de alertas para un servicio
     */
    public static function estadisticasAlerta(int $servicioExamenId): array
    {
        $resultados = self::where('servicio_examen_id', $servicioExamenId)->get();

        return [
            'total' => $resultados->count(),
            'normal' => $resultados->where('tipo_alerta', self::ALERTA_NORMAL)->count(),
            'bajo' => $resultados->where('tipo_alerta', self::ALERTA_BAJO)->count(),
            'alto' => $resultados->where('tipo_alerta', self::ALERTA_ALTO)->count(),
            'critico' => $resultados->where('tipo_alerta', self::ALERTA_CRITICO)->count(),
            'fuera_rango' => $resultados->where('fuera_rango', true)->count(),
            'requieren_revision' => $resultados->where('requiere_revision', true)->count(),
        ];
    }
}
