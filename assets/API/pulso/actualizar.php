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
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => 0, 'message' => 'ID requerido']);
        exit();
    }

    $id = intval($_POST['id']);
    $nombre = $db->real_escape_string($_POST['nombre']);
    $id_cargo = intval($_POST['id_cargo']);
    $anio = intval($_POST['anio']);
    $periodo = isset($_POST['periodo']) ? $db->real_escape_string($_POST['periodo']) : '';
    $bio = isset($_POST['bio']) ? $db->real_escape_string($_POST['bio']) : '';
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;
    $activo = isset($_POST['activo']) && $_POST['activo'] === 'S' ? 'S' : 'N';
    $flickr_url = isset($_POST['flickr_url']) ? $db->real_escape_string($_POST['flickr_url']) : '';

    // Obtener el nombre del cargo para guardarlo en CARGO (retrocompatibilidad)
    $cargo_result = $db->query("SELECT NOMBRE FROM vre_cargos WHERE ID = $id_cargo");
    $cargo_row = $db->recorrer($cargo_result);
    $cargo = $cargo_row ? $db->real_escape_string($cargo_row['NOMBRE']) : '';

    // Obtener foto actual
    $cadFoto = "SELECT FOTO_URL FROM vre_pulso_equipos WHERE ID = $id";
    $sqlFoto = $db->query($cadFoto);
    $fotoActual = '';
    if ($db->rows($sqlFoto) > 0) {
        $rowFoto = $db->recorrer($sqlFoto);
        $fotoActual = $rowFoto['FOTO_URL'];
    }

    $foto_url = $fotoActual;

    // Procesar nueva foto si se subió
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
            echo json_encode(['success' => 0, 'message' => 'Solo se permiten archivos JPG, PNG o GIF']);
            exit();
        }

        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => 0, 'message' => 'El archivo no debe superar los 2MB']);
            exit();
        }

        $uploadDir = "../../../uploads/pulso/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid('equipo_' . time() . '_') . '.' . $extension;
        $rutaCompleta = $uploadDir . $nombreArchivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
            // Eliminar foto anterior si existe
            if ($fotoActual && file_exists("../../../" . ltrim($fotoActual, "/"))) {
                unlink("../../../" . ltrim($fotoActual, "/"));
            }
            $foto_url = "/vidaEstudiantil/uploads/pulso/$nombreArchivo";
        }
    }

    $cad = "UPDATE vre_pulso_equipos SET
                NOMBRE = '$nombre',
                CARGO = '$cargo',
                ID_CARGO = $id_cargo,
                ANIO = $anio,
                PERIODO = '$periodo',
                FOTO_URL = '$foto_url',
                FLICKR_URL = '$flickr_url',
                BIO = '$bio',
                ORDEN = $orden,
                ACTIVO = '$activo'
            WHERE ID = $id";

    if ($db->query($cad)) {
        $info['success'] = 1;
        $info['message'] = 'Colaborador actualizado exitosamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar colaborador: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
