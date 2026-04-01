# 📄 REQUERIMIENTO: Generador de PDF de Resultados de Exámenes Clínicos (VERSIÓN MEJORADA)

## 🎯 OBJETIVO GENERAL

Crear un sistema completo de generación de reportes en PDF para los resultados de exámenes de laboratorio clínico, con soporte para múltiples exámenes en un mismo PDF, manejo de imágenes adjuntas, **firmas digitales de profesionales**, y diseño profesional acorde con estándares de laboratorios clínicos.

---

## 📋 CONTEXTO DEL SISTEMA

### Datos existentes que ya conoces:

**Modelo de datos:** Laravel con tablas relacionadas:
- `orden` → Orden de laboratorio principal
- `servicio_examen` → Servicios (exámenes) solicitados en la orden
- `examen` → Catálogo de exámenes disponibles (8 tipos diferentes)
- `examen_parametros` → Parámetros/componentes de cada examen
- `resultados_examen` → Valores capturados de cada parámetro
- `resultados_adjuntos` → Imágenes adjuntas a los servicios (ECG, Radiografías, Ecografías, etc.)
- `empresa` → Datos del laboratorio
- **`profesionales`** ⭐ **NUEVO** → Personal que captura, valida y **FIRMA** los resultados

**Tabla empresa (datos del encabezado):**
```sql
SELECT id, nit, razon_social, barrio, direccion, ciudad, 
       telefono_uno, telefono_dos, email, logo, created_at, updated_at
FROM empresa;
```

**Tabla profesionales (Personal del laboratorio que firma resultados):** ⭐ **CRÍTICA**
```sql
SELECT id, nombre, apellido, documento, profesion, registro_profesional, 
       especialidad, firma_digital, telefono, email, status, created_at, updated_at
FROM profesionales;
```

**Campos de firma por profesional:**
- `nombre` → Nombre del profesional
- `apellido` → Apellido del profesional
- `profesion` → Ej: "Médico", "Bacteriólogo", "Tecnólogo Clínico"
- `registro_profesional` → Ej: "RP-2024-001234"
- `especialidad` → Ej: "Hematología", "Microbiología"
- `firma_digital` → Ruta a imagen de firma (PNG/JPG, ~100x50px, fondo transparente)
- `documento` → Cédula del profesional

**Estructura de carpetas de imágenes adjuntas:**
```
storage/app/public/examenes/{numero_orden}/
├── IMG_001.jpg                  (Radiografía, Ecografía, etc.)
├── IMG_002.png
├── ECG_resultado.jpg            (Electrocardiograma)
└── RX_torax_frontal.jpg
```

**Estructura de carpetas de firmas profesionales:**
```
storage/app/public/firmas/profesionales/
├── firma_prof_001.png           (Firma de Dr. Juan Pérez)
├── firma_prof_002.jpg           (Firma de Dra. Ana Martínez)
└── firma_prof_003.png           (Firma de Lic. María González)
```

---

## 📐 DISEÑO DEL PDF

### SECCIÓN 1: ENCABEZADO (En todas las páginas)

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  [LOGO EMPRESA]           LABORATORIO CLÍNICO                   │
│                           {razon_social}                         │
│                           NIT: {nit}                             │
│                                                                 │
│  Dirección: {direccion} - {barrio}                             │
│  Ciudad: {ciudad}                                               │
│  Teléfonos: {telefono_uno} / {telefono_dos}                    │
│  Email: {email}                                                 │
│  Web: www.ejemplo.com (opcional)                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**Especificaciones:**
- Alto del encabezado: ~120 puntos
- Logo: max 80x80 px, lado izquierdo
- Información empresa: lado derecho
- Fuente: Arial/Helvetica, colores corporativos del laboratorio
- Línea divisoria horizontal en la parte inferior

---

### SECCIÓN 2: DATOS DE LA ORDEN (Primera página)

```
┌─────────────────────────────────────────────────────────────────┐
│                    RESULTADOS DE LABORATORIO                    │
└─────────────────────────────────────────────────────────────────┘

Número de Orden:  ORD-20260130-0001-1          Fecha: 30/01/2026
Hora Recepción:   09:30 AM                     Hora Entrega: 10:15 AM

┌─ DATOS DEL PACIENTE ─────────────────────────────────────────┐
│ Nombre Completo:    ISAAC REATIGA ROJAS                      │
│ ID/Cédula:          1142727468                               │
│ Edad:               45 años                  Sexo: M          │
│ Fecha Nacimiento:   30/01/1980                               │
│ Teléfono:           +57 315 123 4567                          │
│ Email:              isaac.reatiga@email.com                  │
│ Dirección:          Calle 50 #30-15, Apt 405                │
└──────────────────────────────────────────────────────────────┘

┌─ DATOS CLÍNICOS ─────────────────────────────────────────────┐
│ Médico Solicitante: Dr. Juan Carlos Peña                     │
│ Indicación Clínica: Control periódico - Diabetes tipo 2      │
│ Medicamentos:       Metformina 850mg cada 8h                 │
│ Antecedentes:       Hipertensión, Diabetes tipo 2            │
└──────────────────────────────────────────────────────────────┘
```

