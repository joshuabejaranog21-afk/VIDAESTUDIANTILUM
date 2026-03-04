<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Suprimir errores y warnings para evitar HTML en JSON
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    // Validar datos requeridos
    $requiredFields = ['nombre', 'cargo', 'club_id'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            echo json_encode(['success' => false, 'message' => "El campo $field es requerido"]);
            exit;
        }
    }

    $nombre = trim($_POST['nombre']);
    $cargo = trim($_POST['cargo']);
    $club_id = (int)$_POST['club_id'];
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

    // Validar formato de email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de email inválido']);
        exit;
    }

    // Configuración de BD
    $host = 'localhost';
    $dbname = 'vida_estudiantil';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar que no existe otro miembro con el mismo cargo
    $stmt = $pdo->prepare("
        SELECT ID 
        FROM VRE_DIRECTIVA_CLUBES 
        WHERE CARGO = ? AND ID_CLUB = ? AND ESTADO = 'activo'
    ");
    $stmt->execute([$cargo, $club_id]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un miembro con este cargo']);
        exit;
    }

    // Insertar miembro
    $stmt = $pdo->prepare("
        INSERT INTO VRE_DIRECTIVA_CLUBES 
        (NOMBRE, CARGO, TELEFONO, EMAIL, ESTADO, OBSERVACIONES, ID_CLUB, FECHA_REGISTRO) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $resultado = $stmt->execute([
        $nombre, 
        $cargo, 
        $telefono, 
        $email, 
        $estado, 
        $observaciones, 
        $club_id
    ]);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Miembro agregado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar miembro']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
}
?>