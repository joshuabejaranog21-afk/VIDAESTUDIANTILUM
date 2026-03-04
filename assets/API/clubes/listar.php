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
if (!$temp->tiene_permiso('clubes', 'ver')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para ver clubes';
    echo json_encode($info);
    exit();
}

try {
    $cad = "SELECT c.*,
                   u.NOMBRE as RESPONSABLE_NOMBRE,
                   u.EMAIL as RESPONSABLE_EMAIL,
                   d.DIRECTOR_NOMBRE,
                   d.DIRECTOR_EMAIL as DIRECTOR_EMAIL_DIRECTIVA
            FROM VRE_CLUBES c
            LEFT JOIN SYSTEM_USUARIOS u ON c.ID_DIRECTOR_USUARIO = u.ID
            LEFT JOIN VRE_DIRECTIVA_CLUBES d ON c.ID = d.ID_CLUB
            ORDER BY c.ORDEN ASC, c.NOMBRE ASC";
    $sql = $db->query($cad);

    $clubes = [];
    while($row = $sql->fetch_assoc()) {
        $club_id = $row['ID'];

        // Obtener imágenes desde VRE_GALERIA
        $imagenes_query = $db->query("
            SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
            FROM VRE_GALERIA
            WHERE MODULO = 'clubes'
            AND ID_REGISTRO = $club_id
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

        // Decodificar REDES_SOCIALES si existe
        $row['REDES_SOCIALES'] = $row['REDES_SOCIALES'] ? json_decode($row['REDES_SOCIALES']) : null;

        $clubes[] = $row;
    }

    $info['success'] = 1;
    $info['data'] = $clubes;
    $info['total'] = count($clubes);

} catch (Exception $e) {
    $info['success'] = 0;
    $info['message'] = 'Error al obtener clubes';
    if ($db->mostrarErrores) {
        $info['error'] = $e->getMessage();
    }
}

echo json_encode($info);
?>