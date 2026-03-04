<?php
header('Content-Type: application/json');
require_once('../../API/db.php');

try {
    $db = new Conexion();
    
    $activo = isset($_GET['activo']) ? $_GET['activo'] : 'S';
    
    $query = "
        SELECT
            ID,
            NOMBRE,
            DESCRIPCION,
            ACTIVO,
            ORDEN
        FROM VRE_DEPORTES
        WHERE 1=1
    ";

    if ($activo !== 'todos') {
        $query .= " AND ACTIVO = '$activo'";
    }

    $query .= " ORDER BY ORDEN, NOMBRE";

    $result = $db->query($query);
    $deportes = [];

    while ($row = $db->recorrer($result)) {
        $deporte_id = $row['ID'];

        // Obtener imagen desde VRE_GALERIA
        $imagenes_query = $db->query("
            SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
            FROM VRE_GALERIA
            WHERE MODULO = 'deportes'
            AND ID_REGISTRO = $deporte_id
            AND ACTIVO = 'S'
            ORDER BY ORDEN ASC
        ");

        $imagen_principal = null;

        if ($imagenes_query) {
            while ($img = $db->recorrer($imagenes_query)) {
                if ($img['TIPO'] == 'principal' && !$imagen_principal) {
                    $imagen_principal = $img['URL_IMAGEN'];
                }
            }
        }

        $row['IMAGEN_PRINCIPAL'] = $imagen_principal;

        $deportes[] = $row;
    }
    
    echo json_encode([
        'success' => 1,
        'message' => 'Deportes obtenidos correctamente',
        'data' => $deportes,
        'total' => count($deportes)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => 'Error al obtener deportes: ' . $e->getMessage()
    ]);
}
?>
