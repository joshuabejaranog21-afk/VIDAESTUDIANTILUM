<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && security()) {
    $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
    $correo = $_POST['correo'];
    $observaciones = mysqli_real_escape_string($db, $_POST['observaciones']);
    
    $cad = "INSERT INTO EXAMPLE( NOMBRE, CORREO, OBSERVACIONES ) VALUES ( '$nombre', '$correo', '$observaciones' );";
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
