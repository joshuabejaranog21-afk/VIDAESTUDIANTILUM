<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Suprimir errores y warnings para evitar HTML en JSON
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Verificar método
    if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Obtener datos
    $data = null;
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = file_get_contents('php://input');
        parse_str($input, $data);
        if (empty($data)) {
            $data = json_decode($input, true);
        }
    } else {
        $data = $_POST;
    }

    // Validar datos requeridos
    $requiredFields = ['id', 'nombre', 'cargo'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            echo json_encode(['success' => false, 'message' => "El campo $field es requerido"]);
            exit;
        }
    }

    $id = (int)$data['id'];
    $nombre = trim($data['nombre']);
    $cargo = trim($data['cargo']);
    $telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $estado = isset($data['estado']) ? trim($data['estado']) : 'activo';
    $observaciones = isset($data['observaciones']) ? trim($data['observaciones']) : '';

    // Validar formato de email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de email inválido']);
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
    $stmt = $pdo->prepare("SELECT ID_CLUB FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ESTADO = 'activo'");
    $stmt->execute([$id]);
    $miembro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$miembro) {
        echo json_encode(['success' => false, 'message' => 'Miembro no encontrado']);
        exit;
    }

    // Verificar que no existe otro miembro con el mismo cargo (excluyendo el actual)
    $stmt = $pdo->prepare("
        SELECT ID 
        FROM VRE_DIRECTIVA_CLUBES 
        WHERE CARGO = ? AND ID_CLUB = ? AND ESTADO = 'activo' AND ID != ?
    ");
    $stmt->execute([$cargo, $miembro['ID_CLUB'], $id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un miembro con este cargo']);
        exit;
    }

    // Actualizar miembro
    $stmt = $pdo->prepare("
        UPDATE VRE_DIRECTIVA_CLUBES 
        SET NOMBRE = ?, 
            CARGO = ?, 
            TELEFONO = ?, 
            EMAIL = ?, 
            ESTADO = ?,
            OBSERVACIONES = ?
        WHERE ID = ?
    ");
    
    $resultado = $stmt->execute([
        $nombre, 
        $cargo, 
        $telefono, 
        $email, 
        $estado,
        $observaciones, 
        $id
    ]);
    
    if ($resultado && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Miembro actualizado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar miembro']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>