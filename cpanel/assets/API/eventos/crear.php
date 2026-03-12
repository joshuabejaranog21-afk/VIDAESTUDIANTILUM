<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Crear nuevo evento
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

if (!$temp->tiene_permiso('eventos', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para crear eventos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['titulo'])) {
        $info['success'] = 0;
        $info['message'] = 'El título es requerido';
        echo json_encode($info);
exit;
        exit();
    }

    $titulo = $db->real_escape_string($_POST['titulo']);
    $slug = isset($_POST['slug']) ? $db->real_escape_string($_POST['slug']) : strtolower(str_replace(' ', '-', $titulo));
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $descripcion_corta = isset($_POST['descripcion_corta']) ? $db->real_escape_string($_POST['descripcion_corta']) : null;
    $fecha_evento = isset($_POST['fecha_evento']) ? $db->real_escape_string($_POST['fecha_evento']) : null;
    $fecha_fin = isset($_POST['fecha_fin']) ? $db->real_escape_string($_POST['fecha_fin']) : null;
    $lugar = isset($_POST['lugar']) ? $db->real_escape_string($_POST['lugar']) : null;
    $organizador = isset($_POST['organizador']) ? $db->real_escape_string($_POST['organizador']) : 'OTRO';
    $organizador_nombre = isset($_POST['organizador_nombre']) ? $db->real_escape_string($_POST['organizador_nombre']) : null;
    $categoria = isset($_POST['categoria']) ? $db->real_escape_string($_POST['categoria']) : null;

    // Procesar subida de imagen
    $imagen_principal = null;
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
            $imagen_principal = 'assets/uploads/eventos/' . $nombreArchivo;
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al subir la imagen';
            echo json_encode($info);
            exit();
        }
    }

    $costo = isset($_POST['costo']) ? $db->real_escape_string($_POST['costo']) : null;
    $cupo_maximo = isset($_POST['cupo_maximo']) ? intval($_POST['cupo_maximo']) : null;
    $registro_requerido = isset($_POST['registro_requerido']) ? $db->real_escape_string($_POST['registro_requerido']) : 'N';
    $enlace_registro = isset($_POST['enlace_registro']) ? $db->real_escape_string($_POST['enlace_registro']) : null;
    $estado = isset($_POST['estado']) ? $db->real_escape_string($_POST['estado']) : 'PROXIMO';
    $destacado = isset($_POST['destacado']) ? $db->real_escape_string($_POST['destacado']) : 'N';
    $activo = isset($_POST['activo']) ? $db->real_escape_string($_POST['activo']) : 'S';

    $cad = "INSERT INTO VRE_EVENTOS
            (TITULO, SLUG, DESCRIPCION, DESCRIPCION_CORTA, FECHA_EVENTO, FECHA_FIN, LUGAR,
             ORGANIZADOR, ORGANIZADOR_NOMBRE, CATEGORIA, IMAGEN_PRINCIPAL, COSTO, CUPO_MAXIMO,
             REGISTRO_REQUERIDO, ENLACE_REGISTRO, ESTADO, DESTACADO, ACTIVO, ID_USUARIO_CREADOR)
            VALUES
            ('$titulo', '$slug', " .
            ($descripcion ? "'$descripcion'" : "NULL") . ", " .
            ($descripcion_corta ? "'$descripcion_corta'" : "NULL") . ", " .
            ($fecha_evento ? "'$fecha_evento'" : "NULL") . ", " .
            ($fecha_fin ? "'$fecha_fin'" : "NULL") . ", " .
            ($lugar ? "'$lugar'" : "NULL") . ", " .
            "'$organizador', " .
            ($organizador_nombre ? "'$organizador_nombre'" : "NULL") . ", " .
            ($categoria ? "'$categoria'" : "NULL") . ", " .
            ($imagen_principal ? "'$imagen_principal'" : "NULL") . ", " .
            ($costo ? "'$costo'" : "NULL") . ", " .
            ($cupo_maximo ? "$cupo_maximo" : "NULL") . ", " .
            "'$registro_requerido', " .
            ($enlace_registro ? "'$enlace_registro'" : "NULL") . ", " .
            "'$estado', '$destacado', '$activo', {$temp->usuario_id})";

    $sql = $db->query($cad);

    if ($sql) {
        $id_insertado = $db->insert_id;
        $temp->registrar_auditoria('EVENTOS', 'CREAR', "Evento creado: $titulo (ID: $id_insertado)");

        $info['success'] = 1;
        $info['message'] = 'Evento creado correctamente';
        $info['id'] = $id_insertado;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al crear: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
exit;

