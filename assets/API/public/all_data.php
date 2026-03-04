<?php
include("../db.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$db = new Conexion();
$data = ['success' => 1];

// ============================================
// ANUARIOS - Últimos 6
// ============================================
$cadAnuarios = "SELECT ID, TITULO, ANIO, DESCRIPCION, IMAGEN_PORTADA, LIKES, VISTAS
                FROM VRE_ANUARIOS
                WHERE ACTIVO = 'S'
                ORDER BY ANIO DESC
                LIMIT 6";
$sqlAnuarios = $db->query($cadAnuarios);
$anuarios = [];
while ($row = $db->recorrer($sqlAnuarios)) {
    $anuarios[] = [
        'id' => $row['ID'],
        'titulo' => $row['TITULO'],
        'anio' => $row['ANIO'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_PORTADA'],
        'likes' => $row['LIKES'],
        'vistas' => $row['VISTAS']
    ];
}
$data['anuarios'] = $anuarios;

// ============================================
// EVENTOS - Próximos eventos
// ============================================
$cadEventos = "SELECT ID, TITULO, SLUG, DESCRIPCION_CORTA, FECHA_EVENTO, LUGAR,
                      IMAGEN_PRINCIPAL, CATEGORIA, DESTACADO
               FROM VRE_EVENTOS
               WHERE ACTIVO = 'S' AND ESTADO IN ('PROXIMO', 'EN_CURSO')
               AND FECHA_EVENTO >= NOW()
               ORDER BY DESTACADO DESC, FECHA_EVENTO ASC
               LIMIT 6";
$sqlEventos = $db->query($cadEventos);
$eventos = [];
while ($row = $db->recorrer($sqlEventos)) {
    $eventos[] = [
        'id' => $row['ID'],
        'titulo' => $row['TITULO'],
        'slug' => $row['SLUG'],
        'descripcion' => $row['DESCRIPCION_CORTA'],
        'fecha' => $row['FECHA_EVENTO'],
        'lugar' => $row['LUGAR'],
        'imagen' => $row['IMAGEN_PRINCIPAL'],
        'categoria' => $row['CATEGORIA'],
        'destacado' => $row['DESTACADO']
    ];
}
$data['eventos'] = $eventos;

// ============================================
// CLUBES - Activos
// ============================================
$cadClubes = "SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE
              FROM VRE_CLUBES
              WHERE ACTIVO = 'S'
              ORDER BY ORDEN ASC, NOMBRE ASC
              LIMIT 8";
$sqlClubes = $db->query($cadClubes);
$clubes = [];
while ($row = $db->recorrer($sqlClubes)) {
    $clubes[] = [
        'id' => $row['ID'],
        'nombre' => $row['NOMBRE'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL'],
        'responsable' => $row['RESPONSABLE_NOMBRE']
    ];
}
$data['clubes'] = $clubes;

// ============================================
// MINISTERIOS - Activos
// ============================================
$cadMinisterios = "SELECT ID, NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL
                   FROM VRE_MINISTERIOS
                   WHERE ACTIVO = 'S'
                   ORDER BY ORDEN ASC, NOMBRE ASC
                   LIMIT 8";
$sqlMinisterios = $db->query($cadMinisterios);
$ministerios = [];
while ($row = $db->recorrer($sqlMinisterios)) {
    $ministerios[] = [
        'id' => $row['ID'],
        'nombre' => $row['NOMBRE'],
        'tipo' => $row['TIPO'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL']
    ];
}
$data['ministerios'] = $ministerios;

// ============================================
// DEPORTES - Activos
// ============================================
$cadDeportes = "SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL
                FROM VRE_DEPORTES
                WHERE ACTIVO = 'S'
                ORDER BY ORDEN ASC, NOMBRE ASC
                LIMIT 8";
$sqlDeportes = $db->query($cadDeportes);
$deportes = [];
while ($row = $db->recorrer($sqlDeportes)) {
    $deportes[] = [
        'id' => $row['ID'],
        'nombre' => $row['NOMBRE'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL']
    ];
}
$data['deportes'] = $deportes;

// ============================================
// SERVICIOS CO-CURRICULARES
// ============================================
$cadCocurriculares = "SELECT ID, NOMBRE, SLUG, CATEGORIA, DESCRIPCION, IMAGEN_URL
                      FROM VRE_SERVICIOS_COCURRICULARES
                      WHERE ACTIVO = 'S'
                      ORDER BY ORDEN ASC
                      LIMIT 8";
$sqlCocurriculares = $db->query($cadCocurriculares);
$cocurriculares = [];
while ($row = $db->recorrer($sqlCocurriculares)) {
    $cocurriculares[] = [
        'id' => $row['ID'],
        'nombre' => $row['NOMBRE'],
        'slug' => $row['SLUG'],
        'categoria' => $row['CATEGORIA'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL']
    ];
}
$data['cocurriculares'] = $cocurriculares;

// ============================================
// FEDERACIÓN - Miembros del año actual
// ============================================
$anioActual = date('Y');
$cadFederacion = "SELECT NOMBRE, PUESTO, FOTO_URL
                  FROM VRE_FEDERACION_MIEMBROS
                  WHERE ANIO = $anioActual AND ACTIVO = 'S'
                  ORDER BY ORDEN ASC
                  LIMIT 10";
$sqlFederacion = $db->query($cadFederacion);
$federacion = [];
while ($row = $db->recorrer($sqlFederacion)) {
    $federacion[] = [
        'nombre' => $row['NOMBRE'],
        'puesto' => $row['PUESTO'],
        'foto' => $row['FOTO_URL']
    ];
}
$data['federacion'] = $federacion;

// ============================================
// PULSO UM - Equipo actual
// ============================================
$cadPulso = "SELECT NOMBRE, CARGO, FOTO_URL
             FROM VRE_PULSO_EQUIPOS
             WHERE ANIO = $anioActual AND ACTIVO = 'S'
             ORDER BY ORDEN ASC
             LIMIT 10";
$sqlPulso = $db->query($cadPulso);
$pulso = [];
while ($row = $db->recorrer($sqlPulso)) {
    $pulso[] = [
        'nombre' => $row['NOMBRE'],
        'cargo' => $row['CARGO'],
        'foto' => $row['FOTO_URL']
    ];
}
$data['pulso'] = $pulso;

// ============================================
// BANNERS - Activos
// ============================================
$hoy = date('Y-m-d');
$cadBanners = "SELECT TITULO, DESCRIPCION, IMAGEN_URL, ENLACE, TIPO
              FROM VRE_BANNERS
              WHERE ACTIVO = 'S'
              AND (FECHA_INICIO IS NULL OR FECHA_INICIO <= '$hoy')
              AND (FECHA_FIN IS NULL OR FECHA_FIN >= '$hoy')
              ORDER BY ORDEN ASC
              LIMIT 5";
$sqlBanners = $db->query($cadBanners);
$banners = [];
while ($row = $db->recorrer($sqlBanners)) {
    $banners[] = [
        'titulo' => $row['TITULO'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL'],
        'enlace' => $row['ENLACE'],
        'tipo' => $row['TIPO']
    ];
}
$data['banners'] = $banners;

// ============================================
// AMENIDADES - Vida en el Campus
// ============================================
$cadAmenidades = "SELECT NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL, UBICACION
                  FROM VRE_AMENIDADES
                  WHERE ACTIVO = 'S'
                  ORDER BY ORDEN ASC
                  LIMIT 6";
$sqlAmenidades = $db->query($cadAmenidades);
$amenidades = [];
while ($row = $db->recorrer($sqlAmenidades)) {
    $amenidades[] = [
        'nombre' => $row['NOMBRE'],
        'tipo' => $row['TIPO'],
        'descripcion' => $row['DESCRIPCION'],
        'imagen' => $row['IMAGEN_URL'],
        'ubicacion' => $row['UBICACION']
    ];
}
$data['amenidades'] = $amenidades;

// ============================================
// ESTADÍSTICAS GENERALES
// ============================================
$stats = [];

// Total de clubes activos
$cadStatsC = "SELECT COUNT(*) as total FROM VRE_CLUBES WHERE ACTIVO = 'S'";
$stats['clubes_total'] = $db->recorrer($db->query($cadStatsC))['total'];

// Total de ministerios activos
$cadStatsM = "SELECT COUNT(*) as total FROM VRE_MINISTERIOS WHERE ACTIVO = 'S'";
$stats['ministerios_total'] = $db->recorrer($db->query($cadStatsM))['total'];

// Total de deportes activos
$cadStatsD = "SELECT COUNT(*) as total FROM VRE_DEPORTES WHERE ACTIVO = 'S'";
$stats['deportes_total'] = $db->recorrer($db->query($cadStatsD))['total'];

// Total de eventos próximos
$cadStatsE = "SELECT COUNT(*) as total FROM VRE_EVENTOS WHERE ACTIVO = 'S' AND ESTADO IN ('PROXIMO', 'EN_CURSO')";
$stats['eventos_total'] = $db->recorrer($db->query($cadStatsE))['total'];

$data['stats'] = $stats;

echo json_encode($data);
?>
