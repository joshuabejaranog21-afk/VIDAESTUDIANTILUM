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
                <!-- Hero Section -->
                <?php
                $heroTitulo = $config['hero']['titulo'] ?? 'Vida Estudiantil';
                $heroSubtitulo = $config['hero']['subtitulo'] ?? 'Descubre los clubes, ministerios y actividades';
                $heroBotonTexto = $config['hero']['boton_texto'] ?? 'Explorar Clubes';
                $heroBotonUrl = $config['hero']['boton_url'] ?? 'pages/clubes/';
                $heroColorInicio = $config['hero']['color_inicio'] ?? '#667eea';
                $heroColorFin = $config['hero']['color_fin'] ?? '#764ba2';
                $heroImagenFondo = $config['hero']['imagen_fondo'] ?? '';
                ?>
                <div class="hero-section">
                    <div class="hero-background">
                        <?php if (!empty($heroImagenFondo)): ?>
                            <img src="<?php echo $temp->siteURL . $heroImagenFondo; ?>" alt="Hero" class="hero-image">
                        <?php else: ?>
                            <div class="hero-gradient" style="background: linear-gradient(135deg, <?php echo $heroColorInicio; ?> 0%, <?php echo $heroColorFin; ?> 100%);"></div>
                        <?php endif; ?>
                    </div>
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <h1 class="hero-title"><?php echo htmlspecialchars($heroTitulo); ?></h1>
                        <p class="hero-subtitle"><?php echo htmlspecialchars($heroSubtitulo); ?></p>
                        <a href="<?php echo $temp->siteURL . $heroBotonUrl; ?>" class="btn hero-btn">
                            <?php echo htmlspecialchars($heroBotonTexto); ?>
                        </a>
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
                    <div class="stats-section">
                        <div class="container">
                            <h2 class="text-center mb-5" style="font-size: 2.5rem; font-weight: 700;">
                                <?php echo htmlspecialchars($config['seccion_stats']['titulo'] ?? '¿Por qué unirte?'); ?>
                            </h2>
                            <div class="row">
                                <?php foreach ($estadisticas as $stat): ?>
                                    <div class="col-md-6 col-lg-3 mb-4">
                                        <div class="stat-card">
                                            <div class="stat-icon" style="background: <?php echo htmlspecialchars($stat['COLOR']); ?>;">
                                                <i class="<?php echo htmlspecialchars($stat['ICONO']); ?>"></i>
                                            </div>
                                            <div class="stat-number"><?php echo htmlspecialchars($stat['NUMERO']); ?></div>
                                            <div class="stat-label"><?php echo htmlspecialchars($stat['TITULO']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Clubes Section -->
                <?php if (($config['seccion_clubes']['mostrar'] ?? 'S') === 'S' && !empty($clubes)): ?>
                    <section class="mb-5">
                        <div class="section-header">
                            <h2 class="section-title">
                                <?php echo htmlspecialchars($config['seccion_clubes']['titulo'] ?? 'Clubes Destacados'); ?>
                            </h2>
                            <p class="section-subtitle">
                                <?php echo htmlspecialchars($config['seccion_clubes']['subtitulo'] ?? 'Únete a alguno de nuestros clubes'); ?>
                            </p>
                        </div>
                        <div class="row">
                            <?php foreach ($clubes as $club): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="club-card">
                                        <?php if (!empty($club['IMAGEN'])): ?>
                                            <img src="<?php echo $temp->siteURL . $club['IMAGEN']; ?>" alt="<?php echo htmlspecialchars($club['NOMBRE']); ?>" class="club-card-image">
                                        <?php else: ?>
                                            <div class="club-card-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="club-card-body">
                                            <h5 class="club-card-title"><?php echo htmlspecialchars($club['NOMBRE']); ?></h5>
                                            <p class="club-card-description">
                                                <?php echo htmlspecialchars(substr($club['DESCRIPCION'] ?? '', 0, 100)); ?>
                                                <?php echo strlen($club['DESCRIPCION'] ?? '') > 100 ? '...' : ''; ?>
                                            </p>
                                            <a href="<?php echo $temp->siteURL; ?>pages/clubes/?club=<?php echo $club['ID']; ?>" class="btn club-card-btn">
                                                Ver más
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?php echo $temp->siteURL; ?>pages/clubes/" class="btn btn-lg btn-outline-primary" style="border-radius: 50px; padding: 12px 40px;">
                                Ver todos los clubes
                            </a>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Ministerios Section -->
                <?php if (($config['seccion_ministerios']['mostrar'] ?? 'S') === 'S' && !empty($ministerios)): ?>
                    <section class="mb-5">
                        <div class="section-header">
                            <h2 class="section-title">
                                <?php echo htmlspecialchars($config['seccion_ministerios']['titulo'] ?? 'Ministerios'); ?>
                            </h2>
                            <p class="section-subtitle">
                                <?php echo htmlspecialchars($config['seccion_ministerios']['subtitulo'] ?? 'Crece espiritualmente'); ?>
                            </p>
                        </div>
                        <div class="row">
                            <?php foreach ($ministerios as $ministerio): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="ministerio-card">
                                        <div class="ministerio-icon">
                                            <i class="fas fa-praying-hands"></i>
                                        </div>
                                        <h5 class="ministerio-title"><?php echo htmlspecialchars($ministerio['NOMBRE']); ?></h5>
                                        <p class="ministerio-description">
                                            <?php echo htmlspecialchars(substr($ministerio['DESCRIPCION'] ?? '', 0, 120)); ?>
                                            <?php echo strlen($ministerio['DESCRIPCION'] ?? '') > 120 ? '...' : ''; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?php echo $temp->siteURL; ?>pages/ministerios/" class="btn btn-lg btn-outline-primary" style="border-radius: 50px; padding: 12px 40px;">
                                Ver todos los ministerios
                            </a>
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
