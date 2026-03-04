<?php
/**
 * API: Obtener un co-curricular específico por ID
 * Requiere: id (GET)
 * Retorna: Datos del co-curricular + imágenes de la galería
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
    // Validar ID
    if (empty($_GET['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
        exit();
    }

    $id = intval($_GET['id']);

    // Obtener co-curricular
    $cad = "SELECT * FROM VRE_COCURRICULARES WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql && $sql->num_rows > 0) {
        $data = $sql->fetch_assoc();

        // Obtener imágenes de la galería
        $imagenes_query = $db->query("
            SELECT ID, URL_IMAGEN, TIPO, ORDEN, TITULO, DESCRIPCION
            FROM VRE_GALERIA
            WHERE MODULO = 'cocurriculares'
            AND ID_REGISTRO = $id
            AND ACTIVO = 'S'
            ORDER BY TIPO, ORDEN ASC
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

        $data['IMAGEN_PRINCIPAL'] = $imagen_principal;
        $data['IMAGENES'] = $imagenes;
        $data['TOTAL_IMAGENES'] = count($imagenes);

        $info['success'] = 1;
        $info['message'] = 'Obtenido correctamente';
        $info['data'] = $data;
    } else {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use GET.';
}

echo json_encode($info);
?>
