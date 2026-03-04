<?php
include('../../assets/php/template.php');
header('Content-Type: application/json');

try {
    $db = new Conexion();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
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
            'message' => 'Datos obtenidos de BD'
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

// Datos simulados según el ID
$miembros_simulados = [
    1 => [
        'ID' => 1,
        'NOMBRE' => 'Carlos Martínez López',
        'CARGO' => 'Secretario',
        'EMAIL' => 'carlos.martinez@club.com',
        'TELEFONO' => '+1 234-567-1001',
        'ESTADO' => 'activo',
        'OBSERVACIONES' => 'Secretario del club, maneja las actas'
    ],
    5 => [
        'ID' => 5,
        'NOMBRE' => 'Ana García Pérez',
        'CARGO' => 'Presidente',
        'EMAIL' => 'ana.garcia@club.com',
        'TELEFONO' => '+1 234-567-8901',
        'ESTADO' => 'activo',
        'OBSERVACIONES' => 'Presidente del club desde 2023'
    ],
    999 => [
        'ID' => 999,
        'NOMBRE' => 'Miembro de Prueba',
        'CARGO' => 'Presidente', 
        'EMAIL' => 'prueba@test.com',
        'TELEFONO' => '123456789',
        'ESTADO' => 'activo',
        'OBSERVACIONES' => 'Este es un miembro de prueba'
    ]
];

// Si existe el miembro simulado, devolverlo
if (isset($miembros_simulados[$id])) {
    echo json_encode([
        'success' => true,
        'data' => $miembros_simulados[$id],
        'message' => 'Datos obtenidos correctamente'
    ]);
} else {
    // Generar datos genéricos para cualquier ID
    echo json_encode([
        'success' => true,
        'data' => [
            'ID' => $id,
            'NOMBRE' => 'Usuario ID ' . $id,
            'CARGO' => 'Vocal',
            'EMAIL' => 'usuario' . $id . '@club.com',
            'TELEFONO' => '+1 555-000-' . str_pad($id, 4, '0', STR_PAD_LEFT),
            'ESTADO' => 'activo',
            'OBSERVACIONES' => 'Miembro generado automáticamente para ID ' . $id
        ],
        'message' => 'Datos simulados generados'
    ]);
}
?>
