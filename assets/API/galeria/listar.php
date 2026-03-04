<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Listar imágenes de la galería centralizada
 * Soporta filtros por módulo, registro, tipo
 */

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

// Validar permiso
if (!$temp->tiene_permiso('galeria', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver la galería';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Filtros opcionales
    $modulo = isset($_GET['modulo']) ? $db->real_escape_string($_GET['modulo']) : null;
    $id_registro = isset($_GET['id_registro']) ? intval($_GET['id_registro']) : null;
    $tipo = isset($_GET['tipo']) ? $db->real_escape_string($_GET['tipo']) : null;
    $activo = isset($_GET['activo']) && $_GET['activo'] != '' ? $db->real_escape_string($_GET['activo']) : 'todos';

    // Construir query usando la vista
    $cad = "SELECT * FROM VRE_GALERIA_INFO WHERE 1=1";

    if ($modulo) {
        $cad .= " AND MODULO = '$modulo'";
    }

    if ($id_registro) {
        $cad .= " AND ID_REGISTRO = $id_registro";
    }

    if ($tipo) {
        $cad .= " AND TIPO = '$tipo'";
    }

    if ($activo !== 'todos') {
        $cad .= " AND ACTIVO = '$activo'";
    }

    // Ordenar por módulo, registro y orden
    $cad .= " ORDER BY MODULO, ID_REGISTRO, ORDEN ASC, FECHA_SUBIDA DESC";

    $sql = $db->query($cad);

    if ($sql) {
        $data = [];
        while ($row = $sql->fetch_assoc()) {
            $data[] = $row;
        }

        $info['success'] = 1;
        $info['message'] = 'Imágenes obtenidas correctamente';
        $info['data'] = $data;
        $info['total'] = count($data);

        // Registrar en auditoría
        $temp->registrar_auditoria('GALERIA', 'LISTAR', 'Consultó ' . count($data) . ' imágenes');
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al obtener imágenes: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);

