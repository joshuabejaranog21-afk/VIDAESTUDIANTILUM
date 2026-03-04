<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../db.php");
$db = new Conexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo json_encode(['success' => 0, 'message' => 'ID requerido']); exit; }

// Datos del ministerio
$cad = "SELECT ID, NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL, DIRECTOR_NOMBRE, HORARIO, LUGAR
        FROM VRE_MINISTERIOS WHERE ID = $id AND ACTIVO = 'S'";
$sql = $db->query($cad);
if ($db->rows($sql) === 0) { echo json_encode(['success' => 0, 'message' => 'Ministerio no encontrado']); exit; }

$row = $sql->fetch_assoc();
$ministerio = [
    'id'          => $row['ID'],
    'nombre'      => $row['NOMBRE'],
    'tipo'        => $row['TIPO'],
    'descripcion' => $row['DESCRIPCION'],
    'imagen'      => $row['IMAGEN_URL'],
    'director'    => $row['DIRECTOR_NOMBRE'],
    'horario'     => $row['HORARIO'],
    'lugar'       => $row['LUGAR'],
];

// Directiva
$cadDir = "SELECT NOMBRE, CARGO, EMAIL, FOTO_URL
           FROM VRE_DIRECTIVA_MINISTERIOS
           WHERE ID_MINISTERIO = $id AND ACTIVO = 'S'
           ORDER BY ORDEN ASC, NOMBRE ASC";
$sqlDir = $db->query($cadDir);
$directiva = [];
while ($d = $db->recorrer($sqlDir)) {
    $directiva[] = [
        'nombre' => $d['NOMBRE'],
        'cargo'  => $d['CARGO'],
        'email'  => $d['EMAIL'],
        'foto'   => $d['FOTO_URL'],
    ];
}

// Galería
$cadGal = "SELECT IMAGEN_URL FROM VRE_GALERIA
           WHERE MODULO = 'ministerios' AND ID_REGISTRO = $id AND ACTIVO = 'S'
           ORDER BY ORDEN ASC LIMIT 8";
$sqlGal = $db->query($cadGal);
$galeria = [];
while ($g = $db->recorrer($sqlGal)) { $galeria[] = $g['IMAGEN_URL']; }

echo json_encode(['success' => 1, 'ministerio' => $ministerio, 'directiva' => $directiva, 'galeria' => $galeria]);
