<?php
header('Content-Type: application/json');
include('../../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

$id = intval($_POST['id']);
$activo = $_POST['activo'] === 'S' ? 'S' : 'N';

if ($id === 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$query = "UPDATE VRE_ANUARIOS SET ACTIVO = '$activo' WHERE ID = $id";

if ($db->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado: ' . $db->error]);
}
?>
