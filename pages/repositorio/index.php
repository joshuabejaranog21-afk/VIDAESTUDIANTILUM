<?php
session_start();

// SOLO ADMINISTRADORES PUEDEN ACCEDER AL CPANEL
// Verificar si es admin
include('../../assets/php/template.php');
$temp = new Template('Mi Repositorio de Fotos');

// Solo admins pueden acceder
if (!$temp->validate_session()) {
    header('Location: ../../login.php');
    exit();
}

// Variables para compatibilidad (admins no las usan)
$is_estudiante = false;
$is_admin = true;
$matricula_estudiante = '';
$nombre_estudiante = 'Administrador';
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <style>
        .photo-card {
            transition: transform 0.2s;
        }
        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .photo-preview {
            height: 250px;
            object-fit: cover;
            cursor: pointer;
        }
        .student-info-card {
            background: var(--primary);
            color: white;
        }
        .student-photo-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
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
                            <h1 class="mb-0 pb-0 display-4">Administrar Repositorio de Fotos</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Repositorio</li>
                                </ul>
                            </nav>
                        </div>
                        <?php if ($is_estudiante): ?>
                        <div class="col-12 col-md-5 text-end">
                            <div class="alert alert-success mb-2">
                                <i class="fa fa-user-check"></i> Sesión activa: <strong><?php echo $nombre_estudiante ?: $matricula_estudiante; ?></strong>
                            </div>
                            <button class="btn btn-outline-danger btn-sm" onclick="cerrarSesion()">
                                <i class="fa fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Title End -->

                <!-- Info Alert -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Repositorio de Fotografías Anuales:</strong> Gestiona las fotos de estudiantes organizadas por año.
                            Cada estudiante puede tener múltiples fotos (una por cada año académico).
                        </div>
                    </div>
                </div>

                <!-- Todos los Estudiantes con Fotos -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-0">
                                            <i class="fa fa-users"></i> Estudiantes con Fotos
                                        </h5>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button class="btn btn-primary" onclick="showUploadModal()">
                                            <i class="fa fa-plus"></i> Subir Nueva Foto
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="estudiantesTableContainer">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="mt-3">Cargando estudiantes...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Info Card -->
                <div class="row mb-4" id="studentInfoContainer" style="display:none;">
                    <div class="col-12">
                        <div class="card student-info-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h3 id="studentName" class="mb-2"></h3>
                                        <p class="mb-1"><strong>Matrícula:</strong> <span id="studentMatricula"></span></p>
                                        <p class="mb-1"><strong>Carrera:</strong> <span id="studentCarrera"></span></p>
                                        <p class="mb-0"><strong>Semestre:</strong> <span id="studentSemestre"></span></p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <h2 class="mb-0"><span id="totalFotos">0</span></h2>
                                        <p class="mb-0">Fotografías</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos Grid -->
                <div class="row" id="photosContainer">
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-camera fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Ingresa tu matrícula para ver tu repositorio personal</p>
                    </div>
                </div>
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-upload"></i> Subir Nueva Fotografía
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <!-- Estudiante Info Alert (visible cuando existe) -->
                        <div class="alert alert-success" id="existingStudentAlert" style="display:none;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Estudiante registrado:</strong> <span id="existingStudentName"></span>
                            <br>
                            <small>Solo necesitas completar la información de la foto</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Matrícula *</label>
                                <input type="text" class="form-control" name="matricula" id="uploadMatricula"
                                       required maxlength="7" pattern="[0-9]{7}">
                                <small class="text-muted" id="matriculaHelpText">Ingresa la matrícula de 7 dígitos</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Foto *</label>
                                <select class="form-select" name="tipo_foto" required>
                                    <option value="INDIVIDUAL">Individual</option>
                                    <option value="GRUPAL">Grupal</option>
                                    <option value="EVENTO">Evento</option>
                                    <option value="ACADEMICA">Académica</option>
                                    <option value="OTRA">Otra</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campos de estudiante (ocultos cuando ya existe) -->
                        <div id="studentFieldsContainer">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" id="uploadNombre">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido" id="uploadApellido">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Carrera</label>
                                    <input type="text" class="form-control" name="carrera" id="uploadCarrera">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Semestre</label>
                                    <input type="number" class="form-control" name="semestre" id="uploadSemestre" min="1" max="12">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" name="titulo" placeholder="Ej: Graduación 2024">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="2"
                                      placeholder="Describe la fotografía..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ciclo Escolar *</label>
                                <select class="form-select" name="ciclo_escolar" id="cicloEscolar" required>
                                    <option value="">Selecciona ciclo...</option>
                                </select>
                                <small class="text-muted">Ciclo académico de la fotografía</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Publicación *</label>
                                <input type="datetime-local" class="form-control" name="release_date" id="releaseDate" required>
                                <small class="text-muted">Estudiantes verán la foto desde esta fecha</small>
                            </div>
                        </div>

                        <div class="alert alert-info mb-3">
                            <i class="fa fa-info-circle"></i>
                            <strong>Control de Publicación:</strong> Los administradores pueden subir fotos con anticipación.
                            Los estudiantes solo verán las fotos después de la fecha de publicación establecida.
                        </div>

                        <!-- Flickr URL Section (ÚNICA OPCIÓN) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fab fa-flickr"></i> URL de Flickr o BBCode *
                            </label>
                            <textarea class="form-control" id="flickrInput" name="foto_url" rows="4" required
                                      placeholder="Pega aquí el enlace directo o el BBCode completo de Flickr...&#10;&#10;Ejemplos:&#10;&#10;1. URL directa:&#10;https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg&#10;&#10;2. BBCode completo:&#10;[url=https://flic.kr/p/2pLPh8f][img]https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg[/img][/url]"></textarea>
                            <small class="text-muted">
                                <strong>Tip:</strong> Puedes pegar el BBCode completo y el sistema extraerá automáticamente la URL de la imagen.
                                <a href="#" onclick="showFlickrHelp(); return false;">¿Cómo obtener esto?</a>
                            </small>
                        </div>

                        <div class="mb-3">
                            <img id="imagePreview" src="" alt="Vista previa"
                                 style="max-width: 100%; display: none; border-radius: 8px;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="uploadPhoto()">
                        <i class="fa fa-upload"></i> Subir Fotografía
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div class="modal fade" id="imageViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageViewerTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageViewerImg" src="" alt="" style="max-width: 100%; max-height: 80vh;">
                    <p id="imageViewerDesc" class="mt-3 text-muted"></p>
                </div>
                <div class="modal-footer">
                    <a id="viewerFlickrBtn" href="#" target="_blank" class="btn btn-primary me-auto">
                        <i class="fab fa-flickr me-2"></i>Ver en Flickr
                    </a>
                    <a id="viewerDownloadBtn" href="#" download class="btn btn-success">
                        <i class="fa fa-download me-2"></i>Descargar
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flickr Help Modal -->
    <div class="modal fade" id="flickrHelpModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-question-circle"></i> ¿Cómo obtener la URL o BBCode de Flickr?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Opción 1: Copiar BBCode (Más Fácil)</h6>
                    <ol>
                        <li>Abre la foto en Flickr</li>
                        <li>Busca el botón de compartir (Share) o menú de opciones</li>
                        <li>Selecciona <strong>"BBCode"</strong> o <strong>"Grab the link"</strong></li>
                        <li>Copia TODO el código BBCode</li>
                        <li>Pégalo completo en el campo de arriba</li>
                    </ol>
                    <div class="alert alert-success">
                        <strong>Ejemplo de BBCode:</strong><br>
                        <code>[url=https://flic.kr/p/2pLPh8f][img]https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg[/img][/url][url=https://flic.kr/p/2pLPh8f]Foto[/url]</code>
                    </div>

                    <h6 class="mb-3 mt-4">Opción 2: Copiar URL Directa</h6>
                    <ol>
                        <li>Abre la foto en Flickr</li>
                        <li>Haz clic derecho sobre la imagen</li>
                        <li>Selecciona <strong>"Copiar dirección de imagen"</strong></li>
                        <li>Pégala en el campo de arriba</li>
                    </ol>
                    <div class="alert alert-info">
                        <strong>Ejemplo de URL directa:</strong><br>
                        <code>https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg</code>
                    </div>

                    <h6 class="mb-3 mt-4">Tip</h6>
                    <p class="mb-0">El sistema detectará automáticamente qué tipo de enlace pegaste y extraerá la URL de la imagen.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/repositorio/";
        const authUrl = "<?php echo $temp->siteURL ?>assets/API/auth/";
        const placeholderImg = "<?php echo $temp->siteURL ?>assets/img/placeholder.png";
        let currentMatricula = '';
        let currentStudentData = null; // Guardar datos del estudiante
        const isEstudiante = <?php echo $is_estudiante ? 'true' : 'false'; ?>;
        const matriculaEstudiante = '<?php echo $matricula_estudiante; ?>';


        // Display student info
        function displayStudentInfo(estudiante, total) {
            $('#studentName').text(estudiante.nombre + ' ' + estudiante.apellido);
            $('#studentMatricula').text(estudiante.matricula);
            $('#studentCarrera').text(estudiante.carrera || 'No especificada');
            $('#studentSemestre').text(estudiante.semestre || 'No especificado');
            $('#totalFotos').text(total);
            $('#studentInfoContainer').fadeIn();
        }

        // Render photos
        function renderPhotos(fotos) {
            let html = '';

            fotos.forEach(foto => {
                const cicloEscolar = foto.ciclo_escolar || 'Sin ciclo';
                const fechaSubida = new Date(foto.fecha_subida).toLocaleDateString('es-MX');
                const flickrPageUrl = foto.flickr_page_url || foto.foto_url;

                // Release date info
                let releaseBadge = '';
                if (foto.release_date) {
                    const releaseDate = new Date(foto.release_date);
                    const now = new Date();
                    const isPublished = releaseDate <= now;

                    if (!isPublished) {
                        const fechaRelease = releaseDate.toLocaleDateString('es-MX');
                        releaseBadge = `<span class="badge bg-warning text-dark ms-1">
                            <i class="fa fa-clock"></i> Publicación: ${fechaRelease}
                        </span>`;
                    }
                }

                html += `
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card photo-card h-100">
                            <img src="${foto.foto_url}"
                                 class="card-img-top photo-preview"
                                 alt="${foto.titulo || 'Foto'}"
                                 onerror="this.src=placeholderImg"
                                 onclick="viewImage('${foto.foto_url}', '${foto.titulo} (${cicloEscolar})', '${foto.descripcion || ''}', '${flickrPageUrl}')">
                            <div class="card-body">
                                <h6 class="card-title">${foto.titulo || 'Sin título'}</h6>
                                <div class="mb-2">
                                    <span class="badge bg-primary">
                                        <i class="fa fa-graduation-cap"></i> ${cicloEscolar}
                                    </span>
                                    <span class="badge bg-info ms-1">${foto.tipo_foto}</span>
                                    ${releaseBadge}
                                </div>
                                ${foto.descripcion ? `<p class="card-text small text-muted">${foto.descripcion.substring(0, 80)}${foto.descripcion.length > 80 ? '...' : ''}</p>` : ''}
                                <p class="card-text small text-muted mb-0">
                                    <i class="fa fa-upload"></i> Subida: ${fechaSubida}
                                </p>
                                ${foto.total_referencias > 0 ? `
                                    <p class="card-text small text-primary mb-0">
                                        <i class="fa fa-users"></i> ${foto.total_referencias} persona(s) etiquetada(s)
                                    </p>
                                ` : ''}
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-danger w-100" onclick="deletePhoto(${foto.id})">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#photosContainer').html(html);
        }

        // Show upload modal
        function showUploadModal() {
            if (currentMatricula) {
                $('#uploadMatricula').val(currentMatricula);
            }
            $('#uploadModal').modal('show');
        }

        // Preview image
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        }

        // Upload photo (SOLO FLICKR)
        function uploadPhoto() {
            const matricula = $('#uploadMatricula').val();
            const flickrInput = $('#flickrInput').val().trim();
            const cicloEscolar = $('#cicloEscolar').val();
            const releaseDate = $('#releaseDate').val();

            // Validate matricula
            if (!matricula || matricula.length !== 7) {
                alert('Por favor ingresa una matrícula válida de 7 dígitos');
                return;
            }

            // Si es estudiante nuevo, validar que haya nombre y apellido
            if (!currentStudentData) {
                const nombre = $('#uploadNombre').val().trim();
                const apellido = $('#uploadApellido').val().trim();

                if (!nombre || !apellido) {
                    alert('Para estudiantes nuevos, Nombre y Apellido son requeridos');
                    return;
                }
            }

            // Validate Flickr URL
            if (!flickrInput) {
                alert('Por favor pega la URL de Flickr o el BBCode');
                return;
            }

            // Validate ciclo escolar
            if (!cicloEscolar) {
                alert('Por favor selecciona el ciclo escolar');
                return;
            }

            // Validate release date
            if (!releaseDate) {
                alert('Por favor selecciona la fecha de publicación');
                return;
            }

            const formData = {
                matricula: matricula,
                nombre: $('#uploadNombre').val(),
                apellido: $('#uploadApellido').val(),
                carrera: $('#uploadCarrera').val(),
                semestre: $('#uploadSemestre').val(),
                titulo: $('input[name="titulo"]').val(),
                descripcion: $('textarea[name="descripcion"]').val(),
                tipo_foto: $('select[name="tipo_foto"]').val(),
                ciclo_escolar: cicloEscolar,
                release_date: releaseDate,
                foto_url: flickrInput
            };

            $.ajax({
                url: url + 'upload-flickr.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Fotografía agregada exitosamente! La foto será visible para estudiantes desde: ' + releaseDate);
                        $('#uploadModal').modal('hide');
                        $('#uploadForm')[0].reset();
                        $('#imagePreview').hide();
                        $('#flickrInput').val('');
                        // Recargar lista de estudiantes y fotos del estudiante actual
                        loadEstudiantesTable();
                        if (currentMatricula) {
                            seleccionarEstudiante(currentMatricula);
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error al agregar la foto de Flickr');
                }
            });
        }

        // Show Flickr help
        function showFlickrHelp() {
            $('#flickrHelpModal').modal('show');
        }

        // Ya no hay toggle de método - solo Flickr

        // Preview Flickr URL
        $('#flickrInput').on('blur', function() {
            let input = $(this).val().trim();
            if (!input) return;

            // Extract URL from BBCode if needed
            let imageUrl = input;
            if (input.includes('[img]')) {
                const match = input.match(/\[img\](https?:\/\/[^\[]+)\[\/img\]/);
                if (match && match[1]) {
                    imageUrl = match[1];
                }
            }

            // Show preview
            if (imageUrl.match(/^https?:\/\//)) {
                $('#imagePreview').attr('src', imageUrl).show();
            }
        });

        // View image
        function viewImage(url, title, description, flickrPageUrl) {
            $('#imageViewerTitle').text(title || 'Fotografía');
            $('#imageViewerImg').attr('src', url);
            $('#imageViewerDesc').text(description || '');

            // Configurar botones de Flickr y descarga
            const flickrUrl = flickrPageUrl || url;
            $('#viewerFlickrBtn').attr('href', flickrUrl);
            $('#viewerDownloadBtn').attr('href', url).attr('download', (title || 'foto') + '.jpg');

            $('#imageViewerModal').modal('show');
        }

        // Delete photo
        function deletePhoto(id) {
            if (!confirm('¿Estás seguro de eliminar esta fotografía? Esta acción no se puede deshacer.')) {
                return;
            }

            $.ajax({
                url: url + 'eliminar.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        // Recargar lista de estudiantes y fotos del estudiante actual
                        loadEstudiantesTable();
                        if (currentMatricula) {
                            seleccionarEstudiante(currentMatricula);
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error al eliminar la fotografía');
                }
            });
        }

        // Verificar si estudiante existe al ingresar matrícula
        function verificarEstudiante(matricula) {
            if (!matricula || matricula.length !== 7) {
                // Reset si no hay matrícula válida
                currentStudentData = null;
                $('#studentFieldsContainer').slideDown();
                $('#existingStudentAlert').slideUp();
                $('#matriculaHelpText').html('Ingresa la matrícula de 7 dígitos').removeClass('text-success');
                return;
            }

            console.log('Verificando matrícula:', matricula);

            $.ajax({
                url: url + 'verificar-estudiante.php',
                type: 'POST',
                data: { matricula: matricula },
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta del servidor:', response);

                    if (response.success && response.exists) {
                        // Estudiante existe - ocultar campos
                        currentStudentData = response.student;
                        $('#studentFieldsContainer').slideUp();
                        $('#existingStudentAlert').slideDown();
                        $('#existingStudentName').text(response.student.nombre + ' ' + response.student.apellido);
                        $('#matriculaHelpText').html('<i class="fa fa-check-circle text-success"></i> Estudiante encontrado').addClass('text-success');

                        console.log('Estudiante encontrado:', response.student.nombre);
                    } else {
                        // Estudiante nuevo - mostrar campos
                        currentStudentData = null;
                        $('#studentFieldsContainer').slideDown();
                        $('#existingStudentAlert').slideUp();
                        $('#matriculaHelpText').html('Completa los datos del nuevo estudiante').removeClass('text-success');

                        console.log('Estudiante nuevo');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al verificar estudiante:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });

                    // Mostrar campos por defecto en caso de error
                    currentStudentData = null;
                    $('#studentFieldsContainer').slideDown();
                    $('#existingStudentAlert').slideUp();
                }
            });
        }

        // Cerrar sesión (solo admins)
        function cerrarSesion() {
            if (confirm('¿Estás seguro de cerrar sesión?')) {
                window.location.href = '../../logout.php';
            }
        }

        // Cargar tabla de estudiantes
        function loadEstudiantesTable() {
            fetch(url + 'listar-todos.php')
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.estudiantes.length > 0) {
                        renderEstudiantesTable(result.estudiantes);
                    } else {
                        $('#estudiantesTableContainer').html(`
                            <div class="text-center py-5">
                                <i class="fa fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay estudiantes con fotos registradas</p>
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#estudiantesTableContainer').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-times-circle"></i> Error al cargar los estudiantes
                        </div>
                    `);
                });
        }

        // Renderizar tabla de estudiantes
        function renderEstudiantesTable(estudiantes) {
            let html = `
                <table id="datatableEstudiantes" class="data-table nowrap hover">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Total Fotos</th>
                            <th>Última Foto</th>
                            <th class="empty">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            estudiantes.forEach(est => {
                const ultimaFecha = new Date(est.ultima_foto).toLocaleDateString('es-MX');

                html += `
                    <tr>
                        <td>
                            <img src="${est.foto_muestra}"
                                 class="student-photo-thumb"
                                 onerror="this.src=placeholderImg"
                                 alt="${est.nombre_completo}">
                        </td>
                        <td><strong>${est.matricula}</strong></td>
                        <td>${est.nombre_completo}</td>
                        <td>${est.carrera || '-'}</td>
                        <td>${est.semestre || '-'}</td>
                        <td><span class="badge bg-primary">${est.total_fotos}</span></td>
                        <td>${ultimaFecha}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1"
                                    type="button"
                                    onclick="seleccionarEstudiante('${est.matricula}')">
                                <i class="fa fa-images"></i>
                                <span class="d-none d-xxl-inline-block">Ver Fotos</span>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            $('#estudiantesTableContainer').html(html);

            // Initialize DataTable
            $('#datatableEstudiantes').DataTable({
                "order": [[1, 'asc']], // Ordenar por matrícula
                buttons: ['copy', 'excel', 'csv', 'print'],
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 20, 50, -1], [10, 20, 50, "Todos"]],
                responsive: true,
                language: {
                    "decimal": "",
                    "emptyTable": "Sin datos disponibles",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ estudiantes",
                    "infoEmpty": "Mostrando 0 a 0 de 0 datos",
                    "infoFiltered": "(Buscado entre _MAX_ datos totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Ver _MENU_ filas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se han encontrado resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar para ordenar columna ascendente",
                        "sortDescending": ": activar para ordenar columna descendente"
                    }
                }
            });
        }

        // Seleccionar estudiante y cargar sus fotos
        function seleccionarEstudiante(matricula) {
            currentMatricula = matricula;

            $('#photosContainer').html(`
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando fotografías...</p>
                </div>
            `);

            fetch(url + 'listar.php?matricula=' + matricula)
                .then(r => r.json())
                .then(result => {
                    if (result.success && result.estudiante) {
                        // Guardar datos del estudiante para auto-rellenar el modal
                        currentStudentData = result.estudiante;

                        displayStudentInfo(result.estudiante, result.total);
                        if (result.fotos && result.fotos.length > 0) {
                            renderPhotos(result.fotos);
                        } else {
                            $('#photosContainer').html(`
                                <div class="col-12 text-center py-5">
                                    <i class="fa fa-camera fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aún no hay fotografías para este estudiante</p>
                                    <button class="btn btn-primary" onclick="showUploadModal()">
                                        <i class="fa fa-upload"></i> Subir Primera Foto
                                    </button>
                                </div>
                            `);
                        }

                        // Scroll hacia las fotos
                        $('html, body').animate({
                            scrollTop: $('#studentInfoContainer').offset().top - 100
                        }, 500);
                    } else {
                        // Mostrar mensaje de error del servidor
                        const errorMsg = result.message || 'Error al cargar el repositorio';
                        console.error('Error del servidor:', errorMsg, result);
                        $('#photosContainer').html(`
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>Error:</strong> ${errorMsg}
                                </div>
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('Error de conexión:', error);
                    $('#photosContainer').html(`
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="fa fa-times-circle"></i>
                                Error de conexión al cargar el repositorio
                            </div>
                        </div>
                    `);
                });
        }


        // Generar ciclos escolares (1940-2040)
        function generarCiclosEscolares() {
            const selectCiclo = $('#cicloEscolar');
            const currentYear = new Date().getFullYear();

            // Generar ciclos desde 1940 hasta 2040
            for (let year = 2040; year >= 1940; year--) {
                const ciclo = year + '-' + (year + 1);
                const option = $('<option></option>')
                    .attr('value', ciclo)
                    .text(ciclo);

                // Marcar como seleccionado el ciclo actual
                if (year === currentYear || year === currentYear - 1) {
                    option.attr('selected', 'selected');
                }

                selectCiclo.append(option);
            }
        }

        // Establecer fecha de publicación por defecto
        function establecerFechaPublicacionDefault() {
            const now = new Date();
            // Formato: yyyy-MM-ddThh:mm
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const dateTimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;
            $('#releaseDate').val(dateTimeLocal);
        }

        // Load on ready
        $(document).ready(function() {
            // Generar ciclos escolares
            generarCiclosEscolares();
            // Cargar tabla de estudiantes
            loadEstudiantesTable();
            // Si es estudiante logueado, autocargar su repositorio
            if (isEstudiante && matriculaEstudiante) {
                seleccionarEstudiante(matriculaEstudiante);
            }

            // Listener para verificar matrícula en el modal
            $('#uploadMatricula').on('blur', function() {
                verificarEstudiante($(this).val().trim());
            });

            // También verificar cuando se escribe (después de 7 dígitos)
            $('#uploadMatricula').on('input', function() {
                const val = $(this).val().trim();
                if (val.length === 7) {
                    verificarEstudiante(val);
                }
            });

            // Auto-fill upload form from student info
            $('#uploadModal').on('show.bs.modal', function() {
                // Reset form y alertas
                $('#existingStudentAlert').hide();
                $('#studentFieldsContainer').show();
                $('#matriculaHelpText').html('Ingresa la matrícula de 7 dígitos').removeClass('text-success');

                // Establecer fecha de publicación por defecto (ahora)
                establecerFechaPublicacionDefault();

                if (currentMatricula) {
                    $('#uploadMatricula').val(currentMatricula);

                    // Si ya tenemos los datos del estudiante, rellenar todos los campos
                    if (currentStudentData) {
                        $('#uploadNombre').val(currentStudentData.nombre || '');
                        $('#uploadApellido').val(currentStudentData.apellido || '');
                        $('#uploadCarrera').val(currentStudentData.carrera || '');
                        $('#uploadSemestre').val(currentStudentData.semestre || '');

                        // Mostrar alerta de estudiante existente
                        $('#studentFieldsContainer').slideUp();
                        $('#existingStudentAlert').slideDown();
                        $('#existingStudentName').text(
                            (currentStudentData.nombre || '') + ' ' + (currentStudentData.apellido || '')
                        );
                        $('#matriculaHelpText').html('<i class="fa fa-check-circle text-success"></i> Estudiante encontrado').addClass('text-success');
                    } else {
                        // Verificar automáticamente si no tenemos los datos
                        verificarEstudiante(currentMatricula);
                    }
                }
            });
        });
    </script>
</body>

</html>
