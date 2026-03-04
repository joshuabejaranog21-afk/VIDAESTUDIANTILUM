<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Suprimir errores para JSON limpio
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Verificar parámetro ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de miembro requerido']);
        exit;
    }

    $id = (int)$_GET['id'];

    // Incluir configuración de BD existente
    require_once '../../API/db.php';
    
    $db = new Conexion();

    // Consultar datos reales de la BD
    $sql = "SELECT 
                ID,
                NOMBRE,
                CARGO,
                TELEFONO,
                EMAIL,
                ESTADO,
                OBSERVACIONES
            FROM VRE_DIRECTIVA_CLUBES 
            WHERE ID = $id";
    
    $result = $db->query($sql);
    
    if ($result && $db->rows($result) > 0) {
        $miembro = $db->recorrer($result);
    } else {
        $miembro = null;
    }
    
    if (!$miembro) {
        echo json_encode(['success' => false, 'message' => 'Miembro no encontrado']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $miembro
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
?>