<?php
include("../db.php");
header('Content-Type: application/json');
$db = new Conexion();
$info = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rol = isset($_POST['id_rol']) ? intval($_POST['id_rol']) : 0;
    $permisos_json = isset($_POST['permisos']) ? $_POST['permisos'] : '';

    if ($id_rol > 0 && !empty($permisos_json)) {
        $permisos = json_decode($permisos_json, true);

        // Iniciar transacción
        $db->begin_transaction();

        try {
            // Eliminar permisos actuales del rol
            $cad = "DELETE FROM SYSTEM_ROL_MODULO_PERMISOS WHERE ID_ROL = $id_rol";
            $db->query($cad);

            // Insertar nuevos permisos
            $valores = [];
            foreach ($permisos as $permiso) {
                $id_modulo = intval($permiso['id_modulo']);
                $id_permiso = intval($permiso['id_permiso']);
                $valores[] = "($id_rol, $id_modulo, $id_permiso)";
            }

            if (count($valores) > 0) {
                $cad = "INSERT INTO SYSTEM_ROL_MODULO_PERMISOS (ID_ROL, ID_MODULO, ID_PERMISO) VALUES " . implode(', ', $valores);
                $db->query($cad);
            }

            $db->commit();

            $info['success'] = 1;
            $info['message'] = 'Permisos actualizados correctamente';
        } catch (Exception $e) {
            $db->rollback();
            $info['success'] = 0;
            $info['message'] = 'Error al actualizar permisos: ' . $e->getMessage();
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'Datos incompletos';
    }
} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>
