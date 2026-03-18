<?php
header('Content-Type: application/json');
include('../../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

// Hard delete
$query = "DELETE FROM VRE_ANUARIOS WHERE ID = $id";

if ($db->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Anuario eliminado permanentemente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el anuario: ' . $db->error]);
}
?>
