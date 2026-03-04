<?php
session_start();
include("../../db.php");
header('Content-Type: application/json');

// Solo admins
if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$tipo = $_POST['tipo'] ?? 'GENERAL';
$orden = intval($_POST['orden'] ?? 0);
$activo = $_POST['activo'] ?? 'S';

// Validaciones
if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
    exit();
}

if (!in_array($tipo, ['GENERAL', 'FEDERACION', 'PULSO'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit();
}

// Verificar si ya existe
$check = "SELECT ID FROM vre_cargos WHERE NOMBRE = '" . $db->real_escape_string($nombre) . "'";
if ($db->rows($db->query($check)) > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya existe un cargo con ese nombre']);
    exit();
}

// Insertar
$sql = "INSERT INTO vre_cargos (NOMBRE, DESCRIPCION, TIPO, ORDEN, ACTIVO)
        VALUES (
            '" . $db->real_escape_string($nombre) . "',
            '" . $db->real_escape_string($descripcion) . "',
            '" . $db->real_escape_string($tipo) . "',
            $orden,
            '" . $db->real_escape_string($activo) . "'
        )";

if ($db->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Cargo creado exitosamente',
        'id' => $db->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear el cargo: ' . $db->error]);
}
?>
