<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../db.php");
$db = new Conexion();

$cad = "SELECT ID, NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL, DIRECTOR_NOMBRE, HORARIO, LUGAR
        FROM VRE_MINISTERIOS
        WHERE ACTIVO = 'S'
        ORDER BY ORDEN ASC, NOMBRE ASC";
$sql = $db->query($cad);
$ministerios = [];
while ($row = $db->recorrer($sql)) {
    $ministerios[] = [
        'id'          => $row['ID'],
        'nombre'      => $row['NOMBRE'],
        'tipo'        => $row['TIPO'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen'      => $row['IMAGEN_URL'],
        'director'    => $row['DIRECTOR_NOMBRE'],
        'horario'     => $row['HORARIO'],
        'lugar'       => $row['LUGAR'],
    ];
}
echo json_encode(['success' => 1, 'data' => $ministerios, 'total' => count($ministerios)]);
