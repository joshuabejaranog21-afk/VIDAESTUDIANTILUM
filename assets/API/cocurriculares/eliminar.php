<?php
/**
 * API: Eliminar co-curricular
 * Requiere: id
 * NOTA: También elimina las imágenes asociadas de la galería
 */

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
if (!$temp->tiene_permiso('cocurriculares', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar co-curriculares';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Obtener información antes de eliminar
    $check = $db->query("SELECT * FROM VRE_COCURRICULARES WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
        echo json_encode($info);
        exit();
    }

    $cocurricular = $check->fetch_assoc();

    // Iniciar transacción
    $db->begin_transaction();

    try {
        // 1. Eliminar imágenes asociadas de la galería
        $delete_imagenes = $db->query("
            DELETE FROM VRE_GALERIA
            WHERE MODULO = 'cocurriculares'
            AND ID_REGISTRO = $id
        ");

        if (!$delete_imagenes) {
            throw new Exception('Error al eliminar imágenes de la galería');
        }

        // 2. Eliminar el co-curricular
        $delete_cocurricular = $db->query("DELETE FROM VRE_COCURRICULARES WHERE ID = $id");

        if (!$delete_cocurricular) {
            throw new Exception('Error al eliminar');
        }

        // Commit de la transacción
        $db->commit();

        // Registrar en auditoría
        $temp->registrar_auditoria(
            'COCURRICULARES',
            'ELIMINAR',
            "Eliminado: {$cocurricular['NOMBRE']} (ID: $id)"
        );

        $info['success'] = 1;
        $info['message'] = "'{$cocurricular['NOMBRE']}' eliminado correctamente";

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();

        $info['success'] = 0;
        $info['message'] = 'Error al eliminar: ' . $e->getMessage();
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
