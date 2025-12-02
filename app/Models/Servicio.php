<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'servicio';

    protected $fillable = [
        'cliente_id',
        'fecha',
        'valor_total',
        'valor_pagado',
        'medio_pago',
        'estado_pago',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'valor_total' => 'decimal:2',
            'valor_pagado' => 'decimal:2',
        ];
    }

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function serviciosExamen()
    {
        return $this->hasMany(ServicioExamen::class, 'servicio_id');
    }

    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'servicio_examen', 'servicio_id', 'examen_id')
            ->withPivot('profesional_id', 'estado', 'fecha_toma_muestra', 'fecha_resultado', 'fecha_validacion', 'fecha_entrega', 'observaciones')
            ->withTimestamps();
    }

    // Accessors
    public function getNumeroOrdenAttribute(): string
    {
        return 'ORD-'.str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->valor_total - $this->valor_pagado;
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado_pago', 'Pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado_pago', 'Pagado');
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now();

        return $query->whereDate('fecha', $fecha);
    }

    // Helpers
    public function estaPagado(): bool
    {
        return $this->estado_pago === 'Pagado';
    }

    public function tieneSaldoPendiente(): bool
    {
        return $this->saldo_pendiente > 0;
    }
}
