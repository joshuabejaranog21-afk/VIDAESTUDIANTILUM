<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Federación Estudiantil - Universidad de Montemorelos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --dark-color: #252f40;
            --light-color: #f8f9fa;
            --gray-color: #67748e;
            --shadow-soft: 0 2px 12px 0 rgba(0, 0, 0, 0.09);
            --shadow-soft-lg: 0 8px 26px -4px rgba(20, 20, 20, 0.15), 0 8px 9px -5px rgba(20, 20, 20, 0.06);
            --border-radius: 1rem;
            --border-radius-lg: 1.5rem;
            --border-radius-xl: 2rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--gray-color);
            background-color: #fff;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-custom {
            padding: 1.5rem 0;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-soft);
        }

        .navbar-custom .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
        }

        .navbar-custom .nav-link {
            color: white !important;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: all 0.3s;
        }

        .navbar-custom .nav-link:hover {
            opacity: 0.8;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        /* Cards */
        .card-soft {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-soft-lg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            background: white;
        }

        .card-soft:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px -8px rgba(20, 20, 20, 0.25);
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--gray-color);
            margin-bottom: 3rem;
        }

        /* Member Cards */
        .member-card {
            text-align: center;
            padding: 2rem;
        }

        .member-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border: 5px solid white;
        }

        .member-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .member-position {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .member-info {
            color: var(--gray-color);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Info Sections */
        .info-section {
            padding: 80px 0;
        }

        .info-section:nth-child(even) {
            background-color: var(--light-color);
        }

        .info-icon {
            width: 80px;
            height: 80px;
            border-radius: var(--border-radius);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .info-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--gray-color);
        }

        .info-content h3 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Video Section */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-soft-lg);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--dark-color) 0%, #1a2332 100%);
            color: white;
            padding: 60px 0 30px;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer a:hover {
            color: white;
        }

        /* Buttons */
        .btn-soft {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }

        .btn-soft-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-soft-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading */
        .loading-spinner {
            text-align: center;
            padding: 60px 0;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .member-avatar {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-award me-2"></i>
                Federación Estudiantil UM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#miembros">Miembros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#informacion">Información</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#actividades">Actividades</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 hero-content fade-in-up">
                    <h1 class="hero-title" id="heroTitle">Cargando...</h1>
                    <p class="hero-subtitle">
                        Representando a los estudiantes de la Universidad de Montemorelos
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Miembros Section -->
    <section id="miembros" class="info-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Nuestro Equipo <span id="currentYear"></span></h2>
                <p class="section-subtitle">Conoce a los miembros que trabajan por ti</p>
            </div>

            <div id="membersContainer" class="row">
                <div class="col-12 loading-spinner">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Cargando miembros...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ¿Qué es? Section -->
    <section id="informacion" class="info-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center mb-4 mb-lg-0">
                    <div class="info-icon mx-auto">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>
                <div class="col-lg-10">
                    <h2 class="section-title">¿Qué es la Federación?</h2>
                    <div class="info-content" id="queEsContent">
                        <p>Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ¿Cómo se eligen? Section -->
    <section class="info-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center mb-4 mb-lg-0">
                    <div class="info-icon mx-auto">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                </div>
                <div class="col-lg-10">
                    <h2 class="section-title">¿Cómo se eligen sus miembros?</h2>
                    <div class="info-content" id="eleccionContent">
                        <p>Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Actividades Section -->
    <section id="actividades" class="info-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center mb-4 mb-lg-0">
                    <div class="info-icon mx-auto">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="col-lg-10">
                    <h2 class="section-title">¿Qué actividades realizan?</h2>
                    <div class="info-content" id="actividadesContent">
                        <p>Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ¿Para qué sirve? Section -->
    <section class="info-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center mb-4 mb-lg-0">
                    <div class="info-icon mx-auto">
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
                <div class="col-lg-10">
                    <h2 class="section-title">¿Para qué sirve?</h2>
                    <div class="info-content" id="paraQueSirveContent">
                        <p>Cargando información...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section id="video" class="info-section" style="display: none;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Conoce más sobre nosotros</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="video-container" id="videoContainer"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5><i class="fas fa-award me-2"></i>Federación Estudiantil</h5>
                    <p class="mb-4">Universidad de Montemorelos</p>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#inicio">Inicio</a></li>
                        <li class="mb-2"><a href="#miembros">Miembros</a></li>
                        <li class="mb-2"><a href="#informacion">Información</a></li>
                        <li class="mb-2"><a href="#actividades">Actividades</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Contáctanos</h5>
                    <p>
                        <i class="fas fa-envelope me-2"></i>
                        <span id="contactEmail">federacion@um.edu.mx</span>
                    </p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Universidad de Montemorelos. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const apiUrl = 'assets/API/federacion/public_info.php';

        // Load data
        async function loadData() {
            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data.success) {
                    // Update hero
                    if (data.info && data.info.titulo) {
                        document.getElementById('heroTitle').textContent = data.info.titulo;
                    }

                    // Update current year
                    document.getElementById('currentYear').textContent = data.anio;

                    // Load information sections
                    if (data.info) {
                        document.getElementById('queEsContent').innerHTML = data.info.contenido_que_es || '<p>Información no disponible.</p>';
                        document.getElementById('eleccionContent').innerHTML = data.info.contenido_eleccion || '<p>Información no disponible.</p>';
                        document.getElementById('actividadesContent').innerHTML = data.info.contenido_actividades || '<p>Información no disponible.</p>';
                        document.getElementById('paraQueSirveContent').innerHTML = data.info.contenido_para_que_sirve || '<p>Información no disponible.</p>';

                        // Load video if exists
                        if (data.info.video_url) {
                            loadVideo(data.info.video_url);
                        }
                    }

                    // Load members
                    if (data.miembros && data.miembros.length > 0) {
                        renderMembers(data.miembros);
                    } else {
                        document.getElementById('membersContainer').innerHTML = `
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No hay miembros registrados para este año.</p>
                            </div>
                        `;
                    }
                }
            } catch (error) {
                console.error('Error loading data:', error);
                document.getElementById('membersContainer').innerHTML = `
                    <div class="col-12 text-center py-5">
                        <p class="text-danger">Error al cargar la información.</p>
                    </div>
                `;
            }
        }

        // Render members
        function renderMembers(members) {
            let html = '';

            members.forEach((member, index) => {
                const photoUrl = member.foto_url || 'assets/img/profile/user-solid.svg';

                html += `
                    <div class="col-md-6 col-lg-4 mb-4 fade-in-up" style="animation-delay: ${index * 0.1}s">
                        <div class="card-soft member-card">
                            <img src="${photoUrl}" class="member-avatar" alt="${member.nombre}">
                            <h3 class="member-name">${member.nombre}</h3>
                            <div class="member-position">${member.puesto}</div>
                            <div class="member-info">
                                ${member.carrera ? `<p class="mb-2"><i class="fas fa-graduation-cap me-2"></i>${member.carrera}</p>` : ''}
                                ${member.bio ? `<p class="mt-3">${member.bio}</p>` : ''}
                                ${member.email ? `<p class="mt-2"><i class="fas fa-envelope me-2"></i><a href="mailto:${member.email}">${member.email}</a></p>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            document.getElementById('membersContainer').innerHTML = html;
        }

        // Load video
        function loadVideo(url) {
            let embedUrl = url;

            // Convert YouTube URL to embed
            if (url.includes('youtube.com/watch')) {
                const videoId = url.split('v=')[1]?.split('&')[0];
                if (videoId) {
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
            } else if (url.includes('youtu.be/')) {
                const videoId = url.split('youtu.be/')[1]?.split('?')[0];
                if (videoId) {
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
            }

            document.getElementById('videoContainer').innerHTML = `
                <iframe src="${embedUrl}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            `;
            document.getElementById('video').style.display = 'block';
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>
