<?php
session_start();
include("../db.php");
header('Content-Type: application/json');

// Verificar que hay sesión iniciada (admin o estudiante)
if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida. Debes iniciar sesión.']);
    exit();
}

$db = new Conexion();
$info = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Obtener todos los estudiantes que tienen fotos
    $cad = "SELECT
                e.ID,
                e.MATRICULA,
                e.NOMBRE,
                e.APELLIDO,
                e.CARRERA,
                e.SEMESTRE,
                COUNT(f.ID) as TOTAL_FOTOS,
                MAX(f.FECHA_SUBIDA) as ULTIMA_FOTO,
                MIN(f.FOTO_URL) as FOTO_MUESTRA
            FROM VRE_ESTUDIANTES e
            INNER JOIN VRE_REPOSITORIO_FOTOS f ON e.ID = f.ID_ESTUDIANTE
            WHERE f.ACTIVO = 'S'
            GROUP BY e.ID, e.MATRICULA, e.NOMBRE, e.APELLIDO, e.CARRERA, e.SEMESTRE
            ORDER BY e.APELLIDO, e.NOMBRE";

    $sql = $db->query($cad);

    $estudiantes = [];
    while ($row = $db->recorrer($sql)) {
        $estudiantes[] = [
            'id' => $row['ID'],
            'matricula' => $row['MATRICULA'],
            'nombre' => $row['NOMBRE'],
            'apellido' => $row['APELLIDO'],
            'nombre_completo' => trim($row['NOMBRE'] . ' ' . $row['APELLIDO']),
            'carrera' => $row['CARRERA'],
            'semestre' => $row['SEMESTRE'],
            'total_fotos' => $row['TOTAL_FOTOS'],
            'ultima_foto' => $row['ULTIMA_FOTO'],
            'foto_muestra' => $row['FOTO_MUESTRA']
        ];
    }

    $info['success'] = 1;
    $info['estudiantes'] = $estudiantes;
    $info['total'] = count($estudiantes);
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
