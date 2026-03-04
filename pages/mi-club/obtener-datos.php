<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');
// Evitar que warnings/notice rompan el JSON
error_reporting(0);
ini_set('display_errors', 0);

$response = ['success' => false, 'message' => ''];

try {
    $temp = new Template();
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }
    if (!$temp->es_director_club()) {
        throw new Exception('Acceso denegado');
    }
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    $club_id = (int)$club['ID'];

    // Recibir ID por POST o GET
    $id = 0;
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
    } elseif (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }

    if ($id <= 0) {
        throw new Exception('ID requerido');
    }

    // Consultar datos con prepared statement y restricción por club
    $db = new Conexion();
    $stmt = $db->prepare("SELECT ID, NOMBRE, CARGO, TELEFONO, EMAIL, ESTADO, OBSERVACIONES FROM VRE_DIRECTIVA_CLUBES WHERE ID = ? AND ID_CLUB = ?");
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta');
    }
    $stmt->bind_param('ii', $id, $club_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $miembro = $result->fetch_assoc();
        $response['success'] = true;
        $response['data'] = $miembro;
        $response['message'] = 'Datos obtenidos correctamente';
    } else {
        $response['message'] = 'Miembro no encontrado';
    }

    $stmt->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
