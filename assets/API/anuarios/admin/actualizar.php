<?php
header('Content-Type: application/json');
include('../../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

// Get form data
$id = intval($_POST['id']);
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

if ($id === 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$query = "UPDATE VRE_ANUARIOS SET
    TITULO = '$titulo',
    ANIO = $anio,
    DESCRIPCION = '$descripcion',
    DECADA = " . ($decada > 0 ? $decada : 'NULL') . ",
    ES_CONMEMORATIVO = '$es_conmemorativo',
    RAZON_CONMEMORATIVA = '$razon_conmemorativa',
    IMAGEN_PORTADA = '$imagen_portada',
    PDF_URL = '$pdf_url',
    TOTAL_PAGINAS = $total_paginas,
    FOTOGRAFOS = '$fotografos',
    CONTRIBUYENTES = '$contribuyentes'
WHERE ID = $id";

if ($db->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Anuario actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el anuario: ' . $db->error]);
}
?>
