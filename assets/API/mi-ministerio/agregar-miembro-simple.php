<?php
header('Content-Type: application/json');
include('../../php/template.php');

$temp = new Template();
$db = new Conexion();
$response = ['success' => false, 'message' => ''];

// Validar sesión y permisos
if (!$temp->validate_session()) {
    $response['message'] = 'Sesión inválida';
    echo json_encode($response);
    exit();
}

if (!$temp->es_director_club()) {
    $response['message'] = 'Acceso denegado. Solo directores de club.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener club asignado
        $club = $temp->obtener_club_asignado();
        if (!$club) {
            throw new Exception('No tienes un club asignado');
        }
        
        $club_id = intval($club['ID']);
        
        // Validar datos básicos
        if (empty($_POST['nombre'])) {
            throw new Exception('El nombre es obligatorio');
        }
        
        if (empty($_POST['cargo'])) {
            throw new Exception('El cargo es obligatorio');
        }
        
        // Validar club_id
        $form_club_id = intval($_POST['club_id'] ?? 0);
        if ($form_club_id !== $club_id) {
            throw new Exception('No tienes permiso para agregar miembros a este club');
        }
        
        $nombre = $db->real_escape_string(trim($_POST['nombre']));
        $cargo = $db->real_escape_string(trim($_POST['cargo']));
        $email = !empty($_POST['email']) ? $db->real_escape_string(trim($_POST['email'])) : null;
        $telefono = !empty($_POST['telefono']) ? $db->real_escape_string(trim($_POST['telefono'])) : null;
        $estado = !empty($_POST['estado']) ? $db->real_escape_string(trim($_POST['estado'])) : 'activo';
        $observaciones = !empty($_POST['observaciones']) ? $db->real_escape_string(trim($_POST['observaciones'])) : null;
        
        // Validar email si se proporciona
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El formato del email no es válido');
        }
        
        // Verificar cargos únicos
        $cargos_unicos = ['Presidente', 'Vicepresidente', 'Secretario', 'Tesorero'];
        if (in_array($cargo, $cargos_unicos)) {
            $check_sql = "SELECT COUNT(*) as total FROM VRE_DIRECTIVA_CLUBES WHERE ID_CLUB = $club_id AND CARGO = '$cargo' AND ESTADO = 'activo'";
            $check_result = $db->query($check_sql);
            
            if ($check_result) {
                $row = $check_result->fetch_assoc();
                if ($row['total'] > 0) {
                    throw new Exception("Ya existe un {$cargo} activo en la directiva");
                }
            }
        }
        
        // Insertar miembro
        $sql = "INSERT INTO VRE_DIRECTIVA_CLUBES (
                    ID_CLUB, 
                    NOMBRE, 
                    CARGO, 
                    EMAIL, 
                    TELEFONO, 
                    ESTADO, 
                    OBSERVACIONES,
                    FECHA_REGISTRO
                ) VALUES (
                    $club_id,
                    '$nombre',
                    '$cargo',
                    " . ($email ? "'$email'" : 'NULL') . ",
                    " . ($telefono ? "'$telefono'" : 'NULL') . ",
                    '$estado',
                    " . ($observaciones ? "'$observaciones'" : 'NULL') . ",
                    NOW()
                )";
        
        if ($db->query($sql)) {
            $nuevo_id = $db->insert_id;
            
            // Auditoría
            try {
                $temp->registrar_auditoria('DIRECTIVA', 'CREAR', "Miembro agregado: {$nombre} ({$cargo})");
            } catch (Exception $audit_error) {
                // Error silencioso en auditoría
            }
            
            $response['success'] = true;
            $response['message'] = 'Miembro agregado correctamente';
            $response['data'] = ['id' => $nuevo_id];
        } else {
            throw new Exception('Error al insertar el miembro: ' . $db->error);
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}

echo json_encode($response);
?>