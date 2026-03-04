<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VRE - Vida Estudiantil | Universidad de Montemorelos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #003366;
            --secondary: #F2CD00;
            --dark: #1a2332;
            --light: #f8f9fa;
            --gray: #67748e;
            --navy: #003366;
            --gold: #F2CD00;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #F2CD00;
            --danger: #dc3545;
            --shadow: 0 0.3125rem 0.625rem 0 rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 26px -4px rgba(20, 20, 20, 0.15);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--gray);
            background: #fff;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-custom {
            padding: 1rem 0;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-custom.scrolled {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.5rem 0;
        }

        .navbar-brand-custom {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
        }

        .nav-link-custom {
            color: var(--dark) !important;
            font-weight: 600;
            font-size: 0.875rem;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius);
            transition: all 0.3s;
        }

        .nav-link-custom:hover {
            background: var(--primary);
            color: var(--gold) !important;
        }

        /* Video Hero Section */
        .video-hero {
            position: relative;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0;
        }

        .video-hero video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 1;
        }

        .video-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.85), rgba(242, 205, 0, 0.3));
            z-index: 2;
        }

        .video-hero-content {
            position: relative;
            z-index: 3;
            color: white;
            text-align: center;
            max-width: 800px;
            padding: 2rem;
        }

        .video-hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .video-hero-subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        /* Sections */
        .section {
            padding: 100px 0;
            position: relative;
        }

        .section-gradient {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray);
            text-align: center;
            margin-bottom: 4rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .btn-gradient {
            background: var(--navy);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: var(--shadow);
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            background: var(--gold);
            color: var(--navy);
        }

        .btn-outline-gradient {
            border: 2px solid var(--navy);
            color: var(--navy);
            background: white;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-gradient:hover {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
        }

        /* Cards */
        .card-custom {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s;
            border: none;
            height: 100%;
        }

        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .card-img-custom {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .card-title-custom {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .card-text-custom {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Badge */
        .badge-gradient {
            background: var(--navy);
            color: var(--gold);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-success { background: var(--success); color: white; }
        .badge-info { background: var(--info); color: white; }
        .badge-warning { background: var(--warning); color: var(--dark); }

        /* Category Cards */
        .category-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            transition: all 0.3s;
            height: 100%;
        }

        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .category-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--radius-lg);
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--gold);
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow);
        }

        /* Member Card */
        .member-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            transition: all 0.3s;
        }

        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .member-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            border: 4px solid white;
            box-shadow: var(--shadow);
        }

        .member-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .member-position {
            color: var(--navy);
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Event Card */
        .event-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }

        .event-card:hover {
            transform: translateX(10px);
            box-shadow: var(--shadow-lg);
        }

        .event-date {
            min-width: 90px;
            text-align: center;
            padding: 1rem;
            border-radius: var(--radius-lg);
            background: var(--navy);
            color: var(--gold);
        }

        .event-day {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            line-height: 1;
        }

        .event-month {
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Stats */
        .stat-box {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--navy);
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        /* Info Section */
        .info-box {
            padding: 2rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow);
            height: 100%;
        }

        .info-icon-lg {
            width: 100px;
            height: 100px;
            border-radius: var(--radius-xl);
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        /* Footer */
        .footer {
            background: var(--navy);
            color: white;
            padding: 80px 0 30px;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links a {
            display: inline-flex;
            width: 45px;
            height: 45px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            margin-right: 0.75rem;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: var(--gold);
            color: var(--navy);
            transform: translateY(-3px);
            padding-left: 0;
        }

        /* Tabs */
        .nav-pills .nav-link {
            border-radius: var(--radius-lg);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s;
        }

        .nav-pills .nav-link.active {
            background: var(--navy);
            color: var(--gold);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 60px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .video-hero-title {
                font-size: 2rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .stat-number {
                font-size: 2rem;
            }
            .section {
                padding: 60px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand-custom" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                VRE - Vida Estudiantil
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link-custom" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#anuarios">Anuarios</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#pulso">Pulso UM</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#federacion">Federación</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#involucrate">Involúcrate</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#eventos">Eventos</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#campus">Campus</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="mi-foto.php">Mi Foto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Video Hero Section -->
    <section id="inicio" class="video-hero">
        <video autoplay muted loop playsinline id="heroVideo">
            <source src="assets/video/vida-estudiantil.mp4" type="video/mp4">
        </video>
        <div class="video-hero-content fade-in">
            <h1 class="video-hero-title">Vive la Experiencia UM</h1>
            <p class="video-hero-subtitle">
                Tu portal centralizado para la vida estudiantil en la Universidad de Montemorelos
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="#involucrate" class="btn btn-gradient">
                    <i class="fas fa-users me-2"></i>Involúcrate
                </a>
                <a href="#anuarios" class="btn btn-outline-gradient">
                    <i class="fas fa-book me-2"></i>Explora Anuarios
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section section-gradient">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" id="statClubes">0</span>
                        <span class="stat-label">Clubes</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" id="statMinisterios">0</span>
                        <span class="stat-label">Ministerios</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" id="statDeportes">0</span>
                        <span class="stat-label">Deportes</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <span class="stat-number" id="statEventos">0</span>
                        <span class="stat-label">Eventos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Anuarios Section -->
    <section id="anuarios" class="section">
        <div class="container">
            <h2 class="section-title">Anuarios Institucionales</h2>
            <p class="section-subtitle">
                Repositorio completo de anuarios de la Universidad de Montemorelos. Revive momentos históricos y conmemorativos.
            </p>

            <div id="anuariosContainer" class="row g-4">
                <div class="col-12 loading">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3">Cargando anuarios...</p>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="pages/anuarios/" class="btn btn-gradient btn-lg">
                    Ver Todos los Anuarios
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Pulso UM Section -->
    <section id="pulso" class="section section-gradient">
        <div class="container">
            <h2 class="section-title">Pulso UM</h2>
            <p class="section-subtitle">
                Conoce al equipo de colaboradores detrás de nuestro anuario institucional
            </p>

            <div id="pulsoContainer" class="row g-4">
                <div class="col-12 loading">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="pulso/" class="btn btn-gradient">
                    Ver Equipo Completo
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Federación Estudiantil Section -->
    <section id="federacion" class="section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title text-start">Federación Estudiantil</h2>
                    <p class="section-subtitle text-start">
                        Representando tus intereses y trabajando por la comunidad estudiantil
                    </p>
                </div>
                <div class="col-lg-6 text-end">
                    <a href="federacion.php" class="btn btn-gradient">
                        Conocer Más
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>

            <div id="federacionContainer" class="row g-4">
                <div class="col-12 loading">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Involúcrate Section -->
    <section id="involucrate" class="section section-gradient">
        <div class="container">
            <h2 class="section-title">Involúcrate</h2>
            <p class="section-subtitle">
                Encuentra tu lugar en la vibrante comunidad estudiantil de la UM
            </p>

            <!-- Nav Tabs -->
            <ul class="nav nav-pills justify-content-center mb-5" id="involucrateTab" role="tablist">
                <li class="nav-item me-2">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#clubes">
                        <i class="fas fa-star me-2"></i>Clubes
                    </button>
                </li>
                <li class="nav-item me-2">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ministerios">
                        <i class="fas fa-heart me-2"></i>Ministerios
                    </button>
                </li>
                <li class="nav-item me-2">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#deportes">
                        <i class="fas fa-basketball-ball me-2"></i>Deportes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#cocurriculares">
                        <i class="fas fa-briefcase me-2"></i>Co-Curriculares
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Clubes -->
                <div class="tab-pane fade show active" id="clubes">
                    <div id="clubesContainer" class="row g-4">
                        <div class="col-12 loading">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>

                <!-- Ministerios -->
                <div class="tab-pane fade" id="ministerios">
                    <div id="ministeriosContainer" class="row g-4">
                        <div class="col-12 loading">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>

                <!-- Deportes -->
                <div class="tab-pane fade" id="deportes">
                    <div id="deportesContainer" class="row g-4">
                        <div class="col-12 loading">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>

                <!-- Co-Curriculares -->
                <div class="tab-pane fade" id="cocurriculares">
                    <div id="cocurricularesContainer" class="row g-4">
                        <div class="col-12 loading">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Eventos Section -->
    <section id="eventos" class="section">
        <div class="container">
            <h2 class="section-title">Próximos Eventos</h2>
            <p class="section-subtitle">
                Eventos organizados por la Federación Estudiantil y la Comisión de Culturales
            </p>

            <div id="eventosContainer" class="row">
                <div class="col-12 loading">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vida en el Campus Section -->
    <section id="campus" class="section section-gradient">
        <div class="container">
            <h2 class="section-title">Vida en el Campus</h2>
            <p class="section-subtitle">
                Amenidades disponibles para tu recreación y bienestar dentro de la universidad
            </p>

            <div id="amenidadesContainer" class="row g-4">
                <div class="col-12 loading">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background: var(--navy); color: white;">
        <div class="container text-center">
            <h2 style="color: var(--gold); font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem;">
                ¿Listo para ser parte de la experiencia UM?
            </h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.95;">
                Explora todas las oportunidades que tenemos para ti
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="mi-foto.php" class="btn" style="background: var(--gold); color: var(--navy); padding: 1rem 2rem; border-radius: var(--radius-lg); font-weight: 700; transition: all 0.3s;">
                    <i class="fas fa-camera me-2"></i>Mi Foto
                </a>
                <a href="#involucrate" class="btn" style="border: 2px solid var(--gold); color: var(--gold); background: transparent; padding: 1rem 2rem; border-radius: var(--radius-lg); font-weight: 700; transition: all 0.3s;">
                    <i class="fas fa-users me-2"></i>Involúcrate Ahora
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>
                        <i class="fas fa-graduation-cap me-2"></i>
                        VRE - Vida Estudiantil
                    </h5>
                    <p class="mb-4">
                        Sistema centralizado de vida estudiantil de la Universidad de Montemorelos
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5>Explora</h5>
                    <a href="#anuarios">Anuarios</a>
                    <a href="#pulso">Pulso UM</a>
                    <a href="#federacion">Federación</a>
                    <a href="#eventos">Eventos</a>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5>Involúcrate</h5>
                    <a href="#clubes">Clubes</a>
                    <a href="#ministerios">Ministerios</a>
                    <a href="#deportes">Deportes</a>
                    <a href="#cocurriculares">Co-Curriculares</a>
                </div>

                <div class="col-lg-3 col-md-12">
                    <h5>Contacto</h5>
                    <p>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Universidad de Montemorelos<br>
                        Montemorelos, N.L., México
                    </p>
                    <p>
                        <i class="fas fa-envelope me-2"></i>
                        vidaestudiantil@um.edu.mx
                    </p>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center pt-4">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Universidad de Montemorelos. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const apiUrl = 'assets/API/public/all_data.php';
        let allData = {};

        // Load all data
        async function loadAllData() {
            try {
                const response = await fetch(apiUrl);
                allData = await response.json();

                if (allData.success) {
                    updateStats();
                    renderAnuarios();
                    renderPulso();
                    renderFederacion();
                    renderClubes();
                    renderMinisterios();
                    renderDeportes();
                    renderCocurriculares();
                    renderEventos();
                    renderAmenidades();
                }
            } catch (error) {
                console.error('Error loading data:', error);
            }
        }

        // Update stats
        function updateStats() {
            if (allData.stats) {
                animateNumber('statClubes', allData.stats.clubes_total);
                animateNumber('statMinisterios', allData.stats.ministerios_total);
                animateNumber('statDeportes', allData.stats.deportes_total);
                animateNumber('statEventos', allData.stats.eventos_total);
            }
        }

        // Animate numbers
        function animateNumber(elementId, target) {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current);
                }
            }, 30);
        }

        // Render Anuarios
        function renderAnuarios() {
            const container = document.getElementById('anuariosContainer');
            if (!allData.anuarios || allData.anuarios.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay anuarios disponibles</p></div>';
                return;
            }

            let html = '';
            allData.anuarios.slice(0, 6).forEach((anuario, index) => {
                html += `
                    <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="card-custom">
                            <img src="${anuario.imagen || 'assets/img/default-anuario.jpg'}"
                                 class="card-img-custom" alt="${anuario.titulo}">
                            <div class="card-body-custom">
                                <h3 class="card-title-custom">${anuario.titulo}</h3>
                                <span class="badge-gradient mb-2">${anuario.anio}</span>
                                <p class="card-text-custom">
                                    ${anuario.descripcion ? anuario.descripcion.substring(0, 100) + '...' : ''}
                                </p>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-heart me-1"></i>${anuario.likes}
                                        <i class="fas fa-eye ms-3 me-1"></i>${anuario.vistas}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Pulso
        function renderPulso() {
            const container = document.getElementById('pulsoContainer');
            if (!allData.pulso || allData.pulso.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay miembros registrados</p></div>';
                return;
            }

            let html = '';
            allData.pulso.slice(0, 8).forEach((miembro, index) => {
                html += `
                    <div class="col-md-6 col-lg-3 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="member-card">
                            <img src="${miembro.foto || 'assets/img/profile/user-solid.svg'}"
                                 class="member-avatar" alt="${miembro.nombre}">
                            <h5 class="member-name">${miembro.nombre}</h5>
                            <p class="member-position">${miembro.cargo}</p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Federación
        function renderFederacion() {
            const container = document.getElementById('federacionContainer');
            if (!allData.federacion || allData.federacion.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay miembros registrados</p></div>';
                return;
            }

            let html = '';
            allData.federacion.slice(0, 8).forEach((miembro, index) => {
                html += `
                    <div class="col-md-6 col-lg-3 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="member-card">
                            <img src="${miembro.foto || 'assets/img/profile/user-solid.svg'}"
                                 class="member-avatar" alt="${miembro.nombre}">
                            <h5 class="member-name">${miembro.nombre}</h5>
                            <p class="member-position">${miembro.puesto}</p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Clubes
        function renderClubes() {
            const container = document.getElementById('clubesContainer');
            if (!allData.clubes || allData.clubes.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay clubes disponibles</p></div>';
                return;
            }

            let html = '';
            allData.clubes.forEach((club, index) => {
                html += `
                    <div class="col-md-6 col-lg-3 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="card-title-custom">${club.nombre}</h5>
                            <p class="card-text-custom">
                                ${club.descripcion ? club.descripcion.substring(0, 80) + '...' : ''}
                            </p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Ministerios
        function renderMinisterios() {
            const container = document.getElementById('ministeriosContainer');
            if (!allData.ministerios || allData.ministerios.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay ministerios disponibles</p></div>';
                return;
            }

            let html = '';
            allData.ministerios.forEach((ministerio, index) => {
                html += `
                    <div class="col-md-6 col-lg-3 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h5 class="card-title-custom">${ministerio.nombre}</h5>
                            <span class="badge-custom badge-info mb-2">${ministerio.tipo}</span>
                            <p class="card-text-custom">
                                ${ministerio.descripcion ? ministerio.descripcion.substring(0, 80) + '...' : ''}
                            </p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Deportes
        function renderDeportes() {
            const container = document.getElementById('deportesContainer');
            if (!allData.deportes || allData.deportes.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay deportes disponibles</p></div>';
                return;
            }

            let html = '';
            allData.deportes.forEach((deporte, index) => {
                html += `
                    <div class="col-md-6 col-lg-3 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-basketball-ball"></i>
                            </div>
                            <h5 class="card-title-custom">${deporte.nombre}</h5>
                            <p class="card-text-custom">
                                ${deporte.descripcion ? deporte.descripcion.substring(0, 80) + '...' : ''}
                            </p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Co-Curriculares
        function renderCocurriculares() {
            const container = document.getElementById('cocurricularesContainer');
            if (!allData.cocurriculares || allData.cocurriculares.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay servicios disponibles</p></div>';
                return;
            }

            let html = '';
            allData.cocurriculares.forEach((servicio, index) => {
                html += `
                    <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h5 class="card-title-custom">${servicio.nombre}</h5>
                            <span class="badge-custom badge-success mb-2">${servicio.categoria}</span>
                            <p class="card-text-custom">
                                ${servicio.descripcion ? servicio.descripcion.substring(0, 80) + '...' : ''}
                            </p>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render Eventos
        function renderEventos() {
            const container = document.getElementById('eventosContainer');
            if (!allData.eventos || allData.eventos.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay eventos próximos</p></div>';
                return;
            }

            let html = '<div class="col-lg-8 mx-auto">';
            allData.eventos.slice(0, 6).forEach((evento, index) => {
                const fecha = new Date(evento.fecha);
                const dia = fecha.getDate();
                const mes = fecha.toLocaleDateString('es', { month: 'short' });

                html += `
                    <div class="event-card fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="event-date">
                            <span class="event-day">${dia}</span>
                            <span class="event-month">${mes}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h4 style="color: var(--dark); font-weight: 700; margin-bottom: 0.5rem;">${evento.titulo}</h4>
                            <p style="color: var(--gray); margin-bottom: 0.5rem;">${evento.descripcion || ''}</p>
                            <div class="mb-2">
                                ${evento.categoria ? `<span class="badge-gradient me-2">${evento.categoria}</span>` : ''}
                                ${evento.destacado === 'S' ? '<span class="badge-custom badge-warning">Destacado</span>' : ''}
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>${evento.lugar || 'Por confirmar'}
                            </small>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        // Render Amenidades
        function renderAmenidades() {
            const container = document.getElementById('amenidadesContainer');
            if (!allData.amenidades || allData.amenidades.length === 0) {
                container.innerHTML = '<div class="col-12 text-center"><p>No hay amenidades disponibles</p></div>';
                return;
            }

            const iconMap = {
                'COMEDOR': 'utensils',
                'SNACK': 'coffee',
                'RESIDENCIA': 'home',
                'BIBLIOTECA': 'book',
                'TIENDA': 'shopping-cart',
                'OTRO': 'building'
            };

            let html = '';
            allData.amenidades.forEach((amenidad, index) => {
                const icon = iconMap[amenidad.tipo] || 'building';
                html += `
                    <div class="col-md-6 col-lg-4 fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-${icon}"></i>
                            </div>
                            <h5 class="card-title-custom">${amenidad.nombre}</h5>
                            <span class="badge-custom badge-success mb-2">${amenidad.tipo}</span>
                            <p class="card-text-custom">
                                ${amenidad.descripcion ? amenidad.descripcion.substring(0, 100) + '...' : ''}
                            </p>
                            ${amenidad.ubicacion ? `<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${amenidad.ubicacion}</small>` : ''}
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', loadAllData);
    </script>
</body>
</html>
