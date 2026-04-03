# Lab-Admin: Sistema de Laboratorio Clinico

## Descripcion General

Sistema web para la gestion integral de un laboratorio clinico. Permite administrar pacientes, configurar examenes parametrizables, crear ordenes de servicio, capturar resultados con evaluacion automatica contra valores de referencia, y generar reportes PDF profesionales.

**Stack tecnologico:**
- Backend: Laravel 12 (PHP), Eloquent ORM
- Frontend: Blade templates, Bootstrap 5.3, Font Awesome 6.4, jQuery 3.7
- PDF: barryvdh/laravel-dompdf v3.1
- Build: Vite 7 con Laravel Vite Plugin
- Testing: Pest 4
- BD: MySQL (configurado en .env, default SQLite en config)
- Zona horaria: America/Bogota

## Comandos Utiles

```bash
php artisan serve                    # Iniciar servidor
php artisan migrate                  # Ejecutar migraciones
php artisan db:seed                  # Seeders (clientes, empresa)
php artisan storage:link             # Symlink storage/app/public -> public/storage
npm run dev                          # Vite dev server
npm run build                        # Build produccion
vendor/bin/pint                      # Laravel Pint (code style)
vendor/bin/pest                      # Ejecutar tests
```

## Arquitectura

- **Sin capa de servicios**: La logica de negocio reside directamente en controladores y modelos
- **Sin Livewire**: Vistas Blade puras con jQuery para interacciones AJAX
- **Sin middleware custom**: Usa el stack por defecto de Laravel
- **Sin policies**: Autorizacion manejada directamente en controladores
- **Form Requests**: 16 clases de validacion en `app/Http/Requests/`
- **Layouts**: `layouts/app.blade.php` (autenticado) y `layouts/auth.blade.php` (login)

## Convenciones Importantes

