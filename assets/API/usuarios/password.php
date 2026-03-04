<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $pass = $_POST['pass'];

    $cad = "UPDATE SYSTEM_USUARIOS SET PASS = MD5('$pass') WHERE ID = $id";
    $sql =$db->query($cad);
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
    $info['success'] = 0;
    $info['message'] = 'Error de envío de información';
}
echo json_encode($info);
?>
