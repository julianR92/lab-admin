# 📊 Módulo de Captura de Resultados de Laboratorio

## 🎯 Propósito General
Sistema parametrizable para capturar, evaluar y validar resultados de exámenes de laboratorio clínico con 7 tipos diferentes de resultados, evaluación automática de alertas y valores de referencia contextuales (género, edad, condiciones especiales).

---

## 🏗️ Arquitectura del Sistema

### Modelos Principales

#### 1. **Examen**
Define el tipo de examen y su comportamiento:
- `tipo_resultado` (ENUM): Determina qué vista de captura usar
- `categoria_id`: Agrupación (Hematología, Química, etc.)
- `codigo`: Identificador único del examen
- `nombre`: Nombre del examen

#### 2. **ExamenParametro**
Define cada campo a capturar en un examen:
- `nombre_parametro`: Nombre del campo (ej: "Glucosa", "Hemoglobina")
- `codigo_parametro`: Código alfanumérico
- `tipo_dato`: ENUM('DECIMAL', 'INTEGER', 'TEXTO', 'SELECT', 'TEXTO_LARGO')
- `es_calculado`: BOOLEAN - Si true, el valor se calcula con `formula_calculo`
- `formula_calculo`: JSON con estructura `{formula: "P1 + P2", parametros: ["P1", "P2"]}`
- `opciones_select`: JSON array para campos SELECT
- `seccion`: Agrupa parámetros en acordeones (ej: "Físico", "Químico", "Microscópico")
- `unidad_medida`: mg/dL, g/dL, mm³, etc.
- `decimales`: Número de decimales para valores numéricos
- `requerido`: BOOLEAN - Obligatorio en captura
- `orden`: Orden de visualización

#### 3. **ExamenValorReferencia**
Define los rangos normales para evaluar resultados:
- `tipo_referencia`: ENUM('RANGO', 'CUALITATIVO', 'CATEGORIZADO', 'INFORMATIVO')
- **Contexto de aplicación**:
  - `genero`: 'M', 'F' o NULL (aplica para ambos)
  - `edad_min` / `edad_max`: Rango etario (ej: 0-17 años)
  - `condicion_especial`: Embarazo, diabetes, etc.
- **Para RANGO**:
  - `valor_min` / `valor_max`: Límites del rango normal
  - `operador`: '<', '>', '<=', '>=', '='
- **Para CUALITATIVO**:
  - `valor_cualitativo`: Valor esperado (ej: "NEGATIVO")
- **Para CATEGORIZADO**:
  - `categoria`: Nombre de la categoría (ej: "ÓPTIMO", "ALTO")
  - `valor_min` / `valor_max`: Rango de valores para esa categoría
- `orden`: Orden de prioridad en evaluación

#### 4. **ResultadoExamen**
Almacena el valor capturado y su evaluación:
- **Valores capturados**:
  - `valor_numerico`: DECIMAL(10,4) - Valores numéricos
  - `valor_texto`: VARCHAR - Valores de texto corto
  - `valor_cualitativo`: VARCHAR - Valores cualitativos (SELECT)
  - `valor_fecha` / `valor_hora`: DATE/TIME
  - `unidad_medida`: Unidad específica (puede diferir del parámetro)
- **Valores descriptivos**:
  - `observaciones`: TEXT - Descripción general
  - `interpretacion`: TEXT - Hallazgos técnicos
  - `conclusiones`: TEXT - Resultado final
- **Evaluación automática**:
  - `fuera_rango`: BOOLEAN - True si está fuera del rango normal
  - `tipo_alerta`: ENUM('NORMAL', 'BAJO', 'ALTO', 'CRITICO')
  - `categoria_asignada`: VARCHAR - Categoría automática
  - `valor_referencia_id`: FK al rango que se usó
  - `rango_referencia`: VARCHAR - Texto del rango (ej: "70-100 mg/dL")
- **Control de calidad**:
  - `requiere_revision`: BOOLEAN - True si es crítico o anómalo
  - `revisado_por` / `fecha_revision` / `comentario_revision`
- **Validación profesional**:
  - `validado_por` / `fecha_validacion`
- **Auditoría**:
  - `capturado_por`: Usuario que capturó
  - `created_at` / `updated_at`

