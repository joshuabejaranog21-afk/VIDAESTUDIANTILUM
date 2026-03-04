<?php
include('../../assets/php/template.php');
$temp = new Template('Ministerios');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('ministerios', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar si puede crear/editar/eliminar
$puede_crear = $temp->tiene_permiso('ministerios', 'crear');
$puede_editar = $temp->tiene_permiso('ministerios', 'editar');
$puede_eliminar = $temp->tiene_permiso('ministerios', 'eliminar');
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
                                    <li class="breadcrumb-item active" aria-current="page">Ministerios</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nuevo Ministerio</span>
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
                        <!-- Estado Vacío (se muestra cuando no hay ministerios) -->
                        <div id="emptyStateMinisterios" class="card" style="display: none;">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i data-acorn-icon="heart" class="text-muted" style="width: 80px; height: 80px;"></i>
                                </div>
                                <h3 class="mb-3">Aún no hay ministerios agregados</h3>
                                <p class="text-muted mb-4">Comienza creando el primer ministerio de tu universidad</p>
                                <?php if($puede_crear): ?>
                                <button type="button" class="btn btn-primary btn-lg px-5 py-3" onclick="abrirFormularioCrear()">
                                    <i data-acorn-icon="plus" class="me-2"></i>
                                    <span>Crear Primer Ministerio</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tabla de Ministerios (se muestra cuando hay datos) -->
                        <div id="tableContainerMinisterios" class="card">
                            <div class="card-body">
                                <!-- DataTable Start -->
                                <table id="tablaMinisterios" class="data-table nowrap w-100">
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

    <!-- Modal para crear/editar ministerio -->
    <div class="modal fade" id="modalMinisterio" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMinisterioTitle">Nuevo Ministerio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formMinisterio">
                    <div class="modal-body">
                        <input type="hidden" id="ministerioId">

                        <!-- Información Básica -->
                        <div class="mb-3">
                            <h6 class="mb-3">Información Básica del Ministerio</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nombre del Ministerio *</label>
                                    <input type="text" id="ministerioNombre" name="nombre" class="form-control" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción Inicial *</label>
                                    <textarea id="ministerioDescripcion" name="descripcion" class="form-control" rows="3" required placeholder="Descripción breve del propósito del ministerio"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Logo/Imagen del Ministerio</label>
                                    <input type="hidden" id="ministerioImagenUrl" name="imagen_url">
                                    <div class="d-flex gap-2 align-items-start">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnUploadLogo">
                                            Subir Logo
                                        </button>
                                        <div id="previewLogo" style="display:none;">
                                            <img id="logoPreview" src="" style="max-width:100px; max-height:100px; border-radius:4px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" id="containerActivo">
                                    <label class="form-label">Estado</label>
                                    <select id="ministerioActivo" name="activo" class="form-select">
                                        <option value="S">Activo</option>
                                        <option value="N">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Galería de Imágenes -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Galería de Imágenes del Ministerio</label>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarGaleriaMinisterio">
                                            <i data-acorn-icon="plus"></i> Agregar Imagen a la Galería
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mb-2">Agrega fotos de actividades, eventos o reuniones del ministerio</small>
                                    <div id="galeriaContainerMinisterio">
                                        <!-- Se agregarán imágenes aquí -->
                                    </div>
                                    <input type="hidden" id="ministerioGaleria" name="galeria">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Asignar Director (solo en crear) -->
                        <div id="containerDirector">
                            <h6 class="mb-3">Asignar Director del Ministerio</h6>

                            <!-- Radio buttons -->
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo_director" id="director_existente" value="existente" checked>
                                    <label class="form-check-label" for="director_existente">
                                        <strong>Asignar usuario existente</strong>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_director" id="director_nuevo" value="nuevo">
                                    <label class="form-check-label" for="director_nuevo">
                                        <strong>Crear nuevo usuario director</strong>
                                    </label>
                                </div>
                            </div>

                            <!-- Seccion asignar usuario existente -->
                            <div id="seccion_existente" class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Seleccionar Usuario</label>
                                        <select class="form-select" id="director_usuario" name="director_usuario">
                                            <option value="">Sin asignar</option>
                                            <!-- Se llenará con JavaScript -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seccion crear nuevo usuario -->
                            <div id="seccion_nuevo" style="display:none;">
                                <small class="text-muted d-block mb-3">Crea un nuevo usuario director completando los siguientes campos</small>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre de Usuario</label>
                                        <input type="text" id="nuevoNombre" name="nuevo_nombre" class="form-control" placeholder="usuario.director">
                                        <small class="text-muted">Solo letras, números, puntos y guiones</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="nuevoEmail" name="nuevo_email" class="form-control" placeholder="director@universidad.com">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contraseña Temporal</label>
                                        <input type="password" id="nuevoPassword" name="nuevo_password" class="form-control" placeholder="Mínimo 8 caracteres">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Completo del Director</label>
                                        <input type="text" id="nombreCompleto" name="nombre_completo" class="form-control" placeholder="Juan Pérez">
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <strong>Nota:</strong> Se enviará un email al director con sus credenciales de acceso.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span id="btnTexto">Crear Ministerio</span>
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
                    <h5 class="modal-title">Detalles del Ministerio</h5>
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
                            <p><strong>Estado:</strong> <span id="detalle-activo"></span></p>
                            <p><strong>Horario:</strong> <span id="detalle-horario"></span></p>
                            <p><strong>Día de Reunión:</strong> <span id="detalle-dia"></span></p>
                            <p><strong>Lugar:</strong> <span id="detalle-lugar"></span></p>
                        </div>

                        <!-- Información del Director -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Director/Responsable</h6>
                            <hr>
                            <div class="text-center mb-3" id="detalle-foto-director-container">
                                <!-- Imagen del director -->
                            </div>
                            <p><strong>Nombre:</strong> <span id="detalle-director"></span></p>
                            <p><strong>Contacto:</strong> <span id="detalle-contacto"></span></p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Descripción</h6>
                            <hr>
                            <p id="detalle-descripcion"></p>
                        </div>

                        <!-- Cupos -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Información de Cupos</h6>
                            <hr>
                            <p><strong>Cupo Actual:</strong> <span id="detalle-cupo-actual"></span></p>
                            <p><strong>Cupo Máximo:</strong> <span id="detalle-cupo-maximo"></span></p>
                            <div class="progress" style="height: 25px;">
                                <div id="detalle-progreso" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <!-- Imagen Principal -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Logo del Ministerio</h6>
                            <hr>
                            <div class="text-center" id="detalle-imagen-container">
                                <!-- Imagen -->
                            </div>
                        </div>

                        <!-- Galería -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Galería de Imágenes</h6>
                            <hr>
                            <div id="detalle-galeria-container">
                                <!-- Galería -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <?php if($puede_editar): ?>
                    <button type="button" class="btn btn-primary" id="btnEditarDesdeDetalles">Editar Ministerio</button>
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
        const modalMinisterio = new bootstrap.Modal(document.getElementById('modalMinisterio'));
        const modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));

        // Galería de imágenes
        let galeriaMinisterio = [];

        // Función para agregar imagen a la galería
        function agregarImagenGaleriaMinisterio(url) {
            const container = document.getElementById('galeriaContainerMinisterio');
            const div = document.createElement('div');
            div.className = 'mb-2 p-2 border rounded';
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${url}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    <small class="flex-grow-1">${url}</small>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove(); galeriaMinisterio = galeriaMinisterio.filter(g => g !== '${url}'); document.getElementById('ministerioGaleria').value = JSON.stringify(galeriaMinisterio);">
                        Eliminar
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        // Botón agregar imagen a galería
        document.getElementById('btnAgregarGaleriaMinisterio').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInput');
            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'galeria_ministerio');

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            galeriaMinisterio.push(imagenURL);
                            agregarImagenGaleriaMinisterio(imagenURL);
                            document.getElementById('ministerioGaleria').value = JSON.stringify(galeriaMinisterio);
                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Imagen agregada a la galería'
                            }, { type: 'success' });
                        } else {
                            jQuery.notify({
                                title: 'Error',
                                message: data.message
                            }, { type: 'danger' });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        jQuery.notify({
                            title: 'Error',
                            message: 'Error al subir imagen'
                        }, { type: 'danger' });
                    });
                }
            };
            fileInput.click();
        });

        // Cargar usuarios disponibles
        function cargarUsuariosDisponibles() {
            fetch(siteURL + 'assets/API/usuarios/listar_disponibles.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const select = document.getElementById('director_usuario');
                        select.innerHTML = '<option value="">Sin asignar</option>';
                        data.data.forEach(usuario => {
                            select.innerHTML += `<option value="${usuario.ID}">${usuario.NOMBRE} (${usuario.EMAIL})</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Manejar cambio de tipo de director
        document.querySelectorAll('input[name="tipo_director"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const seccionExistente = document.getElementById('seccion_existente');
                const seccionNuevo = document.getElementById('seccion_nuevo');

                if (this.value === 'existente') {
                    seccionExistente.style.display = 'block';
                    seccionNuevo.style.display = 'none';
                    // Limpiar campos de nuevo usuario
                    document.getElementById('nuevoNombre').value = '';
                    document.getElementById('nuevoEmail').value = '';
                    document.getElementById('nuevoPassword').value = '';
                    document.getElementById('nombreCompleto').value = '';
                } else {
                    seccionExistente.style.display = 'none';
                    seccionNuevo.style.display = 'block';
                    // Limpiar selección de usuario existente
                    document.getElementById('director_usuario').value = '';
                }
            });
        });

        // Abrir formulario para crear
        function abrirFormularioCrear() {
            document.getElementById('formMinisterio').reset();
            document.getElementById('ministerioId').value = '';
            document.getElementById('modalMinisterioTitle').textContent = 'Nuevo Ministerio';
            document.getElementById('btnTexto').textContent = 'Crear Ministerio';

            // Mostrar sección de asignar director (solo en crear)
            document.getElementById('containerDirector').style.display = 'block';
            document.getElementById('containerActivo').style.display = 'none';

            // Resetear radio buttons
            document.getElementById('director_existente').checked = true;
            document.getElementById('seccion_existente').style.display = 'block';
            document.getElementById('seccion_nuevo').style.display = 'none';

            // Limpiar previsualización de logo
            document.getElementById('previewLogo').style.display = 'none';
            document.getElementById('ministerioImagenUrl').value = '';

            // Limpiar galería
            galeriaMinisterio = [];
            document.getElementById('galeriaContainerMinisterio').innerHTML = '';
            document.getElementById('ministerioGaleria').value = '';

            // Cargar usuarios disponibles
            cargarUsuariosDisponibles();

            modalMinisterio.show();
        }

        // Botón de subir logo
        document.getElementById('btnUploadLogo').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInput');

            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'ministerio');

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            document.getElementById('ministerioImagenUrl').value = imagenURL;
                            document.getElementById('logoPreview').src = imagenURL;
                            document.getElementById('previewLogo').style.display = 'block';

                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Logo subido correctamente'
                            }, { type: 'success' });
                        } else {
                            jQuery.notify({
                                title: 'Error',
                                message: data.message
                            }, { type: 'danger' });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        jQuery.notify({
                            title: 'Error',
                            message: 'Error al subir el logo'
                        }, { type: 'danger' });
                    });
                }
            };
            fileInput.click();
        });

        // Editar ministerio
        function editarMinisterio(id) {
            fetch(siteURL + 'assets/API/ministerios/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const ministerio = data.data.find(m => m.ID == id);
                        if (ministerio) {
                            document.getElementById('ministerioId').value = ministerio.ID;
                            document.getElementById('ministerioNombre').value = ministerio.NOMBRE;
                            document.getElementById('ministerioDescripcion').value = ministerio.DESCRIPCION || '';
                            document.getElementById('ministerioActivo').value = ministerio.ACTIVO;

                            // Cargar logo si existe
                            if (ministerio.IMAGEN_PRINCIPAL) {
                                document.getElementById('ministerioImagenUrl').value = ministerio.IMAGEN_PRINCIPAL;
                                document.getElementById('logoPreview').src = ministerio.IMAGEN_PRINCIPAL;
                                document.getElementById('previewLogo').style.display = 'block';
                            } else {
                                document.getElementById('ministerioImagenUrl').value = '';
                                document.getElementById('previewLogo').style.display = 'none';
                            }

                            // Cargar galería existente (desde VRE_GALERIA)
                            const imagenesArray = (ministerio.IMAGENES || []).map(img => img.URL_IMAGEN);
                            galeriaMinisterio = imagenesArray;
                            document.getElementById('ministerioGaleria').value = JSON.stringify(galeriaMinisterio);
                            document.getElementById('galeriaContainerMinisterio').innerHTML = '';
                            galeriaMinisterio.forEach(url => agregarImagenGaleriaMinisterio(url));

                            document.getElementById('modalMinisterioTitle').textContent = 'Editar Ministerio: ' + ministerio.NOMBRE;
                            document.getElementById('btnTexto').textContent = 'Actualizar Ministerio';

                            // Ocultar sección de crear director (solo en editar)
                            document.getElementById('containerDirector').style.display = 'none';
                            document.getElementById('containerActivo').style.display = 'block';

                            modalMinisterio.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Guardar ministerio
        document.getElementById('formMinisterio').addEventListener('submit', function(e) {
            e.preventDefault();

            const ministerioId = document.getElementById('ministerioId').value;
            const nombre = document.getElementById('ministerioNombre').value.trim();
            const descripcion = document.getElementById('ministerioDescripcion').value.trim();

            // Validación básica
            if (!nombre || !descripcion) {
                jQuery.notify({
                    title: 'Error',
                    message: 'Por favor complete todos los campos obligatorios.'
                }, { type: 'danger' });
                return;
            }

            // Si es crear (no editar), validar campos del director
            if (!ministerioId) {
                const tipoDirector = document.querySelector('input[name="tipo_director"]:checked').value;

                if (tipoDirector === 'nuevo') {
                    const nuevoNombre = document.getElementById('nuevoNombre').value.trim();
                    const nuevoEmail = document.getElementById('nuevoEmail').value.trim();
                    const nuevoPassword = document.getElementById('nuevoPassword').value.trim();
                    const nombreCompleto = document.getElementById('nombreCompleto').value.trim();

                    // Si alguno de los campos del nuevo director está completo, todos deben estarlo
                    const tieneAlgunCampoDirector = nuevoNombre || nuevoEmail || nuevoPassword || nombreCompleto;

                    if (tieneAlgunCampoDirector) {
                        if (!nuevoNombre || !nuevoEmail || !nuevoPassword || !nombreCompleto) {
                            jQuery.notify({
                                title: 'Error',
                                message: 'Si deseas crear un director, debes completar todos los campos del formulario.'
                            }, { type: 'danger' });
                            return;
                        }

                        if (nuevoPassword.length < 8) {
                            jQuery.notify({
                                title: 'Error',
                                message: 'La contraseña debe tener al menos 8 caracteres.'
                            }, { type: 'danger' });
                            return;
                        }
                    }
                }
                // Si es "existente", director_usuario puede estar vacío (opcional)
            }

            // Crear FormData con todos los campos
            const formData = new FormData(this);

            let url;
            if (ministerioId) {
                formData.append('id', ministerioId);
                url = siteURL + 'assets/API/ministerios/editar.php';
            } else {
                url = siteURL + 'assets/API/ministerios/crear.php';
            }

            // Deshabilitar botón mientras se procesa
            const btnSubmit = document.querySelector('#formMinisterio button[type="submit"]');
            const btnTextoOriginal = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

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
                        modalMinisterio.hide();

                        setTimeout(() => {
                            cargarMinisterios();
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
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnTextoOriginal;
                });
        });

        // Ver detalles del ministerio
        function verDetallesMinisterio(id) {
            fetch(siteURL + 'assets/API/ministerios/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const ministerio = data.data.find(m => m.ID == id);
                        if (ministerio) {
                            // Información General
                            document.getElementById('detalle-id').textContent = ministerio.ID;
                            document.getElementById('detalle-nombre').textContent = ministerio.NOMBRE;
                            const activoBadge = ministerio.ACTIVO == 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                            document.getElementById('detalle-activo').innerHTML = activoBadge;
                            document.getElementById('detalle-horario').textContent = ministerio.HORARIO || '-';
                            document.getElementById('detalle-dia').textContent = ministerio.DIA_REUNION || '-';
                            document.getElementById('detalle-lugar').textContent = ministerio.LUGAR || '-';

                            // Director
                            document.getElementById('detalle-director').textContent = ministerio.DIRECTOR_NOMBRE || '-';
                            document.getElementById('detalle-contacto').textContent = ministerio.CONTACTO || '-';

                            // Foto del director
                            const fotoContainer = document.getElementById('detalle-foto-director-container');
                            if (ministerio.FOTO_DIRECTOR) {
                                fotoContainer.innerHTML = `<img src="${ministerio.FOTO_DIRECTOR}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #17a2b8;">`;
                            } else {
                                fotoContainer.innerHTML = '<p class="text-muted">Sin foto</p>';
                            }

                            // Descripción
                            document.getElementById('detalle-descripcion').textContent = ministerio.DESCRIPCION || 'Sin descripción';

                            // Cupos
                            document.getElementById('detalle-cupo-actual').textContent = ministerio.CUPO_ACTUAL || '0';
                            document.getElementById('detalle-cupo-maximo').textContent = ministerio.CUPO_MAXIMO || 'Sin límite';

                            if (ministerio.CUPO_MAXIMO) {
                                const porcentaje = (ministerio.CUPO_ACTUAL / ministerio.CUPO_MAXIMO) * 100;
                                const progreso = document.getElementById('detalle-progreso');
                                progreso.style.width = porcentaje + '%';
                                progreso.textContent = Math.round(porcentaje) + '%';
                                progreso.className = 'progress-bar ' + (porcentaje >= 80 ? 'bg-danger' : porcentaje >= 50 ? 'bg-warning' : 'bg-success');
                            }

                            // Imagen
                            const imagenContainer = document.getElementById('detalle-imagen-container');
                            if (ministerio.IMAGEN_PRINCIPAL) {
                                imagenContainer.innerHTML = `<img src="${ministerio.IMAGEN_PRINCIPAL}" style="max-width: 100%; max-height: 300px; border-radius: 8px;">`;
                            } else {
                                imagenContainer.innerHTML = '<p class="text-muted">Sin imagen</p>';
                            }

                            // Galería
                            const galeriaContainer = document.getElementById('detalle-galeria-container');
                            const galeria = ministerio.IMAGENES || [];
                            if (galeria.length > 0) {
                                let galeriaHTML = '<div class="row g-2">';
                                galeria.forEach(img => {
                                    galeriaHTML += `
                                        <div class="col-6 col-md-4">
                                            <img src="${img.URL_IMAGEN}" class="img-fluid rounded" style="width: 100%; height: 120px; object-fit: cover; cursor: pointer;" onclick="window.open('${img.URL_IMAGEN}', '_blank')" title="${img.TITULO || ''}">
                                        </div>
                                    `;
                                });
                                galeriaHTML += '</div>';
                                galeriaContainer.innerHTML = galeriaHTML;
                            } else {
                                galeriaContainer.innerHTML = '<p class="text-muted">Sin imágenes en la galería</p>';
                            }

                            // Botón editar desde detalles
                            const btnEditar = document.getElementById('btnEditarDesdeDetalles');
                            if (btnEditar) {
                                btnEditar.onclick = function() {
                                    modalDetalles.hide();
                                    setTimeout(() => editarMinisterio(ministerio.ID), 300);
                                };
                            }

                            modalDetalles.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'No se pudieron cargar los detalles'
                    }, { type: 'danger' });
                });
        }

        // Event listener delegado para botones de eliminar
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-eliminar')) {
                const btn = e.target.closest('.btn-eliminar');
                const id = btn.getAttribute('data-id');
                const nombre = btn.getAttribute('data-nombre');
                eliminarMinisterio(id, nombre);
            }
        });

        // Cargar ministerios
        function cargarMinisterios() {
            console.log('🔄 Cargando ministerios...');

            fetch(siteURL + 'assets/API/ministerios/listar.php')
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Ministerios recibidos:', data.data.length);
                    if (data.success == 1) {
                        const emptyState = document.getElementById('emptyStateMinisterios');
                        const tableContainer = document.getElementById('tableContainerMinisterios');

                        // Si no hay ministerios, mostrar estado vacío
                        if (data.data.length === 0) {
                            emptyState.style.display = 'block';
                            tableContainer.style.display = 'none';

                            // Actualizar iconos para el estado vacío
                            if (typeof acorn !== 'undefined') {
                                acorn.icons();
                            }
                            console.log('📭 No hay ministerios, mostrando estado vacío');
                            return;
                        }

                        // Si hay ministerios, mostrar tabla
                        emptyState.style.display = 'none';
                        tableContainer.style.display = 'block';

                        // PASO 1: Destruir DataTable PRIMERO si existe
                        if ($.fn.DataTable.isDataTable('#tablaMinisterios')) {
                            $('#tablaMinisterios').DataTable().clear().destroy();
                        }

                        // PASO 2: Construir el HTML
                        const tbody = document.querySelector('#tablaMinisterios tbody');
                        let htmlRows = '';

                        data.data.forEach(ministerio => {
                            const row = `
                                <tr>
                                    <td>${ministerio.ID}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            ${ministerio.IMAGEN_PRINCIPAL ? `<img src="${ministerio.IMAGEN_PRINCIPAL}" class="rounded-circle me-2" style="width:40px;height:40px;object-fit:cover;" alt="${ministerio.NOMBRE}">` : ''}
                                            <div>
                                                <strong>${ministerio.NOMBRE}</strong>
                                                ${ministerio.DESCRIPCION ? `<br><small class="text-muted">${ministerio.DESCRIPCION.substring(0, 50)}...</small>` : ''}
                                            </div>
                                        </div>
                                    </td>
                                    <td>${ministerio.HORARIO || '-'}<br><small class="text-muted">${ministerio.DIA_REUNION || ''}</small></td>
                                    <td>${ministerio.LUGAR || '-'}</td>
                                    <td>
                                        ${ministerio.CUPO_MAXIMO ? `${ministerio.CUPO_ACTUAL || 0} / ${ministerio.CUPO_MAXIMO}` : 'Sin límite'}
                                    </td>
                                    <td>${ministerio.DIRECTOR_NOMBRE || '-'}</td>
                                    <td>
                                        <span class="badge ${ministerio.ACTIVO == 'S' ? 'bg-success' : 'bg-secondary'}">
                                            ${ministerio.ACTIVO == 'S' ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="verDetallesMinisterio(${ministerio.ID})">
                                            <i class="fa fa-eye"></i>
                                            <span class="d-none d-xxl-inline-block">Ver</span>
                                        </button>
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarMinisterio(${ministerio.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1 btn-eliminar" type="button" data-id="${ministerio.ID}" data-nombre="${ministerio.NOMBRE.replace(/"/g, '&quot;')}">
                                            <i class="fa fa-trash"></i>
                                            <span class="d-none d-xxl-inline-block">Eliminar</span>
                                        </button>` : ''}
                                    </td>
                                </tr>
                            `;
                            htmlRows += row;
                        });

                        // PASO 3: Asignar todo de una vez
                        tbody.innerHTML = htmlRows;

                        // PASO 4: Reinicializar DataTable
                        $('#tablaMinisterios').DataTable({
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                            },
                            order: [[1, 'asc']],
                            pageLength: 25
                        });

                        // PASO 5: Actualizar iconos
                        if (typeof acorn !== 'undefined') {
                            acorn.icons();
                        }

                        console.log('✅ Tabla actualizada correctamente');
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
                    console.error('❌ Error al cargar ministerios:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'Error al cargar los ministerios'
                    }, {
                        type: 'danger'
                    });
                });
        }

        // Eliminar ministerio
        function eliminarMinisterio(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar el ministerio "${nombre}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/ministerios/eliminar.php', {
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
                        console.log('🗑️ Ministerio eliminado, recargando...');
                        cargarMinisterios();
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
                        message: 'Error al eliminar el ministerio'
                    }, {
                        type: 'danger'
                    });
                });
        }

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', cargarMinisterios);
    </script>
</body>

</html>
