<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaExamen extends Model
{
    use HasFactory;

    protected $table = 'categoria_examen';

    protected $fillable = [
        'categoria',
        'descripcion',
        'status',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'orden' => 'integer',
        ];
    }

    // Relaciones
    public function examenes()
    {
        return $this->hasMany(Examen::class, 'categoria_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }
}
