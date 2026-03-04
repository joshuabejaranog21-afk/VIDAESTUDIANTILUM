<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $pass = $_POST['pass'];
    $id_cat = $_POST['id_cat'];
    
    
    $cad = "INSERT INTO SYSTEM_USUARIOS( NOMBRE, PASS, ID_CAT, ACTIVO) VALUES ('$nombre', MD5('$pass'), '$id_cat', 'S');";
    $sql =$db->query($cad);
    if ($sql) {
        $info['success'] = 1; 
        $info['message'] = 'Registro creado';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al insertar registro, revise los datos proporcionados.';
        if ($db->mostrarErrores) {
            $info['question'] = $cad;
        }
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Error de envío de información.';
}
echo json_encode($info);
?>
