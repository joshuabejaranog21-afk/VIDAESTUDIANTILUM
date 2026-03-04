<?php
session_start();
include("../../db.php");
header('Content-Type: application/json');

// Solo admins
if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

$sql = "SELECT * FROM vre_cargos ORDER BY ORDEN ASC, ID DESC";
$result = $db->query($sql);

$cargos = [];
while ($row = $db->recorrer($result)) {
    $cargos[] = [
        'ID' => $row['ID'],
        'NOMBRE' => $row['NOMBRE'],
        'DESCRIPCION' => $row['DESCRIPCION'] ?? '',
        'TIPO' => $row['TIPO'],
        'ORDEN' => $row['ORDEN'],
        'ACTIVO' => $row['ACTIVO']
    ];
}

echo json_encode([
    'success' => true,
    'data' => $cargos
]);
?>
