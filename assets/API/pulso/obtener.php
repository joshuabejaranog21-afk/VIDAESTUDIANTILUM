<?php
session_start();
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit();
}

$cad = "SELECT * FROM vre_pulso_equipos WHERE ID = $id";
$sql = $db->query($cad);

if ($db->rows($sql) > 0) {
    $row = $db->recorrer($sql);
    echo json_encode([
        'success' => true,
        'miembro' => [
            'ID' => $row['ID'],
            'NOMBRE' => $row['NOMBRE'],
            'CARGO' => $row['CARGO'],
            'ID_CARGO' => $row['ID_CARGO'],
            'ANIO' => $row['ANIO'],
            'PERIODO' => $row['PERIODO'] ?? '',
            'FOTO_URL' => $row['FOTO_URL'] ?? '',
            'FLICKR_URL' => $row['FLICKR_URL'] ?? '',
            'BIO' => $row['BIO'] ?? '',
            'ORDEN' => $row['ORDEN'],
            'ACTIVO' => $row['ACTIVO']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No encontrado']);
}
?>
