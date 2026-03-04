<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $cad = "SELECT *FROM SYSTEM_USUARIOS WHERE NOMBRE = '".$usuario."' AND PASS = MD5('".$pass."');";
    $sql = $db->query($cad);
    $id;
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if ($db->rows($sql)>0) {
        $primer_login = 'N';
        foreach ($sql as $key) {
            $id = $key['ID'];
            $primer_login = isset($key['PRIMER_LOGIN']) ? $key['PRIMER_LOGIN'] : 'N';
        }
        $token = substr(str_shuffle($permitted_chars), 0, 40);
        $cad = "UPDATE SYSTEM_USUARIOS SET TOKEN = '$token' WHERE ID = $id";
        $sql_token = $db->query($cad);
        # Establece un código de respuesta 200 (correcto).
        $dias  = 30;
        $segundos = $dias * 24 * 60 * 60 ;
        $tiempo = time() + $segundos;
        setcookie("system_name",$usuario,$tiempo,"/");
        setcookie("system_token",$token,$tiempo,"/");
        $info['success'] = 1;
        $info['message'] = 'Iniciando sesión';
        $info['primer_login'] = $primer_login;
    }else{
        # Establece un código de respuesta 500 (error interno del servidor).
        $info['success'] = 0;
        $info['message'] = 'Error, usuario y/o contraseña incorrectos.';
    }
}else {
    # No es una solicitud POST, establezce un código de respuesta 403 (prohibido).
    $info['success'] = 0;
    $info['message'] = 'Error, usuario y/o contraseña incorrectos';
}
echo json_encode($info);
