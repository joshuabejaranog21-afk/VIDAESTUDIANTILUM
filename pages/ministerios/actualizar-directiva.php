<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);

// Configuración de upload
define('UPLOAD_DIR', '../../uploads/directiva-ministerios/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

$response = ['success' => false, 'message' => ''];

// Crear directorio si no existe
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

/**
 * Procesar y guardar foto de directiva
 */
function procesar_foto_directiva($file, $id_ministerio, $cargo) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Validar tipo de archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_TYPES)) {
        throw new Exception('Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP.');
    }
    
    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception('El archivo es muy grande. Máximo 5MB.');
    }
    
    // Generar nombre de archivo único
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower($ext);
    
    if (!in_array($ext, $allowed_ext)) {
        throw new Exception('Extensión de archivo no permitida.');
    }
    
    // Crear nombre único: ministerio_cargo_timestamp.ext
    $filename = 'ministerio_' . $id_ministerio . '_' . strtolower($cargo) . '_' . time() . '.' . $ext;
    $filepath = UPLOAD_DIR . $filename;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Error al guardar la imagen.');
    }
    
    // Retornar ruta relativa para guardar en BD
    return '/uploads/directiva-ministerios/' . $filename;
}

try {
    $temp = new Template();
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }
    if (!$temp->es_director_ministerio()) {
        throw new Exception('Acceso denegado');
    }
    $ministerio = $temp->obtener_ministerio_asignado();
    if (!$ministerio) {
        throw new Exception('No tienes un ministerio asignado');
    }

    $db = new Conexion();

    $id_directiva = isset($_POST['id_directiva']) ? intval($_POST['id_directiva']) : 0;
    $id_ministerio = isset($_POST['id_ministerio']) ? intval($_POST['id_ministerio']) : 0;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : 'actualizar';

    if ($id_directiva <= 0 || $id_ministerio <= 0) {
        throw new Exception('Datos inválidos');
    }

    // Verificar que la directiva pertenece al ministerio del usuario
    $check = $db->query("SELECT ID FROM VRE_DIRECTIVA_MINISTERIOS WHERE ID = $id_directiva AND ID_MINISTERIO = $id_ministerio");
    if (!$check || $check->num_rows == 0) {
        throw new Exception('No tienes permiso para editar esta directiva');
    }

    // Cargos disponibles
    $cargos = ['DIRECTOR', 'SUBDIRECTOR', 'SECRETARIO', 'TESORERO', 'CAPELLAN', 'CONSEJERO_GENERAL', 'LOGISTICA', 'MEDIA'];

    if ($accion === 'agregar' || $accion === 'actualizar') {
        // Obtener cargo
        $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
        if (!in_array($cargo, $cargos)) {
            throw new Exception('Cargo inválido');
        }

        $nombre = isset($_POST['nombre']) ? $db->real_escape_string(trim($_POST['nombre'])) : '';
        $email = isset($_POST['email']) ? $db->real_escape_string(trim($_POST['email'])) : '';
        $telefono = isset($_POST['telefono']) ? $db->real_escape_string(trim($_POST['telefono'])) : '';

        if (empty($nombre)) {
            throw new Exception('El nombre es obligatorio');
        }

        // Validar email si se proporciona
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        $nombre_field = strtoupper($cargo) . '_NOMBRE';
        $email_field = strtoupper($cargo) . '_EMAIL';
        $telefono_field = strtoupper($cargo) . '_TELEFONO';
        $foto_field = strtoupper($cargo) . '_FOTO';
        
        // Procesar foto si se envió
        $foto_path = null;
        if (isset($_FILES['foto'])) {
            // Si es actualización, obtener foto actual para eliminarla
            if ($accion === 'actualizar') {
                $result = $db->query("SELECT $foto_field FROM VRE_DIRECTIVA_MINISTERIOS WHERE ID = $id_directiva");
                if ($result && $row = $result->fetch_assoc()) {
                    $foto_actual = $row[$foto_field];
                    if (!empty($foto_actual)) {
                        $file_path = $_SERVER['DOCUMENT_ROOT'] . $foto_actual;
                        if (file_exists($file_path)) {
                            @unlink($file_path);
                        }
                    }
                }
            }
            
            $foto_path = procesar_foto_directiva($_FILES['foto'], $id_ministerio, $cargo);
        }
        
        // Construir SQL dinámicamente
        $updates = [];
        $updates[] = "$nombre_field = '$nombre'";
        $updates[] = "$email_field = '$email'";
        $updates[] = "$telefono_field = '$telefono'";
        
        if ($foto_path !== null) {
            $foto_path_escaped = $db->real_escape_string($foto_path);
            $updates[] = "$foto_field = '$foto_path_escaped'";
        }
        
        $sql = "UPDATE VRE_DIRECTIVA_MINISTERIOS SET 
                " . implode(",\n                ", $updates) . "
                WHERE ID = $id_directiva";

        if ($db->query($sql)) {
            $temp->registrar_auditoria('DIRECTIVA_MINISTERIOS', 'EDITAR', "Cargo $cargo del ministerio ID $id_ministerio actualizado");
            $response['success'] = true;
            $response['message'] = $accion === 'agregar' ? 'Miembro agregado correctamente' : 'Miembro actualizado correctamente';
        } else {
            throw new Exception('Error al actualizar: ' . $db->error);
        }
    } 
    else if ($accion === 'eliminar') {
        // Eliminar cargo
        $cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
        if (!in_array($cargo, $cargos)) {
            throw new Exception('Cargo inválido');
        }

        $nombre_field = strtoupper($cargo) . '_NOMBRE';
        $email_field = strtoupper($cargo) . '_EMAIL';
        $telefono_field = strtoupper($cargo) . '_TELEFONO';
        $foto_field = strtoupper($cargo) . '_FOTO';
        
        // Obtener foto actual para eliminarla del servidor
        $result = $db->query("SELECT $foto_field FROM VRE_DIRECTIVA_MINISTERIOS WHERE ID = $id_directiva");
        if ($result && $row = $result->fetch_assoc()) {
            $foto_actual = $row[$foto_field];
            if (!empty($foto_actual)) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . $foto_actual;
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
            }
        }

        $sql = "UPDATE VRE_DIRECTIVA_MINISTERIOS SET 
                $nombre_field = NULL,
                $email_field = NULL,
                $telefono_field = NULL,
                $foto_field = NULL
                WHERE ID = $id_directiva";

        if ($db->query($sql)) {
            $temp->registrar_auditoria('DIRECTIVA_MINISTERIOS', 'ELIMINAR', "Cargo $cargo del ministerio ID $id_ministerio eliminado");
            $response['success'] = true;
            $response['message'] = 'Miembro eliminado correctamente';
        } else {
            throw new Exception('Error al eliminar: ' . $db->error);
        }
    }
    else {
        throw new Exception('Acción no válida');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
