<?php
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();

$cad = "SELECT * FROM VRE_FEDERACION_INFO LIMIT 1";
$sql = $db->query($cad);

if ($db->rows($sql) == 0) {
    // Create default record if doesn't exist
    $db->query("INSERT INTO VRE_FEDERACION_INFO(TITULO) VALUES ('Federación Estudiantil UM')");
    $sql = $db->query($cad);
}

$row = $db->recorrer($sql);

$info = [
    'id' => $row['ID'],
    'titulo' => $row['TITULO'],
    'contenido_que_es' => $row['CONTENIDO_QUE_ES'],
    'contenido_eleccion' => $row['CONTENIDO_ELECCION'],
    'contenido_actividades' => $row['CONTENIDO_ACTIVIDADES'],
    'contenido_para_que_sirve' => $row['CONTENIDO_PARA_QUE_SIRVE'],
    'video_url' => $row['VIDEO_URL'],
    'imagen_principal' => $row['IMAGEN_PRINCIPAL']
];

echo json_encode(['success' => 1, 'info' => $info]);
?>
