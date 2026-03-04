<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('../assets/php/template.php');
$temp = new Template('Pulso - Equipo de Colaboración');
$db = new Conexion();
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }, "showSettings":true}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>pulso/pulso.css" />
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
                            <h1 class="mb-0 pb-0 display-4" id="title"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pulso</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Filtro de Años Start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Filtrar por Año</h5>
                                <div id="yearFilters" class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-primary year-badge active" data-year="">Todos</span>
                                    <!-- Los años se cargarán dinámicamente aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filtro de Años End -->

                <!-- Colaboradores Grid Start -->
                <div class="row" id="teamContainer">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <!-- Colaboradores Grid End -->
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>
    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        let currentYear = null;

        // Cargar años disponibles
        function cargarAnios() {
            fetch('<?php echo $temp->siteURL ?>assets/API/pulso/anios.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success === 1 && data.data) {
                        const yearFilters = document.getElementById('yearFilters');
                        data.data.forEach(anio => {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-outline-primary year-badge';
                            badge.setAttribute('data-year', anio);
                            badge.textContent = anio;
                            badge.addEventListener('click', () => filtrarPorAnio(anio));
                            yearFilters.appendChild(badge);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar años:', error));
        }

        // Cargar colaboradores
        function cargarColaboradores(anio = null) {
            const url = anio
                ? `<?php echo $temp->siteURL ?>assets/API/pulso/leer.php?anio=${anio}`
                : '<?php echo $temp->siteURL ?>assets/API/pulso/leer.php';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('teamContainer');
                    container.innerHTML = '';

                    if (data.success === 1 && data.data && data.data.length > 0) {
                        data.data.forEach(colaborador => {
                            const col = document.createElement('div');
                            col.className = 'col-12 col-md-6 col-lg-4 mb-4';

                            // Determinar la URL de la foto
                            let fotoUrl;
                            if (colaborador.FOTO_URL) {
                                // Si es una URL completa (http/https), usarla directamente
                                if (colaborador.FOTO_URL.startsWith('http')) {
                                    fotoUrl = colaborador.FOTO_URL;
                                } else {
                                    // Si es una ruta relativa, agregar el siteURL
                                    fotoUrl = '<?php echo $temp->siteURL ?>' + colaborador.FOTO_URL;
                                }
                            } else {
                                // Imagen por defecto
                                fotoUrl = '<?php echo $temp->siteURL ?>assets/img/profile/default-avatar.png';
                            }

                            const periodo = colaborador.PERIODO || colaborador.ANIO;

                            col.innerHTML = `
                                <div class="card team-card h-100">
                                    <div class="card-body text-center">
                                        <img src="${fotoUrl}" alt="${colaborador.NOMBRE}"
                                             class="rounded-circle team-avatar mb-3"
                                             onerror="this.src='<?php echo $temp->siteURL ?>pulso/default-avatar.svg'">
                                        <h5 class="card-title mb-2">${colaborador.NOMBRE}</h5>
                                        <p class="text-muted mb-2">${colaborador.CARGO}</p>
                                        <span class="badge bg-info mb-3">${periodo}</span>
                                        ${colaborador.BIO ? `<p class="card-text small">${colaborador.BIO}</p>` : ''}
                                    </div>
                                </div>
                            `;
                            container.appendChild(col);
                        });
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No se encontraron colaboradores${anio ? ' para el año ' + anio : ''}.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar colaboradores:', error);
                    document.getElementById('teamContainer').innerHTML = `
                        <div class="col-12 text-center py-5">
                            <p class="text-danger">Error al cargar los datos</p>
                        </div>
                    `;
                });
        }

        // Filtrar por año
        function filtrarPorAnio(anio) {
            currentYear = anio;

            // Actualizar badges activos
            document.querySelectorAll('.year-badge').forEach(badge => {
                badge.classList.remove('active', 'bg-primary');
                badge.classList.add('bg-outline-primary');
            });

            const activeBadge = document.querySelector(`[data-year="${anio}"]`);
            if (activeBadge) {
                activeBadge.classList.add('active', 'bg-primary');
                activeBadge.classList.remove('bg-outline-primary');
            }

            cargarColaboradores(anio);
        }

        // Evento para "Todos"
        document.querySelector('[data-year=""]').addEventListener('click', () => {
            currentYear = null;
            document.querySelectorAll('.year-badge').forEach(badge => {
                badge.classList.remove('active', 'bg-primary');
                badge.classList.add('bg-outline-primary');
            });
            document.querySelector('[data-year=""]').classList.add('active', 'bg-primary');
            document.querySelector('[data-year=""]').classList.remove('bg-outline-primary');
            cargarColaboradores();
        });

        // Inicializar
        window.addEventListener('DOMContentLoaded', () => {
            cargarAnios();
            cargarColaboradores();
        });
    </script>
</body>

</html>
