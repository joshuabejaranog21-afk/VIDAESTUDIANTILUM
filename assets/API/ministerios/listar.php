<?php
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
if (!$temp->tiene_permiso('ministerios', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver ministerios';
    echo json_encode($info);
    exit();
}

try {
    $cad = "SELECT m.*, u.NOMBRE as DIRECTOR_NOMBRE, u.EMAIL as DIRECTOR_EMAIL
            FROM VRE_MINISTERIOS m
            LEFT JOIN SYSTEM_USUARIOS u ON m.ID_DIRECTOR_USUARIO = u.ID
            ORDER BY m.NOMBRE ASC";
    $sql = $db->query($cad);

    $ministerios = [];
    while($row = $sql->fetch_assoc()) {
        $ministerio_id = $row['ID'];

        // Obtener imágenes desde VRE_GALERIA
        $imagenes_query = $db->query("
            SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
            FROM VRE_GALERIA
            WHERE MODULO = 'ministerios'
            AND ID_REGISTRO = $ministerio_id
            AND ACTIVO = 'S'
            ORDER BY ORDEN ASC
        ");

        $imagenes = [];
        $imagen_principal = null;

        if ($imagenes_query) {
            while ($img = $imagenes_query->fetch_assoc()) {
                $imagenes[] = $img;
                if ($img['TIPO'] == 'principal' && !$imagen_principal) {
                    $imagen_principal = $img['URL_IMAGEN'];
                }
            }
        }

        $row['IMAGEN_PRINCIPAL'] = $imagen_principal;
        $row['IMAGENES'] = $imagenes;
        $row['TOTAL_IMAGENES'] = count($imagenes);

        // Mantener compatibilidad temporal con GALERIA_ARRAY (deprecated)
        $row['GALERIA_ARRAY'] = [];

        $ministerios[] = $row;
    }

    $info['success'] = 1;
    $info['data'] = $ministerios;
    $info['total'] = count($ministerios);

} catch (Exception $e) {
    $info['success'] = 0;
    $info['message'] = 'Error al obtener ministerios';
    if ($db->mostrarErrores) {
        $info['error'] = $e->getMessage();
    }
}

echo json_encode($info);
?>