**Especificaciones:**
- Datos ordenados en 2 columnas
- Fondo gris claro (#f5f5f5) en encabezados de sección
- Tipografía: Arial 10pt
- Bordes: líneas finas (0.5pt)
- Espaciado entre secciones: 10pt

---

### SECCIÓN 3: RESULTADOS DE EXÁMENES (Con separación clara)

**Para CADA examen en la orden:**

```
┌════════════════════════════════════════════════════════════════┐
│ EXAMEN 1: HEMATOLOGÍA COMPLETA                                │
│ Código: HEM-001     |  Muestra: Sangre Venosa  |  Estado: ✓   │
│ Capturado por: Lic. María González  |  30/01/2026 - 09:45 AM  │
│ Validado por: Dr. Pedro López       |  30/01/2026 - 10:00 AM  │
└════════════════════════════════════════════════════════════════┘

┌─ Resultados ────────────────────────────────────────────────┐
│                                                             │
│  PARÁMETRO                 RESULTADO    UNIDAD   REFERENCIA │
│  ─────────────────────────────────────────────────────────  │
│  Hemoglobina              14.5        g/dL     12.0-16.0   │
│  Hematocrito              43.2        %        36-46        │
│  Leucocitos               7.2         K/uL     4.5-11.0    │
│  Eritrocitos              4.8         M/uL     4.0-5.5     │
│  Plaquetas                210         K/uL     150-400     │
│  VCM                      89          fL       80-100       │
│  HCM                      30          pg       27-33        │
│  CHCM                     32          g/dL     32-36        │
│                                                             │
└─────────────────────────────────────────────────────────────┘

INTERPRETACIÓN:
Recuento de células sanguíneas dentro de los parámetros normales. 
No se evidencian alteraciones hematológicas significativas.

CONCLUSIÓN:
Hematología completa normal.

┌─ FIRMA Y VALIDACIÓN ─────────────────────────────────────────┐
│                                                              │
│  Profesional a cargo: Dr. Pedro López                        │
│  Profesión: Médico Especialista en Hematología              │
│  Registro Profesional: RP-2024-001234                        │
│  Documento: 1.234.567.890                                    │
│  Teléfono: +57 7 643 2345 | Email: pedro.lopez@lab.com      │
│                                                              │
│  ┌─────────────┐                                             │
│  │             │ Firma Digital                              │
│  │  [FIRMA]    │ Fecha: 30/01/2026 - 10:00 AM              │
│  │             │ Validado: ✓                                │
│  └─────────────┘                                             │
│                                                              │
└──────────────────────────────────────────────────────────────┘

───────────────────────────────────────────────────────────────

┌════════════════════════════════════════════════════════════════┐
│ EXAMEN 2: QUÍMICA SANGUÍNEA COMPLETA (Perfil Glucosa)        │
│ Código: QUI-005     |  Muestra: Suero      |  Estado: ✓       │
│ Capturado por: Lic. Carlos Rodríguez  |  30/01/2026 - 09:50   │
│ Validado por: Dra. Ana Martínez       |  30/01/2026 - 10:05   │
└════════════════════════════════════════════════════════════════┘

┌─ Resultados ────────────────────────────────────────────────┐
│                                                             │
│  PARÁMETRO                 RESULTADO    UNIDAD   REFERENCIA │
│  ─────────────────────────────────────────────────────────  │
│  Glucosa en Ayunas         145         mg/dL    70-100  ⚠️  │
│  Creatinina                0.95        mg/dL    0.7-1.3     │
│  Nitrógeno Ureico          18          mg/dL    7-20        │
│  BUN/Creatinina            18.9        ratio    10-20       │
│  AST (TGO)                 28          U/L      10-40       │
│  ALT (TGP)                 32          U/L      7-56        │
│  Bilirrubina Total         0.8         mg/dL    0.1-1.2     │
│  Colesterol Total          210         mg/dL    <200    ⚠️  │
│  Triglicéridos             165         mg/dL    <150    ⚠️  │
│                                                             │
└─────────────────────────────────────────────────────────────┘

INTERPRETACIÓN:
Se observa glucemia elevada en ayunas (145 mg/dL, superior al 
rango normal 70-100 mg/dL). Colesterol y triglicéridos ligeramente 
elevados. Función hepática y renal normal.

CONCLUSIÓN:
Hiperglucemia e hiperlipidemia leve. Se recomienda seguimiento 
del paciente y posible ajuste de medicación.

┌─ FIRMA Y VALIDACIÓN ─────────────────────────────────────────┐
│                                                              │
│  Profesional a cargo: Dra. Ana Martínez García               │
│  Profesión: Médica Internista                               │
│  Especialidad: Bioquímica Clínica                            │
│  Registro Profesional: RP-2024-001567                        │
│  Documento: 2.345.678.901                                    │
│  Teléfono: +57 7 643 2346 | Email: ana.martinez@lab.com     │
│                                                              │
│  ┌─────────────┐                                             │
│  │             │ Firma Digital                              │
│  │  [FIRMA]    │ Fecha: 30/01/2026 - 10:05 AM              │
│  │             │ Validado: ✓                                │
│  └─────────────┘                                             │
│                                                              │
└──────────────────────────────────────────────────────────────┘

───────────────────────────────────────────────────────────────

┌════════════════════════════════════════════════════════════════┐
│ EXAMEN 3: ELECTROCARDIOGRAMA (ECG - 12 DERIVACIONES)          │
│ Código: ECG-001     |  Muestra: N/A           |  Estado: ✓     │
│ Capturado por: Lic. José Ramírez  |  30/01/2026 - 09:55 AM    │
│ Validado por: Dr. Carlos Ruiz      |  30/01/2026 - 10:10 AM   │
└════════════════════════════════════════════════════════════════┘

[IMAGEN ADJUNTA - ECG_RESULTADO.JPG se inserta aquí]
[Ancho: 100% de la página, con espacio de 20px arriba/abajo]

TÉCNICA: Electrocardiograma de 12 derivaciones a velocidad estándar
FRECUENCIA CARDÍACA: 72 lpm
RITMO: Sinusal regular
EJE CARDÍACO: Normal (-20° a +90°)
DURACIÓN PR: 160 ms (normal: 120-200 ms)
DURACIÓN QRS: 80 ms (normal: <120 ms)
DURACIÓN QT: 400 ms (normal)

HALLAZGOS:
- Ondas P normales en todas las derivaciones
- Complejo QRS normal en amplitud y duración
- Segmento ST sin cambios significativos
- Onda T normal en todas las derivaciones
- No se evidencian arritmias

INTERPRETACIÓN:
Electrocardiograma dentro de los límites normales.

CONCLUSIÓN:
ECG normal. No hay evidencia de isquemia miocárdica aguda ni arritmias.

┌─ FIRMA Y VALIDACIÓN ─────────────────────────────────────────┐
│                                                              │
│  Profesional a cargo: Dr. Carlos Ruiz Montoya                │
│  Profesión: Médico Cardiólogo                                │
│  Especialidad: Cardiología Clínica                            │
│  Registro Profesional: RP-2024-000890                         │
│  Documento: 3.456.789.012                                     │
│  Teléfono: +57 7 643 2347 | Email: carlos.ruiz@lab.com       │
│                                                              │
│  ┌─────────────┐                                             │
│  │             │ Firma Digital                              │
│  │  [FIRMA]    │ Fecha: 30/01/2026 - 10:10 AM              │
│  │             │ Validado: ✓                                │
│  └─────────────┘                                             │
│                                                              │
└──────────────────────────────────────────────────────────────┘

───────────────────────────────────────────────────────────────
```

---

## 🖼️ MANEJO DE IMÁGENES ADJUNTAS

### Ubicación en el PDF:
1. **Inmediatamente después** de los resultados numéricos del examen (para exámenes de imagen como ECG, Radiografía, Ecografía)
2. **Antes** de la interpretación y conclusión
3. Con **separación visual clara** (borde punteado o línea gris)

### Especificaciones:
```
┌─ IMÁGENES ADJUNTAS ──────────────────────────────────────────┐
│                                                              │
│ Cantidad de imágenes: 3                                     │
│                                                              │
│ [Imagen 1]                [Imagen 2]                        │
│ ECG_resultado.jpg         RX_torax_frontal.jpg              │
│ 2.34 MB | 800x600px       1.56 MB | 1024x768px             │
│ Subida: 30/01/2026 - 10:10 AM                              │
│ Por: Lic. María González                                    │
│                                                              │
│ [Imagen 3]                                                  │
│ Ultrasonido_vascular.png                                    │
│ 3.45 MB | 1200x900px                                        │
│ Subida: 30/01/2026 - 10:15 AM                              │
│ Por: Lic. Carlos Rodríguez                                 │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

**Criterios:**
- ✅ Máximo 2 imágenes por fila (responsive)
- ✅ Ancho máximo: 100% del área disponible
- ✅ Alto: proporcional a la imagen original
- ✅ Calidad: 96 DPI (suficiente para pantalla)
- ✅ Formato: JPEG comprimido para reducir tamaño PDF
- ✅ Información: nombre archivo, tamaño, fecha, usuario
- ✅ Si la imagen es muy grande: reducir tamaño (max 1200x900)

---

## 👥 SECCIÓN DE FIRMA Y VALIDACIÓN (POR EXAMEN) ⭐ **CRÍTICA**

**IMPORTANTE:** Cada examen debe estar firmado por el profesional a cargo que lo validó.

### Estructura de la sección de firma:

```
┌─ FIRMA Y VALIDACIÓN ─────────────────────────────────────────┐
│                                                              │
│  Profesional a cargo: [nombre] [apellido]                   │
│  Profesión: [profesion]                                     │
│  Especialidad: [especialidad]                               │
│  Registro Profesional: [registro_profesional]               │
│  Documento: [documento]                                     │
│  Teléfono: [telefono] | Email: [email]                      │
│                                                              │
│  ┌──────────────────┐                                        │
│  │                  │ Firma Digital                          │
│  │   [IMAGEN FIRMA] │ Fecha: [fecha_validacion]             │
│  │                  │ Validado: ✓ / ⏳ (Pendiente)          │
│  │                  │ Comprobante: [hash o ID]              │
│  └──────────────────┘                                        │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### Datos que van en esta sección (desde tabla `profesionales`):
- `nombre` + `apellido` → Nombre completo del profesional
- `profesion` → Ej: "Médico", "Bacteriólogo", "Tecnólogo Clínico", "Cardiólogo"
- `especialidad` → Ej: "Hematología", "Cardiología", "Microbiología"
- `registro_profesional` → Número del registro profesional
- `documento` → Cédula del profesional (importante para responsabilidad legal)
- `telefono` → Contacto directo
- `email` → Email para consultas
- `firma_digital` → Imagen PNG/JPG de la firma autógrafa del profesional
  - Dimensiones recomendadas: 150x60 px (ancho x alto)
  - Fondo: Transparente (PNG) o blanco
  - Color: Negro o azul oscuro
  - Resolución: 150 DPI

### Especificaciones de visualización de firma:
- Ancho en PDF: ~120 puntos (1.7 cm)
- Altura en PDF: ~45 puntos (0.6 cm)
- Posición: Lado derecho de la información del profesional
- Borde: Marco gris claro (1px)
- Si no existe firma: Mostrar espacio en blanco con "(Firma digital no disponible)"

### Estados de validación:
- ✓ Validado - El resultado está firmado y aprobado
- ⏳ Pendiente - El resultado aún no ha sido firmado/validado
- ⚠️ Requiere Revisión - El resultado tiene alertas o valores críticos
- ✗ Rechazado - El resultado no fue aprobado

---

## 📋 SECCIÓN FINAL DEL PDF

### SECCIÓN 4: PIE DE PÁGINA (En todas las páginas)

```
───────────────────────────────────────────────────────────────

ACLARACIONES IMPORTANTES:

1. Estos resultados son confidenciales y solo deben ser entregados 
   al paciente o a su médico tratante.

2. Los valores de referencia pueden variar según el método utilizado 
   y la edad del paciente.

3. ⚠️ = Valor fuera del rango de referencia | ⏳ = Pendiente de validación

4. La interpretación de estos resultados debe ser realizada por 
   un profesional médico calificado.

5. En caso de resultados críticos o anormales, contacte inmediatamente 
   al profesional que validó el resultado.

6. Cada examen ha sido validado por el profesional indicado en la 
   sección "FIRMA Y VALIDACIÓN" de cada resultado.

7. Muestra procesada: 30/01/2026 | Resultado entregado: 30/01/2026

───────────────────────────────────────────────────────────────

CERTIFICACIÓN DEL LABORATORIO:

Certificado ISO 15189:2022 (Laboratorios Clínicos)
Acreditación IDEXX International

Firma Digital: ▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪▪
Fecha de Generación: 31/01/2026 - 12:33 AM
Folio del Reporte: RPT-2026-01-31-00001

Página X de Y
© 2026 {empresa} - Todos los derechos reservados
```

---

## 🔧 REQUISITOS TÉCNICOS BACKEND

### Controlador: `ReportePdfController.php`

**Métodos principales:**

```php
// Método principal - Generar PDF de una orden completa
public function generarPdfOrden(int $ordenId): \Symfony\Component\HttpFoundation\BinaryFileResponse

// Método para generar PDF de un servicio/examen específico
public function generarPdfServicio(int $servicioExamenId): \Symfony\Component\HttpFoundation\BinaryFileResponse

// Método para descargar PDF (genera y descarga)
public function descargarPdf(int $ordenId)

// Método para visualizar en navegador
public function visualizarPdf(int $ordenId)

// Método para obtener vista previa en HTML
public function previaPdf(int $ordenId): View

// Método para enviar por email
public function enviarPorEmail(int $ordenId, $email)

// Método para archivar/guardar PDF
public function archivarPdf(int $ordenId)

// Método para validar/firmar un examen
public function firmarExamen(int $servicioExamenId, int $profesionalId)

// Método para obtener estado de validación
public function obtenerEstadoValidacion(int $servicioExamenId)
```

### Rutas API:

```php
Route::prefix('reportes')->group(function () {
    Route::get('/orden/{ordenId}/pdf', [ReportePdfController::class, 'generarPdfOrden']);
    Route::get('/servicio/{servicioExamenId}/pdf', [ReportePdfController::class, 'generarPdfServicio']);
    Route::get('/orden/{ordenId}/descargar', [ReportePdfController::class, 'descargarPdf']);
    Route::get('/orden/{ordenId}/visualizar', [ReportePdfController::class, 'visualizarPdf']);
    Route::get('/orden/{ordenId}/previa', [ReportePdfController::class, 'previaPdf']);
    Route::post('/orden/{ordenId}/email', [ReportePdfController::class, 'enviarPorEmail']);
    Route::post('/servicio/{servicioExamenId}/firmar', [ReportePdfController::class, 'firmarExamen']);
    Route::get('/servicio/{servicioExamenId}/estado', [ReportePdfController::class, 'obtenerEstadoValidacion']);
});
```

---

## 📦 LIBRERÍAS RECOMENDADAS (Laravel)

### Opción 1: **TCPDF** (Más simple, sin dependencias)
```composer
composer require tecnickcom/tcpdf
```
- ✅ Control total del layout
- ✅ Soporte para imágenes de alta calidad
- ✅ Generación rápida
- ✅ Bajo consumo de memoria
- ❌ Curva de aprendizaje más pronunciada

### Opción 2: **DomPDF** (Recomendado para este proyecto)
```composer
composer require barryvdh/laravel-dompdf
```
- ✅ Convierte HTML → PDF automáticamente
- ✅ Fácil de implementar
- ✅ Genera PDFs profesionales
- ✅ Buen manejo de estilos CSS
- ✅ Soporte para imágenes
- ❌ Más lento que TCPDF
- ❌ Mayor consumo de memoria

### Opción 3: **Snappy** (wkhtmltopdf wrapper)
```composer
composer require barryvdh/laravel-snappy
```
- ✅ Renderizado en navegador real
- ✅ Máxima fidelidad visual
- ✅ Mejor manejo de estilos complejos
- ✅ Soporte para media queries
- ❌ Requiere dependencias del sistema (wkhtmltopdf)
- ❌ Más lento en generación

**RECOMENDACIÓN FINAL:** Usar **DomPDF + Tailwind CSS** para este proyecto.

---

## 📐 ESTRUCTURA DE VISTAS BLADE

### Vista principal: `resources/views/reportes/pdf-examen.blade.php`

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte - Orden {{ $orden->numero_orden }}</title>
    <style>
        /* Estilos CSS para PDF */
        body { font-family: Arial, sans-serif; font-size: 10pt; }
        .encabezado { ... }
        .examen { ... }
        .tabla-resultados { ... }
        .imagen-adjunta { ... }
        .firma-profesional { ... }
        .separador { ... }
    </style>
</head>
<body>
    {{-- Encabezado --}}
    @include('reportes.partials.encabezado', ['empresa' => $empresa])

    {{-- Datos de la orden --}}
    @include('reportes.partials.datos-orden', ['orden' => $orden])

    {{-- Exámenes --}}
    @foreach($orden->servicios as $servicio)
        @include('reportes.partials.examen', [
            'servicio' => $servicio, 
            'numero' => $loop->iteration
        ])
    @endforeach

    {{-- Pie de página --}}
    @include('reportes.partials.pie-pagina', ['empresa' => $empresa])
</body>
</html>
```

### Vista parcial: `resources/views/reportes/partials/examen.blade.php`

```blade
<div class="examen">
    <div class="encabezado-examen">
        <h3>EXAMEN {{ $numero }}: {{ $servicio->examen->nombre }}</h3>
        <p>Código: {{ $servicio->examen->codigo }} | 
           Muestra: {{ $servicio->examen->muestra_requerida }} | 
           Estado: {{ $servicio->estado }}</p>
        <p>Capturado por: {{ $servicio->capturador->nombre }} | 
           {{ $servicio->created_at->format('d/m/Y - H:i') }}</p>
        <p>Validado por: {{ $servicio->validador->nombre ?? 'Pendiente' }} | 
           {{ $servicio->fecha_validacion?->format('d/m/Y - H:i') ?? 'N/A' }}</p>
    </div>

    {{-- Tabla de resultados --}}
    @include('reportes.partials.tabla-resultados', ['servicio' => $servicio])

    {{-- Imágenes adjuntas --}}
    @if($servicio->adjuntos->count() > 0)
        @include('reportes.partials.galeria-imagenes', ['servicio' => $servicio])
    @endif

    {{-- Interpretación y conclusión --}}
    @include('reportes.partials.interpretacion', ['servicio' => $servicio])

    {{-- FIRMA Y VALIDACIÓN DEL PROFESIONAL ⭐ NUEVA SECCIÓN --}}
    @include('reportes.partials.firma-profesional', ['servicio' => $servicio])

    {{-- Separador entre exámenes --}}
    @if(!$loop->last)
        <div class="separador-examen"></div>
    @endif
</div>
```

### Vista parcial: `resources/views/reportes/partials/firma-profesional.blade.php` ⭐ **NUEVA**

```blade
@php
    $profesional = $servicio->validador; // Relación hacia tabla profesionales
    $estado = $servicio->estado;
@endphp

<div class="firma-profesional">
    <h4>FIRMA Y VALIDACIÓN</h4>
    
    <div class="info-profesional">
        <div class="info-texto">
            <p><strong>Profesional a cargo:</strong> {{ $profesional->nombre }} {{ $profesional->apellido }}</p>
            <p><strong>Profesión:</strong> {{ $profesional->profesion }}</p>
            <p><strong>Especialidad:</strong> {{ $profesional->especialidad }}</p>
            <p><strong>Registro Profesional:</strong> {{ $profesional->registro_profesional }}</p>
            <p><strong>Documento:</strong> {{ $profesional->documento }}</p>
            <p><strong>Teléfono:</strong> {{ $profesional->telefono }} | <strong>Email:</strong> {{ $profesional->email }}</p>
        </div>

        <div class="seccion-firma">
            @if($profesional->firma_digital && Storage::disk('public')->exists($profesional->firma_digital))
                <img src="{{ Storage::disk('public')->path($profesional->firma_digital) }}" 
                     alt="Firma de {{ $profesional->nombre }}"
                     class="imagen-firma">
            @else
                <div class="firma-no-disponible">(Firma digital no disponible)</div>
            @endif
            
            <p class="fecha-firma">Fecha: {{ $servicio->fecha_validacion?->format('d/m/Y - H:i') ?? 'Pendiente' }}</p>
            
            <p class="estado-validacion">
                @if($estado === 'VALIDADO')
                    <span class="badge-validado">✓ Validado</span>
                @elseif($estado === 'COMPLETADO')
                    <span class="badge-completado">✓ Completado</span>
                @elseif($estado === 'PENDIENTE')
                    <span class="badge-pendiente">⏳ Pendiente</span>
                @elseif($estado === 'REQUIERE_REVISION')
                    <span class="badge-revision">⚠️ Requiere Revisión</span>
                @else
                    <span class="badge-cancelado">✗ Cancelado</span>
                @endif
            </p>
        </div>
    </div>
</div>
```

---

## 🎨 ESTILOS CSS PARA PDF

```css
/* Encabezado */
.encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #f5f5f5;
    border-bottom: 2px solid #333;
    margin-bottom: 20px;
}

