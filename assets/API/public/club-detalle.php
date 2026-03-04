<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../db.php");
$db = new Conexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo json_encode(['success' => 0, 'message' => 'ID requerido']); exit; }

// Datos del club
$cad = "SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE,
               HORARIO, LUGAR, CONTACTO, REDES_SOCIALES
        FROM VRE_CLUBES WHERE ID = $id AND ACTIVO = 'S'";
$sql = $db->query($cad);
if ($db->rows($sql) === 0) { echo json_encode(['success' => 0, 'message' => 'Club no encontrado']); exit; }

$row = $sql->fetch_assoc();
$redes = [];
if (!empty($row['REDES_SOCIALES'])) {
    $redes = json_decode($row['REDES_SOCIALES'], true) ?? [];
}
$club = [
    'id'          => $row['ID'],
    'nombre'      => $row['NOMBRE'],
    'descripcion' => $row['DESCRIPCION'],
    'imagen'      => $row['IMAGEN_URL'],
    'responsable' => $row['RESPONSABLE_NOMBRE'],
    'horario'     => $row['HORARIO'],
    'lugar'       => $row['LUGAR'],
    'contacto'    => $row['CONTACTO'],
    'redes'       => $redes,
];

// Directiva
$cadDir = "SELECT NOMBRE, CARGO, EMAIL, TELEFONO, FOTO_URL
           FROM VRE_DIRECTIVA_CLUBES
           WHERE ID_CLUB = $id AND ACTIVO = 'S'
           ORDER BY ORDEN ASC, NOMBRE ASC";
$sqlDir = $db->query($cadDir);
$directiva = [];
while ($d = $db->recorrer($sqlDir)) {
    $directiva[] = [
        'nombre'   => $d['NOMBRE'],
        'cargo'    => $d['CARGO'],
        'email'    => $d['EMAIL'],
        'telefono' => $d['TELEFONO'],
        'foto'     => $d['FOTO_URL'],
    ];
}

// Galería
$cadGal = "SELECT IMAGEN_URL FROM VRE_GALERIA
           WHERE MODULO = 'clubes' AND ID_REGISTRO = $id AND ACTIVO = 'S'
           ORDER BY ORDEN ASC LIMIT 8";
$sqlGal = $db->query($cadGal);
$galeria = [];
while ($g = $db->recorrer($sqlGal)) { $galeria[] = $g['IMAGEN_URL']; }

echo json_encode(['success' => 1, 'club' => $club, 'directiva' => $directiva, 'galeria' => $galeria]);