- **Bootstrap 5 exclusivo en vistas**: NUNCA usar Tailwind CSS en archivos Blade
- **UI en espanol**: Todos los textos visibles al usuario estan en espanol
- **Respuestas AJAX**: Los endpoints de resultados y adjuntos retornan JSON
- **DataTables**: Listados principales usan jQuery DataTables con busqueda AJAX
- **Nombres de tabla singulares**: `servicio`, `examen`, `empresa` (no plurales)
- **Navbar**: Gradiente purpura/azul (#667eea a #764ba2)

---

## Modelos y Base de Datos (12 modelos)

### Relaciones Principales

```
Cliente --hasMany--> Servicio --hasMany--> ServicioExamen --hasMany--> ResultadoExamen
                                              |                            |
                                              |--hasMany--> ResultadoAdjunto
                                              |--belongsTo--> Examen
                                              |--belongsTo--> Profesional
                                              
Examen --hasMany--> ExamenParametro --hasMany--> ExamenValorReferencia
   |--belongsTo--> CategoriaExamen

Empresa (singleton, datos del laboratorio)
User (autenticacion y auditoria)
```

### Tabla de Modelos

| Modelo | Tabla | Descripcion |
|--------|-------|-------------|
| User | users | Autenticacion y auditoria |
| Cliente | clientes | Pacientes (nombre, documento, genero, fecha_nacimiento, eps) |
| Empresa | empresa | Datos del laboratorio (nit, razon_social, logo, representante_nombre, representante_apellido, representante_documento, representante_firma) - singleton |
| Profesional | profesionales | Bacteriologos/profesionales (firma_digital, registro_profesional) |
| CategoriaExamen | categoria_examen | Agrupacion de examenes (Hematologia, Quimica, etc.) |
| Examen | examen | Definicion de examenes con tipo_resultado |
| ExamenParametro | examen_parametros | Campos a capturar por examen (tipo_dato, formula, opciones) |
| ExamenValorReferencia | examen_valores_referencia | Rangos normales contextuales (genero, edad) |
| Servicio | servicio | Ordenes de laboratorio con pago |
| ServicioExamen | servicio_examen | Examenes dentro de una orden con estado |
| ResultadoExamen | resultados_examen | Valores capturados y evaluacion automatica |
| ResultadoAdjunto | resultado_adjuntos | Imagenes adjuntas a resultados |

---

## Modulos

### 1. Autenticacion

**Controlador:** `Auth/LoginController`
**Vistas:** `auth/login.blade.php`, `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`

Login con sesion en BD (120 min). Sin registro publico. Recuperacion de contrasena por email.

**Rutas:**
- `GET/POST /login` - Iniciar sesion
- `POST /logout` - Cerrar sesion
- `GET/POST /forgot-password` - Recuperar contrasena
- `GET/POST /reset-password/{token}` - Restablecer contrasena

---

### 2. Dashboard

**Vista:** `dashboard.blade.php`
**Ruta:** `GET /dashboard`

Metricas principales: totalPacientes, examenesPendientes, resultadosValidados, ingresosHoy.

---

### 3. Clientes (Pacientes)

**Controlador:** `ClienteController` + `Api/ClienteController`
**Vistas:** `clientes/index|create|edit|show.blade.php`
**Requests:** `StoreClienteRequest`, `UpdateClienteRequest`

CRUD completo de pacientes. Campos clave: nombre, apellido, tipo_documento (CC/TI/CE/PA/RC), documento (unique), genero (M/F/O), fecha_nacimiento, telefono, email, ciudad, eps.

**Scopes:** `buscar()` (nombre/documento), `porDocumento()`
**Accessors:** `nombreCompleto`, `edad` (calculada desde fecha_nacimiento)

**Rutas:**
- `resource clientes` - CRUD estandar
- `GET /api/clientes/buscar` - Busqueda AJAX para selects

---

### 4. Categorias de Examen

**Controlador:** `CategoriaExamenController`
**Vistas:** `categorias-examen/index|create|edit|show.blade.php`
**Requests:** `StoreCategoriaExamenRequest`, `UpdateCategoriaExamenRequest`

Agrupacion logica de examenes (Hematologia, Quimica Sanguinea, Uroanalisis, etc.). Campos: categoria, descripcion, status, orden. No se puede eliminar si tiene examenes asociados.

---

### 5. Examenes

**Controlador:** `ExamenController`, `ExamenParametroController`, `ExamenValorReferenciaController`
**Vistas:** `examenes/index|create|edit|show.blade.php`
**Requests:** 6 form requests (Store/Update para Examen, Parametro, ValorReferencia)

Modulo central de configuracion. Un examen tiene:

**Nivel Examen:**
- `tipo_resultado`: Define la vista de captura (ver seccion Tipos de Resultado)
- `codigo` (unique), `nombre`, `categoria_id`
- `unidad_medida`, `tecnica`, `muestra_requerida`
- `valor_total`, `valor_remision`, `tiempo_entrega`
- `requiere_ayuno`, `instrucciones_paciente`

**Nivel Parametro (ExamenParametro):**
- `nombre_parametro`, `codigo_parametro` (unique por examen)
- `tipo_dato`: DECIMAL, INTEGER, TEXT, SELECT
- `seccion`: Agrupa parametros en acordeones
- `es_calculado` + `formula_calculo` (JSON: `{formula, parametros}`)
- `opciones_select` (JSON array para tipo SELECT)
- `unidad_medida`, `decimales`, `orden`, `requerido`
- `mostrar_todos_rangos` (BOOLEAN, default false): Si true, en la vista de resultados y en el PDF se muestran TODOS los valores de referencia del parametro en vez del unico rango donde cayo el valor. Util para examenes como HCG (hormona del embarazo) donde el medico necesita ver todos los rangos por semana de gestacion

**Nivel Valor de Referencia (ExamenValorReferencia):**
- `tipo_referencia`: RANGO, CUALITATIVO, CATEGORIZADO, INFORMATIVO
- Contexto: `genero` (M/F/null), `edad_min`, `edad_max`, `condicion_especial`
- Para RANGO: `valor_min`, `valor_max`, `operador`
- Para CUALITATIVO: `valor_cualitativo`
- Para CATEGORIZADO: `categoria`, `valor_min`, `valor_max`
- `orden` (prioridad de evaluacion)

**Rutas:**
- `resource examenes` - CRUD
- `POST/GET/PUT/DELETE /examen-parametros/{id?}` - CRUD parametros
- `POST/GET/PUT/DELETE /examen-valores-referencia/{id?}` - CRUD valores referencia

---

### 6. Profesionales

**Controlador:** `ProfesionalController`
**Vistas:** `profesionales/index|create|edit|show.blade.php`
**Requests:** `StoreProfesionalRequest`, `UpdateProfesionalRequest`

Registro de bacteriologos y profesionales de salud. Campos clave: nombre, apellido, documento (unique), profesion, registro_profesional (unique), especialidad, firma_digital (imagen max 2MB almacenada en `storage/public/firmas/`), telefono, email, status.

No se puede eliminar si tiene servicios asignados o resultados validados.

---

### 7. Empresa (Configuracion)

**Controlador:** `EmpresaController`
**Vista:** `empresa/edit.blade.php`
**Request:** `UpdateEmpresaRequest`

Configuracion del laboratorio (singleton). Se crea registro por defecto si no existe. Campos: nit, razon_social, direccion, barrio, ciudad, telefono_uno, telefono_dos, email, logo (imagen en `storage/public/logos/`), representante_nombre, representante_apellido, representante_documento, representante_firma (imagen en `storage/public/firmas-representante/`).

Metodo `obtenerMembreteParaPDF()` para encabezado de PDFs.

**Rutas:**
- `GET /empresa/configuracion` - Formulario
- `PUT /empresa/configuracion` - Actualizar
- `DELETE /empresa/logo` - Eliminar logo

---

### 8. Servicios (Ordenes de Laboratorio)

**Controlador:** `ServicioController`
**Vistas:** `servicios/index|create|edit|show.blade.php`, `servicios/orden-pdf.blade.php`
**Requests:** `StoreServicioRequest`, `UpdateServicioRequest`

Ordenes de trabajo del laboratorio. Al crear un servicio:
1. Se selecciona cliente y examenes (agrupados por categoria)
2. Se genera `numero_orden` automatico: `ORD-YYYYMMDD-XXXX`
3. Se calcula `valor_total` sumando precios de examenes
4. Se crea un `ServicioExamen` por cada examen seleccionado
5. Se determina `estado_pago`: PENDIENTE, PARCIAL, PAGADO

**Vista show.blade.php** es el hub principal: muestra examenes, permite asignar profesional, cambiar estado, capturar resultados, registrar pagos y descargar PDFs.

**Rutas:**
- `resource servicios` - CRUD
- `GET /servicios/{id}/orden-pdf` - PDF de la orden
- `POST /servicios/{id}/pago` - Registrar pago
- `POST /servicio-examen/{id}/profesional` - Asignar profesional
- `POST /servicio-examen/{id}/fecha-toma-muestra` - Actualizar fecha toma muestra manualmente
- `POST /servicio-examen/{id}/estado` - Cambiar estado

---

### 9. Resultados de Examenes

**Controlador:** `ResultadoExamenController`
**Vistas:** `resultados/create.blade.php`, `resultados/show.blade.php`, `resultados/tipos/*.blade.php`

Modulo mas complejo del sistema. Captura valores segun el `tipo_resultado` del examen y evalua automaticamente contra valores de referencia.

**Flujo de captura:**
1. `create()` carga parametros agrupados por seccion y valores de referencia aplicables al paciente
2. Se renderiza la vista de tipo correspondiente (ver Tipos de Resultado)
3. `store()` recibe JSON via AJAX, itera parametros, asigna valores, evalua, guarda
4. Si hay alertas CRITICAS, retorna warnings en la respuesta JSON
5. Cambia estado del ServicioExamen a COMPLETADO

**Metodo `asignarValor()`**: Asigna el valor al campo correcto segun `tipo_dato`:
- DECIMAL/INTEGER -> `valor_numerico`
- SELECT -> `valor_cualitativo`
- TEXT/TEXTO_LARGO -> `valor_texto`

**Metodo `evaluar()`** en ResultadoExamen:
1. Busca el valor de referencia aplicable (filtrado por genero/edad del paciente)
2. Compara el valor capturado contra el rango/cualitativo/categoria
3. Asigna `tipo_alerta`: NORMAL, BAJO, ALTO, CRITICO
4. Marca `fuera_rango = true` si aplica
5. Marca `requiere_revision = true` si es CRITICO

**Rutas:**
- `GET /servicio-examen/{id}/resultados/create` - Formulario de captura
- `POST /servicio-examen/{id}/resultados` - Guardar (JSON)
- `GET /servicio-examen/{id}/resultados` - Ver resultados

---

### 10. Adjuntos de Resultados

**Controlador:** `ResultadoAdjuntoController`
**Vista parcial:** `resultados/partials/galeria-adjuntos.blade.php`
**JS:** `public/js/galeria-adjuntos.js`
**Request:** `StoreResultadoAdjuntoRequest`

Imagenes asociadas a examenes (ECG, radiografias, etc.). Max 3 archivos por examen. Formatos: jpg, jpeg, png, gif, webp (max 10MB). Se almacenan en `storage/public/examenes/{numeroOrden}/`.

**Rutas (todas retornan JSON):**
- `GET /servicio-examen/{id}/adjuntos` - Listar
- `POST /servicio-examen/{id}/adjuntos` - Subir imagen
- `DELETE /servicio-examen/{id}/adjuntos/{adjuntoId}` - Eliminar
- `GET /servicio-examen/{id}/adjuntos/{adjuntoId}/download` - Descargar
- `GET /servicio-examen/{id}/adjuntos/download-all` - Descargar ZIP
- `POST /servicio-examen/{id}/adjuntos/orden` - Reordenar

---

### 11. Generacion de PDF

**Controlador:** `ResultadoPdfController`
**Vista:** `servicios/resultado-pdf.blade.php`, `servicios/orden-pdf.blade.php`

Dos tipos de PDF:
1. **Orden de servicio** (`descargarOrden`): Lista de examenes solicitados con precios
2. **Resultados** (`generarPdf`/`generarPdfIndividual`): Reporte completo de resultados validados

**PDF de Resultados incluye:**
- Encabezado con logo y datos de la empresa
- Datos del paciente y la orden
- Por cada examen VALIDADO: tabla de parametros, valores, unidades, rangos de referencia
- Alertas visuales (colores de fondo para BAJO/ALTO/CRITICO)
- Textos descriptivos (observaciones, interpretacion, conclusiones)
- Imagenes adjuntas
- Firma digital del profesional con `fecha_validacion` debajo del nombre
- Pie de pagina con fecha de generacion y numero de pagina

**Configuracion DomPDF:** Papel carta, vertical, fuente Carlito, remote enabled.
**Nombre archivo:** `Resultado_{documento}_{nombre}_{apellido}.pdf`

**Rutas:**
- `GET /servicios/{id}/resultados-pdf` - PDF completo del servicio
- `GET /servicios/{id}/examen/{servicioExamenId}/resultados-pdf` - PDF individual

---

### 12. Perfil de Usuario

**Controlador:** `PerfilController`
**Vista:** `perfil/edit.blade.php`

Edicion de nombre y contrasena del usuario autenticado. Requiere contrasena actual para cambiar contrasena.

**Rutas:**
- `GET /perfil` - Formulario
- `PUT /perfil` - Actualizar

---

## Tipos de Resultado de Examenes

El campo `examen.tipo_resultado` determina que vista Blade se usa para capturar resultados:

| Tipo | Vista | Descripcion | Ejemplo |
|------|-------|-------------|---------|
| NUMERICO_SIMPLE | `tipos/estandar.blade.php` | 1 valor numerico con rango | Glicemia |
| NUMERICO_CATEGORIZADO | `tipos/estandar.blade.php` | 1 valor numerico con categorias | Colesterol |
| CUALITATIVO_SIMPLE | `tipos/estandar.blade.php` | 1-3 SELECTs cualitativos | Hemoclasificacion |
| CUALITATIVO_MULTIPLE_OPCIONES | `tipos/cualitativo-multiple.blade.php` | 5-50 campos en secciones/acordeones | Uroanalisis |
| MULTIPLE_CALCULADO | `tipos/multiple-parametros.blade.php` | Valores manuales + calculados con formulas | Clearance Creatinina |
| TABLA_HEMATOLOGIA | `tipos/multiple-parametros.blade.php` | 15-30 valores numericos en tabla | Hemograma III |
| TEXTO_DESCRIPTIVO | `tipos/texto-descriptivo.blade.php` | Texto libre: observaciones, interpretacion, conclusiones | Baciloscopia |

**Parciales compartidos:**
- `tipos/partials/fila-parametro.blade.php` - Fila individual de parametro

---

## Maquina de Estados (ServicioExamen)

```
PENDIENTE -> EN_PROCESO -> COMPLETADO -> VALIDADO -> ENTREGADO
```

| Estado | Significado | Puede editar resultados | Puede generar PDF |
|--------|-------------|------------------------|-------------------|
| PENDIENTE | Sin procesar | No (no hay) | No |
| EN_PROCESO | Profesional asignado, capturando | Si | No |
| COMPLETADO | Resultados capturados | Si | No |
| VALIDADO | Aprobado por profesional | No (solo admin) | Si |
| ENTREGADO | Entregado al paciente | No | Si |

Transiciones controladas en `ServicioController@cambiarEstado`. Cada cambio actualiza timestamps: `fecha_toma_muestra`, `fecha_resultado`, `fecha_validacion`, `fecha_entrega`.

**`fecha_toma_muestra`**: Se asigna automaticamente a `now()` al transicionar a EN_PROCESO (solo si es null). Tambien es editable manualmente desde `show.blade.php` via `ServicioController@actualizarFechaTomaMuestra` mientras el estado sea PENDIENTE, EN_PROCESO o COMPLETADO (`puedeEditarse() = true`). Request: `ActualizarFechaTomaMuestraRequest`.

---

## Sistema de Alertas

Evaluacion automatica al guardar resultados. Se ejecuta en `ResultadoExamen::evaluar()`.

| tipo_alerta | Condicion | Color Bootstrap | Icono | Accion |
|-------------|-----------|-----------------|-------|--------|
| NORMAL | Dentro del rango | success (verde) | check | Ninguna |
| BAJO | Debajo del minimo | info (azul) | arrow-down | Informativa |
| ALTO | Encima del maximo | warning (amarillo) | arrow-up | Importante |
| CRITICO | Muy fuera de rango | danger (rojo) | exclamation-triangle | requiere_revision=true |

**Colores en PDF:**
- BAJO: fondo `#fff3cd`, simbolo down-arrow
- ALTO: fondo `#f8d7da`, simbolo up-arrow
- CRITICO: fondo `#dc3545` con texto blanco, simbolo warning

---

## Almacenamiento de Archivos

```
storage/app/public/
  logos/                  -> Logo de la empresa
  firmas/                 -> Firmas digitales de profesionales
  firmas-representante/   -> Firma del representante legal de la empresa
  examenes/
    {numeroOrden}/        -> Imagenes adjuntas por orden
```

Acceso publico via symlink: `public/storage/ -> storage/app/public/`

---

## Estructura de Directorios Clave

```
app/
  Http/
    Controllers/
      Api/ClienteController.php        # Busqueda AJAX de clientes
      Auth/LoginController.php          # Autenticacion
      CategoriaExamenController.php     # CRUD categorias
      ClienteController.php             # CRUD pacientes
      EmpresaController.php             # Config laboratorio
      ExamenController.php              # CRUD examenes
      ExamenParametroController.php     # CRUD parametros de examen
      ExamenValorReferenciaController.php # CRUD valores referencia
      PerfilController.php              # Perfil usuario
      ProfesionalController.php         # CRUD profesionales
      ResultadoAdjuntoController.php    # Adjuntos de resultados
      ResultadoExamenController.php     # Captura y evaluacion de resultados
      ResultadoPdfController.php        # Generacion PDF
      ServicioController.php            # Ordenes de laboratorio
    Requests/                           # 16 form requests de validacion
  Models/                               # 12 modelos Eloquent
  Providers/AppServiceProvider.php      # Vacio (sin registros custom)

resources/views/
  layouts/app.blade.php                 # Layout principal (Bootstrap 5)
  layouts/auth.blade.php                # Layout login (glassmorphic)
  dashboard.blade.php                   # Panel de metricas
  clientes/                             # index, create, edit, show
  categorias-examen/                    # index, create, edit, show
  examenes/                             # index, create, edit, show
  profesionales/                        # index, create, edit, show
  servicios/                            # index, create, edit, show, orden-pdf, resultado-pdf
  resultados/                           # create, show
    partials/galeria-adjuntos.blade.php
    tipos/estandar.blade.php
    tipos/cualitativo-multiple.blade.php
    tipos/multiple-parametros.blade.php
    tipos/tabla-hematologica.blade.php
    tipos/texto-descriptivo.blade.php
    tipos/partials/fila-parametro.blade.php
  empresa/edit.blade.php
  perfil/edit.blade.php

routes/web.php                          # Todas las rutas (auth + protected)
public/js/galeria-adjuntos.js           # JS para galeria de imagenes
```

---

## Dependencias Principales

**PHP (composer.json):**
- `laravel/framework: ^12.0`
- `barryvdh/laravel-dompdf: ^3.1`

**JS (package.json):**
- Bootstrap 5.3 (CDN en layout)
- jQuery 3.7 (CDN en layout)
- Font Awesome 6.4 (CDN en layout)
- Vite 7 + tailwindcss 4 (solo build, NO usar en Blade)

---

## Seeders Disponibles

- `DatabaseSeeder` - Crea usuario test (test@example.com)
- `ClienteSeeder` - 50 pacientes de prueba + 1 especifico (Juan Perez)
- `EmpresaSeeder` - Laboratorio Clinico San Rafael (Bogota)