/* Tabla de resultados */
.tabla-resultados {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    font-size: 9pt;
}

.tabla-resultados th {
    background-color: #e8e8e8;
    padding: 8px;
    text-align: left;
    border: 1px solid #ccc;
    font-weight: bold;
}

.tabla-resultados td {
    padding: 6px 8px;
    border: 1px solid #ddd;
}

/* Valores fuera de rango */
.valor-fuera-rango {
    color: #d9534f;
    font-weight: bold;
}

.alerta-critica {
    color: #d9534f;
    font-weight: bold;
    background-color: #fff3f3;
}

/* Imágenes */
.galeria-imagenes {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin: 20px 0;
    page-break-inside: avoid;
}

.imagen-item {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.imagen-item img {
    max-width: 100%;
    height: auto;
    margin-bottom: 5px;
}

/* FIRMA Y VALIDACIÓN ⭐ NUEVA */
.firma-profesional {
    margin: 20px 0;
    padding: 15px;
    border: 1px solid #d0d0d0;
    background-color: #fafafa;
    page-break-inside: avoid;
}

.firma-profesional h4 {
    margin-top: 0;
    font-size: 11pt;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

.info-profesional {
    display: flex;
    justify-content: space-between;
}

.info-texto {
    flex: 1;
    font-size: 8pt;
    line-height: 1.6;
}

.info-texto p {
    margin: 3px 0;
}

.seccion-firma {
    text-align: center;
    margin-left: 20px;
    width: 150px;
}

.imagen-firma {
    max-width: 120px;
    height: auto;
    border: 1px solid #ccc;
    padding: 5px;
    background-color: #fff;
    margin-bottom: 8px;
}

.firma-no-disponible {
    width: 120px;
    height: 40px;
    border: 1px dashed #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8pt;
    color: #999;
    margin-bottom: 8px;
}

.fecha-firma {
    font-size: 8pt;
    color: #666;
    margin: 5px 0;
    font-weight: bold;
}

.estado-validacion {
    font-size: 9pt;
    margin: 5px 0;
}

.badge-validado {
    background-color: #28a745;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 8pt;
    font-weight: bold;
}

.badge-completado {
    background-color: #17a2b8;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 8pt;
    font-weight: bold;
}

.badge-pendiente {
    background-color: #ffc107;
    color: #000;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 8pt;
    font-weight: bold;
}

.badge-revision {
    background-color: #fd7e14;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 8pt;
    font-weight: bold;
}

.badge-cancelado {
    background-color: #dc3545;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 8pt;
    font-weight: bold;
}

/* Separadores */
.separador-examen {
    border-top: 3px dashed #999;
    margin: 30px 0 20px 0;
    page-break-inside: avoid;
}

/* Pie de página */
.pie-pagina {
    margin-top: 30px;
    padding-top: 15px;
    border-top: 1px solid #ccc;
    font-size: 8pt;
    color: #666;
    text-align: center;
}
```

---

## 🔄 FLUJO DE GENERACIÓN DEL PDF

```
1. Usuario solicita reporte desde interfaz
   ↓
2. Controlador recibe orden_id
   ↓
3. Recupera datos de:
   - Orden + Paciente
   - Servicios examen + Estado validación
   - Exámenes + Parámetros
   - Resultados capturados
   - Profesionales que validaron (tabla profesionales)
   - Imágenes adjuntas
   - Firmas digitales de profesionales
   ↓
4. Genera vista Blade HTML con todos los datos
   ↓
5. DomPDF convierte HTML → PDF
   ↓
6. Opciones:
   a) Visualizar en navegador
   b) Descargar automáticamente
   c) Enviar por email
   d) Guardar en servidor
   ↓
