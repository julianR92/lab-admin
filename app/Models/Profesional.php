<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    protected $table = 'profesionales';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'profesion',
        'registro_profesional',
        'especialidad',
        'firma_digital',
        'telefono',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    // Relaciones
    public function serviciosExamen()
    {
        return $this->hasMany(ServicioExamen::class, 'profesional_id');
    }

    public function resultadosValidados()
    {
        return $this->hasMany(ResultadoExamen::class, 'validado_por');
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('status', true);
    }
}
