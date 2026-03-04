<?php
/**
 * API: Listar co-curriculares (programas y servicios estudiantiles)
 * Soporta filtros por tipo, activo
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
if (!$temp->tiene_permiso('cocurriculares', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver co-curriculares';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Filtros opcionales
    $tipo = isset($_GET['tipo']) && $_GET['tipo'] != '' ? $db->real_escape_string($_GET['tipo']) : null;
    $activo = isset($_GET['activo']) && $_GET['activo'] != '' ? $db->real_escape_string($_GET['activo']) : 'todos';

    // Construir query
    $cad = "SELECT * FROM VRE_COCURRICULARES WHERE 1=1";

    if ($tipo) {
        $cad .= " AND TIPO = '$tipo'";
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
            $id_cocurricular = $row['ID'];
            $imagenes_query = $db->query("
                SELECT URL_IMAGEN, TIPO, ORDEN
                FROM VRE_GALERIA
                WHERE MODULO = 'cocurriculares'
                AND ID_REGISTRO = $id_cocurricular
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
        $info['message'] = 'Co-curriculares obtenidos correctamente';
        $info['data'] = $data;
        $info['total'] = count($data);

        // Registrar en auditoría
        $temp->registrar_auditoria('COCURRICULARES', 'LISTAR', 'Consultó ' . count($data) . ' co-curriculares');
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al obtener co-curriculares: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);
?>
