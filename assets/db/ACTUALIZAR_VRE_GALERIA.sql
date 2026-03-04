-- ============================================
-- Actualización de tabla VRE_GALERIA
-- Agregar columna DESCRIPCION
-- ============================================

USE pruebasumadmin;

-- Verificar y agregar columna DESCRIPCION a VRE_GALERIA
SET @dbname = 'pruebasumadmin';
SET @tablename = 'VRE_GALERIA';

-- Agregar DESCRIPCION si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'DESCRIPCION'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_GALERIA ADD COLUMN DESCRIPCION TEXT AFTER TITULO',
    'SELECT "DESCRIPCION ya existe en VRE_GALERIA" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT '✓ Tabla VRE_GALERIA actualizada correctamente' AS MENSAJE;

-- Mostrar estructura actualizada
SHOW COLUMNS FROM VRE_GALERIA;
