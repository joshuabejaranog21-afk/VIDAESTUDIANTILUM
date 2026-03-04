<?php
header('Content-Type: application/json');
require_once('../../API/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método no permitido');
    }
    
    $db = new Conexion();
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    if (!$id) throw new Exception('ID requerido');
    
    // Verificar que la liga existe
    $check = $db->query("SELECT ID FROM VRE_LIGAS WHERE ID = $id");
    if ($db->rows($check) == 0) {
        throw new Exception('Liga no encontrada');
    }
    
    // Campos a actualizar
    $updates = [];
    
    if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
        $nombre = $db->real_escape_string(trim($_POST['nombre']));
        $updates[] = "NOMBRE = '$nombre'";
    }
    
    if (isset($_POST['fecha_inicio'])) {
        $fecha = trim($_POST['fecha_inicio']);
        $fecha = $fecha ? "'$fecha'" : 'NULL';
        $updates[] = "FECHA_INICIO = $fecha";
    }
    
    if (isset($_POST['descripcion'])) {
        $desc = $db->real_escape_string(trim($_POST['descripcion']));
        $updates[] = "DESCRIPCION = '$desc'";
    }
    
    if (isset($_POST['requisitos'])) {
        $req = $db->real_escape_string(trim($_POST['requisitos']));
        $updates[] = "REQUISITOS = '$req'";
    }
    
    if (isset($_POST['responsable_nombre'])) {
        $resp_nom = $db->real_escape_string(trim($_POST['responsable_nombre']));
        $updates[] = "RESPONSABLE_NOMBRE = '$resp_nom'";
    }
    
    if (isset($_POST['responsable_contacto'])) {
        $resp_cont = $db->real_escape_string(trim($_POST['responsable_contacto']));
        $updates[] = "RESPONSABLE_CONTACTO = '$resp_cont'";
    }
    
    if (isset($_POST['foto_responsable'])) {
        $foto = $db->real_escape_string(trim($_POST['foto_responsable']));
        $updates[] = "FOTO_RESPONSABLE = '$foto'";
    }
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }
        $email = $db->real_escape_string($email);
        $updates[] = "EMAIL = '$email'";
    }
    
    if (isset($_POST['telefono'])) {
        $tel = $db->real_escape_string(trim($_POST['telefono']));
        $updates[] = "TELEFONO = '$tel'";
    }
    
    if (isset($_POST['activo'])) {
        $activo = in_array($_POST['activo'], ['S', 'N']) ? $_POST['activo'] : 'S';
        $updates[] = "ACTIVO = '$activo'";
    }
    
    if (isset($_POST['estado'])) {
        $estado = in_array($_POST['estado'], ['EN_PREPARACION', 'EN_CURSO', 'PAUSADO', 'CANCELADO']) ? $_POST['estado'] : 'EN_PREPARACION';
        $updates[] = "ESTADO = '$estado'";
    }
    
    if (isset($_POST['orden'])) {
        $orden = (int)$_POST['orden'];
        $updates[] = "ORDEN = $orden";
    }

    // Nota: IMAGEN_URL y GALERIA ya no se manejan aquí, ahora se usan en VRE_GALERIA

    if (empty($updates)) {
        throw new Exception('No hay campos para actualizar');
    }
    
    $query = "UPDATE VRE_LIGAS SET " . implode(', ', $updates) . " WHERE ID = $id";
    
    if ($db->query($query)) {
        echo json_encode([
            'success' => 1,
            'message' => 'Liga actualizada correctamente'
        ]);
    } else {
        throw new Exception($db->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
}
?>
