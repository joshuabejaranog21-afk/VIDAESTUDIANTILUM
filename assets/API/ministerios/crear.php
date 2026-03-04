<?php
include("../../php/template.php");
include("../../php/ImageHelper.php");

header('Content-Type: application/json');
$temp = new Template();
$db = new Conexion();
$imageHelper = new ImageHelper();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('ministerios', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para crear ministerios';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Iniciar transacción
        $db->autocommit(false);
        
        // Recibir y validar datos básicos del ministerio
        $nombre = $db->real_escape_string(trim($_POST['nombre'] ?? ''));
        $descripcion = $db->real_escape_string(trim($_POST['descripcion'] ?? ''));
        $tipo_director = $_POST['tipo_director'] ?? 'existente';

        // Imagen principal (opcional) - se guardará en VRE_GALERIA
        $imagen_principal_url = isset($_POST['imagen_url']) ? $db->real_escape_string(trim($_POST['imagen_url'])) : null;

        // Validar campos obligatorios
        if (empty($nombre) || empty($descripcion)) {
            throw new Exception('El nombre y la descripción del ministerio son obligatorios');
        }

        $director_usuario_id = null;
        $director_creado = false;

        // MODO 1: Asignar usuario existente
        if ($tipo_director === 'existente') {
            $director_usuario_id = intval($_POST['director_usuario'] ?? 0);

            // Si se seleccionó un usuario, verificar que existe
            if ($director_usuario_id > 0) {
                $sql_check = $db->query("SELECT ID, NOMBRE FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id AND ACTIVO = 'S'");
                if (!$sql_check || $sql_check->num_rows === 0) {
                    throw new Exception('El usuario seleccionado no está disponible');
                }
            } else {
                // No se seleccionó usuario, ministerio sin director
                $director_usuario_id = null;
            }
        }
        // MODO 2: Crear nuevo usuario director
        else if ($tipo_director === 'nuevo') {
            $nuevo_nombre = $db->real_escape_string(trim($_POST['nuevo_nombre'] ?? ''));
            $nuevo_email = $db->real_escape_string(trim($_POST['nuevo_email'] ?? ''));
            $nuevo_password = trim($_POST['nuevo_password'] ?? '');
            $nombre_completo = $db->real_escape_string(trim($_POST['nombre_completo'] ?? ''));

            // Si se proporcionan datos del director, crear el usuario
            if (!empty($nuevo_nombre) && !empty($nuevo_email) && !empty($nuevo_password) && !empty($nombre_completo)) {
            // Validar que la contraseña tenga al menos 8 caracteres
            if (strlen($nuevo_password) < 8) {
                throw new Exception('La contraseña debe tener al menos 8 caracteres');
            }
            
            // Verificar que el nombre de usuario no exista
            $sql_check = $db->query("SELECT ID FROM SYSTEM_USUARIOS WHERE NOMBRE = '$nuevo_nombre'");
            if ($sql_check && $sql_check->num_rows > 0) {
                throw new Exception('El nombre de usuario ya existe');
            }
            
            // Verificar que el email no exista
            $sql_check = $db->query("SELECT ID FROM SYSTEM_USUARIOS WHERE EMAIL = '$nuevo_email'");
            if ($sql_check && $sql_check->num_rows > 0) {
                throw new Exception('El email ya está registrado');
            }
            
            // Usar rol DIRECTOR_MINISTERIO existente (ID 11)
            $rol_director = 11;
            
            // Crear nuevo usuario
            $password_hash = md5($nuevo_password);
            $token = md5($nuevo_nombre . time());
            
            $sql_usuario = "INSERT INTO SYSTEM_USUARIOS(NOMBRE, PASS, ID_CAT, EMAIL, ACTIVO, TOKEN, FECHA_CREACION) 
                           VALUES ('$nuevo_nombre', '$password_hash', $rol_director, '$nuevo_email', 'S', '$token', NOW())";
            
            if (!$db->query($sql_usuario)) {
                throw new Exception('Error al crear el usuario director: ' . $db->error);
            }

            $director_usuario_id = $db->insert_id;
            $director_creado = true;
            } else {
                // Campos incompletos, no crear director
                $director_usuario_id = null;
            }
        }
        
        // Crear el ministerio (sin campos de imagen, ahora usa VRE_GALERIA)
        if ($director_usuario_id) {
            $sql_ministerio = "INSERT INTO VRE_MINISTERIOS(NOMBRE, DESCRIPCION, ID_DIRECTOR_USUARIO, ACTIVO, FECHA_CREACION)
                              VALUES ('$nombre', '$descripcion', $director_usuario_id, 'S', NOW())";
        } else {
            $sql_ministerio = "INSERT INTO VRE_MINISTERIOS(NOMBRE, DESCRIPCION, ACTIVO, FECHA_CREACION)
                              VALUES ('$nombre', '$descripcion', 'S', NOW())";
        }

        if (!$db->query($sql_ministerio)) {
            throw new Exception('Error al crear el ministerio: ' . $db->error);
        }

        $ministerio_id = $db->insert_id;

        // Si se proporcionó una imagen principal, guardarla en VRE_GALERIA
        if (!empty($imagen_principal_url)) {
            $cad_galeria = "INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                           VALUES ('ministerios', $ministerio_id, '$nombre - Principal', '$imagen_principal_url', 'principal', 1, 'S', {$temp->usuario_id})";

            if (!$db->query($cad_galeria)) {
                throw new Exception('Error al registrar imagen en galería: ' . $db->error);
            }
        }
        
        // Si se creó director, crear registro en directiva
        if ($director_creado) {
            $sql_directiva = "INSERT INTO VRE_DIRECTIVA_MINISTERIOS(ID_MINISTERIO, DIRECTOR_NOMBRE, DIRECTOR_EMAIL, ESTADO) 
                             VALUES ($ministerio_id, '$nombre_completo', '$nuevo_email', 'activo')";
            if (!$db->query($sql_directiva)) {
                throw new Exception('Error al crear registro de directiva: ' . $db->error);
            }
        }
        
        // Confirmar transacción
        $db->commit();
        $db->autocommit(true);
        
        // Registrar en auditoría
        $descripcion_auditoria = "Ministerio '$nombre' creado (ID: $ministerio_id)";
        if ($director_creado) {
            $descripcion_auditoria .= " con nuevo director: $nuevo_nombre";
        } else if ($director_usuario_id && $tipo_director === 'existente') {
            $sql_director_nombre = $db->query("SELECT NOMBRE FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id");
            if ($sql_director_nombre && $director_info = $sql_director_nombre->fetch_assoc()) {
                $descripcion_auditoria .= " y asignado a director existente: {$director_info['NOMBRE']}";
            }
        } else {
            $descripcion_auditoria .= " sin director asignado";
        }
        $temp->registrar_auditoria('MINISTERIOS', 'CREAR', $descripcion_auditoria);

        // Preparar respuesta exitosa
        if ($director_creado) {
            $mensaje = "Ministerio '$nombre' creado exitosamente y director '$nuevo_nombre' registrado.";
        } else if ($director_usuario_id) {
            $mensaje = "Ministerio '$nombre' creado exitosamente y asignado a director existente.";
        } else {
            $mensaje = "Ministerio '$nombre' creado exitosamente. Un administrador puede asignar un director después.";
        }
        
        $info['success'] = 1;
        $info['message'] = $mensaje;
        $info['ministerio_id'] = $ministerio_id;
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();
        $db->autocommit(true);
        
        $info['success'] = 0;
        $info['message'] = $e->getMessage();
    }
    
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
