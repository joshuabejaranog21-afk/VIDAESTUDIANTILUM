<?php
/**
 * API: Listar instalaciones deportivas
 * Soporta filtros por tipo, disponible, activo
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
if (!$temp->tiene_permiso('instalaciones', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver instalaciones';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Filtros opcionales
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] != '' ? $db->real_escape_string($_GET['tipo']) : null;
    $disponible = isset($_GET['disponible']) && $_GET['disponible'] != '' ? $db->real_escape_string($_GET['disponible']) : null;
    $activo = isset($_GET['activo']) && $_GET['activo'] != '' ? $db->real_escape_string($_GET['activo']) : 'S';

    // Construir query
    $cad = "SELECT * FROM VRE_INSTALACIONES_DEPORTIVAS WHERE 1=1";

    if ($tipo) {
        $cad .= " AND TIPO = '$tipo'";
    }

    if ($disponible) {
        $cad .= " AND DISPONIBLE = '$disponible'";
    }

    if ($activo !== 'todos') {
        $cad .= " AND ACTIVO = '$activo'";
    }

    // Ordenar por orden y nombre
    $cad .= " ORDER BY ORDEN ASC, NOMBRE ASC";

    $sql = $db->query($cad);

    if ($sql) {
        $data = [];
        while ($row = $sql->fetch_assoc()) {
            // Obtener imágenes de la galería centralizada
            $id_instalacion = $row['ID'];
            $imagenes_query = $db->query("
                SELECT URL_IMAGEN, TIPO, ORDEN
                FROM VRE_GALERIA
                WHERE MODULO = 'instalaciones'
                AND ID_REGISTRO = $id_instalacion
                AND ACTIVO = 'S'
                ORDER BY ORDEN ASC
            ");

            $imagenes = [];
            $imagen_principal = null;

            if ($imagenes_query) {
                while ($img = $imagenes_query->fetch_assoc()) {
                    if ($img['TIPO'] == 'principal' && !$imagen_principal) {
                        $imagen_principal = $img['URL_IMAGEN'];
                    }
                    $imagenes[] = $img;
                }
            }

            $row['IMAGEN_PRINCIPAL'] = $imagen_principal;
            $row['IMAGENES'] = $imagenes;
            $row['TOTAL_IMAGENES'] = count($imagenes);

            $data[] = $row;
        }

        $info['success'] = 1;
        $info['message'] = 'Instalaciones obtenidas correctamente';
        $info['data'] = $data;
        $info['total'] = count($data);

        // Registrar en auditoría
        $temp->registrar_auditoria('INSTALACIONES', 'LISTAR', 'Consultó ' . count($data) . ' instalaciones');
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al obtener instalaciones: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);
?>
