<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('assets/php/template.php');
$temp = new Template('Inicio');
$db = new Conexion();

if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Redirigir directores de club automáticamente a su módulo
if ($temp->es_director_club()) {
    header('Location: ' . $temp->siteURL . 'pages/mi-club/');
    exit();
}

// Redirigir directores de ministerio automáticamente a su módulo
if ($temp->es_director_ministerio()) {
    header('Location: ' . $temp->siteURL . 'pages/mi-ministerio/');
    exit();
}

// Redirigir a configuración del home directamente
header('Location: ' . $temp->siteURL . 'configuracion/home/');
exit();

// ====================================
// CARGAR CONFIGURACIÓN DEL HOME
// ====================================
$config = [];
$sql = $db->query("SELECT SECCION, CLAVE, VALOR FROM VRE_HOME_CONFIG WHERE ACTIVO = 'S'");
if ($db->rows($sql) > 0) {
    foreach ($sql as $row) {
        $config[$row['SECCION']][$row['CLAVE']] = $row['VALOR'];
    }
}

// ====================================
// CARGAR ESTADÍSTICAS
// ====================================
$estadisticas = [];
$sqlStats = $db->query("SELECT * FROM VRE_HOME_ESTADISTICAS WHERE ACTIVO = 'S' ORDER BY ORDEN ASC");
if ($db->rows($sqlStats) > 0) {
    $estadisticas = $sqlStats->fetch_all(MYSQLI_ASSOC);
}

// ====================================
// CARGAR BANNERS
// ====================================
$banners = [];
$sqlBanners = $db->query("SELECT * FROM VRE_BANNERS WHERE ACTIVO = 'S' ORDER BY ORDEN ASC");
if ($db->rows($sqlBanners) > 0) {
    $banners = $sqlBanners->fetch_all(MYSQLI_ASSOC);
}

// ====================================
// CARGAR CLUBES DESTACADOS
// ====================================
$clubes = [];
$cantidadClubes = $config['seccion_clubes']['cantidad'] ?? 6;
$sqlClubes = "SELECT c.*
              FROM VRE_CLUBES c
              INNER JOIN VRE_HOME_DESTACADOS d ON d.ID_REGISTRO = c.ID AND d.TIPO = 'club' AND d.ACTIVO = 'S'
              WHERE c.ACTIVO = 'S'
              ORDER BY d.ORDEN ASC
              LIMIT $cantidadClubes";
$resultClubes = $db->query($sqlClubes);
if ($db->rows($resultClubes) > 0) {
    $clubes = $resultClubes->fetch_all(MYSQLI_ASSOC);
}

// Si no hay destacados, mostrar los más recientes
if (empty($clubes)) {
    $sqlClubes = "SELECT * FROM VRE_CLUBES WHERE ACTIVO = 'S' ORDER BY FECHA_CREACION DESC LIMIT $cantidadClubes";
    $resultClubes = $db->query($sqlClubes);
    if ($db->rows($resultClubes) > 0) {
        $clubes = $resultClubes->fetch_all(MYSQLI_ASSOC);
    }
}

// ====================================
// CARGAR MINISTERIOS DESTACADOS
// ====================================
$ministerios = [];
$cantidadMinisterios = $config['seccion_ministerios']['cantidad'] ?? 3;
$sqlMinisterios = "SELECT m.*
                   FROM VRE_MINISTERIOS m
                   INNER JOIN VRE_HOME_DESTACADOS d ON d.ID_REGISTRO = m.ID AND d.TIPO = 'ministerio' AND d.ACTIVO = 'S'
                   WHERE m.ACTIVO = 'S'
                   ORDER BY d.ORDEN ASC
                   LIMIT $cantidadMinisterios";
$resultMinisterios = $db->query($sqlMinisterios);
if ($db->rows($resultMinisterios) > 0) {
    $ministerios = $resultMinisterios->fetch_all(MYSQLI_ASSOC);
}

// Si no hay destacados, mostrar los más recientes
if (empty($ministerios)) {
    $sqlMinisterios = "SELECT * FROM VRE_MINISTERIOS WHERE ACTIVO = 'S' ORDER BY FECHA_CREACION DESC LIMIT $cantidadMinisterios";
    $resultMinisterios = $db->query($sqlMinisterios);
    if ($db->rows($resultMinisterios) > 0) {
        $ministerios = $resultMinisterios->fetch_all(MYSQLI_ASSOC);
    }
}

