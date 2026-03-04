<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $pass_ant = $_POST['pass_ant'];
    $pass = $_POST['pass'];

    // Verificar que la contraseña actual sea correcta
    $cad = "SELECT * FROM SYSTEM_USUARIOS
            WHERE NOMBRE = '$nombre'
            AND PASS = MD5('$pass_ant')
            AND ACTIVO = 'S'";

    $sql = $db->query($cad);

    if ($db->rows($sql) > 0) {
        // Actualizar contraseña y marcar que ya no es primer login
        $cad = "UPDATE SYSTEM_USUARIOS
                SET PASS = MD5('$pass'),
                    PRIMER_LOGIN = 'N'
                WHERE NOMBRE = '$nombre'";

        $sql_update = $db->query($cad);

        if ($sql_update) {
            $info['success'] = 1;
            $info['message'] = 'Contraseña actualizada exitosamente';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al actualizar la contraseña';
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'La contraseña temporal es incorrecta';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
