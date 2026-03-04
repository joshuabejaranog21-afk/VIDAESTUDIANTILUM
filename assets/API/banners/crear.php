<?php
error_reporting(0);
ini_set('display_errors', 0);

try {
    include("../../php/template.php");
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => 0, 'message' => 'Error al cargar template: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

try {
    $temp = new Template();
    $db = new Conexion();
} catch (Exception $e) {
    echo json_encode(['success' => 0, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$info = [];

if (!$temp->validate_session() || !$temp->tiene_permiso('banners', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'Sin permisos';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['titulo']) || empty($_POST['url_imagen'])) {
        $info['success'] = 0;
        $info['message'] = 'Faltan campos requeridos';
        echo json_encode($info);
        exit();
    }

    $titulo = $db->real_escape_string($_POST['titulo']);
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $imagen_url = $db->real_escape_string($_POST['url_imagen']);
    $enlace = isset($_POST['url_enlace']) ? $db->real_escape_string($_POST['url_enlace']) : null;
    $tipo = isset($_POST['tipo']) ? $db->real_escape_string($_POST['tipo']) : 'INFORMATIVO';
    $fecha_inicio = isset($_POST['fecha_inicio']) ? $db->real_escape_string($_POST['fecha_inicio']) : null;
    $fecha_fin = isset($_POST['fecha_fin']) ? $db->real_escape_string($_POST['fecha_fin']) : null;
    $ubicacion = isset($_POST['posicion']) ? $db->real_escape_string($_POST['posicion']) : 'HOME';
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;
    $activo = isset($_POST['activo']) ? $db->real_escape_string($_POST['activo']) : 'S';

    if ($orden == 0) {
        $max = $db->query("SELECT MAX(ORDEN) as max FROM VRE_BANNERS");
        if ($max) {
            $row = $max->fetch_assoc();
            $orden = ($row['max'] ?? 0) + 1;
        }
    }

    try {
        // Iniciar transacción
        $db->begin_transaction();

        // Insertar banner
        $cad = "INSERT INTO VRE_BANNERS (TITULO, DESCRIPCION, IMAGEN_URL, ENLACE, TIPO, FECHA_INICIO, FECHA_FIN, UBICACION, ORDEN, ACTIVO)
                VALUES ('$titulo', " .
                ($descripcion ? "'$descripcion'" : "NULL") . ", " .
                "'$imagen_url', " .
                ($enlace ? "'$enlace'" : "NULL") . ", " .
                "'$tipo', " .
                ($fecha_inicio ? "'$fecha_inicio'" : "NULL") . ", " .
                ($fecha_fin ? "'$fecha_fin'" : "NULL") . ", " .
                "'$ubicacion', $orden, '$activo')";

        if ($db->query($cad)) {
            $banner_id = $db->insert_id;

            // También registrar la imagen en la galería centralizada
            $cad_galeria = "INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, DESCRIPCION, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                           VALUES ('banners', $banner_id, '$titulo', " .
                           ($descripcion ? "'$descripcion'" : "NULL") . ", " .
                           "'$imagen_url', 'principal', 1, '$activo', {$temp->usuario_id})";

            if ($db->query($cad_galeria)) {
                $db->commit();
                $temp->registrar_auditoria('BANNERS', 'CREAR', "Banner creado: $titulo");
                $info['success'] = 1;
                $info['message'] = 'Banner creado correctamente';
                $info['id'] = $banner_id;
            } else {
                $db->rollback();
                $info['success'] = 0;
                $info['message'] = 'Error al registrar en galería: ' . $db->error;
            }
        } else {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = 'Error SQL: ' . $db->error;
        }
    } catch (Exception $e) {
        $db->rollback();
        $info['success'] = 0;
        $info['message'] = 'Excepción: ' . $e->getMessage();
    }
}

echo json_encode($info);
exit;