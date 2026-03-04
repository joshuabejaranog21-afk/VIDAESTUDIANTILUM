<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');

require_once '../../../../conexion.php';
require_once '../../../../validarToken.php';

// Validar autenticación
$token = $_POST['token'] ?? $_GET['token'] ?? '';
if (!validarToken($token)) {
    echo json_encode(['error' => 'Token inválido o sesión expirada']);
    exit;
}

try {
    $id = $_POST['id'] ?? $_GET['id'] ?? null;

    // Validaciones
    if (empty($id)) {
        echo json_encode(['error' => 'El ID de la foto es obligatorio']);
        exit;
    }

    // Verificar que el registro existe
    $check_sql = "SELECT ID FROM VRE_ANUARIOS_FOTOS_ESTUDIANTES WHERE ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['error' => 'La foto no existe']);
        exit;
    }

    // Eliminar foto
    $sql = "DELETE FROM VRE_ANUARIOS_FOTOS_ESTUDIANTES WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Foto eliminada exitosamente'
        ]);
    } else {
        echo json_encode(['error' => 'Error al eliminar la foto']);
    }

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error al eliminar foto: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
