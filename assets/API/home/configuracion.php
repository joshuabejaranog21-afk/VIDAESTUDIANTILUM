<?php
header('Content-Type: application/json');
require_once('../../php/template.php');

$temp = new Template();
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    echo json_encode(['success' => 0, 'message' => 'No autorizado']);
    exit();
}

// Verificar permisos
if (!$temp->tiene_permiso('home', 'editar')) {
    echo json_encode(['success' => 0, 'message' => 'No tienes permisos para editar la configuración del home']);
    exit();
}

try {
    // Acción: Eliminar imagen
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar_imagen') {
        $seccion = $_POST['seccion'] ?? '';

        if ($seccion === 'hero') {
            // Obtener la imagen actual
            $sql = $db->query("SELECT VALOR FROM VRE_HOME_CONFIG WHERE SECCION = 'hero' AND CLAVE = 'imagen_fondo'");
            if ($db->rows($sql) > 0) {
                $row = $sql->fetch_assoc();
                $imagen = $row['VALOR'];

                // Eliminar archivo físico
                if (!empty($imagen) && file_exists('../../' . $imagen)) {
                    unlink('../../' . $imagen);
                }

                // Actualizar BD
                $db->query("UPDATE VRE_HOME_CONFIG SET VALOR = '' WHERE SECCION = 'hero' AND CLAVE = 'imagen_fondo'");
            }
        }

        echo json_encode(['success' => 1, 'message' => 'Imagen eliminada correctamente']);
        exit();
    }

    // Acción: Guardar configuración completa
    if (!isset($_POST['configuracion'])) {
        throw new Exception('No se recibieron datos de configuración');
    }

    $config = json_decode($_POST['configuracion'], true);
    if (!$config) {
        throw new Exception('Error al decodificar la configuración');
    }

    // Procesar cada configuración
    foreach ($config as $clave => $valor) {
        // Separar sección y clave
        $partes = explode('_', $clave, 2);
        if (count($partes) < 2) continue;

        $seccion = $partes[0];
        $campo = $partes[1];

        // Construir la clave completa
        $claveCompleta = $campo;

        // Escapar valores
        $valorEscapado = $db->real_escape_string($valor);
        $seccionEscapada = $db->real_escape_string($seccion);
        $claveEscapada = $db->real_escape_string($claveCompleta);

        // Actualizar
        $sql = "UPDATE VRE_HOME_CONFIG
                SET VALOR = '$valorEscapado'
                WHERE SECCION = '$seccionEscapada' AND CLAVE = '$claveEscapada'";

        $db->query($sql);
    }

    // Procesar imagen de fondo si existe
    if (isset($_FILES['hero_imagen_fondo']) && $_FILES['hero_imagen_fondo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['hero_imagen_fondo'];

        // Validar que sea imagen
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            throw new Exception('Formato de imagen no válido');
        }

        // Validar tamaño (máximo 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('La imagen no puede superar los 5MB');
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'hero_' . uniqid() . '.' . $extension;
        $rutaDestino = '../../uploads/' . $nombreArchivo;

        // Eliminar imagen anterior si existe
        $sql = $db->query("SELECT VALOR FROM VRE_HOME_CONFIG WHERE SECCION = 'hero' AND CLAVE = 'imagen_fondo'");
        if ($db->rows($sql) > 0) {
            $row = $sql->fetch_assoc();
            $imagenAnterior = $row['VALOR'];
            if (!empty($imagenAnterior) && file_exists('../../' . $imagenAnterior)) {
                unlink('../../' . $imagenAnterior);
            }
        }

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            // Guardar en BD
            $rutaBD = 'assets/uploads/' . $nombreArchivo;
            $db->query("UPDATE VRE_HOME_CONFIG SET VALOR = '$rutaBD' WHERE SECCION = 'hero' AND CLAVE = 'imagen_fondo'");
        } else {
            throw new Exception('Error al subir la imagen');
        }
    }

    echo json_encode([
        'success' => 1,
        'message' => 'Configuración guardada correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
}
?>
