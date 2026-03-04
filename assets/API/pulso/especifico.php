<?php
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && security()) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        $info['success'] = 0;
        $info['message'] = 'ID inválido';
    } else {
        $cad = "SELECT * FROM VRE_PULSO_EQUIPOS WHERE ID = $id";
        $sql = $db->query($cad);
        $rows = $db->rows($sql);

        if ($rows > 0) {
            $info['success'] = 1;
            $info['data'] = $sql->fetch_assoc();
        } else {
            $info['success'] = 0;
            $info['message'] = 'Colaborador no encontrado';
        }
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método de acceso incorrecto';
}

echo json_encode($info);
$db = null;
?>
