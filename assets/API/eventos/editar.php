<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Editar evento existente
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

if (!$temp->tiene_permiso('eventos', 'editar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para editar eventos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
exit;
        exit();
    }

    $id = intval($_POST['id']);

    $check = $db->query("SELECT * FROM VRE_EVENTOS WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
        echo json_encode($info);
exit;
        exit();
    }

    $updates = [];

    if (isset($_POST['titulo'])) {
        $titulo = $db->real_escape_string($_POST['titulo']);
        $updates[] = "TITULO = '$titulo'";
    }

    if (isset($_POST['slug'])) {
        $slug = $db->real_escape_string($_POST['slug']);
        $updates[] = "SLUG = '$slug'";
    }

    if (isset($_POST['descripcion'])) {
        $descripcion = $db->real_escape_string($_POST['descripcion']);
        $updates[] = "DESCRIPCION = " . ($descripcion ? "'$descripcion'" : "NULL");
    }

    if (isset($_POST['descripcion_corta'])) {
        $descripcion_corta = $db->real_escape_string($_POST['descripcion_corta']);
        $updates[] = "DESCRIPCION_CORTA = " . ($descripcion_corta ? "'$descripcion_corta'" : "NULL");
    }

    if (isset($_POST['fecha_evento'])) {
        $fecha_evento = $db->real_escape_string($_POST['fecha_evento']);
        $updates[] = "FECHA_EVENTO = " . ($fecha_evento ? "'$fecha_evento'" : "NULL");
    }

    if (isset($_POST['fecha_fin'])) {
        $fecha_fin = $db->real_escape_string($_POST['fecha_fin']);
        $updates[] = "FECHA_FIN = " . ($fecha_fin ? "'$fecha_fin'" : "NULL");
    }

    if (isset($_POST['lugar'])) {
        $lugar = $db->real_escape_string($_POST['lugar']);
        $updates[] = "LUGAR = " . ($lugar ? "'$lugar'" : "NULL");
    }

    if (isset($_POST['organizador'])) {
        $organizador = $db->real_escape_string($_POST['organizador']);
        $updates[] = "ORGANIZADOR = '$organizador'";
    }

    if (isset($_POST['organizador_nombre'])) {
        $organizador_nombre = $db->real_escape_string($_POST['organizador_nombre']);
        $updates[] = "ORGANIZADOR_NOMBRE = " . ($organizador_nombre ? "'$organizador_nombre'" : "NULL");
    }

    if (isset($_POST['categoria'])) {
        $categoria = $db->real_escape_string($_POST['categoria']);
        $updates[] = "CATEGORIA = " . ($categoria ? "'$categoria'" : "NULL");
    }

    if (isset($_POST['imagen_principal'])) {
        $imagen_principal = $db->real_escape_string($_POST['imagen_principal']);
        $updates[] = "IMAGEN_PRINCIPAL = " . ($imagen_principal ? "'$imagen_principal'" : "NULL");
    }

    if (isset($_POST['costo'])) {
        $costo = $db->real_escape_string($_POST['costo']);
        $updates[] = "COSTO = " . ($costo ? "'$costo'" : "NULL");
    }

    if (isset($_POST['cupo_maximo'])) {
        $cupo_maximo = intval($_POST['cupo_maximo']);
        $updates[] = "CUPO_MAXIMO = " . ($cupo_maximo > 0 ? "$cupo_maximo" : "NULL");
    }

    if (isset($_POST['registro_requerido'])) {
        $registro_requerido = $db->real_escape_string($_POST['registro_requerido']);
        if (in_array($registro_requerido, ['S', 'N'])) {
            $updates[] = "REGISTRO_REQUERIDO = '$registro_requerido'";
        }
    }

    if (isset($_POST['enlace_registro'])) {
        $enlace_registro = $db->real_escape_string($_POST['enlace_registro']);
        $updates[] = "ENLACE_REGISTRO = " . ($enlace_registro ? "'$enlace_registro'" : "NULL");
    }

    if (isset($_POST['estado'])) {
        $estado = $db->real_escape_string($_POST['estado']);
        $updates[] = "ESTADO = '$estado'";
    }

    if (isset($_POST['destacado'])) {
        $destacado = $db->real_escape_string($_POST['destacado']);
        if (in_array($destacado, ['S', 'N'])) {
            $updates[] = "DESTACADO = '$destacado'";
        }
    }

    if (isset($_POST['activo'])) {
        $activo = $db->real_escape_string($_POST['activo']);
        if (in_array($activo, ['S', 'N'])) {
            $updates[] = "ACTIVO = '$activo'";
        }
    }

    if (empty($updates)) {
        $info['success'] = 0;
        $info['message'] = 'No hay campos para actualizar';
        echo json_encode($info);
exit;
        exit();
    }

    $cad = "UPDATE VRE_EVENTOS SET " . implode(', ', $updates) . " WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql) {
        $temp->registrar_auditoria('EVENTOS', 'EDITAR', "Editado ID: $id");
        $info['success'] = 1;
        $info['message'] = 'Actualizado correctamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
exit;

