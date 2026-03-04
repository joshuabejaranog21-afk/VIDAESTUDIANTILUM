<?php
header('Content-Type: application/json');
include('../../db.php');

// Log de errores para debugging
error_log("=== UPLOAD PDF DEBUG ===");
error_log("FILES: " . print_r($_FILES, true));
error_log("POST: " . print_r($_POST, true));
error_log("Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'no set'));
error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("post_max_size: " . ini_get('post_max_size'));
error_log("memory_limit: " . ini_get('memory_limit'));

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Validar que se haya enviado un archivo
if (!isset($_FILES['pdf'])) {
    // Obtener información adicional para debug
    $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;
    $postMaxSize = ini_get('post_max_size');

    echo json_encode([
        'success' => false,
        'message' => 'No se recibió ningún archivo',
        'debug' => [
            'filesEmpty' => empty($_FILES),
            'contentLength' => $contentLength,
            'postMaxSize' => $postMaxSize,
            'uploadMaxFilesize' => ini_get('upload_max_filesize'),
            'hint' => 'Si el archivo es muy grande, verifica que reiniciaste Apache después de cambiar php.ini'
        ]
    ]);
    exit();
}

// Verificar errores de upload
$uploadError = $_FILES['pdf']['error'];
if ($uploadError !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP (upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
        UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
        UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
        UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
    ];

    $message = isset($errorMessages[$uploadError])
        ? $errorMessages[$uploadError]
        : 'Error desconocido al subir el archivo (código: ' . $uploadError . ')';

    error_log("Upload error: " . $message);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

$file = $_FILES['pdf'];

// Validar tipo de archivo
$allowedTypes = ['application/pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Solo se permiten archivos PDF']);
    exit();
}

// Validar tamaño (máximo 100MB)
$maxSize = 100 * 1024 * 1024; // 100MB en bytes
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 100MB']);
    exit();
}

// Crear carpeta si no existe
$uploadDir = __DIR__ . '/../../../../uploads/anuarios/';
error_log("Upload directory: " . $uploadDir);

if (!file_exists($uploadDir)) {
    error_log("Creating upload directory...");
    if (!mkdir($uploadDir, 0755, true)) {
        error_log("Failed to create directory");
        echo json_encode(['success' => false, 'message' => 'Error al crear la carpeta de uploads. Verifica los permisos.']);
        exit();
    }
}

// Verificar que la carpeta sea escribible
if (!is_writable($uploadDir)) {
    error_log("Directory is not writable");
    echo json_encode(['success' => false, 'message' => 'La carpeta de uploads no tiene permisos de escritura']);
    exit();
}

// Generar nombre único para el archivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'anuario_' . time() . '_' . uniqid() . '.' . $extension;
$filePath = $uploadDir . $fileName;

error_log("Target file path: " . $filePath);
error_log("Temp file: " . $file['tmp_name']);

// Mover archivo a la carpeta de uploads
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    $error = error_get_last();
    error_log("move_uploaded_file failed: " . print_r($error, true));
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el archivo. Verifica los permisos de la carpeta uploads.',
        'debug' => [
            'uploadDir' => $uploadDir,
            'exists' => file_exists($uploadDir),
            'writable' => is_writable($uploadDir),
            'tmpFile' => $file['tmp_name'],
            'tmpExists' => file_exists($file['tmp_name'])
        ]
    ]);
    exit();
}

error_log("File uploaded successfully: " . $filePath);

// Generar URL del archivo
// Asumiendo que la carpeta www/cpanel es accesible desde /cpanel/
$fileUrl = '/cpanel/uploads/anuarios/' . $fileName;

echo json_encode([
    'success' => true,
    'message' => 'Archivo subido correctamente',
    'url' => $fileUrl,
    'fileName' => $fileName
]);
?>