---

## 🔬 Tipos de Resultados

### 1. **NUMERICO_SIMPLE**
Un solo valor numérico con un rango.
- **Vista**: `estandar.blade.php`
- **Ejemplo**: GLICEMIA (70-100 mg/dL)
- **Parámetros**: 1
- **Captura**: 1 input numérico

### 2. **NUMERICO_CATEGORIZADO**
Un valor numérico con categorías.
- **Vista**: `estandar.blade.php`
- **Ejemplo**: COLESTEROL (<200: Óptimo, 200-239: Límite alto, ≥240: Alto)
- **Parámetros**: 1
- **Captura**: 1 input numérico
- **Evaluación**: Asigna categoría automáticamente según valor

### 3. **CUALITATIVO_SIMPLE**
1-3 valores cualitativos (SELECT).
- **Vista**: `estandar.blade.php`
- **Ejemplo**: HEMOCLASIFICACIÓN (Grupo: A/B/AB/O, RH: +/-)
- **Parámetros**: 1-3
- **Captura**: SELECT dropdowns
- **Evaluación**: Compara con `valor_cualitativo` esperado

### 4. **CUALITATIVO_MULTIPLE_OPCIONES**
5-50 campos cualitativos organizados en secciones.
- **Vista**: `cualitativo-multiple.blade.php`
- **Ejemplo**: UROANALISIS (Físico: 15 campos, Químico: 12 campos, Microscópico: 8 campos)
- **Parámetros**: 5-50 agrupados por `seccion`
- **Captura**: 
  - Acordeón con pestañas por sección
  - SELECT para `tipo_dato=SELECT` con `opciones_select`
  - TEXTAREA para `tipo_dato=TEXTO_LARGO`
  - INPUT para `tipo_dato=TEXTO`
- **Evaluación**: Campo por campo según su valor de referencia

### 5. **MULTIPLE_CALCULADO**
Mix de valores manuales + valores calculados automáticamente.
- **Vista**: `multiple-parametros.blade.php`
- **Ejemplo**: CLEARANCE CREATININA
  - Manuales: Creatinina sérica, orina, volumen, tiempo
  - Calculado: Aclaramiento = (creat_orina × volumen × 1440) / (creat_serica × tiempo_min)
- **Parámetros**: 3-15 (algunos con `es_calculado=true`)
- **Captura**: 
  - Inputs normales para valores manuales
  - Inputs readonly para calculados
  - Botón "Calcular Ahora" (🔄)
  - Auto-cálculo en tiempo real al cambiar valores
- **JavaScript**: 
  - Parsea `formula_calculo['formula']` y `formula_calculo['parametros']`
  - Evalúa expresiones con validación `/^[0-9+\-*/().]+$/`
  - Feedback visual: 🟡 Faltan valores, 🟢 Éxito (2s), 🔴 Error, ⚪ Readonly

### 6. **TABLA_HEMATOLOGIA**
15-30 valores numéricos en formato tabla compacta.
- **Vista**: `multiple-parametros.blade.php`
- **Ejemplo**: HEMOGRAMA III (Eritrocitos, Leucocitos, Plaquetas, etc.)
- **Parámetros**: 15-30
- **Captura**: Tabla HTML con inputs numéricos compactos
- **Evaluación**: Cada parámetro evalúa individualmente

### 7. **TEXTO_DESCRIPTIVO**
Descripción libre con campos estructurados.
- **Vista**: `texto-descriptivo.blade.php`
- **Ejemplo**: BACILOSCOPIA, GOTA GRUESA
- **Parámetros**: 0-5 (opcionales: SELECT/TEXTO + 3 campos descriptivos)
- **Captura**:
  - Campos estructurados opcionales (SELECT/TEXTO)
  - 3 TEXTAREA obligatorios:
    - **Observaciones**: Características de la muestra
    - **Interpretación**: Hallazgos microscópicos ⚠️ REQUERIDO
    - **Conclusiones**: Resultado final ⚠️ REQUERIDO
- **Evaluación**: No aplica rangos, solo validación profesional
- **Peculiaridad**: Si el examen no tiene parámetros, crea uno automático (`codigo: DESC`)

---

