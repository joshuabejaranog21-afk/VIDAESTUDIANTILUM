<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Reordenar imágenes de la galería
 * Recibe un array de IDs con su nuevo orden
 * Requiere: imagenes (JSON array: [{id: 1, orden: 1}, {id: 2, orden: 2}, ...])
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
if (!$temp->tiene_permiso('galeria', 'editar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para reordenar imágenes';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que se envió el array de imágenes
    if (empty($_POST['imagenes'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el array de imágenes';
        echo json_encode($info);
        exit();
    }

    // Decodificar JSON
    $imagenes = json_decode($_POST['imagenes'], true);

    if (!is_array($imagenes) || empty($imagenes)) {
        $info['success'] = 0;
        $info['message'] = 'El formato del array de imágenes es inválido';
        echo json_encode($info);
        exit();
    }

    // Iniciar transacción
    $db->begin_transaction();

    try {
        $actualizados = 0;

        foreach ($imagenes as $imagen) {
            if (!isset($imagen['id']) || !isset($imagen['orden'])) {
                continue;
            }

            $id = intval($imagen['id']);
            $orden = intval($imagen['orden']);

            $cad = "UPDATE VRE_GALERIA SET ORDEN = $orden WHERE ID = $id";
            $sql = $db->query($cad);

            if ($sql) {
                $actualizados++;
            }
        }

        // Commit de la transacción
        $db->commit();

        // Registrar en auditoría
        $temp->registrar_auditoria('GALERIA', 'REORDENAR', "Reordenó $actualizados imágenes");

        $info['success'] = 1;
        $info['message'] = "Se reordenaron $actualizados imágenes correctamente";
        $info['actualizados'] = $actualizados;

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();

        $info['success'] = 0;
        $info['message'] = 'Error al reordenar imágenes: ' . $e->getMessage();
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);

