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
if (!$temp->tiene_permiso('clubes', 'eliminar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para eliminar clubes';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'ID del club no proporcionado';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    try {
        // Obtener nombre del club antes de eliminar para auditoría
        $cadConsulta = "SELECT NOMBRE FROM VRE_CLUBES WHERE ID = $id";
        $sqlConsulta = $db->query($cadConsulta);
        $nombre_club = '';

        if ($db->rows($sqlConsulta) > 0) {
            $row = $sqlConsulta->fetch_assoc();
            $nombre_club = $row['NOMBRE'];
        } else {
            $info['success'] = 0;
            $info['message'] = 'Club no encontrado';
            echo json_encode($info);
            exit();
        }

        // Iniciar transacción
        $db->begin_transaction();

        // 1. Limpiar asignación de club de los usuarios directores
        $db->query("UPDATE SYSTEM_USUARIOS SET ID_CLUB_ASIGNADO = NULL WHERE ID_CLUB_ASIGNADO = $id");

        // 2. Eliminar imágenes de VRE_GALERIA
        $db->query("DELETE FROM VRE_GALERIA WHERE MODULO = 'clubes' AND ID_REGISTRO = $id");

        // 3. Eliminar club (cascada elimina también la directiva)
        $cad = "DELETE FROM VRE_CLUBES WHERE ID = $id";
        $sql = $db->query($cad);

        if ($sql) {
            $db->commit();

            // Registrar en auditoría
            $temp->registrar_auditoria('CLUBES', 'ELIMINAR', "Club eliminado: $nombre_club (ID: $id)");

            $info['success'] = 1;
            $info['message'] = 'Club eliminado exitosamente';
        } else {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = 'Error al eliminar el club';
            if ($db->mostrarErrores) {
                $info['error'] = $db->error;
            }
        }

    } catch (Exception $e) {
        $info['success'] = 0;
        $info['message'] = 'Error al eliminar el club';
        if ($db->mostrarErrores) {
            $info['error'] = $e->getMessage();
        }
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>