// Video hero (mismo archivo que usa vidaEstudiantil)
$heroVideoPath = dirname(__DIR__) . '/vidaEstudiantil/assets/videos/hero.mp4';
$heroVideoWebm = dirname(__DIR__) . '/vidaEstudiantil/assets/videos/hero.webm';
$heroUrlFile   = dirname(__DIR__) . '/vidaEstudiantil/assets/videos/hero-url.txt';
$heroVideoURL  = null;
$heroVideoType = null;
$heroVideoIsEmbed = false;
if (file_exists($heroVideoPath)) {
    $heroVideoURL  = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.mp4';
    $heroVideoType = 'video/mp4';
} elseif (file_exists($heroVideoWebm)) {
    $heroVideoURL  = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.webm';
    $heroVideoType = 'video/webm';
} elseif (file_exists($heroUrlFile) && trim(file_get_contents($heroUrlFile)) !== '') {
    $heroVideoURL  = trim(file_get_contents($heroUrlFile));
    $heroVideoType = 'url';
    // Detectar YouTube
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]{11})/', $heroVideoURL, $m)) {
        $heroVideoURL  = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1];
        $heroVideoIsEmbed = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <style>
        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            overflow: hidden;
            margin-bottom: 40px;
            border-radius: 20px;
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
        }

        .hero-gradient {
            width: 100%;
            height: 100%;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 60px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.4rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .hero-btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            background: white;
            color: #667eea;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: #667eea;
        }

        /* Banner Carousel */
        .banner-carousel {
            margin-bottom: 50px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .banner-carousel .carousel-item {
            height: 400px;
        }

        .banner-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Statistics Section */
        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            margin: 60px 0;
            border-radius: 20px;
            color: white;
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #718096;
        }

        /* Club Cards */
        .club-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            height: 100%;
            background: white;
            border: none;
        }

        .club-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .club-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .club-card-body {
            padding: 20px;
        }

        .club-card-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2d3748;
        }

        .club-card-description {
            color: #718096;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .club-card-btn {
            width: 100%;
            border-radius: 10px;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 500;
        }

        /* Ministerio Cards */
        .ministerio-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            height: 100%;
            text-align: center;
        }

        .ministerio-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .ministerio-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .ministerio-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .ministerio-description {
            color: #718096;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div id="root">
        <?php $temp->nav() ?>

        <main>
            <div class="container">

                <!-- Acceso Rápido (solo superusuarios) -->
                <?php if ($temp->usuario_categoria == 1): ?>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <p class="text-muted text-xs font-weight-bold text-uppercase mb-2">
                            <i class="fas fa-bolt me-1"></i>Gestión rápida
                        </p>
                    </div>
                    <!-- Tarjeta Video Hero -->
                    <div class="col-sm-6 col-lg-3">
                        <a href="<?php echo $temp->siteURL ?>pages/vida-estudiantil/video-hero.php"
                           class="card shadow-sm border-0 border-radius-xl text-decoration-none h-100 move-on-hover">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div class="icon icon-shape icon-md shadow text-center border-radius-lg flex-shrink-0"
                                     style="background:linear-gradient(135deg,#5e72e4,#825ee4);">
                                    <i class="fas fa-film text-white opacity-10"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary mb-0">Vida Estudiantil</p>
                                    <h6 class="mb-0 font-weight-bolder text-dark">Video Hero</h6>
                                    <?php if ($heroVideoURL): ?>
                                        <span class="badge badge-sm bg-gradient-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-sm bg-gradient-secondary">Sin video</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Tarjeta Banners -->
                    <div class="col-sm-6 col-lg-3">
                        <a href="<?php echo $temp->siteURL ?>pages/banners/"
                           class="card shadow-sm border-0 border-radius-xl text-decoration-none h-100 move-on-hover">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div class="icon icon-shape icon-md shadow text-center border-radius-lg flex-shrink-0"
                                     style="background:linear-gradient(135deg,#2dce89,#2dcecc);">
                                    <i class="fas fa-images text-white opacity-10"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary mb-0">Contenido</p>
                                    <h6 class="mb-0 font-weight-bolder text-dark">Banners</h6>
                                    <span class="badge badge-sm bg-gradient-info"><?php echo count($banners); ?> activos</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Tarjeta Clubes -->
                    <div class="col-sm-6 col-lg-3">
                        <a href="<?php echo $temp->siteURL ?>pages/clubes/"
                           class="card shadow-sm border-0 border-radius-xl text-decoration-none h-100 move-on-hover">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div class="icon icon-shape icon-md shadow text-center border-radius-lg flex-shrink-0"
                                     style="background:linear-gradient(135deg,#fb6340,#fbb140);">
                                    <i class="fas fa-users text-white opacity-10"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary mb-0">Involúcrate</p>
                                    <h6 class="mb-0 font-weight-bolder text-dark">Clubes</h6>
                                    <span class="badge badge-sm bg-gradient-warning"><?php echo count($clubes); ?> destacados</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Tarjeta Ministerios -->
                    <div class="col-sm-6 col-lg-3">
                        <a href="<?php echo $temp->siteURL ?>pages/ministerios/"
                           class="card shadow-sm border-0 border-radius-xl text-decoration-none h-100 move-on-hover">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div class="icon icon-shape icon-md shadow text-center border-radius-lg flex-shrink-0"
                                     style="background:linear-gradient(135deg,#11cdef,#1171ef);">
                                    <i class="fas fa-hands-praying text-white opacity-10"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-secondary mb-0">Fe y Servicio</p>
                                    <h6 class="mb-0 font-weight-bolder text-dark">Ministerios</h6>
                                    <span class="badge badge-sm bg-gradient-info"><?php echo count($ministerios); ?> destacados</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hero Section -->
                <?php
                $heroTitulo     = $config['hero']['titulo']       ?? 'Vida Estudiantil';
                $heroSubtitulo  = $config['hero']['subtitulo']    ?? 'Descubre los clubes, ministerios, deportes y actividades que harán de tu experiencia universitaria algo inolvidable.';
                $heroBotonTexto = $config['hero']['boton_texto']  ?? 'Explorar Clubes';
                $heroBotonUrl   = $config['hero']['boton_url']    ?? 'pages/clubes/';
                $heroColorInicio = $config['hero']['color_inicio'] ?? '#5e72e4';
                $heroColorFin    = $config['hero']['color_fin']    ?? '#825ee4';
                $heroImagenFondo = $config['hero']['imagen_fondo'] ?? '';
                ?>
                <div class="page-header min-vh-50 position-relative border-radius-xl overflow-hidden mb-4"
                     style="<?php echo (!$heroVideoURL && empty($heroImagenFondo)) ? 'background:linear-gradient(135deg,' . $heroColorInicio . ' 0%,' . $heroColorFin . ' 100%);' : ''; ?>">

                    <?php if ($heroVideoURL && $heroVideoIsEmbed): ?>
                    <iframe src="<?php echo htmlspecialchars($heroVideoURL); ?>"
                            style="position:absolute;inset:0;width:100%;height:100%;border:0;z-index:0;"
                            allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    <?php elseif ($heroVideoURL && !$heroVideoIsEmbed): ?>
                    <video autoplay muted loop playsinline
                           style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;">
                        <source src="<?php echo htmlspecialchars($heroVideoURL); ?>" type="<?php echo $heroVideoType !== 'url' ? $heroVideoType : ''; ?>">
                    </video>
                    <?php elseif (!empty($heroImagenFondo)): ?>
                    <img src="<?php echo $temp->siteURL . $heroImagenFondo; ?>"
                         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0;" alt="Hero">
                    <?php endif; ?>

                    <span class="mask bg-gradient-dark <?php echo $heroVideoURL ? 'opacity-6' : 'opacity-4'; ?>"></span>

                    <div class="container py-5 position-relative z-index-2">
                        <div class="row justify-content-center text-center">
                            <div class="col-lg-8">
                                <span class="badge badge-sm bg-white text-primary mb-3 px-3 py-2"
                                      style="font-size:.75rem;letter-spacing:.08em;">
                                    Universidad de Monterrey
                                </span>
                                <h1 class="text-white font-weight-bolder display-2 mb-3">
                                    <?php echo htmlspecialchars($heroTitulo); ?>
                                </h1>
                                <p class="text-white opacity-8 lead mb-4">
                                    <?php echo htmlspecialchars($heroSubtitulo); ?>
                                </p>
                                <a href="<?php echo $temp->siteURL . $heroBotonUrl; ?>"
                                   class="btn btn-white btn-lg px-4 font-weight-bolder">
                                    <i class="fas fa-users me-2"></i><?php echo htmlspecialchars($heroBotonTexto); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Wave -->
                    <div class="position-absolute bottom-0 start-0 end-0">
                        <svg viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                            <path fill="#f8f9fa" d="M0,40L80,46C160,53,320,67,480,67C640,67,800,53,960,46C1120,40,1280,40,1360,40L1440,40L1440,80L0,80Z"/>
                        </svg>
                    </div>
                </div>

                <!-- Banner Carousel -->
                <?php if (!empty($banners)): ?>
                    <div id="bannersCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($banners as $index => $banner): ?>
                                <button type="button" data-bs-target="#bannersCarousel" data-bs-slide-to="<?php echo $index; ?>"
                                    <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($banners as $index => $banner): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <?php if (!empty($banner['ENLACE'])): ?>
                                        <a href="<?php echo htmlspecialchars($banner['ENLACE']); ?>">
                                            <img src="<?php echo $temp->siteURL . $banner['IMAGEN_URL']; ?>" alt="<?php echo htmlspecialchars($banner['TITULO']); ?>">
                                        </a>
                                    <?php else: ?>
                                        <img src="<?php echo $temp->siteURL . $banner['IMAGEN_URL']; ?>" alt="<?php echo htmlspecialchars($banner['TITULO']); ?>">
                                    <?php endif; ?>
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
                <?php endif; ?>

                <!-- Statistics Section -->
                <?php if (($config['seccion_stats']['mostrar'] ?? 'S') === 'S' && !empty($estadisticas)): ?>
                <section class="py-5 bg-gray-100">
                    <div class="container">
                        <div class="row g-4 justify-content-center text-center">
                            <?php foreach ($estadisticas as $stat): ?>
                            <div class="col-6 col-md-3">
                                <div class="card shadow-lg border-0 border-radius-xl move-on-hover h-100">
                                    <div class="card-body p-4">
                                        <div class="icon icon-shape icon-lg shadow text-center border-radius-xl mb-3"
                                             style="background:<?php echo htmlspecialchars($stat['COLOR']); ?>;">
                                            <i class="<?php echo htmlspecialchars($stat['ICONO']); ?> text-white opacity-10 fa-lg"></i>
                                        </div>
                                        <h2 class="font-weight-bolder"><?php echo htmlspecialchars($stat['NUMERO']); ?></h2>
                                        <p class="text-secondary mb-0"><?php echo htmlspecialchars($stat['TITULO']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Clubes Section -->
                <?php if (($config['seccion_clubes']['mostrar'] ?? 'S') === 'S' && !empty($clubes)): ?>
                <section class="py-5 bg-gray-100">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <span class="badge badge-sm bg-gradient-primary mb-2">Comunidad</span>
                                <h2 class="font-weight-bolder mb-1">
                                    <?php echo htmlspecialchars($config['seccion_clubes']['titulo'] ?? 'Clubes Destacados'); ?>
                                </h2>
                                <p class="text-secondary">
                                    <?php echo htmlspecialchars($config['seccion_clubes']['subtitulo'] ?? 'Únete a alguno de nuestros clubes y forma parte de una comunidad increíble.'); ?>
                                </p>
                            </div>
                            <div class="col-lg-6 d-flex align-items-end justify-content-lg-end">
                                <a href="<?php echo $temp->siteURL; ?>pages/clubes/" class="btn btn-outline-primary font-weight-bold">
                                    Ver todos los clubes <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($clubes as $club): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card shadow border-0 border-radius-xl move-on-hover h-100">
                                    <div class="card-header p-0 border-0 position-relative">
                                        <?php if (!empty($club['IMAGEN_URL'])): ?>
                                            <img src="<?php echo $temp->siteURL . htmlspecialchars($club['IMAGEN_URL']); ?>"
                                                 class="w-100 border-radius-xl border-radius-bottom-none"
                                                 style="height:200px;object-fit:cover;"
                                                 alt="<?php echo htmlspecialchars($club['NOMBRE']); ?>">
                                        <?php else: ?>
                                            <div class="bg-gradient-primary border-radius-xl border-radius-bottom-none d-flex align-items-center justify-content-center" style="height:200px;">
                                                <i class="fas fa-users fa-3x text-white opacity-8"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body px-4 pb-4 pt-3">
                                        <h5 class="font-weight-bolder mb-2"><?php echo htmlspecialchars($club['NOMBRE']); ?></h5>
                                        <p class="text-secondary text-sm mb-3">
                                            <?php echo htmlspecialchars(mb_substr($club['DESCRIPCION'] ?? '', 0, 110)); ?><?php echo mb_strlen($club['DESCRIPCION'] ?? '') > 110 ? '…' : ''; ?>
                                        </p>
                                        <a href="<?php echo $temp->siteURL; ?>pages/clubes/?club=<?php echo $club['ID']; ?>"
                                           class="btn btn-sm btn-outline-primary font-weight-bold">
                                            Ver club <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Ministerios Section -->
                <?php if (($config['seccion_ministerios']['mostrar'] ?? 'S') === 'S' && !empty($ministerios)): ?>
                <section class="py-5" style="background:linear-gradient(180deg,#f8f9fa 0%,#fff 100%);">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <span class="badge badge-sm bg-gradient-info mb-2">Fe y Servicio</span>
                                <h2 class="font-weight-bolder mb-1">
                                    <?php echo htmlspecialchars($config['seccion_ministerios']['titulo'] ?? 'Ministerios'); ?>
                                </h2>
                                <p class="text-secondary">
                                    <?php echo htmlspecialchars($config['seccion_ministerios']['subtitulo'] ?? 'Crece espiritualmente y sirve a tu comunidad universitaria.'); ?>
                                </p>
                            </div>
                            <div class="col-lg-6 d-flex align-items-end justify-content-lg-end">
                                <a href="<?php echo $temp->siteURL; ?>pages/ministerios/" class="btn btn-outline-info font-weight-bold">
                                    Ver todos <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row g-4">
                            <?php foreach ($ministerios as $ministerio): ?>
                            <div class="col-sm-6 col-lg-3">
                                <div class="card shadow border-0 border-radius-xl move-on-hover h-100">
                                    <div class="card-header p-0 border-0">
                                        <?php if (!empty($ministerio['IMAGEN_URL'])): ?>
                                            <img src="<?php echo $temp->siteURL . htmlspecialchars($ministerio['IMAGEN_URL']); ?>"
                                                 class="w-100 border-radius-xl border-radius-bottom-none"
                                                 style="height:170px;object-fit:cover;"
                                                 alt="<?php echo htmlspecialchars($ministerio['NOMBRE']); ?>">
                                        <?php else: ?>
                                            <div class="bg-gradient-info border-radius-xl border-radius-bottom-none d-flex align-items-center justify-content-center" style="height:170px;">
                                                <i class="fas fa-hands-praying fa-3x text-white opacity-8"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body px-4 pb-4 pt-3">
                                        <h5 class="font-weight-bolder mb-2 text-sm"><?php echo htmlspecialchars($ministerio['NOMBRE']); ?></h5>
                                        <p class="text-secondary text-xs mb-3">
                                            <?php echo htmlspecialchars(mb_substr($ministerio['DESCRIPCION'] ?? '', 0, 90)); ?><?php echo mb_strlen($ministerio['DESCRIPCION'] ?? '') > 90 ? '…' : ''; ?>
                                        </p>
                                        <a href="<?php echo $temp->siteURL; ?>pages/ministerios/?ministerio=<?php echo $ministerio['ID']; ?>"
                                           class="btn btn-sm btn-outline-info font-weight-bold">
                                            Ver más <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        // Animación de contador para las estadísticas
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 100;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 20);
        }

        // Inicializar animación cuando la sección sea visible
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        const text = stat.textContent;
                        const number = parseInt(text.replace(/[^0-9]/g, ''));
                        if (!isNaN(number) && number > 0) {
                            stat.setAttribute('data-target', number);
                            animateCounter(stat, number);
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>

</html>
