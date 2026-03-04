<?php
include('../../assets/php/template.php');
$temp = new Template('Clubes');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('clubes', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar si puede crear/editar/eliminar
$puede_crear = $temp->tiene_permiso('clubes', 'crear');
$puede_editar = $temp->tiene_permiso('clubes', 'editar');
$puede_eliminar = $temp->tiene_permiso('clubes', 'eliminar');
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }, "showSettings":true}'>

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
                        <!-- Title Start -->
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4" id="title"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Involúcrate</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Clubes</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="window.location='crear/'">
                                <i data-acorn-icon="plus"></i>
                                <span>Nuevo Club</span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <!-- Top Buttons End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card">
                            <div class="card-body">
                                <!-- DataTable Start -->
                                <table id="tablaClubes" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Horario</th>
                                            <th>Lugar</th>
                                            <th>Cupo</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Se llenará vía JavaScript -->
                                    </tbody>
                                </table>
                                <!-- DataTable End -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->
            </div>
        </main>
        <!-- Layout Footer Start -->
        <?php $temp->footer() ?>
        <!-- Layout Footer End -->
    </div>

    <!-- Theme Settings Modal Start -->
    <?php $temp->modalSettings() ?>
    <!-- Theme Settings Modal End -->

    <!-- Search Modal Start -->
    <?php $temp->modalSearch() ?>
    <!-- Search Modal End -->

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;

        // Cargar clubes
        function cargarClubes() {
            fetch(siteURL + 'assets/API/clubes/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const tbody = document.querySelector('#tablaClubes tbody');
                        tbody.innerHTML = '';

                        data.data.forEach(club => {
                            const row = `
                                <tr>
                                    <td>${club.ID}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            ${club.IMAGEN_URL ? `<img src="${club.IMAGEN_URL}" class="rounded-circle me-2" style="width:40px;height:40px;object-fit:cover;" alt="${club.NOMBRE}">` : ''}
                                            <div>
                                                <strong>${club.NOMBRE}</strong>
                                                ${club.DESCRIPCION ? `<br><small class="text-muted">${club.DESCRIPCION.substring(0, 50)}...</small>` : ''}
                                            </div>
                                        </div>
                                    </td>
                                    <td>${club.HORARIO || '-'}<br><small class="text-muted">${club.DIA_REUNION || ''}</small></td>
                                    <td>${club.LUGAR || '-'}</td>
                                    <td>
                                        ${club.CUPO_MAXIMO ? `${club.CUPO_ACTUAL || 0} / ${club.CUPO_MAXIMO}` : 'Sin límite'}
                                    </td>
                                    <td>${club.RESPONSABLE_NOMBRE || '-'}</td>
                                    <td>
                                        <span class="badge ${club.ACTIVO == 'S' ? 'bg-success' : 'bg-secondary'}">
                                            ${club.ACTIVO == 'S' ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="ver/?id=${club.ID}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                <i data-acorn-icon="eye"></i>
                                            </a>
                                            ${puedeEditar ? `
                                            <a href="editar/?id=${club.ID}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i data-acorn-icon="edit"></i>
                                            </a>
                                            ` : ''}
                                            ${puedeEliminar ? `
                                            <button onclick="eliminarClub(${club.ID}, '${club.NOMBRE}')" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i data-acorn-icon="bin"></i>
                                            </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });

                        // Inicializar DataTable
                        if ($.fn.DataTable.isDataTable('#tablaClubes')) {
                            $('#tablaClubes').DataTable().destroy();
                        }
                        $('#tablaClubes').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                            },
                            order: [[1, 'asc']],
                            pageLength: 25
                        });

                        // Inicializar iconos de Acorn
                        if (typeof acorn !== 'undefined') {
                            acorn.icons();
                        }

                    } else {
                        jQuery.notify({
                            title: 'Error',
                            message: data.message
                        }, {
                            type: 'danger'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'Error al cargar los clubes'
                    }, {
                        type: 'danger'
                    });
                });
        }

        // Eliminar club
        function eliminarClub(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar el club "${nombre}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/clubes/eliminar.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        jQuery.notify({
                            title: 'Éxito',
                            message: data.message
                        }, {
                            type: 'success'
                        });
                        cargarClubes();
                    } else {
                        jQuery.notify({
                            title: 'Error',
                            message: data.message
                        }, {
                            type: 'danger'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'Error al eliminar el club'
                    }, {
                        type: 'danger'
                    });
                });
        }

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', cargarClubes);
    </script>
</body>

</html>