## ⚠️ Sistema de Alertas

### Flujo de Evaluación Automática

#### 1. **Captura del Resultado** (`ResultadoExamenController@store`)
```php
$resultado->save(); // Guarda el valor capturado
$resultado->evaluar($contexto); // Evalúa automáticamente
$resultado->save(); // Guarda la evaluación
```

#### 2. **Método `evaluar()` en ResultadoExamen.php**
```php
public function evaluar(array $contexto = []): void
{
    // 1. Obtener valor de referencia aplicable
    $valorRef = $this->obtenerValorReferenciaAplicable($contexto);
    
    // 2. Resetear estado
    $this->fuera_rango = false;
    $this->tipo_alerta = self::ALERTA_NORMAL;
    
    // 3. Evaluar según tipo_referencia
    switch ($valorRef->tipo_referencia) {
        case 'RANGO':
            // Comparar valor_numerico con valor_min/valor_max
            if ($this->valor_numerico < $valorRef->valor_min) {
                $this->tipo_alerta = self::ALERTA_BAJO;
                $this->fuera_rango = true;
            }
            // Si está MUY bajo → CRITICO
            break;
        
        case 'CUALITATIVO':
            // Comparar valor_cualitativo con valor esperado
            if ($this->valor_cualitativo !== $valorRef->valor_cualitativo) {
                $this->tipo_alerta = self::ALERTA_ALTO;
                $this->fuera_rango = true;
            }
            break;
        
        case 'CATEGORIZADO':
            // Asignar categoría según rango
            // Ejemplo: <200 = ÓPTIMO, ≥240 = ALTO
            break;
    }
    
    // 4. Marcar para revisión si es crítico
    if ($this->tipo_alerta === self::ALERTA_CRITICO) {
        $this->requiere_revision = true;
    }
}
```

#### 3. **Obtener Valor de Referencia Contextual**
```php
private function obtenerValorReferenciaAplicable(array $contexto)
{
    $query = ExamenValorReferencia::where('parametro_id', $this->parametro_id)
        ->where('status', true);
    
    // Filtrar por género
    if (isset($contexto['genero'])) {
        $query->where(fn($q) => 
            $q->whereNull('genero')->orWhere('genero', $contexto['genero'])
        );
    }
    
    // Filtrar por edad
    if (isset($contexto['edad'])) {
        $query->where(fn($q) => 
            $q->where('edad_min', '<=', $contexto['edad'])
              ->where('edad_max', '>=', $contexto['edad'])
        );
    }
    
    return $query->orderBy('orden')->first();
}
```

### Tipos de Alerta

| Tipo | Descripción | Color | Ícono | Acción |
|------|-------------|-------|-------|--------|
| **NORMAL** | Dentro del rango | Verde | ✓ | Ninguna |
| **BAJO** | Debajo del límite inferior | Azul | ↓ | Alerta informativa |
| **ALTO** | Encima del límite superior | Amarillo | ↑ | Alerta importante |
| **CRITICO** | Valor peligroso (muy bajo/alto) | Rojo | ⚠ | Requiere revisión inmediata |

### Lógica de Criticidad
```php
// Ejemplo para Glucosa
if ($valor < 40 || $valor > 400) {
    $tipo_alerta = 'CRITICO';
    $requiere_revision = true;
} elseif ($valor < 70 || $valor > 140) {
    $tipo_alerta = $valor < 70 ? 'BAJO' : 'ALTO';
}
```

---

## 📋 Parámetros Importantes

### Para Configurar un Examen

#### 1. **Nivel Examen** (`examenes` table)
- `tipo_resultado`: ⚠️ CRÍTICO - Define qué vista usar
- `codigo`: UNIQUE - Identificador del examen
- `categoria_id`: Agrupación lógica

#### 2. **Nivel Parámetro** (`examen_parametros` table)
- **Básicos**:
  - `nombre_parametro`: Etiqueta visible
  - `codigo_parametro`: Código alfanumérico (ej: "GLU", "HB", "P1")
  - `tipo_dato`: Define el control de captura
  - `orden`: Orden de visualización
  - `requerido`: Obligatorio o no
- **Para valores numéricos**:
  - `unidad_medida`: mg/dL, g/dL, etc.
  - `decimales`: 0-4 decimales
