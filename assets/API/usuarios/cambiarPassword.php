<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $pass_ant = $_POST['pass_ant'];
    $pass = $_POST['pass'];

    $cad = "SELECT *FROM SYSTEM_USUARIOS WHERE NOMBRE = '$nombre' AND PASS = MD5('$pass_ant')";
    $read = $db->query($cad);
    if ($db->rows($read) > 0) {
        foreach ($read as $key) {
            $id = $key['ID'];
        }
        $cad = "UPDATE SYSTEM_USUARIOS SET PASS = MD5('$pass') WHERE ID = $id";
        $sql = $db->query($cad);
        if ($sql) {
            $info['success'] = 1;
            $info['message'] = 'Contraseña actualizada';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al actualizar';
            if ($db->mostrarErrores) {
                $info['question'] = $cad;
            }
        }
    } else {
        $info['success'] = 2;
        $info['message'] = 'Tu contraseña anterior no es correcta.';
        $info['cad'] = $cad;

    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Error de envío de información';
}
echo json_encode($info);
