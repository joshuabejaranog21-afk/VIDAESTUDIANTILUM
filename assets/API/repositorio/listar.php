<?php
session_start();
include("../db.php");
header('Content-Type: application/json');

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Validar que se haya enviado la matrícula
    if (!isset($_GET['matricula']) || empty($_GET['matricula'])) {
        echo json_encode(['success' => 0, 'message' => 'Matrícula es requerida']);
        exit();
    }

    $matricula = $db->real_escape_string($_GET['matricula']);

    // VALIDACIÓN DE ACCESO
    // 1. Si es admin del cpanel, puede ver cualquier repositorio (sin restricciones)
    if (security()) {
        // Es admin, puede acceder a todo
    }
    // 2. Si es estudiante logueado, solo puede ver su propio repositorio
    elseif (isset($_SESSION['estudiante_logged']) && $_SESSION['estudiante_logged'] === true) {
        // Es un estudiante logueado, validar que sea su matrícula
        if ($_SESSION['estudiante_matricula'] !== $matricula) {
            echo json_encode([
                'success' => 0,
                'message' => 'No tienes permiso para ver este repositorio. Solo puedes ver tus propias fotos.'
            ]);
            exit();
        }
    }
    // 3. Si no es ni admin ni estudiante logueado, denegar acceso
    else {
        echo json_encode([
            'success' => 0,
            'message' => 'Debes iniciar sesión para ver tu repositorio.'
        ]);
        exit();
    }

    // Verificar que el estudiante existe
    $cad = "SELECT * FROM VRE_ESTUDIANTES WHERE MATRICULA = '$matricula'";
    $sql = $db->query($cad);

    if ($db->rows($sql) == 0) {
        echo json_encode([
            'success' => 1,
            'message' => 'No se encontró el estudiante con esa matrícula',
            'estudiante' => null,
            'fotos' => []
        ]);
        exit();
    }

    $estudiante = $db->recorrer($sql);

    // Obtener todas las fotos del estudiante
    $cad = "SELECT f.*, e.NOMBRE, e.APELLIDO, e.CARRERA, e.SEMESTRE
            FROM VRE_REPOSITORIO_FOTOS f
            INNER JOIN VRE_ESTUDIANTES e ON f.ID_ESTUDIANTE = e.ID
            WHERE f.MATRICULA = '$matricula'
            AND f.ACTIVO = 'S'
            ORDER BY f.CICLO_ESCOLAR DESC, f.FECHA_SUBIDA DESC";

    $sql_fotos = $db->query($cad);

    $fotos = [];
    while ($row = $db->recorrer($sql_fotos)) {
        // Contar referencias en fotos grupales
        $id_foto = $row['ID'];
        $cad_ref = "SELECT COUNT(*) as total_referencias
                    FROM VRE_REPOSITORIO_REFERENCIAS
                    WHERE ID_FOTO = $id_foto";
        $sql_ref = $db->query($cad_ref);
        $row_ref = $db->recorrer($sql_ref);

        $fotos[] = [
            'id' => $row['ID'],
            'titulo' => $row['TITULO'],
            'descripcion' => $row['DESCRIPCION'],
            'foto_url' => $row['FOTO_URL'],
            'flickr_page_url' => isset($row['FLICKR_PAGE_URL']) ? $row['FLICKR_PAGE_URL'] : '',
            'tipo_foto' => $row['TIPO_FOTO'],
            'ciclo_escolar' => isset($row['CICLO_ESCOLAR']) ? $row['CICLO_ESCOLAR'] : '',
            'release_date' => isset($row['RELEASE_DATE']) ? $row['RELEASE_DATE'] : '',
            'fecha_subida' => $row['FECHA_SUBIDA'],
            'total_referencias' => $row_ref['total_referencias']
        ];
    }

    $info['success'] = 1;
    $info['estudiante'] = [
        'id' => $estudiante['ID'],
        'matricula' => $estudiante['MATRICULA'],
        'nombre' => $estudiante['NOMBRE'],
        'apellido' => $estudiante['APELLIDO'],
        'carrera' => $estudiante['CARRERA'],
        'semestre' => $estudiante['SEMESTRE']
    ];
    $info['fotos'] = $fotos;
    $info['total'] = count($fotos);

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
