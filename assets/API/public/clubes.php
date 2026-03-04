<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../db.php");
$db = new Conexion();

$cad = "SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE, HORARIO, LUGAR, CONTACTO
        FROM VRE_CLUBES
        WHERE ACTIVO = 'S'
        ORDER BY ORDEN ASC, NOMBRE ASC";
$sql = $db->query($cad);
$clubes = [];
while ($row = $db->recorrer($sql)) {
    $clubes[] = [
        'id'          => $row['ID'],
        'nombre'      => $row['NOMBRE'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen'      => $row['IMAGEN_URL'],
        'responsable' => $row['RESPONSABLE_NOMBRE'],
        'horario'     => $row['HORARIO'],
        'lugar'       => $row['LUGAR'],
        'contacto'    => $row['CONTACTO'],
    ];
}
echo json_encode(['success' => 1, 'data' => $clubes, 'total' => count($clubes)]);
