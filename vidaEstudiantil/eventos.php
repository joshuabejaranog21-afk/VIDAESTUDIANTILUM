<?php
$titulo = 'Eventos';
$paginaActiva = 'eventos';
$siteURL  = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');
include('../cpanel/assets/API/db.php');
$db = new Conexion();

$eventos = [];
$sql = $db->query("SELECT ID, TITULO, DESCRIPCION_CORTA, FECHA_EVENTO, LUGAR,
    IMAGEN_PRINCIPAL, CATEGORIA, ESTADO, DESTACADO
    FROM VRE_EVENTOS WHERE ACTIVO='S'
    ORDER BY DESTACADO DESC, FECHA_EVENTO ASC");
while ($r = $db->recorrer($sql)) $eventos[] = $r;

// Categorías únicas para el filtro
$categorias = array_unique(array_filter(array_column($eventos, 'CATEGORIA')));
sort($categorias);
?>

<div style="background:linear-gradient(135deg,#2dce89,#2dcccc);padding:3rem 0;color:#fff;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item active text-white">Eventos</li>
            </ol>
        </nav>
        <h1 style="font-size:2.25rem;font-weight:800;margin-bottom:.5rem;">
            <i class="fas fa-calendar-alt me-3"></i>Eventos
        </h1>
        <p style="opacity:.9;margin:0;">Agenda de actividades y eventos del campus</p>
    </div>
</div>

<main class="container py-5">
    <?php if (empty($eventos)): ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No hay eventos disponibles por el momento</h4>
        </div>
    <?php else: ?>

        <!-- Filtros -->
        <?php if (!empty($categorias)): ?>
        <div class="mb-4 d-flex gap-2 flex-wrap align-items-center">
            <span style="font-size:.875rem;font-weight:600;color:#8392ab;">Filtrar:</span>
            <button class="btn btn-sm btn-primary filtro-btn active" data-cat="">Todos</button>
            <?php foreach ($categorias as $cat): ?>
                <button class="btn btn-sm btn-outline-secondary filtro-btn" data-cat="<?php echo htmlspecialchars($cat); ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="row g-4" id="listaEventos">
            <?php foreach ($eventos as $ev): ?>
            <?php
                $fecha = new DateTime($ev['FECHA_EVENTO']);
                $mes   = strtoupper($fecha->format('M'));
                $dia   = $fecha->format('d');
                $anio  = $fecha->format('Y');
                $hora  = $fecha->format('H:i');

                $estadoColor = match($ev['ESTADO'] ?? '') {
                    'PROXIMO'   => 'primary',
                    'EN_CURSO'  => 'success',
                    'TERMINADO' => 'secondary',
                    'CANCELADO' => 'danger',
                    default     => 'secondary'
                };
                $estadoLabel = match($ev['ESTADO'] ?? '') {
                    'PROXIMO'   => 'Próximo',
                    'EN_CURSO'  => 'En curso',
                    'TERMINADO' => 'Terminado',
                    'CANCELADO' => 'Cancelado',
                    default     => $ev['ESTADO'] ?? ''
                };
            ?>
            <div class="col-md-6 col-lg-4 evento-item" data-cat="<?php echo htmlspecialchars($ev['CATEGORIA'] ?? ''); ?>">
                <div class="portal-card">
                    <?php if (!empty($ev['IMAGEN_PRINCIPAL'])): ?>
                    <div class="card-img-wrap" style="height:180px;">
                        <img src="<?php echo $siteURL . htmlspecialchars($ev['IMAGEN_PRINCIPAL']); ?>" alt="<?php echo htmlspecialchars($ev['TITULO']); ?>">
                    </div>
                    <?php else: ?>
                    <div class="card-img-wrap img-placeholder" style="height:120px;background:linear-gradient(135deg,#2dce89,#2dcccc);">
                        <i class="fas fa-calendar-alt" style="font-size:2.5rem;color:rgba(255,255,255,.6);"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="background:linear-gradient(135deg,#2dce89,#2dcccc);color:#fff;border-radius:.5rem;padding:.25rem .6rem;text-align:center;min-width:50px;">
                                    <div style="font-size:.65rem;font-weight:700;letter-spacing:.05em;"><?php echo $mes; ?></div>
                                    <div style="font-size:1.3rem;font-weight:800;line-height:1;"><?php echo $dia; ?></div>
                                </div>
                                <div style="font-size:.78rem;color:#8392ab;"><?php echo $anio; ?> · <?php echo $hora; ?></div>
                            </div>
                            <?php if (!empty($ev['ESTADO'])): ?>
                                <span class="badge bg-<?php echo $estadoColor; ?> bg-opacity-10 text-<?php echo $estadoColor; ?> evento-badge">
                                    <?php echo $estadoLabel; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($ev['TITULO']); ?></h5>
                        <?php if (!empty($ev['DESCRIPCION_CORTA'])): ?>
                            <p class="card-text"><?php echo htmlspecialchars(mb_substr($ev['DESCRIPCION_CORTA'], 0, 90)); ?><?php echo mb_strlen($ev['DESCRIPCION_CORTA']) > 90 ? '…' : ''; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($ev['LUGAR'])): ?>
                            <p class="mb-0" style="font-size:.8rem;color:#8392ab;">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($ev['LUGAR']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($ev['CATEGORIA'])): ?>
                            <span class="badge bg-light text-secondary mt-2" style="font-size:.7rem;"><?php echo htmlspecialchars($ev['CATEGORIA']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p id="sinEventos" class="text-muted mt-3" style="display:none;">No hay eventos en esta categoría.</p>
    <?php endif; ?>
</main>

<script>
document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active', 'btn-primary'));
        document.querySelectorAll('.filtro-btn').forEach(b => { if (!b.classList.contains('active')) b.classList.add('btn-outline-secondary'); });
        this.classList.add('active', 'btn-primary');
        this.classList.remove('btn-outline-secondary');

        const cat = this.dataset.cat;
        let visible = 0;
        document.querySelectorAll('.evento-item').forEach(el => {
            const show = !cat || el.dataset.cat === cat;
            el.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('sinEventos').style.display = visible === 0 ? '' : 'none';
    });
});
</script>

<?php include('assets/php/footer.php'); ?>
