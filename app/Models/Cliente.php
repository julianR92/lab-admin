<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'genero',
        'fecha_nacimiento',
        'edad',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'eps',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Accessor para edad (aunque está en la BD, útil para consultas)
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }

    // Scope para búsquedas por documento
    public function scopePorDocumento($query, string $documento)
    {
        return $query->where('documento', $documento);
    }

    // Scope para búsquedas por nombre o apellido
    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('apellido', 'like', "%{$termino}%")
                ->orWhere('documento', 'like', "%{$termino}%");
        });
    }

    // Relación con servicios (la crearemos más adelante)
    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}
