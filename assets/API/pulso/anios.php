<?php
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && security()) {
    // Obtener años únicos de colaboradores activos
    $cad = "SELECT DISTINCT ANIO FROM VRE_PULSO_EQUIPOS
            WHERE ACTIVO = 'S'
            ORDER BY ANIO DESC";

    $sql = $db->query($cad);
    $rows = $db->rows($sql);

    if ($rows > 0) {
        $info['success'] = 1;
        $info['message'] = "$rows año(s) encontrado(s)";
        $info['data'] = [];
        foreach ($sql as $key) {
            $info['data'][] = $key['ANIO'];
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'No se encontraron años disponibles';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método de acceso incorrecto';
}

echo json_encode($info);
$db = null;
?>
