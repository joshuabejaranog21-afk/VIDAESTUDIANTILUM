<?php
header('Content-Type: application/json');
require_once('../../API/db.php');

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        throw new Exception('Método no permitido');
    }
    
    $db = new Conexion();
    
    // Validar campos requeridos
    $id_deporte = isset($_POST['id_deporte']) ? (int)$_POST['id_deporte'] : null;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
    $requisitos = isset($_POST['requisitos']) ? trim($_POST['requisitos']) : null;
    $responsable_nombre = isset($_POST['responsable_nombre']) ? trim($_POST['responsable_nombre']) : null;
    $responsable_contacto = isset($_POST['responsable_contacto']) ? trim($_POST['responsable_contacto']) : null;
    $foto_responsable = isset($_POST['foto_responsable']) ? trim($_POST['foto_responsable']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $imagen_principal_url = isset($_POST['imagen_url']) ? trim($_POST['imagen_url']) : null; // Se guardará en VRE_GALERIA
    $activo = isset($_POST['activo']) ? $_POST['activo'] : 'S';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 'EN_PREPARACION';
    $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
    
    if (!$id_deporte || !$nombre) {
        throw new Exception('Deporte y nombre son requeridos');
    }
    
    // Validar que el deporte exista
    $check = $db->query("SELECT ID FROM VRE_DEPORTES WHERE ID = $id_deporte");
    if ($db->rows($check) == 0) {
        throw new Exception('El deporte especificado no existe');
    }
    
    // Validar email si se proporciona
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }
    
    // Escapar strings
    $nombre = $db->real_escape_string($nombre);
    $fecha_inicio = $fecha_inicio ? "'$fecha_inicio'" : 'NULL';
    $descripcion = $db->real_escape_string($descripcion);
    $requisitos = $db->real_escape_string($requisitos);
    $responsable_nombre = $db->real_escape_string($responsable_nombre);
    $responsable_contacto = $db->real_escape_string($responsable_contacto);
    $foto_responsable = $db->real_escape_string($foto_responsable);
    $email = $db->real_escape_string($email);
    $telefono = $db->real_escape_string($telefono);

    // Iniciar transacción
    $db->autocommit(false);

    // Insertar liga (sin IMAGEN_URL y GALERIA, ahora usa VRE_GALERIA)
    $query = "
        INSERT INTO VRE_LIGAS (
            ID_DEPORTE, NOMBRE, FECHA_INICIO, DESCRIPCION, REQUISITOS,
            RESPONSABLE_NOMBRE, RESPONSABLE_CONTACTO, FOTO_RESPONSABLE,
            EMAIL, TELEFONO, ACTIVO, ESTADO, ORDEN
        ) VALUES (
            $id_deporte, '$nombre', $fecha_inicio, '$descripcion', '$requisitos',
            '$responsable_nombre', '$responsable_contacto', '$foto_responsable',
            '$email', '$telefono', '$activo', '$estado', $orden
        )
    ";

    if (!$db->query($query)) {
        $db->rollback();
        throw new Exception($db->error);
    }

    $liga_id = $db->insert_id;

    // Si se proporcionó una imagen principal, guardarla en VRE_GALERIA
    if (!empty($imagen_principal_url)) {
        $imagen_url_escaped = $db->real_escape_string($imagen_principal_url);
        $titulo = $db->real_escape_string("$nombre - Principal");

        $cad_galeria = "INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                       VALUES ('ligas', $liga_id, '$titulo', '$imagen_url_escaped', 'principal', 1, 'S', 1)";

        if (!$db->query($cad_galeria)) {
            $db->rollback();
            throw new Exception('Error al registrar imagen en galería: ' . $db->error);
        }
    }

    // Confirmar transacción
    $db->commit();
    $db->autocommit(true);

    echo json_encode([
        'success' => 1,
        'message' => 'Liga creada correctamente',
        'id' => $liga_id
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
}
?>
