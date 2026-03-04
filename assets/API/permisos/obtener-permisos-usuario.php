<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id_rol = isset($_GET['id_rol']) ? intval($_GET['id_rol']) : 0;

    if ($id_rol > 0) {
        // Obtener todos los módulos
        $cad = "SELECT m.ID, m.NOMBRE, m.SLUG, m.DESCRIPCION, m.ICONO
                FROM SYSTEM_MODULOS m
                WHERE m.ACTIVO = 'S'
                ORDER BY m.ORDEN";

        $sql_modulos = $db->query($cad);
        $modulos = [];

        while($modulo = $sql_modulos->fetch_assoc()) {
            // Obtener permisos del rol para este módulo
            $cad_permisos = "SELECT p.ID, p.NOMBRE, p.SLUG,
                            (SELECT COUNT(*)
                             FROM SYSTEM_ROL_MODULO_PERMISOS rmp
                             WHERE rmp.ID_ROL = $id_rol
                             AND rmp.ID_MODULO = {$modulo['ID']}
                             AND rmp.ID_PERMISO = p.ID) as TIENE_PERMISO
                            FROM SYSTEM_PERMISOS p
                            ORDER BY p.ID";

            $sql_permisos = $db->query($cad_permisos);
            $permisos = [];

            while($permiso = $sql_permisos->fetch_assoc()) {
                $permisos[] = [
                    'id' => $permiso['ID'],
                    'nombre' => $permiso['NOMBRE'],
                    'slug' => $permiso['SLUG'],
                    'activo' => $permiso['TIENE_PERMISO'] > 0
                ];
            }

            $modulos[] = [
                'id' => $modulo['ID'],
                'nombre' => $modulo['NOMBRE'],
                'slug' => $modulo['SLUG'],
                'descripcion' => $modulo['DESCRIPCION'],
                'icono' => $modulo['ICONO'],
                'permisos' => $permisos
            ];
        }

        $info['success'] = 1;
        $info['data'] = $modulos;
    } else {
        $info['success'] = 0;
        $info['message'] = 'ID de rol no válido';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
