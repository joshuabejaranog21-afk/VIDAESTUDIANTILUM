<?php
$titulo = 'Ministerios';
$paginaActiva = 'ministerios';
$siteURL  = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$ministerios = [];
$sql = $db->query("SELECT ID, NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE, HORARIO, LUGAR
    FROM VRE_MINISTERIOS WHERE ACTIVO='S' ORDER BY ORDEN ASC, NOMBRE ASC");
while ($r = $db->recorrer($sql)) $ministerios[] = $r;
?>

<div style="background:linear-gradient(135deg,#825ee4,#5e72e4);padding:3rem 0;color:#fff;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item active text-white">Ministerios</li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <i class="fas fa-hands-praying me-3"></i>Ministerios
        </h1>
        <p style="opacity:.9;margin:0;">Crece espiritualmente y sirve a tu comunidad universitaria</p>
    </div>
</div>

<main class="container py-5">
    <?php if (empty($ministerios)): ?>
        <div class="text-center py-5">
            <i class="fas fa-hands-praying fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay ministerios disponibles por el momento</h4>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($ministerios as $m): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="portal-card">
                    <div class="card-img-wrap" style="background:linear-gradient(135deg,#825ee4,#5e72e4);">
                        <?php if (!empty($m['IMAGEN_URL'])): ?>
                            <img src="<?php echo $siteURL . htmlspecialchars($m['IMAGEN_URL']); ?>" alt="<?php echo htmlspecialchars($m['NOMBRE']); ?>">
                        <?php else: ?>
                            <i class="fas fa-hands-praying"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($m['TIPO'])): ?>
                            <span class="badge bg-light text-secondary mb-1" style="font-size:.7rem;"><?php echo htmlspecialchars($m['TIPO']); ?></span>
                        <?php endif; ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($m['NOMBRE']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(mb_substr($m['DESCRIPCION'] ?? '', 0, 100)); ?><?php echo mb_strlen($m['DESCRIPCION'] ?? '') > 100 ? '…' : ''; ?></p>
                        <?php if (!empty($m['HORARIO'])): ?>
                            <p class="mb-1" style="font-size:.8rem;color:#8392ab;"><i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($m['HORARIO']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['LUGAR'])): ?>
                            <p class="mb-2" style="font-size:.8rem;color:#8392ab;"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($m['LUGAR']); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo $portalURL; ?>ministerio/<?php echo $m['ID']; ?>" class="btn-portal d-inline-block text-decoration-none">
                            Ver detalles <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include('assets/php/footer.php'); ?>
