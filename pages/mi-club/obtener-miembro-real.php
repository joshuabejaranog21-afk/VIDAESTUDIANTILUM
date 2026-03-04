<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

try {
    // Incluir configuración de BD existente
    $db = new Conexion();
    
    // Obtener ID del miembro
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de miembro requerido']);
        exit;
    }

    // Consultar datos reales del miembro
    $sql = "SELECT 
                ID,
                NOMBRE,
                CARGO,
                TELEFONO,
                EMAIL,
                ESTADO,
                OBSERVACIONES
            FROM VRE_DIRECTIVA_CLUBES 
            WHERE ID = $id AND ESTADO = 'activo'";
    
    $result = $db->query($sql);
    
    if ($result && $db->rows($result) > 0) {
        $miembro = $db->recorrer($result);
        
        echo json_encode([
            'success' => true,
            'data' => $miembro,
            'message' => 'Datos obtenidos correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Miembro no encontrado',
            'debug' => [
                'id' => $id,
                'sql' => $sql
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>