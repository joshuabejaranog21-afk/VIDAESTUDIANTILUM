<?php
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$tablas = ['VRE_CLUBES', 'VRE_MINISTERIOS', 'VRE_DEPORTES', 'VRE_EVENTOS', 'VRE_INSTALACIONES', 'VRE_ANUARIOS'];

echo "<style>
body { font-family: monospace; padding: 20px; }
table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background: #667eea; color: white; }
.si { color: green; font-weight: bold; }
.no { color: red; font-weight: bold; }
</style>";

foreach ($tablas as $tabla) {
    $sql = $db->query("SHOW TABLES LIKE '$tabla'");
    $existe = $db->rows($sql) > 0;

    echo "<h2>$tabla - " . ($existe ? "<span class='si'>EXISTE ✓</span>" : "<span class='no'>NO EXISTE ✗</span>") . "</h2>";

    if ($existe) {
        echo "<table>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Default</th></tr>";
        $columns = $db->query("SHOW COLUMNS FROM $tabla");
        while ($col = $db->recorrer($columns)) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong></td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>⚠️ Esta tabla necesita ser creada</p><hr>";
    }
}
?>
