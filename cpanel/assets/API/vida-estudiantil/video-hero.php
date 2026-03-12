<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

try {
    include("../../php/template.php");
} catch (Exception $e) {
    echo json_encode(['success' => 0, 'message' => 'Error al cargar template: ' . $e->getMessage()]);
    exit;
}

try {
    $temp = new Template();
} catch (Exception $e) {
    echo json_encode(['success' => 0, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

if (!$temp->validate_session()) {
    echo json_encode(['success' => 0, 'message' => 'Sin sesión']);
    exit;
}

$videoDir  = __DIR__ . '/../../../../vidaEstudiantil/assets/videos/';
$videoPath = $videoDir . 'hero.mp4';
$videoURL  = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.mp4';
$urlFile   = $videoDir . 'hero-url.txt';

$action = $_GET['action'] ?? '';

// ── GET: devuelve si hay video activo ──
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === '') {
    $urlGuardada = (file_exists($urlFile)) ? trim(file_get_contents($urlFile)) : '';
    echo json_encode([
        'success' => 1,
        'tiene_video' => file_exists($videoPath),
        'tiene_url'   => $urlGuardada !== '',
        'url' => file_exists($videoPath) ? $videoURL : ($urlGuardada ?: null),
        'tamaño' => file_exists($videoPath) ? filesize($videoPath) : 0,
    ]);
    exit;
}

// ── DELETE: elimina el video archivo ──
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $action === '') {
    if (file_exists($videoPath)) {
        unlink($videoPath);
        echo json_encode(['success' => 1, 'message' => 'Video eliminado']);
    } else {
        echo json_encode(['success' => 0, 'message' => 'No hay video para eliminar']);
    }
    exit;
}

// ── POST save-url: guarda URL externa ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save-url') {
    $body = json_decode(file_get_contents('php://input'), true);
    $url  = trim($body['video_url'] ?? '');

    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        // Permitir también URLs de YouTube que pueden no pasar FILTER_VALIDATE_URL estrictamente
        if ($url === '' || strpos($url, 'http') !== 0) {
            echo json_encode(['success' => 0, 'message' => 'URL no válida']);
            exit;
        }
    }

    if (!is_dir($videoDir)) mkdir($videoDir, 0755, true);
    file_put_contents($urlFile, $url);
    echo json_encode(['success' => 1, 'message' => 'URL guardada']);
    exit;
}

// ── DELETE delete-url: elimina URL guardada ──
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $action === 'delete-url') {
    if (file_exists($urlFile)) {
        unlink($urlFile);
        echo json_encode(['success' => 1, 'message' => 'URL eliminada']);
    } else {
        echo json_encode(['success' => 0, 'message' => 'No hay URL guardada']);
    }
    exit;
}

// ── POST: sube nuevo video ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => 0, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    $errores = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo supera upload_max_filesize en php.ini',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo supera MAX_FILE_SIZE del formulario',
        UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir en disco',
    ];
    $codigo = $_FILES['video']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode(['success' => 0, 'message' => $errores[$codigo] ?? 'Error al subir archivo']);
    exit;
}

$file = $_FILES['video'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['mp4', 'webm'])) {
    echo json_encode(['success' => 0, 'message' => 'Solo se permiten archivos .mp4 o .webm']);
    exit;
}

// Límite 200 MB
if ($file['size'] > 200 * 1024 * 1024) {
    echo json_encode(['success' => 0, 'message' => 'El video no puede superar 200 MB']);
    exit;
}

// Validar MIME básico
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
$mimesPermitidos = ['video/mp4', 'video/webm', 'video/ogg', 'application/octet-stream'];
if (!in_array($mime, $mimesPermitidos)) {
    echo json_encode(['success' => 0, 'message' => 'Tipo de archivo no válido: ' . $mime]);
    exit;
}

// Crear carpeta si no existe
if (!is_dir($videoDir)) {
    mkdir($videoDir, 0755, true);
}

// Eliminar video anterior si hay
if (file_exists($videoPath)) {
    unlink($videoPath);
}

// Si subieron .webm, guardar con su extensión propia
$destino = $videoDir . 'hero.' . $ext;
$urlFinal = '/vida_estudiantil_Hitha/vidaEstudiantil/assets/videos/hero.' . $ext;

if (!move_uploaded_file($file['tmp_name'], $destino)) {
    echo json_encode(['success' => 0, 'message' => 'Error al guardar el video en el servidor']);
    exit;
}

echo json_encode([
    'success'  => 1,
    'message'  => 'Video subido correctamente',
    'url'      => $urlFinal,
    'tamaño'   => $file['size'],
    'formato'  => $ext,
]);
exit;
