<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicioExamen extends Model
{
    use HasFactory;

    protected $table = 'servicio_examen';

    protected $fillable = [
        'servicio_id',
        'examen_id',
        'es_remitido',
        'pdf_remision',
        'profesional_id',
        'estado',
        'fecha_toma_muestra',
        'fecha_resultado',
        'fecha_validacion',
        'fecha_entrega',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'es_remitido' => 'boolean',
            'fecha_toma_muestra' => 'datetime',
            'fecha_resultado' => 'datetime',
            'fecha_validacion' => 'datetime',
            'fecha_entrega' => 'datetime',
        ];
    }

    // Relaciones
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function examen()
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id');
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoExamen::class, 'servicio_examen_id');
    }

    /**
     * Relación con ResultadosAdjuntos (archivos adjuntos) ⭐ NUEVA
     */
    public function adjuntos(): HasMany
    {
        return $this->hasMany(ResultadoAdjunto::class, 'servicio_examen_id')
            ->orderBy('orden', 'asc');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'EN_PROCESO');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'COMPLETADO');
    }

    public function scopeValidados($query)
    {
        return $query->where('estado', 'VALIDADO');
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'ENTREGADO');
    }

    // Helpers
    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    public function estaValidado(): bool
    {
        return $this->estado === 'VALIDADO';
    }

    public function puedeEditarse(): bool
    {
        return in_array($this->estado, ['PENDIENTE', 'EN_PROCESO', 'COMPLETADO']);
    }

    public function puedeValidarse(): bool
    {
        return $this->estado === 'COMPLETADO';
    }

    public function puedeImprimirse(): bool
    {
        return $this->estado === 'VALIDADO';
    }
}
