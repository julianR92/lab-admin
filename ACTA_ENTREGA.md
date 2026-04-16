# ACTA DE ENTREGA DE SOFTWARE

## Sistema: LabSolutions - Plataforma de Gestion de Laboratorio Clinico

---

## 1. Informacion General

Por medio del presente documento se formaliza la entrega oficial del software **LabSolutions**, una plataforma web disenada para la gestion integral de un laboratorio clinico. El sistema cumple con la totalidad de los requisitos acordados y se encuentra 100% funcional, listo para ser utilizado en el entorno de produccion del cliente.

**URL de acceso:** https://labsolutions.fhernandez-dev.com/

---

## 2. Alcance Funcional del Sistema

A continuacion se describe todo lo que el sistema permite hacer, por modulo.

### 2.1. Acceso Seguro y Gestion de Usuarios

- Inicio de sesion protegido con usuario y contrasena.
- Sesion de usuario con duracion de 120 minutos.
- Cada usuario puede actualizar su nombre y cambiar su contrasena desde su perfil personal.

### 2.2. Panel de Control (Dashboard)

Al ingresar al sistema, el usuario visualiza un panel con las metricas mas importantes del dia:
- Total de pacientes registrados.
- Examenes pendientes por procesar.
- Resultados ya validados.
- Ingresos economicos del dia.

### 2.3. Gestion de Pacientes

Modulo completo para administrar la base de datos de pacientes del laboratorio:
- Registro de nuevos pacientes con datos personales completos (nombre, apellido, tipo y numero de documento, genero, fecha de nacimiento, telefono, correo, ciudad, EPS).
- Busqueda rapida por nombre o documento.
- Calculo automatico de la edad a partir de la fecha de nacimiento.
- Consulta, edicion y eliminacion de pacientes (con validaciones de integridad).
- Historial de servicios asociados a cada paciente.

### 2.4. Configuracion de Examenes (Nucleo del Sistema)

El laboratorio puede crear y configurar cualquier tipo de examen sin depender del desarrollador. El sistema soporta **7 tipos diferentes de examenes**:

1. **Numerico Simple** – Examen con un solo valor numerico (ej: Glicemia, Creatinina).
2. **Numerico Categorizado** – Valor numerico con interpretacion por categorias (ej: Colesterol "Optimo", "Alto").
3. **Cualitativo Simple** – Resultado seleccionado de una lista (ej: Positivo/Negativo, Reactivo/No Reactivo).
4. **Cualitativo Multiple Opciones** – Examen con varios campos cualitativos (ej: Uroanalisis con color, aspecto, nitritos, leucocitos).
5. **Multiple Calculado** – Valores digitados y valores calculados automaticamente con formulas (ej: Depuracion de Creatinina, INR).
6. **Tabla Hematologica** – Tablas amplias como Hemogramas completos con 15 a 30 parametros.
7. **Texto Descriptivo** – Resultado en texto libre redactado por el profesional (ej: Baciloscopias, Coprologicos).

Para cada examen se puede configurar:
- Codigo corto, nombre, tecnica, tipo de muestra requerida.
- Categoria a la que pertenece (Hematologia, Quimica Sanguinea, etc.).
- Precios (valor total y valor de remision).
- Tiempo de entrega en horas.
- Requisitos para el paciente (ayuno, instrucciones especiales).
- Parametros a capturar, con su unidad de medida y numero de decimales.
- Valores de referencia (rangos normales) que pueden variar segun genero, edad o condiciones especiales como embarazo.

### 2.5. Categorias de Examenes

Organizacion logica de examenes por grupos (Hematologia, Quimica Clinica, Uroanalisis, Microbiologia, etc.) para facilitar su busqueda y seleccion al crear ordenes.

### 2.6. Gestion de Profesionales

Registro del personal del laboratorio (bacteriologos, microbiologos, profesionales de la salud):
- Datos personales, profesion, numero de registro profesional, especialidad.
- **Carga de firma digital** que aparecera en los reportes PDF de resultados.
- Control de estado activo/inactivo.
- Historial de servicios asignados y resultados validados por cada profesional.

### 2.7. Configuracion del Laboratorio (Empresa)

