<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../db.php");
header('Content-Type: application/json');

if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida']);
    exit();
}

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $titulo = $db->real_escape_string($_POST['titulo']);
    $contenido_que_es = $db->real_escape_string($_POST['contenido_que_es']);
    $contenido_eleccion = $db->real_escape_string($_POST['contenido_eleccion']);
    $contenido_actividades = $db->real_escape_string($_POST['contenido_actividades']);
    $contenido_para_que_sirve = $db->real_escape_string($_POST['contenido_para_que_sirve']);
    $video_url = isset($_POST['video_url']) ? $db->real_escape_string($_POST['video_url']) : '';

    // Get current image
    $imagen_actual = '';
    if ($id > 0) {
        $cadImg = "SELECT IMAGEN_PRINCIPAL FROM VRE_FEDERACION_INFO WHERE ID = $id";
        $sqlImg = $db->query($cadImg);
        if ($db->rows($sqlImg) > 0) {
            $rowImg = $db->recorrer($sqlImg);
            $imagen_actual = $rowImg['IMAGEN_PRINCIPAL'];
        }
    }

    $imagen_principal = $imagen_actual;

    // Process image upload
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['imagen']['type'], $allowedTypes)) {
            echo json_encode(['success' => 0, 'message' => 'Solo se permiten archivos JPG, PNG o GIF']);
            exit();
        }

        if ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
            echo json_encode(['success' => 0, 'message' => 'El archivo no debe superar los 3MB']);
            exit();
        }

        $uploadDir = "../../../uploads/federacion/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'federacion_principal_' . time() . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreArchivo;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
            // Delete old image
            if ($imagen_actual && file_exists("../../../" . ltrim($imagen_actual, "/"))) {
                unlink("../../../" . ltrim($imagen_actual, "/"));
            }
            $imagen_principal = "/vidaEstudiantil/uploads/federacion/$nombreArchivo";
        }
    }

    if ($id > 0) {
        // Update existing record
        $cad = "UPDATE VRE_FEDERACION_INFO SET
                    TITULO = '$titulo',
                    CONTENIDO_QUE_ES = '$contenido_que_es',
                    CONTENIDO_ELECCION = '$contenido_eleccion',
                    CONTENIDO_ACTIVIDADES = '$contenido_actividades',
                    CONTENIDO_PARA_QUE_SIRVE = '$contenido_para_que_sirve',
                    VIDEO_URL = '$video_url',
                    IMAGEN_PRINCIPAL = '$imagen_principal'
                WHERE ID = $id";
    } else {
        // Insert new record
        $cad = "INSERT INTO VRE_FEDERACION_INFO(
                    TITULO, CONTENIDO_QUE_ES, CONTENIDO_ELECCION,
                    CONTENIDO_ACTIVIDADES, CONTENIDO_PARA_QUE_SIRVE,
                    VIDEO_URL, IMAGEN_PRINCIPAL
                ) VALUES (
                    '$titulo', '$contenido_que_es', '$contenido_eleccion',
                    '$contenido_actividades', '$contenido_para_que_sirve',
                    '$video_url', '$imagen_principal'
                )";
    }

    if ($db->query($cad)) {
        $info['success'] = 1;
        $info['message'] = 'Información actualizada exitosamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
