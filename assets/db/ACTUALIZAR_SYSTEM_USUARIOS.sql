-- ============================================
-- ACTUALIZAR TABLA SYSTEM_USUARIOS
-- Agrega columnas ID_CLUB_ASIGNADO e ID_MINISTERIO_ASIGNADO
-- ============================================

USE pruebasumadmin;

-- Agregar ID_CLUB_ASIGNADO si no existe
SET @dbname = DATABASE();
SET @tablename = 'SYSTEM_USUARIOS';
SET @columnname = 'ID_CLUB_ASIGNADO';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT DEFAULT NULL AFTER ID_CAT')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar ID_MINISTERIO_ASIGNADO si no existe
SET @columnname = 'ID_MINISTERIO_ASIGNADO';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT DEFAULT NULL AFTER ID_CLUB_ASIGNADO')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificación
SELECT 'Columnas agregadas a SYSTEM_USUARIOS:' AS INFO;
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'pruebasumadmin'
AND TABLE_NAME = 'SYSTEM_USUARIOS'
AND COLUMN_NAME IN ('ID_CLUB_ASIGNADO', 'ID_MINISTERIO_ASIGNADO')
ORDER BY COLUMN_NAME;

SELECT '✅ SYSTEM_USUARIOS actualizada correctamente' AS STATUS;
