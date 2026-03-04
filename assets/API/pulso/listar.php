<?php
session_start();
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : 0;

$cad = "SELECT p.*, c.NOMBRE as CARGO_NOMBRE
        FROM vre_pulso_equipos p
        LEFT JOIN vre_cargos c ON p.ID_CARGO = c.ID";

if ($anio > 0) {
    $cad .= " WHERE p.ANIO = $anio";
}

$cad .= " ORDER BY p.ORDEN ASC, p.ID DESC";

$sql = $db->query($cad);

$miembros = [];
while ($row = $db->recorrer($sql)) {
    // Usar CARGO_NOMBRE si existe, sino usar CARGO
    $cargo = $row['CARGO_NOMBRE'] ?: $row['CARGO'];

    $miembros[] = [
        'ID' => $row['ID'],
        'NOMBRE' => $row['NOMBRE'],
        'CARGO' => $cargo,
        'ID_CARGO' => $row['ID_CARGO'],
        'ANIO' => $row['ANIO'],
        'PERIODO' => $row['PERIODO'] ?? '',
        'FOTO_URL' => $row['FOTO_URL'] ?? '',
        'FLICKR_URL' => $row['FLICKR_URL'] ?? '',
        'BIO' => $row['BIO'] ?? '',
        'ORDEN' => $row['ORDEN'],
        'ACTIVO' => $row['ACTIVO']
    ];
}

echo json_encode([
    'success' => true,
    'miembros' => $miembros
]);
?>
