# 📄 Documentación: Generación de PDF de Resultados de Laboratorio

## 🎯 Descripción General

Sistema completo de generación de reportes en PDF para resultados de exámenes de laboratorio clínico. El PDF incluye:

- ✅ Información completa del laboratorio (encabezado con logo)
- ✅ Datos del paciente y de la orden
- ✅ Resultados de todos los exámenes validados
- ✅ Tabla de parámetros con valores, unidades y rangos de referencia
- ✅ Alertas visuales para valores fuera de rango (bajo, alto, crítico)
- ✅ Interpretación, observaciones y conclusiones
- ✅ Imágenes adjuntas (ECG, radiografías, etc.)
- ✅ Firma digital del profesional responsable
- ✅ Diseño profesional siguiendo estándares de laboratorios clínicos

---

## 📁 Archivos Implementados

### 1. Controlador: `ResultadoPdfController.php`
**Ubicación:** `app/Http/Controllers/ResultadoPdfController.php`

**Método principal:** `generarPdf(Request $request, $servicioId)`

**Funcionalidad:**
- Carga el servicio con todas sus relaciones (cliente, exámenes, resultados, adjuntos, profesionales)
- Filtra solo los exámenes con estado `VALIDADO`
- Obtiene los datos de la empresa para el encabezado
- Genera el PDF usando DomPDF
- Retorna el PDF para descarga o visualización

**Características:**
```php
// Solo genera PDF si hay exámenes validados
if ($servicio->serviciosExamen->isEmpty()) {
    abort(404, 'No hay resultados validados para generar el PDF');
}

// Configuración de DomPDF
$pdf->setPaper('letter', 'portrait');
$pdf->setOption('enable_remote', true);
$pdf->setOption('isHtml5ParserEnabled', true);
```

---

### 2. Vista Blade: `resultado-pdf.blade.php`
**Ubicación:** `resources/views/servicios/resultado-pdf.blade.php`

**Secciones del PDF:**

#### a) ENCABEZADO (en todas las páginas)
- Logo del laboratorio
- Nombre comercial (razón social)
- NIT, dirección, ciudad
- Teléfonos y email

#### b) TÍTULO PRINCIPAL
- "RESULTADOS DE LABORATORIO" con fondo azul destacado

#### c) DATOS DE LA ORDEN
- Número de orden
- Fecha del servicio

#### d) DATOS DEL PACIENTE
- Nombre completo
- Tipo y número de documento
- Sexo y fecha de nacimiento
- Edad calculada
- Teléfono, email, dirección
- EPS

#### e) POR CADA EXAMEN VALIDADO:

**Encabezado del Examen:**
- Nombre del examen
- Código y categoría
- Estado (✓ VALIDADO)

**Metadatos:**
- Muestra requerida
- Técnica utilizada
- Fechas de toma de muestra y resultado

**Tabla de Resultados:**
```
┌─────────────────────────────────────────────────────────────┐
│ PARÁMETRO    │ RESULTADO │ UNIDAD │ VALORES DE REFERENCIA │
├─────────────────────────────────────────────────────────────┤
│ Hemoglobina  │  14.5 ↑  │ g/dL   │ 12.0-16.0              │
│ Leucocitos   │   7.2    │ K/uL   │ 4.5-11.0               │
└─────────────────────────────────────────────────────────────┘
```

**Alertas Visuales:**
- 🟡 **Amarillo** = BAJO (↓)
- 🔴 **Rojo claro** = ALTO (↑)
- 🔴 **Rojo intenso** = CRÍTICO (⚠)

**Textos Descriptivos:**
- Observaciones
- Interpretación
- Conclusión

**Imágenes Adjuntas:**
- Muestra todas las imágenes asociadas al examen
- Incluye descripción si existe
- Formato centrado y con borde

**Firma del Profesional:**
- Nombre completo y profesión
- Especialidad (si existe)
- Registro profesional
- Documento de identidad
- Teléfono y email
- Fecha de validación
- Imagen de firma digital (si existe)

#### f) PIE DE PÁGINA
- Fecha y hora de generación
- Nombre del laboratorio
- Número de página

---

### 3. Ruta Web
**Archivo:** `routes/web.php`

```php
Route::get('servicios/{servicio}/resultados-pdf', 
    [ResultadoPdfController::class, 'generarPdf'])
    ->name('servicios.resultados-pdf');
```

**URL de ejemplo:**
```
/servicios/123/resultados-pdf
```

