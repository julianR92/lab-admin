# Lab-Admin: Sistema de Laboratorio Clinico

## Descripcion General

Sistema web para la gestion integral de un laboratorio clinico. Permite administrar pacientes, configurar examenes parametrizables, crear ordenes de servicio, capturar resultados con evaluacion automatica contra valores de referencia contextuales (genero, edad, condicion especial) y generar reportes PDF profesionales con firma digital.

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
   |--hasMany--> ExamenValorReferencia (valores sin parametro, a nivel examen)
   |--belongsTo--> CategoriaExamen

Empresa (singleton, datos del laboratorio)
User (autenticacion y auditoria)
```

### Tabla de Modelos

| Modelo | Tabla | Descripcion |
|--------|-------|-------------|
| User | users | Autenticacion y auditoria |
| Cliente | clientes | Pacientes (nombre, documento, genero, fecha_nacimiento, eps) |
| Empresa | empresa | Datos del laboratorio (nit, razon_social, logo, representante_*) - singleton |
| Profesional | profesionales | Bacteriologos/profesionales (firma_digital, registro_profesional) |
| CategoriaExamen | categoria_examen | Agrupacion de examenes (Hematologia, Quimica, etc.) |
| Examen | examen | Definicion de examenes con tipo_resultado |
| ExamenParametro | examen_parametros | Campos a capturar por examen (tipo_dato, formula, opciones) |
| ExamenValorReferencia | examen_valores_referencia | Rangos/valores de referencia contextuales |
| Servicio | servicio | Ordenes de laboratorio con pago |
| ServicioExamen | servicio_examen | Examenes dentro de una orden con estado |
| ResultadoExamen | resultados_examen | Valores capturados + evaluacion automatica |
| ResultadoAdjunto | resultado_adjuntos | Imagenes adjuntas a resultados |

---

## Configuracion y Parametrizacion de Examenes (MODULO CENTRAL)

La parametrizacion de examenes es el corazon del sistema. Un examen tiene **3 niveles jerarquicos** que se configuran independientemente:

```
Examen (cabecera)
  └── ExamenParametro[]  (campos a capturar)
        └── ExamenValorReferencia[]  (rangos/valores de referencia, contextuales)
