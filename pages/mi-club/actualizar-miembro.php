<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);

$response = ['success' => false, 'message' => ''];

try {
    $temp = new Template();
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }
    if (!$temp->es_director_club()) {
        throw new Exception('Acceso denegado');
    }
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    $club_id = (int)$club['ID'];
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método POST requerido');
    }
    
    $db = new Conexion();
    
    // Obtener datos del formulario
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $cargo = isset($_POST['cargo']) ? trim($_POST['cargo']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    
    if ($id <= 0 || empty($nombre) || empty($cargo)) {
        throw new Exception('Datos requeridos faltantes');
    }
    
    // Validar que el miembro pertenece al club del director
    $check = $db->query("SELECT ID FROM VRE_DIRECTIVA_CLUBES WHERE ID = $id AND ID_CLUB = $club_id");
    if (!$check || $check->num_rows == 0) {
        throw new Exception('No tienes permiso para editar este miembro');
    }
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de email inválido');
    }
    
    // Escapar datos
    $nombre = $db->real_escape_string($nombre);
    $cargo = $db->real_escape_string($cargo);
    $telefono = $db->real_escape_string($telefono);
    $email = $db->real_escape_string($email);
    $estado = $db->real_escape_string($estado);
    $observaciones = $db->real_escape_string($observaciones);
    
    // Actualizar en la base de datos
    $sql = "UPDATE VRE_DIRECTIVA_CLUBES 
            SET NOMBRE = '$nombre', 
                CARGO = '$cargo', 
                TELEFONO = '$telefono', 
                EMAIL = '$email', 
                ESTADO = '$estado', 
                OBSERVACIONES = '$observaciones'
            WHERE ID = $id AND ID_CLUB = $club_id";
    
    if ($db->query($sql)) {
        $temp->registrar_auditoria('DIRECTIVA_CLUBES', 'EDITAR', "Miembro $nombre actualizado");
        $response['success'] = true;
        $response['message'] = 'Miembro actualizado correctamente';
    } else {
        throw new Exception('Error al actualizar');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
