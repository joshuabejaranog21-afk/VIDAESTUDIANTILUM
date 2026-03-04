<?php
/**
 * Script para agregar columna FLICKR_PAGE_URL a la tabla
 * Ejecutar una sola vez desde el navegador
 */

include('../API/db.php');

$db = new Conexion();

echo "<h1>Setup: Agregar FLICKR_PAGE_URL</h1>";

// Verificar si la columna ya existe
$check = "SHOW COLUMNS FROM VRE_REPOSITORIO_FOTOS LIKE 'FLICKR_PAGE_URL'";
$result = $db->query($check);

if ($db->rows($result) > 0) {
    echo "<p style='color: orange;'>✓ La columna FLICKR_PAGE_URL ya existe en la tabla.</p>";
} else {
    // Agregar la columna
    $sql = "ALTER TABLE VRE_REPOSITORIO_FOTOS
            ADD COLUMN FLICKR_PAGE_URL VARCHAR(500) DEFAULT NULL
            COMMENT 'URL de la página de Flickr (ej: https://flic.kr/p/2pLPh8f)'";

    if ($db->query($sql)) {
        echo "<p style='color: green;'>✓ Columna FLICKR_PAGE_URL agregada exitosamente!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error al agregar columna: " . $db->error . "</p>";
    }
}

// Mostrar estadísticas
$stats = "SELECT
            COUNT(*) as total,
            SUM(CASE WHEN FLICKR_PAGE_URL IS NOT NULL AND FLICKR_PAGE_URL != '' THEN 1 ELSE 0 END) as con_url_pagina,
            SUM(CASE WHEN FLICKR_PAGE_URL IS NULL OR FLICKR_PAGE_URL = '' THEN 1 ELSE 0 END) as sin_url_pagina
          FROM VRE_REPOSITORIO_FOTOS";

$result = $db->query($stats);
if ($row = $db->recorrer($result)) {
    echo "<h2>Estadísticas:</h2>";
    echo "<ul>";
    echo "<li>Total de fotos: <strong>{$row['total']}</strong></li>";
    echo "<li>Con URL de página de Flickr: <strong>{$row['con_url_pagina']}</strong></li>";
    echo "<li>Sin URL de página de Flickr: <strong style='color: orange;'>{$row['sin_url_pagina']}</strong></li>";
    echo "</ul>";

    if ($row['sin_url_pagina'] > 0) {
        echo "<p style='color: #666;'><strong>Nota:</strong> Las fotos sin URL de página solo mostrarán la imagen directa cuando hagas clic en 'Ver en Flickr'.</p>";
        echo "<p style='color: #666;'>Para corregirlo, vuelve a subir las fotos con el BBCode completo de Flickr.</p>";
    }
}

echo "<hr>";
echo "<p><a href='../../pages/repositorio/'>← Volver al Repositorio</a></p>";
?>
