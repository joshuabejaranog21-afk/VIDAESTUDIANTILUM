<?php
include('../../assets/php/template.php');
$temp = new Template('Ligas Deportivas');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('ligas', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar permisos específicos
$puede_crear = $temp->tiene_permiso('ligas', 'crear');
$puede_editar = $temp->tiene_permiso('ligas', 'editar');
$puede_eliminar = $temp->tiene_permiso('ligas', 'eliminar');
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
                            <h1 class="mb-0 pb-0 display-4">Ligas Deportivas</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Involúcrate</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Ligas</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end gap-2">
                            <a href="<?php echo $temp->siteURL ?>galeria-deportes/" class="btn btn-outline-secondary btn-icon btn-icon-start w-100 w-md-auto">
                                <i data-acorn-icon="image"></i>
                                <span>Galería</span>
                            </a>
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nueva Liga</span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <!-- Top Buttons End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Filters Start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Filtrar por Deporte</label>
                                        <select id="filtroDeporte" class="form-select">
                                            <option value="">Todos los deportes</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Estado</label>
                                        <select id="filtroEstado" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="S">Activos</option>
                                            <option value="N">Inactivos</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filters End -->

                <!-- Content Start -->
                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card">
                            <div class="card-body">
                                <!-- DataTable Start -->
                                <table id="tablaLigas" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Deporte</th>
                                            <th>Responsable</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
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

    <!-- Modal para crear/editar liga -->
    <div class="modal fade" id="modalLiga" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLigaTitle">Nueva Liga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLiga">
                    <div class="modal-body">
                        <input type="hidden" id="ligaId">

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Deporte *</label>
                                <select id="ligaDeporte" class="form-select" required>
                                    <option value="">Selecciona un deporte</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Nombre de la Liga *</label>
                                <input type="text" id="ligaNombre" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Fecha de Inicio</label>
                                <input type="date" id="ligaFechaInicio" class="form-control">
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Estado de la Liga</label>
                                <select id="ligaEstado" class="form-select">
                                    <option value="EN_PREPARACION">En Preparación</option>
                                    <option value="EN_CURSO">En Curso</option>
                                    <option value="PAUSADO">Pausado</option>
                                    <option value="CANCELADO">Cancelado</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea id="ligaDescripcion" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Requisitos</label>
                                <textarea id="ligaRequisitos" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Responsable</label>
                                <input type="text" id="ligaResponsable" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contacto Responsable</label>
                                <input type="text" id="ligaResponsableContacto" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" id="ligaEmail" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" id="ligaTelefono" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Imagen Principal (Banner)</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="ligaImagen" class="form-control" placeholder="URL o subir archivo">
                                    <button class="btn btn-outline-secondary" type="button" id="btnUploadImagen">
                                        <i data-acorn-icon="upload"></i> Subir
                                    </button>
                                </div>
                                <small class="text-muted">Imagen de portada de la liga (máx 5MB)</small>
                                <div id="previewImagen" class="mt-2" style="display:none;">
                                    <img id="imagenPreview" src="" style="max-width:100%; max-height:150px; border-radius:4px;">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto del Responsable</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="ligaFotoResponsable" class="form-control" placeholder="URL o subir archivo">
                                    <button class="btn btn-outline-secondary" type="button" id="btnUploadFotoResponsable">
                                        <i data-acorn-icon="upload"></i> Subir
                                    </button>
                                </div>
                                <small class="text-muted">Foto de perfil del responsable (máx 5MB)</small>
                                <div id="previewFotoResponsable" class="mt-2" style="display:none;">
                                    <img id="fotoResponsablePreview" src="" style="max-width:100%; max-height:150px; border-radius:50%; border:2px solid #ddd;">
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Galería de Imágenes</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarGaleria">
                                        <i data-acorn-icon="plus"></i> Agregar Imagen
                                    </button>
                                </div>
                                <div id="galeriaContainer">
                                    <!-- Se agregaran imágenes aquí -->
                                </div>
                                <input type="hidden" id="ligaGaleria">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" id="ligaOrden" class="form-control" value="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select id="ligaActivo" class="form-select">
                                    <option value="S">Activo</option>
                                    <option value="N">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Liga</button>
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
                    <h5 class="modal-title">Detalles de la Liga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Información General</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>ID:</strong> <span id="detalle-id"></span></p>
                                    <p><strong>Nombre:</strong> <span id="detalle-nombre"></span></p>
                                    <p><strong>Deporte:</strong> <span id="detalle-deporte"></span></p>
                                    <p><strong>Estado:</strong> <span id="detalle-estado-badge"></span></p>
                                    <p><strong>Fecha de Inicio:</strong> <span id="detalle-fecha"></span></p>
                                    <p><strong>Activo:</strong> <span id="detalle-activo"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Responsable -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Responsable</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3" id="detalle-foto-responsable-container">
                                        <!-- Imagen del responsable -->
                                    </div>
                                    <p><strong>Nombre:</strong> <span id="detalle-responsable"></span></p>
                                    <p><strong>Contacto:</strong> <span id="detalle-responsable-contacto"></span></p>
                                    <p><strong>Email:</strong> <span id="detalle-email"></span></p>
                                    <p><strong>Teléfono:</strong> <span id="detalle-telefono"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción y Requisitos -->
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Descripción y Requisitos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Descripción:</strong>
                                        <p id="detalle-descripcion" class="mt-2"></p>
                                    </div>
                                    <div>
                                        <strong>Requisitos:</strong>
                                        <p id="detalle-requisitos" class="mt-2"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen Principal -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">Imagen Principal (Banner)</h6>
                                </div>
                                <div class="card-body text-center" id="detalle-imagen-principal-container">
                                    <!-- Imagen principal -->
                                </div>
                            </div>
                        </div>

                        <!-- Galería -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Galería de Imágenes</h6>
                                </div>
                                <div class="card-body" id="detalle-galeria-container">
                                    <!-- Galería -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <?php if($puede_editar): ?>
                    <button type="button" class="btn btn-primary" id="btnEditarDesdeDetalles">Editar Liga</button>
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

    <!-- Input file oculto para upload -->
    <input type="file" id="fileInput" style="display:none;" accept="image/*">

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;
        const modalLiga = new bootstrap.Modal(document.getElementById('modalLiga'));
        const modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));

        // Cargar deportes para el filtro y el selector
        function cargarDeportes() {
            fetch(siteURL + 'assets/API/deportes/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const selectFiltro = document.getElementById('filtroDeporte');
                        const selectFormulario = document.getElementById('ligaDeporte');
                        
                        data.data.forEach(deporte => {
                            // Para filtro
                            const optionFiltro = document.createElement('option');
                            optionFiltro.value = deporte.ID;
                            optionFiltro.textContent = deporte.NOMBRE;
                            selectFiltro.appendChild(optionFiltro);

                            // Para formulario
                            const optionFormulario = document.createElement('option');
                            optionFormulario.value = deporte.ID;
                            optionFormulario.textContent = deporte.NOMBRE;
                            selectFormulario.appendChild(optionFormulario);
                        });
                    }
                });
        }

        // Cargar ligas
        function cargarLigas() {
            console.log('🔄 Cargando ligas...');
            const idDeporte = document.getElementById('filtroDeporte').value;
            const estado = document.getElementById('filtroEstado').value;

            console.log('📊 Filtros aplicados:', {
                deporte: idDeporte || 'Todos',
                estado: estado || 'Todos'
            });

            let url = siteURL + 'assets/API/ligas/listar.php';
            const params = [];
            if (idDeporte) params.push('id_deporte=' + idDeporte);
            if (estado !== '') params.push('activo=' + estado);
            if (params.length) url += '?' + params.join('&');

            console.log('🌐 URL llamada:', url);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Ligas recibidas:', data.data.length);
                    if (data.success == 1) {
                        // PASO 1: Destruir DataTable PRIMERO si existe
                        if ($.fn.DataTable.isDataTable('#tablaLigas')) {
                            $('#tablaLigas').DataTable().clear().destroy();
                        }

                        // PASO 2: Limpiar y construir el HTML
                        const tbody = document.querySelector('#tablaLigas tbody');
                        let htmlRows = '';

                        data.data.forEach(liga => {
                            htmlRows += `
                                <tr>
                                    <td>${liga.ID}</td>
                                    <td>
                                        <div>
                                            <strong>${liga.NOMBRE}</strong>
                                            ${liga.DESCRIPCION ? `<br><small class="text-muted">${liga.DESCRIPCION.substring(0, 50)}...</small>` : ''}
                                        </div>
                                    </td>
                                    <td>${liga.DEPORTE_NOMBRE || '-'}</td>
                                    <td>${liga.RESPONSABLE_NOMBRE || '-'}</td>
                                    <td>${liga.EMAIL || '-'}</td>
                                    <td>${liga.TELEFONO || '-'}</td>
                                    <td>
                                        <span class="badge ${liga.ACTIVO == 'S' ? 'bg-success' : 'bg-secondary'}">
                                            ${liga.ACTIVO == 'S' ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                                            <button onclick="verDetallesLiga(${liga.ID})" class="btn btn-sm btn-info text-white">
                                                Ver
                                            </button>
                                            ${puedeEditar ? `
                                            <button onclick="editarLiga(${liga.ID})" class="btn btn-sm btn-warning text-white">
                                                Editar
                                            </button>
                                            ` : ''}
                                            ${puedeEliminar ? `
                                            <button type="button" class="btn btn-sm btn-danger btn-eliminar text-white" data-id="${liga.ID}" data-nombre="${liga.NOMBRE.replace(/"/g, '&quot;')}">
                                                Eliminar
                                            </button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        // Asignar todo de una vez
                        tbody.innerHTML = htmlRows;

                        // PASO 3: Reinicializar DataTable
                        $('#tablaLigas').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                            },
                            order: [[1, 'asc']],
                            pageLength: 25
                        });

                        // PASO 4: Actualizar iconos
                        if (typeof acorn !== 'undefined') {
                            acorn.icons();
                        }

                        console.log('✅ Tabla actualizada correctamente con', data.data.length, 'ligas');

                        // Mostrar resumen de las ligas cargadas
                        const activas = data.data.filter(l => l.ACTIVO === 'S').length;
                        const inactivas = data.data.filter(l => l.ACTIVO === 'N').length;
                        console.log('   📈 Activas:', activas, '| 📉 Inactivas:', inactivas);
                    }
                })
                .catch(error => {
                    console.error('❌ Error al cargar ligas:', error);
                });
        }

        // Abrir formulario para crear
        function abrirFormularioCrear() {
            document.getElementById('formLiga').reset();
            document.getElementById('ligaId').value = '';
            document.getElementById('modalLigaTitle').textContent = 'Nueva Liga';

            // Limpiar galería temporal
            galeria = [];
            document.getElementById('galeriaContainer').innerHTML = '';
            document.getElementById('ligaGaleria').value = '';

            // Ocultar previsualizaciones
            document.getElementById('previewImagen').style.display = 'none';
            document.getElementById('previewFotoResponsable').style.display = 'none';

            modalLiga.show();
        }

        // Editar liga
        function editarLiga(id) {
            fetch(siteURL + 'assets/API/ligas/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const liga = data.data;

                        document.getElementById('ligaId').value = liga.ID;
                        document.getElementById('ligaDeporte').value = liga.ID_DEPORTE;
                        document.getElementById('ligaNombre').value = liga.NOMBRE;
                        document.getElementById('ligaFechaInicio').value = liga.FECHA_INICIO || '';
                        document.getElementById('ligaDescripcion').value = liga.DESCRIPCION || '';
                        document.getElementById('ligaRequisitos').value = liga.REQUISITOS || '';
                        document.getElementById('ligaResponsable').value = liga.RESPONSABLE_NOMBRE || '';
                        document.getElementById('ligaResponsableContacto').value = liga.RESPONSABLE_CONTACTO || '';
                        document.getElementById('ligaFotoResponsable').value = liga.FOTO_RESPONSABLE || '';
                        document.getElementById('ligaEmail').value = liga.EMAIL || '';
                        document.getElementById('ligaTelefono').value = liga.TELEFONO || '';
                        document.getElementById('ligaImagen').value = liga.IMAGEN_URL || '';
                        document.getElementById('ligaOrden').value = liga.ORDEN || 0;
                        document.getElementById('ligaActivo').value = liga.ACTIVO;
                        document.getElementById('ligaEstado').value = liga.ESTADO || 'EN_PREPARACION';

                        // Cargar galería existente
                        galeria = liga.GALERIA_ARRAY || [];
                        document.getElementById('ligaGaleria').value = liga.GALERIA || '';
                        document.getElementById('galeriaContainer').innerHTML = '';
                        galeria.forEach(url => agregarImagenGaleria(url));

                        // Mostrar previsualizaciones
                        if (liga.IMAGEN_URL) {
                            document.getElementById('imagenPreview').src = liga.IMAGEN_URL;
                            document.getElementById('previewImagen').style.display = 'block';
                        } else {
                            document.getElementById('previewImagen').style.display = 'none';
                        }

                        if (liga.FOTO_RESPONSABLE) {
                            document.getElementById('fotoResponsablePreview').src = liga.FOTO_RESPONSABLE;
                            document.getElementById('previewFotoResponsable').style.display = 'block';
                        } else {
                            document.getElementById('previewFotoResponsable').style.display = 'none';
                        }

                        document.getElementById('modalLigaTitle').textContent = 'Editar Liga: ' + liga.NOMBRE;
                        modalLiga.show();
                    }
                });
        }

        // Guardar liga
        document.getElementById('formLiga').addEventListener('submit', function(e) {
            e.preventDefault();

            const ligaId = document.getElementById('ligaId').value;
            const formData = new FormData();

            formData.append('id_deporte', document.getElementById('ligaDeporte').value);
            formData.append('nombre', document.getElementById('ligaNombre').value);
            formData.append('fecha_inicio', document.getElementById('ligaFechaInicio').value);
            formData.append('descripcion', document.getElementById('ligaDescripcion').value);
            formData.append('requisitos', document.getElementById('ligaRequisitos').value);
            formData.append('responsable_nombre', document.getElementById('ligaResponsable').value);
            formData.append('responsable_contacto', document.getElementById('ligaResponsableContacto').value);
            formData.append('foto_responsable', document.getElementById('ligaFotoResponsable').value);
            formData.append('email', document.getElementById('ligaEmail').value);
            formData.append('telefono', document.getElementById('ligaTelefono').value);
            formData.append('imagen_url', document.getElementById('ligaImagen').value);
            formData.append('galeria', document.getElementById('ligaGaleria').value);
            formData.append('orden', document.getElementById('ligaOrden').value);
            formData.append('activo', document.getElementById('ligaActivo').value);
            formData.append('estado', document.getElementById('ligaEstado').value);

            if (ligaId) {
                formData.append('id', ligaId);
                url = siteURL + 'assets/API/ligas/actualizar.php';
            } else {
                url = siteURL + 'assets/API/ligas/crear.php';
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
                        modalLiga.hide();

                        // Esperar a que el modal se cierre antes de recargar
                        setTimeout(() => {
                            cargarLigas();
                        }, 300);
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
                        message: 'Error al procesar la solicitud'
                    }, {
                        type: 'danger'
                    });
                });
        });

        // Eliminar liga
        function eliminarLiga(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar la liga "${nombre}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/ligas/eliminar.php', {
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
                        console.log('🗑️ Liga eliminada, recargando...');
                        cargarLigas();
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
                    console.error('❌ Error al eliminar:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'Error al eliminar la liga'
                    }, {
                        type: 'danger'
                    });
                });
        }

        // Event listener delegado para botones de eliminar
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-eliminar')) {
                const btn = e.target.closest('.btn-eliminar');
                const id = btn.getAttribute('data-id');
                const nombre = btn.getAttribute('data-nombre');
                eliminarLiga(id, nombre);
            }
        });

        // Ver detalles de liga
        function verDetallesLiga(id) {
            fetch(siteURL + 'assets/API/ligas/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const liga = data.data;

                        // Información General
                        document.getElementById('detalle-id').textContent = liga.ID;
                        document.getElementById('detalle-nombre').textContent = liga.NOMBRE;
                        document.getElementById('detalle-deporte').textContent = liga.DEPORTE_NOMBRE || '-';
                        document.getElementById('detalle-fecha').textContent = liga.FECHA_INICIO || '-';

                        // Estado badge
                        const estadoColors = {
                            'EN_PREPARACION': 'warning',
                            'EN_CURSO': 'success',
                            'PAUSADO': 'info',
                            'CANCELADO': 'danger'
                        };
                        const estadoTexts = {
                            'EN_PREPARACION': 'En Preparación',
                            'EN_CURSO': 'En Curso',
                            'PAUSADO': 'Pausado',
                            'CANCELADO': 'Cancelado'
                        };
                        const estadoColor = estadoColors[liga.ESTADO] || 'secondary';
                        const estadoText = estadoTexts[liga.ESTADO] || liga.ESTADO;
                        document.getElementById('detalle-estado-badge').innerHTML = `<span class="badge bg-${estadoColor}">${estadoText}</span>`;

                        // Activo
                        const activoBadge = liga.ACTIVO == 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                        document.getElementById('detalle-activo').innerHTML = activoBadge;

                        // Responsable
                        document.getElementById('detalle-responsable').textContent = liga.RESPONSABLE_NOMBRE || '-';
                        document.getElementById('detalle-responsable-contacto').textContent = liga.RESPONSABLE_CONTACTO || '-';
                        document.getElementById('detalle-email').textContent = liga.EMAIL || '-';
                        document.getElementById('detalle-telefono').textContent = liga.TELEFONO || '-';

                        // Foto del responsable
                        const fotoContainer = document.getElementById('detalle-foto-responsable-container');
                        if (liga.FOTO_RESPONSABLE) {
                            fotoContainer.innerHTML = `<img src="${liga.FOTO_RESPONSABLE}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #17a2b8;" alt="Foto del responsable">`;
                        } else {
                            fotoContainer.innerHTML = '<p class="text-muted">Sin foto</p>';
                        }

                        // Descripción y Requisitos
                        document.getElementById('detalle-descripcion').textContent = liga.DESCRIPCION || 'Sin descripción';
                        document.getElementById('detalle-requisitos').textContent = liga.REQUISITOS || 'Sin requisitos';

                        // Imagen Principal
                        const imagenContainer = document.getElementById('detalle-imagen-principal-container');
                        if (liga.IMAGEN_URL) {
                            imagenContainer.innerHTML = `<img src="${liga.IMAGEN_URL}" style="max-width: 100%; max-height: 300px; border-radius: 8px;" alt="Banner">`;
                        } else {
                            imagenContainer.innerHTML = '<p class="text-muted">Sin imagen</p>';
                        }

                        // Galería
                        const galeriaContainer = document.getElementById('detalle-galeria-container');
                        const galeria = liga.GALERIA_ARRAY || [];
                        if (galeria.length > 0) {
                            let galeriaHTML = '<div class="row g-2">';
                            galeria.forEach(url => {
                                galeriaHTML += `
                                    <div class="col-6 col-md-4">
                                        <img src="${url}" class="img-fluid rounded" style="width: 100%; height: 120px; object-fit: cover; cursor: pointer;" onclick="window.open('${url}', '_blank')">
                                    </div>
                                `;
                            });
                            galeriaHTML += '</div>';
                            galeriaContainer.innerHTML = galeriaHTML;
                        } else {
                            galeriaContainer.innerHTML = '<p class="text-muted">Sin imágenes</p>';
                        }

                        // Botón editar desde detalles
                        const btnEditar = document.getElementById('btnEditarDesdeDetalles');
                        if (btnEditar) {
                            btnEditar.onclick = function() {
                                modalDetalles.hide();
                                setTimeout(() => editarLiga(liga.ID), 300);
                            };
                        }

                        modalDetalles.show();
                    }
                })
                .catch(error => {
                    console.error('Error al cargar detalles:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'No se pudieron cargar los detalles'
                    }, { type: 'danger' });
                });
        }

        // Función de upload genérica
        function subirArchivo(tipoCampo, tipoArchivo) {
            const fileInput = document.getElementById('fileInput');
            const inputField = document.getElementById('liga' + tipoCampo);
            const previewDiv = document.getElementById('preview' + tipoCampo);

            // Corregir el ID del preview: imagenPreview o fotoResponsablePreview
            let previewImgId = tipoCampo.charAt(0).toLowerCase() + tipoCampo.slice(1) + 'Preview';
            const previewImg = document.getElementById(previewImgId);

            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', tipoArchivo);

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            // Usar URL completa devuelta por el servidor
                            const imagenURL = data.url || data.url_relativa;
                            if (inputField) inputField.value = imagenURL;
                            if (previewImg) previewImg.src = imagenURL;
                            if (previewDiv) previewDiv.style.display = 'block';
                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Archivo subido correctamente'
                            }, { type: 'success' });
                        } else {
                            jQuery.notify({
                                title: 'Error',
                                message: data.message
                            }, { type: 'danger' });
                        }
                    });
                }
            };
            fileInput.click();
        }

        // Botones de upload
        document.getElementById('btnUploadImagen').addEventListener('click', function(e) {
            e.preventDefault();
            subirArchivo('Imagen', 'imagen');
        });

        document.getElementById('btnUploadFotoResponsable').addEventListener('click', function(e) {
            e.preventDefault();
            subirArchivo('FotoResponsable', 'foto_responsable');
        });

        // Galería de imágenes
        let galeria = [];
        document.getElementById('btnAgregarGaleria').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInput');
            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'galeria');
                    
                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            // Usar URL completa devuelta por el servidor
                            const imagenURL = data.url || data.url_relativa;
                            galeria.push(imagenURL);
                            agregarImagenGaleria(imagenURL);
                            document.getElementById('ligaGaleria').value = JSON.stringify(galeria);
                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Imagen agregada a la galería'
                            }, { type: 'success' });
                        }
                    });
                }
            };
            fileInput.click();
        });

        function agregarImagenGaleria(url) {
            const container = document.getElementById('galeriaContainer');
            const div = document.createElement('div');
            div.className = 'mb-2 p-2 border rounded';
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${url}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    <small>${url}</small>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove(); galeria = galeria.filter(g => g !== '${url}'); document.getElementById('ligaGaleria').value = JSON.stringify(galeria);">
                        Eliminar
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        // Filtros
        document.getElementById('filtroDeporte').addEventListener('change', cargarLigas);
        document.getElementById('filtroEstado').addEventListener('change', cargarLigas);

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarDeportes();
            cargarLigas();
        });
    </script>
</body>

</html>
