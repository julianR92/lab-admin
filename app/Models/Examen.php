<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $table = 'examen';

    protected $fillable = [
        'categoria_id',
        'codigo',
        'nombre',
        'tipo_resultado',
        'unidad_medida',
        'tecnica',
        'muestra_requerida',
        'valor_total',
        'valor_remision',
        'tiempo_entrega',
        'requiere_ayuno',
        'instrucciones_paciente',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor_total' => 'decimal:2',
            'valor_remision' => 'decimal:2',
            'tiempo_entrega' => 'integer',
            'requiere_ayuno' => 'boolean',
            'status' => 'boolean',
        ];
    }

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaExamen::class, 'categoria_id');
    }

    public function parametros()
    {
        return $this->hasMany(ExamenParametro::class, 'examen_id');
    }

    public function valoresReferencia()
    {
        return $this->hasMany(ExamenValorReferencia::class, 'examen_id');
    }

    public function serviciosExamen()
    {
        return $this->hasMany(ServicioExamen::class, 'examen_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('status', true);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_resultado', $tipo);
    }

    // Helpers
    public function esNumericoSimple(): bool
    {
        return $this->tipo_resultado === 'NUMERICO_SIMPLE';
    }

    public function esCalculado(): bool
    {
        return $this->tipo_resultado === 'MULTIPLE_CALCULADO';
    }

    public function tieneParametrosCalculados(): bool
    {
        return $this->parametros()->where('es_calculado', true)->exists();
    }
}
