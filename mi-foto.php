<?php
session_start();

// Verificar si el estudiante está logueado
$is_logged = isset($_SESSION['estudiante_logged']) && $_SESSION['estudiante_logged'] === true;
$matricula = $is_logged ? $_SESSION['estudiante_matricula'] : '';
$nombre = $is_logged ? trim($_SESSION['estudiante_nombre'] . ' ' . $_SESSION['estudiante_apellido']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Foto - Universidad de Montemorelos</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --accent: #f59e0b;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .header-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 35px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 60px 50px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            max-width: 480px;
            margin: 80px auto;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .login-box h1 {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            font-weight: 800;
            font-size: 2.5rem;
        }

        .login-box p {
            color: #6b7280;
            margin-bottom: 35px;
            font-size: 1.05rem;
        }

        .login-box i.fa-camera {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid var(--gray-200);
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
            font-size: 1.05rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            transition: left 0.3s ease;
        }

        .btn-login:hover::before {
            left: 0;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
        }

        .btn-login i,
        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .photo-item {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .photo-item:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }

        .photo-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.4s ease;
        }

        .photo-item:hover .photo-img {
            transform: scale(1.05);
        }

        .photo-info {
            padding: 24px;
            background: linear-gradient(to bottom, white 0%, var(--gray-50) 100%);
        }

        .photo-info h5 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1.15rem;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .bg-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
        }

        .bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .bg-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
        }

        .bg-dark {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
        }

        .empty-state {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 80px 40px;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .empty-state i {
            font-size: 5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 30px 25px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .btn-back {
            background: white;
            color: var(--primary);
            padding: 12px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            border: 2px solid var(--primary);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-logout {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.35);
        }

        /* Mejoras adicionales */
        .header-card h1 {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        .header-card p {
            color: #6b7280;
            font-size: 1.05rem;
        }

        /* Modal styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        }

        .modal-header {
            border-bottom: 1px solid var(--gray-200);
            padding: 20px 30px;
            background: linear-gradient(to bottom, white 0%, var(--gray-50) 100%);
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 30px;
        }

        .modal-footer {
            border-top: 1px solid var(--gray-200);
            padding: 20px 30px;
            background: var(--gray-50);
            border-radius: 0 0 20px 20px;
        }

        .modal-footer .btn {
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
        }

        .modal-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .modal-footer .btn-success {
            background: linear-gradient(135deg, var(--secondary) 0%, #059669 100%);
            border: none;
        }

        .modal-footer .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .modal-footer .btn-secondary {
            background: white;
            color: var(--dark);
            border: 2px solid var(--gray-200);
        }

        .modal-footer .btn-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-200);
        }

        @media (max-width: 768px) {
            .photo-grid {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <?php if (!$is_logged): ?>
            <!-- Login Box -->
            <div class="login-box">
                <i class="fas fa-camera fa-4x mb-4" style="color: var(--primary);"></i>
                <h1>Mi Foto</h1>
                <p>Inicia sesión con tu cuenta de UM Móvil para ver tu repositorio de fotografías</p>

                <form id="loginForm">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="matricula" placeholder="Matrícula (7 dígitos)"
                               maxlength="7" pattern="[0-9]{7}" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" id="password" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>

                <div class="mt-4">
                    <a href="home.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                    </a>
                </div>

                <div id="loginMessage" class="mt-3"></div>
            </div>
        <?php else: ?>
            <!-- Logged In Content -->
            <div class="header-card">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="mb-2" style="color: var(--primary); font-weight: 800;">
                            <i class="fas fa-camera me-2"></i>Mi Foto
                        </h1>
                        <p class="mb-0" style="font-size: 1.1rem; color: #666;">
                            <i class="fas fa-user me-2"></i><?php echo $nombre; ?>
                            <span class="ms-3">
                                <i class="fas fa-id-card me-2"></i><?php echo $matricula; ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <a href="home.php" class="btn btn-back me-2">
                            <i class="fas fa-home me-2"></i>Inicio
                        </a>
                        <button class="btn btn-logout" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-row" id="statsRow">
                <div class="stat-card">
                    <div class="stat-number" id="totalFotos">0</div>
                    <div class="stat-label">Total Fotos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="fotosMes">0</div>
                    <div class="stat-label">Este Mes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="fotosIndividual">0</div>
                    <div class="stat-label">Individuales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="fotosGrupal">0</div>
                    <div class="stat-label">Grupales</div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loadingPhotos" class="empty-state">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3">Cargando tus fotografías...</p>
            </div>

            <!-- Photos Grid -->
            <div class="photo-grid" id="photosGrid" style="display: none;"></div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <i class="fas fa-camera-retro"></i>
                <h3 class="mb-3">Aún no tienes fotografías</h3>
                <p class="text-muted">Las fotografías son subidas por el administrador del sistema</p>
                <p class="text-muted">Cuando tengas fotos disponibles, aparecerán aquí</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($is_logged): ?>
    <!-- Image Viewer Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageFullsize" src="" alt="" style="max-width: 100%; border-radius: 10px;">
                    <p id="imageDescription" class="mt-3 text-muted"></p>
                </div>
                <div class="modal-footer">
                    <a id="flickrLinkBtn" href="#" target="_blank" class="btn btn-primary me-auto">
                        <i class="fab fa-flickr me-2"></i>Ver en Flickr
                    </a>
                    <a id="downloadBtn" href="#" download class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Descargar
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const apiUrl = 'assets/API/';
        const isLogged = <?php echo $is_logged ? 'true' : 'false'; ?>;
        const matricula = '<?php echo $matricula; ?>';
        let currentPhotoId = null;

        <?php if (!$is_logged): ?>
        // Login form
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const matriculaInput = document.getElementById('matricula').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('loginMessage');

            messageDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Iniciando sesión...';

            try {
                // Crear FormData para enviar al servidor
                const formData = new FormData();
                formData.append('username', matriculaInput);
                formData.append('password', password);

                const response = await fetch(apiUrl + 'auth/login-estudiante.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    messageDiv.innerHTML = '<div class="alert alert-success">¡Sesión iniciada! Redirigiendo...</div>';
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger">' + (result.message || 'Error al iniciar sesión') + '</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
            }
        });
        <?php else: ?>
        // Load photos on page load
        window.addEventListener('DOMContentLoaded', loadPhotos);

        function loadPhotos() {
            fetch(apiUrl + 'repositorio/listar.php?matricula=' + matricula)
                .then(r => r.json())
                .then(result => {
                    document.getElementById('loadingPhotos').style.display = 'none';

                    if (result.success && result.fotos && result.fotos.length > 0) {
                        updateStats(result.fotos);
                        renderPhotos(result.fotos);
                        document.getElementById('photosGrid').style.display = 'grid';
                    } else {
                        document.getElementById('emptyState').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingPhotos').innerHTML =
                        '<div class="alert alert-danger">Error al cargar las fotografías</div>';
                });
        }

        function updateStats(fotos) {
            const total = fotos.length;
            const thisMonth = fotos.filter(f => {
                const date = new Date(f.fecha_subida);
                const now = new Date();
                return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
            }).length;
            const individual = fotos.filter(f => f.tipo_foto === 'INDIVIDUAL').length;
            const grupal = fotos.filter(f => f.tipo_foto === 'GRUPAL').length;

            document.getElementById('totalFotos').textContent = total;
            document.getElementById('fotosMes').textContent = thisMonth;
            document.getElementById('fotosIndividual').textContent = individual;
            document.getElementById('fotosGrupal').textContent = grupal;
        }

        function renderPhotos(fotos) {
            const grid = document.getElementById('photosGrid');
            const badgeColors = {
                'INDIVIDUAL': 'primary',
                'GRUPAL': 'success',
                'EVENTO': 'warning',
                'ACADEMICA': 'info',
                'OTRA': 'secondary'
            };

            grid.innerHTML = fotos.map(foto => {
                const fechaFoto = foto.fecha_foto ? new Date(foto.fecha_foto) : null;
                const anioFoto = fechaFoto ? fechaFoto.getFullYear() : null;
                const flickrPageUrl = foto.flickr_page_url || foto.foto_url;

                return `
                <div class="photo-item">
                    <img src="${foto.foto_url}" class="photo-img" alt="${foto.titulo || 'Foto'}"
                         onerror="this.src='assets/img/placeholder.png'"
                         onclick="viewImage(${foto.id}, '${foto.foto_url}', '${escapeHtml(foto.titulo)}', '${escapeHtml(foto.descripcion || '')}', '${anioFoto || ''}', '${flickrPageUrl}')">
                    <div class="photo-info">
                        <h5>${foto.titulo || 'Sin título'}</h5>
                        <div class="mb-2">
                            <span class="badge bg-${badgeColors[foto.tipo_foto] || 'secondary'}">${foto.tipo_foto}</span>
                            ${anioFoto ? `<span class="badge bg-dark ms-1">${anioFoto}</span>` : ''}
                        </div>
                        ${foto.descripcion ? `<p class="mt-2 mb-0 small text-muted">${foto.descripcion.substring(0, 80)}${foto.descripcion.length > 80 ? '...' : ''}</p>` : ''}
                        <p class="mt-2 mb-0 small text-muted">
                            <i class="fas fa-calendar me-1"></i>${fechaFoto ? fechaFoto.toLocaleDateString('es-MX') : 'Sin fecha'}
                        </p>
                    </div>
                </div>
                `;
            }).join('');
        }

        function viewImage(id, url, title, description, anio, flickrPageUrl) {
            currentPhotoId = id;
            const titleText = title || 'Fotografía';
            const displayTitle = anio ? `${titleText} (${anio})` : titleText;
            document.getElementById('imageTitle').textContent = displayTitle;
            document.getElementById('imageFullsize').src = url;
            document.getElementById('imageDescription').textContent = description || 'Sin descripción';

            // Configurar botón de Flickr - abrir la página de Flickr (no solo la imagen)
            const flickrUrl = flickrPageUrl || url;
            document.getElementById('flickrLinkBtn').href = flickrUrl;

            // Configurar botón de descarga
            document.getElementById('downloadBtn').href = url;
            document.getElementById('downloadBtn').download = titleText + '.jpg';

            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        function logout() {
            if (confirm('¿Estás seguro de cerrar sesión?')) {
                $.ajax({
                    url: apiUrl + 'auth/logout-estudiante.php',
                    type: 'POST',
                    success: function() {
                        window.location.reload();
                    }
                });
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }
        <?php endif; ?>
    </script>
</body>
</html>
