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
if (!$temp->tiene_permiso('clubes', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para crear clubes';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Iniciar transacción
        $db->autocommit(false);
        
        // PASO 1: Recibir y validar datos básicos del club
        $nombre = $db->real_escape_string(trim($_POST['nombre'] ?? ''));
        $descripcion = $db->real_escape_string(trim($_POST['descripcion'] ?? ''));
        $tipo_director = $_POST['tipo_director'] ?? 'existente';
        
        // Validar campos obligatorios
        if (empty($nombre) || empty($descripcion)) {
            throw new Exception('El nombre y la descripción del club son obligatorios');
        }
        
        // PASO 2: Procesar imagen principal (opcional) - se guardará en VRE_GALERIA
        $imagen_principal_url = isset($_POST['imagen_url']) ? $db->real_escape_string(trim($_POST['imagen_url'])) : null;
        
        // PASO 3: Manejar director (existente o nuevo)
        $director_usuario_id = null;
        $director_creado = false;
        $nuevo_nombre = '';
        $nombre_completo = '';

        if ($tipo_director === 'existente') {
            // Asignar usuario existente
            $director_usuario_id = intval($_POST['director_usuario'] ?? 0);

            // Si se seleccionó un usuario, verificar que existe y está disponible
            if ($director_usuario_id > 0) {
                // Verificar que el usuario existe y no tiene club asignado
                $sql_check = $db->query("SELECT ID, NOMBRE FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id AND ACTIVO = 'S' AND ID_CLUB_ASIGNADO IS NULL");
                if (!$sql_check || $sql_check->num_rows === 0) {
                    throw new Exception('El usuario seleccionado no está disponible o ya tiene un club asignado');
                }
            } else {
                // No se seleccionó usuario, crear club sin director
                $director_usuario_id = null;
            }

        } else if ($tipo_director === 'nuevo') {
            // Crear nuevo usuario director
            $nuevo_nombre = $db->real_escape_string(trim($_POST['nuevo_nombre'] ?? ''));
            $nuevo_email = $db->real_escape_string(trim($_POST['nuevo_email'] ?? ''));
            $nuevo_password = trim($_POST['nuevo_password'] ?? '');
            $nombre_completo = $db->real_escape_string(trim($_POST['nombre_completo'] ?? ''));

            // Validar datos del nuevo usuario
            if (empty($nuevo_nombre) || empty($nuevo_email) || empty($nuevo_password) || empty($nombre_completo)) {
                throw new Exception('Todos los campos del nuevo director son obligatorios');
            }

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

            // Usar rol DIRECTOR_CLUB existente (ID 8)
            $rol_director = 8;

            // Crear nuevo usuario
            $password_hash = md5($nuevo_password);
            $token = md5($nuevo_nombre . time());

            $sql_usuario = "INSERT INTO SYSTEM_USUARIOS(NOMBRE, NOMBRE_COMPLETO, PASS, ID_CAT, EMAIL, ACTIVO, TOKEN, FECHA_CREACION)
                           VALUES ('$nuevo_nombre', '$nombre_completo', '$password_hash', $rol_director, '$nuevo_email', 'S', '$token', NOW())";

            if (!$db->query($sql_usuario)) {
                throw new Exception('Error al crear el usuario director: ' . $db->error);
            }

            $director_usuario_id = $db->insert_id;
            $director_creado = true;
        }

        // PASO 3.5: Obtener nombre del responsable (director) antes de crear el club
        $responsable_nombre = '';
        if ($director_usuario_id) {
            if ($director_creado && !empty($nombre_completo)) {
                // Si se creó un nuevo director, usar su nombre completo
                $responsable_nombre = $nombre_completo;
            } else if ($director_usuario_id > 0) {
                // Si es un director existente, obtener su nombre completo o username de la BD
                $sql_responsable = $db->query("SELECT NOMBRE_COMPLETO, NOMBRE FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id");
                if ($sql_responsable && $sql_responsable->num_rows > 0) {
                    $resp_info = $sql_responsable->fetch_assoc();
                    // Preferir NOMBRE_COMPLETO si existe, sino usar NOMBRE (username)
                    $responsable_nombre = !empty($resp_info['NOMBRE_COMPLETO'])
                        ? $db->real_escape_string($resp_info['NOMBRE_COMPLETO'])
                        : $db->real_escape_string($resp_info['NOMBRE']);
                }
            }
        }

        // PASO 4: Crear el club incluyendo el RESPONSABLE_NOMBRE
        $director_value = ($director_usuario_id && intval($director_usuario_id) > 0) ? intval($director_usuario_id) : 'NULL';
        $responsable_value = !empty($responsable_nombre) ? "'$responsable_nombre'" : 'NULL';

        $sql_club = "INSERT INTO VRE_CLUBES(NOMBRE, DESCRIPCION, ID_DIRECTOR_USUARIO, RESPONSABLE_NOMBRE, ACTIVO, FECHA_CREACION)
                     VALUES ('$nombre', '$descripcion', $director_value, $responsable_value, 'S', NOW())";

        if (!$db->query($sql_club)) {
            throw new Exception('Error al crear el club: ' . $db->error);
        }

        $club_id = $db->insert_id;

        // Si se proporcionó una imagen principal, guardarla en VRE_GALERIA
        if (!empty($imagen_principal_url)) {
            $cad_galeria = "INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                           VALUES ('clubes', $club_id, '$nombre - Principal', '$imagen_principal_url', 'principal', 1, 'S', {$temp->usuario_id})";

            if (!$db->query($cad_galeria)) {
                throw new Exception('Error al registrar imagen en galería: ' . $db->error);
            }
        }

        // PASO 5: Asignar club al director actualizando el usuario (solo si hay director)
        if ($director_usuario_id) {
            if (!$db->query("UPDATE SYSTEM_USUARIOS SET ID_CLUB_ASIGNADO = $club_id WHERE ID = $director_usuario_id")) {
                throw new Exception('Error al asignar el club al director: ' . $db->error);
            }

            // PASO 5B: Crear registro en VRE_DIRECTIVA_CLUBES e insertar director
            $director_nombre = '';
            $director_email = '';
            $sql_director = $db->query("SELECT NOMBRE_COMPLETO, NOMBRE, EMAIL FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id");
            if ($sql_director && $sql_director->num_rows > 0) {
                $director_info = $sql_director->fetch_assoc();
                // Preferir NOMBRE_COMPLETO si existe, sino usar NOMBRE (username)
                $director_nombre = !empty($director_info['NOMBRE_COMPLETO'])
                    ? $db->real_escape_string($director_info['NOMBRE_COMPLETO'])
                    : $db->real_escape_string($director_info['NOMBRE']);
                $director_email = $db->real_escape_string($director_info['EMAIL']);
            }

            $sql_directiva = "INSERT INTO VRE_DIRECTIVA_CLUBES(ID_CLUB, DIRECTOR_NOMBRE, DIRECTOR_EMAIL, ESTADO)
                             VALUES ($club_id, '$director_nombre', '$director_email', 'activo')";
            if (!$db->query($sql_directiva)) {
                throw new Exception('Error al crear registro de directiva: ' . $db->error);
            }
        }
        
        // Confirmar transacción
        $db->commit();
        $db->autocommit(true);
        
        // PASO 6: Registrar en auditoría
        $descripcion_auditoria = "Club '$nombre' creado (ID: $club_id) y asignado a ";
        if ($director_creado) {
            $descripcion_auditoria .= "nuevo director: $nuevo_nombre";
        } else {
            $sql_director_nombre = $db->query("SELECT NOMBRE FROM SYSTEM_USUARIOS WHERE ID = $director_usuario_id");
            $director_info = $sql_director_nombre->fetch_assoc();
            $descripcion_auditoria .= "director: {$director_info['NOMBRE']}";
        }
        
        $temp->registrar_auditoria('CLUBES', 'CREAR', $descripcion_auditoria);
        
        // Preparar respuesta exitosa
        $mensaje = "Club '$nombre' creado exitosamente";
        if ($director_creado) {
            $mensaje .= " y director '$nuevo_nombre' registrado";
        }
        $mensaje .= ". El director debe completar la información del club.";
        
        $info['success'] = 1;
        $info['message'] = $mensaje;
        $info['club_id'] = $club_id;
        $info['director_creado'] = $director_creado;
        $info['director_usuario_id'] = $director_usuario_id;

        // Debug info
        $info['debug'] = [
            'tipo_director' => $tipo_director,
            'director_id' => $director_usuario_id,
            'director_value' => $director_value,
            'club_id' => $club_id,
            'director_creado' => $director_creado,
            'sql_club' => $sql_club
        ];
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();
        $db->autocommit(true);
        
        $info['success'] = 0;
        $info['message'] = $e->getMessage();
        
        // Log de debug solo en desarrollo
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $info['debug'] = [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'post_data' => $_POST
            ];
        }
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>