<?php
include("../db.php");
header('Content-Type: application/json');

// Verificar que hay sesión iniciada
if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida. Debes iniciar sesión.']);
    exit();
}

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que se haya enviado la matrícula
    if (!isset($_POST['matricula']) || empty($_POST['matricula'])) {
        echo json_encode(['success' => 0, 'message' => 'Matrícula es requerida']);
        exit();
    }

    $matricula = $db->real_escape_string($_POST['matricula']);

    // Verificar que el estudiante existe
    $cad = "SELECT ID FROM VRE_ESTUDIANTES WHERE MATRICULA = '$matricula'";
    $sql = $db->query($cad);

    if ($db->rows($sql) == 0) {
        echo json_encode(['success' => 0, 'message' => 'Estudiante no encontrado']);
        exit();
    }

    // Actualizar datos
    $nombre = isset($_POST['nombre']) ? $db->real_escape_string($_POST['nombre']) : null;
    $apellido = isset($_POST['apellido']) ? $db->real_escape_string($_POST['apellido']) : null;
    $carrera = isset($_POST['carrera']) ? $db->real_escape_string($_POST['carrera']) : null;
    $semestre = isset($_POST['semestre']) ? intval($_POST['semestre']) : null;
    $email = isset($_POST['email']) ? $db->real_escape_string($_POST['email']) : null;

    $updates = [];
    if ($nombre !== null) $updates[] = "NOMBRE = '$nombre'";
    if ($apellido !== null) $updates[] = "APELLIDO = '$apellido'";
    if ($carrera !== null) $updates[] = "CARRERA = '$carrera'";
    if ($semestre !== null) $updates[] = "SEMESTRE = $semestre";
    if ($email !== null) $updates[] = "EMAIL = '$email'";

    if (empty($updates)) {
        echo json_encode(['success' => 0, 'message' => 'No hay datos para actualizar']);
        exit();
    }

    $cad = "UPDATE VRE_ESTUDIANTES SET " . implode(', ', $updates) . " WHERE MATRICULA = '$matricula'";

    if ($db->query($cad)) {
        $info['success'] = 1;
        $info['message'] = 'Datos actualizados exitosamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar: ' . $db->error;
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
