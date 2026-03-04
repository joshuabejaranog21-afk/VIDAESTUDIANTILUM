-- ============================================
-- SCRIPT DE REPARACIÓN DE ROLES
-- Este script corrige los roles en la tabla SYSTEM_CAT_USUARIOS
-- ============================================

USE pruebasumadmin;

-- Paso 1: Verificar los roles actuales
SELECT 'ROLES ACTUALES EN LA BASE DE DATOS:' AS INFO;
SELECT * FROM SYSTEM_CAT_USUARIOS;

-- Paso 2: Eliminar todos los roles existentes (CUIDADO: esto eliminará todos los roles)
-- Si hay usuarios asignados a roles, primero se deben actualizar o la FK los pondrá en NULL
DELETE FROM SYSTEM_CAT_USUARIOS;

-- Paso 3: Reiniciar el contador de AUTO_INCREMENT
ALTER TABLE SYSTEM_CAT_USUARIOS AUTO_INCREMENT = 1;

-- Paso 4: Insertar los roles correctamente
INSERT INTO SYSTEM_CAT_USUARIOS(ID, NOMBRE, DESCRIPCION) VALUES
(1, 'SUPERUSUARIO', 'Acceso total al sistema'),
(2, 'ADMINISTRADOR', 'Administrador con permisos amplios'),
(3, 'EDITOR_INVOLUCRATE', 'Editor de módulo Involúcrate (Clubes, Ministerios, Deportes)'),
(4, 'EDITOR_EVENTOS', 'Editor de eventos y multimedia'),
(5, 'VISUALIZADOR', 'Solo puede ver información, sin editar'),
(8, 'DIRECTOR_CLUB', 'Director de club estudiantil'),
(11, 'DIRECTOR_MINISTERIO', 'Director de ministerio');

-- Paso 5: Verificar que los roles se insertaron correctamente
SELECT 'ROLES DESPUÉS DE LA REPARACIÓN:' AS INFO;
SELECT * FROM SYSTEM_CAT_USUARIOS;

-- Paso 6: Reasignar roles a usuarios existentes (ajustar según sea necesario)
-- IMPORTANTE: Si tienes usuarios que perdieron su rol, debes reasignárselos aquí
UPDATE SYSTEM_USUARIOS SET ID_CAT = 1 WHERE NOMBRE = 'Suriel';
UPDATE SYSTEM_USUARIOS SET ID_CAT = 2 WHERE NOMBRE = 'admin';

-- Verificar usuarios
SELECT 'USUARIOS CON SUS ROLES:' AS INFO;
SELECT u.ID, u.NOMBRE, u.EMAIL, c.NOMBRE AS ROL
FROM SYSTEM_USUARIOS u
LEFT JOIN SYSTEM_CAT_USUARIOS c ON u.ID_CAT = c.ID;