---

### 4. Botón en Vista Show
**Archivo:** `resources/views/servicios/show.blade.php`

Se agregó un botón que solo aparece si hay exámenes validados:

```blade
@if($servicio->serviciosExamen->where('estado', 'VALIDADO')->count() > 0)
<a href="{{ route('servicios.resultados-pdf', $servicio) }}" 
   class="btn btn-info me-2" target="_blank">
    <i class="fas fa-file-medical-alt me-2"></i>Resultados PDF
</a>
@endif
```

---

## 🎨 Diseño y Estilos

### Paleta de Colores
- **Principal:** `#2c3e50` (azul oscuro)
- **Secundario:** `#3498db` (azul claro)
- **Alertas:**
  - Normal: Sin color
  - Bajo: `#fff3cd` (amarillo claro)
  - Alto: `#f8d7da` (rojo claro)
  - Crítico: `#dc3545` (rojo intenso)

### Tipografía
- **Fuente:** Arial, Helvetica, sans-serif
- **Tamaño base:** 9pt
- **Títulos:** 11-14pt
- **Encabezados de tabla:** 8pt bold

### Formato de Página
- **Tamaño:** Carta (Letter)
- **Orientación:** Vertical (Portrait)
- **Márgenes:** Estándar

---

## 🔍 Tipos de Resultado Soportados

El PDF maneja dinámicamente todos los tipos de exámenes:

1. ✅ **NUMERICO_SIMPLE** - Tabla con valores numéricos
2. ✅ **NUMERICO_CATEGORIZADO** - Tabla con categorías automáticas
3. ✅ **CUALITATIVO_SIMPLE** - Tabla con valores SELECT
4. ✅ **CUALITATIVO_REACTIVO** - Tabla con reactivo + valor numérico
5. ✅ **CUALITATIVO_MULTIPLE_OPCIONES** - Tabla con múltiples SELECT
6. ✅ **MULTIPLE_CALCULADO** - Tabla con valores manuales y calculados
7. ✅ **TABLA_HEMATOLOGIA** - Tabla grande con 15-25 parámetros
8. ✅ **TEXTO_DESCRIPTIVO** - Solo textos (observaciones, interpretación)

---

## 🖼️ Manejo de Imágenes

### Adjuntos
Las imágenes se cargan desde:
```
storage/app/public/{ruta_archivo}
```

**Formatos soportados:**
- JPG/JPEG
- PNG
- PDF (se convierte a imagen)

**Restricciones:**
- Ancho máximo: 100% del ancho de página
- Alto máximo: 400px

### Firma Digital
La firma se carga desde:
```
storage/app/public/{firma_digital}
```

**Especificaciones:**
- Tamaño recomendado: 150x60px
- Fondo transparente (PNG)
- Formato: PNG, JPG

---

## 📊 Sistema de Alertas

### Lógica de Evaluación
El PDF lee los campos de la tabla `resultados_examen`:

- `fuera_rango` (BOOLEAN): ¿Está fuera del rango normal?
- `tipo_alerta` (ENUM): NORMAL, BAJO, ALTO, CRITICO
- `rango_referencia` (VARCHAR): Texto del rango (ej: "70-100 mg/dL")

### Símbolos de Alerta
- **↓** = Valor BAJO
- **↑** = Valor ALTO
- **⚠** = Valor CRÍTICO

### Colores de Fondo
Los valores fuera de rango se destacan con colores de fondo:
```css
.alerta-bajo    { background-color: #fff3cd; }
.alerta-alto    { background-color: #f8d7da; }
.alerta-critico { background-color: #dc3545; color: white; }
```

---

## 🔐 Validación y Seguridad

### Requisitos para Generar PDF
1. ✅ El servicio debe existir
2. ✅ Debe tener al menos 1 examen con estado `VALIDADO`
3. ✅ El usuario debe estar autenticado (middleware `auth`)

### Estado de Exámenes
Solo se incluyen exámenes con:
```php
'estado' => 'VALIDADO'
```

Los estados `PENDIENTE`, `EN_PROCESO`, `COMPLETADO` NO se incluyen.

---

## 💻 Uso del Sistema

### Para el Usuario Final

1. **Ir al Servicio:**
   - Navegar a `Servicios` → Seleccionar una orden

2. **Validar Exámenes:**
   - Asegurarse de que los exámenes estén en estado `VALIDADO`

