<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../../../../conexion.php';
require_once '../../../../validarToken.php';

// Validar autenticación
$token = $_POST['token'] ?? '';
if (!validarToken($token)) {
    echo json_encode(['error' => 'Token inválido o sesión expirada']);
    exit;
}

try {
    // Obtener datos del formulario
    $id_anuario = $_POST['id_anuario'] ?? null;
    $matricula = trim($_POST['matricula'] ?? '');
    $nombre_estudiante = trim($_POST['nombre_estudiante'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    $facultad = trim($_POST['facultad'] ?? '');
    $foto_url = trim($_POST['foto_url'] ?? '');
    $anio = $_POST['anio'] ?? date('Y');
    $activo = $_POST['activo'] ?? 'S';

    // Validaciones
    if (empty($matricula)) {
        echo json_encode(['error' => 'La matrícula es obligatoria']);
        exit;
    }

    if (empty($foto_url)) {
        echo json_encode(['error' => 'La URL de la foto es obligatoria']);
        exit;
    }

    // Validar que la URL sea válida
    if (!filter_var($foto_url, FILTER_VALIDATE_URL)) {
        echo json_encode(['error' => 'La URL de la foto no es válida']);
        exit;
    }

    // Verificar si ya existe una foto para este estudiante en este anuario
    $check_sql = "SELECT ID FROM VRE_ANUARIOS_FOTOS_ESTUDIANTES
                  WHERE MATRICULA = ? AND ID_ANUARIO = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('si', $matricula, $id_anuario);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['error' => 'Ya existe una foto para esta matrícula en este anuario']);
        exit;
    }

    // Insertar nueva foto
    $sql = "INSERT INTO VRE_ANUARIOS_FOTOS_ESTUDIANTES
            (ID_ANUARIO, MATRICULA, NOMBRE_ESTUDIANTE, CARRERA, FACULTAD, FOTO_URL, ANIO, ACTIVO)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssssss',
        $id_anuario,
        $matricula,
        $nombre_estudiante,
        $carrera,
        $facultad,
        $foto_url,
        $anio,
        $activo
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Foto agregada exitosamente',
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['error' => 'Error al agregar la foto']);
    }

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error al agregar foto: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
