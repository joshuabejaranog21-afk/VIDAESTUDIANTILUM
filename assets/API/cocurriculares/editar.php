<?php
/**
 * API: Editar co-curricular existente
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
if (!$temp->tiene_permiso('cocurriculares', 'editar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para editar co-curriculares';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'Falta el ID';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Verificar que existe
    $check = $db->query("SELECT * FROM VRE_COCURRICULARES WHERE ID = $id");
    if (!$check || $check->num_rows == 0) {
        $info['success'] = 0;
        $info['message'] = 'No encontrado';
        echo json_encode($info);
        exit();
    }

    // Construir UPDATE dinámico
    $updates = [];

    if (isset($_POST['nombre'])) {
        $nombre = $db->real_escape_string($_POST['nombre']);
        $updates[] = "NOMBRE = '$nombre'";
    }

    if (isset($_POST['tipo'])) {
        $tipo = $db->real_escape_string($_POST['tipo']);
        $tipos_validos = ['PROGRAMA', 'SERVICIO', 'APOYO', 'OTRO'];
        if (in_array($tipo, $tipos_validos)) {
            $updates[] = "TIPO = '$tipo'";
        }
    }

    if (isset($_POST['descripcion'])) {
        $descripcion = $db->real_escape_string($_POST['descripcion']);
        $updates[] = "DESCRIPCION = " . ($descripcion ? "'$descripcion'" : "NULL");
    }

    if (isset($_POST['objetivo'])) {
        $objetivo = $db->real_escape_string($_POST['objetivo']);
        $updates[] = "OBJETIVO = " . ($objetivo ? "'$objetivo'" : "NULL");
    }

    if (isset($_POST['requisitos'])) {
        $requisitos = $db->real_escape_string($_POST['requisitos']);
        $updates[] = "REQUISITOS = " . ($requisitos ? "'$requisitos'" : "NULL");
    }

    if (isset($_POST['beneficios'])) {
        $beneficios = $db->real_escape_string($_POST['beneficios']);
        $updates[] = "BENEFICIOS = " . ($beneficios ? "'$beneficios'" : "NULL");
    }

    if (isset($_POST['responsable_nombre'])) {
        $responsable_nombre = $db->real_escape_string($_POST['responsable_nombre']);
        $updates[] = "RESPONSABLE_NOMBRE = " . ($responsable_nombre ? "'$responsable_nombre'" : "NULL");
    }

    if (isset($_POST['responsable_email'])) {
        $responsable_email = $db->real_escape_string($_POST['responsable_email']);
        $updates[] = "RESPONSABLE_EMAIL = " . ($responsable_email ? "'$responsable_email'" : "NULL");
    }

    if (isset($_POST['responsable_telefono'])) {
        $responsable_telefono = $db->real_escape_string($_POST['responsable_telefono']);
        $updates[] = "RESPONSABLE_TELEFONO = " . ($responsable_telefono ? "'$responsable_telefono'" : "NULL");
    }

    if (isset($_POST['horarios'])) {
        $horarios = $db->real_escape_string($_POST['horarios']);
        $updates[] = "HORARIOS = " . ($horarios ? "'$horarios'" : "NULL");
    }

    if (isset($_POST['ubicacion'])) {
        $ubicacion = $db->real_escape_string($_POST['ubicacion']);
        $updates[] = "UBICACION = " . ($ubicacion ? "'$ubicacion'" : "NULL");
    }

    if (isset($_POST['cupo_maximo'])) {
        $cupo_maximo = intval($_POST['cupo_maximo']);
        $updates[] = "CUPO_MAXIMO = " . ($cupo_maximo > 0 ? "$cupo_maximo" : "NULL");
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
    $cad = "UPDATE VRE_COCURRICULARES SET " . implode(', ', $updates) . " WHERE ID = $id";
    $sql = $db->query($cad);

    if ($sql) {
        // Registrar en auditoría
        $temp->registrar_auditoria('COCURRICULARES', 'EDITAR', "Editado ID: $id");

        $info['success'] = 1;
        $info['message'] = 'Actualizado correctamente';
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar: ' . $db->error;
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido. Use POST.';
}

echo json_encode($info);
?>
