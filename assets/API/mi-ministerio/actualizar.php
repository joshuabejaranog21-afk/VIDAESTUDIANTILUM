<?php
include('../../php/template.php');
include('../../php/ImageHelper.php');

header('Content-Type: application/json');
$temp = new Template();
$db = new Conexion();
$imageHelper = new ImageHelper();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    $info['success'] = 0;
    $info['message'] = 'Sesión inválida';
    echo json_encode($info);
    exit();
}

// Solo directores de ministerio pueden usar esta API
if (!$temp->es_director_ministerio()) {
    $info['success'] = 0;
    $info['message'] = 'Acceso denegado. Solo para directores de ministerio.';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener información del ministerio asignado
        $ministerio = $temp->obtener_ministerio_asignado();
        if (!$ministerio) {
            throw new Exception('No tienes un ministerio asignado');
        }

        $ministerio_id = $ministerio['ID'];
        
        // Validar que el ministerio_id del formulario coincida
        $form_ministerio_id = intval($_POST['ministerio_id'] ?? 0);
        $ministerio_id = intval($ministerio_id); // Convertir a integer para comparación
        
        if ($form_ministerio_id !== $ministerio_id) {
            throw new Exception('No puedes editar este ministerio');
        }

        // Iniciar transacción
        $db->autocommit(false);

        // Recibir y validar datos
        $descripcion = $db->real_escape_string(trim($_POST['descripcion'] ?? ''));
        $objetivo = $db->real_escape_string(trim($_POST['objetivo'] ?? ''));
        $horario = $db->real_escape_string(trim($_POST['horario'] ?? ''));
        $dia_reunion = $db->real_escape_string(trim($_POST['dia_reunion'] ?? ''));
        $lugar = $db->real_escape_string(trim($_POST['lugar'] ?? ''));
        $cupo_maximo = !empty($_POST['cupo_maximo']) ? intval($_POST['cupo_maximo']) : null;
        $telefono = $db->real_escape_string(trim($_POST['telefono'] ?? ''));
        $requisitos = $db->real_escape_string(trim($_POST['requisitos'] ?? ''));
        $beneficios = $db->real_escape_string(trim($_POST['beneficios'] ?? ''));
        $estado = $db->real_escape_string($_POST['estado'] ?? 'BORRADOR');

        // Validar campos obligatorios
        if (empty($descripcion)) {
            throw new Exception('La descripción es obligatoria');
        }

        // No validar estado para ministerios (los directores no pueden cambiar estado)

        // Procesar nueva imagen si existe
        $imagen_url = $ministerio['IMAGEN_URL']; // Mantener imagen actual por defecto
        if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $imageHelper->uploadImage($_FILES['nueva_imagen'], 'ministerios', 'ministerio', 800, 600);
            if (!$uploadResult['success']) {
                throw new Exception('Error al subir nueva imagen: ' . $uploadResult['message']);
            }
            
            // Eliminar imagen anterior si existe
            if (!empty($ministerio['IMAGEN_URL'])) {
                $imageHelper->deleteImage($ministerio['IMAGEN_URL']);
            }
            
            $imagen_url = $uploadResult['url'];
        }

        // Procesar galería de imágenes (JSON)
        $galeria = $ministerio['GALERIA']; // Mantener galería actual por defecto
        if (isset($_POST['galeria']) && !empty($_POST['galeria'])) {
            // La galería viene como JSON string desde el frontend
            $galeria_json = trim($_POST['galeria']);
            // Validar que sea JSON válido
            $galeria_array = json_decode($galeria_json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($galeria_array)) {
                $galeria = $db->real_escape_string($galeria_json);
            }
        }

        // Actualizar información del ministerio
        $sql = "UPDATE VRE_MINISTERIOS SET
                    DESCRIPCION = '$descripcion',
                    OBJETIVO = '$objetivo',
                    HORARIO = '$horario',
                    DIA_REUNION = '$dia_reunion',
                    LUGAR = '$lugar',
                    CUPO_MAXIMO = " . ($cupo_maximo ? $cupo_maximo : 'NULL') . ",
                    IMAGEN_URL = '$imagen_url',
                    GALERIA = " . (!empty($galeria) ? "'$galeria'" : 'NULL') . ",
                    TELEFONO = '$telefono',
                    REQUISITOS = '$requisitos',
                    BENEFICIOS = '$beneficios',
                    ESTADO = '$estado'
                WHERE ID = $ministerio_id
                AND ID_DIRECTOR_USUARIO = " . $temp->usuario_id;

        if (!$db->query($sql)) {
            throw new Exception('Error al actualizar el club: ' . $db->error);
        }

        // Verificar que el query se ejecutó (comentar temporalmente la validación de affected_rows)
        // La validación de affected_rows puede fallar si no hay cambios reales
        /*
        if ($db->affected_rows === 0) {
            throw new Exception('No se pudo actualizar el club. Verifica tus permisos.');
        }
        */
        
        // Verificar que se actualizó correctamente (restaurar validación)
        if ($db->affected_rows === 0) {
            // Verificar si es porque no hay cambios reales
            $check_sql = $db->query("SELECT COUNT(*) as total FROM VRE_MINISTERIOS WHERE ID = $ministerio_id AND ID_DIRECTOR_USUARIO = " . $temp->usuario_id);
            if ($check_sql) {
                $check_result = $check_sql->fetch_assoc();
                if ($check_result['total'] == 0) {
                    throw new Exception('No se encontró el ministerio o no tienes permisos para editarlo');
                }
                // Si el ministerio existe, probablemente no había cambios reales que hacer
            }
        }

        // Confirmar transacción
        $db->commit();
        $db->autocommit(true);

        // Registrar en auditoría
        $cambios_realizados = [];
        if ($descripcion !== $ministerio['DESCRIPCION']) $cambios_realizados[] = 'descripción';
        if ($objetivo !== ($ministerio['OBJETIVO'] ?? '')) $cambios_realizados[] = 'objetivo';
        if ($horario !== ($ministerio['HORARIO'] ?? '')) $cambios_realizados[] = 'horario';
        if ($dia_reunion !== ($ministerio['DIA_REUNION'] ?? '')) $cambios_realizados[] = 'día de reunión';
        if ($lugar !== ($ministerio['LUGAR'] ?? '')) $cambios_realizados[] = 'lugar';
        
        $descripcion_cambios = count($cambios_realizados) > 0 
            ? 'Campos actualizados: ' . implode(', ', $cambios_realizados)
            : 'Información actualizada';
            
        $temp->registrar_auditoria('MINISTERIOS', 'EDITAR', "Ministerio '{$ministerio['NOMBRE']}' actualizado por director. $descripcion_cambios");

        // Preparar mensaje de respuesta
        $mensaje = 'Información del ministerio actualizada exitosamente';

        $info['success'] = 1;
        $info['message'] = $mensaje;
        $info['cambios_realizados'] = count($cambios_realizados);

    } catch (Exception $e) {
        // Rollback en caso de error
        $db->rollback();
        $db->autocommit(true);

        $info['success'] = 0;
        $info['message'] = $e->getMessage();

        // Debug info solo en desarrollo
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $info['debug'] = [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }
    }

} else {
    $info['success'] = 0;
    $info['message'] = 'Método no permitido';
}

echo json_encode($info);
?>