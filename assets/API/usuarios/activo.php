<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $activo = $_POST['activo'];

    $cad = "UPDATE SYSTEM_USUARIOS SET ACTIVO = '$activo' WHERE ID = $id";
    $sql =$db->query($cad);
    if ($sql) {
        $info['success'] = 1; 
        $info['message'] = 'Registro actualizado';
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
