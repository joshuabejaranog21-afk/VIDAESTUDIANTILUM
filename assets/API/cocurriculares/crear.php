<?php
/**
 * API: Crear nuevo programa/servicio co-curricular
 * Requiere: nombre, tipo
 * Opcional: descripcion, objetivo, requisitos, beneficios, responsable_nombre, responsable_email, responsable_telefono, horarios, ubicacion, cupo_maximo, activo, orden
 * NOTA: Las imágenes se gestionan desde el módulo de Galería
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
if (!$temp->tiene_permiso('cocurriculares', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para crear co-curriculares';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar campos requeridos
    if (empty($_POST['nombre']) || empty($_POST['tipo'])) {
        $info['success'] = 0;
        $info['message'] = 'Faltan campos requeridos: nombre, tipo';
        echo json_encode($info);
        exit();
    }

    $nombre = $db->real_escape_string($_POST['nombre']);
    $tipo = $db->real_escape_string($_POST['tipo']);

    // Validar tipo
    $tipos_validos = ['PROGRAMA', 'SERVICIO', 'APOYO', 'OTRO'];
    if (!in_array($tipo, $tipos_validos)) {
        $info['success'] = 0;
        $info['message'] = 'Tipo inválido. Use: ' . implode(', ', $tipos_validos);
        echo json_encode($info);
        exit();
    }

    // Campos opcionales
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $objetivo = isset($_POST['objetivo']) ? $db->real_escape_string($_POST['objetivo']) : null;
    $requisitos = isset($_POST['requisitos']) ? $db->real_escape_string($_POST['requisitos']) : null;
    $beneficios = isset($_POST['beneficios']) ? $db->real_escape_string($_POST['beneficios']) : null;
    $responsable_nombre = isset($_POST['responsable_nombre']) ? $db->real_escape_string($_POST['responsable_nombre']) : null;
    $responsable_email = isset($_POST['responsable_email']) ? $db->real_escape_string($_POST['responsable_email']) : null;
    $responsable_telefono = isset($_POST['responsable_telefono']) ? $db->real_escape_string($_POST['responsable_telefono']) : null;
    $horarios = isset($_POST['horarios']) ? $db->real_escape_string($_POST['horarios']) : null;
    $ubicacion = isset($_POST['ubicacion']) ? $db->real_escape_string($_POST['ubicacion']) : null;
    $cupo_maximo = isset($_POST['cupo_maximo']) ? intval($_POST['cupo_maximo']) : null;
    $activo = isset($_POST['activo']) ? $db->real_escape_string($_POST['activo']) : 'S';
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;

    // Validar estado
    if (!in_array($activo, ['S', 'N'])) $activo = 'S';

    // Si orden es 0, calcular el siguiente disponible
    if ($orden == 0) {
        $max_orden = $db->query("SELECT MAX(ORDEN) as max_orden FROM VRE_COCURRICULARES");
        if ($max_orden) {
            $row = $max_orden->fetch_assoc();
            $orden = ($row['max_orden'] ?? 0) + 1;
        }
    }

    // Insertar en la base de datos
    $cad = "INSERT INTO VRE_COCURRICULARES
            (NOMBRE, TIPO, DESCRIPCION, OBJETIVO, REQUISITOS, BENEFICIOS,
             RESPONSABLE_NOMBRE, RESPONSABLE_EMAIL, RESPONSABLE_TELEFONO,
             HORARIOS, UBICACION, CUPO_MAXIMO, ACTIVO, ORDEN)
            VALUES
            ('$nombre', '$tipo', " .
            ($descripcion ? "'$descripcion'" : "NULL") . ", " .
            ($objetivo ? "'$objetivo'" : "NULL") . ", " .
            ($requisitos ? "'$requisitos'" : "NULL") . ", " .
            ($beneficios ? "'$beneficios'" : "NULL") . ", " .
            ($responsable_nombre ? "'$responsable_nombre'" : "NULL") . ", " .
            ($responsable_email ? "'$responsable_email'" : "NULL") . ", " .
            ($responsable_telefono ? "'$responsable_telefono'" : "NULL") . ", " .
            ($horarios ? "'$horarios'" : "NULL") . ", " .
            ($ubicacion ? "'$ubicacion'" : "NULL") . ", " .
            ($cupo_maximo ? "$cupo_maximo" : "NULL") . ", " .
            "'$activo', $orden)";

    $sql = $db->query($cad);

    if ($sql) {
        $id_insertado = $db->insert_id;

        // Registrar en auditoría
        $temp->registrar_auditoria('COCURRICULARES', 'CREAR', "Co-curricular creado: $nombre (ID: $id_insertado)");

        $info['success'] = 1;
        $info['message'] = 'Programa/Servicio creado correctamente';
        $info['id'] = $id_insertado;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al crear: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
