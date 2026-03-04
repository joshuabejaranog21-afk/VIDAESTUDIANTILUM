<?php
/**
 * API: Crear nueva instalación deportiva
 * Requiere: nombre, tipo
 * Opcional: descripcion, ubicacion, coordenadas, capacidad, horarios, servicios, reglas, costo, disponible, activo, orden
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
if (!$temp->tiene_permiso('instalaciones', 'crear')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para crear instalaciones';
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
    $tipos_validos = ['CANCHA', 'GYM', 'PISCINA', 'PISTA', 'OTRO'];
    if (!in_array($tipo, $tipos_validos)) {
        $info['success'] = 0;
        $info['message'] = 'Tipo inválido. Use: ' . implode(', ', $tipos_validos);
        echo json_encode($info);
        exit();
    }

    // Campos opcionales
    $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : null;
    $ubicacion = isset($_POST['ubicacion']) ? $db->real_escape_string($_POST['ubicacion']) : null;
    $coordenadas = isset($_POST['coordenadas']) ? $db->real_escape_string($_POST['coordenadas']) : null;
    $capacidad = isset($_POST['capacidad']) ? intval($_POST['capacidad']) : null;
    $horarios = isset($_POST['horarios']) ? $db->real_escape_string($_POST['horarios']) : null;
    $servicios = isset($_POST['servicios']) ? $db->real_escape_string($_POST['servicios']) : null;
    $reglas = isset($_POST['reglas']) ? $db->real_escape_string($_POST['reglas']) : null;
    $costo = isset($_POST['costo']) ? $db->real_escape_string($_POST['costo']) : null;
    $disponible = isset($_POST['disponible']) ? $db->real_escape_string($_POST['disponible']) : 'S';
    $activo = isset($_POST['activo']) ? $db->real_escape_string($_POST['activo']) : 'S';
    $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;

    // Validar estados
    if (!in_array($disponible, ['S', 'N'])) $disponible = 'S';
    if (!in_array($activo, ['S', 'N'])) $activo = 'S';

    // Si orden es 0, calcular el siguiente disponible
    if ($orden == 0) {
        $max_orden = $db->query("SELECT MAX(ORDEN) as max_orden FROM VRE_INSTALACIONES_DEPORTIVAS");
        if ($max_orden) {
            $row = $max_orden->fetch_assoc();
            $orden = ($row['max_orden'] ?? 0) + 1;
        }
    }

    // Insertar en la base de datos
    $cad = "INSERT INTO VRE_INSTALACIONES_DEPORTIVAS
            (NOMBRE, TIPO, DESCRIPCION, UBICACION, COORDENADAS, CAPACIDAD, HORARIOS, SERVICIOS, REGLAS, COSTO, DISPONIBLE, ACTIVO, ORDEN)
            VALUES
            ('$nombre', '$tipo', " .
            ($descripcion ? "'$descripcion'" : "NULL") . ", " .
            ($ubicacion ? "'$ubicacion'" : "NULL") . ", " .
            ($coordenadas ? "'$coordenadas'" : "NULL") . ", " .
            ($capacidad ? "$capacidad" : "NULL") . ", " .
            ($horarios ? "'$horarios'" : "NULL") . ", " .
            ($servicios ? "'$servicios'" : "NULL") . ", " .
            ($reglas ? "'$reglas'" : "NULL") . ", " .
            ($costo ? "'$costo'" : "NULL") . ", " .
            "'$disponible', '$activo', $orden)";

    $sql = $db->query($cad);

    if ($sql) {
        $id_insertado = $db->insert_id;

        // Registrar en auditoría
        $temp->registrar_auditoria('INSTALACIONES', 'CREAR', "Instalación creada: $nombre (ID: $id_insertado)");

        $info['success'] = 1;
        $info['message'] = 'Instalación creada correctamente';
        $info['id'] = $id_insertado;
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al crear instalación: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
