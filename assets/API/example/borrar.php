<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && security()) {
    $id = $_GET['id'];
    $cad = "DELETE FROM EXAMPLE WHERE ID = $id";
    $sql =$db->query($cad);
    if ($sql) {
        $info['success'] = 1; 
        $info['message'] = 'Registro borrado con exito.';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al intentar borrar, intente nuevamente.';
        if ($db->mostrarErrores) {
            $info['question'] = $cad;
        }
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Error de envío de información.';
}
echo json_encode($info);
$db = null;
?>
