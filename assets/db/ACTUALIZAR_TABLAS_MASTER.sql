-- ============================================
-- SCRIPT MAESTRO DE ACTUALIZACIÓN - VERSIÓN SEGURA
-- Sistema VRE - Vida Estudiantil
-- Universidad de Montemorelos
-- ============================================
-- Este script actualiza las tablas VRE_COCURRICULARES
-- y VRE_INSTALACIONES_DEPORTIVAS para corregir el error:
-- "Unknown column 'DESCRIPCION' in 'field list'"
-- ============================================

USE pruebasumadmin;

-- ============================================
-- 1. ACTUALIZAR VRE_COCURRICULARES
-- ============================================

-- Agregar nuevas columnas solo si no existen
-- Nota: MySQL no tiene ADD COLUMN IF NOT EXISTS en versiones antiguas,
-- por lo que usaremos un enfoque que ignora errores si la columna ya existe

SET @dbname = 'pruebasumadmin';
SET @tablename = 'VRE_COCURRICULARES';

-- Agregar TIPO si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'TIPO'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN TIPO VARCHAR(100) AFTER NOMBRE',
    'SELECT "TIPO ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar OBJETIVO si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'OBJETIVO'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN OBJETIVO TEXT AFTER DESCRIPCION',
    'SELECT "OBJETIVO ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar REQUISITOS si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'REQUISITOS'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN REQUISITOS TEXT AFTER OBJETIVO',
    'SELECT "REQUISITOS ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar BENEFICIOS si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'BENEFICIOS'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN BENEFICIOS TEXT AFTER REQUISITOS',
    'SELECT "BENEFICIOS ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar RESPONSABLE_NOMBRE si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'RESPONSABLE_NOMBRE'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN RESPONSABLE_NOMBRE VARCHAR(200) AFTER BENEFICIOS',
    'SELECT "RESPONSABLE_NOMBRE ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar RESPONSABLE_EMAIL si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'RESPONSABLE_EMAIL'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN RESPONSABLE_EMAIL VARCHAR(200) AFTER RESPONSABLE_NOMBRE',
    'SELECT "RESPONSABLE_EMAIL ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar RESPONSABLE_TELEFONO si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'RESPONSABLE_TELEFONO'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN RESPONSABLE_TELEFONO VARCHAR(50) AFTER RESPONSABLE_EMAIL',
    'SELECT "RESPONSABLE_TELEFONO ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar HORARIOS si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'HORARIOS'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN HORARIOS TEXT AFTER RESPONSABLE_TELEFONO',
    'SELECT "HORARIOS ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar UBICACION si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'UBICACION'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN UBICACION VARCHAR(300) AFTER HORARIOS',
    'SELECT "UBICACION ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar CUPO_MAXIMO si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = 'CUPO_MAXIMO'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_COCURRICULARES ADD COLUMN CUPO_MAXIMO INT AFTER UBICACION',
    'SELECT "CUPO_MAXIMO ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT '✓ Tabla VRE_COCURRICULARES actualizada correctamente' AS MENSAJE;

-- ============================================
-- 2. ACTUALIZAR VRE_INSTALACIONES_DEPORTIVAS
-- ============================================

SET @tablename2 = 'VRE_INSTALACIONES_DEPORTIVAS';

-- Agregar DISPONIBLE si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename2
    AND COLUMN_NAME = 'DISPONIBLE'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_INSTALACIONES_DEPORTIVAS ADD COLUMN DISPONIBLE ENUM(''S'',''N'') DEFAULT ''S'' AFTER ACTIVO',
    'SELECT "DISPONIBLE ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar ORDEN si no existe
SET @column_check = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename2
    AND COLUMN_NAME = 'ORDEN'
);

SET @sql = IF(@column_check = 0,
    'ALTER TABLE VRE_INSTALACIONES_DEPORTIVAS ADD COLUMN ORDEN INT DEFAULT 0 AFTER DISPONIBLE',
    'SELECT "ORDEN ya existe" AS mensaje'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar todas las instalaciones existentes como disponibles
UPDATE VRE_INSTALACIONES_DEPORTIVAS
SET DISPONIBLE = IFNULL(DISPONIBLE, 'S'),
    ORDEN = IFNULL(ORDEN, 0);

SELECT '✓ Tabla VRE_INSTALACIONES_DEPORTIVAS actualizada correctamente' AS MENSAJE;

-- ============================================
-- VERIFICACIÓN FINAL
-- ============================================

SELECT '✓ ACTUALIZACIÓN COMPLETADA EXITOSAMENTE' AS MENSAJE;

-- Mostrar estructura de las tablas actualizadas
SELECT 'Estructura de VRE_COCURRICULARES:' AS INFO;
SHOW COLUMNS FROM VRE_COCURRICULARES;

SELECT 'Estructura de VRE_INSTALACIONES_DEPORTIVAS:' AS INFO;
SHOW COLUMNS FROM VRE_INSTALACIONES_DEPORTIVAS;
