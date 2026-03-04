<?php
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();

$cad = "SELECT DISTINCT ANIO FROM VRE_FEDERACION_MIEMBROS ORDER BY ANIO DESC";
$sql = $db->query($cad);

$anios = [];
while ($row = $db->recorrer($sql)) {
    $anios[] = $row['ANIO'];
}

echo json_encode(['success' => 1, 'anios' => $anios]);
?>
