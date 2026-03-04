<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Subir nueva imagen a la galería centralizada
 * Requiere: modulo, id_registro, url_imagen
 * Opcional: titulo, descripcion, tipo, orden
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
if (!$temp->tiene_permiso('galeria', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para subir imágenes';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar campos requeridos
    if (empty($_POST['modulo']) || empty($_POST['id_registro']) || empty($_POST['url_imagen'])) {
        $info['success'] = 0;
        $info['message'] = 'Faltan campos requeridos: modulo, id_registro, url_imagen';
        echo json_encode($info);
        exit();
    }

    $modulo = $db->real_escape_string($_POST['modulo']);
    $id_registro = intval($_POST['id_registro']);
    $url_imagen = $db->real_escape_string($_POST['url_imagen']);

    // Validar que el módulo sea válido
    $modulos_validos = ['clubes', 'ministerios', 'deportes', 'ligas', 'instalaciones', 'cocurriculares', 'eventos', 'banners'];
    if (!in_array($modulo, $modulos_validos)) {
        $info['success'] = 0;
        $info['message'] = 'Módulo inválido. Use: ' . implode(', ', $modulos_validos);
        echo json_encode($info);
        exit();
    }

    // Verificar que el registro existe en su tabla correspondiente
    $tabla_modulo = '';
    switch($modulo) {
        case 'clubes':
            $tabla_modulo = 'VRE_CLUBES';
            break;
        case 'ministerios':
            $tabla_modulo = 'VRE_MINISTERIOS';
            break;
        case 'deportes':
            $tabla_modulo = 'VRE_DEPORTES';
            break;
        case 'ligas':
            $tabla_modulo = 'VRE_LIGAS';
            break;
        case 'instalaciones':
            $tabla_modulo = 'VRE_INSTALACIONES_DEPORTIVAS';
            break;
        case 'cocurriculares':
            $tabla_modulo = 'VRE_COCURRICULARES';
            break;
        case 'eventos':
            $tabla_modulo = 'VRE_EVENTOS';
            break;
        case 'banners':
            $tabla_modulo = 'VRE_BANNERS';
            break;
    }

    $check = $db->query("SELECT ID FROM $tabla_modulo WHERE ID = $id_registro");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = "No existe el registro con ID $id_registro en $modulo";
        echo json_encode($info);
        exit();
    }

    // Campos opcionales
    $titulo = isset($_POST['titulo']) ? $db->real_escape_string($_POST['titulo']) : null;
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $tipo = isset($_POST['tipo']) ? $db->real_escape_string($_POST['tipo']) : 'galeria';
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;
    $activo = isset($_POST['activo']) ? $db->real_escape_string($_POST['activo']) : 'S';

    // Validar tipo
    $tipos_validos = ['principal', 'galeria', 'banner', 'responsable'];
    if (!in_array($tipo, $tipos_validos)) {
        $tipo = 'galeria';
    }

    // Si no se especifica orden, calcular el siguiente disponible
    if ($orden == 0) {
        $max_orden = $db->query("SELECT MAX(ORDEN) as max_orden FROM VRE_GALERIA WHERE MODULO = '$modulo' AND ID_REGISTRO = $id_registro AND TIPO = '$tipo'");
        if ($max_orden) {
            $row = $max_orden->fetch_assoc();
            $orden = ($row['max_orden'] ?? 0) + 1;
        }
    }

    // Insertar en la base de datos
    $cad = "INSERT INTO VRE_GALERIA
            (MODULO, ID_REGISTRO, TITULO, DESCRIPCION, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
            VALUES
            ('$modulo', $id_registro, " .
            ($titulo ? "'$titulo'" : "NULL") . ", " .
            ($descripcion ? "'$descripcion'" : "NULL") . ", " .
            "'$url_imagen', '$tipo', $orden, '$activo', {$temp->usuario_id})";

    $sql = $db->query($cad);

    if ($sql) {
        $id_insertado = $db->insert_id;

        // Registrar en auditoría
        $temp->registrar_auditoria(
            'GALERIA',
            'SUBIR',
            "Subió imagen a $modulo (ID: $id_registro) - Tipo: $tipo"
        );

        $info['success'] = 1;
        $info['message'] = 'Imagen subida correctamente';
        $info['id'] = $id_insertado;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al subir imagen: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);