7. Respuesta al usuario con PDF firmado
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [ ] Crear controlador `ReportePdfController`
- [ ] Crear vistas Blade para PDF
- [ ] Crear vista parcial para firma profesional ⭐
- [ ] Configurar DomPDF en `config/dompdf.php`
- [ ] Crear rutas API
- [ ] Implementar método `generarPdfOrden()`
- [ ] Implementar método `generarPdfServicio()`
- [ ] Agregar lógica de carga de imágenes adjuntas
- [ ] Agregar lógica de carga de firmas de profesionales
- [ ] Agregar estilos CSS para PDF
- [ ] Agregar estilos CSS para sección de firma
- [ ] Probar con diferentes tamaños de imágenes
- [ ] Probar con múltiples exámenes (saltos de página)
- [ ] Probar visualización de firmas
- [ ] Agregar watermark (CONFIDENCIAL/BORRADOR si aplica)
- [ ] Implementar descarga/visualización
- [ ] Implementar envío por email
- [ ] Validar generación de PDFs grandes
- [ ] Optimizar tamaño de PDF final
- [ ] Documentar para equipo
- [ ] Testing exhaustivo de firmas
- [ ] Pruebas con profesionales sin firma registrada

---

## 📊 DATOS QUE DEBE CONTENER CADA SECCIÓN

