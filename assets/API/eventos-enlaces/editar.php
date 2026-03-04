<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('eventos', 'editar')) {
    $info['success'] = 0;
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    $updates = [];

    if (isset($_POST['titulo'])) $updates[] = "TITULO = '" . $db->real_escape_string($_POST['titulo']) . "'";
    if (isset($_POST['url'])) $updates[] = "URL = '" . $db->real_escape_string($_POST['url']) . "'";
    if (isset($_POST['url_imagen_preview'])) {
        $val = $_POST['url_imagen_preview'] ? "'" . $db->real_escape_string($_POST['url_imagen_preview']) . "'" : "NULL";
        $updates[] = "URL_IMAGEN_PREVIEW = $val";
    }
    if (isset($_POST['tipo'])) $updates[] = "TIPO = '" . $db->real_escape_string($_POST['tipo']) . "'";
    if (isset($_POST['descripcion'])) {
        $val = $_POST['descripcion'] ? "'" . $db->real_escape_string($_POST['descripcion']) . "'" : "NULL";
        $updates[] = "DESCRIPCION = $val";
    }
    if (isset($_POST['orden'])) $updates[] = "ORDEN = " . intval($_POST['orden']);
    if (isset($_POST['activo'])) $updates[] = "ACTIVO = '" . $db->real_escape_string($_POST['activo']) . "'";

    if (!empty($updates)) {
        $cad = "UPDATE VRE_EVENTOS_ENLACES SET " . implode(', ', $updates) . " WHERE ID = $id";
        if ($db->query($cad)) {
            $info['success'] = 1;
            $info['message'] = 'Actualizado';
        } else {
            $info['success'] = 0;
            $info['message'] = $db->error;
        }
    }
}

echo json_encode($info);
exit;

