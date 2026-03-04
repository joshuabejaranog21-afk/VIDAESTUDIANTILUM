-- ============================================
-- SCRIPT PARA VERIFICAR DATOS ANTIGUOS
-- Este script verifica si hay datos de anuarios en otras tablas o bases de datos
-- ============================================

USE pruebasumadmin;

-- Verificar si la tabla VRE_ANUARIOS tiene datos
SELECT 'VERIFICANDO TABLA VRE_ANUARIOS:' AS INFO;
SELECT COUNT(*) AS TOTAL_ANUARIOS FROM VRE_ANUARIOS;
SELECT * FROM VRE_ANUARIOS LIMIT 10;

-- Verificar si hay otras bases de datos con tablas similares
SELECT 'BASES DE DATOS DISPONIBLES:' AS INFO;
SHOW DATABASES;

-- Verificar todas las tablas en la base de datos actual
SELECT 'TABLAS EN pruebasumadmin:' AS INFO;
SHOW TABLES;

-- Buscar tablas que contengan "ANUARIO" en su nombre
SELECT 'BUSCANDO TABLAS CON "ANUARIO":' AS INFO;
SELECT TABLE_NAME, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'pruebasumadmin'
AND TABLE_NAME LIKE '%ANUARIO%';

-- Verificar tabla VRE_PULSO_EQUIPOS
SELECT 'VERIFICANDO TABLA VRE_PULSO_EQUIPOS:' AS INFO;
SELECT COUNT(*) AS TOTAL_MIEMBROS_PULSO FROM VRE_PULSO_EQUIPOS;
SELECT * FROM VRE_PULSO_EQUIPOS LIMIT 10;

-- Verificar tabla VRE_FEDERACION_MIEMBROS
SELECT 'VERIFICANDO TABLA VRE_FEDERACION_MIEMBROS:' AS INFO;
SELECT COUNT(*) AS TOTAL_MIEMBROS_FEDERACION FROM VRE_FEDERACION_MIEMBROS;
SELECT * FROM VRE_FEDERACION_MIEMBROS LIMIT 10;

-- Si encontramos una base de datos diferente, verificar sus tablas
-- NOTA: Cambia 'nombre_bd_antigua' por el nombre real si existe
-- USE nombre_bd_antigua;
-- SELECT * FROM VRE_ANUARIOS LIMIT 10;
