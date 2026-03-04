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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = $db->query("SELECT * FROM VRE_BANNERS WHERE ID = $id");

    if ($sql && $sql->num_rows > 0) {
        $info['success'] = 1;
        $info['data'] = $sql->fetch_assoc();
    } else {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
    }
}

echo json_encode($info);
exit;