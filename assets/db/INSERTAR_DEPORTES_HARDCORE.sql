-- ============================================
-- INSERTAR DEPORTES HARDCORE
-- Basketball, Voleibol, Futbol
-- ============================================

USE pruebasumadmin;

-- Limpiar tabla de deportes existente (opcional)
-- DELETE FROM VRE_DEPORTES;

-- Insertar deportes principales HARDCORE
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

-- Verificación
SELECT 'Deportes HARDCORE insertados correctamente' AS STATUS;
SELECT * FROM VRE_DEPORTES ORDER BY ORDEN;
