<?php
header('Content-Type: application/json');
$info = [];
if (isset($_COOKIE['system_name'])) {
    unset($_COOKIE['system_name']);
    unset($_COOKIE['system_token']);
    setcookie('system_name', '', time() - 3600, "/"); // empty value and old timestamp
    setcookie('system_token', '', time() - 3600, "/"); // empty value and old timestamp

    $info['success'] = 1; 
    $info['message'] = 'Sesión cerrada';
} else {
    $info['success'] = 0;
    $info['message'] = 'No se detectó sesión en el sistema';
}
echo json_encode($info);
