<?php
session_start();
header('Content-Type: application/json');
include('../db.php');

$db = new Conexion();
$id_anuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_anuario === 0) {
    echo json_encode(['liked' => false, 'logged' => false]);
    exit();
}

// Get user info
$user_id = null;
$matricula = null;
$ip = $_SERVER['REMOTE_ADDR'];
$is_estudiante = false;
$is_logged = false;

// Verificar si es estudiante logueado
if (isset($_SESSION['estudiante_logged']) && $_SESSION['estudiante_logged'] === true) {
    $is_estudiante = true;
    $is_logged = true;
    $matricula = $_SESSION['estudiante_matricula'];
}
// Verificar si es admin
elseif (security()) {
    $is_logged = true;
    // Try to get user ID from session
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

// Check if liked
$liked = false;

if ($is_logged) {
    $check_query = "SELECT ID FROM VRE_ANUARIOS_LIKES WHERE ID_ANUARIO = $id_anuario";

    if ($is_estudiante && $matricula) {
        $matricula_safe = $db->real_escape_string($matricula);
        $check_query .= " AND MATRICULA = '$matricula_safe'";
    } elseif ($user_id) {
        $check_query .= " AND ID_USUARIO = $user_id";
    } else {
        $check_query .= " AND IP = '$ip'";
    }

    $check_result = $db->query($check_query);
    $liked = ($check_result && $db->rows($check_result) > 0);
}

echo json_encode([
    'liked' => $liked,
    'logged' => $is_logged
]);
?>