```

Los valores de referencia tambien pueden definirse a nivel examen (sin parametro_id) cuando el examen tiene un solo resultado logico.

### Nivel 1: Examen (cabecera)

Tabla `examen` - Controlador `ExamenController`, vistas `examenes/{create,edit,show}.blade.php`.

**Campos clave:**
- `codigo` (unique, string 20) - Identificador corto del examen (ej: GLU, HEM3, URO)
- `nombre` (string 255) - Nombre descriptivo
- `categoria_id` - FK a categoria_examen
- **`tipo_resultado`** (enum) - **CRITICO: define la vista de captura y la logica de evaluacion**
- `unidad_medida` - Unidad base del examen (puede sobrescribirse en parametros)
- `tecnica` - Metodologia (ej: "Colorimetrico", "Ziehl-Neelsen")
- `muestra_requerida` - Tipo de muestra (Sangre, Orina, Esputo, etc.)
- `valor_total` / `valor_remision` - Precios
- `tiempo_entrega` - Horas (1-720)
- `requiere_ayuno` (boolean), `instrucciones_paciente` (text)
- `status` (boolean)

**Valores validos de `tipo_resultado`** (definidos en `ExamenController::getTiposResultado()`):
1. `NUMERICO_SIMPLE`
2. `NUMERICO_CATEGORIZADO`
3. `CUALITATIVO_SIMPLE`
4. `CUALITATIVO_MULTIPLE_OPCIONES`
5. `MULTIPLE_CALCULADO`
6. `TABLA_HEMATOLOGIA`
7. `TEXTO_DESCRIPTIVO`

**Nota:** El `tipo_resultado` NO se valida contra los parametros automaticamente; el administrador debe configurar parametros y valores de referencia coherentes con el tipo elegido.

### Nivel 2: ExamenParametro

Tabla `examen_parametros` - Controlador `ExamenParametroController`, modal AJAX en `examenes/show.blade.php`.

**Campos clave:**
- `examen_id` - FK al examen
- `nombre_parametro` - Etiqueta visible (ej: "Hemoglobina")
- `codigo_parametro` - Codigo unico por examen, **mayusculas + digitos + underscore** (regex `/^[A-Z0-9_]+$/`)
- `seccion` - Agrupacion para acordeones (ej: "Fisico", "Quimico", "Microscopico"). Solo letras y espacios
- **`tipo_dato`** (enum) - Define el control HTML y el campo donde se guarda el valor:
  - `DECIMAL` - Input numerico con decimales, se guarda en `valor_numerico`
  - `INTEGER` - Input numerico entero, se guarda en `valor_numerico`
  - `TEXT` - Input/textarea texto libre, se guarda en `valor_texto`
  - `SELECT` - Dropdown, se guarda en `valor_cualitativo`
- `unidad_medida` - Unidad especifica del parametro
- `decimales` (0-4) - Solo aplica para DECIMAL
- `orden` - Orden de visualizacion
- `requerido` (boolean) - Obligatorio en captura
- **`es_calculado`** (boolean) - Si true, el valor se calcula con `formula_calculo`
- **`formula_calculo`** (JSON) - REQUERIDO cuando `es_calculado=1`:
  ```json
  {
    "formula": "(P1 * P2) / P3",
    "parametros": ["P1", "P2", "P3"],
    "descripcion": "Texto opcional"
  }
  ```
- **`opciones_select`** (JSON array) - REQUERIDO cuando `tipo_dato=SELECT`:
  ```json
  ["NEGATIVO", "POSITIVO +", "POSITIVO ++", "POSITIVO +++"]
  ```
- `mostrar_todos_rangos` (boolean, default false) - Si true, en la vista de resultados y en el PDF se muestran TODOS los valores de referencia del parametro en lugar del unico rango donde cayo el valor. Util para examenes como HCG por semanas de gestacion
- `status` (boolean)

**Limpieza automatica en controlador** (`ExamenParametroController@store/update`):
- Si `es_calculado=0` -> limpia `formula_calculo`
- Si `tipo_dato != SELECT` -> limpia `opciones_select`
- Si `tipo_dato != DECIMAL` -> limpia `decimales`

### Nivel 3: ExamenValorReferencia

Tabla `examen_valores_referencia` - Controlador `ExamenValorReferenciaController`, modal AJAX en `examenes/show.blade.php`.

**Campos clave:**
- `examen_id` - FK obligatorio
- `parametro_id` - FK nullable. Si es NULL, el valor aplica al examen completo (util para examenes de 1 solo resultado)
- **`tipo_referencia`** (enum) - Define como se evalua:
  - `RANGO` - Valor numerico dentro de [valor_min, valor_max]
  - `CUALITATIVO` - Comparacion exacta contra `valor_cualitativo`
  - `CATEGORIZADO` - Asignacion de `categoria` segun rango o `operador`
  - `INFORMATIVO` - Solo muestra `descripcion`, no evalua
- **Filtros de contexto** (todos opcionales, permiten tener multiples valores por parametro):
  - `genero` (M/F/null) - null = aplica a ambos
  - `edad_min` / `edad_max` (0-120)
  - `condicion_especial` (string) - Embarazo, diabetes, etc.
- **Valores segun tipo_referencia:**
  - RANGO: `valor_min`, `valor_max` (ambos requeridos)
  - CUALITATIVO: `valor_cualitativo` (requerido)
  - CATEGORIZADO: `valor_min`, `valor_max`, `categoria` (requerido), `operador` opcional (<, <=, >, >=, ==)
  - INFORMATIVO: solo `descripcion`
- `orden` - Prioridad de evaluacion (menor numero = mayor prioridad)
- `status` (boolean)

**Limpieza automatica en controlador** (`ExamenValorReferenciaController@store/update`):
- Segun `tipo_referencia` se limpian los campos que no aplican para evitar basura en BD.

---

## Guia de Parametrizacion por Tipo de Examen

Esta seccion es la referencia para configurar un examen nuevo. Para cada `tipo_resultado` se describe: cuantos parametros crear, que `tipo_dato` usar, que valores de referencia definir, y la vista de captura que se usara automaticamente.

### 1. NUMERICO_SIMPLE
**Ejemplo:** Glicemia, Creatinina, TSH

- **Parametros:** 1 con `tipo_dato=DECIMAL` o `INTEGER`
- **Valores de referencia:** Uno o varios `tipo_referencia=RANGO` (puede haber varios para distintos genero/edad)
- **Vista de captura:** `resultados/tipos/estandar.blade.php` (1 input numerico)
- **Evaluacion:**
  - Si `valor < valor_min` -> `tipo_alerta=BAJO`, `fuera_rango=true`
  - Si `valor > valor_max` -> `tipo_alerta=ALTO`, `fuera_rango=true`
  - Valores muy extremos -> `CRITICO` + `requiere_revision=true`

### 2. NUMERICO_CATEGORIZADO
**Ejemplo:** Colesterol Total, Trigliceridos, Indice VIH

- **Parametros:** 1 con `tipo_dato=DECIMAL`
- **Valores de referencia:** Varios `tipo_referencia=CATEGORIZADO`, ordenados por `orden`. Cada uno con `categoria` (ej: "Optimo", "Intermedio Alto", "Alto")
- **Vista de captura:** `resultados/tipos/estandar.blade.php` (input numerico)
- **Evaluacion:** Itera por orden, asigna la categoria cuyo rango contiene el valor; guarda `categoria_asignada` y `rango_referencia` (ej: "200-239 mg/dL - Intermedio Alto")

### 3. CUALITATIVO_SIMPLE
**Ejemplo:** Hemoclasificacion, Prueba de Embarazo, Dengue IgM

- **Parametros:** 1-3 con `tipo_dato=SELECT` y `opciones_select` definido
- **Valores de referencia:** `tipo_referencia=CUALITATIVO` con `valor_cualitativo` = valor esperado (ej: "NEGATIVO")
- **Vista de captura:** `resultados/tipos/estandar.blade.php` (SELECT dropdowns)
- **Evaluacion:** Si `valor_cualitativo != valor_esperado` -> `tipo_alerta=ALTO`, `fuera_rango=true`

### 4. CUALITATIVO_MULTIPLE_OPCIONES
**Ejemplo:** Uroanalisis (Fisico, Quimico, Microscopico)

- **Parametros:** 5-50 organizados por `seccion`. Mezcla de `tipo_dato=SELECT` (con opciones) y `TEXT` (texto libre)
- **Valores de referencia:** Por parametro, tipicamente `CUALITATIVO` con el valor normal esperado
- **Vista de captura:** `resultados/tipos/cualitativo-multiple.blade.php` (acordeon por seccion)
- **Evaluacion:** Campo por campo contra su valor de referencia

### 5. MULTIPLE_CALCULADO
**Ejemplo:** Clearance Creatinina, Microalbuminuria, HOMA-IR

- **Parametros:**
  - 2+ con `es_calculado=false` (capturados manualmente)
  - 1+ con `es_calculado=true` y `formula_calculo` definido
- **Valores de referencia:** `RANGO` o `CATEGORIZADO`, especialmente sobre los parametros calculados
- **Vista de captura:** `resultados/tipos/multiple-parametros.blade.php` (inputs normales + readonly para calculados + boton "Calcular Ahora")
- **Evaluacion:**
  - Tras capturar manuales, se calculan los derivados via `ResultadoExamenController::calcularParametrosCalculados()`
  - La formula se evalua reemplazando `codigo_parametro` por valores. Regex de seguridad: `/^[0-9+\-*/().]+$/`
  - Cada parametro (manual y calculado) se evalua individualmente contra su valor de referencia
- **Validacion clave:** No se puede guardar si faltan manuales requeridos

### 6. TABLA_HEMATOLOGIA
**Ejemplo:** Hemograma III (Serie Roja, Serie Blanca, Plaquetas)

- **Parametros:** 15-30 con `tipo_dato=DECIMAL`, agrupados por `seccion`
- **Valores de referencia:** `RANGO` por parametro, tipicamente con filtros de `genero` (Hb, Hto difieren M/F) y `edad_min/edad_max`
- **Vista de captura:** `resultados/tipos/multiple-parametros.blade.php` (tabla compacta)
- **Evaluacion:** Cada parametro individualmente

### 7. TEXTO_DESCRIPTIVO
**Ejemplo:** Baciloscopia, Coloracion de Gram, Urocultivo

- **Parametros:** 0-5 (opcionales: SELECT/TEXT estructurados + descripciones libres)
- **Valores de referencia:** Tipicamente `INFORMATIVO` o ninguno (el profesional interpreta)
- **Vista de captura:** `resultados/tipos/texto-descriptivo.blade.php`:
  - Campos estructurados opcionales (SELECT/TEXT)
  - 3 TEXTAREAS en `resultados_examen`: `observaciones`, `interpretacion` (requerido), `conclusiones` (requerido)
- **Peculiaridad critica:** Si el examen NO tiene parametros configurados, `ResultadoExamenController@store` crea automaticamente uno llamado "Descripcion" con `codigo_parametro=DESC`, `tipo_dato=TEXT`
- **Evaluacion:** No aplica rangos. Siempre marca `requiere_revision=true`

---

## Flujo de Captura de Resultados

**Controlador:** `ResultadoExamenController`, **Vistas:** `resultados/create.blade.php` + `resultados/tipos/*.blade.php`

### 1. Validaciones previas (create)
- Debe existir `servicio_examen.profesional_id` (profesional asignado) para capturar
- Estado del examen debe permitir captura (PENDIENTE -> EN_PROCESO automaticamente, no ENTREGADO)
- Se calcula el `contexto` del paciente: `{genero, edad, condicion_especial}` desde `Cliente`

### 2. Renderizado dinamico
- `create.blade.php` hace `@include` del parcial segun `examen.tipo_resultado`
- Parametros se cargan agrupados por `seccion`
- Valores de referencia se filtran por contexto del paciente

### 3. Guardado (store, via AJAX POST con JSON)
```php
DB::beginTransaction()
foreach ($resultados as $parametroId => $data) {
    $resultado = ResultadoExamen::firstOrNew([...]);
    $this->asignarValor($resultado, $parametro, $data);  // segun tipo_dato
    $resultado->evaluar($contexto);                       // auto-evaluacion
    $resultado->save();
    if ($resultado->esCritico()) $warnings[] = ...;
}
$this->calcularParametrosCalculados($servicioExamen, $contexto);
$servicioExamen->update(['estado' => 'COMPLETADO', 'fecha_resultado' => now()]);
DB::commit()
```

### 4. Metodo `asignarValor()` - Mapea `tipo_dato` a columna
- `DECIMAL|INTEGER` -> `valor_numerico`
- `SELECT` -> `valor_cualitativo`
- `TEXT` -> `valor_texto`
- Textos descriptivos (TEXTO_DESCRIPTIVO) -> `observaciones`, `interpretacion`, `conclusiones`

### 5. Metodo `ResultadoExamen::evaluar($contexto)`
```php
$valorRef = $this->obtenerValorReferenciaAplicable($contexto);
// Filtra por parametro_id, genero, edad, condicion, ordena por 'orden'
// Para CATEGORIZADO: busca el rango donde cae el valor
$evaluacion = $valorRef->evaluarValor($this->valorPrincipal);
$this->fuera_rango = !$evaluacion['dentro_rango'];
$this->tipo_alerta = $evaluacion['tipo_alerta'];
$this->categoria_asignada = $evaluacion['categoria'];
$this->valor_referencia_id = $valorRef->id;
$this->rango_referencia = $valorRef->rango_texto;
if ($this->esCritico()) $this->requiere_revision = true;
```

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

Transiciones en `ServicioController@cambiarEstado`. Cada cambio actualiza timestamps: `fecha_toma_muestra`, `fecha_resultado`, `fecha_validacion`, `fecha_entrega`.

**`fecha_toma_muestra`:** Se asigna a `now()` al transicionar a EN_PROCESO (solo si es null). Editable manualmente desde `show.blade.php` via `ServicioController@actualizarFechaTomaMuestra` mientras el estado sea PENDIENTE/EN_PROCESO/COMPLETADO (`puedeEditarse()=true`). Request: `ActualizarFechaTomaMuestraRequest`.

---

## Sistema de Alertas

Constantes en `ResultadoExamen`: `ALERTA_NORMAL`, `ALERTA_BAJO`, `ALERTA_ALTO`, `ALERTA_CRITICO`.

| tipo_alerta | Condicion | Color Bootstrap | Icono | Accion |
|-------------|-----------|-----------------|-------|--------|
| NORMAL | Dentro del rango | success (verde) | check (✓) | Ninguna |
| BAJO | Debajo del minimo | info (azul) | arrow-down (↓) | Informativa |
| ALTO | Encima del maximo | warning (amarillo) | arrow-up (↑) | Importante |
| CRITICO | Muy fuera de rango | danger (rojo) | exclamation-triangle (⚠) | requiere_revision=true |

**Colores en PDF:**
- BAJO: fondo `#fff3cd`
- ALTO: fondo `#f8d7da`
- CRITICO: fondo `#dc3545` + texto blanco

---

## Modulos del Sistema

### 1. Autenticacion
**Controlador:** `Auth/LoginController`. Sin registro publico. Sesion 120 min. Recuperacion por email.
**Rutas:** `/login`, `/logout`, `/forgot-password`, `/reset-password/{token}`

### 2. Dashboard
**Vista:** `dashboard.blade.php`. Metricas: totalPacientes, examenesPendientes, resultadosValidados, ingresosHoy.

### 3. Clientes (Pacientes)
**Controlador:** `ClienteController` + `Api/ClienteController`
CRUD de pacientes. Campos: nombre, apellido, tipo_documento (CC/TI/CE/PA/RC), documento (unique), genero (M/F/O), fecha_nacimiento, telefono, email, ciudad, eps.
**Scopes:** `buscar()`, `porDocumento()`. **Accessors:** `nombreCompleto`, `edad`.
**Rutas:** `resource clientes`, `GET /api/clientes/buscar` (AJAX para selects).

### 4. Categorias de Examen
**Controlador:** `CategoriaExamenController`
Agrupacion logica de examenes. Campos: categoria, descripcion, status, orden. No se puede eliminar si tiene examenes asociados.

### 5. Examenes
Ver seccion completa arriba ("Configuracion y Parametrizacion de Examenes").
**Rutas:**
- `resource examenes`
- `POST/PUT/DELETE /examen-parametros/{id?}`
- `POST/PUT/DELETE /examen-valores-referencia/{id?}`

### 6. Profesionales
**Controlador:** `ProfesionalController`
Bacteriologos y profesionales. Campos: nombre, apellido, documento (unique), profesion, registro_profesional (unique), especialidad, firma_digital (max 2MB en `storage/public/firmas/`), telefono, email, status. No se elimina si tiene servicios o validaciones.

### 7. Empresa (Singleton)
**Controlador:** `EmpresaController`
Datos del laboratorio. Se crea por defecto si no existe. Campos: nit, razon_social, direccion, barrio, ciudad, telefono_uno, telefono_dos, email, logo (`storage/public/logos/`), representante_nombre/apellido/documento, representante_firma (`storage/public/firmas-representante/`).
Metodo `obtenerMembreteParaPDF()`.
**Rutas:** `GET|PUT /empresa/configuracion`, `DELETE /empresa/logo`.

### 8. Servicios (Ordenes)
**Controlador:** `ServicioController`
Al crear:
1. Seleccionar cliente + examenes (agrupados por categoria)
2. `numero_orden` automatico: `ORD-YYYYMMDD-XXXX`
3. `valor_total` = suma de precios
4. Un `ServicioExamen` por examen seleccionado
5. `estado_pago`: PENDIENTE/PARCIAL/PAGADO

**`show.blade.php`** es el hub: muestra examenes, asigna profesional, cambia estado, captura resultados, registra pagos, descarga PDFs.

**Rutas:**
- `resource servicios`
- `GET /servicios/{id}/orden-pdf`
- `POST /servicios/{id}/pago`
- `POST /servicio-examen/{id}/profesional`
- `POST /servicio-examen/{id}/fecha-toma-muestra`
- `POST /servicio-examen/{id}/estado`

### 9. Resultados de Examenes
Ver "Flujo de Captura de Resultados" arriba.
**Rutas:**
- `GET /servicio-examen/{id}/resultados/create`
- `POST /servicio-examen/{id}/resultados`
- `GET /servicio-examen/{id}/resultados`

### 10. Adjuntos de Resultados
**Controlador:** `ResultadoAdjuntoController`
Imagenes asociadas (ECG, radiografias). Max 3 archivos por examen. Formatos: jpg, jpeg, png, gif, webp (max 10MB). Se almacenan en `storage/public/examenes/{numeroOrden}/`.
**Rutas (JSON):** listar, subir, eliminar, download, download-all (ZIP), reordenar.

### 11. Generacion de PDF
**Controlador:** `ResultadoPdfController`
- **Orden de servicio** (`descargarOrden`): examenes solicitados con precios
- **Resultados** (`generarPdf`/`generarPdfIndividual`): reporte de examenes VALIDADOS con encabezado, datos paciente, parametros, rangos, alertas, textos descriptivos, imagenes, firma digital + fecha validacion, pie de pagina.

**DomPDF:** Papel carta, vertical, fuente Carlito, remote enabled.
**Archivo:** `Resultado_{documento}_{nombre}_{apellido}.pdf`

**Rutas:**
- `GET /servicios/{id}/resultados-pdf`
- `GET /servicios/{id}/examen/{servicioExamenId}/resultados-pdf`

### 12. Perfil de Usuario
**Controlador:** `PerfilController`
Edicion de nombre y contrasena. Requiere contrasena actual para cambiar.

---

## Tipos de Resultado - Tabla Resumen

| tipo_resultado | Vista de Captura | tipo_dato Tipico | tipo_referencia Tipico |
|----------------|------------------|------------------|------------------------|
| NUMERICO_SIMPLE | tipos/estandar.blade.php | DECIMAL/INTEGER | RANGO |
| NUMERICO_CATEGORIZADO | tipos/estandar.blade.php | DECIMAL | CATEGORIZADO |
| CUALITATIVO_SIMPLE | tipos/estandar.blade.php | SELECT | CUALITATIVO |
| CUALITATIVO_MULTIPLE_OPCIONES | tipos/cualitativo-multiple.blade.php | SELECT + TEXT | CUALITATIVO |
| MULTIPLE_CALCULADO | tipos/multiple-parametros.blade.php | DECIMAL + calculados | RANGO/CATEGORIZADO |
| TABLA_HEMATOLOGIA | tipos/multiple-parametros.blade.php | DECIMAL | RANGO (por genero/edad) |
| TEXTO_DESCRIPTIVO | tipos/texto-descriptivo.blade.php | TEXT (opcional) | INFORMATIVO o ninguno |

**Parciales compartidos:** `tipos/partials/fila-parametro.blade.php`

---

## Almacenamiento de Archivos

```
storage/app/public/
  logos/                  -> Logo de la empresa
  firmas/                 -> Firmas digitales de profesionales
  firmas-representante/   -> Firma del representante legal
  examenes/
    {numeroOrden}/        -> Imagenes adjuntas por orden
```

Acceso publico: symlink `public/storage/ -> storage/app/public/` (`php artisan storage:link`).

---

## Estructura de Directorios Clave

```
app/
  Http/
    Controllers/
      Api/ClienteController.php          # Busqueda AJAX de clientes
      Auth/LoginController.php           # Autenticacion
      CategoriaExamenController.php      # CRUD categorias
      ClienteController.php              # CRUD pacientes
      EmpresaController.php              # Config laboratorio
      ExamenController.php               # CRUD examenes + getTiposResultado()
      ExamenParametroController.php      # CRUD parametros + getTiposDato()
      ExamenValorReferenciaController.php# CRUD referencia + getTiposReferencia()
      PerfilController.php               # Perfil usuario
      ProfesionalController.php          # CRUD profesionales
      ResultadoAdjuntoController.php     # Adjuntos de resultados
      ResultadoExamenController.php      # Captura y evaluacion
      ResultadoPdfController.php         # Generacion PDF
      ServicioController.php             # Ordenes de laboratorio
    Requests/                            # 16 form requests
  Models/                                # 12 modelos Eloquent

resources/views/
  layouts/{app,auth}.blade.php
  dashboard.blade.php
  clientes/ categorias-examen/ examenes/ profesionales/
  servicios/{index,create,edit,show,orden-pdf,resultado-pdf}.blade.php
  resultados/
    {create,show}.blade.php
    partials/galeria-adjuntos.blade.php
    tipos/estandar.blade.php
    tipos/cualitativo-multiple.blade.php
    tipos/multiple-parametros.blade.php
    tipos/tabla-hematologica.blade.php
    tipos/texto-descriptivo.blade.php
    tipos/partials/fila-parametro.blade.php
  empresa/edit.blade.php
  perfil/edit.blade.php

routes/web.php                           # Todas las rutas
public/js/galeria-adjuntos.js            # JS galeria imagenes
```

---

## Dependencias

**PHP (composer.json):** `laravel/framework: ^12.0`, `barryvdh/laravel-dompdf: ^3.1`
**JS (package.json):** Bootstrap 5.3, jQuery 3.7, Font Awesome 6.4 (todos CDN en layout). Vite 7 + tailwindcss 4 (solo build - NO usar en Blade).

## Seeders

- `DatabaseSeeder` - Usuario test (test@example.com)
- `ClienteSeeder` - 50 pacientes + Juan Perez
- `EmpresaSeeder` - Laboratorio Clinico San Rafael (Bogota)

---

## Checklist: Crear un Examen Nuevo

1. **Crear categoria** (si no existe) en `/categorias-examen`
2. **Crear examen** en `/examenes/create`:
   - Definir `codigo`, `nombre`, `categoria_id`
   - Elegir **`tipo_resultado`** segun la tabla de arriba (CRITICO)
   - Completar precios, tiempo_entrega, muestra_requerida, tecnica
3. **Agregar parametros** desde `/examenes/{id}` (boton "Nuevo Parametro"):
   - Para cada campo a capturar: `nombre_parametro`, `codigo_parametro` (MAYUSCULAS), `tipo_dato`, `unidad_medida`, `orden`
   - Si es calculado: marcar `es_calculado` + definir JSON `formula_calculo`
   - Si es SELECT: definir JSON `opciones_select`
   - Si aplica: agrupar con `seccion`
4. **Agregar valores de referencia** desde `/examenes/{id}` (boton "Agregar Valor de Referencia"):
   - Asociar a `parametro_id` (o dejar null para el examen completo)
   - Elegir `tipo_referencia` acorde al `tipo_resultado`:
     - Numericos -> RANGO o CATEGORIZADO
     - Cualitativos -> CUALITATIVO
     - Textos descriptivos -> INFORMATIVO (opcional)
   - Definir contexto si aplica (`genero`, `edad_min`, `edad_max`, `condicion_especial`)
   - Asignar `orden` de prioridad (menor = mayor prioridad)
5. **Verificar en orden de prueba**: crear un servicio con un paciente, asignar profesional, capturar un resultado y confirmar que la evaluacion automatica marca correctamente `tipo_alerta` y `rango_referencia`.
