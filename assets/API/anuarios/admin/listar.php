<?php
header('Content-Type: application/json');
include('../../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

$query = "SELECT * FROM VRE_ANUARIOS ORDER BY ANIO DESC";
$result = $db->query($query);

if ($result && $db->rows($result) > 0) {
    $anuarios = [];
    while ($row = $result->fetch_assoc()) {
        $anuarios[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $anuarios]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron anuarios']);
}
?>
