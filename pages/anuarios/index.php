<?php
include('../../assets/php/template.php');
$temp = new Template('Anuarios');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
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
                <!-- Title and Top Buttons Start -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4" id="title">Anuarios Institucionales</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Anuarios</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Filters and Search Start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card filters-card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Search -->
                                    <div class="col-md-4">
                                        <label class="form-label">Buscar</label>
                                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por título o descripción...">
                                    </div>

                                    <!-- Decade Filter -->
                                    <div class="col-md-3">
                                        <label class="form-label">Década</label>
                                        <select class="form-select" id="decadeFilter">
                                            <option value="">Todas las décadas</option>
                                            <option value="2020">2020s</option>
                                            <option value="2010">2010s</option>
                                            <option value="2000">2000s</option>
                                            <option value="1990">1990s</option>
                                            <option value="1980">1980s</option>
                                            <option value="1970">1970s</option>
                                            <option value="1960">1960s</option>
                                            <option value="1950">1950s</option>
                                        </select>
                                    </div>

                                    <!-- Order By -->
                                    <div class="col-md-3">
                                        <label class="form-label">Ordenar por</label>
                                        <select class="form-select" id="orderBy">
                                            <option value="recent">Más reciente</option>
                                            <option value="oldest">Más antiguo</option>
                                            <option value="likes">Más votado</option>
                                            <option value="views">Más visto</option>
                                        </select>
                                    </div>

                                    <!-- Conmemorative Filter -->
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="conmemorativeFilter">
                                            <label class="form-check-label" for="conmemorativeFilter">
                                                Solo conmemorativos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filters and Search End -->

                <!-- Anuarios Grid Start -->
                <div class="row" id="anuariosContainer">
                    <!-- Los anuarios se cargarán aquí dinámicamente -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <!-- Anuarios Grid End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

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

        .anuario-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 15px;
            overflow: hidden;
        }

        .anuario-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .anuario-card-img {
            height: 350px;
            object-fit: cover;
            position: relative;
            transition: transform 0.3s ease;
        }

        .anuario-card:hover .anuario-card-img {
            transform: scale(1.05);
        }

        .anuario-card .card-img-wrapper {
            overflow: hidden;
            position: relative;
        }

        .anuario-card .year-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 8px 16px;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .conmemorative-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .stats-row {
            display: flex;
            gap: 20px;
            padding: 12px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            font-size: 0.9rem;
        }

        .stat-item i {
            color: #667eea;
        }

        .btn-view-anuario {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-view-anuario:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .filters-card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-radius: 15px;
        }

        .page-title-container h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/anuarios/";
        let currentFilters = {
            search: '',
            decade: '',
            order: 'recent',
            conmemorative: false
        };

        // Load anuarios
        function loadAnuarios() {
            const params = new URLSearchParams({
                search: currentFilters.search,
                decade: currentFilters.decade,
                order: currentFilters.order,
                conmemorative: currentFilters.conmemorative ? 'S' : ''
            });

            fetch(url + 'listar.php?' + params.toString())
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        renderAnuarios(result.data);
                    } else {
                        $('#anuariosContainer').html('<div class="col-12"><div class="alert alert-warning rounded-3 shadow-sm"><i class="fa fa-info-circle"></i> No se encontraron anuarios</div></div>');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#anuariosContainer').html('<div class="col-12"><div class="alert alert-danger rounded-3 shadow-sm"><i class="fa fa-exclamation-circle"></i> Error al cargar los anuarios</div></div>');
                });
        }

        // Render anuarios
        function renderAnuarios(anuarios) {
            if (anuarios.length === 0) {
                $('#anuariosContainer').html('<div class="col-12"><div class="alert alert-info rounded-3 shadow-sm"><i class="fa fa-search"></i> No se encontraron anuarios con los filtros seleccionados</div></div>');
                return;
            }

            let html = '';
            anuarios.forEach((anuario, index) => {
                const conmemorativoBadge = anuario.ES_CONMEMORATIVO === 'S'
                    ? `<div class="conmemorative-badge"><i class="fa fa-star"></i> Conmemorativo</div>`
                    : '';

                const imagenPortada = anuario.IMAGEN_PORTADA || '<?php echo $temp->siteURL ?>assets/img/default-anuario.jpg';

                const descripcion = anuario.DESCRIPCION && anuario.DESCRIPCION.length > 120
                    ? anuario.DESCRIPCION.substring(0, 120) + '...'
                    : (anuario.DESCRIPCION || 'Sin descripción disponible');

                html += `
                    <div class="col-12 col-md-6 col-xl-4 mb-4" style="animation: fadeInUp 0.5s ease ${index * 0.1}s both;">
                        <div class="card h-100 anuario-card">
                            <div class="card-img-wrapper">
                                <img src="${imagenPortada}" class="card-img-top anuario-card-img" alt="${anuario.TITULO}">
                                ${conmemorativoBadge}
                                <div class="year-badge">${anuario.ANIO}</div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3" style="color: #333;">${anuario.TITULO}</h5>
                                <p class="card-text text-muted small flex-grow-1 mb-3">${descripcion}</p>

                                <div class="stats-row mb-3">
                                    <div class="stat-item">
                                        <i class="fa fa-heart"></i>
                                        <span>${anuario.LIKES}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fa fa-eye"></i>
                                        <span>${anuario.VISTAS}</span>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="ver.php?id=${anuario.ID}" class="btn btn-primary btn-view-anuario">
                                        <i class="fa fa-book-open"></i> Ver Anuario
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#anuariosContainer').html(html);
        }

        // Event listeners
        $('#searchInput').on('input', function() {
            currentFilters.search = $(this).val();
            loadAnuarios();
        });

        $('#decadeFilter').on('change', function() {
            currentFilters.decade = $(this).val();
            loadAnuarios();
        });

        $('#orderBy').on('change', function() {
            currentFilters.order = $(this).val();
            loadAnuarios();
        });

        $('#conmemorativeFilter').on('change', function() {
            currentFilters.conmemorative = $(this).is(':checked');
            loadAnuarios();
        });

        // Load on page ready
        $(document).ready(function() {
            loadAnuarios();
        });
    </script>
</body>

</html>
