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
if (!$temp->tiene_permiso('ministerios', 'editar')) {
    $info['success'] = 0;
    $info['message'] = 'No tienes permiso para editar ministerios';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $info['success'] = 0;
        $info['message'] = 'ID del ministerio no proporcionado';
        echo json_encode($info);
        exit();
    }

    $id = intval($_POST['id']);

    // Recibir datos (sin campos de imagen, ahora usa VRE_GALERIA)
    $nombre = $db->real_escape_string($_POST['nombre'] ?? '');
    $descripcion = $db->real_escape_string($_POST['descripcion'] ?? '');
    $objetivo = $db->real_escape_string($_POST['objetivo'] ?? '');
    $requisitos = $db->real_escape_string($_POST['requisitos'] ?? '');
    $beneficios = $db->real_escape_string($_POST['beneficios'] ?? '');
    $horario = $db->real_escape_string($_POST['horario'] ?? '');
    $dia_reunion = $db->real_escape_string($_POST['dia_reunion'] ?? '');
    $lugar = $db->real_escape_string($_POST['lugar'] ?? '');
    $telefono = $db->real_escape_string($_POST['telefono'] ?? '');
    $cupo_maximo = isset($_POST['cupo_maximo']) ? intval($_POST['cupo_maximo']) : null;
    $director_usuario = isset($_POST['director_usuario']) ? intval($_POST['director_usuario']) : null;
    $activo = $_POST['activo'] ?? 'S';

    // Validar campos obligatorios
    if (empty($nombre)) {
        $info['success'] = 0;
        $info['message'] = 'El nombre del ministerio es obligatorio';
        echo json_encode($info);
        exit();
    }

    try {
        // Construir el UPDATE sin campos de imagen (ahora usa VRE_GALERIA)
        $updates = [
            "NOMBRE = '$nombre'",
            "DESCRIPCION = '$descripcion'",
            "OBJETIVO = '$objetivo'",
            "REQUISITOS = '$requisitos'",
            "BENEFICIOS = '$beneficios'",
            "HORARIO = '$horario'",
            "DIA_REUNION = '$dia_reunion'",
            "LUGAR = '$lugar'",
            "TELEFONO = '$telefono'",
            "CUPO_MAXIMO = " . ($cupo_maximo ? $cupo_maximo : 'NULL'),
            "ID_DIRECTOR_USUARIO = " . ($director_usuario ? $director_usuario : 'NULL'),
            "ACTIVO = '$activo'"
        ];

        $cad = "UPDATE VRE_MINISTERIOS SET " . implode(", ", $updates) . " WHERE ID = $id";

        $sql = $db->query($cad);

        if ($sql) {
            // Si hay director, actualizar en directiva
            if ($director_usuario) {
                $result_usuario = $db->query("SELECT NOMBRE, EMAIL FROM SYSTEM_USUARIOS WHERE ID = $director_usuario");
                if ($result_usuario && $row_usuario = $result_usuario->fetch_assoc()) {
                    $director_nombre = $db->real_escape_string($row_usuario['NOMBRE']);
                    $director_email = $db->real_escape_string($row_usuario['EMAIL']);

                    $sql_update = "UPDATE VRE_DIRECTIVA_MINISTERIOS SET
                                DIRECTOR_NOMBRE = '$director_nombre',
                                DIRECTOR_EMAIL = '$director_email'
                                WHERE ID_MINISTERIO = $id";
                    $db->query($sql_update);
                }
            }

            // Registrar en auditoría
            $temp->registrar_auditoria('MINISTERIOS', 'EDITAR', "Ministerio editado: $nombre (ID: $id)");

            $info['success'] = 1;
            $info['message'] = 'Ministerio actualizado exitosamente';
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al actualizar el ministerio';
            $info['error'] = $db->error;
        }

    } catch (Exception $e) {
        $info['success'] = 0;
        $info['message'] = 'Error al actualizar el ministerio';
        $info['error'] = $e->getMessage();
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
