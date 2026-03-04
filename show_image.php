<?php
$imagePath = 'uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg';

// Verificar si el archivo existe
if (!file_exists($imagePath)) {
    die("ERROR: El archivo no existe en: " . realpath('.') . '/' . $imagePath);
}

// Obtener información del archivo
$imageInfo = getimagesize($imagePath);
$mimeType = $imageInfo['mime'];

// Enviar headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($imagePath));

// Leer y mostrar la imagen
readfile($imagePath);
?>
