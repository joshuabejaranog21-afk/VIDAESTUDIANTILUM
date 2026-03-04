-- ============================================
-- Actualización de tabla VRE_INSTALACIONES_DEPORTIVAS
-- Agregar columnas faltantes
-- ============================================

USE pruebasumadmin;

-- Agregar columnas faltantes a VRE_INSTALACIONES_DEPORTIVAS
ALTER TABLE VRE_INSTALACIONES_DEPORTIVAS
    ADD COLUMN DISPONIBLE ENUM('S','N') DEFAULT 'S' AFTER ACTIVO,
    ADD COLUMN ORDEN INT DEFAULT 0 AFTER DISPONIBLE;

-- Actualizar todas las instalaciones existentes como disponibles
UPDATE VRE_INSTALACIONES_DEPORTIVAS
SET DISPONIBLE = 'S', ORDEN = 0
WHERE 1=1;

SELECT 'Tabla VRE_INSTALACIONES_DEPORTIVAS actualizada correctamente' AS mensaje;
