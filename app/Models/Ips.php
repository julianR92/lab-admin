<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ips extends Model
{
    use HasFactory;

    protected $table = 'ips';

    protected $fillable = [
        'razon_social',
        'nit',
        'correo_electronico',
        'logo',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
