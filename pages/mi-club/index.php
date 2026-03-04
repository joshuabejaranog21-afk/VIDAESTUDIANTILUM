<?php
include('../../assets/php/template.php');
$temp = new Template('Mi Club');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Solo directores de club pueden acceder
if (!$temp->es_director_club()) {
    echo "<h3>Acceso Denegado</h3>";
    echo "<p>Esta página es solo para directores de club.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Obtener información del club asignado
$club = $temp->obtener_club_asignado();
if (!$club) {
    echo "<h3>Club No Asignado</h3>";
    echo "<p>No tienes un club asignado. Contacta al administrador.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Validar datos completos del club
function validar_club_completo($club) {
    $campos_requeridos = [
        'DESCRIPCION' => 'Descripción',
        'OBJETIVO' => 'Objetivo',
        'HORARIO' => 'Horario de reunión',
        'DIA_REUNION' => 'Día de reunión',
        'LUGAR' => 'Lugar de reunión',
        'RESPONSABLE_NOMBRE' => 'Nombre del responsable',
        'EMAIL' => 'Email de contacto'
    ];

    $campos_faltantes = [];
    foreach ($campos_requeridos as $campo => $nombre) {
        if (empty($club[$campo]) || trim($club[$campo]) === '') {
            $campos_faltantes[] = $nombre;
        }
    }

    return [
        'completo' => empty($campos_faltantes),
        'faltantes' => $campos_faltantes
    ];
}

$validacion = validar_club_completo($club);
$club_completo = $validacion['completo'];
$datos_faltantes = $validacion['faltantes'];

// Obtener directiva del club (usando nueva tabla VRE_DIRECTIVA_CLUBES)
$sql_directiva = $db->query("SELECT * FROM VRE_DIRECTIVA_CLUBES WHERE ID_CLUB = " . $club['ID']);

error_log("DEBUG: Club ID = " . $club['ID']);
error_log("DEBUG: SQL rows = " . ($sql_directiva ? $sql_directiva->num_rows : 'null'));

$directiva = ['total' => 0];
if ($sql_directiva && $sql_directiva->num_rows > 0) {
    $directiva_data = $sql_directiva->fetch_assoc();

    // Contar cargos con nombre
    $cargos_llenos = 0;
    $cargos_nombres = ['DIRECTOR', 'SUBDIRECTOR', 'SECRETARIO', 'TESORERO', 'CAPELLAN', 'CONSEJERO_GENERAL', 'LOGISTICA', 'MEDIA'];
    foreach ($cargos_nombres as $cargo) {
        $campo_nombre = strtoupper($cargo) . '_NOMBRE';
        if (!empty($directiva_data[$campo_nombre])) {
            $cargos_llenos++;
        }
    }
    $directiva['total'] = $cargos_llenos;
    $directiva['datos'] = $directiva_data;
}

// Obtener imágenes del club desde VRE_GALERIA
$imagenes_query = $db->query("
    SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
    FROM VRE_GALERIA
    WHERE MODULO = 'clubes'
    AND ID_REGISTRO = {$club['ID']}
    AND ACTIVO = 'S'
    ORDER BY ORDEN ASC
");

$imagenes = [];
$imagen_principal = null;

if ($imagenes_query) {
    while ($img = $imagenes_query->fetch_assoc()) {
        $imagenes[] = $img;
        if ($img['TIPO'] == 'principal' && !$imagen_principal) {
            $imagen_principal = $img['URL_IMAGEN'];
        }
    }
}

// Mantener compatibilidad temporal
$galeria = array_filter(array_map(function($img) {
    return $img['TIPO'] == 'galeria' ? $img['URL_IMAGEN'] : null;
}, $imagenes));
?>
<!DOCTYPE html>
<html lang="es" data-footer="true">
<head>
    <?php $temp->head() ?>
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
        }
    </style>
</head>
<body>
    <div id="root">
        <?php $temp->nav() ?>
        <main>
            <div class="container">
                <!-- Header -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <h1 class="mb-0 pb-0 display-4">Mi Club</h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active">Mi Club</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-start justify-content-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="editarClub()">
                                    <i data-acorn-icon="edit"></i> Editar Información
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="gestionarDirectiva()">
                                    <i data-acorn-icon="user-plus"></i> Gestionar Directiva
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-primary bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1"><?php echo isset($directiva['total']) ? $directiva['total'] : '0'; ?></h4>
                                        <small>Miembros de Directiva</small>
                                    </div>
                                    <i data-acorn-icon="users" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-success bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1"><?php echo $club['CUPO_ACTUAL'] ?: '0'; ?></h4>
                                        <small>Miembros Inscritos</small>
                                    </div>
                                    <i data-acorn-icon="user" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1"><?php echo $club['CUPO_MAXIMO'] ?: '∞'; ?></h4>
                                        <small>Cupo Máximo</small>
                                    </div>
                                    <i data-acorn-icon="trend-up" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-<?php
                            if($club['ACTIVO'] == 'S' && $club_completo) {
                                echo 'success';
                            } elseif($club_completo) {
                                echo 'info';
                            } else {
                                echo 'warning';
                            }
                        ?> bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1 small">
                                            <?php
                                            if($club['ACTIVO'] == 'S' && $club_completo) {
                                                echo 'Activo y Completo';
                                            } elseif($club['ACTIVO'] == 'S' && !$club_completo) {
                                                echo 'Incompleto';
                                            } elseif($club['ACTIVO'] == 'N' && $club_completo) {
                                                echo 'Completo pero Inactivo';
                                            } else {
                                                echo 'Incompleto e Inactivo';
                                            }
                                            ?>
                                        </h4>
                                        <small>Estado del Club</small>
                                    </div>
                                    <i data-acorn-icon="<?php echo ($club['ACTIVO'] == 'S' && $club_completo) ? 'check-circle' : 'alert-triangle'; ?>" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Club -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title d-flex align-items-center">
                                    <i data-acorn-icon="star" class="me-2"></i>
                                    <?php echo htmlspecialchars($club['NOMBRE']); ?>
                                    <span class="badge <?php echo $club['ACTIVO'] == 'S' ? 'bg-success' : 'bg-secondary'; ?> ms-2">
                                        <?php echo $club['ACTIVO'] == 'S' ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Descripción</label>
                                            <p><?php echo $club['DESCRIPCION'] ? nl2br(htmlspecialchars($club['DESCRIPCION'])) : '<em class="text-muted">Sin descripción</em>'; ?></p>
                                        </div>
                                        
                                        <?php if($club['OBJETIVO']): ?>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Objetivo</label>
                                            <p><?php echo nl2br(htmlspecialchars($club['OBJETIVO'])); ?></p>
                                        </div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Horario de reunión</label>
                                                <p><?php echo $club['HORARIO'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Día de reunión</label>
                                                <p><?php echo $club['DIA_REUNION'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Lugar</label>
                                                <p><?php echo $club['LUGAR'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Cupo</label>
                                                <p>
                                                    <?php if($club['CUPO_MAXIMO']): ?>
                                                        <?php echo $club['CUPO_ACTUAL'] ?: 0; ?> / <?php echo $club['CUPO_MAXIMO']; ?> personas
                                                    <?php else: ?>
                                                        <em class="text-muted">Sin límite</em>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="mb-3">
                                            <?php if($imagen_principal): ?>
                                                <img src="<?php echo htmlspecialchars($imagen_principal); ?>"
                                                     class="img-fluid rounded shadow"
                                                     alt="Logo del club"
                                                     style="max-height: 200px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                                    <div class="text-center">
                                                        <i data-acorn-icon="image" class="text-muted" data-acorn-size="48"></i>
                                                        <p class="text-muted mt-2">Sin imagen</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="text-muted small">
                                            <p><strong>Director:</strong><br><?php echo htmlspecialchars($club['DIRECTOR_NOMBRE']); ?></p>
                                            <?php if($club['DIRECTOR_EMAIL']): ?>
                                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($club['DIRECTOR_EMAIL']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Directiva del Club -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i data-acorn-icon="users" class="me-2"></i>
                                        Directiva del Club
                                    </h5>
                                    <small class="text-muted"><?php echo isset($directiva['total']) ? $directiva['total'] : '0'; ?> cargo<?php echo (isset($directiva['total']) && $directiva['total'] != 1) ? 's' : ''; ?> asignado<?php echo (isset($directiva['total']) && $directiva['total'] != 1) ? 's' : ''; ?></small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="gestionarDirectiva()">
                                        <i data-acorn-icon="edit-square"></i> Gestionar
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(isset($directiva['total']) && $directiva['total'] > 0): ?>
                                    <div class="alert alert-info">
                                        <i data-acorn-icon="info" class="me-2"></i>
                                        Tienes <strong><?php echo $directiva['total']; ?></strong> cargo<?php echo $directiva['total'] != 1 ? 's' : ''; ?> asignado<?php echo $directiva['total'] != 1 ? 's' : ''; ?> en la directiva.
                                    </div>
                                    <p><small class="text-muted">Haz clic en "Gestionar" para ver y editar los detalles de cada cargo.</small></p>
                                    
                                    <?php 
                                    // Mostrar cargos asignados
                                    $cargos_info = [
                                        'DIRECTOR' => 'Director',
                                        'SUBDIRECTOR' => 'Subdirector',
                                        'SECRETARIO' => 'Secretario',
                                        'TESORERO' => 'Tesorero',
                                        'CAPELLAN' => 'Capellán',
                                        'CONSEJERO_GENERAL' => 'Consejero General',
                                        'LOGISTICA' => 'Logística',
                                        'MEDIA' => 'Media'
                                    ];
                                    ?>
                                    <div class="row mt-3">
                                        <?php foreach($cargos_info as $slug => $nombre_cargo): ?>
                                            <?php
                                            $campo_nombre = $slug . '_NOMBRE';
                                            $campo_email = $slug . '_EMAIL';
                                            $campo_telefono = $slug . '_TELEFONO';
                                            $campo_foto = $slug . '_FOTO';

                                            if (!empty($directiva['datos'][$campo_nombre])):
                                            ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100 border-start border-primary border-4">
                                                    <div class="card-body text-center">
                                                        <?php if (!empty($directiva['datos'][$campo_foto])): ?>
                                                            <img src="<?php echo htmlspecialchars($directiva['datos'][$campo_foto]); ?>"
                                                                 class="rounded-circle mx-auto mb-3"
                                                                 style="width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--bs-primary);"
                                                                 alt="<?php echo htmlspecialchars($directiva['datos'][$campo_nombre]); ?>">
                                                        <?php else: ?>
                                                            <div class="bg-primary bg-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                                <span class="text-white fw-bold" style="font-size: 18px;">
                                                                    <?php
                                                                    $nombres = explode(' ', trim($directiva['datos'][$campo_nombre]));
                                                                    echo strtoupper(substr($nombres[0], 0, 1));
                                                                    if (isset($nombres[1])) {
                                                                        echo strtoupper(substr($nombres[1], 0, 1));
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($directiva['datos'][$campo_nombre]); ?></h6>
                                                        <p class="text-primary fw-semibold mb-2" style="font-size: 12px;"><?php echo $nombre_cargo; ?></p>
                                                        
                                                        <?php if(!empty($directiva['datos'][$campo_email])): ?>
                                                            <small class="text-muted d-block mb-1">
                                                                <i data-acorn-icon="email" class="me-1" data-acorn-size="14"></i>
                                                                <?php echo htmlspecialchars($directiva['datos'][$campo_email]); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(!empty($directiva['datos'][$campo_telefono])): ?>
                                                            <small class="text-muted d-block mb-1">
                                                                <i data-acorn-icon="phone" class="me-1" data-acorn-size="14"></i>
                                                                <?php echo htmlspecialchars($directiva['datos'][$campo_telefono]); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        
                                                        <div class="mt-2">
                                                            <span class="badge bg-success">Activo</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i data-acorn-icon="users" class="text-muted" data-acorn-size="64"></i>
                                        <h5 class="mt-3 text-muted">Sin directiva asignada</h5>
                                        <p class="text-muted">Agrega miembros a la directiva de tu club</p>
                                        <button type="button" class="btn btn-primary" onclick="gestionarDirectiva()">
                                            <i data-acorn-icon="plus"></i> Gestionar Directiva
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galería del Club -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i data-acorn-icon="image" class="me-2"></i>
                                        Galería del Club
                                    </h5>
                                    <small class="text-muted"><?php echo count($galeria); ?> foto<?php echo count($galeria) != 1 ? 's' : ''; ?></small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editarClub()">
                                        <i data-acorn-icon="edit-square"></i> Gestionar Galería
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(count($galeria) > 0): ?>
                                    <div class="row g-3">
                                        <?php foreach($galeria as $foto_url): ?>
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <div class="card h-100 shadow-sm hover-shadow" style="cursor: pointer;" onclick="window.open('<?php echo htmlspecialchars($foto_url); ?>', '_blank')">
                                                    <img src="<?php echo htmlspecialchars($foto_url); ?>"
                                                         class="card-img-top"
                                                         style="height: 200px; object-fit: cover;"
                                                         alt="Foto del club">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i data-acorn-icon="image" class="text-muted" data-acorn-size="64"></i>
                                        <h5 class="mt-3 text-muted">Sin fotos en la galería</h5>
                                        <p class="text-muted">Agrega fotos de actividades, eventos o instalaciones de tu club</p>
                                        <button type="button" class="btn btn-primary" onclick="editarClub()">
                                            <i data-acorn-icon="plus"></i> Agregar Fotos
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas y notificaciones -->
                <div class="row mt-4">
                    <div class="col-12">
                        <?php if(!$club_completo): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <i data-acorn-icon="alert-triangle" class="me-3 mt-1" data-acorn-size="24"></i>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-2">
                                        <i data-acorn-icon="info" class="me-2"></i>
                                        Información Incompleta del Club
                                    </h5>
                                    <p class="mb-2">Para que tu club sea visible en la página web y los estudiantes puedan encontrarlo, necesitas completar la siguiente información:</p>
                                    <ul class="mb-3">
                                        <?php foreach($datos_faltantes as $faltante): ?>
                                            <li><strong><?php echo htmlspecialchars($faltante); ?></strong></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p class="mb-0">
                                        <a href="<?php echo $temp->siteURL; ?>pages/mi-club/editar" class="btn btn-sm btn-warning">
                                            <i data-acorn-icon="edit"></i> Completar Información
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>

                        <?php if($club['ACTIVO'] == 'N'): ?>
                        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                            <i data-acorn-icon="lock-off" class="me-2"></i>
                            <strong>Tu club está inactivo</strong>. Aunque completes la información, el administrador debe activarlo para que sea visible públicamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php elseif($club_completo): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i data-acorn-icon="check-circle" class="me-2"></i>
                            <strong>¡Excelente!</strong> Tu club tiene toda la información completa y está activo. Los estudiantes pueden verlo en la página web.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>
    
    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>
    
    <script>
        function editarClub() {
            window.location.href = '/cpanel/pages/mi-club/editar';
        }
        
        function gestionarDirectiva() {
            window.location.href = '/cpanel/pages/mi-club/directiva';
        }
    </script>
</body>
</html>