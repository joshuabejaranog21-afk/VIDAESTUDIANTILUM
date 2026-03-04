<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Editar imagen existente en la galería
 * Requiere: id
 * Opcional: titulo, descripcion, tipo, orden, activo
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
    $info['message'] = 'No tienes permiso para editar imágenes';
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

    // Verificar que la imagen existe
    $check = $db->query("SELECT * FROM VRE_GALERIA WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'Imagen no encontrada';
        echo json_encode($info);
        exit();
    }

    // Construir UPDATE dinámico solo con campos enviados
    $updates = [];

    if (isset($_POST['titulo'])) {
        $titulo = $db->real_escape_string($_POST['titulo']);
        $updates[] = "TITULO = " . ($titulo ? "'$titulo'" : "NULL");
    }

    if (isset($_POST['descripcion'])) {
        $descripcion = $db->real_escape_string($_POST['descripcion']);
        $updates[] = "DESCRIPCION = " . ($descripcion ? "'$descripcion'" : "NULL");
    }

    if (isset($_POST['tipo'])) {
        $tipo = $db->real_escape_string($_POST['tipo']);
        $tipos_validos = ['principal', 'galeria', 'banner', 'responsable'];
        if (in_array($tipo, $tipos_validos)) {
            $updates[] = "TIPO = '$tipo'";
        }
    }

    if (isset($_POST['orden'])) {
        $orden = intval($_POST['orden']);
        $updates[] = "ORDEN = $orden";
    }

    if (isset($_POST['activo'])) {
        $activo = $db->real_escape_string($_POST['activo']);
        if (in_array($activo, ['S', 'N'])) {
            $updates[] = "ACTIVO = '$activo'";
        }
    }

    // Si no hay nada que actualizar
    if (empty($updates)) {
        $info['success'] = 0;
        $info['message'] = 'No hay campos para actualizar';
        echo json_encode($info);
        exit();
    }

    // Ejecutar UPDATE
    $cad = "UPDATE VRE_GALERIA SET " . implode(', ', $updates) . " WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql) {
        // Registrar en auditoría
        $temp->registrar_auditoria('GALERIA', 'EDITAR', "Editó imagen ID: $id");

        $info['success'] = 1;
        $info['message'] = 'Imagen actualizada correctamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar imagen: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);

