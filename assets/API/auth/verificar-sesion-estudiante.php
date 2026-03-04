<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['estudiante_logged']) && $_SESSION['estudiante_logged'] === true) {
    // Verificar si el token no ha expirado
    if (isset($_SESSION['token_expires']) && time() > $_SESSION['token_expires']) {
        // Token expirado
        unset($_SESSION['estudiante_logged']);
        echo json_encode([
            'logged' => false,
            'message' => 'Sesión expirada'
        ]);
        exit();
    }

    echo json_encode([
        'logged' => true,
        'estudiante' => [
            'id' => $_SESSION['estudiante_id'] ?? null,
            'matricula' => $_SESSION['estudiante_matricula'] ?? '',
            'nombre' => $_SESSION['estudiante_nombre'] ?? '',
            'apellido' => $_SESSION['estudiante_apellido'] ?? '',
            'carrera' => $_SESSION['estudiante_carrera'] ?? '',
            'nombre_completo' => trim(($_SESSION['estudiante_nombre'] ?? '') . ' ' . ($_SESSION['estudiante_apellido'] ?? ''))
        ]
    ]);
} else {
    echo json_encode([
        'logged' => false,
        'message' => 'No hay sesión activa'
    ]);
}
?>
