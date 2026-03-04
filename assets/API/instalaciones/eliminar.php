<?php
/**
 * API: Eliminar instalación deportiva
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
if (!$temp->tiene_permiso('instalaciones', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar instalaciones';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID de la instalación';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Obtener información de la instalación antes de eliminar
    $check = $db->query("SELECT * FROM VRE_INSTALACIONES_DEPORTIVAS WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'Instalación no encontrada';
        echo json_encode($info);
        exit();
    }

    $instalacion = $check->fetch_assoc();

    // Iniciar transacción
    $db->begin_transaction();

    try {
        // 1. Eliminar imágenes asociadas de la galería
        $delete_imagenes = $db->query("
            DELETE FROM VRE_GALERIA
            WHERE MODULO = 'instalaciones'
            AND ID_REGISTRO = $id
        ");

        if (!$delete_imagenes) {
            throw new Exception('Error al eliminar imágenes de la galería');
        }

        // 2. Eliminar la instalación
        $delete_instalacion = $db->query("DELETE FROM VRE_INSTALACIONES_DEPORTIVAS WHERE ID = $id");

        if (!$delete_instalacion) {
            throw new Exception('Error al eliminar la instalación');
        }

        // Commit de la transacción
        $db->commit();

        // Registrar en auditoría
        $temp->registrar_auditoria(
            'INSTALACIONES',
            'ELIMINAR',
            "Instalación eliminada: {$instalacion['NOMBRE']} (ID: $id)"
        );

        $info['success'] = 1;
        $info['message'] = "Instalación '{$instalacion['NOMBRE']}' eliminada correctamente";

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();

        $info['success'] = 0;
        $info['message'] = 'Error al eliminar instalación: ' . $e->getMessage();
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
