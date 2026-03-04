<?php
header('Content-Type: application/json');
include('../../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

// Get form data
$titulo = $db->real_escape_string($_POST['titulo']);
$anio = intval($_POST['anio']);
$descripcion = $db->real_escape_string($_POST['descripcion'] ?? '');
$decada = intval($_POST['decada'] ?? 0);
$total_paginas = intval($_POST['total_paginas'] ?? 0);
$es_conmemorativo = $_POST['es_conmemorativo'] === 'S' ? 'S' : 'N';
$razon_conmemorativa = $db->real_escape_string($_POST['razon_conmemorativa'] ?? '');
$pdf_url = $db->real_escape_string($_POST['pdf_url']);
$imagen_portada = $db->real_escape_string($_POST['imagen_portada'] ?? '');
$fotografos = $db->real_escape_string($_POST['fotografos'] ?? '');
$contribuyentes = $db->real_escape_string($_POST['contribuyentes'] ?? '');

// Get user ID
$user_id = null;
if (isset($_COOKIE['system_name']) && isset($_COOKIE['system_token'])) {
    $name = $db->real_escape_string($_COOKIE['system_name']);
    $token = $db->real_escape_string($_COOKIE['system_token']);
    $user_query = "SELECT ID FROM SYSTEM_USUARIOS WHERE NOMBRE = '$name' AND TOKEN = '$token' AND ACTIVO = 'S'";
    $user_result = $db->query($user_query);
    if ($user_result && $db->rows($user_result) > 0) {
        $user_data = $user_result->fetch_assoc();
        $user_id = $user_data['ID'];
    }
}

$query = "INSERT INTO VRE_ANUARIOS (
    TITULO, ANIO, DESCRIPCION, DECADA, ES_CONMEMORATIVO, RAZON_CONMEMORATIVA,
    IMAGEN_PORTADA, PDF_URL, TOTAL_PAGINAS, FOTOGRAFOS, CONTRIBUYENTES, ID_USUARIO_CREADOR
) VALUES (
    '$titulo', $anio, '$descripcion', " . ($decada > 0 ? $decada : 'NULL') . ", '$es_conmemorativo', '$razon_conmemorativa',
    '$imagen_portada', '$pdf_url', $total_paginas, '$fotografos', '$contribuyentes', " . ($user_id ? $user_id : 'NULL') . "
)";

if ($db->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Anuario creado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al crear el anuario: ' . $db->error]);
}
?>
