<?php
$titulo = 'Clubes';
$paginaActiva = 'clubes';
$siteURL = '/cpanel/cpanel_Hithan-main/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$clubes = [];
$sql = $db->query("SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE, HORARIO, LUGAR, RESPONSABLE_CONTACTO AS CONTACTO
    FROM VRE_CLUBES WHERE ACTIVO='S' ORDER BY ORDEN ASC, NOMBRE ASC");
while ($r = $db->recorrer($sql)) $clubes[] = $r;
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,#5e72e4,#825ee4);padding:3rem 0;color:#fff;margin-bottom:0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Clubes</li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <i class="fas fa-users me-3"></i>Clubes Estudiantiles
        </h1>
        <p style="opacity:.9;margin:0;">Encuentra el club perfecto para ti y forma parte de una comunidad increíble</p>
    </div>
</div>

<main class="container py-5">
    <?php if (empty($clubes)): ?>
        <div class="text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay clubes disponibles por el momento</h4>
        </div>
    <?php else: ?>
        <!-- Buscador -->
        <div class="mb-4">
            <input type="text" id="buscador" class="form-control form-control-lg"
                placeholder="🔍 Buscar club..." style="border-radius:.75rem;border:2px solid #e9ecef;max-width:400px;">
        </div>
        <div class="row g-4" id="listaClubes">
            <?php foreach ($clubes as $club): ?>
            <div class="col-sm-6 col-lg-4 club-item" data-nombre="<?php echo strtolower($club['NOMBRE']); ?>">
                <div class="portal-card">
                    <div class="card-img-wrap">
                        <?php if (!empty($club['IMAGEN_URL'])): ?>
                            <img src="<?php echo $siteURL . htmlspecialchars($club['IMAGEN_URL']); ?>" alt="<?php echo htmlspecialchars($club['NOMBRE']); ?>">
                        <?php else: ?>
                            <i class="fas fa-users"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($club['NOMBRE']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(mb_substr($club['DESCRIPCION'] ?? '', 0, 100)); ?><?php echo mb_strlen($club['DESCRIPCION'] ?? '') > 100 ? '…' : ''; ?></p>
                        <?php if (!empty($club['HORARIO'])): ?>
                            <p class="mb-1" style="font-size:.8rem;color:#8392ab;">
                                <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($club['HORARIO']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($club['LUGAR'])): ?>
                            <p class="mb-2" style="font-size:.8rem;color:#8392ab;">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($club['LUGAR']); ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?php echo $portalURL; ?>club/<?php echo $club['ID']; ?>" class="btn-portal d-inline-block text-decoration-none">
                            Ver detalles <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3" id="sinResultados" style="display:none;">No se encontraron clubes con ese nombre.</p>
    <?php endif; ?>
</main>

<script>
document.getElementById('buscador')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.club-item').forEach(el => {
        el.style.display = el.dataset.nombre.includes(q) ? '' : 'none';
    });
    const visibles = [...document.querySelectorAll('.club-item')].filter(e => e.style.display !== 'none');
    document.getElementById('sinResultados').style.display = visibles.length === 0 ? '' : 'none';
});
</script>

<?php include('assets/php/footer.php'); ?>
