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
    $check = $db->query("SELECT NOMBRE FROM VRE_LIGAS WHERE ID = $id");
    if ($db->rows($check) == 0) {
        throw new Exception('Liga no encontrada');
    }
    
    $liga = $db->recorrer($check);
    
    // Verificar si hay actividades asociadas
    $actividades = $db->query("SELECT COUNT(*) as cnt FROM VRE_DEPORTES_ACTIVIDADES WHERE ID_LIGA = $id");
    $act_row = $db->recorrer($actividades);

    if ($act_row['cnt'] > 0) {
        throw new Exception('No se puede eliminar la liga porque tiene actividades asociadas');
    }

    // Iniciar transacción
    $db->autocommit(false);

    // 1. Eliminar imágenes de VRE_GALERIA
    $db->query("DELETE FROM VRE_GALERIA WHERE MODULO = 'ligas' AND ID_REGISTRO = $id");

    // 2. Eliminar liga
    if ($db->query("DELETE FROM VRE_LIGAS WHERE ID = $id")) {
        $db->commit();
        $db->autocommit(true);

        echo json_encode([
            'success' => 1,
            'message' => 'Liga eliminada correctamente: ' . $liga['NOMBRE']
        ]);
    } else {
        $db->rollback();
        $db->autocommit(true);
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
