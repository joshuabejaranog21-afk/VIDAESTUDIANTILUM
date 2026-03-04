<?php
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();

if (!isset($_GET['id'])) {
    echo json_encode(['success' => 0, 'message' => 'ID requerido']);
    exit();
}

$id = intval($_GET['id']);

$cad = "SELECT * FROM VRE_FEDERACION_MIEMBROS WHERE ID = $id";
$sql = $db->query($cad);

if ($db->rows($sql) == 0) {
    echo json_encode(['success' => 0, 'message' => 'Miembro no encontrado']);
    exit();
}

$row = $db->recorrer($sql);

$miembro = [
    'id' => $row['ID'],
    'nombre' => $row['NOMBRE'],
    'puesto' => $row['PUESTO'],
    'anio' => $row['ANIO'],
    'matricula' => $row['MATRICULA'],
    'carrera' => $row['CARRERA'],
    'foto_url' => $row['FOTO_URL'],
    'bio' => $row['BIO'],
    'email' => $row['EMAIL'],
    'telefono' => $row['TELEFONO'],
    'orden' => $row['ORDEN'],
    'activo' => $row['ACTIVO']
];

echo json_encode(['success' => 1, 'miembro' => $miembro]);
?>
