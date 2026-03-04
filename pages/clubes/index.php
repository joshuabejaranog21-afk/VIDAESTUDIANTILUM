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
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="abrirFormularioCrear()">
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
                        <!-- Estado Vacío (se muestra cuando no hay clubes) -->
                        <div id="emptyStateClubs" class="card" style="display: none;">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i data-acorn-icon="star" class="text-muted" style="width: 80px; height: 80px;"></i>
                                </div>
                                <h3 class="mb-3">Aún no hay clubes agregados</h3>
                                <p class="text-muted mb-4">Comienza creando el primer club estudiantil de tu universidad</p>
                                <?php if($puede_crear): ?>
                                <button type="button" class="btn btn-primary btn-lg px-5 py-3" onclick="abrirFormularioCrear()">
                                    <i data-acorn-icon="plus" class="me-2"></i>
                                    <span>Crear Primer Club</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tabla de Clubes (se muestra cuando hay datos) -->
                        <div id="tableContainerClubs" class="card">
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

    <!-- Modal para crear/editar club -->
    <div class="modal fade" id="modalClub" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClubTitle">Nuevo Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formClub">
                    <div class="modal-body">
                        <input type="hidden" id="clubId">

                        <!-- Información Básica -->
                        <div class="mb-3">
                            <h6 class="mb-3">Información Básica del Club</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nombre del Club *</label>
                                    <input type="text" id="clubNombre" name="nombre" class="form-control" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Descripción Inicial *</label>
                                    <textarea id="clubDescripcion" name="descripcion" class="form-control" rows="3" required placeholder="Descripción breve del propósito del club"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Logo/Imagen del Club</label>
                                    <input type="hidden" id="clubImagenUrl" name="imagen_url">
                                    <div class="d-flex gap-2 align-items-start">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnUploadLogoClub">
                                            Subir Logo
                                        </button>
                                        <div id="previewLogoClub" style="display:none;">
                                            <img id="logoPreviewClub" src="" style="max-width:100px; max-height:100px; border-radius:4px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" id="containerActivoClub">
                                    <label class="form-label">Estado</label>
                                    <select id="clubActivo" name="activo" class="form-select">
                                        <option value="S">Activo</option>
                                        <option value="N">Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Galería de Imágenes -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Galería de Imágenes del Club</label>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarGaleriaClub">
                                            <i data-acorn-icon="plus"></i> Agregar Imagen a la Galería
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mb-2">Agrega fotos de actividades, eventos o instalaciones del club</small>
                                    <div id="galeriaContainerClub">
                                        <!-- Se agregarán imágenes aquí -->
                                    </div>
                                    <input type="hidden" id="clubGaleria" name="galeria">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Asignar Director (solo en crear) -->
                        <div id="containerDirectorClub">
                            <h6 class="mb-3">Asignar Director del Club</h6>

                            <!-- Radio buttons -->
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="tipo_director" id="director_existente_club" value="existente" checked>
                                    <label class="form-check-label" for="director_existente_club">
                                        <strong>Asignar usuario existente</strong>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_director" id="director_nuevo_club" value="nuevo">
                                    <label class="form-check-label" for="director_nuevo_club">
                                        <strong>Crear nuevo usuario director</strong>
                                    </label>
                                </div>
                            </div>

                            <!-- Seccion asignar usuario existente -->
                            <div id="seccion_existente_club" class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Seleccionar Usuario</label>
                                        <select class="form-select" id="director_usuario_club" name="director_usuario">
                                            <option value="">Sin asignar</option>
                                            <!-- Se llenará con JavaScript -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seccion crear nuevo usuario -->
                            <div id="seccion_nuevo_club" style="display:none;">
                                <small class="text-muted d-block mb-3">Crea un nuevo usuario director completando los siguientes campos</small>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre de Usuario</label>
                                        <input type="text" id="nuevo_nombre_club" name="nuevo_nombre" class="form-control" placeholder="usuario.director">
                                        <small class="text-muted">Solo letras, números, puntos y guiones</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="nuevo_email_club" name="nuevo_email" class="form-control" placeholder="director@universidad.com">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contraseña Temporal</label>
                                        <input type="password" id="nuevo_password_club" name="nuevo_password" class="form-control" placeholder="Mínimo 8 caracteres">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Completo del Director</label>
                                        <input type="text" id="nombre_completo_club" name="nombre_completo" class="form-control" placeholder="Juan Pérez">
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
                            <span id="btnTextoClub">Crear Club</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalles -->
    <div class="modal fade" id="modalDetallesClub" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Información General</h6>
                            <hr>
                            <p><strong>ID:</strong> <span id="detalle-id-club"></span></p>
                            <p><strong>Nombre:</strong> <span id="detalle-nombre-club"></span></p>
                            <p><strong>Estado:</strong> <span id="detalle-activo-club"></span></p>
                            <p><strong>Horario:</strong> <span id="detalle-horario-club"></span></p>
                            <p><strong>Día de Reunión:</strong> <span id="detalle-dia-club"></span></p>
                            <p><strong>Lugar:</strong> <span id="detalle-lugar-club"></span></p>
                        </div>

                        <!-- Información del Director -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Director/Responsable</h6>
                            <hr>
                            <p><strong>Nombre:</strong> <span id="detalle-director-club"></span></p>
                            <p><strong>Contacto:</strong> <span id="detalle-contacto-club"></span></p>
                        </div>

                        <!-- Descripción -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">Descripción</h6>
                            <hr>
                            <p id="detalle-descripcion-club"></p>
                        </div>

                        <!-- Cupos -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Información de Cupos</h6>
                            <hr>
                            <p><strong>Cupo Actual:</strong> <span id="detalle-cupo-actual-club"></span></p>
                            <p><strong>Cupo Máximo:</strong> <span id="detalle-cupo-maximo-club"></span></p>
                            <div class="progress" style="height: 25px;">
                                <div id="detalle-progreso-club" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                            </div>
                        </div>

                        <!-- Imagen Principal -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Logo del Club</h6>
                            <hr>
                            <div class="text-center" id="detalle-imagen-container-club">
                                <!-- Imagen -->
                            </div>
                        </div>

                        <!-- Galería -->
                        <div class="col-md-6 mb-4">
                            <h6 class="mb-3">Galería de Imágenes</h6>
                            <hr>
                            <div id="detalle-galeria-container-club">
                                <!-- Galería -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <?php if($puede_editar): ?>
                    <button type="button" class="btn btn-primary" id="btnEditarDesdeDetallesClub">Editar Club</button>
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
    <input type="file" id="fileInputClub" style="display:none;" accept="image/*">

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;
        const modalClub = new bootstrap.Modal(document.getElementById('modalClub'));
        const modalDetallesClub = new bootstrap.Modal(document.getElementById('modalDetallesClub'));

        // Galería de imágenes
        let galeriaClub = [];

        // Función para agregar imagen a la galería
        function agregarImagenGaleriaClub(url) {
            const container = document.getElementById('galeriaContainerClub');
            const div = document.createElement('div');
            div.className = 'mb-2 p-2 border rounded';
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${url}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    <small class="flex-grow-1">${url}</small>
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.parentElement.remove(); galeriaClub = galeriaClub.filter(g => g !== '${url}'); document.getElementById('clubGaleria').value = JSON.stringify(galeriaClub);">
                        Eliminar
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        // Botón agregar imagen a galería
        document.getElementById('btnAgregarGaleriaClub').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInputClub');
            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'galeria_club');

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            galeriaClub.push(imagenURL);
                            agregarImagenGaleriaClub(imagenURL);
                            document.getElementById('clubGaleria').value = JSON.stringify(galeriaClub);
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
            console.log('🔍 Cargando usuarios disponibles...');
            fetch(siteURL + 'assets/API/usuarios/listar_disponibles.php?tipo=club')
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('📄 Respuesta cruda:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('📦 Respuesta del API:', data);
                        if (data.success == 1) {
                            const select = document.getElementById('director_usuario_club');
                            console.log('📝 Select encontrado:', select);
                            select.innerHTML = '<option value="">Sin asignar</option>';
                            data.data.forEach(usuario => {
                                // Mostrar nombre completo si existe, sino mostrar username
                                const nombreMostrar = usuario.NOMBRE_COMPLETO || usuario.NOMBRE;
                                const emailMostrar = usuario.EMAIL ? `(${usuario.EMAIL})` : '';
                                select.innerHTML += `<option value="${usuario.ID}">${nombreMostrar} ${emailMostrar}</option>`;
                            });
                            console.log('✅ Usuarios cargados:', data.data.length);
                        } else {
                            console.warn('⚠️ No hay usuarios disponibles:', data.message);
                        }
                    } catch(e) {
                        console.error('❌ Error parseando JSON:', e);
                        console.error('📄 Texto recibido:', text.substring(0, 500));
                    }
                })
                .catch(error => console.error('❌ Error cargando usuarios:', error));
        }

        // Manejar cambio de tipo de director
        document.querySelectorAll('input[name="tipo_director"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const seccionExistente = document.getElementById('seccion_existente_club');
                const seccionNuevo = document.getElementById('seccion_nuevo_club');

                if (this.value === 'existente') {
                    seccionExistente.style.display = 'block';
                    seccionNuevo.style.display = 'none';
                    // Limpiar campos de nuevo usuario
                    document.getElementById('nuevo_nombre_club').value = '';
                    document.getElementById('nuevo_email_club').value = '';
                    document.getElementById('nuevo_password_club').value = '';
                    document.getElementById('nombre_completo_club').value = '';
                } else {
                    seccionExistente.style.display = 'none';
                    seccionNuevo.style.display = 'block';
                    // Limpiar selección de usuario existente
                    document.getElementById('director_usuario_club').value = '';
                }
            });
        });

        // Abrir formulario para crear
        function abrirFormularioCrear() {
            document.getElementById('formClub').reset();
            document.getElementById('clubId').value = '';
            document.getElementById('modalClubTitle').textContent = 'Nuevo Club';
            document.getElementById('btnTextoClub').textContent = 'Crear Club';

            // Mostrar sección de crear director (solo en crear)
            document.getElementById('containerDirectorClub').style.display = 'block';
            document.getElementById('containerActivoClub').style.display = 'none';

            // Resetear radio buttons
            document.getElementById('director_existente_club').checked = true;
            document.getElementById('seccion_existente_club').style.display = 'block';
            document.getElementById('seccion_nuevo_club').style.display = 'none';

            // Limpiar previsualización de logo
            document.getElementById('previewLogoClub').style.display = 'none';
            document.getElementById('clubImagenUrl').value = '';

            // Limpiar galería
            galeriaClub = [];
            document.getElementById('galeriaContainerClub').innerHTML = '';
            document.getElementById('clubGaleria').value = '';

            // Cargar usuarios disponibles
            cargarUsuariosDisponibles();

            modalClub.show();
        }

        // Botón de subir logo
        document.getElementById('btnUploadLogoClub').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInputClub');

            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'club');

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            document.getElementById('clubImagenUrl').value = imagenURL;
                            document.getElementById('logoPreviewClub').src = imagenURL;
                            document.getElementById('previewLogoClub').style.display = 'block';

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

        // Editar club
        function editarClub(id) {
            fetch(siteURL + 'assets/API/clubes/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const club = data.data.find(c => c.ID == id);
                        if (club) {
                            document.getElementById('clubId').value = club.ID;
                            document.getElementById('clubNombre').value = club.NOMBRE;
                            document.getElementById('clubDescripcion').value = club.DESCRIPCION || '';
                            document.getElementById('clubActivo').value = club.ACTIVO;

                            // Cargar logo si existe
                            if (club.IMAGEN_PRINCIPAL) {
                                document.getElementById('clubImagenUrl').value = club.IMAGEN_PRINCIPAL;
                                document.getElementById('logoPreviewClub').src = club.IMAGEN_PRINCIPAL;
                                document.getElementById('previewLogoClub').style.display = 'block';
                            } else {
                                document.getElementById('clubImagenUrl').value = '';
                                document.getElementById('previewLogoClub').style.display = 'none';
                            }

                            // Cargar galería existente (desde VRE_GALERIA)
                            const imagenesArray = (club.IMAGENES || []).map(img => img.URL_IMAGEN);
                            galeriaClub = imagenesArray;
                            document.getElementById('clubGaleria').value = JSON.stringify(galeriaClub);
                            document.getElementById('galeriaContainerClub').innerHTML = '';
                            galeriaClub.forEach(url => agregarImagenGaleriaClub(url));

                            document.getElementById('modalClubTitle').textContent = 'Editar Club: ' + club.NOMBRE;
                            document.getElementById('btnTextoClub').textContent = 'Actualizar Club';

                            // Ocultar sección de crear director (solo en editar)
                            document.getElementById('containerDirectorClub').style.display = 'none';
                            document.getElementById('containerActivoClub').style.display = 'block';

                            modalClub.show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Guardar club
        document.getElementById('formClub').addEventListener('submit', function(e) {
            e.preventDefault();

            const clubId = document.getElementById('clubId').value;
            const nombre = document.getElementById('clubNombre').value.trim();
            const descripcion = document.getElementById('clubDescripcion').value.trim();

            // Validación básica
            if (!nombre || !descripcion) {
                jQuery.notify({
                    title: 'Error',
                    message: 'Por favor complete todos los campos obligatorios.'
                }, { type: 'danger' });
                return;
            }

            // Si es crear (no editar), validar campos del director
            if (!clubId) {
                const tipoDirector = document.querySelector('input[name="tipo_director"]:checked').value;

                if (tipoDirector === 'nuevo') {
                    const nuevoNombre = document.getElementById('nuevo_nombre_club').value.trim();
                    const nuevoEmail = document.getElementById('nuevo_email_club').value.trim();
                    const nuevoPassword = document.getElementById('nuevo_password_club').value.trim();
                    const nombreCompleto = document.getElementById('nombre_completo_club').value.trim();

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

            // Crear FormData con todos los campos
            const formData = new FormData(this);

            let url;
            if (clubId) {
                formData.append('id', clubId);
                url = siteURL + 'assets/API/clubes/editar.php';
            } else {
                url = siteURL + 'assets/API/clubes/crear.php';
            }

            // Deshabilitar botón mientras se procesa
            const btnSubmit = document.querySelector('#formClub button[type="submit"]');
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
                        modalClub.hide();

                        setTimeout(() => {
                            cargarClubes();
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

        // Ver detalles del club
        function verDetallesClub(id) {
            fetch(siteURL + 'assets/API/clubes/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const club = data.data.find(c => c.ID == id);
                        if (club) {
                            // Información General
                            document.getElementById('detalle-id-club').textContent = club.ID;
                            document.getElementById('detalle-nombre-club').textContent = club.NOMBRE;
                            const activoBadge = club.ACTIVO == 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
                            document.getElementById('detalle-activo-club').innerHTML = activoBadge;
                            document.getElementById('detalle-horario-club').textContent = club.HORARIO || '-';
                            document.getElementById('detalle-dia-club').textContent = club.DIA_REUNION || '-';
                            document.getElementById('detalle-lugar-club').textContent = club.LUGAR || '-';

                            // Director
                            document.getElementById('detalle-director-club').textContent = club.RESPONSABLE_NOMBRE || '-';
                            document.getElementById('detalle-contacto-club').textContent = club.CONTACTO || '-';

                            // Descripción
                            document.getElementById('detalle-descripcion-club').textContent = club.DESCRIPCION || 'Sin descripción';

                            // Cupos
                            document.getElementById('detalle-cupo-actual-club').textContent = club.CUPO_ACTUAL || '0';
                            document.getElementById('detalle-cupo-maximo-club').textContent = club.CUPO_MAXIMO || 'Sin límite';

                            if (club.CUPO_MAXIMO) {
                                const porcentaje = (club.CUPO_ACTUAL / club.CUPO_MAXIMO) * 100;
                                const progreso = document.getElementById('detalle-progreso-club');
                                progreso.style.width = porcentaje + '%';
                                progreso.textContent = Math.round(porcentaje) + '%';
                                progreso.className = 'progress-bar ' + (porcentaje >= 80 ? 'bg-danger' : porcentaje >= 50 ? 'bg-warning' : 'bg-success');
                            }

                            // Imagen
                            const imagenContainer = document.getElementById('detalle-imagen-container-club');
                            if (club.IMAGEN_PRINCIPAL) {
                                imagenContainer.innerHTML = `<img src="${club.IMAGEN_PRINCIPAL}" style="max-width: 100%; max-height: 300px; border-radius: 8px;">`;
                            } else {
                                imagenContainer.innerHTML = '<p class="text-muted">Sin imagen</p>';
                            }

                            // Galería
                            const galeriaContainer = document.getElementById('detalle-galeria-container-club');
                            const galeria = club.IMAGENES || [];
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
                            const btnEditar = document.getElementById('btnEditarDesdeDetallesClub');
                            if (btnEditar) {
                                btnEditar.onclick = function() {
                                    modalDetallesClub.hide();
                                    setTimeout(() => editarClub(club.ID), 300);
                                };
                            }

                            modalDetallesClub.show();
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
            if (e.target.closest('.btn-eliminar-club')) {
                const btn = e.target.closest('.btn-eliminar-club');
                const id = btn.getAttribute('data-id');
                const nombre = btn.getAttribute('data-nombre');
                eliminarClub(id, nombre);
            }
        });

        // Cargar clubes
        function cargarClubes() {
            console.log('🔄 Cargando clubes...');

            fetch(siteURL + 'assets/API/clubes/listar.php')
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Clubes recibidos:', data.data.length);
                    if (data.success == 1) {
                        const emptyState = document.getElementById('emptyStateClubs');
                        const tableContainer = document.getElementById('tableContainerClubs');

                        // Si no hay clubes, mostrar estado vacío
                        if (data.data.length === 0) {
                            emptyState.style.display = 'block';
                            tableContainer.style.display = 'none';

                            // Actualizar iconos para el estado vacío
                            if (typeof acorn !== 'undefined') {
                                acorn.icons();
                            }
                            console.log('📭 No hay clubes, mostrando estado vacío');
                            return;
                        }

                        // Si hay clubes, mostrar tabla
                        emptyState.style.display = 'none';
                        tableContainer.style.display = 'block';

                        // PASO 1: Destruir DataTable PRIMERO si existe
                        if ($.fn.DataTable.isDataTable('#tablaClubes')) {
                            $('#tablaClubes').DataTable().clear().destroy();
                        }

                        // PASO 2: Construir el HTML
                        const tbody = document.querySelector('#tablaClubes tbody');
                        let htmlRows = '';

                        data.data.forEach(club => {
                            const row = `
                                <tr>
                                    <td>${club.ID}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            ${club.IMAGEN_PRINCIPAL ? `<img src="${club.IMAGEN_PRINCIPAL}" class="rounded-circle me-2" style="width:40px;height:40px;object-fit:cover;" alt="${club.NOMBRE}">` : ''}
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
                                        <button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="verDetallesClub(${club.ID})">
                                            <i class="fa fa-eye"></i>
                                            <span class="d-none d-xxl-inline-block">Ver</span>
                                        </button>
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarClub(${club.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1 btn-eliminar-club" type="button" data-id="${club.ID}" data-nombre="${club.NOMBRE.replace(/"/g, '&quot;')}">
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
                        $('#tablaClubes').DataTable({
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
                    console.error('❌ Error al cargar clubes:', error);
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
                        console.log('🗑️ Club eliminado, recargando...');
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
                    console.error('❌ Error al eliminar:', error);
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
