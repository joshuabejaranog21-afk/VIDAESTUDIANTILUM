<?php
header('Content-Type: application/json');
require_once('../../API/db.php');

try {
    $db = new Conexion();
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        throw new Exception('ID requerido');
    }
    
    $query = "
        SELECT 
            l.*,
            d.NOMBRE as DEPORTE_NOMBRE
        FROM VRE_LIGAS l
        LEFT JOIN VRE_DEPORTES d ON l.ID_DEPORTE = d.ID
        WHERE l.ID = $id
    ";
    
    $result = $db->query($query);
    
    if ($db->rows($result) == 0) {
        throw new Exception('Liga no encontrada');
    }
    
    $liga = $db->recorrer($result);
    
    // Parsear galería si es JSON
    if ($liga['GALERIA']) {
        $liga['GALERIA_ARRAY'] = json_decode($liga['GALERIA'], true) ?: [];
    } else {
        $liga['GALERIA_ARRAY'] = [];
    }
    
    echo json_encode([
        'success' => 1,
        'message' => 'Liga obtenida correctamente',
        'data' => $liga
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
}
?>
