<?php
error_reporting(0);
ini_set('display_errors', 0);

include("../../php/template.php");

header('Content-Type: application/json');
$temp = new Template();
$db = new Conexion();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtener el tipo de filtro (club o ministerio) si se proporciona
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

    // Query base: usuarios activos
    $cad = "SELECT
                u.ID,
                u.NOMBRE,
                u.NOMBRE_COMPLETO,
                u.EMAIL,
                u.ID_CAT,
                c.NOMBRE as ROL_NOMBRE
            FROM SYSTEM_USUARIOS u
            LEFT JOIN SYSTEM_CAT_USUARIOS c ON u.ID_CAT = c.ID
            WHERE u.ACTIVO = 'S'";

    // Filtrar según el tipo si se especifica
    if ($tipo === 'club') {
        // Solo usuarios que no tengan club asignado
        $cad .= " AND (u.ID_CLUB_ASIGNADO IS NULL OR u.ID_CLUB_ASIGNADO = 0)";
    } else if ($tipo === 'ministerio') {
        // Podrías agregar filtro similar para ministerios si existe el campo
        // $cad .= " AND (u.ID_MINISTERIO_ASIGNADO IS NULL OR u.ID_MINISTERIO_ASIGNADO = 0)";
    }

    // Ordenar por nombre
    $cad .= " ORDER BY u.NOMBRE ASC";

    $sql = $db->query($cad);
    $rows = $db->rows($sql);

    if ($rows > 0) {
        $info['success'] = 1;
        $info['message'] = "$rows usuario(s) disponible(s)";
        $info['data'] = [];

        foreach ($sql as $usuario) {
            $info['data'][] = [
                'ID' => $usuario['ID'],
                'NOMBRE' => $usuario['NOMBRE'],
                'NOMBRE_COMPLETO' => $usuario['NOMBRE_COMPLETO'],
                'EMAIL' => $usuario['EMAIL'],
                'ID_CAT' => $usuario['ID_CAT'],
                'ROL_NOMBRE' => $usuario['ROL_NOMBRE']
            ];
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'No hay usuarios disponibles';
        $info['data'] = [];
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
