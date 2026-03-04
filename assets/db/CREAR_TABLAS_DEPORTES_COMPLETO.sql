-- ============================================
-- SCRIPT COMPLETO PARA MÓDULO DE DEPORTES
-- Crea todas las tablas necesarias
-- ============================================

USE pruebasumadmin;

-- ============================================
-- TABLA VRE_DEPORTES
-- ============================================
CREATE TABLE IF NOT EXISTS VRE_DEPORTES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE VARCHAR(200) NOT NULL,
    DESCRIPCION TEXT,
    ACTIVO ENUM('S','N') DEFAULT 'S',
    ORDEN INT DEFAULT 0
) ENGINE = INNODB;

-- ============================================
-- TABLA VRE_LIGAS
-- ============================================
CREATE TABLE IF NOT EXISTS VRE_LIGAS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_DEPORTE INT NOT NULL,
    NOMBRE VARCHAR(200) NOT NULL,
    FECHA_INICIO DATE,
    DESCRIPCION TEXT,
    REQUISITOS TEXT,
    RESPONSABLE_NOMBRE VARCHAR(200),
    RESPONSABLE_CONTACTO VARCHAR(200),
    FOTO_RESPONSABLE VARCHAR(500),
    EMAIL VARCHAR(200),
    TELEFONO VARCHAR(50),
    ACTIVO ENUM('S','N') DEFAULT 'S',
    ESTADO ENUM('EN_PREPARACION','EN_CURSO','PAUSADO','CANCELADO') DEFAULT 'EN_PREPARACION',
    ORDEN INT DEFAULT 0,
    FECHA_CREACION DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_DEPORTE) REFERENCES VRE_DEPORTES(ID) ON DELETE CASCADE
) ENGINE = INNODB;

-- ============================================
-- TABLA VRE_DEPORTES_ACTIVIDADES
-- ============================================
CREATE TABLE IF NOT EXISTS VRE_DEPORTES_ACTIVIDADES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_LIGA INT,
    NOMBRE VARCHAR(200) NOT NULL,
    DESCRIPCION TEXT,
    FECHA DATE,
    HORA TIME,
    LUGAR VARCHAR(200),
    TIPO ENUM('PARTIDO','ENTRENAMIENTO','REUNION','OTRO') DEFAULT 'PARTIDO',
    ESTADO ENUM('PROGRAMADO','EN_CURSO','FINALIZADO','CANCELADO') DEFAULT 'PROGRAMADO',
    FOREIGN KEY (ID_LIGA) REFERENCES VRE_LIGAS(ID) ON DELETE CASCADE
) ENGINE = INNODB;

-- ============================================
-- TABLA VRE_INSTALACIONES_DEPORTIVAS
-- ============================================
CREATE TABLE IF NOT EXISTS VRE_INSTALACIONES_DEPORTIVAS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE VARCHAR(200) NOT NULL,
    TIPO VARCHAR(100),
    CAPACIDAD INT,
    DESCRIPCION TEXT,
    UBICACION VARCHAR(300),
    HORARIO TEXT,
    ACTIVO ENUM('S','N') DEFAULT 'S'
) ENGINE = INNODB;

-- ============================================
-- INSERTAR DEPORTES HARDCORE
-- ============================================
INSERT INTO VRE_DEPORTES (NOMBRE, DESCRIPCION, ACTIVO, ORDEN) VALUES
('BASKETBALL', 'Baloncesto competitivo - Equipos masculinos y femeninos', 'S', 1),
('VOLEIBOL', 'Voleibol de alto rendimiento - Categorías masculina, femenina y mixta', 'S', 2),
('FUTBOL', 'Fútbol soccer competitivo - Liga universitaria', 'S', 3),
('FUTBOL AMERICANO', 'Fútbol americano - Equipos representativos', 'S', 4),
('SOFTBALL', 'Softball universitario - Categorías masculina y femenina', 'S', 5),
('ATLETISMO', 'Atletismo y carreras de pista - Competencias inter-universitarias', 'S', 6),
('NATACIÓN', 'Natación competitiva - Eventos individuales y relevos', 'S', 7),
('TENIS', 'Tenis - Categorías individuales y dobles', 'S', 8),
('ARTES MARCIALES', 'Artes marciales mixtas y defensa personal', 'S', 9),
('GIMNASIO', 'Entrenamiento funcional y fitness', 'S', 10)
ON DUPLICATE KEY UPDATE
    DESCRIPCION = VALUES(DESCRIPCION),
    ORDEN = VALUES(ORDEN);

-- ============================================
-- VERIFICACIÓN
-- ============================================
SELECT '✅ Todas las tablas de deportes creadas' AS STATUS;

SELECT 'Tablas creadas:' AS INFO;
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'pruebasumadmin'
AND TABLE_NAME IN ('VRE_DEPORTES', 'VRE_LIGAS', 'VRE_DEPORTES_ACTIVIDADES', 'VRE_INSTALACIONES_DEPORTIVAS')
ORDER BY TABLE_NAME;

SELECT 'Deportes insertados:' AS INFO;
SELECT * FROM VRE_DEPORTES ORDER BY ORDEN;
