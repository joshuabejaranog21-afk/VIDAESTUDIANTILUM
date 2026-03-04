<?php
include('../../assets/php/template.php');
$temp = new Template('Ver Club');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('clubes', 'ver')) {
    echo "No tienes permiso para ver clubes";
    exit();
}

// Obtener ID del club
$id_club = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_club <= 0) {
    header('Location: ../');
    exit();
}

// Obtener información del club
$sql = $db->query("SELECT * FROM VRE_CLUBES WHERE ID = $id_club");
$club = $sql->fetch_assoc();

if (!$club) {
    header('Location: ../');
    exit();
}

// Obtener información del responsable
$responsable = null;
if (isset($club['ID_RESPONSABLE']) && $club['ID_RESPONSABLE']) {
    $sql_resp = $db->query("SELECT ID, NOMBRE, EMAIL FROM SYSTEM_USUARIOS WHERE ID = " . intval($club['ID_RESPONSABLE']));
    $responsable = $sql_resp->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true">
<head>
    <?php $temp->head() ?>
</head>
<body>
    <div id="root">
        <?php $temp->nav() ?>
        <main>
            <div class="container">
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4"><?php echo htmlspecialchars($club['NOMBRE']); ?></h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../">Clubes</a></li>
                                    <li class="breadcrumb-item active">Ver</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <a href="editar/?id=<?php echo $id_club; ?>" class="btn btn-outline-primary btn-icon btn-icon-start">
                                <i data-acorn-icon="edit"></i>
                                <span>Editar Club</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Información Principal -->
                    <div class="col-12 col-lg-8">
                        <!-- Logo e Información Básica -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <?php if($club['IMAGEN_URL']): ?>
                                        <img src="<?php echo htmlspecialchars($club['IMAGEN_URL']); ?>" class="img-fluid rounded" alt="Logo" style="object-fit:cover;aspect-ratio:1;">
                                        <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="aspect-ratio:1;">
                                            <i data-acorn-icon="image" style="font-size:48px;" class="text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="mb-3"><?php echo htmlspecialchars($club['NOMBRE']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($club['DESCRIPCION'] ?? ''); ?></p>
                                        <div class="mb-3">
                                            <span class="badge <?php echo ($club['ACTIVO'] == 'S') ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ($club['ACTIVO'] == 'S') ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles del Club -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Detalles</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1"><small>Horario de Reunión</small></p>
                                        <p class="mb-0"><strong><?php echo htmlspecialchars($club['HORARIO'] ?? '-'); ?></strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1"><small>Día de Reunión</small></p>
                                        <p class="mb-0"><strong><?php echo htmlspecialchars($club['DIA_REUNION'] ?? '-'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1"><small>Lugar</small></p>
                                        <p class="mb-0"><strong><?php echo htmlspecialchars($club['LUGAR'] ?? '-'); ?></strong></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1"><small>Cupo</small></p>
                                        <p class="mb-0">
                                            <strong>
                                                <?php 
                                                if($club['CUPO_MAXIMO']) {
                                                    echo ($club['CUPO_ACTUAL'] ?? 0) . " / " . $club['CUPO_MAXIMO'];
                                                } else {
                                                    echo "Sin límite";
                                                }
                                                ?>
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contacto -->
                        <?php if($club['EMAIL'] || $club['TELEFONO'] || $club['RESPONSABLE_CONTACTO']): ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Información de Contacto</h5>
                            </div>
                            <div class="card-body">
                                <?php if($club['EMAIL']): ?>
                                <div class="mb-3">
                                    <p class="text-muted mb-1"><small>Email</small></p>
                                    <p class="mb-0"><strong><?php echo htmlspecialchars($club['EMAIL']); ?></strong></p>
                                </div>
                                <?php endif; ?>

                                <?php if($club['TELEFONO']): ?>
                                <div class="mb-3">
                                    <p class="text-muted mb-1"><small>Teléfono</small></p>
                                    <p class="mb-0"><strong><?php echo htmlspecialchars($club['TELEFONO']); ?></strong></p>
                                </div>
                                <?php endif; ?>

                                <?php if($club['RESPONSABLE_CONTACTO']): ?>
                                <div class="mb-0">
                                    <p class="text-muted mb-1"><small>Contacto Responsable</small></p>
                                    <p class="mb-0"><strong><?php echo htmlspecialchars($club['RESPONSABLE_CONTACTO']); ?></strong></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-12 col-lg-4">
                        <!-- Responsable -->
                        <?php if($responsable): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Director del Club</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img src="<?php echo $temp->siteURL ?>assets/img/profile/user-solid.svg" class="rounded-circle" style="width:80px;height:80px;" alt="Director">
                                </div>
                                <h6><?php echo htmlspecialchars($responsable['NOMBRE']); ?></h6>
                                <p class="text-muted text-small"><?php echo htmlspecialchars($responsable['EMAIL']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Resumen -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Información General</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="text-muted mb-1"><small>ID del Club</small></p>
                                    <p class="mb-0"><code><?php echo $club['ID']; ?></code></p>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted mb-1"><small>Nombre Responsable</small></p>
                                    <p class="mb-0"><strong><?php echo htmlspecialchars($club['RESPONSABLE_NOMBRE'] ?? '-'); ?></strong></p>
                                </div>
                                <div class="mb-0">
                                    <p class="text-muted mb-1"><small>Estado</small></p>
                                    <p class="mb-0">
                                        <span class="badge <?php echo ($club['ACTIVO'] == 'S') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ($club['ACTIVO'] == 'S') ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="../" class="btn btn-secondary">
                                        <i data-acorn-icon="chevron-left"></i> Volver
                                    </a>
                                    <a href="editar/?id=<?php echo $id_club; ?>" class="btn btn-primary">
                                        <i data-acorn-icon="edit"></i> Editar Club
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>
    <?php $temp->scripts() ?>
</body>
</html>
