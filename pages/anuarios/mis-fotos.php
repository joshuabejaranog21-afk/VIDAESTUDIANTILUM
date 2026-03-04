<?php
include('../../assets/php/template.php');
$temp = new Template('Mis Fotografías');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// TODO: Obtener matrícula del usuario desde la sesión
// Por ahora usamos una variable de ejemplo
$matricula_usuario = ''; // Implementar según tu sistema de autenticación
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
</head>

<body>
    <div id="root">
        <?php $temp->nav() ?>

        <main>
            <div class="container">
                <!-- Title Start -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">Mis Fotografías</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/anuarios/">Anuarios</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Mis Fotografías</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Title End -->

                <!-- Info Alert -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Aquí encontrarás todas tus fotografías personales que aparecen en los anuarios institucionales.
                            Busca por año o anuario específico.
                        </div>
                    </div>
                </div>

                <!-- Filters Start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Matrícula</label>
                                        <input type="text" class="form-control" id="matriculaInput" placeholder="Ingresa tu matrícula">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Año</label>
                                        <select class="form-select" id="yearFilter">
                                            <option value="">Todos los años</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary w-100" onclick="loadMyPhotos()">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filters End -->

                <!-- Photos Grid Start -->
                <div class="row" id="photosContainer">
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Ingresa tu matrícula y haz clic en "Buscar" para ver tus fotografías</p>
                    </div>
                </div>
                <!-- Photos Grid End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/anuarios/";

        // Load photos
        function loadMyPhotos() {
            const matricula = $('#matriculaInput').val().trim();
            const year = $('#yearFilter').val();

            if (!matricula) {
                alert('Por favor ingresa tu matrícula');
                return;
            }

            $('#photosContainer').html(`
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `);

            const params = new URLSearchParams({
                matricula: matricula,
                anio: year
            });

            fetch(url + 'mis-fotos.php?' + params.toString())
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.data.length > 0) {
                        renderPhotos(result.data);
                    } else {
                        $('#photosContainer').html(`
                            <div class="col-12 text-center py-5">
                                <i class="fa fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron fotografías con la matrícula proporcionada</p>
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#photosContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger">Error al cargar las fotografías</div>
                        </div>
                    `);
                });
        }

        // Render photos
        function renderPhotos(photos) {
            // Group by anuario
            const groupedPhotos = {};
            photos.forEach(photo => {
                const year = photo.ANIO || 'Sin año';
                if (!groupedPhotos[year]) {
                    groupedPhotos[year] = [];
                }
                groupedPhotos[year].push(photo);
            });

            let html = '';

            Object.keys(groupedPhotos).sort().reverse().forEach(year => {
                html += `
                    <div class="col-12 mb-4">
                        <h4 class="mb-3">
                            <i class="fa fa-calendar"></i> Año ${year}
                            <span class="badge bg-primary">${groupedPhotos[year].length} foto(s)</span>
                        </h4>
                        <div class="row">
                `;

                groupedPhotos[year].forEach(photo => {
                    html += `
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100">
                                <img src="${photo.FOTO_URL}" class="card-img-top" alt="${photo.NOMBRE_ESTUDIANTE}" style="height: 300px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title">${photo.NOMBRE_ESTUDIANTE}</h6>
                                    <p class="card-text small text-muted mb-1">
                                        <strong>Matrícula:</strong> ${photo.MATRICULA}
                                    </p>
                                    ${photo.CARRERA ? `<p class="card-text small text-muted mb-1"><strong>Carrera:</strong> ${photo.CARRERA}</p>` : ''}
                                    ${photo.FACULTAD ? `<p class="card-text small text-muted mb-0"><strong>Facultad:</strong> ${photo.FACULTAD}</p>` : ''}
                                </div>
                                ${photo.ID_ANUARIO ? `
                                <div class="card-footer">
                                    <a href="ver.php?id=${photo.ID_ANUARIO}" class="btn btn-sm btn-outline-primary w-100">
                                        Ver Anuario
                                    </a>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            $('#photosContainer').html(html);
        }

        // Load years for filter
        function loadYears() {
            fetch(url + 'listar.php')
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        const years = [...new Set(result.data.map(a => a.ANIO))].sort().reverse();
                        let options = '<option value="">Todos los años</option>';
                        years.forEach(year => {
                            options += `<option value="${year}">${year}</option>`;
                        });
                        $('#yearFilter').html(options);
                    }
                });
        }

        // Enter key on matricula input
        $('#matriculaInput').on('keypress', function(e) {
            if (e.which === 13) {
                loadMyPhotos();
            }
        });

        // Load on page ready
        $(document).ready(function() {
            loadYears();
        });
    </script>
</body>

</html>
