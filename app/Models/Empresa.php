<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';

    protected $fillable = [
        'nit',
        'razon_social',
        'direccion',
        'barrio',
        'ciudad',
        'telefono_uno',
        'telefono_dos',
        'email',
        'logo',
    ];

    // Accessors
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        // Si es una URL completa, retornarla
        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        // Si es una ruta en storage, generar URL pública
        return Storage::disk('public')->url($this->logo);
    }

    public function getDireccionCompletaAttribute(): string
    {
        $partes = array_filter([
            $this->direccion,
            $this->barrio,
            $this->ciudad,
        ]);

        return implode(', ', $partes);
    }

    public function getContactoCompletoAttribute(): string
    {
        $contactos = [];

        if ($this->telefono_uno) {
            $contactos[] = "Tel: {$this->telefono_uno}";
        }

        if ($this->telefono_dos) {
            $contactos[] = "Tel2: {$this->telefono_dos}";
        }

        if ($this->email) {
            $contactos[] = $this->email;
        }

        return implode(' | ', $contactos);
    }

    // Static methods
    public static function obtenerEmpresa(): ?self
    {
        // Retorna el primer registro (normalmente solo hay uno)
        return static::first();
    }

    // Helpers
    public function tieneLogoConfigurado(): bool
    {
        return ! empty($this->logo);
    }

    public function obtenerMembreteParaPDF(): array
    {
        return [
            'razon_social' => $this->razon_social,
            'nit' => $this->nit,
            'direccion' => $this->direccion_completa,
            'contacto' => $this->contacto_completo,
            'logo_url' => $this->logo_url,
            'tiene_logo' => $this->tieneLogoConfigurado(),
            'email' => $this->email,
        ];
    }
}
