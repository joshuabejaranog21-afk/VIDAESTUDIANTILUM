<?php
include('../../assets/php/template.php');
$temp = new Template('Mi Ministerio');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Solo directores de ministerio pueden acceder
if (!$temp->es_director_ministerio()) {
    echo "<h3>Acceso Denegado</h3>";
    echo "<p>Esta página es solo para directores de ministerio.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Obtener información del ministerio asignado
$ministerio = $temp->obtener_ministerio_asignado();
if (!$ministerio) {
    echo "<h3>Ministerio No Asignado</h3>";
    echo "<p>No tienes un ministerio asignado. Contacta al administrador.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Obtener directiva del ministerio (usando tabla VRE_DIRECTIVA_MINISTERIOS)
$sql_directiva = $db->query("SELECT * FROM VRE_DIRECTIVA_MINISTERIOS WHERE ID_MINISTERIO = " . $ministerio['ID']);

error_log("DEBUG: Ministerio ID = " . $ministerio['ID']);
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

// Obtener imágenes del ministerio desde VRE_GALERIA
$imagenes_query = $db->query("
    SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
    FROM VRE_GALERIA
    WHERE MODULO = 'ministerios'
    AND ID_REGISTRO = {$ministerio['ID']}
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
                            <h1 class="mb-0 pb-0 display-4">Mi Ministerio</h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active">Mi Ministerio</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-start justify-content-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="editarMinisterio()">
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
                                        <h4 class="mb-1"><?php echo $ministerio['ACTIVO'] == 'S' ? 'Activo' : 'Inactivo'; ?></h4>
                                        <small>Estado del Ministerio</small>
                                    </div>
                                    <i data-acorn-icon="<?php echo $ministerio['ACTIVO'] == 'S' ? 'check-circle' : 'alert-circle'; ?>" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-info bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1"><?php echo !empty($ministerio['HORARIO']) ? 'Sí' : 'No'; ?></h4>
                                        <small>Horario Definido</small>
                                    </div>
                                    <i data-acorn-icon="clock" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card bg-warning bg-gradient text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-1 small"><?php echo !empty($ministerio['CUPO_MAXIMO']) ? $ministerio['CUPO_MAXIMO'] : '∞'; ?></h4>
                                        <small>Cupo Máximo</small>
                                    </div>
                                    <i data-acorn-icon="trending-up" data-acorn-size="32"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Ministerio -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title d-flex align-items-center">
                                    <i data-acorn-icon="heart" class="me-2"></i>
                                    <?php echo htmlspecialchars($ministerio['NOMBRE']); ?>
                                    <span class="badge <?php echo $ministerio['ACTIVO'] == 'S' ? 'bg-success' : 'bg-secondary'; ?> ms-2">
                                        <?php echo $ministerio['ACTIVO'] == 'S' ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Descripción</label>
                                            <p><?php echo $ministerio['DESCRIPCION'] ? nl2br(htmlspecialchars($ministerio['DESCRIPCION'])) : '<em class="text-muted">Sin descripción</em>'; ?></p>
                                        </div>
                                        
                                        <?php if($ministerio['OBJETIVO']): ?>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Objetivo</label>
                                            <p><?php echo nl2br(htmlspecialchars($ministerio['OBJETIVO'])); ?></p>
                                        </div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Horario de reunión</label>
                                                <p><?php echo $ministerio['HORARIO'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Día de reunión</label>
                                                <p><?php echo $ministerio['DIA_REUNION'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Lugar</label>
                                                <p><?php echo $ministerio['LUGAR'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Teléfono</label>
                                                <p><?php echo $ministerio['TELEFONO'] ?: '<em class="text-muted">No definido</em>'; ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="mb-3">
                                            <?php if($imagen_principal): ?>
                                                <img src="<?php echo htmlspecialchars($imagen_principal); ?>"
                                                     class="img-fluid rounded shadow"
                                                     alt="Logo del ministerio"
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
                                            <p><strong>Director:</strong><br><?php echo htmlspecialchars($ministerio['DIRECTOR_NOMBRE']); ?></p>
                                            <?php if($ministerio['DIRECTOR_EMAIL']): ?>
                                                <p><strong>Email:</strong><br><?php echo htmlspecialchars($ministerio['DIRECTOR_EMAIL']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Directiva del Ministerio -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i data-acorn-icon="users" class="me-2"></i>
                                        Directiva del Ministerio
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
                                                            <!-- Mostrar foto si existe -->
                                                            <img src="<?php echo htmlspecialchars($directiva['datos'][$campo_foto]); ?>"
                                                                 class="rounded-circle mx-auto mb-3"
                                                                 style="width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--bs-primary);"
                                                                 alt="<?php echo htmlspecialchars($directiva['datos'][$campo_nombre]); ?>">
                                                        <?php else: ?>
                                                            <!-- Mostrar iniciales si no hay foto -->
                                                            <div class="bg-primary bg-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                                <span class="text-white fw-bold" style="font-size: 24px;">
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
                                        <p class="text-muted">Agrega miembros a la directiva de tu ministerio</p>
                                        <button type="button" class="btn btn-primary" onclick="gestionarDirectiva()">
                                            <i data-acorn-icon="plus"></i> Gestionar Directiva
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galería del Ministerio -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i data-acorn-icon="image" class="me-2"></i>
                                        Galería del Ministerio
                                    </h5>
                                    <small class="text-muted"><?php echo count($galeria); ?> foto<?php echo count($galeria) != 1 ? 's' : ''; ?></small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editarMinisterio()">
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
                                                         alt="Foto del ministerio">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i data-acorn-icon="image" class="text-muted" data-acorn-size="64"></i>
                                        <h5 class="mt-3 text-muted">Sin fotos en la galería</h5>
                                        <p class="text-muted">Agrega fotos de actividades, eventos o reuniones de tu ministerio</p>
                                        <button type="button" class="btn btn-primary" onclick="editarMinisterio()">
                                            <i data-acorn-icon="plus"></i> Agregar Fotos
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
        function editarMinisterio() {
            window.location.href = '/cpanel/pages/mi-ministerio/editar?id=<?php echo $ministerio['ID']; ?>';
        }
        
        function gestionarDirectiva() {
            window.location.href = '/cpanel/pages/mi-ministerio/directiva?id=<?php echo $ministerio['ID']; ?>';
        }
    </script>
</body>
</html>
