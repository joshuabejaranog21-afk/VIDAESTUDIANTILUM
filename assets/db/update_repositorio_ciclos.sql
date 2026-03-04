-- ============================================
-- ACTUALIZACIÓN: Agregar Ciclos Escolares y Release Date
-- ============================================
-- Este script agrega los campos para manejar ciclos escolares
-- y fechas de publicación para controlar visibilidad
-- ============================================

USE pruebasumadmin;

-- Agregar campo de ciclo escolar
ALTER TABLE VRE_REPOSITORIO_FOTOS
ADD COLUMN IF NOT EXISTS CICLO_ESCOLAR VARCHAR(10) COMMENT 'Ciclo escolar formato: 2024-2025'
AFTER TIPO_FOTO;

-- Agregar campo de fecha de publicación
ALTER TABLE VRE_REPOSITORIO_FOTOS
ADD COLUMN IF NOT EXISTS RELEASE_DATE DATETIME COMMENT 'Fecha en que la foto se hace visible para estudiantes'
AFTER CICLO_ESCOLAR;

-- Agregar índice para búsquedas por ciclo
ALTER TABLE VRE_REPOSITORIO_FOTOS
ADD INDEX IF NOT EXISTS idx_ciclo (CICLO_ESCOLAR);

-- Agregar índice para release date
ALTER TABLE VRE_REPOSITORIO_FOTOS
ADD INDEX IF NOT EXISTS idx_release (RELEASE_DATE);

-- Migrar datos existentes: convertir FECHA_FOTO a CICLO_ESCOLAR
UPDATE VRE_REPOSITORIO_FOTOS
SET CICLO_ESCOLAR = CONCAT(YEAR(FECHA_FOTO), '-', YEAR(FECHA_FOTO) + 1)
WHERE FECHA_FOTO IS NOT NULL AND CICLO_ESCOLAR IS NULL;

-- Establecer release_date como fecha actual para fotos existentes (ya publicadas)
UPDATE VRE_REPOSITORIO_FOTOS
SET RELEASE_DATE = FECHA_SUBIDA
WHERE RELEASE_DATE IS NULL;

SELECT '✅ ACTUALIZACIÓN COMPLETADA - Ciclos escolares y Release Date agregados' as STATUS;
SELECT COUNT(*) as FOTOS_ACTUALIZADAS FROM VRE_REPOSITORIO_FOTOS WHERE CICLO_ESCOLAR IS NOT NULL;
