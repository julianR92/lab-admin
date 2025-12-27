<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenParametro extends Model
{
    use HasFactory;

    protected $table = 'examen_parametros';

    protected $fillable = [
        'examen_id',
        'nombre_parametro',
        'seccion',
        'codigo_parametro',
        'tipo_dato',
        'unidad_medida',
        'decimales',
        'orden',
        'es_calculado',
        'formula_calculo',
        'requerido',
        'opciones_select',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'decimales' => 'integer',
            'orden' => 'integer',
            'es_calculado' => 'boolean',
            'formula_calculo' => 'array',
            'requerido' => 'boolean',
            'opciones_select' => 'array',
            'status' => 'boolean',
        ];
    }

    // Relaciones
    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function valoresReferencia()
    {
        return $this->hasMany(ExamenValorReferencia::class, 'parametro_id');
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoExamen::class, 'parametro_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopeCalculados($query)
    {
        return $query->where('es_calculado', true);
    }

    public function scopeManuales($query)
    {
        return $query->where('es_calculado', false);
    }

    // Helpers
    public function esSelect(): bool
    {
        return $this->tipo_dato === 'SELECT';
    }

    public function esNumerico(): bool
    {
        return in_array($this->tipo_dato, ['DECIMAL', 'INTEGER']);
    }

    public function getOpcionesSelectArray(): array
    {
        return $this->opciones_select ?? [];
    }
}
