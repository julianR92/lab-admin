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
}
