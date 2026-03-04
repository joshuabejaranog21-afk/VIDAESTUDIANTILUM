<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('eventos', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'Sin permisos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id_evento']) || empty($_POST['titulo']) || empty($_POST['url'])) {
        $info['success'] = 0;
        $info['message'] = 'Faltan campos requeridos';
        echo json_encode($info);
exit;
        exit();
    }

    $id_evento = intval($_POST['id_evento']);
    $titulo = $db->real_escape_string($_POST['titulo']);
    $url = $db->real_escape_string($_POST['url']);
    $url_imagen_preview = isset($_POST['url_imagen_preview']) ? $db->real_escape_string($_POST['url_imagen_preview']) : null;
    $tipo = isset($_POST['tipo']) ? $db->real_escape_string($_POST['tipo']) : 'OTRO';
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;

    if ($orden == 0) {
        $max = $db->query("SELECT MAX(ORDEN) as max FROM VRE_EVENTOS_ENLACES WHERE ID_EVENTO = $id_evento");
        if ($max) {
            $row = $max->fetch_assoc();
            $orden = ($row['max'] ?? 0) + 1;
        }
    }

    $cad = "INSERT INTO VRE_EVENTOS_ENLACES (ID_EVENTO, TITULO, URL, URL_IMAGEN_PREVIEW, TIPO, DESCRIPCION, ORDEN)
            VALUES ($id_evento, '$titulo', '$url', " .
            ($url_imagen_preview ? "'$url_imagen_preview'" : "NULL") . ", " .
            "'$tipo', " .
            ($descripcion ? "'$descripcion'" : "NULL") . ", $orden)";

    if ($db->query($cad)) {
        $temp->registrar_auditoria('EVENTOS_ENLACES', 'CREAR', "Enlace creado para evento ID: $id_evento");
        $info['success'] = 1;
        $info['message'] = 'Enlace creado';
        $info['id'] = $db->insert_id;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error: ' . $db->error;
    }
} else {
    $info['success'] = 0;
}

echo json_encode($info);
exit;