### Encabezado (desde tabla `empresa`):
```php
$empresa = [
    'id' => 1,
    'nit' => '890.210.234-1',
    'razon_social' => 'LABORATORIO CLÍNICO SALUD+',
    'barrio' => 'Centro',
    'direccion' => 'Calle 50 #30-15',
    'ciudad' => 'Bucaramanga',
    'telefono_uno' => '+57 7 643 2345',
    'telefono_dos' => '+57 315 123 4567',
    'email' => 'contacto@labsaludmas.com',
    'logo' => '/path/to/logo.png',
]
```

### Datos de Orden:
```php
$orden = [
    'numero_orden' => 'ORD-20260130-0001-1',
    'fecha_toma' => '2026-01-30 09:30:00',
    'paciente' => [...],
    'medico_solicitante' => 'Dr. Juan Pérez',
    'indicacion_clinica' => 'Control periódico',
    'servicios' => [...] // Array de exámenes
]
```

### Resultados por Examen:
```php
$resultados = [
    [
        'parametro' => 'Hemoglobina',
        'valor' => 14.5,
        'unidad' => 'g/dL',
        'rango_referencia' => '12.0-16.0',
        'fuera_rango' => false,
        'tipo_alerta' => 'NORMAL'
    ],
    ...
]
```

### Datos del Profesional que valida (desde tabla `profesionales`): ⭐
```php
$profesional = [
    'id' => 5,
    'nombre' => 'Pedro',
    'apellido' => 'López',
    'documento' => '1.234.567.890',
    'profesion' => 'Médico',
    'registro_profesional' => 'RP-2024-001234',
    'especialidad' => 'Hematología',
    'firma_digital' => 'storage/firmas/profesionales/firma_prof_005.png',
    'telefono' => '+57 7 643 2345',
    'email' => 'pedro.lopez@lab.com',
    'status' => true,
]
```

