<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');

require_once '../../../../conexion.php';
require_once '../../../db.php';

// Validar autenticación (solo administradores)
if (!security()) {
    echo json_encode(['error' => 'No tienes permisos para realizar esta acción. Debes iniciar sesión como administrador.']);
    exit;
}

try {
    // Obtener datos del formulario
    $id = $_POST['id'] ?? null;
    $id_anuario = $_POST['id_anuario'] ?? null;
    $matricula = trim($_POST['matricula'] ?? '');
    $nombre_estudiante = trim($_POST['nombre_estudiante'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    $facultad = trim($_POST['facultad'] ?? '');
    $foto_url = trim($_POST['foto_url'] ?? '');
    $anio = $_POST['anio'] ?? date('Y');
    $activo = $_POST['activo'] ?? 'S';

    // Validaciones
    if (empty($id)) {
        echo json_encode(['error' => 'El ID de la foto es obligatorio']);
        exit;
    }

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

    // Actualizar foto
    $sql = "UPDATE VRE_ANUARIOS_FOTOS_ESTUDIANTES SET
            ID_ANUARIO = ?,
            MATRICULA = ?,
            NOMBRE_ESTUDIANTE = ?,
            CARRERA = ?,
            FACULTAD = ?,
            FOTO_URL = ?,
            ANIO = ?,
            ACTIVO = ?
            WHERE ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'isssssssi',
        $id_anuario,
        $matricula,
        $nombre_estudiante,
        $carrera,
        $facultad,
        $foto_url,
        $anio,
        $activo,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Foto actualizada exitosamente'
        ]);
    } else {
        echo json_encode(['error' => 'Error al actualizar la foto']);
    }

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Error al actualizar foto: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
