<?php
header('Content-Type: application/json');
session_start();

require_once '../../php/template.php';

$temp = new Template();
$db = new Conexion();
$response = ['success' => false, 'message' => ''];

try {
    
    // Verificar sesión y permisos
    if (!$temp->validate_session()) {
        throw new Exception('Sesión inválida');
    }
    
    if (!$temp->es_director_club()) {
        throw new Exception('Acceso denegado');
    }
    
    // Obtener el club asignado
    $club = $temp->obtener_club_asignado();
    if (!$club) {
        throw new Exception('No tienes un club asignado');
    }
    
    $club_id = intval($club['ID']);
    
    // Validar datos del formulario
    if (empty($_POST['nombre'])) {
        throw new Exception('El nombre es obligatorio');
    }
    
    if (empty($_POST['cargo'])) {
        throw new Exception('El cargo es obligatorio');
    }
    
    // Validar que el club_id del formulario coincida con el club asignado
    $form_club_id = intval($_POST['club_id']);
    if ($form_club_id !== $club_id) {
        throw new Exception('No tienes permiso para agregar miembros a este club');
    }
    
    $nombre = trim($_POST['nombre']);
    $cargo = trim($_POST['cargo']);
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $estado = !empty($_POST['estado']) ? trim($_POST['estado']) : 'activo';
    $observaciones = !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : null;
    
    // Validar longitudes
    if (strlen($nombre) > 100) {
        throw new Exception('El nombre no puede exceder 100 caracteres');
    }
    
    if (strlen($cargo) > 50) {
        throw new Exception('El cargo no puede exceder 50 caracteres');
    }
    
    if ($email && strlen($email) > 100) {
        throw new Exception('El email no puede exceder 100 caracteres');
    }
    
    if ($telefono && strlen($telefono) > 20) {
        throw new Exception('El teléfono no puede exceder 20 caracteres');
    }
    
    if ($observaciones && strlen($observaciones) > 500) {
        throw new Exception('Las observaciones no pueden exceder 500 caracteres');
    }
    
    // Validar email si se proporciona
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido');
    }
    
    // Validar estado
    if (!in_array($estado, ['activo', 'inactivo'])) {
        $estado = 'activo';
    }
    
    // Obtener conexión a la base de datos
    $conexion = new Conexion();
    $db = $conexion->obtenerConexion();
    
    // Verificar si ya existe un miembro con el mismo cargo (para cargos únicos)
    $cargos_unicos = ['Presidente', 'Vicepresidente', 'Secretario', 'Tesorero'];
    if (in_array($cargo, $cargos_unicos)) {
        $check_sql = "SELECT COUNT(*) as total FROM VRE_DIRECTIVA_CLUBES WHERE ID_CLUB = ? AND CARGO = ? AND ESTADO = 'activo'";
        $check_stmt = $db->prepare($check_sql);
        if ($check_stmt) {
            $check_stmt->bind_param("is", $club_id, $cargo);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result()->fetch_assoc();
            
            if ($check_result['total'] > 0) {
                throw new Exception("Ya existe un {$cargo} activo en la directiva");
            }
            $check_stmt->close();
        }
    }
    
    // Iniciar transacción
    $db->autocommit(false);
    
    try {
        // Insertar nuevo miembro
        $sql = "INSERT INTO VRE_DIRECTIVA_CLUBES (
                    ID_CLUB, 
                    NOMBRE, 
                    CARGO, 
                    EMAIL, 
                    TELEFONO, 
                    ESTADO, 
                    OBSERVACIONES,
                    FECHA_REGISTRO
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $db->error);
        }
        
        $stmt->bind_param("issssss", $club_id, $nombre, $cargo, $email, $telefono, $estado, $observaciones);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al insertar el miembro: ' . $stmt->error);
        }
        
        $nuevo_id = $db->insert_id;
        $stmt->close();
        
        // Registrar en auditoría
        $temp->registrar_auditoria('DIRECTIVA', 'CREAR', "Miembro agregado: {$nombre} ({$cargo})");
        
        // Confirmar transacción
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Miembro agregado correctamente';
        $response['data'] = ['id' => $nuevo_id];
        
    } catch (Exception $e) {
        // Revertir transacción
        $db->rollback();
        throw $e;
    }
    
    $db->close();
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    
    // Log del error
    try {
        $temp->registrar_auditoria('DIRECTIVA', 'ERROR', 'Error al agregar miembro: ' . $e->getMessage());
    } catch (Exception $audit_error) {
        // Error silencioso en auditoría
    }
}

echo json_encode($response);
?>