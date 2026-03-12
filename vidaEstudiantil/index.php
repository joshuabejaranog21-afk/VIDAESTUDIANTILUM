<?php
$titulo = 'Inicio';
$paginaActiva = 'home';
$siteURL = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('assets/php/header.php');

include('../cpanel/assets/API/db.php');
$db = new Conexion();
$hoy = date('Y-m-d');

// Video hero
$heroVideoPath = __DIR__ . '/assets/videos/hero.mp4';
$heroVideoWebm = __DIR__ . '/assets/videos/hero.webm';
$heroUrlFile   = __DIR__ . '/assets/videos/hero-url.txt';
$heroVideoURL  = null;
$heroVideoType = null;
$heroIsEmbed   = false;
if (file_exists($heroVideoPath)) {
    $heroVideoURL  = $portalURL . 'assets/videos/hero.mp4';
    $heroVideoType = 'video/mp4';
} elseif (file_exists($heroVideoWebm)) {
    $heroVideoURL  = $portalURL . 'assets/videos/hero.webm';
    $heroVideoType = 'video/webm';
} elseif (file_exists($heroUrlFile)) {
    $savedUrl = trim(file_get_contents($heroUrlFile));
    if ($savedUrl !== '') {
        // Detectar YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]{11})/', $savedUrl, $m)) {
            $heroVideoURL  = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
            $heroIsEmbed   = true;
        } else {
            $heroVideoURL  = $savedUrl;
            $heroVideoType = 'video/mp4';
        }
    }
}
$anio = date('Y');

