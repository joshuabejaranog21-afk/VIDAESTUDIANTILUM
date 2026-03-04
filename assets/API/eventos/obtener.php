<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Obtener un evento específico por ID
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

if (!$temp->tiene_permiso('eventos', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver eventos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (empty($_GET['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
exit;
        exit();
    }

    $id = intval($_GET['id']);

    $cad = "SELECT * FROM VRE_EVENTOS WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql && $sql->num_rows > 0) {
        $data = $sql->fetch_assoc();

        // Obtener imágenes de la galería
        $imagenes_query = $db->query("
            SELECT ID, URL_IMAGEN, TIPO, ORDEN, TITULO, DESCRIPCION
            FROM VRE_GALERIA
            WHERE MODULO = 'eventos'
            AND ID_REGISTRO = $id
            AND ACTIVO = 'S'
            ORDER BY TIPO, ORDEN ASC
        ");

        $imagenes = [];
        if ($imagenes_query) {
            while ($img = $imagenes_query->fetch_assoc()) {
                $imagenes[] = $img;
            }
        }

        // Obtener enlaces multimedia
        $enlaces_query = $db->query("
            SELECT *
            FROM VRE_EVENTOS_ENLACES
            WHERE ID_EVENTO = $id
            AND ACTIVO = 'S'
            ORDER BY ORDEN ASC
        ");

        $enlaces = [];
        if ($enlaces_query) {
            while ($enlace = $enlaces_query->fetch_assoc()) {
                $enlaces[] = $enlace;
            }
        }

        $data['IMAGENES'] = $imagenes;
        $data['TOTAL_IMAGENES'] = count($imagenes);
        $data['ENLACES'] = $enlaces;
        $data['TOTAL_ENLACES'] = count($enlaces);

        $info['success'] = 1;
        $info['message'] = 'Obtenido correctamente';
        $info['data'] = $data;
    } else {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);
exit;

