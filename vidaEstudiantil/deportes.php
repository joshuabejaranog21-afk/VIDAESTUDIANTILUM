<?php
$titulo = 'Deportes';
$paginaActiva = 'deportes';
$siteURL = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$deportes = [];
$sql = $db->query("SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE, RESPONSABLE_CONTACTO
    FROM VRE_DEPORTES WHERE ACTIVO='S' ORDER BY ORDEN ASC, NOMBRE ASC");
while ($r = $db->recorrer($sql)) $deportes[] = $r;
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,#fb6340,#fbb140);padding:3rem 0;color:#fff;margin-bottom:0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Deportes</li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <i class="fas fa-running me-3"></i>Deportes
        </h1>
        <p style="opacity:.9;margin:0;">Mantente activo y compite representando a la universidad</p>
    </div>
</div>

<main class="container py-5">
    <?php if (empty($deportes)): ?>
        <div class="text-center py-5">
            <i class="fas fa-running fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay deportes disponibles por el momento</h4>
        </div>
    <?php else: ?>
        <!-- Buscador -->
        <div class="mb-4">
            <input type="text" id="buscador" class="form-control form-control-lg"
                placeholder="🔍 Buscar deporte..." style="border-radius:.75rem;border:2px solid #e9ecef;max-width:400px;">
        </div>
        <div class="row g-4" id="listaDeportes">
            <?php foreach ($deportes as $deporte): ?>
            <div class="col-sm-6 col-lg-4 deporte-item" data-nombre="<?php echo strtolower($deporte['NOMBRE']); ?>">
                <div class="portal-card">
                    <div class="card-img-wrap">
                        <?php if (!empty($deporte['IMAGEN_URL'])): ?>
                            <img src="<?php echo $siteURL . htmlspecialchars($deporte['IMAGEN_URL']); ?>" alt="<?php echo htmlspecialchars($deporte['NOMBRE']); ?>">
                        <?php else: ?>
                            <i class="fas fa-running"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($deporte['NOMBRE']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(mb_substr($deporte['DESCRIPCION'] ?? '', 0, 100)); ?><?php echo mb_strlen($deporte['DESCRIPCION'] ?? '') > 100 ? '…' : ''; ?></p>
                        <?php if (!empty($deporte['RESPONSABLE_NOMBRE'])): ?>
                            <p class="mb-2" style="font-size:.8rem;color:#8392ab;">
                                <i class="fas fa-user-tie me-1"></i><?php echo htmlspecialchars($deporte['RESPONSABLE_NOMBRE']); ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?php echo $portalURL; ?>deporte/<?php echo $deporte['ID']; ?>" class="btn-portal d-inline-block text-decoration-none">
                            Ver detalles <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3" id="sinResultados" style="display:none;">No se encontraron deportes con ese nombre.</p>
    <?php endif; ?>
</main>

<script>
document.getElementById('buscador')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.deporte-item').forEach(el => {
        el.style.display = el.dataset.nombre.includes(q) ? '' : 'none';
    });
    const visibles = [...document.querySelectorAll('.deporte-item')].filter(e => e.style.display !== 'none');
    document.getElementById('sinResultados').style.display = visibles.length === 0 ? '' : 'none';
});
</script>

<?php include('assets/php/footer.php'); ?>
