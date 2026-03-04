<?php
header('Content-Type: application/json');
include('../db.php');

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

$query = "SELECT * FROM VRE_ANUARIOS WHERE ID = $id AND ACTIVO = 'S'";
$result = $db->query($query);

if ($result && $db->rows($result) > 0) {
    $anuario = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $anuario]);
} else {
    echo json_encode(['success' => false, 'message' => 'Anuario no encontrado']);
}
?>
