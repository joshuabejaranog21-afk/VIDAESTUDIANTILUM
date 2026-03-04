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
$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit();
}

$sql = "SELECT * FROM vre_cargos WHERE ID = " . intval($id);
$result = $db->query($sql);

if ($db->rows($result) > 0) {
    $row = $db->recorrer($result);
    echo json_encode([
        'success' => true,
        'data' => [
            'ID' => $row['ID'],
            'NOMBRE' => $row['NOMBRE'],
            'DESCRIPCION' => $row['DESCRIPCION'] ?? '',
            'TIPO' => $row['TIPO'],
            'ORDEN' => $row['ORDEN'],
            'ACTIVO' => $row['ACTIVO']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cargo no encontrado']);
}
?>
