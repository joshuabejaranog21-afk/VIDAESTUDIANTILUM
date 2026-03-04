-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 18, 2025 at 09:48 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pruebasumadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `example`
--

DROP TABLE IF EXISTS `example`;
CREATE TABLE IF NOT EXISTS `example` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CORREO` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OBSERVACIONES` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_auditoria`
--

DROP TABLE IF EXISTS `system_auditoria`;
CREATE TABLE IF NOT EXISTS `system_auditoria` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int DEFAULT NULL,
  `MODULO` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACCION` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `IP` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FECHA` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_USUARIO` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_auditoria`
--

INSERT INTO `system_auditoria` (`ID`, `ID_USUARIO`, `MODULO`, `ACCION`, `DESCRIPCION`, `IP`, `FECHA`) VALUES
(1, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 0 instalaciones', '::1', '2025-11-18 12:21:31'),
(2, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 0 instalaciones', '::1', '2025-11-18 14:06:56'),
(3, 1, 'MINISTERIOS', 'CREAR', 'Ministerio \'SORDOS MUDOS\' creado (ID: 3) con nuevo director: Joshua', '::1', '2025-11-18 14:18:10'),
(4, 1, 'CLUBES', 'CREAR', 'Club \'SORDOS MUDOS\' creado (ID: 2) y asignado a nuevo director: Michael', '::1', '2025-11-18 14:28:55'),
(5, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 0 instalaciones', '::1', '2025-11-18 14:29:12'),
(6, 1, 'COCURRICULARES', 'CREAR', 'Co-curricular creado: NAVIDAD (ID: 1)', '::1', '2025-11-18 14:33:38'),
(7, 1, 'COCURRICULARES', 'LISTAR', 'Consultó 1 co-curriculares', '::1', '2025-11-18 14:33:38'),
(8, 1, 'EVENTOS', 'CREAR', 'Evento creado: foto de anuario (ID: 1)', '::1', '2025-11-18 14:34:55'),
(9, 1, 'BANNERS', 'CREAR', 'Banner creado: BANNER', '::1', '2025-11-18 14:54:35'),
(10, 1, 'COCURRICULARES', 'LISTAR', 'Consultó 1 co-curriculares', '::1', '2025-11-18 14:54:46'),
(11, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 0 instalaciones', '::1', '2025-11-18 14:54:48'),
(12, 1, 'INSTALACIONES', 'CREAR', 'Instalación creada: Gimnasio Universitario (ID: 1)', '::1', '2025-11-18 14:57:36'),
(13, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 1 instalaciones', '::1', '2025-11-18 14:57:36'),
(14, 1, 'INSTALACIONES', 'LISTAR', 'Consultó 1 instalaciones', '::1', '2025-11-18 15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `system_cat_usuarios`
--

DROP TABLE IF EXISTS `system_cat_usuarios`;
CREATE TABLE IF NOT EXISTS `system_cat_usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_cat_usuarios`
--

INSERT INTO `system_cat_usuarios` (`ID`, `NOMBRE`, `DESCRIPCION`, `ACTIVO`) VALUES
(1, 'SUPERUSUARIO', 'Acceso total al sistema', 'S'),
(2, 'ADMINISTRADOR', 'Administrador con permisos amplios', 'S'),
(3, 'EDITOR_INVOLUCRATE', 'Editor de m├│dulo Invol├║crate (Clubes, Ministerios, Deportes)', 'S'),
(4, 'EDITOR_EVENTOS', 'Editor de eventos y multimedia', 'S'),
(5, 'VISUALIZADOR', 'Solo puede ver informaci├│n, sin editar', 'S'),
(8, 'DIRECTOR_CLUB', 'Director de club estudiantil', 'S'),
(11, 'DIRECTOR_MINISTERIO', 'Director de ministerio', 'S');

-- --------------------------------------------------------

--
-- Table structure for table `system_modulos`
--

DROP TABLE IF EXISTS `system_modulos`;
CREATE TABLE IF NOT EXISTS `system_modulos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SLUG` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ICONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SLUG` (`SLUG`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_modulos`
--

INSERT INTO `system_modulos` (`ID`, `NOMBRE`, `SLUG`, `DESCRIPCION`, `ICONO`, `ORDEN`, `ACTIVO`) VALUES
(1, 'Dashboard', 'dashboard', 'Panel principal de administración', 'home', 1, 'S'),
(2, 'Usuarios', 'usuarios', 'Gestión de usuarios y roles', 'user', 2, 'S'),
(3, 'Anuarios', 'anuarios', 'Gestión de anuarios institucionales', 'book', 3, 'S'),
(4, 'Pulso UM', 'pulso', 'Gestión de equipo Pulso y entregas', 'users', 4, 'S'),
(5, 'Federación', 'federacion', 'Gestión de federación estudiantil', 'award', 5, 'S'),
(6, 'Clubes', 'clubes', 'Gestión de clubes estudiantiles', 'star', 6, 'S'),
(7, 'Ministerios', 'ministerios', 'Gestión de ministerios', 'heart', 7, 'S'),
(8, 'Deportes', 'deportes', 'Gestión de deportes y actividades', 'activity', 8, 'S'),
(9, 'Instalaciones', 'instalaciones', 'Gestión de instalaciones deportivas', 'map-pin', 9, 'S'),
(10, 'Co-Curriculares', 'cocurriculares', 'Gestión de servicios co-curriculares', 'briefcase', 10, 'S'),
(11, 'Eventos', 'eventos', 'Gestión de eventos y multimedia', 'calendar', 11, 'S'),
(12, 'Banners', 'banners', 'Gestión de banners informativos', 'image', 12, 'S'),
(13, 'Vida Campus', 'vida-campus', 'Gestión de amenidades del campus', 'coffee', 13, 'S'),
(14, 'Configuración', 'configuracion', 'Configuración general del sistema', 'settings', 14, 'S');

-- --------------------------------------------------------

--
-- Table structure for table `system_permisos`
--

DROP TABLE IF EXISTS `system_permisos`;
CREATE TABLE IF NOT EXISTS `system_permisos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SLUG` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SLUG` (`SLUG`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_permisos`
--

INSERT INTO `system_permisos` (`ID`, `NOMBRE`, `SLUG`, `DESCRIPCION`) VALUES
(1, 'Ver', 'ver', 'Permiso para visualizar información'),
(2, 'Crear', 'crear', 'Permiso para crear nuevos registros'),
(3, 'Editar', 'editar', 'Permiso para modificar registros existentes'),
(4, 'Eliminar', 'eliminar', 'Permiso para borrar registros'),
(5, 'Administrar', 'administrar', 'Permiso total sobre el módulo');

-- --------------------------------------------------------

--
-- Table structure for table `system_rol_modulo_permisos`
--

DROP TABLE IF EXISTS `system_rol_modulo_permisos`;
CREATE TABLE IF NOT EXISTS `system_rol_modulo_permisos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_ROL` int NOT NULL,
  `ID_MODULO` int NOT NULL,
  `ID_PERMISO` int NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_rol_modulo_permiso` (`ID_ROL`,`ID_MODULO`,`ID_PERMISO`),
  KEY `ID_MODULO` (`ID_MODULO`),
  KEY `ID_PERMISO` (`ID_PERMISO`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_uploads`
--

DROP TABLE IF EXISTS `system_uploads`;
CREATE TABLE IF NOT EXISTS `system_uploads` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FECHA` datetime DEFAULT CURRENT_TIMESTAMP,
  `URL` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_uploads`
--

INSERT INTO `system_uploads` (`ID`, `NOMBRE`, `FECHA`, `URL`) VALUES
(1, 'club_691cd0cbbc1a7.png', '2025-11-18 20:02:19', '/cpanel/assets/uploads/club_691cd0cbbc1a7.png'),
(2, 'ministerio_691cd2dc3049c.png', '2025-11-18 20:11:08', '/cpanel/assets/uploads/ministerio_691cd2dc3049c.png'),
(3, 'ministerio_691cd37433bc4.png', '2025-11-18 20:13:40', '/cpanel/assets/uploads/ministerio_691cd37433bc4.png'),
(4, 'ministerio_691cd474beb97.png', '2025-11-18 20:17:56', '/cpanel/assets/uploads/ministerio_691cd474beb97.png'),
(5, 'club_691cd4b8364a9.png', '2025-11-18 20:19:04', '/cpanel/assets/uploads/club_691cd4b8364a9.png'),
(6, 'club_691cd6f4b3c78.png', '2025-11-18 20:28:36', '/cpanel/assets/uploads/club_691cd6f4b3c78.png'),
(7, 'banner_691cd89007b7c.png', '2025-11-18 20:35:28', '/cpanel/assets/uploads/banner_691cd89007b7c.png'),
(8, 'banner_691cd8a582cf3.png', '2025-11-18 20:35:49', '/cpanel/assets/uploads/banner_691cd8a582cf3.png'),
(9, 'banner_691cdc0ff3a9e.png', '2025-11-18 20:50:24', '/cpanel/assets/uploads/banner_691cdc0ff3a9e.png'),
(10, 'banner_691cdc13a4601.png', '2025-11-18 20:50:27', '/cpanel/assets/uploads/banner_691cdc13a4601.png'),
(11, 'banner_691cdd08c8b84.png', '2025-11-18 20:54:32', '/cpanel/assets/uploads/banner_691cdd08c8b84.png');

-- --------------------------------------------------------

--
-- Table structure for table `system_usuarios`
--

DROP TABLE IF EXISTS `system_usuarios`;
CREATE TABLE IF NOT EXISTS `system_usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PASS` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_CAT` int DEFAULT NULL,
  `ID_CLUB_ASIGNADO` int DEFAULT NULL,
  `ID_MINISTERIO_ASIGNADO` int DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `PRIMER_LOGIN` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `TOKEN` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ULTIMO_ACCESO` datetime DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_CAT` (`ID_CAT`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_usuarios`
--

INSERT INTO `system_usuarios` (`ID`, `NOMBRE`, `PASS`, `EMAIL`, `ID_CAT`, `ID_CLUB_ASIGNADO`, `ID_MINISTERIO_ASIGNADO`, `ACTIVO`, `PRIMER_LOGIN`, `TOKEN`, `ULTIMO_ACCESO`, `FECHA_CREACION`) VALUES
(1, 'Suriel', '81dc9bdb52d04dc20036dbd8313ed055', NULL, 1, NULL, NULL, 'S', 'N', 'mnG1NugDVsQ6WcO8lw3MYJPiteqvLT2pxbhRrB9z', '2025-11-18 15:36:59', '2025-10-03 07:12:35'),
(2, 'admin', '8ab673d3dbeebef06336765eb7e32df6', NULL, 2, NULL, NULL, 'S', 'N', NULL, NULL, '2025-10-03 07:12:35'),
(10, 'Joshua', '25d55ad283aa400af464c76d713c07ad', 'starrynight@gmail.com', 11, NULL, NULL, 'S', 'S', 'de05e74d8be173954a4ae0da704078ab', NULL, '2025-11-18 14:18:10'),
(12, 'Michael', '25d55ad283aa400af464c76d713c07ad', '1220593@alumno.um.edu.mx', 8, 2, NULL, 'S', 'S', '7e2775a0f659febf28f58f3cf1d8f57f', NULL, '2025-11-18 14:28:55'),
(13, 'COBB', '15bbd36a7b76899e9edc35dc983eca10', NULL, 1, NULL, NULL, 'S', 'S', NULL, NULL, '2025-11-18 15:09:04'),
(14, 'JESSICA', '81dc9bdb52d04dc20036dbd8313ed055', NULL, 2, NULL, NULL, 'S', 'S', NULL, NULL, '2025-11-18 15:09:25');

-- --------------------------------------------------------

--
-- Table structure for table `vre_amenidades`
--

DROP TABLE IF EXISTS `vre_amenidades`;
CREATE TABLE IF NOT EXISTS `vre_amenidades` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO` enum('COMEDOR','SNACK','RESIDENCIA','BIBLIOTECA','TIENDA','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'OTRO',
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `UBICACION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `COORDENADAS` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `HORARIOS` text COLLATE utf8mb4_unicode_ci,
  `SERVICIOS` text COLLATE utf8mb4_unicode_ci,
  `MENU` text COLLATE utf8mb4_unicode_ci,
  `PRECIOS` text COLLATE utf8mb4_unicode_ci,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_amenidades`
--

INSERT INTO `vre_amenidades` (`ID`, `NOMBRE`, `TIPO`, `DESCRIPCION`, `UBICACION`, `COORDENADAS`, `HORARIOS`, `SERVICIOS`, `MENU`, `PRECIOS`, `IMAGEN_URL`, `GALERIA`, `CONTACTO`, `TELEFONO`, `EMAIL`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 'Comedor Universitario', 'COMEDOR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 1, '2025-10-03 07:21:24'),
(2, 'Snack UM', 'SNACK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 2, '2025-10-03 07:21:24'),
(3, 'Residencias Estudiantiles', 'RESIDENCIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 3, '2025-10-03 07:21:24');

-- --------------------------------------------------------

--
-- Table structure for table `vre_anuarios`
--

DROP TABLE IF EXISTS `vre_anuarios`;
CREATE TABLE IF NOT EXISTS `vre_anuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ANIO` year NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `DECADA` int DEFAULT NULL,
  `ES_CONMEMORATIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `RAZON_CONMEMORATIVA` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_PORTADA` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PDF_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TOTAL_PAGINAS` int DEFAULT '0',
  `FOTOGRAFOS` text COLLATE utf8mb4_unicode_ci,
  `CONTRIBUYENTES` text COLLATE utf8mb4_unicode_ci,
  `LIKES` int DEFAULT '0',
  `VISTAS` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  `ID_USUARIO_CREADOR` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_USUARIO_CREADOR` (`ID_USUARIO_CREADOR`),
  KEY `idx_anio` (`ANIO`),
  KEY `idx_decada` (`DECADA`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_anuarios`
--

INSERT INTO `vre_anuarios` (`ID`, `TITULO`, `ANIO`, `DESCRIPCION`, `DECADA`, `ES_CONMEMORATIVO`, `RAZON_CONMEMORATIVA`, `IMAGEN_PORTADA`, `PDF_URL`, `TOTAL_PAGINAS`, `FOTOGRAFOS`, `CONTRIBUYENTES`, `LIKES`, `VISTAS`, `ACTIVO`, `FECHA_CREACION`, `ID_USUARIO_CREADOR`) VALUES
(1, 'ANUARIOUM 2026', '2026', 'ANUARIO NUMERO 2026', 2020, 'N', '', '', 'https://online.fliphtml5.com/VidaEstudiantilUM/ovkc/#p=5', 10, 'CHRIS, JOSHUA, STAFF DE PULSO', 'UNIVER', 1, 10, 'S', '2025-10-03 08:22:37', 1),
(2, 'ANUARIO VIDA ESTUDIANTIL 2011', '2011', 'Anuario Del 2011', 2010, 'N', '', '', 'https://online.fliphtml5.com/VidaEstudiantilUM/thjg/', 189, 'JOHAN, CHRIS, SANTOS , BEJARANO', 'JOSHUA BEJARANO, ANNY TORRES \r\n', 1, 7, 'S', '2025-10-03 11:29:20', 1),
(3, 'anuario prueba', '2027', 'asdfxgchjh', 2020, 'S', 'defargstr', '', '/vidaEstudiantil/uploads/anuarios/anuario_1760066404_68e87b641bb18.pdf', 189, 'eafrsgtdh', 'eafrsdgtfy', 1, 10, 'S', '2025-10-09 21:20:04', 1),
(4, 'ANUARIO 2028', '2028', 'ANUARIO PARA EL AÑO 2028', 2020, 'N', '', '', '/vidaEstudiantil/uploads/anuarios/anuario_1761141266_68f8e21239933.pdf', 189, 'JOHAN, ARELY, LUCIA.', 'DEBORA, ANGEL,', 0, 0, 'S', '2025-10-22 07:54:26', 1),
(5, 'ANUARIO 1945', '1945', 'ANUARIO DEL 1945', 1950, 'S', 'ANIVERSARIO', '', '/vidaEstudiantil/uploads/anuarios/anuario_1761152089_68f90c596a61c.pdf', 61, 'CHRIS, OTRAS PERSONAS', 'UM MEDIA', 1, 3, 'S', '2025-10-22 10:54:49', 1),
(6, 'ANUARIO PRUEBA NUEVA', '2029', 'ANUARIO DE PRUEBA DE 2029', 2020, 'S', 'ANIVERSARIO 83', 'https://cdn.um.click/archivos/2024/07/img-AdL8mtOrHE67vKo1.png', '/vidaEstudiantil/uploads/anuarios/anuario_1762278228_690a3b54b55c4.pdf', 189, 'JOHAN, SURIEL ,JOSHUA', 'UMEDIA', 1, 4, 'S', '2025-11-04 11:43:48', 1),
(7, 'anuario nuevo prueba', '2060', 'fsdfa', 2010, 'N', '', '', '/vidaEstudiantil/uploads/anuarios/anuario_1763483046_691c9da66b291.pdf', 1234, 'JOSHUA BEJARANO, johan', 'UM PULSO', 0, 0, 'N', '2025-11-18 10:24:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vre_anuarios_fotos_estudiantes`
--

DROP TABLE IF EXISTS `vre_anuarios_fotos_estudiantes`;
CREATE TABLE IF NOT EXISTS `vre_anuarios_fotos_estudiantes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_ANUARIO` int DEFAULT NULL,
  `MATRICULA` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NOMBRE_ESTUDIANTE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CARRERA` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FACULTAD` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FOTO_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ANIO` year DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  PRIMARY KEY (`ID`),
  KEY `ID_ANUARIO` (`ID_ANUARIO`),
  KEY `idx_matricula` (`MATRICULA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vre_anuarios_likes`
--

DROP TABLE IF EXISTS `vre_anuarios_likes`;
CREATE TABLE IF NOT EXISTS `vre_anuarios_likes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_ANUARIO` int NOT NULL,
  `ID_USUARIO` int DEFAULT NULL,
  `MATRICULA` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IP` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FECHA` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_like` (`ID_ANUARIO`,`ID_USUARIO`,`MATRICULA`,`IP`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_anuarios_likes`
--

INSERT INTO `vre_anuarios_likes` (`ID`, `ID_ANUARIO`, `ID_USUARIO`, `MATRICULA`, `IP`, `FECHA`) VALUES
(2, 1, 1, NULL, '::1', '2025-10-09 10:09:31'),
(3, 2, 1, NULL, '::1', '2025-10-09 10:09:56'),
(5, 3, 1, NULL, '::1', '2025-10-19 16:59:48'),
(7, 5, 1, NULL, '::1', '2025-10-22 11:00:58'),
(8, 6, 1, NULL, '::1', '2025-11-04 11:45:15');

-- --------------------------------------------------------

--
-- Table structure for table `vre_banners`
--

DROP TABLE IF EXISTS `vre_banners`;
CREATE TABLE IF NOT EXISTS `vre_banners` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IMAGEN_MOBILE_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ENLACE` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TIPO` enum('EVENTO','INFORMATIVO','URGENTE','PROMOCIONAL') COLLATE utf8mb4_unicode_ci DEFAULT 'INFORMATIVO',
  `UBICACION` enum('HOME','INVOLUCRATE','EVENTOS','TODAS') COLLATE utf8mb4_unicode_ci DEFAULT 'HOME',
  `FECHA_INICIO` date DEFAULT NULL,
  `FECHA_FIN` date DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `VISTAS` int DEFAULT '0',
  `CLICKS` int DEFAULT '0',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `idx_activo` (`ACTIVO`,`FECHA_INICIO`,`FECHA_FIN`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_banners`
--

INSERT INTO `vre_banners` (`ID`, `TITULO`, `DESCRIPCION`, `IMAGEN_URL`, `IMAGEN_MOBILE_URL`, `ENLACE`, `TIPO`, `UBICACION`, `FECHA_INICIO`, `FECHA_FIN`, `ACTIVO`, `VISTAS`, `CLICKS`, `ORDEN`, `FECHA_CREACION`) VALUES
(7, 'BANNER', 'adsfgchvjbkbhgfcds', 'http://localhost/cpanel/assets/uploads/banner_691cdd08c8b84.png', NULL, NULL, 'INFORMATIVO', 'HOME', '2025-11-18', '2025-11-27', 'S', 0, 0, 1, '2025-11-18 14:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `vre_cargos`
--

DROP TABLE IF EXISTS `vre_cargos`;
CREATE TABLE IF NOT EXISTS `vre_cargos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) NOT NULL,
  `DESCRIPCION` text,
  `TIPO` enum('FEDERACION','PULSO','GENERAL') DEFAULT 'GENERAL',
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NOMBRE` (`NOMBRE`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vre_cargos`
--

INSERT INTO `vre_cargos` (`ID`, `NOMBRE`, `DESCRIPCION`, `TIPO`, `ORDEN`, `ACTIVO`, `FECHA_CREACION`) VALUES
(1, 'PRESIDENTE', 'Presidente de la organización', 'FEDERACION', 1, 'S', '2025-11-09 18:23:42'),
(2, 'VICEPRESIDENTE', 'Vicepresidente', 'FEDERACION', 2, 'S', '2025-11-09 18:23:42'),
(3, 'SECRETARIO', 'Secretario', 'FEDERACION', 3, 'S', '2025-11-09 18:23:42'),
(4, 'TESORERO', 'Tesorero', 'FEDERACION', 4, 'S', '2025-11-09 18:23:42'),
(5, 'VOCAL', 'Vocal', 'FEDERACION', 5, 'S', '2025-11-09 18:23:42'),
(6, 'DIRECTOR', 'Director General', 'PULSO', 1, 'S', '2025-11-09 18:23:42'),
(7, 'SUBDIRECTOR', 'Subdirector', 'PULSO', 2, 'S', '2025-11-09 18:23:42'),
(8, 'EDITOR', 'Editor', 'PULSO', 3, 'S', '2025-11-09 18:23:42'),
(9, 'REDACTOR', 'Redactor', 'PULSO', 4, 'S', '2025-11-09 18:23:42'),
(10, 'FOTÓGRAFO', 'Fotógrafo', 'PULSO', 5, 'S', '2025-11-09 18:23:42'),
(11, 'DISEÑADOR', 'Diseñador', 'PULSO', 6, 'S', '2025-11-09 18:23:42'),
(12, 'FOT├ôGRAFO', 'Fot├│grafo', 'PULSO', 5, 'S', '2025-11-18 12:03:52'),
(13, 'DISE├æADOR', 'Dise├▒ador', 'PULSO', 6, 'S', '2025-11-18 12:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `vre_clubes`
--

DROP TABLE IF EXISTS `vre_clubes`;
CREATE TABLE IF NOT EXISTS `vre_clubes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `OBJETIVO` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `BENEFICIOS` text COLLATE utf8mb4_unicode_ci,
  `HORARIO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIA_REUNION` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CUPO_MAXIMO` int DEFAULT NULL,
  `CUPO_ACTUAL` int DEFAULT '0',
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_DIRECTOR_USUARIO` int DEFAULT NULL,
  `REDES_SOCIALES` text COLLATE utf8mb4_unicode_ci,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_clubes`
--

INSERT INTO `vre_clubes` (`ID`, `NOMBRE`, `DESCRIPCION`, `OBJETIVO`, `REQUISITOS`, `BENEFICIOS`, `HORARIO`, `DIA_REUNION`, `LUGAR`, `CUPO_MAXIMO`, `CUPO_ACTUAL`, `IMAGEN_URL`, `GALERIA`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_CONTACTO`, `EMAIL`, `TELEFONO`, `ID_DIRECTOR_USUARIO`, `REDES_SOCIALES`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(2, 'SORDOS MUDOS', 'RSTDYFUYTDRSDYFUYTD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 'S', 0, '2025-11-18 14:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `vre_cocurriculares`
--

DROP TABLE IF EXISTS `vre_cocurriculares`;
CREATE TABLE IF NOT EXISTS `vre_cocurriculares` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO` enum('PROGRAMA','SERVICIO','APOYO','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'PROGRAMA',
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `OBJETIVO` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `BENEFICIOS` text COLLATE utf8mb4_unicode_ci,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `HORARIOS` text COLLATE utf8mb4_unicode_ci,
  `UBICACION` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CUPO_MAXIMO` int DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_cocurriculares`
--

INSERT INTO `vre_cocurriculares` (`ID`, `NOMBRE`, `TIPO`, `DESCRIPCION`, `OBJETIVO`, `REQUISITOS`, `BENEFICIOS`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_EMAIL`, `RESPONSABLE_TELEFONO`, `HORARIOS`, `UBICACION`, `CUPO_MAXIMO`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 'NAVIDAD', 'PROGRAMA', 'ERSDTFYGUHILO;P', 'FGHKJLOK;P\'L[', 'FGHKIJLIO;KP', 'GHJKLOK;P[', 'COBB', '1220593@alumno.um.edu.mx', '8114853560', 'lun-ver:4-6pm', 'Iglesia Universitaria', NULL, 'S', 1, '2025-11-18 14:33:38');

-- --------------------------------------------------------

--
-- Table structure for table `vre_config`
--

DROP TABLE IF EXISTS `vre_config`;
CREATE TABLE IF NOT EXISTS `vre_config` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CLAVE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VALOR` text COLLATE utf8mb4_unicode_ci,
  `TIPO` enum('TEXT','JSON','URL','NUMBER','BOOLEAN') COLLATE utf8mb4_unicode_ci DEFAULT 'TEXT',
  `DESCRIPCION` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CATEGORIA` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ULTIMA_ACTUALIZACION` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CLAVE` (`CLAVE`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_config`
--

INSERT INTO `vre_config` (`ID`, `CLAVE`, `VALOR`, `TIPO`, `DESCRIPCION`, `CATEGORIA`, `ULTIMA_ACTUALIZACION`) VALUES
(1, 'video_hero_url', '', 'URL', 'URL del video principal de la página de inicio', 'HOME', '2025-10-03 07:21:39'),
(2, 'video_hero_poster', '', 'URL', 'Imagen poster del video hero', 'HOME', '2025-10-03 07:21:39'),
(3, 'paleta_colores', '{\"primario\":\"#003366\",\"secundario\":\"#FFD700\",\"acento\":\"#CC0000\"}', 'JSON', 'JSON con colores institucionales', 'DISEÑO', '2025-10-03 07:21:39'),
(4, 'redes_sociales', '{\"facebook\":\"\",\"instagram\":\"\",\"youtube\":\"\",\"twitter\":\"\"}', 'JSON', 'JSON con enlaces de redes sociales', 'GENERAL', '2025-10-03 07:21:39'),
(5, 'email_contacto', '', 'TEXT', 'Email de contacto general', 'GENERAL', '2025-10-03 07:21:39'),
(6, 'telefono_contacto', '', 'TEXT', 'Teléfono de contacto general', 'GENERAL', '2025-10-03 07:21:39'),
(7, 'direccion', '', 'TEXT', 'Dirección física de la universidad', 'GENERAL', '2025-10-03 07:21:39'),
(8, 'logo_url', '', 'URL', 'URL del logo institucional', 'DISEÑO', '2025-10-03 07:21:39'),
(9, 'favicon_url', '', 'URL', 'URL del favicon', 'DISEÑO', '2025-10-03 07:21:39'),
(10, 'meta_description', '', 'TEXT', 'Descripción meta para SEO', 'SEO', '2025-10-03 07:21:39'),
(11, 'meta_keywords', '', 'TEXT', 'Keywords para SEO', 'SEO', '2025-10-03 07:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `vre_deportes`
--

DROP TABLE IF EXISTS `vre_deportes`;
CREATE TABLE IF NOT EXISTS `vre_deportes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `REGLAS` text COLLATE utf8mb4_unicode_ci,
  `BENEFICIOS` text COLLATE utf8mb4_unicode_ci,
  `EQUIPO_NECESARIO` text COLLATE utf8mb4_unicode_ci,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_deportes`
--

INSERT INTO `vre_deportes` (`ID`, `NOMBRE`, `DESCRIPCION`, `REGLAS`, `BENEFICIOS`, `EQUIPO_NECESARIO`, `IMAGEN_URL`, `GALERIA`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_CONTACTO`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 'BASKETBALL', 'Baloncesto competitivo - Equipos masculinos y femeninos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 1, '2025-11-18 14:21:04'),
(2, 'VOLEIBOL', 'Voleibol de alto rendimiento - Categor├¡as masculina, femenina y mixta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 2, '2025-11-18 14:21:04'),
(3, 'FUTBOL', 'F├║tbol soccer competitivo - Liga universitaria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 3, '2025-11-18 14:21:04'),
(4, 'FUTBOL AMERICANO', 'F├║tbol americano - Equipos representativos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 4, '2025-11-18 14:21:04'),
(5, 'SOFTBALL', 'Softball universitario - Categor├¡as masculina y femenina', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 5, '2025-11-18 14:21:04'),
(6, 'ATLETISMO', 'Atletismo y carreras de pista - Competencias inter-universitarias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 6, '2025-11-18 14:21:04'),
(7, 'NATACI├ôN', 'Nataci├│n competitiva - Eventos individuales y relevos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 7, '2025-11-18 14:21:04'),
(8, 'TENIS', 'Tenis - Categor├¡as individuales y dobles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 8, '2025-11-18 14:21:04'),
(9, 'ARTES MARCIALES', 'Artes marciales mixtas y defensa personal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 9, '2025-11-18 14:21:04'),
(10, 'GIMNASIO', 'Entrenamiento funcional y fitness', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 10, '2025-11-18 14:21:04'),
(11, 'BASKETBALL', 'Baloncesto competitivo - Equipos masculinos y femeninos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 1, '2025-11-18 14:23:50'),
(12, 'VOLEIBOL', 'Voleibol de alto rendimiento - Categor├¡as masculina, femenina y mixta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 2, '2025-11-18 14:23:50'),
(13, 'FUTBOL', 'F├║tbol soccer competitivo - Liga universitaria', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 3, '2025-11-18 14:23:50'),
(14, 'FUTBOL AMERICANO', 'F├║tbol americano - Equipos representativos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 4, '2025-11-18 14:23:50'),
(15, 'SOFTBALL', 'Softball universitario - Categor├¡as masculina y femenina', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 5, '2025-11-18 14:23:50'),
(16, 'ATLETISMO', 'Atletismo y carreras de pista - Competencias inter-universitarias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 6, '2025-11-18 14:23:50'),
(17, 'NATACI├ôN', 'Nataci├│n competitiva - Eventos individuales y relevos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 7, '2025-11-18 14:23:50'),
(18, 'TENIS', 'Tenis - Categor├¡as individuales y dobles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 8, '2025-11-18 14:23:50'),
(19, 'ARTES MARCIALES', 'Artes marciales mixtas y defensa personal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 9, '2025-11-18 14:23:50'),
(20, 'GIMNASIO', 'Entrenamiento funcional y fitness', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 10, '2025-11-18 14:23:50');

-- --------------------------------------------------------

--
-- Table structure for table `vre_deportes_actividades`
--

DROP TABLE IF EXISTS `vre_deportes_actividades`;
CREATE TABLE IF NOT EXISTS `vre_deportes_actividades` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_DEPORTE` int NOT NULL,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `TIPO` enum('ENTRENAMIENTO','PARTIDO','TORNEO','EVENTO') COLLATE utf8mb4_unicode_ci DEFAULT 'ENTRENAMIENTO',
  `FECHA_EVENTO` date DEFAULT NULL,
  `HORA_INICIO` time DEFAULT NULL,
  `HORA_FIN` time DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CUPO_MAXIMO` int DEFAULT NULL,
  `INSCRITOS` int DEFAULT '0',
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ESTADO` enum('PROGRAMADO','EN_CURSO','FINALIZADO','CANCELADO') COLLATE utf8mb4_unicode_ci DEFAULT 'PROGRAMADO',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_DEPORTE` (`ID_DEPORTE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vre_directiva_clubes`
--

DROP TABLE IF EXISTS `vre_directiva_clubes`;
CREATE TABLE IF NOT EXISTS `vre_directiva_clubes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_CLUB` int NOT NULL,
  `DIRECTOR_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ESTADO` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `FECHA_ACTUALIZACION` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_CLUB` (`ID_CLUB`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_directiva_clubes`
--

INSERT INTO `vre_directiva_clubes` (`ID`, `ID_CLUB`, `DIRECTOR_NOMBRE`, `DIRECTOR_EMAIL`, `DIRECTOR_TELEFONO`, `DIRECTOR_FOTO`, `SUBDIRECTOR_NOMBRE`, `SUBDIRECTOR_EMAIL`, `SUBDIRECTOR_TELEFONO`, `SUBDIRECTOR_FOTO`, `SECRETARIO_NOMBRE`, `SECRETARIO_EMAIL`, `SECRETARIO_TELEFONO`, `SECRETARIO_FOTO`, `TESORERO_NOMBRE`, `TESORERO_EMAIL`, `TESORERO_TELEFONO`, `TESORERO_FOTO`, `CAPELLAN_NOMBRE`, `CAPELLAN_EMAIL`, `CAPELLAN_TELEFONO`, `CAPELLAN_FOTO`, `CONSEJERO_GENERAL_NOMBRE`, `CONSEJERO_GENERAL_EMAIL`, `CONSEJERO_GENERAL_TELEFONO`, `CONSEJERO_GENERAL_FOTO`, `LOGISTICA_NOMBRE`, `LOGISTICA_EMAIL`, `LOGISTICA_TELEFONO`, `LOGISTICA_FOTO`, `MEDIA_NOMBRE`, `MEDIA_EMAIL`, `MEDIA_TELEFONO`, `MEDIA_FOTO`, `ESTADO`, `FECHA_ACTUALIZACION`) VALUES
(1, 2, 'Joshua Bejarano', '1220593@alumno.um.edu.mx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'activo', '2025-11-18 14:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `vre_directiva_ministerios`
--

DROP TABLE IF EXISTS `vre_directiva_ministerios`;
CREATE TABLE IF NOT EXISTS `vre_directiva_ministerios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_MINISTERIO` int NOT NULL,
  `DIRECTOR_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIRECTOR_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SUBDIRECTOR_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SECRETARIO_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TESORERO_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPELLAN_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONSEJERO_GENERAL_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LOGISTICA_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MEDIA_FOTO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ESTADO` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `FECHA_ACTUALIZACION` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_MINISTERIO` (`ID_MINISTERIO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_directiva_ministerios`
--

INSERT INTO `vre_directiva_ministerios` (`ID`, `ID_MINISTERIO`, `DIRECTOR_NOMBRE`, `DIRECTOR_EMAIL`, `DIRECTOR_TELEFONO`, `DIRECTOR_FOTO`, `SUBDIRECTOR_NOMBRE`, `SUBDIRECTOR_EMAIL`, `SUBDIRECTOR_TELEFONO`, `SUBDIRECTOR_FOTO`, `SECRETARIO_NOMBRE`, `SECRETARIO_EMAIL`, `SECRETARIO_TELEFONO`, `SECRETARIO_FOTO`, `TESORERO_NOMBRE`, `TESORERO_EMAIL`, `TESORERO_TELEFONO`, `TESORERO_FOTO`, `CAPELLAN_NOMBRE`, `CAPELLAN_EMAIL`, `CAPELLAN_TELEFONO`, `CAPELLAN_FOTO`, `CONSEJERO_GENERAL_NOMBRE`, `CONSEJERO_GENERAL_EMAIL`, `CONSEJERO_GENERAL_TELEFONO`, `CONSEJERO_GENERAL_FOTO`, `LOGISTICA_NOMBRE`, `LOGISTICA_EMAIL`, `LOGISTICA_TELEFONO`, `LOGISTICA_FOTO`, `MEDIA_NOMBRE`, `MEDIA_EMAIL`, `MEDIA_TELEFONO`, `MEDIA_FOTO`, `ESTADO`, `FECHA_ACTUALIZACION`) VALUES
(1, 3, 'Joshua Bejarano', 'starrynight@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'activo', '2025-11-18 14:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `vre_entrega_anuario`
--

DROP TABLE IF EXISTS `vre_entrega_anuario`;
CREATE TABLE IF NOT EXISTS `vre_entrega_anuario` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ANIO` year NOT NULL,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `FECHA_ENTREGA` date DEFAULT NULL,
  `HORA_INICIO` time DEFAULT NULL,
  `HORA_FIN` time DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `INSTRUCCIONES` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ANIO` (`ANIO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vre_estudiantes`
--

DROP TABLE IF EXISTS `vre_estudiantes`;
CREATE TABLE IF NOT EXISTS `vre_estudiantes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MATRICULA` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NOMBRE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `APELLIDO` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CARRERA` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SEMESTRE` int DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_REGISTRO` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `MATRICULA` (`MATRICULA`),
  KEY `idx_matricula` (`MATRICULA`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_estudiantes`
--

INSERT INTO `vre_estudiantes` (`ID`, `MATRICULA`, `NOMBRE`, `APELLIDO`, `CARRERA`, `SEMESTRE`, `EMAIL`, `ACTIVO`, `FECHA_REGISTRO`) VALUES
(1, '1220593', 'Usuario', 'Ejemplo', 'Ingeniería en Sistemas', 5, 'ejemplo@um.edu.mx', 'S', '2025-11-17 13:23:59'),
(7, '1220527', 'Cris', 'Castellanos', 'IECE', 5, NULL, 'S', '2025-11-04 13:58:17'),
(8, '9801131', '', '', NULL, NULL, NULL, 'S', '2025-11-05 10:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `vre_eventos`
--

DROP TABLE IF EXISTS `vre_eventos`;
CREATE TABLE IF NOT EXISTS `vre_eventos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SLUG` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `DESCRIPCION_CORTA` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FECHA_EVENTO` datetime DEFAULT NULL,
  `FECHA_FIN` datetime DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ORGANIZADOR` enum('FEDERACION','CULTURALES','DEPORTIVO','ESPIRITUAL','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'OTRO',
  `ORGANIZADOR_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CATEGORIA` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_PRINCIPAL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `COSTO` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CUPO_MAXIMO` int DEFAULT NULL,
  `REGISTRO_REQUERIDO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `ENLACE_REGISTRO` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ESTADO` enum('PROXIMO','EN_CURSO','FINALIZADO','CANCELADO') COLLATE utf8mb4_unicode_ci DEFAULT 'PROXIMO',
  `DESTACADO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N',
  `LIKES` int DEFAULT '0',
  `VISTAS` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  `ID_USUARIO_CREADOR` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SLUG` (`SLUG`),
  KEY `ID_USUARIO_CREADOR` (`ID_USUARIO_CREADOR`),
  KEY `idx_fecha` (`FECHA_EVENTO`),
  KEY `idx_estado` (`ESTADO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_eventos`
--

INSERT INTO `vre_eventos` (`ID`, `TITULO`, `SLUG`, `DESCRIPCION`, `DESCRIPCION_CORTA`, `FECHA_EVENTO`, `FECHA_FIN`, `LUGAR`, `ORGANIZADOR`, `ORGANIZADOR_NOMBRE`, `CATEGORIA`, `IMAGEN_PRINCIPAL`, `COSTO`, `CUPO_MAXIMO`, `REGISTRO_REQUERIDO`, `ENLACE_REGISTRO`, `ESTADO`, `DESTACADO`, `LIKES`, `VISTAS`, `ACTIVO`, `FECHA_CREACION`, `ID_USUARIO_CREADOR`) VALUES
(1, 'foto de anuario', 'foto-de-anuario', 'entrega de la foto de anuario', NULL, '2025-11-18 17:34:00', '2025-11-29 19:34:00', 'VICERRECTORIA', 'FEDERACION', NULL, NULL, NULL, NULL, NULL, 'N', NULL, 'EN_CURSO', 'S', 0, 0, 'S', '2025-11-18 14:34:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vre_eventos_multimedia`
--

DROP TABLE IF EXISTS `vre_eventos_multimedia`;
CREATE TABLE IF NOT EXISTS `vre_eventos_multimedia` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_EVENTO` int NOT NULL,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `TIPO` enum('VIDEO','FOTO','ALBUM','ENLACE','YOUTUBE','DRIVE') COLLATE utf8mb4_unicode_ci DEFAULT 'ENLACE',
  `URL` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `IMAGEN_PREVIEW` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_EVENTO` (`ID_EVENTO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vre_federacion_info`
--

DROP TABLE IF EXISTS `vre_federacion_info`;
CREATE TABLE IF NOT EXISTS `vre_federacion_info` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CONTENIDO_QUE_ES` text COLLATE utf8mb4_unicode_ci,
  `CONTENIDO_ELECCION` text COLLATE utf8mb4_unicode_ci,
  `CONTENIDO_ACTIVIDADES` text COLLATE utf8mb4_unicode_ci,
  `CONTENIDO_PARA_QUE_SIRVE` text COLLATE utf8mb4_unicode_ci,
  `VIDEO_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_PRINCIPAL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ULTIMA_ACTUALIZACION` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_federacion_info`
--

INSERT INTO `vre_federacion_info` (`ID`, `TITULO`, `CONTENIDO_QUE_ES`, `CONTENIDO_ELECCION`, `CONTENIDO_ACTIVIDADES`, `CONTENIDO_PARA_QUE_SIRVE`, `VIDEO_URL`, `IMAGEN_PRINCIPAL`, `ULTIMA_ACTUALIZACION`) VALUES
(1, 'Federación Estudiantil UM', '<p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв 	</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p><p><br></p>', '<p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p>', '<p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p>', '<p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p>la federacion estudiantil es ыфн нщгк фку тще ьшту огые ф цфыеу ща ьн ешьу ш д дуе нщг пщ пгуыуыы ш дцшдд тумук лтщц ш фь ыгку ершы фтньщку щррщр ыщгдв вЫВпфвлпофвп флопрвф пфлвоп флопф флпоифв</p><p><br></p>', 'https://www.um.edu.mx/assets/video/home.mp4', '', '2025-11-09 17:13:13');

-- --------------------------------------------------------

--
-- Table structure for table `vre_federacion_miembros`
--

DROP TABLE IF EXISTS `vre_federacion_miembros`;
CREATE TABLE IF NOT EXISTS `vre_federacion_miembros` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PUESTO` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_CARGO` int DEFAULT NULL,
  `ANIO` year NOT NULL,
  `MATRICULA` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CARRERA` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FOTO_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FLICKR_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BIO` text COLLATE utf8mb4_unicode_ci,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_federacion_cargo` (`ID_CARGO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_federacion_miembros`
--

INSERT INTO `vre_federacion_miembros` (`ID`, `NOMBRE`, `PUESTO`, `ID_CARGO`, `ANIO`, `MATRICULA`, `CARRERA`, `FOTO_URL`, `FLICKR_URL`, `BIO`, `EMAIL`, `TELEFONO`, `ORDEN`, `ACTIVO`, `FECHA_CREACION`) VALUES
(1, 'JOSHUA BEJARANO', 'PRESIDENTE', NULL, '2025', '1220593', 'INGENIERÍA EN SISTEMAS COMPUTACIONALES', '/vidaEstudiantil/uploads/federacion/miembro_1761146000_68f8f49091437.jpg', NULL, 'Joshua is a magnificent student, hard-working and strong-willed, who makes whatever he wants and puts his mind to it, carismatic and good looking... excellent body and mind ', '1220593@alumno.um.edu.mx', '9452093095', 1, 'S', '2025-10-22 09:13:20');

-- --------------------------------------------------------

--
-- Table structure for table `vre_galeria`
--

DROP TABLE IF EXISTS `vre_galeria`;
CREATE TABLE IF NOT EXISTS `vre_galeria` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MODULO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'clubes, ministerios, deportes, ligas, eventos, banners',
  `ID_REGISTRO` int NOT NULL COMMENT 'ID del registro en la tabla correspondiente',
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `URL_IMAGEN` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO` enum('principal','galeria','banner','evento') COLLATE utf8mb4_unicode_ci DEFAULT 'galeria',
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `SUBIDO_POR` int DEFAULT NULL,
  `FECHA_SUBIDA` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `SUBIDO_POR` (`SUBIDO_POR`),
  KEY `idx_modulo_registro` (`MODULO`,`ID_REGISTRO`),
  KEY `idx_tipo` (`TIPO`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_galeria`
--

INSERT INTO `vre_galeria` (`ID`, `MODULO`, `ID_REGISTRO`, `TITULO`, `DESCRIPCION`, `URL_IMAGEN`, `TIPO`, `ORDEN`, `ACTIVO`, `SUBIDO_POR`, `FECHA_SUBIDA`) VALUES
(2, 'ministerios', 3, 'SORDOS MUDOS - Principal', NULL, 'http://localhost/cpanel/assets/uploads/ministerio_691cd474beb97.png', 'principal', 1, 'S', 1, '2025-11-18 14:18:10'),
(3, 'clubes', 2, 'SORDOS MUDOS - Principal', NULL, 'http://localhost/cpanel/assets/uploads/club_691cd6f4b3c78.png', 'principal', 1, 'S', 1, '2025-11-18 14:28:55'),
(4, 'banners', 7, 'BANNER', 'adsfgchvjbkbhgfcds', 'http://localhost/cpanel/assets/uploads/banner_691cdd08c8b84.png', 'principal', 1, 'S', 1, '2025-11-18 14:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `vre_instalaciones_deportivas`
--

DROP TABLE IF EXISTS `vre_instalaciones_deportivas`;
CREATE TABLE IF NOT EXISTS `vre_instalaciones_deportivas` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO` enum('CANCHA','GYM','PISCINA','PISTA','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'OTRO',
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `UBICACION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `COORDENADAS` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CAPACIDAD` int DEFAULT NULL,
  `HORARIOS` text COLLATE utf8mb4_unicode_ci,
  `SERVICIOS` text COLLATE utf8mb4_unicode_ci,
  `REGLAS` text COLLATE utf8mb4_unicode_ci,
  `COSTO` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `DISPONIBLE` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_instalaciones_deportivas`
--

INSERT INTO `vre_instalaciones_deportivas` (`ID`, `NOMBRE`, `TIPO`, `DESCRIPCION`, `UBICACION`, `COORDENADAS`, `CAPACIDAD`, `HORARIOS`, `SERVICIOS`, `REGLAS`, `COSTO`, `IMAGEN_URL`, `GALERIA`, `DISPONIBLE`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 'Gimnasio Universitario', 'GYM', 'el gimnasion universitario forma parte de las instalaciones con las que cuenta la universidad, en donde permite el desarrollo fisico de los alumnos, con actividades deportivas distintas. Cuenta con 2 canchas designadas para el basquetbol y 2 canchas para voleibol', 'Incredible Pizza https://maps.apple.com/place?address=Av.%20Lazaro%20Cardenas%20999%20Ote,%20Torres%20Brisas,%2064780%20Monterrey,%20NL,%20Mexico&coordinate=25.617115,-100.276836&name=Incredible%20Piz', NULL, 200, 'LUN-VER:5-6PM', 'DUCHAS\r\nBAÑOS \r\nAGUA', 'ROPA ADECUADA QUE CUMPLA CON LOS REGLAMENTOS DE LA UNIVERSIDAD', 'GRATIS', NULL, NULL, 'S', 'S', 1, '2025-11-18 14:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `vre_ligas`
--

DROP TABLE IF EXISTS `vre_ligas`;
CREATE TABLE IF NOT EXISTS `vre_ligas` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_DEPORTE` int NOT NULL,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FECHA_INICIO` date DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FOTO_RESPONSABLE` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ESTADO` enum('EN_PREPARACION','EN_CURSO','PAUSADO','CANCELADO') COLLATE utf8mb4_unicode_ci DEFAULT 'EN_PREPARACION',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_DEPORTE` (`ID_DEPORTE`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_ligas`
--

INSERT INTO `vre_ligas` (`ID`, `ID_DEPORTE`, `NOMBRE`, `FECHA_INICIO`, `DESCRIPCION`, `REQUISITOS`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_CONTACTO`, `FOTO_RESPONSABLE`, `EMAIL`, `TELEFONO`, `ACTIVO`, `ESTADO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 2, 'LUVM', '2025-11-18', 'wesrtyuiuo', 'sadfghjk\r\nsdfghj\r\nasdfgh', 'NOSE', '87466214', '', 'bejaranojoshua5@gmail.com', '8114853560', 'S', 'EN_CURSO', 0, '2025-11-18 14:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `vre_ministerios`
--

DROP TABLE IF EXISTS `vre_ministerios`;
CREATE TABLE IF NOT EXISTS `vre_ministerios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO` enum('JUVENIL','MUSICAL','INSTRUMENTAL','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'OTRO',
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `OBJETIVO` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `ACTIVIDADES` text COLLATE utf8mb4_unicode_ci,
  `HORARIO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DIA_REUNION` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_DIRECTOR_USUARIO` int DEFAULT NULL,
  `REDES_SOCIALES` text COLLATE utf8mb4_unicode_ci,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_ministerios`
--

INSERT INTO `vre_ministerios` (`ID`, `NOMBRE`, `TIPO`, `DESCRIPCION`, `OBJETIVO`, `REQUISITOS`, `ACTIVIDADES`, `HORARIO`, `DIA_REUNION`, `LUGAR`, `IMAGEN_URL`, `GALERIA`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_CONTACTO`, `EMAIL`, `TELEFONO`, `ID_DIRECTOR_USUARIO`, `REDES_SOCIALES`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(3, 'SORDOS MUDOS', 'OTRO', 'dsfdbvnb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, 'S', 0, '2025-11-18 14:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `vre_pulso_equipos`
--

DROP TABLE IF EXISTS `vre_pulso_equipos`;
CREATE TABLE IF NOT EXISTS `vre_pulso_equipos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CARGO` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ID_CARGO` int DEFAULT NULL,
  `ANIO` year NOT NULL,
  `PERIODO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FOTO_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FLICKR_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BIO` text COLLATE utf8mb4_unicode_ci,
  `ORDEN` int DEFAULT '0',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `fk_pulso_cargo` (`ID_CARGO`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_pulso_equipos`
--

INSERT INTO `vre_pulso_equipos` (`ID`, `NOMBRE`, `CARGO`, `ID_CARGO`, `ANIO`, `PERIODO`, `FOTO_URL`, `FLICKR_URL`, `BIO`, `ORDEN`, `ACTIVO`, `FECHA_CREACION`) VALUES
(1, 'JOSHUA BEJARANO', 'LIDER', NULL, '2025', 'ENERO-DICIEMBRE 2025', '', NULL, 'ES LIDER DEL DEPARTAMENTO DE PULSO', 1, 'S', '2025-10-10 00:00:43'),
(2, 'Andrew', 'FOTOGRAFO', NULL, '2025', 'ENERO-DICIEMBRE 2025', 'uploads/pulso/fotos/colaborador_68e9195baf773_1760106843.jpg', NULL, 'es fotografo en pulso', 2, 'S', '2025-10-10 08:34:03'),
(3, 'johan', 'jefe', NULL, '2025', 'ENERO-DICIEMBRE 2025', 'uploads/pulso/fotos/colaborador_68f90edc2aa6b_1761152732.png', NULL, 'jefe de pulso', 2, 'S', '2025-10-22 11:05:32');

-- --------------------------------------------------------

--
-- Table structure for table `vre_repositorio_fotos`
--

DROP TABLE IF EXISTS `vre_repositorio_fotos`;
CREATE TABLE IF NOT EXISTS `vre_repositorio_fotos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_ESTUDIANTE` int NOT NULL,
  `MATRICULA` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TITULO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `FOTO_URL` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIPO_FOTO` enum('INDIVIDUAL','GRUPAL','EVENTO','ACADEMICA','OTRA') COLLATE utf8mb4_unicode_ci DEFAULT 'INDIVIDUAL',
  `CICLO_ESCOLAR` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RELEASE_DATE` datetime DEFAULT NULL,
  `FECHA_FOTO` date DEFAULT NULL,
  `TAGS` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON con etiquetas de búsqueda',
  `ORDEN` int DEFAULT '0',
  `PRIVADA` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N' COMMENT 'Si es privada solo la ve el dueño',
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `FECHA_SUBIDA` datetime DEFAULT CURRENT_TIMESTAMP,
  `FLICKR_PAGE_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_ESTUDIANTE` (`ID_ESTUDIANTE`),
  KEY `idx_matricula` (`MATRICULA`),
  KEY `idx_tipo` (`TIPO_FOTO`),
  KEY `idx_fecha` (`FECHA_SUBIDA`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_repositorio_fotos`
--

INSERT INTO `vre_repositorio_fotos` (`ID`, `ID_ESTUDIANTE`, `MATRICULA`, `TITULO`, `DESCRIPCION`, `FOTO_URL`, `TIPO_FOTO`, `CICLO_ESCOLAR`, `RELEASE_DATE`, `FECHA_FOTO`, `TAGS`, `ORDEN`, `PRIVADA`, `ACTIVO`, `FECHA_SUBIDA`, `FLICKR_PAGE_URL`) VALUES
(1, 1, '1220593', 'foto de anuario', '', '/vidaEstudiantil/uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg', 'INDIVIDUAL', '2025-2026', '2025-10-22 08:04:48', '0000-00-00', NULL, 0, 'N', 'S', '2025-10-22 08:04:48', NULL),
(2, 7, '1220527', 'sdfghjk', 'dcfvghjkl', 'https://live.staticflickr.com/65535/53664304842_8a53de4206_h.jpg', 'INDIVIDUAL', '2025-2026', '2025-11-04 13:58:17', '2025-11-04', NULL, 0, 'N', 'S', '2025-11-04 13:58:17', NULL),
(3, 1, '1220593', '', '', 'https://live.staticflickr.com/65535/53665533174_47cf5628f1_h.jpg', 'INDIVIDUAL', '2025-2026', '2025-11-04 21:05:49', '2025-11-04', NULL, 0, 'S', 'S', '2025-11-04 21:05:49', NULL),
(4, 1, '1220593', '', '', 'https://live.staticflickr.com/65535/54472340488_4ec1ded083_h.jpg', 'INDIVIDUAL', '2025-2026', '2025-11-05 10:34:11', '2025-11-05', NULL, 0, 'S', 'S', '2025-11-05 10:34:11', 'https://www.flickr.com/gp/universidaddemontemorelos/pK61067z81'),
(5, 1, '1220593', '', '', 'https://live.staticflickr.com/65535/53671695255_cfca81da75_h.jpg', 'INDIVIDUAL', '2025-2026', '2025-11-05 10:38:12', '2023-06-05', NULL, 0, 'S', 'S', '2025-11-05 10:38:12', 'https://www.flickr.com/gp/universidaddemontemorelos/e24v5Vi63h');

-- --------------------------------------------------------

--
-- Table structure for table `vre_repositorio_referencias`
--

DROP TABLE IF EXISTS `vre_repositorio_referencias`;
CREATE TABLE IF NOT EXISTS `vre_repositorio_referencias` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_FOTO` int NOT NULL,
  `MATRICULA_REFERENCIADA` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NOMBRE_REFERENCIADO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `POSICION_X` decimal(5,2) DEFAULT NULL COMMENT 'Coordenada X del tag en la foto (%)',
  `POSICION_Y` decimal(5,2) DEFAULT NULL COMMENT 'Coordenada Y del tag en la foto (%)',
  `CONFIRMADO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'N' COMMENT 'Si el referenciado confirmó que es él',
  `FECHA_REFERENCIA` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `ID_FOTO` (`ID_FOTO`),
  KEY `idx_matricula_ref` (`MATRICULA_REFERENCIADA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vre_servicios_cocurriculares`
--

DROP TABLE IF EXISTS `vre_servicios_cocurriculares`;
CREATE TABLE IF NOT EXISTS `vre_servicios_cocurriculares` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SLUG` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CATEGORIA` enum('EMPRENDIMIENTO','MINISTERIO','VOLUNTARIADO','ACADEMICO','OTRO') COLLATE utf8mb4_unicode_ci DEFAULT 'OTRO',
  `DESCRIPCION` text COLLATE utf8mb4_unicode_ci,
  `OBJETIVO` text COLLATE utf8mb4_unicode_ci,
  `BENEFICIOS` text COLLATE utf8mb4_unicode_ci,
  `REQUISITOS` text COLLATE utf8mb4_unicode_ci,
  `COMO_PARTICIPAR` text COLLATE utf8mb4_unicode_ci,
  `HORARIO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LUGAR` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `EMAIL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TELEFONO` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SITIO_WEB` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `REDES_SOCIALES` text COLLATE utf8mb4_unicode_ci,
  `IMAGEN_URL` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GALERIA` text COLLATE utf8mb4_unicode_ci,
  `ACTIVO` enum('S','N') COLLATE utf8mb4_unicode_ci DEFAULT 'S',
  `ORDEN` int DEFAULT '0',
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SLUG` (`SLUG`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vre_servicios_cocurriculares`
--

INSERT INTO `vre_servicios_cocurriculares` (`ID`, `NOMBRE`, `SLUG`, `CATEGORIA`, `DESCRIPCION`, `OBJETIVO`, `BENEFICIOS`, `REQUISITOS`, `COMO_PARTICIPAR`, `HORARIO`, `LUGAR`, `RESPONSABLE_NOMBRE`, `RESPONSABLE_CONTACTO`, `EMAIL`, `TELEFONO`, `SITIO_WEB`, `REDES_SOCIALES`, `IMAGEN_URL`, `GALERIA`, `ACTIVO`, `ORDEN`, `FECHA_CREACION`) VALUES
(1, 'Universidad del Mañana', 'universidad-manana', 'ACADEMICO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 1, '2025-10-03 07:20:45'),
(2, 'Ministerio Juvenil', 'ministerio-juvenil', 'MINISTERIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 2, '2025-10-03 07:20:45'),
(3, 'Ministerios Musicales', 'ministerios-musicales', 'MINISTERIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 3, '2025-10-03 07:20:45'),
(4, 'Ministerios Instrumentales', 'ministerios-instrumentales', 'MINISTERIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 4, '2025-10-03 07:20:45'),
(5, 'Emprendum', 'emprendum', 'EMPRENDIMIENTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 5, '2025-10-03 07:20:45'),
(6, 'Enactus', 'enactus', 'EMPRENDIMIENTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 6, '2025-10-03 07:20:45'),
(7, 'Mentes en Misión', 'mentes-en-mision', 'VOLUNTARIADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 7, '2025-10-03 07:20:45'),
(8, 'Becarios', 'becarios', 'ACADEMICO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'S', 8, '2025-10-03 07:20:45');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `system_auditoria`
--
ALTER TABLE `system_auditoria`
  ADD CONSTRAINT `system_auditoria_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `system_usuarios` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `system_rol_modulo_permisos`
--
ALTER TABLE `system_rol_modulo_permisos`
  ADD CONSTRAINT `system_rol_modulo_permisos_ibfk_1` FOREIGN KEY (`ID_ROL`) REFERENCES `system_cat_usuarios` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `system_rol_modulo_permisos_ibfk_2` FOREIGN KEY (`ID_MODULO`) REFERENCES `system_modulos` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `system_rol_modulo_permisos_ibfk_3` FOREIGN KEY (`ID_PERMISO`) REFERENCES `system_permisos` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `system_usuarios`
--
ALTER TABLE `system_usuarios`
  ADD CONSTRAINT `system_usuarios_ibfk_1` FOREIGN KEY (`ID_CAT`) REFERENCES `system_cat_usuarios` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `vre_anuarios`
--
ALTER TABLE `vre_anuarios`
  ADD CONSTRAINT `vre_anuarios_ibfk_1` FOREIGN KEY (`ID_USUARIO_CREADOR`) REFERENCES `system_usuarios` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `vre_anuarios_fotos_estudiantes`
--
ALTER TABLE `vre_anuarios_fotos_estudiantes`
  ADD CONSTRAINT `vre_anuarios_fotos_estudiantes_ibfk_1` FOREIGN KEY (`ID_ANUARIO`) REFERENCES `vre_anuarios` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_anuarios_likes`
--
ALTER TABLE `vre_anuarios_likes`
  ADD CONSTRAINT `vre_anuarios_likes_ibfk_1` FOREIGN KEY (`ID_ANUARIO`) REFERENCES `vre_anuarios` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_deportes_actividades`
--
ALTER TABLE `vre_deportes_actividades`
  ADD CONSTRAINT `vre_deportes_actividades_ibfk_1` FOREIGN KEY (`ID_DEPORTE`) REFERENCES `vre_deportes` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_directiva_clubes`
--
ALTER TABLE `vre_directiva_clubes`
  ADD CONSTRAINT `vre_directiva_clubes_ibfk_1` FOREIGN KEY (`ID_CLUB`) REFERENCES `vre_clubes` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_directiva_ministerios`
--
ALTER TABLE `vre_directiva_ministerios`
  ADD CONSTRAINT `vre_directiva_ministerios_ibfk_1` FOREIGN KEY (`ID_MINISTERIO`) REFERENCES `vre_ministerios` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_eventos`
--
ALTER TABLE `vre_eventos`
  ADD CONSTRAINT `vre_eventos_ibfk_1` FOREIGN KEY (`ID_USUARIO_CREADOR`) REFERENCES `system_usuarios` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `vre_eventos_multimedia`
--
ALTER TABLE `vre_eventos_multimedia`
  ADD CONSTRAINT `vre_eventos_multimedia_ibfk_1` FOREIGN KEY (`ID_EVENTO`) REFERENCES `vre_eventos` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_federacion_miembros`
--
ALTER TABLE `vre_federacion_miembros`
  ADD CONSTRAINT `fk_federacion_cargo` FOREIGN KEY (`ID_CARGO`) REFERENCES `vre_cargos` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `vre_galeria`
--
ALTER TABLE `vre_galeria`
  ADD CONSTRAINT `vre_galeria_ibfk_1` FOREIGN KEY (`SUBIDO_POR`) REFERENCES `system_usuarios` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `vre_ligas`
--
ALTER TABLE `vre_ligas`
  ADD CONSTRAINT `vre_ligas_ibfk_1` FOREIGN KEY (`ID_DEPORTE`) REFERENCES `vre_deportes` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_pulso_equipos`
--
ALTER TABLE `vre_pulso_equipos`
  ADD CONSTRAINT `fk_pulso_cargo` FOREIGN KEY (`ID_CARGO`) REFERENCES `vre_cargos` (`ID`) ON DELETE SET NULL;

--
-- Constraints for table `vre_repositorio_fotos`
--
ALTER TABLE `vre_repositorio_fotos`
  ADD CONSTRAINT `vre_repositorio_fotos_ibfk_1` FOREIGN KEY (`ID_ESTUDIANTE`) REFERENCES `vre_estudiantes` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `vre_repositorio_referencias`
--
ALTER TABLE `vre_repositorio_referencias`
  ADD CONSTRAINT `vre_repositorio_referencias_ibfk_1` FOREIGN KEY (`ID_FOTO`) REFERENCES `vre_repositorio_fotos` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