3. **Generar PDF:**
   - Hacer clic en el botón `Resultados PDF`
   - El PDF se abrirá en una nueva pestaña
   - Se puede descargar o imprimir

### Para el Desarrollador

**Personalizar el Diseño:**
Editar `resources/views/servicios/resultado-pdf.blade.php`

**Modificar Lógica:**
Editar `app/Http/Controllers/ResultadoPdfController.php`

**Agregar Nuevos Campos:**
```blade
<div class="datos-row">
    <div class="datos-label">Nuevo Campo:</div>
    <div class="datos-value">{{ $servicio->nuevo_campo }}</div>
</div>
```

**Cambiar Formato de Página:**
```php
$pdf->setPaper('legal', 'landscape'); // Oficio horizontal
```

---

## 📝 Ejemplos de Uso

### Generar PDF Simple
```php
// En el navegador
/servicios/123/resultados-pdf
```

### Generar PDF con Agrupación (Futuro)
```php
// Con parámetro de consulta
/servicios/123/resultados-pdf?agrupar=true
```

---

## 🐛 Solución de Problemas

### PDF no se genera
**Problema:** Error 404 "No hay resultados validados"
**Solución:** Verificar que al menos un examen tenga estado `VALIDADO`

### Imágenes no aparecen
**Problema:** Imágenes adjuntas o firma no se muestran
**Solución:** 
- Verificar que las rutas sean correctas
- Ejecutar: `php artisan storage:link`
- Verificar permisos de `storage/app/public/`

### Logo del laboratorio no aparece
**Problema:** Logo no visible en encabezado
**Solución:**
- Verificar que existe un registro en la tabla `empresa`
- Verificar que el campo `logo` tenga una ruta válida
- Ejecutar: `php artisan storage:link`

### Formato de texto roto
**Problema:** Textos largos se cortan o salen del PDF
**Solución:** Ajustar CSS en la vista `.blade.php`:
```css
word-wrap: break-word;
overflow-wrap: break-word;
```

---

## 🔄 Flujo de Generación de PDF

```
┌─────────────────┐
│  Usuario hace   │
│  clic en botón  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Controlador    │
│  valida servicio│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Carga datos:   │
│  - Servicio     │
│  - Cliente      │
│  - Exámenes     │
│  - Resultados   │
│  - Adjuntos     │
│  - Profesional  │
│  - Empresa      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Renderiza      │
│  vista Blade    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  DomPDF genera  │
│  archivo PDF    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Usuario recibe │
│  PDF para ver   │
│  o descargar    │
└─────────────────┘
```

---

## 📦 Dependencias

### Paquetes de Composer
```json
{
    "barryvdh/laravel-dompdf": "^3.0"
}
```

### Instalación (si no está instalado)
```bash
composer require barryvdh/laravel-dompdf
```

---

## ✅ Checklist de Implementación

- [x] Controlador `ResultadoPdfController` creado
- [x] Vista Blade `resultado-pdf.blade.php` creada
- [x] Ruta agregada en `web.php`
- [x] Botón agregado en vista `show.blade.php`
- [x] Estilos CSS implementados
- [x] Manejo de alertas visuales
- [x] Soporte para imágenes adjuntas
- [x] Soporte para firma digital
- [x] Código formateado con Laravel Pint
- [x] Documentación completa

---

## 🎓 Notas Técnicas

### Relaciones Eloquent Utilizadas
```php
$servicio->cliente
$servicio->serviciosExamen->examen
$servicio->serviciosExamen->profesional
$servicio->serviciosExamen->resultados
$servicio->serviciosExamen->adjuntos
```

### Eager Loading Optimizado
```php
Servicio::with([
    'cliente',
    'serviciosExamen' => function ($query) {
        $query->where('estado', 'VALIDADO')
            ->with(['examen', 'profesional', 'resultados', 'adjuntos']);
    }
])
```

### Manejo de Valores NULL
El PDF maneja correctamente valores NULL en:
- Firma digital
- Imágenes adjuntas
- Textos descriptivos
- Valores de referencia

---

## 📧 Soporte

Para dudas o mejoras, consultar:
- Documentación de Laravel: https://laravel.com/docs
- Documentación de DomPDF: https://github.com/barryvdh/laravel-dompdf
- Documentación del sistema: `DOCUMENTACION_MODULO_RESULTADOS.md`

---

**Fecha de implementación:** 31 de enero de 2026
**Versión del sistema:** 1.0
**Autor:** Sistema Lab-Admin
