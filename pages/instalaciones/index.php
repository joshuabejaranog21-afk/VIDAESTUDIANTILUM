<?php
include('../../assets/php/template.php');
$temp = new Template('Instalaciones Deportivas');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('instalaciones', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar permisos específicos
$puede_crear = $temp->tiene_permiso('instalaciones', 'crear');
$puede_editar = $temp->tiene_permiso('instalaciones', 'editar');
$puede_eliminar = $temp->tiene_permiso('instalaciones', 'eliminar');
$puede_ver_galeria = $temp->tiene_permiso('galeria', 'ver');
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
                                    <li class="breadcrumb-item active" aria-current="page">Instalaciones</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end gap-2">
                            <?php if($puede_ver_galeria): ?>
                            <a href="<?php echo $temp->siteURL ?>pages/galeria/?modulo=instalaciones" class="btn btn-outline-info btn-icon btn-icon-start">
                                <i data-acorn-icon="image"></i>
                                <span>Gestionar Imágenes</span>
                            </a>
                            <?php endif; ?>
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nueva Instalación</span>
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
                                <table id="tablaInstalaciones" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Ubicación</th>
                                            <th>Capacidad</th>
                                            <th>Disponible</th>
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

    <!-- Modal para crear/editar instalación -->
    <div class="modal fade" id="modalInstalacion" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInstalacionTitle">Nueva Instalación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formInstalacion">
                    <div class="modal-body">
                        <input type="hidden" id="instalacionId">

                        <!-- Información Básica -->
                        <div class="mb-4">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" id="instalacionNombre" name="nombre" class="form-control" required maxlength="200">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tipo *</label>
                                    <select id="instalacionTipo" name="tipo" class="form-select" required>
                                        <option value="">Selecciona tipo</option>
                                        <option value="CANCHA">Cancha</option>
                                        <option value="GYM">Gimnasio</option>
                                        <option value="PISTA">Pista</option>
                                        <option value="OTRO">Otro</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Capacidad</label>
                                    <input type="number" id="instalacionCapacidad" name="capacidad" class="form-control" min="0">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea id="instalacionDescripcion" name="descripcion" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Ubicación -->
                        <div class="mb-4">
                            <h6 class="mb-3">Ubicación</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Ubicación / Link de Google Maps</label>
                                    <input type="text" id="instalacionUbicacion" name="ubicacion" class="form-control" maxlength="500" placeholder="Ej: Campus Norte - Edificio Deportivo, o link de Google Maps">
                                    <small class="text-muted">Puede ingresar una ubicación descriptiva o un link directo de Google Maps</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Detalles -->
                        <div class="mb-4">
                            <h6 class="mb-3">Información Adicional</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Horarios</label>
                                    <textarea id="instalacionHorarios" name="horarios" class="form-control" rows="3" placeholder="Lun-Vie: 6:00 AM - 10:00 PM&#10;Sábados: 8:00 AM - 6:00 PM"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Servicios Disponibles</label>
                                    <textarea id="instalacionServicios" name="servicios" class="form-control" rows="3" placeholder="• Duchas&#10;• Casilleros&#10;• Estacionamiento"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Reglas / Reglamento</label>
                                    <textarea id="instalacionReglas" name="reglas" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Costo</label>
                                    <input type="text" id="instalacionCosto" name="costo" class="form-control" maxlength="100" placeholder="Ej: Gratis, $50/hora, Incluido en colegiatura">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Estados -->
                        <div class="mb-3">
                            <h6 class="mb-3">Estado y Orden</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Disponible</label>
                                    <select id="instalacionDisponible" name="disponible" class="form-select">
                                        <option value="S">Sí - Disponible</option>
                                        <option value="N">No - No disponible</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select id="instalacionActivo" name="activo" class="form-select">
                                        <option value="S">Activo</option>
                                        <option value="N">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Orden</label>
                                    <input type="number" id="instalacionOrden" name="orden" class="form-control" min="0" value="0">
                                    <small class="text-muted">0 = automático</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i data-acorn-icon="info"></i> <strong>Nota:</strong> Las imágenes se gestionan desde el módulo "Galería" o usando el botón "Gestionar Imágenes" en la parte superior.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span id="btnTexto">Crear Instalación</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Instalación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Información General</h6>
                            <hr>
                            <p><strong>ID:</strong> <span id="detalle-id"></span></p>
                            <p><strong>Nombre:</strong> <span id="detalle-nombre"></span></p>
                            <p><strong>Tipo:</strong> <span id="detalle-tipo"></span></p>
                            <p><strong>Capacidad:</strong> <span id="detalle-capacidad"></span></p>
                            <p><strong>Disponible:</strong> <span id="detalle-disponible"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle-activo"></span></p>
                        </div>

                        <!-- Ubicación -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Ubicación</h6>
                            <hr>
                            <p id="detalle-ubicacion"></p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Descripción</h6>
                            <hr>
                            <p id="detalle-descripcion"></p>
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Horarios</h6>
                            <hr>
                            <div id="detalle-horarios"></div>
                        </div>

                        <!-- Servicios -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Servicios</h6>
                            <hr>
                            <div id="detalle-servicios"></div>
                        </div>

                        <!-- Reglas -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Reglas</h6>
                            <hr>
                            <div id="detalle-reglas"></div>
                        </div>

                        <!-- Costo -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Costo</h6>
                            <hr>
                            <p id="detalle-costo"></p>
                        </div>

                        <!-- Imágenes -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Imágenes <span id="detalle-total-imagenes" class="badge bg-primary"></span></h6>
                            <hr>
                            <div id="detalle-imagenes" class="row g-2">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <?php if($puede_editar): ?>
                    <button type="button" class="btn btn-primary" id="btnEditarDesdeDetalles">Editar</button>
                    <?php endif; ?>
                    <?php if($puede_ver_galeria): ?>
                    <a href="#" id="btnGestionarImagenesDetalles" class="btn btn-info">Gestionar Imágenes</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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

        let modalInstalacion;
        let modalDetalles;

        // Abrir formulario para crear
        function abrirFormularioCrear() {
            document.getElementById('formInstalacion').reset();
            document.getElementById('instalacionId').value = '';
            document.getElementById('modalInstalacionTitle').textContent = 'Nueva Instalación';
            document.getElementById('btnTexto').textContent = 'Crear Instalación';
            modalInstalacion.show();
        }

        // Editar instalación
        function editarInstalacion(id) {
            fetch(siteURL + 'assets/API/instalaciones/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const inst = data.data;
                        document.getElementById('instalacionId').value = inst.ID;
                        document.getElementById('instalacionNombre').value = inst.NOMBRE;
                        document.getElementById('instalacionTipo').value = inst.TIPO;
                        document.getElementById('instalacionCapacidad').value = inst.CAPACIDAD || '';
                        document.getElementById('instalacionDescripcion').value = inst.DESCRIPCION || '';
                        document.getElementById('instalacionUbicacion').value = inst.UBICACION || '';
                        document.getElementById('instalacionHorarios').value = inst.HORARIOS || '';
                        document.getElementById('instalacionServicios').value = inst.SERVICIOS || '';
                        document.getElementById('instalacionReglas').value = inst.REGLAS || '';
                        document.getElementById('instalacionCosto').value = inst.COSTO || '';
                        document.getElementById('instalacionDisponible').value = inst.DISPONIBLE;
                        document.getElementById('instalacionActivo').value = inst.ACTIVO;
                        document.getElementById('instalacionOrden').value = inst.ORDEN;

                        document.getElementById('modalInstalacionTitle').textContent = 'Editar: ' + inst.NOMBRE;
                        document.getElementById('btnTexto').textContent = 'Actualizar Instalación';

                        modalInstalacion.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Guardar instalación
        document.getElementById('formInstalacion').addEventListener('submit', function(e) {
            e.preventDefault();

            const instalacionId = document.getElementById('instalacionId').value;
            const formData = new FormData(this);

            let url;
            if (instalacionId) {
                formData.append('id', instalacionId);
                url = siteURL + 'assets/API/instalaciones/editar.php';
            } else {
                url = siteURL + 'assets/API/instalaciones/crear.php';
            }

            fetch(url, {
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
                        modalInstalacion.hide();
                        cargarInstalaciones();
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
                });
        });

        // Ver detalles
        function verDetalles(id) {
            fetch(siteURL + 'assets/API/instalaciones/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const inst = data.data;

                        document.getElementById('detalle-id').textContent = inst.ID;
                        document.getElementById('detalle-nombre').textContent = inst.NOMBRE;
                        document.getElementById('detalle-tipo').textContent = inst.TIPO;
                        document.getElementById('detalle-capacidad').textContent = inst.CAPACIDAD || 'No especificado';

                        const disponibleBadge = inst.DISPONIBLE == 'S' ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-secondary">No disponible</span>';
                        document.getElementById('detalle-disponible').innerHTML = disponibleBadge;

                        const activoBadge = inst.ACTIVO == 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                        document.getElementById('detalle-activo').innerHTML = activoBadge;

                        // Mostrar ubicación (si es link de Google Maps, hacerlo clickeable)
                        if (inst.UBICACION) {
                            if (inst.UBICACION.includes('maps.google.com') || inst.UBICACION.includes('goo.gl/maps')) {
                                document.getElementById('detalle-ubicacion').innerHTML = `<a href="${inst.UBICACION}" target="_blank" class="btn btn-sm btn-outline-primary"><i data-acorn-icon="navigation"></i> Abrir en Google Maps</a>`;
                            } else {
                                document.getElementById('detalle-ubicacion').textContent = inst.UBICACION;
                            }
                        } else {
                            document.getElementById('detalle-ubicacion').textContent = 'No especificado';
                        }
                        document.getElementById('detalle-descripcion').textContent = inst.DESCRIPCION || 'Sin descripción';
                        document.getElementById('detalle-horarios').innerHTML = inst.HORARIOS ? '<pre>' + inst.HORARIOS + '</pre>' : 'No especificado';
                        document.getElementById('detalle-servicios').innerHTML = inst.SERVICIOS ? '<pre>' + inst.SERVICIOS + '</pre>' : 'No especificado';
                        document.getElementById('detalle-reglas').innerHTML = inst.REGLAS ? '<pre>' + inst.REGLAS + '</pre>' : 'No especificado';
                        document.getElementById('detalle-costo').textContent = inst.COSTO || 'No especificado';

                        // Imágenes
                        const imagenesContainer = document.getElementById('detalle-imagenes');
                        document.getElementById('detalle-total-imagenes').textContent = inst.TOTAL_IMAGENES;

                        if (inst.IMAGENES && inst.IMAGENES.length > 0) {
                            let html = '';
                            inst.IMAGENES.forEach(img => {
                                html += `
                                    <div class="col-6 col-md-3">
                                        <img src="${img.URL_IMAGEN}" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open('${img.URL_IMAGEN}', '_blank')">
                                        <small class="d-block text-center mt-1">${img.TIPO}</small>
                                    </div>
                                `;
                            });
                            imagenesContainer.innerHTML = html;
                        } else {
                            imagenesContainer.innerHTML = '<p class="text-muted">Sin imágenes</p>';
                        }

                        // Botón gestionar imágenes
                        document.getElementById('btnGestionarImagenesDetalles').href = siteURL + 'pages/galeria/?modulo=instalaciones&id_registro=' + inst.ID;

                        // Botón editar desde detalles
                        document.getElementById('btnEditarDesdeDetalles').onclick = function() {
                            modalDetalles.hide();
                            setTimeout(() => editarInstalacion(inst.ID), 300);
                        };

                        modalDetalles.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Eliminar instalación
        function eliminarInstalacion(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar la instalación "${nombre}"?\n\nEsto también eliminará todas las imágenes asociadas.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/instalaciones/eliminar.php', {
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
                        cargarInstalaciones();
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
                });
        }

        // Cargar instalaciones
        function cargarInstalaciones() {
            fetch(siteURL + 'assets/API/instalaciones/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        // Destruir DataTable si existe
                        if ($.fn.DataTable.isDataTable('#tablaInstalaciones')) {
                            $('#tablaInstalaciones').DataTable().clear().destroy();
                        }

                        // Construir HTML
                        const tbody = document.querySelector('#tablaInstalaciones tbody');
                        let htmlRows = '';

                        data.data.forEach(inst => {
                            const tipoLabels = {
                                'CANCHA': 'Cancha',
                                'GYM': 'Gimnasio',
                                'PISCINA': 'Piscina',
                                'PISTA': 'Pista',
                                'OTRO': 'Otro'
                            };

                            const row = `
                                <tr>
                                    <td>${inst.ID}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            ${inst.IMAGEN_PRINCIPAL ? `<img src="${inst.IMAGEN_PRINCIPAL}" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;">` : ''}
                                            <div>
                                                <strong>${inst.NOMBRE}</strong>
                                                ${inst.DESCRIPCION ? `<br><small class="text-muted">${inst.DESCRIPCION.substring(0, 50)}...</small>` : ''}
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">${tipoLabels[inst.TIPO] || inst.TIPO}</span></td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        ${inst.UBICACION ?
                                            (inst.UBICACION.includes('maps.google') || inst.UBICACION.includes('goo.gl/maps') ?
                                                `<a href="${inst.UBICACION}" target="_blank" class="btn btn-xs btn-outline-primary" title="${inst.UBICACION}">
                                                    <i data-acorn-icon="navigation"></i> Ver mapa
                                                </a>`
                                                : `<span title="${inst.UBICACION}">${inst.UBICACION}</span>`)
                                            : '-'}
                                    </td>
                                    <td>${inst.CAPACIDAD || '-'}</td>
                                    <td>
                                        <span class="badge ${inst.DISPONIBLE == 'S' ? 'bg-success' : 'bg-secondary'}">
                                            ${inst.DISPONIBLE == 'S' ? 'Sí' : 'No'}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge ${inst.ACTIVO == 'S' ? 'bg-success' : 'bg-secondary'}">
                                            ${inst.ACTIVO == 'S' ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="verDetalles(${inst.ID})">
                                            <i class="fa fa-eye"></i>
                                            <span class="d-none d-xxl-inline-block">Ver</span>
                                        </button>
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarInstalacion(${inst.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="eliminarInstalacion(${inst.ID}, '${inst.NOMBRE.replace(/'/g, "\\'")}')">
                                            <i class="fa fa-trash"></i>
                                            <span class="d-none d-xxl-inline-block">Eliminar</span>
                                        </button>` : ''}
                                    </td>
                                </tr>
                            `;
                            htmlRows += row;
                        });

                        tbody.innerHTML = htmlRows;

                        // Reinicializar DataTable
                        $('#tablaInstalaciones').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                            },
                            order: [[0, 'asc']],
                            pageLength: 25
                        });

                        // Actualizar iconos
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
                });
        }

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar modales
            modalInstalacion = new bootstrap.Modal(document.getElementById('modalInstalacion'));
            modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));

            // Cargar instalaciones
            cargarInstalaciones();
        });
    </script>
</body>

</html>