---

## 🚀 ENTREGABLES

1. ✅ **Controlador** `ReportePdfController.php` completo
2. ✅ **Vistas Blade** con todas las parciales necesarias
3. ✅ **Vista parcial para firma profesional** ⭐ NUEVA
4. ✅ **Estilos CSS** optimizados para PDF
5. ✅ **Estilos CSS para firma** ⭐ NUEVA
6. ✅ **Rutas API** configuradas
7. ✅ **Métodos modelo** para relaciones necesarias
8. ✅ **Sistema de manejo de imágenes** en PDF
9. ✅ **Sistema de manejo de firmas** en PDF ⭐
10. ✅ **Validaciones** de datos
11. ✅ **Tests unitarios** para generación
12. ✅ **Tests de firma** de profesionales
13. ✅ **Documentación** de uso

---

## 🎯 CONSIDERACIONES ESPECIALES POR TIPO DE EXAMEN

### 1. **HEMATOLOGÍA COMPLETA**
- Tabla con 8-10 parámetros
- Sin imágenes de diagnóstico
- Interpretación estándar
- Firmada por: Bacteriólogo o Tecnólogo Clínico

### 2. **QUÍMICA SANGUÍNEA**
- Tabla con 10-15 parámetros
- Puede incluir gráfico de glucosa
- Interpretación con énfasis en valores críticos
- Firmada por: Químico Farmacéutico o Bioquímico

