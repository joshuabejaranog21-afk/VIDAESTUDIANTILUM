<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && security()) {
    $id = $_POST['id'];
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $correo = $_POST['correo'];
    $observaciones = mysqli_real_escape_string($db, $_POST['observaciones']);

    $cad = "UPDATE EXAMPLE SET NOMBRE = '$nombre', CORREO = '$correo', OBSERVACIONES = '$observaciones' WHERE ID = $id";
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
