<?php
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && security()) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? intval($data['id']) : 0;

    if ($id <= 0) {
        $info['success'] = 0;
        $info['message'] = 'ID inválido';
    } else {
        // Marcar como inactivo en lugar de eliminar
        $cad = "UPDATE VRE_PULSO_EQUIPOS SET ACTIVO = 'N' WHERE ID = $id";

        if ($db->query($cad)) {
            $info['success'] = 1;
            $info['message'] = 'Colaborador eliminado exitosamente';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al eliminar colaborador';
            if ($db->mostrarErrores) {
                $info['error'] = $db->error;
            }
        }
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método de acceso incorrecto';
}

echo json_encode($info);
$db = null;
?>
