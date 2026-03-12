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

    $eventoActual = $check->fetch_assoc();
    $updates = [];

    // Procesar subida de nueva imagen
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen_principal'];

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            $info['success'] = 0;
            $info['message'] = 'Formato de imagen no válido. Solo JPG, PNG, GIF, WEBP.';
            echo json_encode($info);
            exit();
        }

        // Validar tamaño (máx 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            $info['success'] = 0;
            $info['message'] = 'La imagen no puede superar los 5MB';
            echo json_encode($info);
            exit();
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'evento_' . uniqid() . '_' . time() . '.' . $extension;
        $directorioDestino = '../../uploads/eventos/';

        // Crear directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            // Eliminar imagen anterior si existe
            if (!empty($eventoActual['IMAGEN_PRINCIPAL'])) {
                $imagenAnterior = '../../' . $eventoActual['IMAGEN_PRINCIPAL'];
                if (file_exists($imagenAnterior)) {
                    unlink($imagenAnterior);
                }
            }

            $nuevaImagen = 'assets/uploads/eventos/' . $nombreArchivo;
            $updates[] = "IMAGEN_PRINCIPAL = '$nuevaImagen'";
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al subir la imagen';
            echo json_encode($info);
            exit();
        }
    }

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

    // Nota: imagen_principal se maneja arriba mediante $_FILES, no aquí

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

