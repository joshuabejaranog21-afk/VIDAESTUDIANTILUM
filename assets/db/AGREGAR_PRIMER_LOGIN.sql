-- ============================================
-- AGREGAR CAMPO PRIMER_LOGIN A SYSTEM_USUARIOS
-- ============================================
-- Este campo se usa para forzar cambio de contraseña en el primer login

USE pruebasumadmin;

-- Agregar columna PRIMER_LOGIN
ALTER TABLE SYSTEM_USUARIOS
ADD COLUMN PRIMER_LOGIN ENUM('S','N') DEFAULT 'S' AFTER ACTIVO;

-- Marcar como NO para usuarios existentes (ya tienen contraseña establecida)
UPDATE SYSTEM_USUARIOS SET PRIMER_LOGIN = 'N' WHERE ID IN (1, 2);

-- Los nuevos usuarios se crearán con PRIMER_LOGIN = 'S' por defecto
