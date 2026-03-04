<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Obtener una imagen específica por ID
 * Requiere: id (GET)
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
if (!$temp->tiene_permiso('galeria', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver la galería';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Validar ID
    if (empty($_GET['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID de la imagen';
        echo json_encode($info);
        exit();
    }

    $id = intval($_GET['id']);

    // Obtener imagen usando la vista para tener toda la información
    $cad = "SELECT * FROM VRE_GALERIA_INFO WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql && $sql->num_rows > 0) {
        $data = $sql->fetch_assoc();

        $info['success'] = 1;
        $info['message'] = 'Imagen obtenida correctamente';
        $info['data'] = $data;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Imagen no encontrada';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);

