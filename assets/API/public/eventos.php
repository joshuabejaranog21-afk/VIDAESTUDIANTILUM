<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../db.php");
$db = new Conexion();

$categoria  = isset($_GET['categoria'])  ? $db->real_escape_string($_GET['categoria'])  : null;
$destacados = isset($_GET['destacados']) ? 1 : null;
$limite     = isset($_GET['limite'])     ? min((int)$_GET['limite'], 50) : 20;

$cad = "SELECT ID, TITULO, DESCRIPCION_CORTA, FECHA_EVENTO, LUGAR,
               IMAGEN_PRINCIPAL, CATEGORIA, ESTADO, DESTACADO
        FROM VRE_EVENTOS
        WHERE ACTIVO = 'S'";
if ($categoria)  $cad .= " AND CATEGORIA = '$categoria'";
if ($destacados) $cad .= " AND DESTACADO = 'S'";
$cad .= " ORDER BY DESTACADO DESC, FECHA_EVENTO ASC LIMIT $limite";

$sql = $db->query($cad);
$eventos = [];
while ($row = $db->recorrer($sql)) {
    $eventos[] = [
        'id'          => $row['ID'],
        'titulo'      => $row['TITULO'],
        'descripcion' => $row['DESCRIPCION_CORTA'],
        'fecha'       => $row['FECHA_EVENTO'],
        'lugar'       => $row['LUGAR'],
        'imagen'      => $row['IMAGEN_PRINCIPAL'],
        'categoria'   => $row['CATEGORIA'],
        'estado'      => $row['ESTADO'],
        'destacado'   => $row['DESTACADO'],
    ];
}
echo json_encode(['success' => 1, 'data' => $eventos, 'total' => count($eventos)]);
