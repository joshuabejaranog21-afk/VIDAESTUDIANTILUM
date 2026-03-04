<?php
include("../../assets/php/template.php");

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

// Validar que sea director de ministerio
if (!$temp->es_director_ministerio()) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para editar ministerios';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'ID del ministerio no proporcionado';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);
    
    // Verificar que el ministerio pertenece al director
    $ministerio_check = $db->query("SELECT ID FROM VRE_MINISTERIOS WHERE ID = $id AND ID_DIRECTOR_USUARIO = " . $temp->usuario_id);
    if (!$ministerio_check || $ministerio_check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No tienes permiso para editar este ministerio';
        echo json_encode($info);
        exit();
    }

    // Recibir datos
    $nombre = $db->real_escape_string($_POST['nombre'] ?? '');
    $descripcion = $db->real_escape_string($_POST['descripcion'] ?? '');
    $objetivo = $db->real_escape_string($_POST['objetivo'] ?? '');
    $requisitos = $db->real_escape_string($_POST['requisitos'] ?? '');
    $beneficios = $db->real_escape_string($_POST['beneficios'] ?? '');
    $horario = $db->real_escape_string($_POST['horario'] ?? '');
    $dia_reunion = $db->real_escape_string($_POST['dia_reunion'] ?? '');
    $lugar = $db->real_escape_string($_POST['lugar'] ?? '');
    $telefono = $db->real_escape_string($_POST['telefono'] ?? '');
    $cupo_maximo = isset($_POST['cupo_maximo']) ? intval($_POST['cupo_maximo']) : null;
    $activo = $_POST['activo'] ?? 'S';
    
    // Manejar imagen
    $imagen_url = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_size = $_FILES['imagen']['size'];
        
        // Validar tamaño (5MB máximo)
        if ($file_size > 5 * 1024 * 1024) {
            $info['success'] = 0;
            $info['message'] = 'El archivo es muy grande (máximo 5MB)';
            echo json_encode($info);
            exit();
        }
        
        // Validar tipo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            $info['success'] = 0;
            $info['message'] = 'Tipo de archivo no permitido';
            echo json_encode($info);
            exit();
        }
        
        // Crear directorio si no existe
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/ministerios/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generar nombre único
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_filename = 'ministerio_' . $id . '_' . time() . '.' . $ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $imagen_url = '/uploads/ministerios/' . $new_filename;
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al guardar la imagen';
            echo json_encode($info);
            exit();
        }
    }

    // Validar campos obligatorios
    if (empty($nombre)) {
        $info['success'] = 0;
        $info['message'] = 'El nombre del ministerio es obligatorio';
        echo json_encode($info);
        exit();
    }

    try {
        // Construir el UPDATE dinámicamente
        $updates = [
            "NOMBRE = '$nombre'",
            "DESCRIPCION = '$descripcion'",
            "OBJETIVO = '$objetivo'",
            "REQUISITOS = '$requisitos'",
            "BENEFICIOS = '$beneficios'",
            "HORARIO = '$horario'",
            "DIA_REUNION = '$dia_reunion'",
            "LUGAR = '$lugar'",
            "TELEFONO = '$telefono'",
            "CUPO_MAXIMO = " . ($cupo_maximo ? $cupo_maximo : 'NULL'),
            "ACTIVO = '$activo'"
        ];
        
        // Agregar imagen si se cargó
        if ($imagen_url) {
            $imagen_url_escaped = $db->real_escape_string($imagen_url);
            $updates[] = "IMAGEN_URL = '$imagen_url_escaped'";
        }
        
        $cad = "UPDATE VRE_MINISTERIOS SET " . implode(", ", $updates) . " WHERE ID = $id";
        error_log("DEBUG SQL: " . $cad);

        $sql = $db->query($cad);

        if ($sql) {
            // Registrar en auditoría
            $temp->registrar_auditoria('MINISTERIOS', 'EDITAR', "Ministerio editado: $nombre (ID: $id)");

            $info['success'] = 1;
            $info['message'] = 'Ministerio actualizado exitosamente';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al actualizar el ministerio';
            $info['error'] = $db->error;
            error_log("DEBUG ERROR: " . $db->error);
        }

    } catch (Exception $e) {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar el ministerio';
        $info['error'] = $e->getMessage();
        error_log("DEBUG EXCEPTION: " . $e->getMessage());
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
