<?php
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();

// Obtener parámetro de año si existe
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : null;

$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && security()) {
    if ($anio) {
        // Filtrar colaboradores por año
        $cad = "SELECT * FROM VRE_PULSO_EQUIPOS
                WHERE ANIO = $anio AND ACTIVO = 'S'
                ORDER BY ORDEN ASC, NOMBRE ASC";
    } else {
        // Obtener todos los colaboradores activos
        $cad = "SELECT * FROM VRE_PULSO_EQUIPOS
                WHERE ACTIVO = 'S'
                ORDER BY ANIO DESC, ORDEN ASC, NOMBRE ASC";
    }

    $sql = $db->query($cad);
    $rows = $db->rows($sql);

    if ($rows > 0) {
        $info['success'] = 1;
        $info['message'] = "$rows colaborador(es) encontrado(s)";
        $info['data'] = [];
        foreach ($sql as $key) {
            $info['data'][] = $key;
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'No se encontraron colaboradores';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método de acceso incorrecto';
}

echo json_encode($info);
$db = null;
?>
