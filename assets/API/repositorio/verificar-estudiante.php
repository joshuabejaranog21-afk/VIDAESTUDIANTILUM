<?php
/**
 * API: Verificar si un estudiante existe por matrícula
 * Retorna los datos del estudiante si existe
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Incluir configuración
require_once __DIR__ . '/../../config.php';

// Validar sesión - permitir tanto admins como sin sesión para el formulario de upload
$is_admin = security();
$allowed = $is_admin || true; // Permitir acceso para verificar estudiantes en el formulario

if (!$allowed) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    // Obtener matrícula del request
    $matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';

    if (empty($matricula)) {
        echo json_encode([
            'success' => false,
            'message' => 'Matrícula requerida'
        ]);
        exit;
    }

    // Validar formato de matrícula (7 dígitos)
    if (!preg_match('/^\d{7}$/', $matricula)) {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de matrícula inválido (debe ser 7 dígitos)'
        ]);
        exit;
    }

    // Buscar estudiante en la base de datos
    $stmt = $conn->prepare("
        SELECT
            ID,
            MATRICULA,
            NOMBRE,
            APELLIDO,
            CARRERA,
            SEMESTRE,
            EMAIL
        FROM VRE_ESTUDIANTES
        WHERE MATRICULA = ? AND ACTIVO = 'S'
    ");

    $stmt->bind_param('s', $matricula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Estudiante existe
        echo json_encode([
            'success' => true,
            'exists' => true,
            'student' => [
                'id' => $row['ID'],
                'matricula' => $row['MATRICULA'],
                'nombre' => $row['NOMBRE'],
                'apellido' => $row['APELLIDO'],
                'carrera' => $row['CARRERA'],
                'semestre' => $row['SEMESTRE'],
                'email' => $row['EMAIL']
            ]
        ]);
    } else {
        // Estudiante no existe
        echo json_encode([
            'success' => true,
            'exists' => false,
            'message' => 'Estudiante nuevo - por favor complete todos los campos'
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar estudiante: ' . $e->getMessage()
    ]);
}

$conn->close();
