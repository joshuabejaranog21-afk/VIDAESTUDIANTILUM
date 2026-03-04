<?php
session_start();
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$id_anuario = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_anuario === 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

// Get user info
$user_id = null;
$matricula = null;
$ip = $_SERVER['REMOTE_ADDR'];
$is_estudiante = false;
$is_admin = false;

// Verificar si es estudiante logueado
if (isset($_SESSION['estudiante_logged']) && $_SESSION['estudiante_logged'] === true) {
    $is_estudiante = true;
    $matricula = $_SESSION['estudiante_matricula'];
}
// Verificar si es admin
elseif (security()) {
    $is_admin = true;
    // Try to get user ID from session/cookies
    if (isset($_COOKIE['system_name']) && isset($_COOKIE['system_token'])) {
        $name = $db->real_escape_string($_COOKIE['system_name']);
        $token = $db->real_escape_string($_COOKIE['system_token']);
        $user_query = "SELECT ID FROM SYSTEM_USUARIOS WHERE NOMBRE = '$name' AND TOKEN = '$token' AND ACTIVO = 'S'";
        $user_result = $db->query($user_query);
        if ($user_result && $db->rows($user_result) > 0) {
            $user_data = $user_result->fetch_assoc();
            $user_id = $user_data['ID'];
        }
    }
}
// No está autenticado
else {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para dar like. <a href="../pages/login-estudiante.php">Iniciar sesión</a>']);
    exit();
}

// Check if already liked
$check_query = "SELECT ID FROM VRE_ANUARIOS_LIKES WHERE ID_ANUARIO = $id_anuario";

if ($is_estudiante && $matricula) {
    // Verificar por matrícula
    $matricula_safe = $db->real_escape_string($matricula);
    $check_query .= " AND MATRICULA = '$matricula_safe'";
} elseif ($user_id) {
    // Verificar por ID de usuario admin
    $check_query .= " AND ID_USUARIO = $user_id";
} else {
    // Verificar por IP (fallback)
    $check_query .= " AND IP = '$ip'";
}

$check_result = $db->query($check_query);

if ($check_result && $db->rows($check_result) > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya has dado like a este anuario']);
    exit();
}

// Add like
$matricula_value = $is_estudiante && $matricula ? "'$matricula'" : 'NULL';
$user_id_value = $user_id ? $user_id : 'NULL';

$insert_query = "INSERT INTO VRE_ANUARIOS_LIKES (ID_ANUARIO, ID_USUARIO, MATRICULA, IP)
                 VALUES ($id_anuario, $user_id_value, $matricula_value, '$ip')";

if ($db->query($insert_query)) {
    // Update counter
    $db->query("UPDATE VRE_ANUARIOS SET LIKES = LIKES + 1 WHERE ID = $id_anuario");

    // Get updated count
    $count_query = "SELECT LIKES FROM VRE_ANUARIOS WHERE ID = $id_anuario";
    $count_result = $db->query($count_query);
    $count_data = $count_result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'likes' => $count_data['LIKES'],
        'message' => '¡Gracias por tu like!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar like']);
}
?>
