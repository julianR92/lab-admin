<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoExamen extends Model
{
    use HasFactory;

    protected $table = 'resultados_examen';

    protected $fillable = [
        'servicio_examen_id',
        'parametro_id',
        'valor_numerico',
        'valor_texto',
        'valor_cualitativo',
        'unidad_medida',
        'fuera_rango',
        'tipo_alerta',
        'notas_tecnico',
        'validado_por',
        'fecha_validacion',
    ];

    protected function casts(): array
    {
        return [
            'valor_numerico' => 'decimal:4',
            'fuera_rango' => 'boolean',
            'fecha_validacion' => 'datetime',
        ];
    }

    // Relaciones
    public function servicioExamen()
    {
        return $this->belongsTo(ServicioExamen::class, 'servicio_examen_id');
    }

    public function parametro()
    {
        return $this->belongsTo(ExamenParametro::class, 'parametro_id');
    }

    public function validador()
    {
        return $this->belongsTo(Profesional::class, 'validado_por');
    }

    // Accessors
    public function getValorFormateadoAttribute(): string
    {
        if (! is_null($this->valor_numerico)) {
            $decimales = $this->parametro->decimales ?? 2;

            return number_format($this->valor_numerico, $decimales);
        }

        if (! is_null($this->valor_cualitativo)) {
            return $this->valor_cualitativo;
        }

        return $this->valor_texto ?? '';
    }

    // Scopes
    public function scopeFueraDeRango($query)
    {
        return $query->where('fuera_rango', true);
    }

    public function scopePorTipoAlerta($query, string $tipo)
    {
        return $query->where('tipo_alerta', $tipo);
    }

    public function scopeValidados($query)
    {
        return $query->whereNotNull('validado_por');
    }

    // Helpers
    public function estaFueraDeRango(): bool
    {
        return $this->fuera_rango === true;
    }

    public function esAlertaAlta(): bool
    {
        return $this->tipo_alerta === 'ALTO';
    }

    public function esAlertaBaja(): bool
    {
        return $this->tipo_alerta === 'BAJO';
    }

    public function esAlertaCritica(): bool
    {
        return $this->tipo_alerta === 'CRITICO';
    }

    public function esNormal(): bool
    {
        return $this->tipo_alerta === 'NORMAL';
    }

    public function estaValidado(): bool
    {
        return ! is_null($this->validado_por);
    }
}
