<?php
include('../../assets/php/template.php');
$temp = new Template('Biblioteca');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="/cpanel/assets/css/vendor/dropzone.min.css" type="text/css" />
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 15px;
        }

        .dropzone {
            border: 2px dashed #ddd;
            border-radius: 10px;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .img-fluid.rounded {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
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
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <div class="alert alert-info mb-0 w-100 w-md-auto">
                                <i class="fa fa-info-circle"></i>
                                Arrastra imágenes abajo para subirlas
                            </div>
                        </div>
                        <!-- Title End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <section class="scroll-section" id="images">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa fa-cloud-upload-alt"></i> Subir Imágenes
                            </h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="dropzone" id="dropzone"></div>
                            </form>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle"></i>
                                    Formatos aceptados: PNG, JPEG. Las imágenes se agregan automáticamente a la biblioteca.
                                </small>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-images"></i> Biblioteca de Imágenes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="contentTable">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="mt-3">Cargando biblioteca...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="text" class="" id="pivCopy" style="opacity:0">
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
    <script src="/cpanel/assets/js/vendor/dropzone.min.js"></script>
    <script src="/cpanel/assets/js/cs/dropzone.templates.js"></script>
    <script type="text/javascript">
        var myDropzone = new Dropzone(
            "div#dropzone", {
                dictDefaultMessage: "Arrastra tus imágenes aquí para subirlas",
                dictFallbackMessage: "Tu navegador no soporta esta función.",
                dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
                dictFileTooBig: "El archivo es muy grande ({{filesize}}MiB). Tamaño máximo de archivo: {{maxFilesize}}MiB.",
                dictInvalidFileType: "No puedes subir archivos de este tipo.",
                dictResponseError: "El servidor ha respondido con el dódigo {{statusCode}}.",
                dictCancelUpload: "Cancelar subida",
                dictCancelUploadConfirmation: "¿Estás seguro que deseas cancelar la subida de este archivo?",
                dictRemoveFile: "Remover archivo",
                dictMaxFilesExceeded: "No puedes subir más archivos.",

                url: "/cpanel/assets/API/biblioteca/upload.php",
                paramName: "file",
                thumbnailWidth: 160,
                maxFilesize: 1,
                uploadMultiple: true,
                previewTemplate: DropzoneTemplates.previewTemplate,
                acceptedFiles: 'image/png,image/jpeg'
            }
        );
        myDropzone.on("complete", function(file) {
            leer();
        });

        Dropzone.autoDiscover = false;

        function copiarAlPortapapeles(texto) {
            // Crea un campo de texto "oculto"
            var aux = document.getElementById('pivCopy');

            // Asigna el contenido del elemento especificado al valor del campo
            aux.setAttribute("value", texto);
            // Añade el campo a la página
            // document.body.appendChild(aux);

            // Selecciona el contenido del campo
            // aux.classList.remove('d-none');
            aux.select();
            // aux.classList.add('d-none');

            // Copia el texto seleccionado
            document.execCommand("copy");

            // Elimina el campo de la página
            // document.body.removeChild(aux);

            // notifica al usuario que ha sido copiado al portapapeles
            // sistema.showNotificationPersonal('top', 'right', 'Copiado al portapapeles', 'info');
            jQuery.notify({
                title: 'Copiado al portapapeles',
                message: texto
            }, {
                type: 'info',
                delay: 5000,
                placement: {
                    from: 'top',
                    align: 'right',
                },
            }, );
        }
    </script>
    <script>
        function leer() {
            var ruta = "tabla.php";
            var contentTable = $('#contentTable');
            //GET
            fetch(ruta)
                .then(response => response.text())
                .then(response => contentTable.html(response));
        }

        function borrar(id) {
            var ruta = '/cpanel/assets/API/biblioteca/borrar/' + id;
            var contentTable = $('#contentTable');
            //GET
            fetch(ruta)
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if (response['success'] == 1) {
                        jQuery.notify({
                            title: '¡Exito!',
                            message: response['message']
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        }, );
                        leer();
                    } else {
                        jQuery.notify({
                            title: '¡Error!',
                            message: response['message']
                        }, {
                            type: 'warning',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        }, );
                    }
                });
        }

        
        window.addEventListener('ready', leer());
    </script>
</body>

</html>