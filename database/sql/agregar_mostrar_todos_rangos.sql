-- Script para agregar el campo mostrar_todos_rangos a examen_parametros
-- Ejecutar directamente en MySQL. No usar con php artisan migrate.
--
-- Propósito: Cuando mostrar_todos_rangos = 1, en la pantalla de resultados y en el PDF
-- se despliegan TODOS los valores de referencia del parámetro (no solo el rango matched).
-- Útil para exámenes como HCG (hormona del embarazo) donde el médico necesita ver
-- todos los rangos por semana de gestación para interpretar el resultado.
--
-- Aplica desde: 2026-04-01

ALTER TABLE `examen_parametros`
    ADD COLUMN `mostrar_todos_rangos` TINYINT(1) NOT NULL DEFAULT 0
    AFTER `requerido`;
