<?php
include("../../php/template.php");

header('Content-Type: application/json');
$temp = new Template();
$db = new Conexion();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('ministerios', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar ministerios';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'ID del ministerio no proporcionado';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    try {
        // Obtener nombre para auditoría
        $result = $db->query("SELECT NOMBRE FROM VRE_MINISTERIOS WHERE ID = $id");
        $row = $result->fetch_assoc();
        $nombre = $row ? $row['NOMBRE'] : 'Desconocido';

        // Iniciar transacción
        $db->begin_transaction();

        // 1. Eliminar imágenes de VRE_GALERIA
        $db->query("DELETE FROM VRE_GALERIA WHERE MODULO = 'ministerios' AND ID_REGISTRO = $id");

        // 2. Eliminar ministerio (cascada elimina también la directiva)
        $sql = $db->query("DELETE FROM VRE_MINISTERIOS WHERE ID = $id");

        if ($sql) {
            $db->commit();

            // Registrar en auditoría
            $temp->registrar_auditoria('MINISTERIOS', 'ELIMINAR', "Ministerio eliminado: $nombre (ID: $id)");

            $info['success'] = 1;
            $info['message'] = 'Ministerio eliminado exitosamente';
        } else {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = 'Error al eliminar el ministerio';
            $info['error'] = $db->error;
        }

    } catch (Exception $e) {
        $db->rollback();
        $info['success'] = 0;
        $info['message'] = 'Error al eliminar el ministerio';
        $info['error'] = $e->getMessage();
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
