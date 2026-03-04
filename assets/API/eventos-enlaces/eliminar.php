<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('eventos', 'eliminar')) {
    $info['success'] = 0;
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id'])) {
    $id = intval($_POST['id']);

    if ($db->query("DELETE FROM VRE_EVENTOS_ENLACES WHERE ID = $id")) {
        $temp->registrar_auditoria('EVENTOS_ENLACES', 'ELIMINAR', "Enlace eliminado ID: $id");
        $info['success'] = 1;
        $info['message'] = 'Eliminado';
    } else {
        $info['success'] = 0;
        $info['message'] = $db->error;
    }
}

echo json_encode($info);
exit;

