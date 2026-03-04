<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Eliminar evento
 * También elimina imágenes y enlaces asociados
 */

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
exit;
    exit();
}

if (!$temp->tiene_permiso('eventos', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar eventos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
exit;
        exit();
    }

    $id = intval($_POST['id']);

    $check = $db->query("SELECT * FROM VRE_EVENTOS WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
        echo json_encode($info);
exit;
        exit();
    }

    $evento = $check->fetch_assoc();

    $db->begin_transaction();

    try {
        // 1. Eliminar imágenes de galería
        $delete_imagenes = $db->query("
            DELETE FROM VRE_GALERIA
            WHERE MODULO = 'eventos'
            AND ID_REGISTRO = $id
        ");

        if (!$delete_imagenes) {
            throw new Exception('Error al eliminar imágenes');
        }

        // 2. Eliminar enlaces (se eliminan automáticamente por CASCADE)

        // 3. Eliminar el evento
        $delete_evento = $db->query("DELETE FROM VRE_EVENTOS WHERE ID = $id");

        if (!$delete_evento) {
            throw new Exception('Error al eliminar');
        }

        $db->commit();

        $temp->registrar_auditoria('EVENTOS', 'ELIMINAR', "Eliminado: {$evento['TITULO']} (ID: $id)");

        $info['success'] = 1;
        $info['message'] = "'{$evento['TITULO']}' eliminado correctamente";

    } catch (Exception $e) {
        $db->rollback();
        $info['success'] = 0;
        $info['message'] = 'Error al eliminar: ' . $e->getMessage();
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
exit;

