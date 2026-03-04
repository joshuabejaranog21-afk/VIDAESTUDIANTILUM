<?php
header('Content-Type: application/json');
include('../db.php');

if (!security()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$db = new Conexion();

// Get filters
$search = isset($_GET['search']) ? $db->real_escape_string($_GET['search']) : '';
$decade = isset($_GET['decade']) ? intval($_GET['decade']) : 0;
$order = isset($_GET['order']) ? $_GET['order'] : 'recent';
$conmemorative = isset($_GET['conmemorative']) && $_GET['conmemorative'] === 'S' ? 'S' : '';

// Build query
$query = "SELECT * FROM VRE_ANUARIOS WHERE ACTIVO = 'S'";

// Apply filters
if (!empty($search)) {
    $query .= " AND (TITULO LIKE '%$search%' OR DESCRIPCION LIKE '%$search%')";
}

if ($decade > 0) {
    $query .= " AND DECADA = $decade";
}

if ($conmemorative === 'S') {
    $query .= " AND ES_CONMEMORATIVO = 'S'";
}

// Apply ordering
switch ($order) {
    case 'oldest':
        $query .= " ORDER BY ANIO ASC";
        break;
    case 'likes':
        $query .= " ORDER BY LIKES DESC";
        break;
    case 'views':
        $query .= " ORDER BY VISTAS DESC";
        break;
    case 'recent':
    default:
        $query .= " ORDER BY ANIO DESC";
        break;
}

$result = $db->query($query);

if ($result && $db->rows($result) > 0) {
    $anuarios = [];
    while ($row = $result->fetch_assoc()) {
        $anuarios[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $anuarios]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron anuarios']);
}
?>
