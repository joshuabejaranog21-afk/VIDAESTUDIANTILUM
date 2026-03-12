<?php
$titulo = 'Deporte';
$paginaActiva = 'deportes';
$siteURL = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

// Obtener ID de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$deporte = null;
if ($id > 0) {
    $sql = $db->query("SELECT * FROM VRE_DEPORTES WHERE ID = $id AND ACTIVO='S' LIMIT 1");
    $deporte = $db->recorrer($sql);
}

if (!$deporte) {
    header('Location: ' . $portalURL . 'deportes');
    exit();
}
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,#fb6340,#fbb140);padding:3rem 0;color:#fff;margin-bottom:0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>deportes" style="color:rgba(255,255,255,.75);">Deportes</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($deporte['NOMBRE']); ?></li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <?php echo htmlspecialchars($deporte['NOMBRE']); ?>
        </h1>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">
        <!-- Imagen principal -->
        <div class="col-lg-5">
            <?php if (!empty($deporte['IMAGEN_URL'])): ?>
                <img src="<?php echo $siteURL . htmlspecialchars($deporte['IMAGEN_URL']); ?>"
                     class="w-100 border-radius-xl shadow-lg"
                     alt="<?php echo htmlspecialchars($deporte['NOMBRE']); ?>">
            <?php else: ?>
                <div class="bg-gradient-warning border-radius-xl shadow-lg d-flex align-items-center justify-content-center" style="height:400px;">
                    <i class="fas fa-running fa-5x text-white opacity-6"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm border-radius-xl p-4">
                <h4 class="mb-3">Información del Deporte</h4>

                <?php if (!empty($deporte['DESCRIPCION'])): ?>
                    <p class="text-secondary mb-4"><?php echo nl2br(htmlspecialchars($deporte['DESCRIPCION'])); ?></p>
                <?php endif; ?>

                <div class="row g-3">
                    <?php if (!empty($deporte['RESPONSABLE_NOMBRE'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-user-tie me-2"></i>Responsable</p>
                                <p class="mb-0 font-weight-bold"><?php echo htmlspecialchars($deporte['RESPONSABLE_NOMBRE']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($deporte['RESPONSABLE_CONTACTO'])): ?>
                        <div class="col-md-6">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-envelope me-2"></i>Contacto</p>
                                <p class="mb-0 font-weight-bold"><?php echo htmlspecialchars($deporte['RESPONSABLE_CONTACTO']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($deporte['EQUIPO_NECESARIO'])): ?>
                        <div class="col-md-12">
                            <div class="p-3 border-radius-lg" style="background:#f8f9fa;">
                                <p class="text-sm text-muted mb-1"><i class="fas fa-tools me-2"></i>Equipo Necesario</p>
                                <p class="mb-0 font-weight-bold"><?php echo nl2br(htmlspecialchars($deporte['EQUIPO_NECESARIO'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($deporte['REGLAS'])): ?>
                    <div class="mt-4">
                        <h5 class="mb-3">Reglas</h5>
                        <div class="p-3 border-radius-lg" style="background:#fff8e1;border-left:4px solid #fbb140;">
                            <?php echo nl2br(htmlspecialchars($deporte['REGLAS'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($deporte['BENEFICIOS'])): ?>
                    <div class="mt-4">
                        <h5 class="mb-3">Beneficios</h5>
                        <div class="p-3 border-radius-lg" style="background:#e8f5e9;border-left:4px solid #2dce89;">
                            <?php echo nl2br(htmlspecialchars($deporte['BENEFICIOS'])); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?php echo $portalURL; ?>deportes" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Deportes
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('assets/php/footer.php'); ?>
