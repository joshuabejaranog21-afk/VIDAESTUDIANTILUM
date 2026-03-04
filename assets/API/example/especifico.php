<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && security()) {
    $id = $_GET['id'];
    $cad = "SELECT * FROM EXAMPLE WHERE ID = $id";
    $sql =$db->query($cad);
    $rows = $db->rows($sql);
    if ($rows > 0) {
        $info['success'] = 1;
        $info['message'] = "$rows registro(s) encontrado(s)";
        foreach ($sql as $key) {
            $info['data'] = $key;
        }
    } else {
        $info['success'] = 0; 
        $info['message'] = 'No se encontraron registros';
        if ($db->mostrarErrores) {
            $info['question'] = $cad;
        }
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Metodo de acceso incorrecto';
}
echo json_encode($info);
$db = null;
?>
