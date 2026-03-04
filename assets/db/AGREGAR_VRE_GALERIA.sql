-- ============================================
-- SCRIPT PARA AGREGAR TABLA VRE_GALERIA
-- Esta tabla centraliza la gestión de imágenes
-- ============================================

USE pruebasumadmin;

-- Crear tabla VRE_GALERIA si no existe
CREATE TABLE IF NOT EXISTS VRE_GALERIA(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    MODULO VARCHAR(50) NOT NULL COMMENT 'clubes, ministerios, deportes, ligas, eventos, banners',
    ID_REGISTRO INT NOT NULL COMMENT 'ID del registro en la tabla correspondiente',
    TITULO VARCHAR(200),
    URL_IMAGEN VARCHAR(500) NOT NULL,
    TIPO ENUM('principal', 'galeria', 'banner', 'evento') DEFAULT 'galeria',
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S','N') DEFAULT 'S',
    SUBIDO_POR INT,
    FECHA_SUBIDA DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SUBIDO_POR) REFERENCES SYSTEM_USUARIOS(ID) ON DELETE SET NULL,
    INDEX idx_modulo_registro (MODULO, ID_REGISTRO),
    INDEX idx_tipo (TIPO)
) ENGINE = INNODB;

-- Mensaje de confirmación
SELECT 'Tabla VRE_GALERIA creada correctamente' AS STATUS;
