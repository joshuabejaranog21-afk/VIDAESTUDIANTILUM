-- ============================================
-- ACTUALIZAR TABLA VRE_COCURRICULARES
-- Agrega todas las columnas necesarias
-- ============================================

USE pruebasumadmin;

-- Crear tabla si no existe
CREATE TABLE IF NOT EXISTS VRE_COCURRICULARES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE VARCHAR(200) NOT NULL,
    TIPO ENUM('PROGRAMA','SERVICIO','APOYO','OTRO') DEFAULT 'PROGRAMA',
    DESCRIPCION TEXT,
    OBJETIVO TEXT,
    REQUISITOS TEXT,
    BENEFICIOS TEXT,
    RESPONSABLE_NOMBRE VARCHAR(200),
    RESPONSABLE_EMAIL VARCHAR(200),
    RESPONSABLE_TELEFONO VARCHAR(50),
    HORARIOS TEXT,
    UBICACION VARCHAR(500),
    CUPO_MAXIMO INT,
    ACTIVO ENUM('S','N') DEFAULT 'S',
    ORDEN INT DEFAULT 0,
    FECHA_CREACION DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB;

-- Si la tabla ya existe, agregar columnas faltantes una por una
SET @dbname = DATABASE();
SET @tablename = 'VRE_COCURRICULARES';

-- Agregar columna TIPO
SET @columnname = 'TIPO';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ENUM(''PROGRAMA'',''SERVICIO'',''APOYO'',''OTRO'') DEFAULT ''PROGRAMA'' AFTER NOMBRE')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna OBJETIVO
SET @columnname = 'OBJETIVO';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT AFTER DESCRIPCION')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna REQUISITOS
SET @columnname = 'REQUISITOS';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT AFTER OBJETIVO')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna BENEFICIOS
SET @columnname = 'BENEFICIOS';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT AFTER REQUISITOS')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna RESPONSABLE_NOMBRE
SET @columnname = 'RESPONSABLE_NOMBRE';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(200) AFTER BENEFICIOS')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna RESPONSABLE_EMAIL
SET @columnname = 'RESPONSABLE_EMAIL';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(200) AFTER RESPONSABLE_NOMBRE')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna RESPONSABLE_TELEFONO
SET @columnname = 'RESPONSABLE_TELEFONO';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(50) AFTER RESPONSABLE_EMAIL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna HORARIOS
SET @columnname = 'HORARIOS';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT AFTER RESPONSABLE_TELEFONO')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna UBICACION
SET @columnname = 'UBICACION';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(500) AFTER HORARIOS')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar columna CUPO_MAXIMO
SET @columnname = 'CUPO_MAXIMO';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT AFTER UBICACION')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verificación
SELECT '✅ Tabla VRE_COCURRICULARES actualizada correctamente' AS STATUS;
SELECT 'Columnas en VRE_COCURRICULARES:' AS INFO;
DESCRIBE VRE_COCURRICULARES;
