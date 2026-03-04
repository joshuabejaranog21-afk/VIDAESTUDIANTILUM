<?php
include('../../assets/php/template.php');
$temp = new Template('Ver Anuario');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

$anuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($anuario_id === 0) {
    header('Location: ' . $temp->siteURL . 'pages/anuarios/');
    exit();
}

// Registrar vista
$db = new Conexion();
$db->query("UPDATE VRE_ANUARIOS SET VISTAS = VISTAS + 1 WHERE ID = $anuario_id");
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <style>
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

        .anuario-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .anuario-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="rgba(255,255,255,0.05)" width="50" height="50"/></svg>');
            opacity: 0.3;
        }

        .anuario-header .container {
            position: relative;
            z-index: 1;
        }

        .like-button {
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
            border: 2px solid white;
        }

        .like-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .like-button.liked {
            background-color: #e74c3c !important;
            color: white !important;
            border-color: #e74c3c !important;
            animation: heartBeat 0.5s;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1.1); }
        }

        .content-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .content-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .portada-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .portada-card img {
            transition: transform 0.3s ease;
        }

        .portada-card:hover img {
            transform: scale(1.05);
        }

        .stat-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(10px);
        }

        .btn-action {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .info-card {
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .photo-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .photo-card img {
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .photo-card:hover img {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <div id="root">
        <?php $temp->nav() ?>

        <main>
            <!-- Header Start -->
            <div class="anuario-header" id="anuarioHeader">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-white" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Header End -->

            <div class="container">
                <!-- Back Button -->
                <div class="row mb-4">
                    <div class="col-12">
                        <a href="<?php echo $temp->siteURL ?>pages/anuarios/" class="btn btn-outline-primary">
                            <i class="fa fa-arrow-left"></i> Volver a Anuarios
                        </a>
                    </div>
                </div>

                <!-- Anuario Content Start -->
                <div class="row" id="anuarioContent">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <!-- Anuario Content End -->

                <!-- My Photos Section (if user is logged in) -->
                <div class="row mt-5" id="myPhotosSection" style="display: none;">
                    <div class="col-12">
                        <div class="card content-card mb-4">
                            <div class="card-body p-4">
                                <h3 class="mb-4">
                                    <i class="fa fa-user-circle text-primary"></i>
                                    Mis Fotografías en este Anuario
                                </h3>
                                <div id="myPhotosContainer">
                                    <!-- Fotos del usuario se cargarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/anuarios/";
        const anuarioId = <?php echo $anuario_id ?>;
        let hasLiked = false;

        // Load anuario details
        function loadAnuario() {
            fetch(url + 'ver.php?id=' + anuarioId)
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        renderAnuario(result.data);
                        checkLikeStatus();
                    } else {
                        window.location.href = '<?php echo $temp->siteURL ?>pages/anuarios/';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar el anuario');
                });
        }

        // Render anuario
        function renderAnuario(anuario) {
            // Update header
            const conmemorativoBadge = anuario.ES_CONMEMORATIVO === 'S'
                ? `<span class="badge bg-warning text-dark mb-3"><i class="fa fa-star"></i> Anuario Conmemorativo: ${anuario.RAZON_CONMEMORATIVA}</span><br>`
                : '';

            $('#anuarioHeader').html(`
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            ${conmemorativoBadge}
                            <h1 class="display-3 mb-3 fw-bold" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">${anuario.TITULO}</h1>
                            <h3 class="mb-4" style="opacity: 0.9;">Año ${anuario.ANIO}</h3>
                            <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
                                <span class="stat-badge">
                                    <i class="fa fa-heart"></i>
                                    <span id="likesCount">${anuario.LIKES}</span> likes
                                </span>
                                <span class="stat-badge">
                                    <i class="fa fa-eye"></i>
                                    ${anuario.VISTAS} vistas
                                </span>
                                <span class="stat-badge">
                                    <i class="fa fa-file-pdf"></i>
                                    ${anuario.TOTAL_PAGINAS} páginas
                                </span>
                            </div>
                            <button class="btn btn-light like-button" id="likeButton" onclick="toggleLike()">
                                <i class="fa fa-heart"></i> Me gusta
                            </button>
                        </div>
                    </div>
                </div>
            `);

            // Update content
            let contentHtml = `
                <div class="col-md-8">
                    <div class="card content-card mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-3"><i class="fa fa-info-circle text-primary"></i> Descripción</h4>
                            <p class="text-muted" style="font-size: 1.05rem; line-height: 1.7;">${anuario.DESCRIPCION || 'Sin descripción disponible'}</p>
                        </div>
                    </div>

                    ${anuario.PDF_URL ? `
                    <div class="card content-card mb-4">
                        <div class="card-body p-4">
                            <h4 class="mb-4"><i class="fa fa-book-open text-primary"></i> Ver Anuario Digital</h4>
                            <div class="d-grid gap-3">
                                <a href="visor-pdf.php?id=${anuario.ID}" class="btn btn-primary btn-lg btn-action" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <i class="fa fa-book-open"></i> Abrir Visor Interactivo
                                </a>
                                <a href="${anuario.PDF_URL}" target="_blank" class="btn btn-outline-primary btn-action">
                                    <i class="fa fa-download"></i> Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>

                <div class="col-md-4">
                    ${anuario.IMAGEN_PORTADA ? `
                    <div class="card portada-card mb-4">
                        <div style="overflow: hidden;">
                            <img src="${anuario.IMAGEN_PORTADA}" class="card-img-top" alt="Portada" style="width: 100%; height: auto;">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="mb-0"><i class="fa fa-image text-primary"></i> Portada Original</h5>
                        </div>
                    </div>
                    ` : ''}

                    ${anuario.FOTOGRAFOS ? `
                    <div class="card info-card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fa fa-camera"></i> Fotógrafos</h5>
                            <p class="mb-0" style="line-height: 1.6;">${anuario.FOTOGRAFOS}</p>
                        </div>
                    </div>
                    ` : ''}

                    ${anuario.CONTRIBUYENTES ? `
                    <div class="card info-card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fa fa-users"></i> Contribuyentes</h5>
                            <p class="mb-0" style="line-height: 1.6;">${anuario.CONTRIBUYENTES}</p>
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;

            $('#anuarioContent').html(contentHtml);

            // Load user's photos in this anuario
            loadMyPhotos();
        }

        // Check if user has liked
        function checkLikeStatus() {
            fetch(url + 'check-like.php?id=' + anuarioId)
                .then(r => r.json())
                .then(result => {
                    if (result.liked) {
                        hasLiked = true;
                        $('#likeButton').addClass('liked');
                    }
                });
        }

        // Toggle like
        function toggleLike() {
            const action = hasLiked ? 'unlike' : 'like';

            fetch(url + action + '.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + anuarioId
            })
            .then(r => r.json())
            .then(result => {
                if (result.success) {
                    hasLiked = !hasLiked;
                    $('#likeButton').toggleClass('liked');
                    $('#likesCount').text(result.likes);
                }
            });
        }

        // Load user's photos
        function loadMyPhotos() {
            fetch(url + 'mis-fotos.php?id_anuario=' + anuarioId)
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.data.length > 0) {
                        $('#myPhotosSection').show();
                        renderMyPhotos(result.data);
                    }
                });
        }

        // Render user's photos
        function renderMyPhotos(photos) {
            let html = '<div class="row">';
            photos.forEach((photo, index) => {
                html += `
                    <div class="col-md-3 mb-4" style="animation: fadeInUp 0.5s ease ${index * 0.1}s both;">
                        <div class="card photo-card h-100">
                            <div style="overflow: hidden; height: 250px;">
                                <img src="${photo.FOTO_URL}" class="card-img-top" alt="${photo.NOMBRE_ESTUDIANTE}">
                            </div>
                            <div class="card-body">
                                <h6 class="card-title fw-bold text-primary">${photo.NOMBRE_ESTUDIANTE}</h6>
                                <p class="card-text small text-muted mb-0" style="line-height: 1.5;">
                                    ${photo.CARRERA || ''}<br>
                                    ${photo.FACULTAD || ''}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#myPhotosContainer').html(html);
        }

        // Load on page ready
        $(document).ready(function() {
            loadAnuario();
        });
    </script>
</body>

</html>
