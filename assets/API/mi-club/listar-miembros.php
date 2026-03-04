<?php
header('Content-Type: application/json');
session_start();

require_once '../../functions/Conexion.php';
require_once '../../functions/Template.php';
require_once '../../functions/Auditoria.php';

$response = ['success' => false, 'message' => '', 'data' => []];

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
    
    // Obtener conexión a la base de datos
    $conexion = new Conexion();
    $db = $conexion->obtenerConexion();
    
    // Consultar miembros de la directiva del club
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
            WHERE ID_CLUB = ? 
            ORDER BY 
                CASE CARGO
                    WHEN 'Presidente' THEN 1
                    WHEN 'Vicepresidente' THEN 2
                    WHEN 'Secretario' THEN 3
                    WHEN 'Tesorero' THEN 4
                    WHEN 'Coordinador' THEN 5
                    WHEN 'Vocal' THEN 6
                    WHEN 'Delegado' THEN 7
                    ELSE 8
                END,
                NOMBRE ASC";
    
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $db->error);
    }
    
    $stmt->bind_param("i", $club_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $miembros = [];
    
    while ($row = $result->fetch_assoc()) {
        $miembros[] = $row;
    }
    
    $stmt->close();
    $db->close();
    
    $response['success'] = true;
    $response['data'] = $miembros;
    $response['message'] = 'Miembros cargados correctamente';
    
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
                'Listar miembros',
                null,
                json_encode(['error' => $e->getMessage()])
            );
        } catch (Exception $audit_error) {
            // Error silencioso en auditoría
        }
    }
}

echo json_encode($response);
?>