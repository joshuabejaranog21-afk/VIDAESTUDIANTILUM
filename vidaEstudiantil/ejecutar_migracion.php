<?php
include('../cpanel/assets/API/db.php');
$db = new Conexion();

echo "<style>
body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
.success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #28a745; }
.error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #dc3545; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #17a2b8; }
pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>";

echo "<h1>Ejecutando Migración: VRE_INSTALACIONES</h1>";

// Leer el archivo SQL
$sqlFile = '../cpanel/assets/db/CREAR_VRE_INSTALACIONES.sql';
$sql = file_get_contents($sqlFile);

if (!$sql) {
    echo "<div class='error'>❌ No se pudo leer el archivo SQL</div>";
    exit;
}

echo "<div class='info'>📄 Archivo SQL cargado correctamente</div>";

// Dividir en statements individuales
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($stmt) {
        return !empty($stmt) && !preg_match('/^--/', $stmt);
    }
);

echo "<div class='info'>🔍 Se encontraron " . count($statements) . " comandos SQL</div>";

$success = 0;
$errors = 0;

foreach ($statements as $index => $statement) {
    if (empty(trim($statement))) continue;

    echo "<div class='info'><strong>Ejecutando comando " . ($index + 1) . ":</strong><br>";
    echo "<pre>" . htmlspecialchars(substr($statement, 0, 200)) . (strlen($statement) > 200 ? '...' : '') . "</pre></div>";

    try {
        $result = $db->query($statement);
        if ($result) {
            echo "<div class='success'>✅ Comando ejecutado correctamente</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error: " . $db->error . "</div>";
            $errors++;
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Excepción: " . $e->getMessage() . "</div>";
        $errors++;
    }
}

echo "<hr>";
echo "<h2>Resumen:</h2>";
echo "<div class='success'>✅ Comandos exitosos: $success</div>";
if ($errors > 0) {
    echo "<div class='error'>❌ Comandos con error: $errors</div>";
}

echo "<div class='info'><a href='instalaciones.php' style='color: #0c5460; font-weight: bold;'>→ Ir a ver Instalaciones</a></div>";
?>
