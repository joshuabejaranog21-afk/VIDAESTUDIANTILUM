<?php
include("../db.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = new Conexion();

// Get general information
$cadInfo = "SELECT * FROM VRE_FEDERACION_INFO LIMIT 1";
$sqlInfo = $db->query($cadInfo);

$info = null;
if ($db->rows($sqlInfo) > 0) {
    $row = $db->recorrer($sqlInfo);
    $info = [
        'titulo' => $row['TITULO'],
        'contenido_que_es' => $row['CONTENIDO_QUE_ES'],
        'contenido_eleccion' => $row['CONTENIDO_ELECCION'],
        'contenido_actividades' => $row['CONTENIDO_ACTIVIDADES'],
        'contenido_para_que_sirve' => $row['CONTENIDO_PARA_QUE_SIRVE'],
        'video_url' => $row['VIDEO_URL'],
        'imagen_principal' => $row['IMAGEN_PRINCIPAL']
    ];
}

// Get current year's active members
$anioActual = date('Y');
$cadMiembros = "SELECT * FROM VRE_FEDERACION_MIEMBROS
                WHERE ANIO = $anioActual AND ACTIVO = 'S'
                ORDER BY ORDEN ASC, ID ASC";
$sqlMiembros = $db->query($cadMiembros);

$miembros = [];
while ($row = $db->recorrer($sqlMiembros)) {
    $miembros[] = [
        'nombre' => $row['NOMBRE'],
        'puesto' => $row['PUESTO'],
        'carrera' => $row['CARRERA'],
        'foto_url' => $row['FOTO_URL'],
        'bio' => $row['BIO'],
        'email' => $row['EMAIL']
    ];
}

echo json_encode([
    'success' => 1,
    'info' => $info,
    'miembros' => $miembros,
    'anio' => $anioActual
]);
?>
