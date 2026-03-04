<?php
header('Content-Type: application/json');
session_start();

require_once '../../php/template.php';

$temp = new Template();
$db = new Conexion();
$response = ['success' => false, 'message' => '', 'data' => null];

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
    if (empty($_GET['id'])) {
        throw new Exception('ID del miembro es requerido');
    }
    
    $miembro_id = intval($_GET['id']);
    
    // Usar conexión existente
    $db = $db->query ? $db : $db->obtenerConexion();
    
    // Consultar el miembro específico, verificando que pertenezca al club del director
    $sql = "SELECT 
                ID,
                NOMBRE,
                CARGO,
                EMAIL,
                TELEFONO,
                ESTADO,
                OBSERVACIONES,
                DATE_FORMAT(FECHA_REGISTRO, '%d/%m/%Y') as FECHA_REGISTRO_FORMATTED
            FROM VRE_DIRECTIVA_CLUBES 
            WHERE ID = ? AND ID_CLUB = ?";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $db->error);
    }
    
    $stmt->bind_param("ii", $miembro_id, $club_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $miembro = $result->fetch_assoc();
    
    if (!$miembro) {
        throw new Exception('Miembro no encontrado o no tienes permisos para acceder a él');
    }
    
    $stmt->close();
    $db->close();
    
    $response['success'] = true;
    $response['data'] = $miembro;
    $response['message'] = 'Miembro cargado correctamente';
    
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
                'Obtener miembro',
                isset($miembro_id) ? $miembro_id : null,
                json_encode(['error' => $e->getMessage(), 'get_data' => $_GET])
            );
        } catch (Exception $audit_error) {
            // Error silencioso en auditoría
        }
    }
}

echo json_encode($response);
?>