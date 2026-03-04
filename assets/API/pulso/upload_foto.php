<?php
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && security()) {

    // Verificar que se haya enviado un archivo
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $info['success'] = 0;
        $info['message'] = 'No se recibió ningún archivo o hubo un error en la subida';
        echo json_encode($info);
        exit;
    }

    $file = $_FILES['foto'];

    // Validar tipo de archivo
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas)) {
        $info['success'] = 0;
        $info['message'] = 'Tipo de archivo no permitido. Solo se aceptan: ' . implode(', ', $extensionesPermitidas);
        echo json_encode($info);
        exit;
    }

    // Validar tamaño (máximo 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB en bytes
    if ($file['size'] > $maxSize) {
        $info['success'] = 0;
        $info['message'] = 'El archivo es demasiado grande. Tamaño máximo: 5MB';
        echo json_encode($info);
        exit;
    }

    // Crear directorio si no existe
    $uploadDir = '../../../uploads/pulso/fotos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generar nombre único para el archivo
    $nombreArchivo = uniqid('colaborador_') . '_' . time() . '.' . $extension;
    $rutaDestino = $uploadDir . $nombreArchivo;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        // URL relativa para guardar en la base de datos
        $urlRelativa = 'uploads/pulso/fotos/' . $nombreArchivo;

        $info['success'] = 1;
        $info['message'] = 'Foto subida exitosamente';
        $info['url'] = $urlRelativa;
        $info['filename'] = $nombreArchivo;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al mover el archivo al servidor';
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método de acceso incorrecto';
}

echo json_encode($info);
$db = null;
?>
