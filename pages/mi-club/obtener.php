<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

try {
    $db = new Conexion();
    
    // Intentar obtener ID de múltiples fuentes
    $id = 0;
    
    // Opción 1: De URL path info (/obtener.php/123)
    if (isset($_SERVER['PATH_INFO'])) {
        $path_parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (!empty($path_parts[0]) && is_numeric($path_parts[0])) {
            $id = (int)$path_parts[0];
        }
    }
    
    // Opción 2: De POST
    if ($id == 0 && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
    }
    
    // Opción 3: De GET
    if ($id == 0 && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    }
    
    // Opción 4: De REQUEST_URI
    if ($id == 0) {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (preg_match('/obtener\.php\/(\d+)/', $uri, $matches)) {
            $id = (int)$matches[1];
        }
    }
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'ID requerido',
            'debug' => [
                'post' => $_POST,
                'get' => $_GET,
                'method' => $_SERVER['REQUEST_METHOD'],
                'path_info' => $_SERVER['PATH_INFO'] ?? 'NO_PATH_INFO',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'NO_REQUEST_URI'
            ]
        ]);
        exit;
    }
    
    // Consultar datos reales de la base de datos
    $sql = "SELECT * FROM VRE_DIRECTIVA_CLUBES WHERE ID = $id";
    $result = $db->query($sql);
    
    if ($result && $db->rows($result) > 0) {
        $miembro = $db->recorrer($result);
        echo json_encode([
            'success' => true,
            'data' => $miembro,
            'message' => 'Datos obtenidos correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Miembro no encontrado en BD',
            'debug' => ['id' => $id, 'sql' => $sql]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>