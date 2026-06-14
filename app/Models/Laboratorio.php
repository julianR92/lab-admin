<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratorio extends Model
{
    use HasFactory;

    protected $table = 'laboratorios';

    protected $fillable = [
        'nombre',
        'nit',
        'telefono',
        'email',
        'ciudad',
        'contacto',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function examenes(): BelongsToMany
    {
        return $this->belongsToMany(Examen::class, 'laboratorio_examen', 'laboratorio_id', 'examen_id')
            ->withPivot('valor_remision')
            ->withTimestamps();
    }

    public function serviciosExamen(): HasMany
    {
        return $this->hasMany(ServicioExamen::class, 'laboratorio_id');
    }
}
