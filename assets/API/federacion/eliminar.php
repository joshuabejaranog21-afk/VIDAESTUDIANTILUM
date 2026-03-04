<?php
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => 0, 'message' => 'ID requerido']);
        exit();
    }

    $id = intval($_POST['id']);

    // Obtener foto para eliminarla
    $cadFoto = "SELECT FOTO_URL FROM VRE_FEDERACION_MIEMBROS WHERE ID = $id";
    $sqlFoto = $db->query($cadFoto);
    if ($db->rows($sqlFoto) > 0) {
        $rowFoto = $db->recorrer($sqlFoto);
        $fotoUrl = $rowFoto['FOTO_URL'];

        // Eliminar de la base de datos
        $cad = "DELETE FROM VRE_FEDERACION_MIEMBROS WHERE ID = $id";
        if ($db->query($cad)) {
            // Eliminar archivo físico
            if ($fotoUrl && file_exists("../../../" . ltrim($fotoUrl, "/"))) {
                unlink("../../../" . ltrim($fotoUrl, "/"));
            }

            $info['success'] = 1;
            $info['message'] = 'Miembro eliminado exitosamente';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al eliminar miembro: ' . $db->error;
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'Miembro no encontrado';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
