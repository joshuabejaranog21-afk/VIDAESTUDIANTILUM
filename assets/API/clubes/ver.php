<?php
include("../../php/template.php");

header('Content-Type: application/json');
$temp = new Template();
$db = new Conexion();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('clubes', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver clubes';
    echo json_encode($info);
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $info['success'] = 0;
    $info['message'] = 'ID de club no proporcionado';
    echo json_encode($info);
    exit();
}

$id = intval($_GET['id']);

try {
    $cad = "SELECT * FROM VRE_CLUBES WHERE ID = $id";
    $sql = $db->query($cad);

    if ($db->rows($sql) > 0) {
        $club = $sql->fetch_assoc();

        // Decodificar JSON fields
        $club['GALERIA'] = $club['GALERIA'] ? json_decode($club['GALERIA']) : [];
        $club['REDES_SOCIALES'] = $club['REDES_SOCIALES'] ? json_decode($club['REDES_SOCIALES']) : null;

        $info['success'] = 1;
        $info['data'] = $club;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Club no encontrado';
    }

} catch (Exception $e) {
    $info['success'] = 0;
    $info['message'] = 'Error al obtener club';
    if ($db->mostrarErrores) {
        $info['error'] = $e->getMessage();
    }
}

echo json_encode($info);
?>