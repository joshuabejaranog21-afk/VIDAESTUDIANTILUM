<?php
header('Content-Type: application/json');
require_once('../../API/db.php');

try {
    $db = new Conexion();

    // Parámetros opcionales
    $id_deporte = isset($_GET['id_deporte']) && !empty($_GET['id_deporte']) ? (int)$_GET['id_deporte'] : null;
    $activo = isset($_GET['activo']) ? $_GET['activo'] : '';

    // Construir query (sin IMAGEN_URL y GALERIA, ahora usa VRE_GALERIA)
    $query = "
        SELECT
            l.ID,
            l.ID_DEPORTE,
            l.NOMBRE,
            l.FECHA_INICIO,
            l.DESCRIPCION,
            l.REQUISITOS,
            l.RESPONSABLE_NOMBRE,
            l.RESPONSABLE_CONTACTO,
            l.FOTO_RESPONSABLE,
            l.EMAIL,
            l.TELEFONO,
            l.ACTIVO,
            l.ESTADO,
            l.ORDEN,
            l.FECHA_CREACION,
            d.NOMBRE as DEPORTE_NOMBRE
        FROM VRE_LIGAS l
        LEFT JOIN VRE_DEPORTES d ON l.ID_DEPORTE = d.ID
        WHERE 1=1
    ";

    // Filtrar por deporte solo si se proporciona
    if ($id_deporte) {
        $query .= " AND l.ID_DEPORTE = $id_deporte";
    }

    // Filtrar por estado solo si se proporciona (cadena vacía = todos)
    if ($activo !== '' && $activo !== 'todos') {
        $query .= " AND l.ACTIVO = '$activo'";
    }

    $query .= " ORDER BY l.ORDEN, l.NOMBRE";

    $result = $db->query($query);
    $ligas = [];

    while ($row = $db->recorrer($result)) {
        $liga_id = $row['ID'];

        // Obtener imágenes desde VRE_GALERIA
        $imagenes_query = $db->query("
            SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
            FROM VRE_GALERIA
            WHERE MODULO = 'ligas'
            AND ID_REGISTRO = $liga_id
            AND ACTIVO = 'S'
            ORDER BY ORDEN ASC
        ");

        $imagenes = [];
        $imagen_principal = null;

        if ($imagenes_query) {
            while ($img = $db->recorrer($imagenes_query)) {
                $imagenes[] = $img;
                if ($img['TIPO'] == 'principal' && !$imagen_principal) {
                    $imagen_principal = $img['URL_IMAGEN'];
                }
            }
        }

        $row['IMAGEN_PRINCIPAL'] = $imagen_principal;
        $row['IMAGENES'] = $imagenes;
        $row['TOTAL_IMAGENES'] = count($imagenes);

        $ligas[] = $row;
    }
    
    echo json_encode([
        'success' => 1,
        'message' => 'Ligas obtenidas correctamente',
        'data' => $ligas,
        'total' => count($ligas)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => 'Error al obtener ligas: ' . $e->getMessage()
    ]);
}
?>
