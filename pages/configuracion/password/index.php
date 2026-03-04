<?php
include('../../../assets/php/template.php');
$temp = new Template('Cambiar contraseña');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
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
                <!-- Title and Top Buttons Start -->
                <div class="page-title-container">
                    <div class="row">
                        <!-- Title Start -->
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">
                                <i class="fa fa-key"></i> <?php echo $temp->titulo ?>
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cambiar contraseña</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-lock"></i> Cambiar Contraseña
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-4">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Seguridad:</strong> Asegúrate de usar una contraseña segura que incluya letras, números y caracteres especiales.
                                </div>
                                <form autocomplete="off" id="form-crear">
                                    <input type="hidden" name="nombre" value="<?php echo $temp->usuario_nombre ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Contraseña actual *</label>
                                        <input name="pass_ant" type="password" class="form-control" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contraseña nueva *</label>
                                        <input name="pass" id="pass1" type="password" class="form-control" required />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Repetir contraseña nueva *</label>
                                        <input id="pass2" type="password" class="form-control" required />
                                    </div>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-primary btn-lg" id="btnCrear" onclick="crear()">
                                            <i class="fa fa-save"></i> Actualizar Contraseña
                                        </button>
                                    </div>
                                </form>
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
        function crear() {
            var ruta = '/cpanel/assets/API/usuarios/cambiarPassword/';
            const data = new FormData(document.getElementById('form-crear'));
            // POST
            if ($("#pass1").val() === $("#pass2").val()) {
                fetch(ruta, {
                        method: "POST",
                        body: data
                    })
                    .then(response => response.json())
                    .then(response => {
                        console.log(response);
                        if (response['success'] == 1) {
                            jQuery.notify({
                                title: '¡Exito!',
                                message: response['message']
                            },{
                                type: 'success',
                                delay: 5000,
                                placement: {
                                    from: 'top',
                                    align: 'right',
                                }
                            });
                        } else {
                            jQuery.notify({
                                title: '¡Error!',
                                message: 'Por favor, verifique las contraseñas proporcionadas.'
                            }, {
                                type: 'warning',
                                delay: 5000,
                                placement: {
                                    from: 'top',
                                    align: 'right',
                                }
                            });
                        }
                    });
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
        }
    </script>
</body>

</html>