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

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID requerido']);
    exit();
}

// Verificar si el cargo está en uso
$checkFederacion = "SELECT COUNT(*) as total FROM vre_federacion_miembros WHERE ID_CARGO = $id";
$resultFed = $db->query($checkFederacion);
$rowFed = $db->recorrer($resultFed);

$checkPulso = "SELECT COUNT(*) as total FROM vre_pulso_equipos WHERE ID_CARGO = $id";
$resultPulso = $db->query($checkPulso);
$rowPulso = $db->recorrer($resultPulso);

$totalUsos = $rowFed['total'] + $rowPulso['total'];

if ($totalUsos > 0) {
    echo json_encode([
        'success' => false,
        'message' => "Este cargo está siendo utilizado por $totalUsos colaborador(es). No se puede eliminar. Considera desactivarlo en su lugar."
    ]);
    exit();
}

// Eliminar
$sql = "DELETE FROM vre_cargos WHERE ID = $id";

if ($db->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Cargo eliminado exitosamente'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el cargo: ' . $db->error]);
}
?>