### 3. **ELECTROCARDIOGRAMA (ECG)**
- **Imagen principal del ECG** (archivo adjunto)
- Datos técnicos: FC, Ritmo, Eje, PR, QRS, QT
- Interpretación profesional
- Firmada por: **Cardiólogo** (especialista requerido)
- Firma debe ser visible en el PDF

### 4. **RADIOGRAFÍA**
- **1-3 imágenes** de rayos X (archivos adjuntos)
- Técnica utilizada (AP, Lateral, Oblicua, etc.)
- Hallazgos detallados
- Firmada por: **Radiólogo** (especialista requerido)
- Firma prominente en la sección

### 5. **ECOGRAFÍA**
- **2-5 imágenes** (múltiples vistas - archivos adjuntos)
- Protocolo de estudio seguido
- Conclusiones por región anatómica
- Firmada por: **Ecógrafo certificado**
- Firma visible para responsabilidad

### 6. **UROANÁLISIS**
- Tabla de parámetros químicos
- Tabla de sedimento urinario (si aplica)
- Microscopía si se adjuntó imagen
- Firmada por: Tecnólogo Clínico

### 7. **CULTIVO BACTERIANO**
- Tabla de sensibilidad antibiótica
- Imagen de placa petri (opcional - archivo adjunto)
- Interpretación de susceptibilidad
- Recomendaciones de tratamiento
- Firmada por: **Bacteriólogo** (especialista requerido)