El laboratorio configura una sola vez sus datos corporativos:
- NIT, razon social, direccion, barrio, ciudad.
- Telefonos de contacto, correo electronico.
- **Logo institucional** que se usa en el sistema y en los PDF.
- Datos y firma del representante legal para los reportes.

### 2.8. Ordenes de Servicio (Servicios)

Modulo central de operacion diaria:
- Creacion de ordenes seleccionando un paciente y uno o varios examenes.
- Agrupacion visual de examenes por categoria para facilitar la seleccion.
- **Numero de orden unico y automatico** con formato ORD-AAAAMMDD-XXXX.
- Calculo automatico del valor total de la orden sumando los precios.
- Registro de pagos: efectivo, tarjeta debito, tarjeta credito, transferencia, Nequi, Daviplata.
- Control del estado de pago (Pendiente, Parcial, Pagado) con saldo restante.
- Observaciones por orden.
- Filtros de busqueda por fecha, estado de pago y nombre de paciente.
- **Descarga de la orden en PDF** con los examenes solicitados y precios.
- Asignacion del profesional responsable de cada examen.
- Edicion de la fecha y hora de toma de muestra.

### 2.9. Flujo de Estados del Examen

Cada examen dentro de una orden sigue un flujo controlado:

**PENDIENTE → EN PROCESO → COMPLETADO → VALIDADO → ENTREGADO**

- **Pendiente**: Sin procesar.
- **En Proceso**: Profesional asignado, capturando resultados.
- **Completado**: Resultados ya ingresados.
- **Validado**: Resultados revisados y aprobados por el profesional responsable.
- **Entregado**: Reporte entregado al paciente (ya no se puede editar).

### 2.10. Captura de Resultados

El corazon del sistema. Segun el tipo de examen, se muestra una interfaz optimizada para la captura:
- Formularios dinamicos adaptados al tipo de examen.
- Campos agrupados por secciones (ej: Fisico, Quimico, Microscopico en Uroanalisis).
- Calculos automaticos para examenes que requieren formulas.
- **Evaluacion automatica** de cada resultado contra los valores de referencia del paciente (tomando en cuenta su genero y edad).
- Sistema de alertas visuales:
  - **NORMAL** (verde) – Dentro del rango normal.
  - **BAJO** (azul) – Por debajo del rango.
  - **ALTO** (amarillo) – Por encima del rango.
  - **CRITICO** (rojo) – Muy fuera de rango, requiere revision urgente.
- Campos de observaciones, interpretacion y conclusiones para resultados descriptivos.

### 2.11. Adjuntos de Resultados

- Carga de imagenes asociadas a un examen (electrocardiogramas, radiografias, ecografias, fotografias de laminas, etc.).
- Hasta 3 archivos por examen en formatos JPG, PNG, GIF, WEBP (hasta 10 MB cada uno).
- Galeria con vista previa.
- Descarga individual o en archivo ZIP de todos los adjuntos.
- Reordenamiento de imagenes.

### 2.12. Generacion de Reportes PDF

Dos tipos de reportes profesionales:

**Orden de Servicio en PDF:**
- Encabezado con logo y datos del laboratorio.
- Datos del paciente y la orden.
- Lista de examenes solicitados con precios.

**Resultados en PDF:**
- Membrete institucional con logo.
- Datos completos del paciente y la orden.
- Tabla de resultados con valores, unidades y rangos de referencia.
- Codigo de colores para destacar resultados fuera de rango.
- Textos de interpretacion y conclusiones para examenes descriptivos.
- Imagenes adjuntas incluidas en el reporte.
- **Firma digital del profesional** que valido los resultados.
- Pie de pagina con fecha de generacion y numeracion de paginas.
- Opcion de descargar el reporte de un solo examen o de todos los examenes de la orden.

---

## 3. Tecnologia Utilizada

El sistema fue desarrollado con tecnologias modernas, estables y ampliamente usadas en la industria, lo que garantiza su mantenibilidad y escalabilidad a futuro.

