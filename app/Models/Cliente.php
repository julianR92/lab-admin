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
        'ips_id',
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

    // Accessor para edad en años (usado en evaluación de valores de referencia)
    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }

    // Accessor para edad formateada (días/meses/años según corresponda)
    public function getEdadTextoAttribute(): string
    {
        $nacimiento = \Carbon\Carbon::parse($this->fecha_nacimiento);
        $hoy = \Carbon\Carbon::now();

        $anos = (int) $nacimiento->diffInYears($hoy);
        if ($anos >= 1) {
            return "{$anos} años";
        }

        $meses = (int) $nacimiento->diffInMonths($hoy);
        if ($meses >= 1) {
            return "{$meses} meses";
        }

        $dias = (int) $nacimiento->diffInDays($hoy);
        return "{$dias} días";
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

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function ips()
    {
        return $this->belongsTo(Ips::class);
    }
}
