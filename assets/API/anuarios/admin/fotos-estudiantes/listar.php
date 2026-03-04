<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../../../../conexion.php';
require_once '../../../../validarToken.php';

// Validar autenticación
$token = $_GET['token'] ?? '';
if (!validarToken($token)) {
    echo json_encode(['error' => 'Token inválido o sesión expirada']);
    exit;
}

try {
    $id_anuario = $_GET['id_anuario'] ?? null;

    // Construir query base
    $sql = "SELECT
                f.ID,
                f.ID_ANUARIO,
                f.MATRICULA,
                f.NOMBRE_ESTUDIANTE,
                f.CARRERA,
                f.FACULTAD,
                f.FOTO_URL,
                f.ANIO,
                f.ACTIVO,
                a.TITULO as NOMBRE_ANUARIO
            FROM VRE_ANUARIOS_FOTOS_ESTUDIANTES f
            LEFT JOIN VRE_ANUARIOS a ON f.ID_ANUARIO = a.ID";

    // Filtrar por anuario si se especifica
    if ($id_anuario) {
        $sql .= " WHERE f.ID_ANUARIO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id_anuario);
    } else {
        $sql .= " ORDER BY f.ID_ANUARIO DESC, f.NOMBRE_ESTUDIANTE ASC";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $fotos = [];
    while ($row = $result->fetch_assoc()) {
        $fotos[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $fotos,
        'total' => count($fotos)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error al obtener fotos: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
