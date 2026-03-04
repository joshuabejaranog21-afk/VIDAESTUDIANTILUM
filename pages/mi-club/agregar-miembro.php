<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(0);
ini_set('display_errors', 0);

include('../../assets/php/template.php');

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
    
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $cargo = isset($_POST['cargo']) ? trim($_POST['cargo']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : 'activo';
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
    
    if (empty($nombre)) {
        throw new Exception('El nombre es obligatorio');
    }
    if (empty($cargo)) {
        throw new Exception('El cargo es obligatorio');
    }
    
    // Validar formato de email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de email inválido');
    }
    
    $db = new Conexion();
    $nombre = $db->real_escape_string($nombre);
    $cargo = $db->real_escape_string($cargo);
    $telefono = $db->real_escape_string($telefono);
    $email = $db->real_escape_string($email);
    $estado = $db->real_escape_string($estado);
    $observaciones = $db->real_escape_string($observaciones);
    
    $sql = "INSERT INTO VRE_DIRECTIVA_CLUBES 
            (ID_CLUB, NOMBRE, CARGO, EMAIL, TELEFONO, ESTADO, OBSERVACIONES, FECHA_REGISTRO)
            VALUES ($club_id, '$nombre', '$cargo', '$email', '$telefono', '$estado', '$observaciones', NOW())";
    
    if ($db->query($sql)) {
        $temp->registrar_auditoria('DIRECTIVA_CLUBES', 'CREAR', "Miembro $nombre agregado al club ID $club_id");
        $response['success'] = true;
        $response['message'] = 'Miembro agregado correctamente';
    } else {
        throw new Exception('Error al guardar en la base de datos');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
