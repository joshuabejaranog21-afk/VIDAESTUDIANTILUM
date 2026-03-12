<?php
$titulo = 'Anuarios';
$paginaActiva = 'anuarios';
$siteURL = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$anuarios = [];
$sql = $db->query("SELECT ID, TITULO, ANIO, DESCRIPCION, PDF_URL, IMAGEN_PORTADA, FECHA_CREACION
    FROM VRE_ANUARIOS WHERE ACTIVO='S' ORDER BY ANIO DESC");
while ($r = $db->recorrer($sql)) $anuarios[] = $r;
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg,#2dce89,#2dcecc);padding:3rem 0;color:#fff;margin-bottom:0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Anuarios</li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <i class="fas fa-book me-3"></i>Anuarios
        </h1>
        <p style="opacity:.9;margin:0;">Explora los recuerdos de cada generación universitaria</p>
    </div>
</div>

<main class="container py-5">
    <?php if (empty($anuarios)): ?>
        <div class="text-center py-5">
            <i class="fas fa-book fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay anuarios disponibles por el momento</h4>
        </div>
    <?php else: ?>
        <!-- Filtro por año -->
        <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <span class="text-muted">Filtrar por año:</span>
            <button class="btn btn-sm btn-success filter-btn active" data-year="all">Todos</button>
            <?php
            $years = array_unique(array_map(function($a) { return $a['ANIO']; }, $anuarios));
            rsort($years);
            foreach ($years as $year):
            ?>
                <button class="btn btn-sm btn-outline-success filter-btn" data-year="<?php echo $year; ?>">
                    <?php echo $year; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid de anuarios en horizontal -->
        <div class="row g-4" id="anuariosGrid">
            <?php foreach ($anuarios as $anuario): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 anuario-item" data-year="<?php echo htmlspecialchars($anuario['ANIO']); ?>">
                <div class="card shadow-sm border-0 border-radius-xl h-100" style="transition:transform .3s,box-shadow .3s;">
                    <?php if (!empty($anuario['IMAGEN_PORTADA'])): ?>
                        <div class="card-header p-0 border-0 position-relative" style="border-radius:1rem 1rem 0 0;overflow:hidden;">
                            <img src="<?php echo $siteURL . htmlspecialchars($anuario['IMAGEN_PORTADA']); ?>"
                                 class="w-100" style="height:320px;object-fit:cover;"
                                 alt="<?php echo htmlspecialchars($anuario['TITULO']); ?>">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-success px-3 py-2" style="font-size:.9rem;">
                                    <i class="fas fa-calendar-alt me-1"></i><?php echo htmlspecialchars($anuario['ANIO']); ?>
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card-header p-0 border-0 bg-gradient-success d-flex align-items-center justify-content-center position-relative"
                             style="height:320px;border-radius:1rem 1rem 0 0;">
                            <i class="fas fa-book fa-4x text-white opacity-6"></i>
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-white text-success px-3 py-2" style="font-size:.9rem;">
                                    <i class="fas fa-calendar-alt me-1"></i><?php echo htmlspecialchars($anuario['ANIO']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <h5 class="card-title font-weight-bolder mb-2" style="font-size:.95rem;">
                            <?php echo htmlspecialchars($anuario['TITULO']); ?>
                        </h5>
                        <?php if (!empty($anuario['DESCRIPCION'])): ?>
                            <p class="card-text text-xs text-secondary mb-3">
                                <?php echo htmlspecialchars(mb_substr($anuario['DESCRIPCION'], 0, 80)); ?><?php echo mb_strlen($anuario['DESCRIPCION']) > 80 ? '…' : ''; ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($anuario['PDF_URL'])): ?>
                            <a href="<?php echo $siteURL . htmlspecialchars($anuario['PDF_URL']); ?>"
                               target="_blank"
                               class="btn btn-sm btn-success w-100 font-weight-bold">
                                <i class="fas fa-download me-1"></i>Descargar PDF
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                                <i class="fas fa-times-circle me-1"></i>No disponible
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
// Filtro por año
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const year = this.dataset.year;

        // Actualizar botones activos
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'btn-success');
            b.classList.add('btn-outline-success');
        });
        this.classList.add('active', 'btn-success');
        this.classList.remove('btn-outline-success');

        // Filtrar anuarios
        document.querySelectorAll('.anuario-item').forEach(item => {
            if (year === 'all' || item.dataset.year === year) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>

<?php include('assets/php/footer.php'); ?>
