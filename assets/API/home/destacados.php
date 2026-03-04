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
    // LISTAR ELEMENTOS DISPONIBLES
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'listar') {
        if (!$temp->tiene_permiso('home', 'ver')) {
            throw new Exception('No tienes permisos');
        }

        $tipo = $_GET['tipo'] ?? '';
        if (empty($tipo) || !in_array($tipo, ['club', 'ministerio', 'evento'])) {
            throw new Exception('Tipo no válido');
        }

        $items = [];

        if ($tipo === 'club') {
            $sql = "SELECT
                        c.ID,
                        c.NOMBRE,
                        c.DESCRIPCION,
                        c.IMAGEN,
                        CASE WHEN d.ID IS NOT NULL THEN 'S' ELSE 'N' END as DESTACADO
                    FROM VRE_CLUBES c
                    LEFT JOIN VRE_HOME_DESTACADOS d ON d.TIPO = 'club' AND d.ID_REGISTRO = c.ID AND d.ACTIVO = 'S'
                    WHERE c.ACTIVO = 'S'
                    ORDER BY c.NOMBRE ASC";

            $result = $db->query($sql);
            if ($db->rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
        } elseif ($tipo === 'ministerio') {
            $sql = "SELECT
                        m.ID,
                        m.NOMBRE,
                        m.DESCRIPCION,
                        m.IMAGEN,
                        CASE WHEN d.ID IS NOT NULL THEN 'S' ELSE 'N' END as DESTACADO
                    FROM VRE_MINISTERIOS m
                    LEFT JOIN VRE_HOME_DESTACADOS d ON d.TIPO = 'ministerio' AND d.ID_REGISTRO = m.ID AND d.ACTIVO = 'S'
                    WHERE m.ACTIVO = 'S'
                    ORDER BY m.NOMBRE ASC";

            $result = $db->query($sql);
            if ($db->rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
        } elseif ($tipo === 'evento') {
            $sql = "SELECT
                        e.ID,
                        e.NOMBRE,
                        e.DESCRIPCION,
                        e.IMAGEN,
                        CASE WHEN d.ID IS NOT NULL THEN 'S' ELSE 'N' END as DESTACADO
                    FROM VRE_EVENTOS e
                    LEFT JOIN VRE_HOME_DESTACADOS d ON d.TIPO = 'evento' AND d.ID_REGISTRO = e.ID AND d.ACTIVO = 'S'
                    WHERE e.ACTIVO = 'S'
                    ORDER BY e.FECHA_INICIO DESC";

            $result = $db->query($sql);
            if ($db->rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
        }

        echo json_encode([
            'success' => 1,
            'data' => $items
        ]);
        exit();
    }

    // ==========================================
    // AGREGAR A DESTACADOS
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
        if (!$temp->tiene_permiso('home', 'editar')) {
            throw new Exception('No tienes permisos para editar');
        }

        $tipo = $db->real_escape_string($_POST['tipo'] ?? '');
        $idRegistro = intval($_POST['id_registro'] ?? 0);

        if (empty($tipo) || empty($idRegistro)) {
            throw new Exception('Datos incompletos');
        }

        if (!in_array($tipo, ['club', 'ministerio', 'evento'])) {
            throw new Exception('Tipo no válido');
        }

        // Verificar si ya existe
        $sqlCheck = "SELECT ID FROM VRE_HOME_DESTACADOS
                     WHERE TIPO = '$tipo' AND ID_REGISTRO = $idRegistro";
        $resultCheck = $db->query($sqlCheck);

        if ($db->rows($resultCheck) > 0) {
            // Ya existe, solo activarlo
            $sqlUpdate = "UPDATE VRE_HOME_DESTACADOS
                          SET ACTIVO = 'S'
                          WHERE TIPO = '$tipo' AND ID_REGISTRO = $idRegistro";
            $db->query($sqlUpdate);
        } else {
            // Obtener el siguiente orden
            $sqlOrden = "SELECT MAX(ORDEN) as max_orden FROM VRE_HOME_DESTACADOS WHERE TIPO = '$tipo'";
            $resultOrden = $db->query($sqlOrden);
            $rowOrden = $resultOrden->fetch_assoc();
            $maxOrden = $rowOrden['max_orden'] ?? 0;
            $nuevoOrden = $maxOrden + 1;

            // Insertar nuevo
            $sqlInsert = "INSERT INTO VRE_HOME_DESTACADOS (TIPO, ID_REGISTRO, ORDEN, ACTIVO)
                          VALUES ('$tipo', $idRegistro, $nuevoOrden, 'S')";
            $db->query($sqlInsert);
        }

        echo json_encode([
            'success' => 1,
            'message' => 'Agregado a destacados'
        ]);
        exit();
    }

    // ==========================================
    // QUITAR DE DESTACADOS
    // ==========================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'quitar') {
        if (!$temp->tiene_permiso('home', 'editar')) {
            throw new Exception('No tienes permisos para editar');
        }

        $tipo = $db->real_escape_string($_POST['tipo'] ?? '');
        $idRegistro = intval($_POST['id_registro'] ?? 0);

        if (empty($tipo) || empty($idRegistro)) {
            throw new Exception('Datos incompletos');
        }

        $sql = "UPDATE VRE_HOME_DESTACADOS
                SET ACTIVO = 'N'
                WHERE TIPO = '$tipo' AND ID_REGISTRO = $idRegistro";

        $db->query($sql);

        echo json_encode([
            'success' => 1,
            'message' => 'Quitado de destacados'
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
