<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ResultadoAdjunto extends Model
{
    //
    use HasFactory;

    protected $table = 'resultados_adjuntos';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'servicio_examen_id',
        'tipo_archivo',
        'nombre_archivo',
        'ruta_archivo',
        'mime_type',
        'tamano_bytes',
        'descripcion',
        'orden',
        'subido_por',
    ];

    protected $casts = [
        'tamano_bytes' => 'integer',
        'orden' => 'integer',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'url_archivo',
        'tamano_mb',
        'icono_tipo',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Relación con ServicioExamen
     */
    public function servicioExamen(): BelongsTo
    {
        return $this->belongsTo(ServicioExamen::class, 'servicio_examen_id');
    }

    /**
     * Relación con Usuario (quien subió el archivo)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Profesional::class, 'subido_por');
    }

    // ========================================
    // ACCESSORS (ATRIBUTOS CALCULADOS)
    // ========================================

    /**
     * Obtener URL completa del archivo
     */
    public function getUrlArchivoAttribute(): string
    {
        if (Storage::disk('public')->exists($this->ruta_archivo)) {
            return Storage::disk('public')->url($this->ruta_archivo);
        }

        return asset('images/file-not-found.png');
    }

    /**
     * Obtener tamaño en MB
     */
    public function getTamanoMbAttribute(): string
    {
        if ($this->tamano_bytes) {
            return number_format($this->tamano_bytes / 1024 / 1024, 2).' MB';
        }

        return '0 MB';
    }

    /**
     * Obtener ícono según tipo de archivo
     */
    public function getIconoTipoAttribute(): string
    {
        return match ($this->tipo_archivo) {
            'IMAGEN' => 'fas fa-image',
            'PDF' => 'fas fa-file-pdf',
            'DOCUMENTO' => 'fas fa-file-word',
            'ELECTROCARDIOGRAMA' => 'fas fa-heartbeat',
            'RADIOGRAFIA' => 'fas fa-x-ray',
            'ECOGRAFIA' => 'fas fa-file-medical',
            default => 'fas fa-file',
        };
    }

    /**
     * Obtener extensión del archivo
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->nombre_archivo, PATHINFO_EXTENSION);
    }

    /**
     * Verificar si es una imagen
     */
    public function getEsImagenAttribute(): bool
    {
        $extensionesImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];

        return in_array(strtolower($this->extension), $extensionesImagen);
    }

    // ========================================
    // SCOPES (CONSULTAS REUTILIZABLES)
    // ========================================

    /**
     * Filtrar por tipo de archivo
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo_archivo', $tipo);
    }

    /**
     * Filtrar por servicio examen
     */
    public function scopeDelServicio($query, int $servicioExamenId)
    {
        return $query->where('servicio_examen_id', $servicioExamenId);
    }

    /**
     * Ordenar por campo 'orden'
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'asc');
    }

    /**
     * Solo imágenes
     */
    public function scopeSoloImagenes($query)
    {
        return $query->whereIn('tipo_archivo', ['IMAGEN', 'RADIOGRAFIA', 'ECOGRAFIA', 'ELECTROCARDIOGRAMA']);
    }

    /**
     * Solo PDFs
     */
    public function scopeSoloPdfs($query)
    {
        return $query->where('tipo_archivo', 'PDF');
    }

    // ========================================
    // MÉTODOS PERSONALIZADOS
    // ========================================

    /**
     * Eliminar archivo físico y registro de BD
     */
    public function eliminarCompleto(): bool
    {
        // Eliminar archivo físico
        if (Storage::disk('public')->exists($this->ruta_archivo)) {
            Storage::disk('public')->delete($this->ruta_archivo);
        }

        // Eliminar registro de BD
        return $this->delete();
    }

    /**
     * Descargar archivo
     */
    public function descargar()
    {
        if (Storage::disk('public')->exists($this->ruta_archivo)) {
            return Storage::disk('public')->download($this->ruta_archivo, $this->nombre_archivo);
        }

        abort(404, 'Archivo no encontrado');
    }

    /**
     * Verificar si el archivo existe físicamente
     */
    public function archivoExiste(): bool
    {
        return Storage::disk('public')->exists($this->ruta_archivo);
    }

    // ========================================
    // EVENTOS DEL MODELO
    // ========================================

    protected static function boot()
    {
        parent::boot();

        // Al eliminar, borrar archivo físico
        static::deleting(function ($adjunto) {
            if (Storage::disk('public')->exists($adjunto->ruta_archivo)) {
                Storage::disk('public')->delete($adjunto->ruta_archivo);
            }
        });
    }
}