| Componente | Tecnologia |
|------------|------------|
| Lenguaje de backend | PHP 8.2+ |
| Framework de backend | Laravel 12 |
| Base de datos | MySQL |
| Motor de vistas | Blade (nativo de Laravel) |
| Framework CSS | Bootstrap 5.3 |
| Iconografia | Font Awesome 6.4 |
| Interacciones frontend | jQuery 3.7 |
| Generacion de PDF | DomPDF (barryvdh/laravel-dompdf) |
| Compilador de recursos | Vite 7 |
| Zona horaria | America/Bogota |
| Idioma de la interfaz | Espanol |

---

## 4. Entregables

El cliente recibe la totalidad de los entregables en un archivo comprimido (ZIP) que incluye:

- **Codigo fuente completo** de la aplicacion.
- **Base de datos** (archivo .sql) con la estructura y datos iniciales.
- Archivo `.env.example` con las variables de configuracion.
- Documentacion tecnica del sistema (CLAUDE.md).
- Archivos de configuracion (composer.json, package.json).

El cliente es propietario y tiene derecho pleno sobre el codigo fuente y la base de datos entregados.

---

## 5. Hospedaje Web (Hosting)

- El sistema se encuentra actualmente desplegado y alojado de forma gratuita durante **un (1) ano** en el subdominio:

  **https://labsolutions.fhernandez-dev.com/**

- Durante este periodo de un (1) ano el cliente no asume costos de hospedaje ni de subdominio.
- **Al cumplirse el ano de hospedaje gratuito, el servicio generara cobros** por concepto de:
  - Renovacion del hospedaje web.
  - Renovacion del subdominio o asignacion de un dominio propio.
  - Mantenimiento del servidor.
- Los valores de estos cobros seran acordados y comunicados con antelacion al vencimiento del periodo gratuito.
- El cliente tiene plena libertad de migrar el sistema a su propio hosting en cualquier momento, utilizando el ZIP del codigo fuente entregado.

### Credenciales de Acceso Inicial

Se entrega un usuario administrador con acceso completo al sistema:

| Campo | Valor |
|-------|-------|
| URL | https://labsolutions.fhernandez-dev.com/ |
| Usuario (correo) | admin@lab.com |
| Contrasena | password123 |

**Importante:** Se recomienda al cliente cambiar la contrasena inmediatamente despues del primer ingreso desde el modulo "Perfil de Usuario" por razones de seguridad.

---

## 6. Soporte Tecnico Post-Entrega

Con el fin de garantizar la correcta operacion del sistema, el desarrollador otorga un periodo de soporte tecnico gratuito bajo las siguientes condiciones:

- **Duracion:** cuatro (4) meses calendario a partir de la fecha de firma de esta acta.
- **Alcance del soporte:**
  - Correccion de errores (bugs) funcionales del software entregado.
  - Ajustes menores derivados de fallos tecnicos del sistema.
  - Acompanamiento en caso de incidencias tecnicas.
- **No incluye:**
  - Desarrollo de nuevas funcionalidades o modulos no contemplados en el alcance original.
  - Cambios estructurales al sistema.
  - Capacitacion adicional a la acordada.
  - Migraciones de servidor o cambios de infraestructura.
  - Correcciones derivadas de modificaciones realizadas por terceros sobre el codigo.
- Al finalizar los 4 meses, cualquier solicitud de soporte, correccion, mejora o nuevo desarrollo generara un cobro adicional segun tarifa vigente.

---

## 7. Condiciones Economicas

- **Saldo a entregar:** DOS MILLONES DE PESOS COLOMBIANOS (COP $2.000.000).
- Este valor corresponde al saldo final del proyecto segun lo acordado inicialmente.
- Con el pago de este saldo se formaliza la entrega total del software, la transferencia del codigo fuente y de la base de datos al cliente.

---

## 8. Declaracion de Conformidad

El cliente declara haber recibido a satisfaccion el software **LabSolutions** con todas las funcionalidades descritas en el presente documento, el codigo fuente, la base de datos y el acceso al sistema desplegado.

El desarrollador se compromete a cumplir con el periodo de soporte tecnico de 4 meses segun lo establecido en la seccion 6.

---

## 9. Datos del Desarrollador

**Desarrollador:** Julian Rincon
**Correo electronico:** julianrincon9230@gmail.com

---

**Fecha de entrega:** 15-04-2026

**Firma Cliente:** _______________________________

**Firma Desarrollador:** _______________________________
