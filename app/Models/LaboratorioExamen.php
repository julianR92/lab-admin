<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratorioExamen extends Model
{
    use HasFactory;

    protected $table = 'laboratorio_examen';

    protected $fillable = [
        'laboratorio_id',
        'examen_id',
        'valor_remision',
    ];

    protected function casts(): array
    {
        return [
            'valor_remision' => 'decimal:2',
        ];
    }

    public function laboratorio(): BelongsTo
    {
        return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }
}
