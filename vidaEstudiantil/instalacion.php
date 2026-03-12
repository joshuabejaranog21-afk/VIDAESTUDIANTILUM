<?php
$titulo = 'Instalación';
$paginaActiva = 'instalaciones';
$siteURL = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

// Obtener ID de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$instalacion = null;
if ($id > 0) {
    $sql = $db->query("SELECT * FROM VRE_INSTALACIONES WHERE ID = $id AND ACTIVO='S' LIMIT 1");
    $instalacion = $db->recorrer($sql);
}

if (!$instalacion) {
    header('Location: ' . $portalURL . 'instalaciones');
    exit();
}
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,#11cdef,#1171ef);padding:3rem 0;color:#fff;margin-bottom:0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>instalaciones" style="color:rgba(255,255,255,.75);">Instalaciones</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($instalacion['NOMBRE']); ?></li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <?php echo htmlspecialchars($instalacion['NOMBRE']); ?>
        </h1>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">
        <!-- Imagen principal -->
        <div class="col-lg-5">
            <?php if (!empty($instalacion['IMAGEN_URL'])): ?>
                <img src="<?php echo $siteURL . htmlspecialchars($instalacion['IMAGEN_URL']); ?>"
                     class="w-100 border-radius-xl shadow-lg"
                     alt="<?php echo htmlspecialchars($instalacion['NOMBRE']); ?>">
            <?php else: ?>
                <div class="bg-gradient-info border-radius-xl shadow-lg d-flex align-items-center justify-content-center" style="height:400px;">
                    <i class="fas fa-building fa-5x text-white opacity-6"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm border-radius-xl p-4">
                <h4 class="mb-3">Información</h4>

                <?php if (!empty($instalacion['DESCRIPCION'])): ?>
                    <p class="text-secondary mb-4"><?php echo nl2br(htmlspecialchars($instalacion['DESCRIPCION'])); ?></p>
                <?php endif; ?>

                <div class="row g-3">
                    <?php if (!empty($instalacion['UBICACION'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i>Ubicación</p>
                                <p class="mb-0 font-weight-bold"><?php echo htmlspecialchars($instalacion['UBICACION']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($instalacion['CAPACIDAD'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-users me-2"></i>Capacidad</p>
                                <p class="mb-0 font-weight-bold"><?php echo htmlspecialchars($instalacion['CAPACIDAD']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($instalacion['HORARIO'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-clock me-2"></i>Horario</p>
                                <p class="mb-0 font-weight-bold"><?php echo nl2br(htmlspecialchars($instalacion['HORARIO'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($instalacion['RESPONSABLE_CONTACTO'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-envelope me-2"></i>Contacto</p>
                                <p class="mb-0 font-weight-bold"><?php echo htmlspecialchars($instalacion['RESPONSABLE_CONTACTO']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($instalacion['SERVICIOS'])): ?>
                    <div class="mt-4">
                        <h5 class="mb-3">Servicios disponibles</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $servicios = explode(',', $instalacion['SERVICIOS']);
                            foreach ($servicios as $servicio):
                            ?>
                                <span class="badge bg-gradient-info px-3 py-2">
                                    <i class="fas fa-check me-1"></i><?php echo trim(htmlspecialchars($servicio)); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?php echo $portalURL; ?>instalaciones" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Instalaciones
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('assets/php/footer.php'); ?>
