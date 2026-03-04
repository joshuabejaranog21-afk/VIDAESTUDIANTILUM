<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Validar que sea POST y que exista archivo
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método no permitido');
    }

    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] != UPLOAD_ERR_OK) {
        throw new Exception('No se subió archivo o hubo error en la subida');
    }

    $file = $_FILES['archivo'];
    $tipo_permitido = isset($_POST['tipo']) ? $_POST['tipo'] : 'imagen'; // imagen, foto, banner, etc

    // Validaciones
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extensiones_permitidas)) {
        throw new Exception('Extensión no permitida. Use: ' . implode(', ', $extensiones_permitidas));
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        throw new Exception('Archivo muy grande. Máximo 5MB');
    }

    // Crear directorio si no existe
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generar nombre único
    $nombre_archivo = $tipo_permitido . '_' . uniqid() . '.' . $ext;
    $ruta_archivo = $upload_dir . $nombre_archivo;

    // Validar imagen
    $info = getimagesize($file['tmp_name']);
    if ($info === false) {
        throw new Exception('Archivo no es una imagen válida');
    }

    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $ruta_archivo)) {
        throw new Exception('Error al guardar el archivo');
    }

    // URL relativa para acceder al archivo
    $url_relativa = '/cpanel/assets/uploads/' . $nombre_archivo;

    // Obtener URL base del sitio
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $url_completa = $protocol . '://' . $host . $url_relativa;

    // Registrar en BD (opcional)
    @require_once('db.php');
    if (class_exists('Conexion')) {
        try {
            $db = new Conexion();
            $nombre_escaped = $db->real_escape_string($nombre_archivo);
            $url_escaped = $db->real_escape_string($url_relativa);
            $fecha = date('Y-m-d H:i:s');

            $insert = "INSERT INTO SYSTEM_UPLOADS (NOMBRE, URL, FECHA) VALUES ('$nombre_escaped', '$url_escaped', '$fecha')";
            @$db->query($insert);
        } catch (Exception $e) {
            // Ignorar error de BD, continuar con la respuesta
        }
    }

    echo json_encode([
        'success' => 1,
        'message' => 'Archivo subido correctamente',
        'url' => $url_completa,
        'url_relativa' => $url_relativa,
        'nombre' => $nombre_archivo,
        'tamaño' => $file['size']
    ], JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_SLASHES);
}
exit;
