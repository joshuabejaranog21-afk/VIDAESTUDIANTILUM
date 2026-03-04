<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('banners', 'ver')) {
    $info['success'] = 0;
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $tipo = isset($_GET['tipo']) ? $db->real_escape_string($_GET['tipo']) : null;
    $ubicacion = isset($_GET['ubicacion']) ? $db->real_escape_string($_GET['ubicacion']) : null;
    $activo = isset($_GET['activo']) ? $db->real_escape_string($_GET['activo']) : 'todos';

    $cad = "SELECT * FROM VRE_BANNERS WHERE 1=1";
    if ($tipo) $cad .= " AND TIPO = '$tipo'";
    if ($ubicacion) $cad .= " AND UBICACION = '$ubicacion'";
    if ($activo !== 'todos') $cad .= " AND ACTIVO = '$activo'";
    $cad .= " ORDER BY ORDEN ASC, ID DESC";

    $sql = $db->query($cad);
    $data = [];
    if ($sql) {
        while ($row = $sql->fetch_assoc()) $data[] = $row;
        $info['success'] = 1;
        $info['data'] = $data;
        $info['total'] = count($data);
    }
}

echo json_encode($info);
exit;