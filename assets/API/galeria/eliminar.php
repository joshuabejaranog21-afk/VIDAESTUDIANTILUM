<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Eliminar imagen de la galería
 * Requiere: id
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
if (!$temp->tiene_permiso('galeria', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar imágenes';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID de la imagen';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Obtener información de la imagen antes de eliminar (para auditoría)
    $check = $db->query("SELECT * FROM VRE_GALERIA WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'Imagen no encontrada';
        echo json_encode($info);
        exit();
    }

    $imagen = $check->fetch_assoc();

    // Eliminar de la base de datos
    $cad = "DELETE FROM VRE_GALERIA WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql) {
        // Registrar en auditoría
        $descripcion_auditoria = "Eliminó imagen ID: $id - Módulo: {$imagen['MODULO']} - Registro: {$imagen['ID_REGISTRO']}";
        $temp->registrar_auditoria('GALERIA', 'ELIMINAR', $descripcion_auditoria);

        $info['success'] = 1;
        $info['message'] = 'Imagen eliminada correctamente';
        $info['url_imagen'] = $imagen['URL_IMAGEN']; // Retornar URL por si se quiere eliminar físicamente
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al eliminar imagen: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);

