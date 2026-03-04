<?php
include('../../assets/php/template.php');
$temp = new Template('Co-Curriculares');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('cocurriculares', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar permisos específicos
$puede_crear = $temp->tiene_permiso('cocurriculares', 'crear');
$puede_editar = $temp->tiene_permiso('cocurriculares', 'editar');
$puede_eliminar = $temp->tiene_permiso('cocurriculares', 'eliminar');
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
                                    <li class="breadcrumb-item active" aria-current="page">Co-Curriculares</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end gap-2">
                            <?php if($puede_ver_galeria): ?>
                            <a href="<?php echo $temp->siteURL ?>pages/galeria/?modulo=cocurriculares" class="btn btn-outline-info btn-icon btn-icon-start">
                                <i data-acorn-icon="image"></i>
                                <span>Gestionar Imágenes</span>
                            </a>
                            <?php endif; ?>
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nuevo Programa/Servicio</span>
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
                                <table id="tablaCocurriculares" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Responsable</th>
                                            <th>Cupo</th>
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
    <div class="modal fade" id="modalCocurricular" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCocurricularTitle">Nuevo Programa/Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCocurricular">
                    <div class="modal-body">
                        <input type="hidden" id="cocurricularId">

                        <!-- Información Básica -->
                        <div class="mb-4">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" id="cocurricularNombre" name="nombre" class="form-control" required maxlength="200">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tipo *</label>
                                    <select id="cocurricularTipo" name="tipo" class="form-select" required>
                                        <option value="">Selecciona tipo</option>
                                        <option value="PROGRAMA">Programa</option>
                                        <option value="SERVICIO">Servicio</option>
                                        <option value="APOYO">Apoyo</option>
                                        <option value="OTRO">Otro</option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea id="cocurricularDescripcion" name="descripcion" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Objetivo</label>
                                    <textarea id="cocurricularObjetivo" name="objetivo" class="form-control" rows="2" placeholder="¿Cuál es el objetivo principal de este programa/servicio?"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Requisitos</label>
                                    <textarea id="cocurricularRequisitos" name="requisitos" class="form-control" rows="3" placeholder="• Requisito 1&#10;• Requisito 2"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Beneficios</label>
                                    <textarea id="cocurricularBeneficios" name="beneficios" class="form-control" rows="3" placeholder="• Beneficio 1&#10;• Beneficio 2"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Información de Contacto/Responsable -->
                        <div class="mb-4">
                            <h6 class="mb-3">Responsable del Programa</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nombre del Responsable</label>
                                    <input type="text" id="cocurricularResponsableNombre" name="responsable_nombre" class="form-control" maxlength="200">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="cocurricularResponsableEmail" name="responsable_email" class="form-control" maxlength="200">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" id="cocurricularResponsableTelefono" name="responsable_telefono" class="form-control" maxlength="50">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Detalles Operativos -->
                        <div class="mb-4">
                            <h6 class="mb-3">Información Operativa</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ubicación</label>
                                    <input type="text" id="cocurricularUbicacion" name="ubicacion" class="form-control" maxlength="500" placeholder="Ej: Edificio Central, Aula 201">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Horarios</label>
                                    <textarea id="cocurricularHorarios" name="horarios" class="form-control" rows="2" placeholder="Lun-Vie: 2:00 PM - 4:00 PM"></textarea>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cupo Máximo</label>
                                    <input type="number" id="cocurricularCupoMaximo" name="cupo_maximo" class="form-control" min="0" placeholder="Dejar vacío si no aplica">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Estados -->
                        <div class="mb-3">
                            <h6 class="mb-3">Estado y Orden</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select id="cocurricularActivo" name="activo" class="form-select">
                                        <option value="S">Activo</option>
                                        <option value="N">Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Orden</label>
                                    <input type="number" id="cocurricularOrden" name="orden" class="form-control" min="0" value="0">
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
                            <span id="btnTexto">Crear Programa/Servicio</span>
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
                    <h5 class="modal-title">Detalles del Programa/Servicio</h5>
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
                            <p><strong>Estado:</strong> <span id="detalle-activo"></span></p>
                        </div>

                        <!-- Responsable -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Responsable del Programa</h6>
                            <hr>
                            <p><strong>Nombre:</strong> <span id="detalle-responsable-nombre"></span></p>
                            <p><strong>Email:</strong> <span id="detalle-responsable-email"></span></p>
                            <p><strong>Teléfono:</strong> <span id="detalle-responsable-telefono"></span></p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Descripción</h6>
                            <hr>
                            <p id="detalle-descripcion"></p>
                        </div>

                        <!-- Objetivo -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Objetivo</h6>
                            <hr>
                            <p id="detalle-objetivo"></p>
                        </div>

                        <!-- Requisitos -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Requisitos</h6>
                            <hr>
                            <div id="detalle-requisitos"></div>
                        </div>

                        <!-- Beneficios -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Beneficios</h6>
                            <hr>
                            <div id="detalle-beneficios"></div>
                        </div>

                        <!-- Información Operativa -->
                        <div class="col-md-4 mb-4">
                            <h6 class="mb-3">Ubicación</h6>
                            <hr>
                            <p id="detalle-ubicacion"></p>
                        </div>

                        <!-- Horarios -->
                        <div class="col-md-4 mb-4">
                            <h6 class="mb-3">Horarios</h6>
                            <hr>
                            <div id="detalle-horarios"></div>
                        </div>

                        <!-- Cupo -->
                        <div class="col-md-4 mb-4">
                            <h6 class="mb-3">Cupo Máximo</h6>
                            <hr>
                            <p id="detalle-cupo"></p>
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

        let modalCocurricular;
        let modalDetalles;

        // Abrir formulario para crear
        function abrirFormularioCrear() {
            document.getElementById('formCocurricular').reset();
            document.getElementById('cocurricularId').value = '';
            document.getElementById('modalCocurricularTitle').textContent = 'Nuevo Programa/Servicio';
            document.getElementById('btnTexto').textContent = 'Crear Programa/Servicio';
            modalCocurricular.show();
        }

        // Editar co-curricular
        function editarCocurricular(id) {
            fetch(siteURL + 'assets/API/cocurriculares/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const inst = data.data;
                        document.getElementById('cocurricularId').value = inst.ID;
                        document.getElementById('cocurricularNombre').value = inst.NOMBRE;
                        document.getElementById('cocurricularTipo').value = inst.TIPO;
                        document.getElementById('cocurricularDescripcion').value = inst.DESCRIPCION || '';
                        document.getElementById('cocurricularObjetivo').value = inst.OBJETIVO || '';
                        document.getElementById('cocurricularRequisitos').value = inst.REQUISITOS || '';
                        document.getElementById('cocurricularBeneficios').value = inst.BENEFICIOS || '';
                        document.getElementById('cocurricularResponsableNombre').value = inst.RESPONSABLE_NOMBRE || '';
                        document.getElementById('cocurricularResponsableEmail').value = inst.RESPONSABLE_EMAIL || '';
                        document.getElementById('cocurricularResponsableTelefono').value = inst.RESPONSABLE_TELEFONO || '';
                        document.getElementById('cocurricularUbicacion').value = inst.UBICACION || '';
                        document.getElementById('cocurricularHorarios').value = inst.HORARIOS || '';
                        document.getElementById('cocurricularCupoMaximo').value = inst.CUPO_MAXIMO || '';
                        document.getElementById('cocurricularActivo').value = inst.ACTIVO;
                        document.getElementById('cocurricularOrden').value = inst.ORDEN;

                        document.getElementById('modalCocurricularTitle').textContent = 'Editar: ' + inst.NOMBRE;
                        document.getElementById('btnTexto').textContent = 'Actualizar Programa/Servicio';

                        modalCocurricular.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Guardar co-curricular
        document.getElementById('formCocurricular').addEventListener('submit', function(e) {
            e.preventDefault();

            const cocurricularId = document.getElementById('cocurricularId').value;
            const formData = new FormData(this);

            let url;
            if (cocurricularId) {
                formData.append('id', cocurricularId);
                url = siteURL + 'assets/API/cocurriculares/editar.php';
            } else {
                url = siteURL + 'assets/API/cocurriculares/crear.php';
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
                        modalCocurricular.hide();
                        cargarCocurriculares();
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
            fetch(siteURL + 'assets/API/cocurriculares/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const inst = data.data;

                        const tipoLabels = {
                            'PROGRAMA': 'Programa',
                            'SERVICIO': 'Servicio',
                            'APOYO': 'Apoyo',
                            'OTRO': 'Otro'
                        };

                        document.getElementById('detalle-id').textContent = inst.ID;
                        document.getElementById('detalle-nombre').textContent = inst.NOMBRE;
                        document.getElementById('detalle-tipo').innerHTML = `<span class="badge bg-info">${tipoLabels[inst.TIPO] || inst.TIPO}</span>`;

                        const activoBadge = inst.ACTIVO == 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                        document.getElementById('detalle-activo').innerHTML = activoBadge;

                        // Responsable
                        document.getElementById('detalle-responsable-nombre').textContent = inst.RESPONSABLE_NOMBRE || 'No especificado';
                        document.getElementById('detalle-responsable-email').textContent = inst.RESPONSABLE_EMAIL || 'No especificado';
                        document.getElementById('detalle-responsable-telefono').textContent = inst.RESPONSABLE_TELEFONO || 'No especificado';

                        // Información
                        document.getElementById('detalle-descripcion').textContent = inst.DESCRIPCION || 'Sin descripción';
                        document.getElementById('detalle-objetivo').textContent = inst.OBJETIVO || 'No especificado';
                        document.getElementById('detalle-requisitos').innerHTML = inst.REQUISITOS ? '<pre>' + inst.REQUISITOS + '</pre>' : 'No especificado';
                        document.getElementById('detalle-beneficios').innerHTML = inst.BENEFICIOS ? '<pre>' + inst.BENEFICIOS + '</pre>' : 'No especificado';

                        // Operativa
                        document.getElementById('detalle-ubicacion').textContent = inst.UBICACION || 'No especificado';
                        document.getElementById('detalle-horarios').innerHTML = inst.HORARIOS ? '<pre>' + inst.HORARIOS + '</pre>' : 'No especificado';
                        document.getElementById('detalle-cupo').textContent = inst.CUPO_MAXIMO || 'Sin límite';

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
                        document.getElementById('btnGestionarImagenesDetalles').href = siteURL + 'pages/galeria/?modulo=cocurriculares&id_registro=' + inst.ID;

                        // Botón editar desde detalles
                        document.getElementById('btnEditarDesdeDetalles').onclick = function() {
                            modalDetalles.hide();
                            setTimeout(() => editarCocurricular(inst.ID), 300);
                        };

                        modalDetalles.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Eliminar co-curricular
        function eliminarCocurricular(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar el programa/servicio "${nombre}"?\n\nEsto también eliminará todas las imágenes asociadas.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/cocurriculares/eliminar.php', {
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
                        cargarCocurriculares();
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

        // Cargar cocurriculares
        function cargarCocurriculares() {
            fetch(siteURL + 'assets/API/cocurriculares/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        // Destruir DataTable si existe
                        if ($.fn.DataTable.isDataTable('#tablaCocurriculares')) {
                            $('#tablaCocurriculares').DataTable().clear().destroy();
                        }

                        // Construir HTML
                        const tbody = document.querySelector('#tablaCocurriculares tbody');
                        let htmlRows = '';

                        data.data.forEach(inst => {
                            const tipoLabels = {
                                'PROGRAMA': 'Programa',
                                'SERVICIO': 'Servicio',
                                'APOYO': 'Apoyo',
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
                                    <td>${inst.RESPONSABLE_NOMBRE || '-'}</td>
                                    <td>${inst.CUPO_MAXIMO || '-'}</td>
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
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarCocurricular(${inst.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="eliminarCocurricular(${inst.ID}, '${inst.NOMBRE.replace(/'/g, "\\'")}')">
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
                        $('#tablaCocurriculares').DataTable({
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
            modalCocurricular = new bootstrap.Modal(document.getElementById('modalCocurricular'));
            modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));

            // Cargar cocurriculares
            cargarCocurriculares();
        });
    </script>
</body>

</html>
