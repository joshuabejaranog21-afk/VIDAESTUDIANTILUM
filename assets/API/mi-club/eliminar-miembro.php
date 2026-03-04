<?php
header('Content-Type: application/json');
session_start();

require_once '../../php/template.php';

$temp = new Template();
$db = new Conexion();
$response = ['success' => false, 'message' => ''];

try {
    
    // Verificar sesión y permisos
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }
    
    if (!$temp->es_director_club()) {
        throw new Exception('Acceso denegado');
    }
    
    // Obtener el club asignado
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    
    $club_id = intval($club['ID']);
    
    // Validar ID del miembro
    if (empty($_POST['id'])) {
        throw new Exception('ID del miembro es requerido');
    }
    
    $miembro_id = intval($_POST['id']);
    
    // Obtener conexión a la base de datos
    $conexion = new Conexion();
    $db = $conexion->obtenerConexion();
    
    // Verificar que el miembro existe y pertenece al club, y obtener sus datos para auditoría
    $check_sql = "SELECT NOMBRE, CARGO FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ID_CLUB = ?";
    $check_stmt = $db->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception('Error al preparar consulta de verificación: ' . $db->error);
    }
    
    $check_stmt->bind_param("ii", $miembro_id, $club_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $miembro_data = $check_result->fetch_assoc();
    
    if (!$miembro_data) {
        throw new Exception('Miembro no encontrado o no tienes permisos para eliminarlo');
    }
    $check_stmt->close();
    
    // Iniciar transacción
    $db->autocommit(false);
    
    try {
        // Eliminar miembro
        $sql = "DELETE FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ID_CLUB = ?";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $db->error);
        }
        
        $stmt->bind_param("ii", $miembro_id, $club_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar el miembro: ' . $stmt->error);
        }
        
        if ($db->affected_rows === 0) {
            throw new Exception('No se pudo eliminar el miembro. Verifique que existe y que tiene permisos');
        }
        
        $stmt->close();
        
        // Registrar en auditoría
        $auditoria = new Auditoria();
        $auditoria->registrarAccion(
            $temp->usuario_id,
            'DELETE',
            'DIRECTIVA',
            'Eliminar miembro',
            $miembro_id,
            json_encode([
                'club_id' => $club_id,
                'nombre_eliminado' => $miembro_data['NOMBRE'],
                'cargo_eliminado' => $miembro_data['CARGO']
            ])
        );
        
        // Confirmar transacción
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Miembro eliminado correctamente';
        
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
                'Eliminar miembro',
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