<?php
header('Content-Type: application/json');

try {
    // Incluir la configuración de BD existente
    require_once '../../API/db.php';
    
    $db = new Conexion();
    
    // Probar consulta simple
    $result = $db->query("SELECT 1 as test");
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Conexión exitosa', 'time' => date('Y-m-d H:i:s')]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en consulta']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>