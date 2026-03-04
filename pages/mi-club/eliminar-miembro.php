<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $temp = new Template();

    // Validar sesión y permisos
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }

    if (!$temp->es_director_club()) {
        throw new Exception('Acceso denegado');
    }

    // Obtener club asignado
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    $club_id = (int)$club['ID'];

    // Validar ID recibido
    if (!isset($_POST['id'])) {
        throw new Exception('ID del miembro es requerido');
    }
    $miembro_id = (int)$_POST['id'];
    if ($miembro_id <= 0) {
        throw new Exception('ID inválido');
    }

    // Conexión BD
    $db = new Conexion();

    // Soft delete: marcar como inactivo (según ENUM de la tabla), restringiendo por club
    $stmt = $db->prepare("UPDATE VRE_DIRECTIVA_CLUBES SET ESTADO = 'inactivo' WHERE ID = ? AND ID_CLUB = ?");
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta');
    }
    $stmt->bind_param('ii', $miembro_id, $club_id);
    $ok = $stmt->execute();

    if (!$ok) {
        throw new Exception('Error al ejecutar la eliminación');
    }

    if ($db->affected_rows <= 0) {
        // No afectó filas: puede no existir, pertenecer a otro club o ya estar eliminado
        throw new Exception('No se pudo eliminar el miembro');
    }

    $stmt->close();

    // Auditoría
    $temp->registrar_auditoria('DIRECTIVA', 'ELIMINAR', 'Miembro de directiva eliminado. ID: ' . $miembro_id . ' (club ' . $club_id . ')');

    $response['success'] = true;
    $response['message'] = 'Miembro eliminado correctamente';

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
