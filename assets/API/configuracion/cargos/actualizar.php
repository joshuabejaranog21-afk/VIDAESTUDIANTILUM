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

$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$tipo = $_POST['tipo'] ?? 'GENERAL';
$orden = intval($_POST['orden'] ?? 0);
$activo = $_POST['activo'] ?? 'S';

// Validaciones
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit();
}

if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
    exit();
}

if (!in_array($tipo, ['GENERAL', 'FEDERACION', 'PULSO'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit();
}

// Verificar si ya existe otro cargo con el mismo nombre
$check = "SELECT ID FROM vre_cargos WHERE NOMBRE = '" . $db->real_escape_string($nombre) . "' AND ID != $id";
if ($db->rows($db->query($check)) > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya existe otro cargo con ese nombre']);
    exit();
}

// Actualizar
$sql = "UPDATE vre_cargos SET
        NOMBRE = '" . $db->real_escape_string($nombre) . "',
        DESCRIPCION = '" . $db->real_escape_string($descripcion) . "',
        TIPO = '" . $db->real_escape_string($tipo) . "',
        ORDEN = $orden,
        ACTIVO = '" . $db->real_escape_string($activo) . "'
        WHERE ID = $id";

if ($db->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Cargo actualizado exitosamente'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el cargo: ' . $db->error]);
}
?>
