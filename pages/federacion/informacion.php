<?php
include('../../assets/php/template.php');
$temp = new Template('Información - Federación Estudiantil');
if (!$temp->validate_session(2)) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 15px;
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
                        <div class="col-12">
                            <h1 class="mb-0 pb-0 display-4">
                                <i class="fa fa-info-circle"></i> Información de la Federación
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active">Federación - Información</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Title End -->

                <!-- Form Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-5">
                            <div class="card-body">
                                <form id="infoForm">
                                    <input type="hidden" id="infoId" name="id">

                                    <div class="mb-4">
                                        <label class="form-label">Título Principal</label>
                                        <input type="text" class="form-control" name="titulo"
                                               placeholder="Ej: Federación Estudiantil UM" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fa fa-question-circle"></i> ¿Qué es la Federación?
                                        </label>
                                        <div id="editor_que_es" style="height: 200px;"></div>
                                        <textarea name="contenido_que_es" style="display:none;"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fa fa-vote-yea"></i> ¿Cómo se eligen sus miembros?
                                        </label>
                                        <div id="editor_eleccion" style="height: 200px;"></div>
                                        <textarea name="contenido_eleccion" style="display:none;"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fa fa-calendar-check"></i> ¿Qué actividades realizan?
                                        </label>
                                        <div id="editor_actividades" style="height: 200px;"></div>
                                        <textarea name="contenido_actividades" style="display:none;"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fa fa-bullseye"></i> ¿Para qué sirve?
                                        </label>
                                        <div id="editor_para_que_sirve" style="height: 200px;"></div>
                                        <textarea name="contenido_para_que_sirve" style="display:none;"></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">
                                                <i class="fa fa-video"></i> URL del Video (YouTube, Vimeo, etc.)
                                            </label>
                                            <input type="url" class="form-control" name="video_url"
                                                   placeholder="https://youtube.com/watch?v=...">
                                            <small class="text-muted">Opcional: URL completa del video</small>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">
                                                <i class="fa fa-image"></i> Imagen Principal
                                            </label>
                                            <input type="file" class="form-control" name="imagen"
                                                   accept="image/*" onchange="previewImage(event)">
                                            <small class="text-muted">JPG, PNG - Máx. 3MB</small>

                                            <div id="currentImage" style="display:none;" class="mt-3">
                                                <p class="text-muted">Imagen actual:</p>
                                                <img id="currentImageImg" src="" style="max-width: 300px; border-radius: 8px;">
                                            </div>

                                            <div id="imagePreview" style="display:none;" class="mt-3">
                                                <p class="text-muted">Vista previa:</p>
                                                <img id="previewImg" src="" style="max-width: 300px; border-radius: 8px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i> Guardar Información
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Form End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        const apiUrl = "<?php echo $temp->siteURL ?>assets/API/federacion/";

        // Initialize Quill editors
        const quillQueEs = new Quill('#editor_que_es', { theme: 'snow' });
        const quillEleccion = new Quill('#editor_eleccion', { theme: 'snow' });
        const quillActividades = new Quill('#editor_actividades', { theme: 'snow' });
        const quillParaQueSirve = new Quill('#editor_para_que_sirve', { theme: 'snow' });

        // Load current information
        function loadInfo() {
            fetch(apiUrl + 'info_obtener.php')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.info) {
                        const info = data.info;

                        $('#infoId').val(info.id);
                        $('[name="titulo"]').val(info.titulo || '');
                        $('[name="video_url"]').val(info.video_url || '');

                        // Set Quill contents
                        if (info.contenido_que_es) {
                            quillQueEs.root.innerHTML = info.contenido_que_es;
                        }
                        if (info.contenido_eleccion) {
                            quillEleccion.root.innerHTML = info.contenido_eleccion;
                        }
                        if (info.contenido_actividades) {
                            quillActividades.root.innerHTML = info.contenido_actividades;
                        }
                        if (info.contenido_para_que_sirve) {
                            quillParaQueSirve.root.innerHTML = info.contenido_para_que_sirve;
                        }

                        // Show current image
                        if (info.imagen_principal) {
                            $('#currentImageImg').attr('src', info.imagen_principal);
                            $('#currentImage').show();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Preview image
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            }
        }

        // Save form
        $('#infoForm').on('submit', function(e) {
            e.preventDefault();

            // Update textareas with Quill content
            $('[name="contenido_que_es"]').val(quillQueEs.root.innerHTML);
            $('[name="contenido_eleccion"]').val(quillEleccion.root.innerHTML);
            $('[name="contenido_actividades"]').val(quillActividades.root.innerHTML);
            $('[name="contenido_para_que_sirve"]').val(quillParaQueSirve.root.innerHTML);

            const formData = new FormData(this);

            $.ajax({
                url: apiUrl + 'info_actualizar.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadInfo();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error al guardar: ' + error);
                }
            });
        });

        // Initialize
        $(document).ready(function() {
            loadInfo();
        });
    </script>
</body>
</html>
