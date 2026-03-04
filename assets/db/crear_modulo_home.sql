-- =====================================================
-- CREACION DEL MODULO HOME PARA EL SITIO PUBLICO
-- Permite controlar toda la configuración de la página
-- de inicio desde el cpanel administrativo
-- =====================================================

-- 1. TABLA DE CONFIGURACION DEL HERO SECTION
-- =====================================================
CREATE TABLE IF NOT EXISTS VRE_HOME_CONFIG (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    SECCION VARCHAR(50) NOT NULL COMMENT 'hero, call_to_action, footer, etc.',
    CLAVE VARCHAR(100) NOT NULL COMMENT 'hero_titulo, hero_subtitulo, etc.',
    VALOR TEXT COMMENT 'Valor de la configuración',
    TIPO ENUM('texto', 'textarea', 'url', 'imagen', 'color', 'numero') DEFAULT 'texto',
    DESCRIPCION VARCHAR(255) COMMENT 'Descripción del campo',
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S', 'N') DEFAULT 'S',
    FECHA_ACTUALIZACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_seccion_clave (SECCION, CLAVE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración general de la página de inicio';

-- 2. TABLA DE ESTADISTICAS PARA MOSTRAR EN EL HOME
-- =====================================================
CREATE TABLE IF NOT EXISTS VRE_HOME_ESTADISTICAS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TITULO VARCHAR(100) NOT NULL COMMENT 'Ej: Estudiantes Activos',
    NUMERO VARCHAR(20) NOT NULL COMMENT 'Ej: 2500+',
    ICONO VARCHAR(100) COMMENT 'Clase de Font Awesome: fas fa-users',
    COLOR VARCHAR(20) DEFAULT '#667eea' COMMENT 'Color hexadecimal',
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S', 'N') DEFAULT 'S',
    FECHA_CREACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FECHA_ACTUALIZACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estadísticas animadas del home';

-- 3. TABLA PARA ELEMENTOS DESTACADOS EN EL HOME
-- =====================================================
CREATE TABLE IF NOT EXISTS VRE_HOME_DESTACADOS (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    TIPO ENUM('club', 'ministerio', 'evento') NOT NULL,
    ID_REGISTRO INT NOT NULL COMMENT 'ID del club, ministerio o evento',
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S', 'N') DEFAULT 'S',
    FECHA_CREACION TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (TIPO),
    INDEX idx_registro (ID_REGISTRO)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Elementos destacados para mostrar en el home';

-- 4. INSERTAR CONFIGURACION INICIAL DEL HERO SECTION
-- =====================================================
INSERT INTO VRE_HOME_CONFIG (SECCION, CLAVE, VALOR, TIPO, DESCRIPCION, ORDEN) VALUES
-- Hero Section
('hero', 'titulo', 'Vida Estudiantil', 'texto', 'Título principal del hero', 1),
('hero', 'subtitulo', 'Descubre los clubes, ministerios y actividades que harán de tu experiencia universitaria algo inolvidable', 'textarea', 'Subtítulo/descripción del hero', 2),
('hero', 'boton_texto', 'Explorar Clubes', 'texto', 'Texto del botón principal', 3),
('hero', 'boton_url', 'clubes.php', 'url', 'URL del botón principal', 4),
('hero', 'color_inicio', '#667eea', 'color', 'Color inicial del gradiente', 5),
('hero', 'color_fin', '#764ba2', 'color', 'Color final del gradiente', 6),
('hero', 'imagen_fondo', '', 'imagen', 'Imagen de fondo (opcional, sobrescribe gradiente)', 7),

-- Sección de Clubes
('seccion_clubes', 'titulo', 'Clubes Destacados', 'texto', 'Título de la sección de clubes', 1),
('seccion_clubes', 'subtitulo', 'Únete a alguno de nuestros clubes y forma parte de una comunidad increíble', 'textarea', 'Subtítulo de la sección', 2),
('seccion_clubes', 'mostrar', 'S', 'texto', 'Mostrar sección (S/N)', 3),
('seccion_clubes', 'cantidad', '6', 'numero', 'Cantidad de clubes a mostrar', 4),

-- Sección de Ministerios
('seccion_ministerios', 'titulo', 'Ministerios', 'texto', 'Título de la sección de ministerios', 1),
('seccion_ministerios', 'subtitulo', 'Crece espiritualmente y sirve a tu comunidad', 'textarea', 'Subtítulo de la sección', 2),
('seccion_ministerios', 'mostrar', 'S', 'texto', 'Mostrar sección (S/N)', 3),
('seccion_ministerios', 'cantidad', '3', 'numero', 'Cantidad de ministerios a mostrar', 4),

-- Sección de Eventos
('seccion_eventos', 'titulo', 'Próximos Eventos', 'texto', 'Título de la sección de eventos', 1),
('seccion_eventos', 'subtitulo', 'No te pierdas ninguna actividad', 'textarea', 'Subtítulo de la sección', 2),
('seccion_eventos', 'mostrar', 'S', 'texto', 'Mostrar sección (S/N)', 3),
('seccion_eventos', 'cantidad', '3', 'numero', 'Cantidad de eventos a mostrar', 4),

-- Sección de Estadísticas
('seccion_stats', 'mostrar', 'S', 'texto', 'Mostrar estadísticas (S/N)', 1),
('seccion_stats', 'titulo', '¿Por qué unirte?', 'texto', 'Título de la sección', 2),

-- Footer
('footer', 'descripcion', 'Promoviendo el desarrollo integral de nuestros estudiantes a través de la vida estudiantil.', 'textarea', 'Descripción del footer', 1),
('footer', 'facebook', '#', 'url', 'URL de Facebook', 2),
('footer', 'instagram', '#', 'url', 'URL de Instagram', 3),
('footer', 'twitter', '#', 'url', 'URL de Twitter', 4),
('footer', 'youtube', '', 'url', 'URL de YouTube', 5);

-- 5. INSERTAR ESTADISTICAS INICIALES
-- =====================================================
INSERT INTO VRE_HOME_ESTADISTICAS (TITULO, NUMERO, ICONO, COLOR, ORDEN) VALUES
('Estudiantes Activos', '2,500+', 'fas fa-users', '#667eea', 1),
('Clubes Disponibles', '25+', 'fas fa-star', '#764ba2', 2),
('Eventos al Año', '100+', 'fas fa-calendar-alt', '#f093fb', 3),
('Ministerios', '15+', 'fas fa-hands-praying', '#4facfe', 4);

-- 6. AGREGAR MODULO AL SISTEMA
-- =====================================================
INSERT INTO SYSTEM_MODULOS (NOMBRE, SLUG, DESCRIPCION, ICONO, ORDEN, ACTIVO)
VALUES ('Inicio (Home)', 'home', 'Configuración de la página de inicio del sitio público', 'home', 3, 'S')
ON DUPLICATE KEY UPDATE
    NOMBRE = 'Inicio (Home)',
    DESCRIPCION = 'Configuración de la página de inicio del sitio público',
    ICONO = 'home';

-- 7. ASIGNAR PERMISOS AL ROL DE ADMINISTRADOR (ID=2)
-- =====================================================
SET @modulo_home_id = (SELECT ID FROM SYSTEM_MODULOS WHERE SLUG = 'home' LIMIT 1);

-- Insertar permisos para el rol ADMINISTRADOR
INSERT INTO SYSTEM_ROL_MODULO_PERMISOS (ID_ROL, ID_MODULO, ID_PERMISO)
SELECT 2, @modulo_home_id, p.ID
FROM SYSTEM_PERMISOS p
WHERE p.SLUG IN ('ver', 'crear', 'editar', 'eliminar')
ON DUPLICATE KEY UPDATE ID_ROL = ID_ROL;

-- Insertar permisos para el rol SUPERUSUARIO (aunque no los necesita, por consistencia)
INSERT INTO SYSTEM_ROL_MODULO_PERMISOS (ID_ROL, ID_MODULO, ID_PERMISO)
SELECT 1, @modulo_home_id, p.ID
FROM SYSTEM_PERMISOS p
WHERE p.SLUG IN ('ver', 'crear', 'editar', 'eliminar')
ON DUPLICATE KEY UPDATE ID_ROL = ID_ROL;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
