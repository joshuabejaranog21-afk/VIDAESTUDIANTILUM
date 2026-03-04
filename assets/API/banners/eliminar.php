<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('banners', 'eliminar')) {
    $info['success'] = 0;
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id'])) {
    $id = intval($_POST['id']);

    $check = $db->query("SELECT * FROM VRE_BANNERS WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
        echo json_encode($info);
        exit();
    }

    $banner = $check->fetch_assoc();

    try {
        $db->begin_transaction();

        // Eliminar de la galería primero
        $db->query("DELETE FROM VRE_GALERIA WHERE MODULO = 'banners' AND ID_REGISTRO = $id");

        // Eliminar el banner
        if ($db->query("DELETE FROM VRE_BANNERS WHERE ID = $id")) {
            $db->commit();
            $temp->registrar_auditoria('BANNERS', 'ELIMINAR', "Banner eliminado: {$banner['TITULO']}");
            $info['success'] = 1;
            $info['message'] = "'{$banner['TITULO']}' eliminado";
        } else {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = $db->error;
        }
    } catch (Exception $e) {
        $db->rollback();
        $info['success'] = 0;
        $info['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($info);
exit;