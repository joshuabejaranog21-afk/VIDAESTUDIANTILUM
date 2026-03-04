-- ============================================
-- INSTALACIÓN RÁPIDA: MÓDULO REPOSITORIO DE FOTOGRAFÍAS
-- ============================================
-- Este script instala solo el módulo de repositorio
-- sin afectar las demás tablas del sistema
-- ============================================

USE pruebasumadmin;

-- Verificar si las tablas ya existen antes de crearlas
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- Tabla de estudiantes con datos de matrícula
CREATE TABLE IF NOT EXISTS VRE_ESTUDIANTES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    MATRICULA VARCHAR(7) UNIQUE NOT NULL,
    NOMBRE VARCHAR(100) NOT NULL,
    APELLIDO VARCHAR(100) NOT NULL,
    CARRERA VARCHAR(200),
    SEMESTRE INT,
    EMAIL VARCHAR(200),
    ACTIVO ENUM('S','N') DEFAULT 'S',
    FECHA_REGISTRO DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_matricula (MATRICULA)
) ENGINE=INNODB;

-- Tabla de fotografías personales vinculadas a matrícula
CREATE TABLE IF NOT EXISTS VRE_REPOSITORIO_FOTOS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_ESTUDIANTE INT NOT NULL,
    MATRICULA VARCHAR(7) NOT NULL,
    TITULO VARCHAR(200),
    DESCRIPCION TEXT,
    FOTO_URL VARCHAR(500) NOT NULL,
    TIPO_FOTO ENUM('INDIVIDUAL','GRUPAL','EVENTO','ACADEMICA','OTRA') DEFAULT 'INDIVIDUAL',
    FECHA_FOTO DATE,
    TAGS TEXT COMMENT 'JSON con etiquetas de búsqueda',
    ORDEN INT DEFAULT 0,
    PRIVADA ENUM('S','N') DEFAULT 'N' COMMENT 'Si es privada solo la ve el dueño',
    ACTIVO ENUM('S','N') DEFAULT 'S',
    FECHA_SUBIDA DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_ESTUDIANTE) REFERENCES VRE_ESTUDIANTES(ID) ON DELETE CASCADE,
    INDEX idx_matricula (MATRICULA),
    INDEX idx_tipo (TIPO_FOTO),
    INDEX idx_fecha (FECHA_SUBIDA)
) ENGINE=INNODB;

-- Tabla para referencias de alumnos en fotos grupales (segunda etapa)
CREATE TABLE IF NOT EXISTS VRE_REPOSITORIO_REFERENCIAS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_FOTO INT NOT NULL,
    MATRICULA_REFERENCIADA VARCHAR(7) NOT NULL,
    NOMBRE_REFERENCIADO VARCHAR(200),
    POSICION_X DECIMAL(5,2) COMMENT 'Coordenada X del tag en la foto (%)',
    POSICION_Y DECIMAL(5,2) COMMENT 'Coordenada Y del tag en la foto (%)',
    CONFIRMADO ENUM('S','N') DEFAULT 'N' COMMENT 'Si el referenciado confirmó que es él',
    FECHA_REFERENCIA DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_FOTO) REFERENCES VRE_REPOSITORIO_FOTOS(ID) ON DELETE CASCADE,
    INDEX idx_matricula_ref (MATRICULA_REFERENCIADA)
) ENGINE=INNODB;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

-- Insertar estudiante de ejemplo solo si no existe
INSERT IGNORE INTO VRE_ESTUDIANTES(MATRICULA, NOMBRE, APELLIDO, CARRERA, SEMESTRE, EMAIL) VALUES
('1220593', 'Usuario', 'Ejemplo', 'Ingeniería en Sistemas', 5, 'ejemplo@um.edu.mx');

-- Insertar módulo en el sistema solo si no existe
INSERT IGNORE INTO SYSTEM_MODULOS(ID, NOMBRE, SLUG, DESCRIPCION, ICONO, ORDEN) VALUES
(15, 'Repositorio de Fotos', 'repositorio', 'Repositorio personal de fotografías vinculadas a matrícula', 'camera', 15);

-- Mostrar resumen
SELECT '✅ MÓDULO DE REPOSITORIO INSTALADO CORRECTAMENTE' as STATUS;
SELECT COUNT(*) as TOTAL_ESTUDIANTES FROM VRE_ESTUDIANTES;
SELECT COUNT(*) as TOTAL_FOTOS FROM VRE_REPOSITORIO_FOTOS;

-- Instrucciones
SELECT '
====================================
INSTALACIÓN COMPLETADA
====================================

Próximos pasos:

1. Crear carpeta de uploads:
   mkdir -p uploads/repositorio
   chmod 777 uploads/repositorio

2. Acceder al módulo:
   - Inicia sesión en el sistema
   - Ve a "Repositorio de Fotos" en el menú

3. Probar con la matrícula de ejemplo:
   - Matrícula: 1220593
   - Nombre: Usuario Ejemplo

4. Consultar la documentación:
   - Ver archivo REPOSITORIO_FOTOS_README.md

====================================
' as INSTRUCCIONES;