- **Para calculados**:
  - `es_calculado`: true
  - `formula_calculo`: ⚠️ JSON REQUERIDO
    ```json
    {
      "formula": "(P1 + P2) / P3 * 100",
      "parametros": ["P1", "P2", "P3"]
    }
    ```
- **Para SELECT**:
  - `opciones_select`: ⚠️ JSON array REQUERIDO
    ```json
    ["NEGATIVO", "POSITIVO +", "POSITIVO ++", "POSITIVO +++"]
    ```
- **Para organización**:
  - `seccion`: Agrupa en acordeones (CUALITATIVO_MULTIPLE_OPCIONES)

#### 3. **Nivel Valor de Referencia** (`examen_valores_referencia` table)
- **Contexto** (todos opcionales):
  - `genero`: NULL, 'M', 'F'
  - `edad_min` / `edad_max`: NULL o números
  - `condicion_especial`: NULL o texto
- **Para RANGO**:
  - `valor_min` / `valor_max`: REQUERIDOS
  - `operador`: '<', '>', '<=', '>=', '=' (opcional)
- **Para CUALITATIVO**:
  - `valor_cualitativo`: REQUERIDO (ej: "NEGATIVO")
- **Para CATEGORIZADO**:
  - `categoria`: REQUERIDO
  - `valor_min` / `valor_max`: REQUERIDOS
- `orden`: Prioridad de evaluación (menor = mayor prioridad)

---

## 🔄 Flujo de Trabajo Completo

### 1. Captura Inicial
```
Usuario → Vista create.blade.php 
       → Selecciona tipo según examen.tipo_resultado
       → Llena formulario
       → Submit AJAX
```

### 2. Procesamiento Backend
```php
ResultadoExamenController@store:
1. DB::beginTransaction()
2. foreach ($resultados as $parametroId => $data)
3.   - Crear/Actualizar ResultadoExamen
4.   - Asignar valor con asignarValor($resultado, $parametro, $data)
5.   - $resultado->evaluar($contexto) ← EVALUACIÓN AUTOMÁTICA
6.   - $resultado->save()
7.   - Si CRITICO → Agregar warning
8. Calcular parámetros calculados
9. Actualizar servicio_examen.estado = 'COMPLETADO'
10. DB::commit()
```

### 3. Visualización
```
Vista show.blade.php:
- Si TEXTO_DESCRIPTIVO → 3 bloques de texto
- Sino → Tabla con columnas:
  - Parámetro
  - Valor (con icono de alerta)
  - Rango Referencia
  - Estado (badge con color)
  - Capturado Por
- Resumen de alertas al final
```

### 4. Validación Profesional
```
- Bacteriólogo revisa resultados
- Si hay alertas críticas → Debe marcar como revisado
- Validar resultados → estado = 'VALIDADO'
- Generar PDF con firma digital
```

---

## 🎨 Convenciones de UI

### Colores Bootstrap 5
- **Verde** (`bg-success`, `text-success`): Normal ✓
- **Azul** (`bg-info`, `text-info`): Bajo ↓
- **Amarillo** (`bg-warning`, `text-warning`): Alto ↑
- **Rojo** (`bg-danger`, `text-danger`): Crítico ⚠

### Inputs Calculados (MULTIPLE_CALCULADO)
- **Gris** (`#e9ecef`): Readonly, esperando cálculo
- **Amarillo** (`#fff3cd`): Faltan valores para calcular
- **Verde** (`#d1e7dd`): Calculado correctamente (2s)
- **Rojo** (`#f8d7da`): Error en fórmula

### Acordeones (CUALITATIVO_MULTIPLE_OPCIONES)
```html
<div class="accordion">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button">FÍSICO (15)</button>
    </h2>
    <div class="accordion-collapse collapse show">
      <!-- Campos de la sección -->
    </div>
  </div>
</div>
```

---

## 🚀 Puntos de Extensión

### Agregar Nuevo Tipo de Resultado
1. Crear constante en Examen::getTiposResultado()
2. Crear vista en `resources/views/resultados/tipos/mi-nuevo-tipo.blade.php`
3. Agregar case en `create.blade.php` línea ~180
4. Agregar case en `show.blade.php` línea ~125
5. Actualizar validación en `StoreExamenRequest` y `UpdateExamenRequest`

