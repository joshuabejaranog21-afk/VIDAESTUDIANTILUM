<?php
header('Content-Type: application/json');
session_start();

require_once '../../functions/Conexion.php';
require_once '../../functions/Template.php';
require_once '../../functions/Auditoria.php';

$response = ['success' => false, 'message' => ''];

try {
    $temp = new Template();
    
    // Verificar autenticación
    if (!$temp->esta_autenticado()) {
        throw new Exception('No estás autenticado');
    }
    
    // Verificar que sea director de club
    if (!$temp->es_director_club()) {
        throw new Exception('No tienes permisos para acceder a esta funcionalidad');
    }
    
    // Obtener el club asignado
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    
    $club_id = intval($club['ID']);
    
    // Validar datos del formulario
    if (empty($_POST['id'])) {
        throw new Exception('ID del miembro es requerido');
    }
    
    if (empty($_POST['nombre'])) {
        throw new Exception('El nombre es obligatorio');
    }
    
    if (empty($_POST['cargo'])) {
        throw new Exception('El cargo es obligatorio');
    }
    
    // Validar que el club_id del formulario coincida con el club asignado
    $form_club_id = intval($_POST['club_id']);
    if ($form_club_id !== $club_id) {
        throw new Exception('No tienes permiso para actualizar miembros de este club');
    }
    
    $miembro_id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $cargo = trim($_POST['cargo']);
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $estado = !empty($_POST['estado']) ? trim($_POST['estado']) : 'activo';
    $observaciones = !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : null;
    
    // Validar longitudes
    if (strlen($nombre) > 100) {
        throw new Exception('El nombre no puede exceder 100 caracteres');
    }
    
    if (strlen($cargo) > 50) {
        throw new Exception('El cargo no puede exceder 50 caracteres');
    }
    
    if ($email && strlen($email) > 100) {
        throw new Exception('El email no puede exceder 100 caracteres');
    }
    
    if ($telefono && strlen($telefono) > 20) {
        throw new Exception('El teléfono no puede exceder 20 caracteres');
    }
    
    if ($observaciones && strlen($observaciones) > 500) {
        throw new Exception('Las observaciones no pueden exceder 500 caracteres');
    }
    
    // Validar email si se proporciona
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    // Validar estado
    if (!in_array($estado, ['activo', 'inactivo'])) {
        $estado = 'activo';
    }
    
    // Obtener conexión a la base de datos
    $conexion = new Conexion();
    $db = $conexion->obtenerConexion();
    
    // Verificar que el miembro existe y pertenece al club
    $check_sql = "SELECT COUNT(*) as total FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ID_CLUB = ?";
    $check_stmt = $db->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception('Error al preparar consulta de verificación: ' . $db->error);
    }
    
    $check_stmt->bind_param("ii", $miembro_id, $club_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    
    if ($check_result['total'] == 0) {
        throw new Exception('Miembro no encontrado o no tienes permisos para editarlo');
    }
    $check_stmt->close();
    
    // Verificar si ya existe un miembro con el mismo cargo (para cargos únicos)
    $cargos_unicos = ['Presidente', 'Vicepresidente', 'Secretario', 'Tesorero'];
    if (in_array($cargo, $cargos_unicos)) {
        $check_cargo_sql = "SELECT COUNT(*) as total FROM VRE_DIRECTIVA_CLUBES WHERE ID_CLUB = ? AND CARGO = ? AND ESTADO = 'activo' AND ID != ?";
        $check_cargo_stmt = $db->prepare($check_cargo_sql);
        if ($check_cargo_stmt) {
            $check_cargo_stmt->bind_param("isi", $club_id, $cargo, $miembro_id);
            $check_cargo_stmt->execute();
            $check_cargo_result = $check_cargo_stmt->get_result()->fetch_assoc();
            
            if ($check_cargo_result['total'] > 0) {
                throw new Exception("Ya existe otro {$cargo} activo en la directiva");
            }
            $check_cargo_stmt->close();
        }
    }
    
    // Iniciar transacción
    $db->autocommit(false);
    
    try {
        // Actualizar miembro
        $sql = "UPDATE VRE_DIRECTIVA_CLUBES SET 
                    NOMBRE = ?, 
                    CARGO = ?, 
                    EMAIL = ?, 
                    TELEFONO = ?, 
                    ESTADO = ?, 
                    OBSERVACIONES = ?
                WHERE ID = ? AND ID_CLUB = ?";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $db->error);
        }
        
        $stmt->bind_param("ssssssii", $nombre, $cargo, $email, $telefono, $estado, $observaciones, $miembro_id, $club_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar el miembro: ' . $stmt->error);
        }
        
        if ($db->affected_rows === 0) {
            // Verificar si es porque no hay cambios reales
            $verify_sql = $db->query("SELECT COUNT(*) as total FROM VRE_DIRECTIVA_CLUBES WHERE ID = $miembro_id AND ID_CLUB = $club_id");
            if ($verify_sql) {
                $verify_result = $verify_sql->fetch_assoc();
                if ($verify_result['total'] == 0) {
                    throw new Exception('No se encontró el miembro o no tienes permisos para editarlo');
                }
                // Si el miembro existe, probablemente no había cambios reales que hacer
            }
        }
        
        $stmt->close();
        
        // Registrar en auditoría
        $auditoria = new Auditoria();
        $auditoria->registrarAccion(
            $temp->usuario_id,
            'UPDATE',
            'DIRECTIVA',
            'Actualizar miembro',
            $miembro_id,
            json_encode([
                'club_id' => $club_id,
                'nombre' => $nombre,
                'cargo' => $cargo,
                'email' => $email,
                'telefono' => $telefono,
                'estado' => $estado,
                'observaciones' => $observaciones
            ])
        );
        
        // Confirmar transacción
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Miembro actualizado correctamente';
        
    } catch (Exception $e) {
        // Revertir transacción
        $db->rollback();
        throw $e;
    }
    
    $db->close();
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    
    // Log del error
    if (isset($temp) && $temp->esta_autenticado()) {
        try {
            $auditoria = new Auditoria();
            $auditoria->registrarAccion(
                $temp->usuario_id,
                'ERROR',
                'DIRECTIVA',
                'Actualizar miembro',
                isset($miembro_id) ? $miembro_id : null,
                json_encode(['error' => $e->getMessage(), 'post_data' => $_POST])
            );
        } catch (Exception $audit_error) {
            // Error silencioso en auditoría
        }
    }
}

echo json_encode($response);
?>