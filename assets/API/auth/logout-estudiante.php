<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Destruir sesión del estudiante
if (isset($_SESSION['estudiante_logged'])) {
    // Limpiar variables de sesión del estudiante
    unset($_SESSION['estudiante_logged']);
    unset($_SESSION['estudiante_id']);
    unset($_SESSION['estudiante_matricula']);
    unset($_SESSION['estudiante_nombre']);
    unset($_SESSION['estudiante_apellido']);
    unset($_SESSION['estudiante_carrera']);
    unset($_SESSION['access_token']);
    unset($_SESSION['token_expires']);

    echo json_encode([
        'success' => true,
        'message' => 'Sesión cerrada exitosamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No hay sesión activa'
    ]);
}
?>
