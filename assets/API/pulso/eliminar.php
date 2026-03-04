<?php
session_start();
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        exit();
    }

    // Obtener foto para eliminarla
    $cadFoto = "SELECT FOTO_URL FROM vre_pulso_equipos WHERE ID = $id";
    $sqlFoto = $db->query($cadFoto);
    if ($db->rows($sqlFoto) > 0) {
        $rowFoto = $db->recorrer($sqlFoto);
        $fotoUrl = $rowFoto['FOTO_URL'];

        // Eliminar el registro
        $cad = "DELETE FROM vre_pulso_equipos WHERE ID = $id";
        if ($db->query($cad)) {
            // Eliminar archivo de foto si existe
            if ($fotoUrl && file_exists("../../../" . ltrim($fotoUrl, "/"))) {
                unlink("../../../" . ltrim($fotoUrl, "/"));
            }

            echo json_encode([
                'success' => true,
                'message' => 'Colaborador eliminado exitosamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $db->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Colaborador no encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
