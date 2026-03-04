<?php
header('Content-Type: application/json');
include('../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();
$id_anuario = isset($_GET['id_anuario']) ? intval($_GET['id_anuario']) : 0;
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : 0;

// TODO: Get matricula from user session
// Por ahora usamos una matrícula de ejemplo
// En producción, deberás obtener la matrícula del usuario logueado desde la sesión
$matricula = isset($_GET['matricula']) ? $db->real_escape_string($_GET['matricula']) : '';

if (empty($matricula)) {
    // Try to get from user session - you'll need to implement this based on your authentication system
    // For now, return empty
    echo json_encode(['success' => false, 'message' => 'Matrícula no encontrada', 'data' => []]);
    exit();
}

$query = "SELECT * FROM VRE_ANUARIOS_FOTOS_ESTUDIANTES
          WHERE ACTIVO = 'S'
          AND MATRICULA = '$matricula'";

if ($id_anuario > 0) {
    $query .= " AND ID_ANUARIO = $id_anuario";
}

if ($anio > 0) {
    $query .= " AND ANIO = $anio";
}

$query .= " ORDER BY ANIO DESC";

$result = $db->query($query);

if ($result && $db->rows($result) > 0) {
    $fotos = [];
    while ($row = $result->fetch_assoc()) {
        $fotos[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $fotos]);
} else {
    echo json_encode(['success' => false, 'data' => []]);
}
?>
