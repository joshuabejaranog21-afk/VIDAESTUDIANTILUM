-- ============================================
-- SCRIPT PARA ACTUALIZAR TABLAS VRE_CLUBES Y VRE_MINISTERIOS
-- Agrega columnas faltantes necesarias para el sistema
-- ============================================

USE pruebasumadmin;

-- Verificar y agregar columna ID_DIRECTOR_USUARIO a VRE_CLUBES
SET @dbname = DATABASE();
SET @tablename = 'VRE_CLUBES';
SET @columnname = 'ID_DIRECTOR_USUARIO';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT AFTER TELEFONO')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificar y agregar columna ID_DIRECTOR_USUARIO a VRE_MINISTERIOS
SET @tablename = 'VRE_MINISTERIOS';
SET @columnname = 'ID_DIRECTOR_USUARIO';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT AFTER TELEFONO')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Mensaje de confirmación
SELECT 'Tablas VRE_CLUBES y VRE_MINISTERIOS actualizadas correctamente' AS STATUS;