// Banners
$banners = [];
$sqlB = $db->query("SELECT TITULO, DESCRIPCION, IMAGEN_URL, ENLACE FROM VRE_BANNERS
    WHERE ACTIVO = 'S' AND (FECHA_INICIO IS NULL OR FECHA_INICIO <= '$hoy')
    AND (FECHA_FIN IS NULL OR FECHA_FIN >= '$hoy') ORDER BY ORDEN ASC LIMIT 5");
while ($r = $db->recorrer($sqlB)) $banners[] = $r;

// Obtener configuración de eventos
$cantidadEventos = intval($homeConfig['eventos']['cantidad'] ?? 6);
$mostrarEventos = ($homeConfig['eventos']['mostrar'] ?? '1') === '1';
$estiloEventos = $homeConfig['eventos']['estilo'] ?? 'auto';
$soloDestacados = ($homeConfig['eventos']['solo_destacados'] ?? '0') === '1';

// Próximos eventos
$eventos = [];
$whereDestacados = $soloDestacados ? "AND DESTACADO='S'" : "";
$sqlE = $db->query("SELECT ID, TITULO, DESCRIPCION_CORTA, FECHA_EVENTO, LUGAR, IMAGEN_PRINCIPAL, CATEGORIA
    FROM VRE_EVENTOS WHERE ACTIVO='S' $whereDestacados ORDER BY DESTACADO DESC, FECHA_EVENTO ASC LIMIT $cantidadEventos");
while ($r = $db->recorrer($sqlE)) $eventos[] = $r;
?>

<!-- ═══════════════════ HERO ═══════════════════ -->
<header class="bg-gradient-primary">
    <div class="page-header min-vh-75 position-relative" style="<?php echo $heroVideoURL ? '' : 'background: linear-gradient(135deg,#5e72e4 0%,#825ee4 60%,#11cdef 100%);'; ?>">

        <?php if ($heroVideoURL && $heroIsEmbed): ?>
        <!-- YouTube embed de fondo -->
        <iframe src="<?php echo htmlspecialchars($heroVideoURL); ?>"
                style="position:absolute;inset:0;width:100%;height:100%;border:0;z-index:0;pointer-events:none;"
                allow="autoplay; encrypted-media" allowfullscreen></iframe>
        <?php elseif ($heroVideoURL): ?>
        <!-- Video de fondo -->
        <video autoplay muted loop playsinline
               style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
            <source src="<?php echo htmlspecialchars($heroVideoURL); ?>" type="<?php echo $heroVideoType; ?>">
        </video>
        <?php endif; ?>

        <span class="mask bg-gradient-dark <?php echo $heroVideoURL ? 'opacity-6' : 'opacity-4'; ?>"></span>
        <div class="container pb-lg-9 pb-8 pt-lg-7 postion-relative z-index-2">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="text-white font-weight-bolder display-1 mb-3">
                        <?php echo htmlspecialchars($homeConfig['hero']['titulo'] ?? 'Vida Estudiantil'); ?>
                    </h1>
                    <p class="text-white opacity-8 lead mb-4">
                        <?php echo htmlspecialchars($homeConfig['hero']['subtitulo'] ?? 'Descubre los clubes, ministerios, deportes y actividades que harán de tu experiencia universitaria algo inolvidable.'); ?>
                    </p>
                    <?php
                    $cantidadBotones = intval($homeConfig['hero']['cantidad_botones'] ?? 2);
                    $boton1Texto = $homeConfig['hero']['boton1_texto'] ?? 'Ver Eventos';
                    $boton1Enlace = $homeConfig['hero']['boton1_enlace'] ?? '#eventos';
                    $boton2Texto = $homeConfig['hero']['boton2_texto'] ?? 'Comunidad';
                    $boton2Enlace = $homeConfig['hero']['boton2_enlace'] ?? 'clubes';

                    // Construir URLs completas para enlaces relativos
                    if ($boton1Enlace !== '#eventos' && strpos($boton1Enlace, '#') !== 0 && strpos($boton1Enlace, 'http') !== 0) {
                        $boton1Enlace = $portalURL . $boton1Enlace;
                    }
                    if (strpos($boton2Enlace, '#') !== 0 && strpos($boton2Enlace, 'http') !== 0) {
                        $boton2Enlace = $portalURL . $boton2Enlace;
                    }
                    ?>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <?php if ($cantidadBotones == 1): ?>
                            <!-- Solo 1 botón grande -->
                            <a href="<?php echo htmlspecialchars($boton1Enlace); ?>" class="btn btn-white btn-lg px-5 py-3 font-weight-bolder" style="font-size:1.1rem;">
                                <?php echo htmlspecialchars($boton1Texto); ?>
                            </a>
                        <?php else: ?>
                            <!-- 2 botones -->
                            <a href="<?php echo htmlspecialchars($boton1Enlace); ?>" class="btn btn-white btn-lg px-4 font-weight-bolder">
                                <?php echo htmlspecialchars($boton1Texto); ?>
                            </a>
                            <a href="<?php echo htmlspecialchars($boton2Enlace); ?>" class="btn btn-outline-white btn-lg px-4 font-weight-bolder">
                                <?php echo htmlspecialchars($boton2Texto); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Wave -->
        <div class="position-absolute bottom-0 start-0 end-0">
            <svg viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                <path fill="#f8f9fa" d="M0,64L80,74.7C160,85,320,107,480,106.7C640,107,800,85,960,74.7C1120,64,1280,64,1360,64L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"/>
            </svg>
        </div>
    </div>
</header>

<div class="bg-gray-100">

<!-- ═══════════════════ BANNERS ═══════════════════ -->
<?php if (!empty($banners)): ?>
<section class="py-4">
    <div class="container">
        <div id="bannersCarousel" class="carousel slide shadow-lg border-radius-xl overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($banners as $i => $b): ?>
                    <button type="button" data-bs-target="#bannersCarousel" data-bs-slide-to="<?php echo $i; ?>"
                        <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner border-radius-xl">
                <?php foreach ($banners as $i => $b): ?>
                    <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>" style="height:400px;">
                        <?php if (!empty($b['IMAGEN_URL'])): ?>
                            <img src="<?php echo $siteURL . htmlspecialchars($b['IMAGEN_URL']); ?>"
                                 class="d-block w-100 h-100" style="object-fit:cover;"
                                 alt="<?php echo htmlspecialchars($b['TITULO']); ?>">
                        <?php else: ?>
                            <div class="d-block w-100 h-100 bg-gradient-primary"></div>
                        <?php endif; ?>
                        <div class="carousel-caption">
                            <span class="mask bg-gradient-dark opacity-5 border-radius-lg"></span>
                            <div class="position-relative z-index-2">
                                <h4 class="text-white font-weight-bolder"><?php echo htmlspecialchars($b['TITULO']); ?></h4>
                                <?php if (!empty($b['DESCRIPCION'])): ?>
                                    <p class="text-white opacity-8 d-none d-md-block"><?php echo htmlspecialchars($b['DESCRIPCION']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($b['ENLACE'])): ?>
                                    <a href="<?php echo htmlspecialchars($b['ENLACE']); ?>" class="btn btn-sm btn-white mt-1">Ver más</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#bannersCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannersCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════ EVENTOS ═══════════════════ -->
<?php if ($mostrarEventos): ?>
<?php
// Determinar el estilo a usar
$cantEventos = count($eventos);
$estiloReal = $estiloEventos;
if ($estiloEventos === 'auto') {
    if ($cantEventos == 1) $estiloReal = 'hero';
    elseif ($cantEventos == 2) $estiloReal = 'dual';
    else $estiloReal = 'grid';
}
?>
<section id="eventos" class="py-6" style="background:#fff;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-6">
                <span class="badge badge-sm bg-gradient-primary text-white mb-3">Agenda</span>
                <h2 class="font-weight-bolder text-dark mb-1"><?php echo htmlspecialchars($homeConfig['eventos']['titulo'] ?? 'Próximos Eventos'); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($homeConfig['eventos']['subtitulo'] ?? 'No te pierdas ninguna actividad del campus.'); ?></p>
            </div>
            <div class="col-lg-6 d-flex align-items-end justify-content-lg-end">
                <a href="<?php echo $portalURL; ?>eventos" class="btn btn-gradient-primary font-weight-bold">
                    Ver todos los eventos <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>

        <?php if (!empty($eventos)): ?>

            <?php if ($estiloReal === 'hero' && $cantEventos >= 1): ?>
                <!-- Estilo HERO: 1 evento grande horizontal -->
                <?php
                $ev = $eventos[0];
                $fecha = new DateTime($ev['FECHA_EVENTO']);
                $mes   = strtoupper($fecha->format('M'));
                $dia   = $fecha->format('d');
                $year  = $fecha->format('Y');
                ?>
                <div class="card shadow border-0 border-radius-xl move-on-hover">
                    <div class="row g-0">
                        <?php if (!empty($ev['IMAGEN_PRINCIPAL'])): ?>
                        <div class="col-md-5">
                            <img src="<?php echo $siteURL . htmlspecialchars($ev['IMAGEN_PRINCIPAL']); ?>"
                                 class="w-100 h-100 border-radius-xl border-radius-end-none"
                                 style="object-fit:cover;min-height:350px;"
                                 alt="<?php echo htmlspecialchars($ev['TITULO']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="col-md-7">
                            <div class="card-body p-5">
                                <div class="d-flex gap-3 mb-3">
                                    <div class="text-center">
                                        <div class="bg-gradient-primary border-radius-lg px-3 py-3" style="min-width:70px;">
                                            <span class="d-block text-white text-sm font-weight-bold"><?php echo $mes; ?></span>
                                            <span class="d-block text-white font-weight-bolder" style="font-size:2.2rem;line-height:1;"><?php echo $dia; ?></span>
                                            <span class="d-block text-white opacity-7 text-sm"><?php echo $year; ?></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?php if (!empty($ev['CATEGORIA'])): ?>
                                            <span class="badge badge-lg bg-gradient-primary mb-2"><?php echo htmlspecialchars($ev['CATEGORIA']); ?></span>
                                        <?php endif; ?>
                                        <h3 class="font-weight-bolder mb-2"><?php echo htmlspecialchars($ev['TITULO']); ?></h3>
                                        <?php if (!empty($ev['LUGAR'])): ?>
                                            <p class="text-secondary mb-2">
                                                <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($ev['LUGAR']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($ev['DESCRIPCION_CORTA'])): ?>
                                    <p class="text-secondary mb-4">
                                        <?php echo htmlspecialchars($ev['DESCRIPCION_CORTA']); ?>
                                    </p>
                                <?php endif; ?>
                                <a href="<?php echo $portalURL; ?>evento/<?php echo $ev['ID']; ?>"
                                   class="btn btn-primary btn-lg font-weight-bold">
                                    Ver detalles del evento <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($estiloReal === 'dual'): ?>
                <!-- Estilo DUAL: 2 eventos lado a lado -->
                <div class="row g-4">
                    <?php foreach ($eventos as $ev): ?>
                    <?php
                        $fecha = new DateTime($ev['FECHA_EVENTO']);
                        $mes   = strtoupper($fecha->format('M'));
                        $dia   = $fecha->format('d');
                        $year  = $fecha->format('Y');
                    ?>
                    <div class="col-md-6">
                        <div class="card shadow border-0 border-radius-xl h-100 move-on-hover">
                            <?php if (!empty($ev['IMAGEN_PRINCIPAL'])): ?>
                            <div class="card-header p-0 border-0">
                                <img src="<?php echo $siteURL . htmlspecialchars($ev['IMAGEN_PRINCIPAL']); ?>"
                                     class="w-100 border-radius-xl border-radius-bottom-none"
                                     style="height:220px;object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($ev['TITULO']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="card-body p-4">
                                <div class="d-flex gap-3 mb-3">
                                    <div class="text-center flex-shrink-0">
                                        <div class="bg-gradient-primary border-radius-lg px-2 py-2" style="min-width:60px;">
                                            <span class="d-block text-white text-xs font-weight-bold"><?php echo $mes; ?></span>
                                            <span class="d-block text-white font-weight-bolder" style="font-size:1.8rem;line-height:1;"><?php echo $dia; ?></span>
                                            <span class="d-block text-white opacity-7 text-xs"><?php echo $year; ?></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?php if (!empty($ev['CATEGORIA'])): ?>
                                            <span class="badge badge-sm bg-gradient-primary mb-1"><?php echo htmlspecialchars($ev['CATEGORIA']); ?></span>
                                        <?php endif; ?>
                                        <h5 class="font-weight-bolder mb-1"><?php echo htmlspecialchars($ev['TITULO']); ?></h5>
                                    </div>
                                </div>
                                <?php if (!empty($ev['LUGAR'])): ?>
                                    <p class="text-secondary text-sm mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($ev['LUGAR']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($ev['DESCRIPCION_CORTA'])): ?>
                                    <p class="text-secondary text-sm mb-3">
                                        <?php echo htmlspecialchars(mb_substr($ev['DESCRIPCION_CORTA'], 0, 100)); ?>
                                    </p>
                                <?php endif; ?>
                                <a href="<?php echo $portalURL; ?>evento/<?php echo $ev['ID']; ?>"
                                   class="btn btn-outline-primary font-weight-bold w-100">
                                    Ver evento
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($estiloReal === 'lista'): ?>
                <!-- Estilo LISTA: Scroll horizontal -->
                <div class="d-flex gap-4 overflow-auto pb-3" style="scroll-snap-type: x mandatory;">
                    <?php foreach ($eventos as $ev): ?>
                    <?php
                        $fecha = new DateTime($ev['FECHA_EVENTO']);
                        $mes   = strtoupper($fecha->format('M'));
                        $dia   = $fecha->format('d');
                        $year  = $fecha->format('Y');
                    ?>
                    <div style="min-width:320px;scroll-snap-align: start;">
                        <div class="card shadow border-0 border-radius-xl h-100 move-on-hover">
                            <?php if (!empty($ev['IMAGEN_PRINCIPAL'])): ?>
                            <div class="card-header p-0 border-0">
                                <img src="<?php echo $siteURL . htmlspecialchars($ev['IMAGEN_PRINCIPAL']); ?>"
                                     class="w-100 border-radius-xl border-radius-bottom-none"
                                     style="height:180px;object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($ev['TITULO']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="card-body p-4 d-flex gap-3">
                                <div class="text-center flex-shrink-0">
                                    <div class="bg-gradient-primary border-radius-lg px-2 py-2" style="min-width:52px;">
                                        <span class="d-block text-white text-xs font-weight-bold"><?php echo $mes; ?></span>
                                        <span class="d-block text-white font-weight-bolder" style="font-size:1.6rem;line-height:1;"><?php echo $dia; ?></span>
                                        <span class="d-block text-white opacity-7 text-xs"><?php echo $year; ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <?php if (!empty($ev['CATEGORIA'])): ?>
                                        <span class="badge badge-sm bg-gradient-primary mb-1"><?php echo htmlspecialchars($ev['CATEGORIA']); ?></span>
                                    <?php endif; ?>
                                    <h6 class="font-weight-bolder mb-1"><?php echo htmlspecialchars($ev['TITULO']); ?></h6>
                                    <?php if (!empty($ev['LUGAR'])): ?>
                                        <p class="text-secondary text-xs mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($ev['LUGAR']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <a href="<?php echo $portalURL; ?>evento/<?php echo $ev['ID']; ?>"
                                       class="btn btn-sm btn-outline-primary font-weight-bold mt-2">
                                        Ver evento
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Estilo GRID: Grid de 3 columnas (por defecto) -->
                <div class="row g-4">
                    <?php foreach ($eventos as $ev): ?>
                    <?php
                        $fecha = new DateTime($ev['FECHA_EVENTO']);
                        $mes   = strtoupper($fecha->format('M'));
                        $dia   = $fecha->format('d');
                        $year  = $fecha->format('Y');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow border-0 border-radius-xl h-100 move-on-hover">
                            <?php if (!empty($ev['IMAGEN_PRINCIPAL'])): ?>
                            <div class="card-header p-0 border-0">
                                <img src="<?php echo $siteURL . htmlspecialchars($ev['IMAGEN_PRINCIPAL']); ?>"
                                     class="w-100 border-radius-xl border-radius-bottom-none"
                                     style="height:160px;object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($ev['TITULO']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="card-body p-4 d-flex gap-3">
                                <div class="text-center flex-shrink-0">
                                    <div class="bg-gradient-primary border-radius-lg px-2 py-2" style="min-width:52px;">
                                        <span class="d-block text-white text-xs font-weight-bold"><?php echo $mes; ?></span>
                                        <span class="d-block text-white font-weight-bolder" style="font-size:1.6rem;line-height:1;"><?php echo $dia; ?></span>
                                        <span class="d-block text-white opacity-7 text-xs"><?php echo $year; ?></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <?php if (!empty($ev['CATEGORIA'])): ?>
                                        <span class="badge badge-sm bg-gradient-primary mb-1"><?php echo htmlspecialchars($ev['CATEGORIA']); ?></span>
                                    <?php endif; ?>
                                    <h6 class="font-weight-bolder mb-1"><?php echo htmlspecialchars($ev['TITULO']); ?></h6>
                                    <?php if (!empty($ev['LUGAR'])): ?>
                                        <p class="text-secondary text-xs mb-1">
                                            <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($ev['LUGAR']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($ev['DESCRIPCION_CORTA'])): ?>
                                        <p class="text-secondary text-xs mb-2">
                                            <?php echo htmlspecialchars(mb_substr($ev['DESCRIPCION_CORTA'], 0, 80)); ?>
                                        </p>
                                    <?php endif; ?>
                                    <a href="<?php echo $portalURL; ?>evento/<?php echo $ev['ID']; ?>"
                                       class="btn btn-sm btn-outline-primary font-weight-bold">
                                        Ver evento
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-alt fa-3x mb-3 text-white opacity-4"></i>
            <p class="text-white opacity-7">No hay eventos próximos por el momento.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

</div><!-- /bg-gray-100 -->

<?php include('assets/php/footer.php'); ?>