### 8. **PANEL LIPÍDICO**
- Tabla de valores (Colesterol, HDL, LDL, Triglicéridos, Ratio)
- Gráfico de tendencia (si hay históricos)
- Riesgo cardiovascular estimado
- Firmada por: Químico Farmacéutico

---

## 🔐 SEGURIDAD Y PRIVACIDAD

✅ PDF debe estar protegido con contraseña (opcional)
✅ No almacenar PDFs por más de 90 días
✅ Registrar cada descarga en log de auditoría
✅ Validar permisos: solo paciente/médico/profesional pueden acceder
✅ Aplicar marca de agua "CONFIDENCIAL" (opcional)
✅ Incluir aviso legal de privacidad
✅ Usar HTTPS para descargas
✅ Incluir firma digital de profesional responsable ⭐
✅ Validar que el profesional tenga status = 1 (activo)
✅ Verificar que el profesional esté autorizado para firmar ese tipo de examen
✅ Registrar quién firmó, cuándo y desde dónde

---

## 🖼️ SOBRE LAS FIRMAS DIGITALES

### Consideraciones importantes:
1. **Responsabilidad legal** - La firma vincula al profesional con los resultados
2. **Validación** - Solo profesionales con status = true pueden firmar
3. **Trazabilidad** - Cada firma debe registrar fecha, hora, usuario
4. **Presentación** - La firma debe verse claramente en el PDF
5. **Fallback** - Si no hay firma, mostrar "(Firma digital no disponible)"

### Formato recomendado para imágenes de firma:
- **Tipo**: PNG con fondo transparente o JPG blanco
- **Dimensiones**: 150x60 pixels (ancho x alto)
- **Resolución**: 150 DPI
- **Color**: Negro o azul oscuro
- **Nombre del archivo**: `firma_prof_{id_profesional}.png`
- **Ubicación**: `storage/app/public/firmas/profesionales/`

---

## 📞 CONTACTO Y SOPORTE

Si hay preguntas sobre:
- Estructura de datos → Revisar modelo relacional
- Validaciones → Revisar tabla `resultados_examen`
- Firmas → Revisar tabla `profesionales`
- Formatos → Consultar con área clínica
- Normativas → ISO 15189:2022 Laboratorios Clínicos
- Responsabilidad legal → Consultar con área legal/compliance

---

## 🚨 CAMBIOS PRINCIPALES EN ESTA VERSIÓN

✅ **NUEVA:** Tabla `profesionales` integrada completamente
✅ **NUEVA:** Sección "FIRMA Y VALIDACIÓN" por cada examen
✅ **NUEVA:** Visualización de imagen de firma digital en PDF
✅ **NUEVA:** Datos completos del profesional: profesión, registro, especialidad, documento
✅ **NUEVA:** Estado de validación (Validado, Pendiente, Requiere Revisión, Cancelado)
✅ **NUEVA:** Información de contacto del profesional en el PDF
✅ **MEJORADO:** Clarificación de responsabilidades por tipo de examen
✅ **MEJORADO:** Requisitos de seguridad para firmas

---

**FECHA DE ENTREGA ESPERADA:** 5 días calendario
**PLAZO PARA REVISIONES:** 3 días
**AMBIENTE DE TESTING:** Desarrollo y staging
**CONSIDERACIÓN ESPECIAL:** Ajustar tamaño de firmas según PDFs reales proporcionados
