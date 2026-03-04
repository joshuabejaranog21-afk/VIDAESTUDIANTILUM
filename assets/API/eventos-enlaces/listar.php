<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('eventos', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'Sin permisos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_evento = isset($_GET['id_evento']) ? intval($_GET['id_evento']) : null;
    $tipo = isset($_GET['tipo']) ? $db->real_escape_string($_GET['tipo']) : null;

    $cad = "SELECT * FROM VRE_EVENTOS_ENLACES WHERE 1=1";
    if ($id_evento) $cad .= " AND ID_EVENTO = $id_evento";
    if ($tipo) $cad .= " AND TIPO = '$tipo'";
    $cad .= " AND ACTIVO = 'S' ORDER BY ORDEN ASC";

    $sql = $db->query($cad);
    $data = [];
    if ($sql) {
        while ($row = $sql->fetch_assoc()) $data[] = $row;
        $info['success'] = 1;
        $info['data'] = $data;
        $info['total'] = count($data);
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
exit;

