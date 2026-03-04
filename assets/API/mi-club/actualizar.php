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

// Solo directores de club pueden usar esta API
if (!$temp->es_director_club()) {
    $info['success'] = 0;
    $info['message'] = 'Acceso denegado. Solo para directores de club.';
    echo json_encode($info);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Obtener información del club asignado
        $club = $temp->obtener_club_asignado();
        if (!$club) {
            throw new Exception('No tienes un club asignado');
        }

        $club_id = $club['ID'];
        
        // Validar que el club_id del formulario coincida
        $form_club_id = intval($_POST['club_id'] ?? 0);
        $club_id = intval($club_id); // Convertir a integer para comparación
        
        if ($form_club_id !== $club_id) {
            throw new Exception('No puedes editar este club');
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
        $responsable_nombre = $db->real_escape_string(trim($_POST['responsable_nombre'] ?? ''));
        $responsable_contacto = $db->real_escape_string(trim($_POST['responsable_contacto'] ?? ''));
        $email = $db->real_escape_string(trim($_POST['email'] ?? ''));
        $telefono = $db->real_escape_string(trim($_POST['telefono'] ?? ''));
        $requisitos = $db->real_escape_string(trim($_POST['requisitos'] ?? ''));
        $beneficios = $db->real_escape_string(trim($_POST['beneficios'] ?? ''));

        // Validar campos obligatorios
        if (empty($descripcion)) {
            throw new Exception('La descripción es obligatoria');
        }

        // Procesar nueva imagen si existe
        $imagen_url = $club['IMAGEN_URL']; // Mantener imagen actual por defecto
        $imagen_actualizada = false;

        if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $imageHelper->uploadImage($_FILES['nueva_imagen'], 'clubes', 'club', 800, 600);
            if (!$uploadResult['success']) {
                throw new Exception('Error al subir nueva imagen: ' . $uploadResult['message']);
            }

            // Eliminar imagen anterior si existe
            if (!empty($club['IMAGEN_URL'])) {
                $imageHelper->deleteImage($club['IMAGEN_URL']);
            }

            $imagen_url = $uploadResult['url'];
            $imagen_actualizada = true;
        }

        // Procesar galería de imágenes (JSON) y guardar en VRE_GALERIA
        $galeria = $club['GALERIA']; // Mantener galería actual por defecto para compatibilidad
        if (isset($_POST['galeria']) && !empty($_POST['galeria'])) {
            // La galería viene como JSON string desde el frontend
            $galeria_json = trim($_POST['galeria']);
            // Validar que sea JSON válido
            $galeria_array = json_decode($galeria_json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($galeria_array)) {
                $galeria = $db->real_escape_string($galeria_json);

                // Eliminar imágenes antiguas de tipo galería en VRE_GALERIA
                $db->query("DELETE FROM VRE_GALERIA WHERE MODULO = 'clubes' AND ID_REGISTRO = $club_id AND TIPO = 'galeria'");

                // Insertar nuevas imágenes en VRE_GALERIA
                $orden = 1;
                foreach ($galeria_array as $url_imagen) {
                    if (!empty($url_imagen)) {
                        $url_escaped = $db->real_escape_string($url_imagen);
                        $titulo_img = $club['NOMBRE'] . ' - Galería ' . $orden;
                        $db->query("INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                                   VALUES ('clubes', $club_id, '$titulo_img', '$url_escaped', 'galeria', $orden, 'S', {$temp->usuario_id})");
                        $orden++;
                    }
                }
            }
        }

        // Actualizar información del club
        // Ya validamos que el usuario tiene este club asignado en las líneas 30-43
        $sql = "UPDATE VRE_CLUBES SET
                    DESCRIPCION = '$descripcion',
                    OBJETIVO = '$objetivo',
                    HORARIO = '$horario',
                    DIA_REUNION = '$dia_reunion',
                    LUGAR = '$lugar',
                    CUPO_MAXIMO = " . ($cupo_maximo ? $cupo_maximo : 'NULL') . ",
                    IMAGEN_URL = '$imagen_url',
                    GALERIA = " . (!empty($galeria) ? "'$galeria'" : 'NULL') . ",
                    RESPONSABLE_NOMBRE = '$responsable_nombre',
                    RESPONSABLE_CONTACTO = '$responsable_contacto',
                    EMAIL = '$email',
                    TELEFONO = '$telefono',
                    REQUISITOS = '$requisitos',
                    BENEFICIOS = '$beneficios'
                WHERE ID = $club_id";

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
            // Validar que el usuario tiene este club asignado
            $check_sql = $db->query("SELECT COUNT(*) as total
                                     FROM VRE_CLUBES c
                                     INNER JOIN SYSTEM_USUARIOS u ON u.ID = " . $temp->usuario_id . "
                                     WHERE c.ID = $club_id
                                     AND u.ID_CLUB_ASIGNADO = c.ID");
            if ($check_sql) {
                $check_result = $check_sql->fetch_assoc();
                if ($check_result['total'] == 0) {
                    throw new Exception('No se encontró el club o no tienes permisos para editarlo');
                }
                // Si el club existe, probablemente no había cambios reales que hacer
            }
        }

        // Confirmar transacción
        $db->commit();
        $db->autocommit(true);

        // Registrar en auditoría
        $cambios_realizados = [];
        if ($descripcion !== $club['DESCRIPCION']) $cambios_realizados[] = 'descripción';
        if ($objetivo !== ($club['OBJETIVO'] ?? '')) $cambios_realizados[] = 'objetivo';
        if ($horario !== ($club['HORARIO'] ?? '')) $cambios_realizados[] = 'horario';
        if ($dia_reunion !== ($club['DIA_REUNION'] ?? '')) $cambios_realizados[] = 'día de reunión';
        if ($lugar !== ($club['LUGAR'] ?? '')) $cambios_realizados[] = 'lugar';
        if ($responsable_nombre !== ($club['RESPONSABLE_NOMBRE'] ?? '')) $cambios_realizados[] = 'nombre del responsable';

        $descripcion_cambios = count($cambios_realizados) > 0
            ? 'Campos actualizados: ' . implode(', ', $cambios_realizados)
            : 'Información actualizada';

        $temp->registrar_auditoria('CLUBES', 'EDITAR', "Club '{$club['NOMBRE']}' actualizado por director. $descripcion_cambios");

        // Preparar mensaje de respuesta
        $mensaje = 'Información del club actualizada exitosamente';

        $info['success'] = 1;
        $info['message'] = $mensaje;
        $info['cambios_realizados'] = count($cambios_realizados);
        $info['debug'] = [
            'imagen_actualizada' => $imagen_actualizada,
            'imagen_url_nueva' => $imagen_url,
            'imagen_url_anterior' => $club['IMAGEN_URL'],
            'file_received' => isset($_FILES['nueva_imagen']) ? 'SI' : 'NO',
            'file_error' => isset($_FILES['nueva_imagen']) ? $_FILES['nueva_imagen']['error'] : 'N/A'
        ];

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