<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenValorReferencia extends Model
{
    use HasFactory;

    protected $table = 'examen_valores_referencia';

    protected $fillable = [
        'examen_id',
        'parametro_id',
        'tipo_referencia',
        'genero',
        'edad_min',
        'edad_max',
        'condicion_especial',
        'valor_min',
        'valor_max',
        'operador',
        'valor_cualitativo',
        'categoria',
        'descripcion',
        'orden',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'edad_min' => 'integer',
            'edad_max' => 'integer',
            'valor_min' => 'decimal:4',
            'valor_max' => 'decimal:4',
            'orden' => 'integer',
            'status' => 'boolean',
        ];
    }

    // Relaciones
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function parametro()
    {
        return $this->belongsTo(ExamenParametro::class, 'parametro_id');
    }

    /**
     * Obtener los resultados asociados a través del parámetro.
     * Solo aplica si el valor de referencia está asociado a un parámetro específico.
     */
    public function resultados()
    {
        if ($this->parametro_id) {
            return $this->hasManyThrough(
                ResultadoExamen::class,
                ExamenParametro::class,
                'id', // Foreign key en examen_parametros
                'parametro_id', // Foreign key en resultados_examen
                'parametro_id', // Local key en examen_valores_referencia
                'id' // Local key en examen_parametros
            );
        }

        // Si es un valor de referencia general (sin parametro_id), retornar query vacío
        return ResultadoExamen::whereRaw('1 = 0');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('status', true);
    }

    public function scopePorGenero($query, ?string $genero)
    {
        return $query->where(function ($q) use ($genero) {
            $q->whereNull('genero')
                ->orWhere('genero', $genero);
        });
    }

    public function scopePorEdad($query, int $edad)
    {
        return $query->where(function ($q) use ($edad) {
            $q->where(function ($sq) use ($edad) {
                $sq->whereNull('edad_min')
                    ->orWhere('edad_min', '<=', $edad);
            })->where(function ($sq) use ($edad) {
                $sq->whereNull('edad_max')
                    ->orWhere('edad_max', '>=', $edad);
            });
        });
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    // Helpers
    public function aplicaParaCliente(string $genero, int $edad): bool
    {
        $aplicaGenero = is_null($this->genero) || $this->genero === $genero;
        $aplicaEdadMin = is_null($this->edad_min) || $this->edad_min <= $edad;
        $aplicaEdadMax = is_null($this->edad_max) || $this->edad_max >= $edad;

        return $aplicaGenero && $aplicaEdadMin && $aplicaEdadMax;
    }

    /**
     * Evaluar un valor contra este valor de referencia
     */
    public function evaluarValor($valor): array
    {
        $resultado = [
            'dentro_rango' => true,
            'tipo_alerta' => 'NORMAL',
            'categoria' => null,
        ];

        switch ($this->tipo_referencia) {
            case 'RANGO':
                $resultado = $this->evaluarRango($valor);
                break;

            case 'CATEGORIZADO':
                $resultado = $this->evaluarCategorizado($valor);
                break;

            case 'CUALITATIVO':
                $resultado = $this->evaluarCualitativo($valor);
                break;

            case 'INFORMATIVO':
                // Los valores informativos no generan alertas
                $resultado['dentro_rango'] = true;
                $resultado['tipo_alerta'] = 'NORMAL';
                break;
        }

        return $resultado;
    }

    /**
     * Evaluar valor contra un rango numérico
     */
    private function evaluarRango($valor): array
    {
        $resultado = [
            'dentro_rango' => true,
            'tipo_alerta' => 'NORMAL',
            'categoria' => null,
        ];

        if (! is_numeric($valor)) {
            return $resultado;
        }

        // Verificar si está fuera del rango
        if ($this->valor_min !== null && $valor < $this->valor_min) {
            $resultado['dentro_rango'] = false;
            $resultado['tipo_alerta'] = 'BAJO';

            // Verificar si es crítico (muy por debajo)
            $diferencia = (($this->valor_min - $valor) / $this->valor_min) * 100;
            if ($diferencia > 30) {
                $resultado['tipo_alerta'] = 'CRITICO';
            }
        } elseif ($this->valor_max !== null && $valor > $this->valor_max) {
            $resultado['dentro_rango'] = false;
            $resultado['tipo_alerta'] = 'ALTO';

            // Verificar si es crítico (muy por encima)
            $diferencia = (($valor - $this->valor_max) / $this->valor_max) * 100;
            if ($diferencia > 30) {
                $resultado['tipo_alerta'] = 'CRITICO';
            }
        }

        return $resultado;
    }

    /**
     * Evaluar valor categorizado (ej: Colesterol)
     */
    private function evaluarCategorizado($valor): array
    {
        $resultado = [
            'dentro_rango' => true,
            'tipo_alerta' => 'NORMAL',
            'categoria' => $this->categoria,
        ];

        if (! is_numeric($valor)) {
            return $resultado;
        }

        // Verificar si el valor cae en esta categoría
        $dentroDeLaCategoria = true;

        if ($this->operador) {
            // Usar operador si está definido
            switch ($this->operador) {
                case '<':
                    $dentroDeLaCategoria = $valor < $this->valor_max;
                    break;
                case '<=':
                    $dentroDeLaCategoria = $valor <= $this->valor_max;
                    break;
                case '>':
                    $dentroDeLaCategoria = $valor > $this->valor_min;
                    break;
                case '>=':
                    $dentroDeLaCategoria = $valor >= $this->valor_min;
                    break;
                case '==':
                    $dentroDeLaCategoria = $valor == $this->valor_min;
                    break;
            }
        } else {
            // Usar rango si no hay operador
            if ($this->valor_min !== null && $valor < $this->valor_min) {
                $dentroDeLaCategoria = false;
            }
            if ($this->valor_max !== null && $valor > $this->valor_max) {
                $dentroDeLaCategoria = false;
            }
        }

        if ($dentroDeLaCategoria) {
            // Determinar tipo de alerta según la categoría
            $categoriaLower = strtolower($this->categoria ?? '');

            if (str_contains($categoriaLower, 'crítico') || str_contains($categoriaLower, 'critico') ||
                str_contains($categoriaLower, 'muy alto') || str_contains($categoriaLower, 'severo')) {
                $resultado['tipo_alerta'] = 'CRITICO';
                $resultado['dentro_rango'] = false;
            } elseif (str_contains($categoriaLower, 'alto') || str_contains($categoriaLower, 'elevado') ||
                      str_contains($categoriaLower, 'intermedio')) {
                $resultado['tipo_alerta'] = 'ALTO';
                $resultado['dentro_rango'] = false;
            } elseif (str_contains($categoriaLower, 'bajo') || str_contains($categoriaLower, 'límite')) {
                $resultado['tipo_alerta'] = 'BAJO';
                $resultado['dentro_rango'] = false;
            } else {
                // Categorías óptimas/normales
                $resultado['tipo_alerta'] = 'NORMAL';
                $resultado['dentro_rango'] = true;
            }
        }

        return $resultado;
    }

    /**
     * Evaluar valor cualitativo
     */
    private function evaluarCualitativo($valor): array
    {
        $resultado = [
            'dentro_rango' => true,
            'tipo_alerta' => 'NORMAL',
            'categoria' => null,
        ];

        // Solo el valor literal "REACTIVO" (case-insensitive, ignorando espacios) genera alerta.
        // Los demás valores cualitativos (POSITIVO, NEGATIVO, AMARILLO, TURBIO, etc.) no activan
        // formato rojo por decisión de negocio: el reporte debe destacar únicamente reactivos.
        if (is_string($valor) && strtoupper(trim($valor)) === 'REACTIVO') {
            $resultado['dentro_rango'] = false;
            $resultado['tipo_alerta'] = 'ALTO';
        }

        return $resultado;
    }

    /**
     * Obtener el texto del rango para mostrar
     */
    public function getRangoTextoAttribute(): string
    {
        $decimales = $this->parametro?->decimales ?? 2;
        $prefijo = $this->obtenerPrefijoContexto();

        // Formatea con decimales solo si el valor no es entero
        $formatNum = function ($v) use ($decimales) {
            $f = (float) $v;
            if (floor($f) == $f) {
                return number_format($f, 0, '.', '');
            }

            return number_format($f, $decimales, '.', '');
        };

        switch ($this->tipo_referencia) {
            case 'RANGO':
                $partes = [];
                if ($this->valor_min !== null) {
                    $partes[] = $formatNum($this->valor_min);
                }
                if ($this->valor_max !== null) {
                    if (! empty($partes)) {
                        $partes[] = '-';
                    }
                    $partes[] = $formatNum($this->valor_max);
                }

                $rango = implode(' ', $partes).($this->parametro && $this->parametro->unidad_medida ? ' '.$this->parametro->unidad_medida : '');

                return $prefijo ? $prefijo.' '.$rango : $rango;

            case 'CATEGORIZADO':
                // Formato: Categoría: min - max (sin operadores)
                $categoria = $this->categoria ?? 'Sin categoría';
                $rango = '';

                if ($this->valor_min !== null && $this->valor_max !== null) {
                    $rango = $formatNum($this->valor_min).' - '.$formatNum($this->valor_max);
                } elseif ($this->valor_min !== null) {
                    $rango = $formatNum($this->valor_min).' +';
                } elseif ($this->valor_max !== null) {
                    $rango = '0 - '.$formatNum($this->valor_max);
                }

                $texto = $categoria.': '.$rango;

                return $prefijo ? $prefijo.' - '.$texto : $texto;

            case 'CUALITATIVO':
                $valor = $this->valor_cualitativo ?? '-';

                return $prefijo ? $prefijo.' '.$valor : $valor;

            case 'INFORMATIVO':
                return $this->descripcion ?? 'Informativo';

            default:
                return '-';
        }
    }

    /**
     * Obtener prefijo contextual (condición, género, edad)
     */
    private function obtenerPrefijoContexto(): ?string
    {
        $partes = [];

        // Prioridad 1: Condición especial (ej: "Embarazo 5 Semanas")
        if ($this->condicion_especial) {
            return $this->condicion_especial;
        }

        // Prioridad 2: Género
        if ($this->genero) {
            $partes[] = $this->genero === 'M' ? 'Hombre' : 'Mujer';
        }

        // Prioridad 3: Rango de edad
        if ($this->edad_min !== null || $this->edad_max !== null) {
            if ($this->edad_max === null) {
                $partes[] = "({$this->edad_min} en adelante)";
            } elseif ($this->edad_min === null) {
                $partes[] = "(0 - {$this->edad_max} años)";
            } else {
                $partes[] = "({$this->edad_min} - {$this->edad_max} años)";
            }
        }

        return ! empty($partes) ? implode(' ', $partes) : null;
    }
}
