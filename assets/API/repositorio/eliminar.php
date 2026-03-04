<?php
include("../db.php");
header('Content-Type: application/json');

// Verificar que hay sesión iniciada
if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida. Debes iniciar sesión.']);
    exit();
}

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que se haya enviado el ID de la foto
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => 0, 'message' => 'ID de foto es requerido']);
        exit();
    }

    $id = intval($_POST['id']);

    // Obtener información de la foto antes de eliminar
    $cad = "SELECT * FROM VRE_REPOSITORIO_FOTOS WHERE ID = $id";
    $sql = $db->query($cad);

    if ($db->rows($sql) == 0) {
        echo json_encode(['success' => 0, 'message' => 'Foto no encontrada']);
        exit();
    }

    $foto = $db->recorrer($sql);

    // Opcional: Verificar que el usuario que elimina sea el dueño de la foto
    // (Comentado por ahora ya que usamos sesión de admin)
    /*
    if (isset($_POST['matricula'])) {
        $matricula = $db->real_escape_string($_POST['matricula']);
        if ($foto['MATRICULA'] != $matricula) {
            echo json_encode(['success' => 0, 'message' => 'No tienes permiso para eliminar esta foto']);
            exit();
        }
    }
    */

    // Eliminar archivo físico
    $rutaArchivo = "../../../" . $foto['FOTO_URL'];
    if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }

    // Eliminar registro de base de datos (esto también eliminará las referencias por CASCADE)
    $cad = "DELETE FROM VRE_REPOSITORIO_FOTOS WHERE ID = $id";

    if ($db->query($cad)) {
        $info['success'] = 1;
        $info['message'] = 'Fotografía eliminada exitosamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al eliminar la fotografía: ' . $db->error;
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
