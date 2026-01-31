{{-- Galería de Adjuntos --}}
<div class="card border-0 shadow-sm mb-4" id="galeria-adjuntos">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-images me-2 text-primary"></i>
                Imágenes Adjuntas
                <span class="badge bg-primary ms-2" id="contador-adjuntos">0</span>
            </h6>
            <div class="btn-group btn-group-sm" role="group">
                @if ($servicioExamen->estado !== 'ENTREGADO')
                    <button type="button" class="btn btn-outline-primary" id="btn-subir-imagenes">
                        <i class="fas fa-upload me-1"></i>Subir Imágenes
                    </button>
                @endif
                <button type="button" class="btn btn-outline-success" id="btn-descargar-todo" disabled>
                    <i class="fas fa-download me-1"></i>Descargar Todo (ZIP)
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Zona de carga (inicialmente oculta) --}}
        <div id="zona-carga" class="d-none mb-4">
            <div class="border border-2 border-dashed rounded p-4"
                 id="dropzone"
                 style="border-color: #dee2e6; background-color: #f8f9fa; transition: all 0.3s;">
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Arrastra imágenes aquí o haz clic para seleccionar</h5>
                    <p class="text-muted small mb-3">
                        Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP<br>
                        Tamaño máximo: 10 MB por imagen<br>
                        Máximo 3 imágenes por examen
                    </p>
                    <input type="file"
                           id="file-input"
                           multiple
                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                           class="d-none">
                    <button type="button" class="btn btn-primary" id="btn-seleccionar-archivos">
                        <i class="fas fa-folder-open me-2"></i>Seleccionar Archivos
                    </button>
                </div>
            </div>

            {{-- Cola de archivos por subir --}}
            <div id="cola-archivos" class="mt-3"></div>

            {{-- Botones de acción --}}
            <div id="botones-carga" class="mt-3 d-none">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" id="btn-cancelar-carga">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btn-subir-archivos">
                        <i class="fas fa-upload me-1"></i>
                        <span id="texto-subir">Subir 0 imágenes</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Galería de imágenes --}}
        <div id="galeria-imagenes" class="row g-3">
            {{-- Se llenará dinámicamente con JavaScript --}}
        </div>

        {{-- Mensaje cuando no hay imágenes --}}
        <div id="sin-imagenes" class="text-center py-5">
            <i class="fas fa-image fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">No se han adjuntado imágenes a este examen</p>
            @if ($servicioExamen->estado !== 'ENTREGADO')
                <button type="button" class="btn btn-primary mt-3" id="btn-primera-carga">
                    <i class="fas fa-upload me-2"></i>Subir Primera Imagen
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Modal de Vista Completa de Imagen --}}
<div class="modal fade" id="modalImagenCompleta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo">Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-dark position-relative">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 500px;">
                    <img src=""
                         id="modalImagenVisor"
                         class="img-fluid"
                         style="max-height: 80vh; object-fit: contain;">
                </div>

                {{-- Controles de zoom --}}
                <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light btn-sm" id="btn-zoom-out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-light btn-sm" id="btn-zoom-reset">
                            <i class="fas fa-compress-arrows-alt"></i>
                        </button>
                        <button type="button" class="btn btn-light btn-sm" id="btn-zoom-in">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="flex-grow-1">
                    <small class="text-muted" id="modalImagenInfo"></small>
                </div>
                <button type="button" class="btn btn-primary" id="btn-descargar-imagen">
                    <i class="fas fa-download me-1"></i>Descargar
                </button>
                @if ($servicioExamen->estado !== 'ENTREGADO')
                    <button type="button" class="btn btn-danger" id="btn-eliminar-imagen-modal">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Estilos CSS --}}
<style>
    #dropzone.drag-over {
        border-color: #0d6efd !important;
        background-color: #e7f1ff !important;
    }

    .imagen-card {
        position: relative;
        overflow: hidden;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .imagen-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .imagen-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .imagen-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .imagen-card:hover .imagen-card-overlay {
        opacity: 1;
    }

    .archivo-item {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        transition: all 0.3s;
    }

    .archivo-item.subiendo {
        background-color: #e7f1ff;
    }

    .archivo-item.exitoso {
        background-color: #d1e7dd;
        border-color: #198754;
    }

    .archivo-item.error {
        background-color: #f8d7da;
        border-color: #dc3545;
    }

    .progress {
        height: 4px;
    }

    #modalImagenVisor {
        transition: transform 0.3s;
    }
</style>

{{-- Incluir el JavaScript --}}
@push('scripts')
<script src="{{ asset('js/galeria-adjuntos.js') }}"></script>
<script>
    // Inicializar galería con el ID del servicio examen
    const servicioExamenId = {{ $servicioExamen->id }};
    const estadoExamen = '{{ $servicioExamen->estado }}';

    document.addEventListener('DOMContentLoaded', function() {
        GaleriaAdjuntos.init(servicioExamenId, estadoExamen);
    });
</script>
@endpush
