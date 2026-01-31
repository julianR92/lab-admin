/**
 * Galería de Adjuntos para Resultados de Exámenes
 * Sistema completo de carga, visualización y gestión de imágenes
 */

const GaleriaAdjuntos = {
    servicioExamenId: null,
    estadoExamen: null,
    archivosEnCola: [],
    adjuntosCargados: [],
    maxArchivos: 20,
    maxTamano: 10485760, // 10 MB en bytes
    formatosPermitidos: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
    zoomLevel: 1,
    imagenActual: null,

    /**
     * Inicializar galería
     */
    init(servicioExamenId, estadoExamen = null) {
        this.servicioExamenId = servicioExamenId;
        this.estadoExamen = estadoExamen;
        this.configurarEventos();
        this.cargarAdjuntos();
    },

    /**
     * Configurar eventos del DOM
     */
    configurarEventos() {
        const self = this;

        // Botones principales
        document.getElementById('btn-subir-imagenes')?.addEventListener('click', () => this.mostrarZonaCarga());
        document.getElementById('btn-primera-carga')?.addEventListener('click', () => this.mostrarZonaCarga());
        document.getElementById('btn-seleccionar-archivos')?.addEventListener('click', (e) => {
            e.stopPropagation(); // Evitar propagación al dropzone
            document.getElementById('file-input').click();
        });
        document.getElementById('btn-cancelar-carga')?.addEventListener('click', () => this.ocultarZonaCarga());
        document.getElementById('btn-subir-archivos')?.addEventListener('click', () => this.subirArchivos());
        document.getElementById('btn-descargar-todo')?.addEventListener('click', () => this.descargarTodo());

        // Input de archivos
        document.getElementById('file-input')?.addEventListener('change', (e) => {
            this.agregarArchivos(Array.from(e.target.files));
            e.target.value = ''; // Limpiar input
        });

        // Drag and Drop
        const dropzone = document.getElementById('dropzone');
        if (dropzone) {
            dropzone.addEventListener('click', (e) => {
                // Solo abrir selector si no se hizo clic en el botón
                if (e.target.id !== 'btn-seleccionar-archivos' &&
                    !e.target.closest('#btn-seleccionar-archivos')) {
                    document.getElementById('file-input').click();
                }
            });

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('drag-over');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('drag-over');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('drag-over');
                const archivos = Array.from(e.dataTransfer.files);
                this.agregarArchivos(archivos);
            });
        }

        // Modal de imagen completa
        document.getElementById('btn-zoom-in')?.addEventListener('click', () => this.zoomIn());
        document.getElementById('btn-zoom-out')?.addEventListener('click', () => this.zoomOut());
        document.getElementById('btn-zoom-reset')?.addEventListener('click', () => this.resetZoom());
        document.getElementById('btn-descargar-imagen')?.addEventListener('click', () => this.descargarImagen());
        document.getElementById('btn-eliminar-imagen-modal')?.addEventListener('click', () => this.eliminarImagenModal());

        // Resetear zoom al cerrar modal
        const modalElement = document.getElementById('modalImagenCompleta');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => this.resetZoom());
        }
    },

    /**
     * Mostrar zona de carga
     */
    mostrarZonaCarga() {
        document.getElementById('zona-carga')?.classList.remove('d-none');
        document.getElementById('sin-imagenes')?.classList.add('d-none');
    },

    /**
     * Ocultar zona de carga
     */
    ocultarZonaCarga() {
        document.getElementById('zona-carga')?.classList.add('d-none');
        this.archivosEnCola = [];
        this.actualizarColaCarga();

        // Mostrar mensaje si no hay imágenes
        if (this.adjuntosCargados.length === 0) {
            document.getElementById('sin-imagenes')?.classList.remove('d-none');
        }
    },

    /**
     * Agregar archivos a la cola
     */
    agregarArchivos(archivos) {
        const archivosValidos = archivos.filter(archivo => {
            // Validar formato
            if (!this.formatosPermitidos.includes(archivo.type)) {
                this.mostrarNotificacion(`El archivo "${archivo.name}" no es una imagen válida`, 'warning');
                return false;
            }

            // Validar tamaño
            if (archivo.size > this.maxTamano) {
                const tamanoMB = (archivo.size / 1024 / 1024).toFixed(2);
                this.mostrarNotificacion(`El archivo "${archivo.name}" excede el tamaño máximo (${tamanoMB} MB)`, 'warning');
                return false;
            }

            // Validar cantidad máxima
            if (this.archivosEnCola.length + this.adjuntosCargados.length >= this.maxArchivos) {
                this.mostrarNotificacion(`Máximo ${this.maxArchivos} imágenes permitidas`, 'warning');
                return false;
            }

            // Verificar duplicados
            const yaExiste = this.archivosEnCola.some(a => a.name === archivo.name && a.size === archivo.size);
            if (yaExiste) {
                this.mostrarNotificacion(`El archivo "${archivo.name}" ya está en la cola`, 'info');
                return false;
            }

            return true;
        });

        // Agregar archivos válidos a la cola
        archivosValidos.forEach(archivo => {
            this.archivosEnCola.push({
                file: archivo,
                id: Date.now() + Math.random(),
                estado: 'pendiente',
                progreso: 0,
            });
        });

        this.actualizarColaCarga();
    },

    /**
     * Actualizar visualización de la cola de carga
     */
    actualizarColaCarga() {
        const contenedor = document.getElementById('cola-archivos');
        const botones = document.getElementById('botones-carga');
        const textoSubir = document.getElementById('texto-subir');

        if (this.archivosEnCola.length === 0) {
            contenedor.innerHTML = '';
            botones?.classList.add('d-none');
            return;
        }

        botones?.classList.remove('d-none');
        textoSubir.textContent = `Subir ${this.archivosEnCola.length} ${this.archivosEnCola.length === 1 ? 'imagen' : 'imágenes'}`;

        contenedor.innerHTML = this.archivosEnCola.map(archivo => {
            const tamanoMB = (archivo.file.size / 1024 / 1024).toFixed(2);
            const iconoEstado = {
                'pendiente': '<i class="fas fa-clock text-muted"></i>',
                'subiendo': '<i class="fas fa-spinner fa-spin text-primary"></i>',
                'exitoso': '<i class="fas fa-check-circle text-success"></i>',
                'error': '<i class="fas fa-exclamation-circle text-danger"></i>',
            }[archivo.estado];

            return `
                <div class="archivo-item ${archivo.estado}" data-id="${archivo.id}">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            ${iconoEstado}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small">${archivo.file.name}</strong>
                                <span class="badge bg-secondary">${tamanoMB} MB</span>
                            </div>
                            ${archivo.estado === 'subiendo' ? `
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         role="progressbar"
                                         style="width: ${archivo.progreso}%"
                                         aria-valuenow="${archivo.progreso}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            ` : ''}
                            ${archivo.mensaje ? `<small class="text-muted">${archivo.mensaje}</small>` : ''}
                        </div>
                        ${archivo.estado === 'pendiente' ? `
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger ms-2"
                                    onclick="GaleriaAdjuntos.eliminarDeCola(${archivo.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    },

    /**
     * Eliminar archivo de la cola
     */
    eliminarDeCola(archivoId) {
        this.archivosEnCola = this.archivosEnCola.filter(a => a.id !== archivoId);
        this.actualizarColaCarga();
    },

    /**
     * Subir archivos al servidor
     */
    async subirArchivos() {
        const archivosPendientes = this.archivosEnCola.filter(a => a.estado === 'pendiente');

        if (archivosPendientes.length === 0) {
            this.mostrarNotificacion('No hay archivos para subir', 'info');
            return;
        }

        // Deshabilitar botón de subir
        const btnSubir = document.getElementById('btn-subir-archivos');
        btnSubir.disabled = true;

        // Subir archivos uno por uno
        for (const archivo of archivosPendientes) {
            await this.subirArchivo(archivo);
        }

        // Limpiar cola de exitosos
        this.archivosEnCola = this.archivosEnCola.filter(a => a.estado !== 'exitoso');

        // Recargar galería
        await this.cargarAdjuntos();

        // Ocultar zona de carga si todos fueron exitosos
        if (this.archivosEnCola.length === 0) {
            this.ocultarZonaCarga();
        } else {
            this.actualizarColaCarga();
        }

        btnSubir.disabled = false;
    },

    /**
     * Subir un archivo individual
     */
    async subirArchivo(archivo) {
        archivo.estado = 'subiendo';
        archivo.progreso = 0;
        this.actualizarColaCarga();

        const formData = new FormData();
        formData.append('archivo', archivo.file);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {};

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/servicio-examen/${this.servicioExamenId}/adjuntos`, {
                method: 'POST',
                headers: headers,
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                archivo.estado = 'exitoso';
                archivo.progreso = 100;
                archivo.mensaje = 'Subido correctamente';
            } else {
                archivo.estado = 'error';
                archivo.mensaje = data.message || 'Error al subir';
            }

        } catch (error) {
            archivo.estado = 'error';
            archivo.mensaje = 'Error de conexión';
            console.error('Error:', error);
        }

        this.actualizarColaCarga();
    },

    /**
     * Cargar adjuntos desde el servidor
     */
    async cargarAdjuntos() {
        try {
            const response = await fetch(`/servicio-examen/${this.servicioExamenId}/adjuntos`);
            const data = await response.json();

            if (data.success) {
                this.adjuntosCargados = data.data;
                this.renderizarGaleria();
                this.actualizarContador();
            }

        } catch (error) {
            console.error('Error al cargar adjuntos:', error);
            this.mostrarNotificacion('Error al cargar las imágenes', 'danger');
        }
    },

    /**
     * Renderizar galería de imágenes
     */
    renderizarGaleria() {
        const contenedor = document.getElementById('galeria-imagenes');
        const sinImagenes = document.getElementById('sin-imagenes');
        const btnDescargarTodo = document.getElementById('btn-descargar-todo');

        if (this.adjuntosCargados.length === 0) {
            contenedor.innerHTML = '';
            sinImagenes?.classList.remove('d-none');
            btnDescargarTodo.disabled = true;
            return;
        }

        sinImagenes?.classList.add('d-none');
        btnDescargarTodo.disabled = false;

        contenedor.innerHTML = this.adjuntosCargados.map(adjunto => {
            const fecha = new Date(adjunto.created_at).toLocaleDateString('es-CO');
            const usuario = adjunto.usuario?.nombre || 'Usuario';
            const puedeEliminar = this.estadoExamen !== 'ENTREGADO';

            return `
                <div class="col-md-3 col-sm-6">
                    <div class="imagen-card" onclick="GaleriaAdjuntos.verImagenCompleta(${adjunto.id})">
                        <img src="${adjunto.url_archivo}" alt="${adjunto.nombre_archivo}">
                        <div class="imagen-card-overlay">
                            <div class="mb-2">
                                <i class="fas fa-search-plus fa-2x"></i>
                            </div>
                            <small class="text-center px-2">${adjunto.nombre_archivo}</small>
                        </div>
                        ${puedeEliminar ? `
                        <div class="position-absolute top-0 end-0 p-2">
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="event.stopPropagation(); GaleriaAdjuntos.confirmarEliminar(${adjunto.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        ` : ''}
                        <div class="position-absolute bottom-0 start-0 p-2 bg-dark bg-opacity-75 text-white small" style="border-top-right-radius: 0.375rem;">
                            ${adjunto.tamano_mb}
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar me-1"></i>${fecha}
                        </small>
                        <small class="text-muted d-block">
                            <i class="fas fa-user me-1"></i>${usuario}
                        </small>
                    </div>
                </div>
            `;
        }).join('');
    },

    /**
     * Ver imagen en tamaño completo
     */
    verImagenCompleta(adjuntoId) {
        const adjunto = this.adjuntosCargados.find(a => a.id === adjuntoId);
        if (!adjunto) return;

        this.imagenActual = adjunto;
        this.resetZoom();

        document.getElementById('modalImagenTitulo').textContent = adjunto.nombre_archivo;
        document.getElementById('modalImagenVisor').src = adjunto.url_archivo;
        document.getElementById('modalImagenInfo').textContent =
            `${adjunto.tamano_mb} • Subido: ${new Date(adjunto.created_at).toLocaleString('es-CO')}`;

        const modal = new bootstrap.Modal(document.getElementById('modalImagenCompleta'));
        modal.show();
    },

    /**
     * Controles de zoom
     */
    zoomIn() {
        this.zoomLevel = Math.min(this.zoomLevel + 0.25, 3);
        this.aplicarZoom();
    },

    zoomOut() {
        this.zoomLevel = Math.max(this.zoomLevel - 0.25, 0.5);
        this.aplicarZoom();
    },

    resetZoom() {
        this.zoomLevel = 1;
        this.aplicarZoom();
    },

    aplicarZoom() {
        const img = document.getElementById('modalImagenVisor');
        img.style.transform = `scale(${this.zoomLevel})`;
    },

    /**
     * Descargar imagen individual
     */
    descargarImagen() {
        if (!this.imagenActual) return;
        window.location.href = `/servicio-examen/${this.servicioExamenId}/adjuntos/${this.imagenActual.id}/download`;
    },

    /**
     * Descargar todas las imágenes como ZIP
     */
    descargarTodo() {
        if (this.adjuntosCargados.length === 0) {
            this.mostrarNotificacion('No hay imágenes para descargar', 'info');
            return;
        }
        window.location.href = `/servicio-examen/${this.servicioExamenId}/adjuntos/download-all`;
    },

    /**
     * Confirmar eliminación
     */
    confirmarEliminar(adjuntoId) {
        if (!confirm('¿Está seguro de eliminar esta imagen?')) return;
        this.eliminarAdjunto(adjuntoId);
    },

    /**
     * Eliminar desde modal
     */
    eliminarImagenModal() {
        if (!this.imagenActual) return;

        if (!confirm('¿Está seguro de eliminar esta imagen?')) return;

        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('modalImagenCompleta')).hide();

        this.eliminarAdjunto(this.imagenActual.id);
    },

    /**
     * Eliminar adjunto
     */
    async eliminarAdjunto(adjuntoId) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Content-Type': 'application/json',
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/servicio-examen/${this.servicioExamenId}/adjuntos/${adjuntoId}`, {
                method: 'DELETE',
                headers: headers,
            });

            const data = await response.json();

            if (data.success) {
                this.mostrarNotificacion('Imagen eliminada exitosamente', 'success');
                await this.cargarAdjuntos();
            } else {
                this.mostrarNotificacion(data.message || 'Error al eliminar', 'danger');
            }

        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion('Error al eliminar la imagen', 'danger');
        }
    },

    /**
     * Actualizar contador
     */
    actualizarContador() {
        const contador = document.getElementById('contador-adjuntos');
        if (contador) {
            contador.textContent = this.adjuntosCargados.length;
        }
    },

    /**
     * Mostrar notificación
     */
    mostrarNotificacion(mensaje, tipo = 'info') {
        // Usar sistema de alertas de Bootstrap
        const alertas = document.querySelector('.container-fluid');
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alerta.style.zIndex = '9999';
        alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        alertas?.insertBefore(alerta, alertas.firstChild);

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    },
};