### Agregar Nuevo Tipo de Alerta
1. Agregar constante en ResultadoExamen (ej: `ALERTA_URGENTE`)
2. Agregar case en getters: `getIconoAlertaAttribute`, `getColorAlertaAttribute`, `getClaseBootstrapAttribute`
3. Actualizar método `evaluar()` con lógica de detección

### Implementar Cálculo Complejo
1. Crear parámetro con `es_calculado=true`
2. Definir `formula_calculo`:
   ```json
   {
     "formula": "EXPRESION_JAVASCRIPT",
     "parametros": ["COD1", "COD2"]
   }
   ```
3. El JavaScript en `multiple-parametros.blade.php` lo ejecutará automáticamente

---

## 🔒 Seguridad

### Validación de Fórmulas
- Regex: `/^[0-9+\-*/().]+$/` - Solo operaciones matemáticas básicas
- Se usa `new Function()` en sandbox, NO `eval()`
- Timeout de cálculo: 100ms

### Permisos (Spatie Laravel Permission)
- `ver.resultados`: Ver resultados
- `capturar.resultados`: Capturar valores
- `validar.resultados`: Validación profesional
- `editar.validados`: Editar resultados ya validados (solo Administrador)

### Auditoría Completa
- `capturado_por`: Usuario que capturó
- `revisado_por`: Usuario que revisó (si aplica)
- `validado_por`: Usuario que validó (Bacteriólogo)
- `created_at` / `updated_at`: Timestamps automáticos

---

## 📊 Ejemplo Completo: HEMOGRAMA III

### Configuración
```
Examen:
  tipo_resultado: TABLA_HEMATOLOGIA
  codigo: HEM3
  nombre: HEMOGRAMA COMPLETO TIPO III

Parámetros (20):
  1. Eritrocitos (tipo_dato: DECIMAL, unidad: mill/mm³, decimales: 2)
  2. Hemoglobina (tipo_dato: DECIMAL, unidad: g/dL, decimales: 1)
  3. Hematocrito (tipo_dato: DECIMAL, unidad: %, decimales: 1)
  ... (17 más)

Valores de Referencia (ejemplo Hemoglobina):
  - Hombres: 13.5-17.5 g/dL (genero='M')
  - Mujeres: 12.0-15.5 g/dL (genero='F')
  - Crítico: <7.0 o >20.0 (todos)
```

### Captura
```
Vista: multiple-parametros.blade.php (tabla)
Input: <input type="number" step="0.1" name="resultados[15][valor]" placeholder="0.0">
```

### Evaluación
```
Paciente: Mujer, 35 años
Resultado capturado: Hemoglobina = 10.5 g/dL

1. obtenerValorReferenciaAplicable() → 12.0-15.5 (genero='F')
2. evaluar() → 10.5 < 12.0 → ALERTA_BAJO
3. fuera_rango = true
4. No es crítico (>7.0)
5. Guardar: tipo_alerta='BAJO', rango_referencia='12.0-15.5 g/dL'
```

### Visualización
```
Tabla en show.blade.php:
┌────────────────┬───────────────┬──────────────────┬──────────┐
│ Hemoglobina    │ 10.5 g/dL ↓  │ 12.0-15.5 g/dL   │ ⚠ BAJO   │
└────────────────┴───────────────┴──────────────────┴──────────┘
Color: text-info (azul)
```

---

## 📝 Notas Finales

- **Parametrización total**: Todo el comportamiento se define en BD, sin cambios en código
- **Contexto dinámico**: Los rangos se aplican según género, edad y condiciones
- **Evaluación automática**: Al guardar, se evalúa inmediatamente sin intervención manual
- **Trazabilidad completa**: Cada acción queda registrada con usuario y timestamp
- **Extensible**: Agregar tipos nuevos solo requiere crear vista y actualizar switches
- **Bootstrap 5 exclusivo**: NUNCA usar Tailwind CSS en este proyecto

---

**Versión**: 1.0  
**Fecha**: Enero 2026  
**Autor**: Sistema Lab-Admin
