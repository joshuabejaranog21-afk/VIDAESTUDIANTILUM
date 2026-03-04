<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Suprimir errores y warnings para evitar HTML en JSON
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Verificar método
    if (!in_array($_SERVER['REQUEST_METHOD'], ['DELETE', 'POST'])) {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Obtener ID del miembro
    $id = null;
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : null;
    } else {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    }

    if (!$id || !is_numeric($id)) {
        echo json_encode(['success' => false, 'message' => 'ID de miembro requerido']);
        exit;
    }

    // Configuración de BD (ajustar según tu configuración)
    $host = 'localhost';
    $dbname = 'vida_estudiantil';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar que el miembro existe
    $stmt = $pdo->prepare("SELECT NOMBRE FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ESTADO = 'activo'");
    $stmt->execute([$id]);
    $miembro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$miembro) {
        echo json_encode(['success' => false, 'message' => 'Miembro no encontrado']);
        exit;
    }

    // Eliminar miembro (soft delete)
    $stmt = $pdo->prepare("
        UPDATE VRE_DIRECTIVA_CLUBES 
        SET ESTADO = 'eliminado' 
        WHERE ID = ?
    ");
    
    $resultado = $stmt->execute([$id]);
    
    if ($resultado && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Miembro eliminado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar miembro']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>