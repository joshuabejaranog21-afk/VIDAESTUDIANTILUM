<?php
/**
 * API: Editar instalación deportiva existente
 * Requiere: id
 * Opcional: Todos los demás campos
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
if (!$temp->tiene_permiso('instalaciones', 'editar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para editar instalaciones';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID de la instalación';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Verificar que la instalación existe
    $check = $db->query("SELECT * FROM VRE_INSTALACIONES_DEPORTIVAS WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'Instalación no encontrada';
        echo json_encode($info);
        exit();
    }

    // Construir UPDATE dinámico solo con campos enviados
    $updates = [];

    if (isset($_POST['nombre'])) {
        $nombre = $db->real_escape_string($_POST['nombre']);
        $updates[] = "NOMBRE = '$nombre'";
    }

    if (isset($_POST['tipo'])) {
        $tipo = $db->real_escape_string($_POST['tipo']);
        $tipos_validos = ['CANCHA', 'GYM', 'PISCINA', 'PISTA', 'OTRO'];
        if (in_array($tipo, $tipos_validos)) {
            $updates[] = "TIPO = '$tipo'";
        }
    }

    if (isset($_POST['descripcion'])) {
        $descripcion = $db->real_escape_string($_POST['descripcion']);
        $updates[] = "DESCRIPCION = " . ($descripcion ? "'$descripcion'" : "NULL");
    }

    if (isset($_POST['ubicacion'])) {
        $ubicacion = $db->real_escape_string($_POST['ubicacion']);
        $updates[] = "UBICACION = " . ($ubicacion ? "'$ubicacion'" : "NULL");
    }

    if (isset($_POST['coordenadas'])) {
        $coordenadas = $db->real_escape_string($_POST['coordenadas']);
        $updates[] = "COORDENADAS = " . ($coordenadas ? "'$coordenadas'" : "NULL");
    }

    if (isset($_POST['capacidad'])) {
        $capacidad = intval($_POST['capacidad']);
        $updates[] = "CAPACIDAD = " . ($capacidad > 0 ? "$capacidad" : "NULL");
    }

    if (isset($_POST['horarios'])) {
        $horarios = $db->real_escape_string($_POST['horarios']);
        $updates[] = "HORARIOS = " . ($horarios ? "'$horarios'" : "NULL");
    }

    if (isset($_POST['servicios'])) {
        $servicios = $db->real_escape_string($_POST['servicios']);
        $updates[] = "SERVICIOS = " . ($servicios ? "'$servicios'" : "NULL");
    }

    if (isset($_POST['reglas'])) {
        $reglas = $db->real_escape_string($_POST['reglas']);
        $updates[] = "REGLAS = " . ($reglas ? "'$reglas'" : "NULL");
    }

    if (isset($_POST['costo'])) {
        $costo = $db->real_escape_string($_POST['costo']);
        $updates[] = "COSTO = " . ($costo ? "'$costo'" : "NULL");
    }

    if (isset($_POST['disponible'])) {
        $disponible = $db->real_escape_string($_POST['disponible']);
        if (in_array($disponible, ['S', 'N'])) {
            $updates[] = "DISPONIBLE = '$disponible'";
        }
    }

    if (isset($_POST['activo'])) {
        $activo = $db->real_escape_string($_POST['activo']);
        if (in_array($activo, ['S', 'N'])) {
            $updates[] = "ACTIVO = '$activo'";
        }
    }

    if (isset($_POST['orden'])) {
        $orden = intval($_POST['orden']);
        $updates[] = "ORDEN = $orden";
    }

    // Si no hay nada que actualizar
    if (empty($updates)) {
        $info['success'] = 0;
        $info['message'] = 'No hay campos para actualizar';
        echo json_encode($info);
        exit();
    }

    // Ejecutar UPDATE
    $cad = "UPDATE VRE_INSTALACIONES_DEPORTIVAS SET " . implode(', ', $updates) . " WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql) {
        // Registrar en auditoría
        $temp->registrar_auditoria('INSTALACIONES', 'EDITAR', "Instalación editada ID: $id");

        $info['success'] = 1;
        $info['message'] = 'Instalación actualizada correctamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar instalación: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
