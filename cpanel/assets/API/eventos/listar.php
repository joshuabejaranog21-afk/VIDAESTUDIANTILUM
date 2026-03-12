<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Listar eventos
 * Soporta filtros por categoria, estado, organizador, destacado, activo
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
exit;
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('eventos', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver eventos';
    echo json_encode($info);
exit;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Filtros opcionales
    $categoria = isset($_GET['categoria']) && $_GET['categoria'] != '' ? $db->real_escape_string($_GET['categoria']) : null;
    $estado = isset($_GET['estado']) && $_GET['estado'] != '' ? $db->real_escape_string($_GET['estado']) : null;
    $organizador = isset($_GET['organizador']) && $_GET['organizador'] != '' ? $db->real_escape_string($_GET['organizador']) : null;
    $destacado = isset($_GET['destacado']) && $_GET['destacado'] != '' ? $db->real_escape_string($_GET['destacado']) : null;
    $activo = isset($_GET['activo']) && $_GET['activo'] != '' ? $db->real_escape_string($_GET['activo']) : 'todos';

    // Construir query
    $cad = "SELECT * FROM VRE_EVENTOS WHERE 1=1";

    if ($categoria) {
        $cad .= " AND CATEGORIA = '$categoria'";
    }

    if ($estado) {
        $cad .= " AND ESTADO = '$estado'";
    }

    if ($organizador) {
        $cad .= " AND ORGANIZADOR = '$organizador'";
    }

    if ($destacado) {
        $cad .= " AND DESTACADO = '$destacado'";
    }

    if ($activo !== 'todos') {
        $cad .= " AND ACTIVO = '$activo'";
    }

    // Ordenar por fecha de evento descendente
    $cad .= " ORDER BY FECHA_EVENTO DESC, ID DESC";

    $sql = $db->query($cad);

    if ($sql) {
        $data = [];
        while ($row = $sql->fetch_assoc()) {
            // Obtener imágenes de la galería centralizada (solo si la tabla existe)
            $id_evento = $row['ID'];
            $imagenes = [];
            $imagen_principal = $row['IMAGEN_PRINCIPAL']; // Usar la imagen principal guardada en la tabla

            $imagenes_query = @$db->query("
                SELECT URL_IMAGEN, TIPO, ORDEN
                FROM VRE_GALERIA
                WHERE MODULO = 'eventos'
                AND ID_REGISTRO = $id_evento
                AND ACTIVO = 'S'
                ORDER BY ORDEN ASC
            ");

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
        $info['message'] = 'Eventos obtenidos correctamente';
        $info['data'] = $data;
        $info['total'] = count($data);
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al obtener eventos: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);
exit;

