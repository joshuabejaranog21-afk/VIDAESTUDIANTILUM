<?php
header('Content-Type: application/json');
require_once('../../php/template.php');

$temp = new Template();
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    echo json_encode(['success' => 0, 'message' => 'No autorizado']);
    exit();
}

try {
    // ==========================================
    // LISTAR ESTADÍSTICAS
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'listar') {
        if (!$temp->tiene_permiso('home', 'ver')) {
            throw new Exception('No tienes permisos para ver las estadísticas');
        }

        $sql = $db->query("SELECT * FROM VRE_HOME_ESTADISTICAS ORDER BY ORDEN ASC, ID ASC");

        $estadisticas = [];
        if ($db->rows($sql) > 0) {
            while ($row = $sql->fetch_assoc()) {
                $estadisticas[] = $row;
            }
        }

        echo json_encode([
            'success' => 1,
            'data' => $estadisticas
        ]);
        exit();
    }

    // ==========================================
    // CREAR ESTADÍSTICA
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
        if (!$temp->tiene_permiso('home', 'crear')) {
            throw new Exception('No tienes permisos para crear estadísticas');
        }

        $titulo = $db->real_escape_string($_POST['titulo'] ?? '');
        $numero = $db->real_escape_string($_POST['numero'] ?? '');
        $icono = $db->real_escape_string($_POST['icono'] ?? 'fas fa-chart-bar');
        $color = $db->real_escape_string($_POST['color'] ?? '#667eea');

        if (empty($titulo) || empty($numero)) {
            throw new Exception('El título y el número son obligatorios');
        }

        // Obtener el siguiente orden
        $sqlOrden = $db->query("SELECT MAX(ORDEN) as max_orden FROM VRE_HOME_ESTADISTICAS");
        $rowOrden = $sqlOrden->fetch_assoc();
        $maxOrden = $rowOrden['max_orden'] ?? 0;
        $nuevoOrden = $maxOrden + 1;

        $sql = "INSERT INTO VRE_HOME_ESTADISTICAS (TITULO, NUMERO, ICONO, COLOR, ORDEN, ACTIVO)
                VALUES ('$titulo', '$numero', '$icono', '$color', $nuevoOrden, 'S')";

        $db->query($sql);

        echo json_encode([
            'success' => 1,
            'message' => 'Estadística creada correctamente',
            'id' => $db->insert_id
        ]);
        exit();
    }

    // ==========================================
    // ACTUALIZAR ESTADÍSTICA
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
        if (!$temp->tiene_permiso('home', 'editar')) {
            throw new Exception('No tienes permisos para editar estadísticas');
        }

        $id = intval($_POST['id'] ?? 0);
        $titulo = $db->real_escape_string($_POST['titulo'] ?? '');
        $numero = $db->real_escape_string($_POST['numero'] ?? '');
        $icono = $db->real_escape_string($_POST['icono'] ?? 'fas fa-chart-bar');
        $color = $db->real_escape_string($_POST['color'] ?? '#667eea');

        if (empty($id) || empty($titulo) || empty($numero)) {
            throw new Exception('Datos incompletos');
        }

        $sql = "UPDATE VRE_HOME_ESTADISTICAS
                SET TITULO = '$titulo',
                    NUMERO = '$numero',
                    ICONO = '$icono',
                    COLOR = '$color'
                WHERE ID = $id";

        $db->query($sql);

        echo json_encode([
            'success' => 1,
            'message' => 'Estadística actualizada correctamente'
        ]);
        exit();
    }

    // ==========================================
    // TOGGLE ACTIVO/INACTIVO
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'toggle') {
        if (!$temp->tiene_permiso('home', 'editar')) {
            throw new Exception('No tienes permisos para editar estadísticas');
        }

        $id = intval($_POST['id'] ?? 0);
        $activo = $db->real_escape_string($_POST['activo'] ?? 'S');

        if (empty($id)) {
            throw new Exception('ID no proporcionado');
        }

        $sql = "UPDATE VRE_HOME_ESTADISTICAS SET ACTIVO = '$activo' WHERE ID = $id";
        $db->query($sql);

        echo json_encode([
            'success' => 1,
            'message' => 'Estado actualizado'
        ]);
        exit();
    }

    // ==========================================
    // ELIMINAR ESTADÍSTICA
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        if (!$temp->tiene_permiso('home', 'eliminar')) {
            throw new Exception('No tienes permisos para eliminar estadísticas');
        }

        $id = intval($_POST['id'] ?? 0);

        if (empty($id)) {
            throw new Exception('ID no proporcionado');
        }

        $sql = "DELETE FROM VRE_HOME_ESTADISTICAS WHERE ID = $id";
        $db->query($sql);

        echo json_encode([
            'success' => 1,
            'message' => 'Estadística eliminada correctamente'
        ]);
        exit();
    }

    throw new Exception('Acción no válida');

} catch (Exception $e) {
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
}
?>
