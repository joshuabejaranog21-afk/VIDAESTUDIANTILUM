<?php
error_reporting(0);
ini_set('display_errors', 0);

/**
 * API: Acciones de galería para el director de club
 * Maneja: toggle, eliminar, reordenar, subir_principal
 * Solo accesible por directores de club, limitado a su propio club
 */

include("../../php/template.php");

header('Content-Type: application/json');

$temp = new Template();
$db   = new Conexion();
$info = [];

// Validar sesión
if (!$temp->validate_session()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión inválida']);
    exit();
}

// Validar que es director de club
if (!$temp->es_director_club()) {
    echo json_encode(['success' => 0, 'message' => 'Acceso denegado']);
    exit();
}

// Obtener el club asignado al director
$club = $temp->obtener_club_asignado();
if (!$club) {
    echo json_encode(['success' => 0, 'message' => 'No tienes un club asignado']);
    exit();
}

$club_id = intval($club['ID']);
$accion  = $_POST['accion'] ?? '';

// -------------------------------------------------
// ACCIÓN: toggle - Activar/desactivar imagen
// -------------------------------------------------
if ($accion === 'toggle') {
    $id     = intval($_POST['id'] ?? 0);
    $activo = $_POST['activo'] ?? '';

    if (!$id || !in_array($activo, ['S', 'N'])) {
        echo json_encode(['success' => 0, 'message' => 'Datos inválidos']);
        exit();
    }

    // Verificar que la imagen pertenece al club del director
    $check = $db->query("SELECT ID FROM VRE_GALERIA WHERE ID = $id AND MODULO = 'clubes' AND ID_REGISTRO = $club_id");
    if (!$check || $check->num_rows === 0) {
        echo json_encode(['success' => 0, 'message' => 'Imagen no encontrada o sin acceso']);
        exit();
    }

    $sql = $db->query("UPDATE VRE_GALERIA SET ACTIVO = '$activo' WHERE ID = $id");
    if ($sql) {
        $temp->registrar_auditoria('GALERIA', 'TOGGLE', "Imagen ID $id -> ACTIVO = $activo");
        echo json_encode(['success' => 1, 'message' => $activo === 'S' ? 'Imagen activada' : 'Imagen desactivada']);
    } else {
        echo json_encode(['success' => 0, 'message' => 'Error al actualizar']);
    }
    exit();
}

// -------------------------------------------------
// ACCIÓN: eliminar - Eliminar imagen de galería
// -------------------------------------------------
if ($accion === 'eliminar') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => 0, 'message' => 'ID inválido']);
        exit();
    }

    $check = $db->query("SELECT ID, TIPO FROM VRE_GALERIA WHERE ID = $id AND MODULO = 'clubes' AND ID_REGISTRO = $club_id");
    if (!$check || $check->num_rows === 0) {
        echo json_encode(['success' => 0, 'message' => 'Imagen no encontrada o sin acceso']);
        exit();
    }

    $sql = $db->query("DELETE FROM VRE_GALERIA WHERE ID = $id");
    if ($sql) {
        $temp->registrar_auditoria('GALERIA', 'ELIMINAR', "Imagen ID $id eliminada del club $club_id");
        echo json_encode(['success' => 1, 'message' => 'Imagen eliminada']);
    } else {
        echo json_encode(['success' => 0, 'message' => 'Error al eliminar']);
    }
    exit();
}

// -------------------------------------------------
// ACCIÓN: reordenar - Guardar nuevo orden
// -------------------------------------------------
if ($accion === 'reordenar') {
    $imagenes_json = $_POST['imagenes'] ?? '';
    $imagenes = json_decode($imagenes_json, true);

    if (!is_array($imagenes) || empty($imagenes)) {
        echo json_encode(['success' => 0, 'message' => 'Datos de orden inválidos']);
        exit();
    }

    $db->begin_transaction();
    try {
        foreach ($imagenes as $item) {
            $id    = intval($item['id']);
            $orden = intval($item['orden']);
            // Solo actualizar si la imagen pertenece al club
            $db->query("UPDATE VRE_GALERIA SET ORDEN = $orden WHERE ID = $id AND MODULO = 'clubes' AND ID_REGISTRO = $club_id");
        }
        $db->commit();
        $temp->registrar_auditoria('GALERIA', 'REORDENAR', "Reordenó galería del club $club_id");
        echo json_encode(['success' => 1, 'message' => 'Orden guardado']);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => 0, 'message' => 'Error al guardar orden']);
    }
    exit();
}

// -------------------------------------------------
// ACCIÓN: subir_galeria - Registrar imagen en VRE_GALERIA
// (La imagen ya fue subida por upload.php)
// -------------------------------------------------
if ($accion === 'subir_galeria') {
    $url   = trim($_POST['url'] ?? '');
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo  = $_POST['tipo'] ?? 'galeria';

    if (empty($url)) {
        echo json_encode(['success' => 0, 'message' => 'URL inválida']);
        exit();
    }

    if (!in_array($tipo, ['galeria', 'principal'])) {
        $tipo = 'galeria';
    }

    $url_escaped   = $db->real_escape_string($url);
    $titulo_escaped = $db->real_escape_string($titulo ?: $club['NOMBRE'] . ' - Foto');

    // Si es principal, desactivar la anterior
    if ($tipo === 'principal') {
        $db->query("UPDATE VRE_GALERIA SET ACTIVO = 'N' WHERE MODULO = 'clubes' AND ID_REGISTRO = $club_id AND TIPO = 'principal'");
    }

    // Obtener el siguiente orden
    $orden_q = $db->query("SELECT COALESCE(MAX(ORDEN), 0) + 1 AS siguiente FROM VRE_GALERIA WHERE MODULO = 'clubes' AND ID_REGISTRO = $club_id AND TIPO = '$tipo'");
    $orden   = $orden_q ? intval($orden_q->fetch_assoc()['siguiente']) : 1;

    $sql = $db->query("INSERT INTO VRE_GALERIA (MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN, ACTIVO, SUBIDO_POR)
                       VALUES ('clubes', $club_id, '$titulo_escaped', '$url_escaped', '$tipo', $orden, 'S', {$temp->usuario_id})");

    if ($sql) {
        $nuevo_id = $db->insert_id;
        $temp->registrar_auditoria('GALERIA', 'SUBIR', "Nueva imagen tipo=$tipo en club $club_id");
        echo json_encode(['success' => 1, 'message' => 'Imagen guardada', 'id' => $nuevo_id, 'orden' => $orden]);
    } else {
        echo json_encode(['success' => 0, 'message' => 'Error al guardar en BD']);
    }
    exit();
}

echo json_encode(['success' => 0, 'message' => 'Acción no reconocida']);
