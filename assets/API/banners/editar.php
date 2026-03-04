<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");
header('Content-Type: application/json');

$temp = new Template();
$db = new Conexion();
$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('banners', 'editar')) {
    $info['success'] = 0;
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    $updates = [];
    $updates_galeria = [];

    if (isset($_POST['titulo'])) {
        $titulo = $db->real_escape_string($_POST['titulo']);
        $updates[] = "TITULO = '$titulo'";
        $updates_galeria[] = "TITULO = '$titulo'";
    }
    if (isset($_POST['descripcion'])) {
        $val = $_POST['descripcion'] ? "'" . $db->real_escape_string($_POST['descripcion']) . "'" : "NULL";
        $updates[] = "DESCRIPCION = $val";
        $updates_galeria[] = "DESCRIPCION = $val";
    }
    if (isset($_POST['url_imagen'])) {
        $imagen = $db->real_escape_string($_POST['url_imagen']);
        $updates[] = "IMAGEN_URL = '$imagen'";
        $updates_galeria[] = "URL_IMAGEN = '$imagen'";
    }
    if (isset($_POST['url_enlace'])) {
        $val = $_POST['url_enlace'] ? "'" . $db->real_escape_string($_POST['url_enlace']) . "'" : "NULL";
        $updates[] = "ENLACE = $val";
    }
    if (isset($_POST['tipo'])) $updates[] = "TIPO = '" . $db->real_escape_string($_POST['tipo']) . "'";
    if (isset($_POST['fecha_inicio'])) {
        $val = $_POST['fecha_inicio'] ? "'" . $db->real_escape_string($_POST['fecha_inicio']) . "'" : "NULL";
        $updates[] = "FECHA_INICIO = $val";
    }
    if (isset($_POST['fecha_fin'])) {
        $val = $_POST['fecha_fin'] ? "'" . $db->real_escape_string($_POST['fecha_fin']) . "'" : "NULL";
        $updates[] = "FECHA_FIN = $val";
    }
    if (isset($_POST['posicion'])) $updates[] = "UBICACION = '" . $db->real_escape_string($_POST['posicion']) . "'";
    if (isset($_POST['orden'])) $updates[] = "ORDEN = " . intval($_POST['orden']);
    if (isset($_POST['activo'])) {
        $activo = $db->real_escape_string($_POST['activo']);
        $updates[] = "ACTIVO = '$activo'";
        $updates_galeria[] = "ACTIVO = '$activo'";
    }

    if (!empty($updates)) {
        try {
            $db->begin_transaction();

            // Actualizar banner
            $cad = "UPDATE VRE_BANNERS SET " . implode(', ', $updates) . " WHERE ID = $id";
            if ($db->query($cad)) {
                // También actualizar en galería si hay cambios relevantes
                if (!empty($updates_galeria)) {
                    $cad_galeria = "UPDATE VRE_GALERIA SET " . implode(', ', $updates_galeria) . " WHERE MODULO = 'banners' AND ID_REGISTRO = $id AND TIPO = 'principal'";
                    $db->query($cad_galeria); // No fallar si no existe
                }

                $db->commit();
                $temp->registrar_auditoria('BANNERS', 'EDITAR', "Banner editado ID: $id");
                $info['success'] = 1;
                $info['message'] = 'Actualizado';
            } else {
                $db->rollback();
                $info['success'] = 0;
                $info['message'] = $db->error;
            }
        } catch (Exception $e) {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = 'Error: ' . $e->getMessage();
        }
    }
}

echo json_encode($info);
exit;