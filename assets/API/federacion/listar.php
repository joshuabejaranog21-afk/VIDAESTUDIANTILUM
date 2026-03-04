<?php
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();
$info = [];

$anio = isset($_GET['anio']) ? $db->real_escape_string($_GET['anio']) : '';

$cad = "SELECT * FROM VRE_FEDERACION_MIEMBROS WHERE 1=1";

if ($anio) {
    $cad .= " AND ANIO = '$anio'";
}

$cad .= " ORDER BY ANIO DESC, ORDEN ASC, ID DESC";

$sql = $db->query($cad);

$miembros = [];
while ($row = $db->recorrer($sql)) {
    $miembros[] = [
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
}

$info['success'] = 1;
$info['miembros'] = $miembros;
$info['total'] = count($miembros);

echo json_encode($info);
?>
