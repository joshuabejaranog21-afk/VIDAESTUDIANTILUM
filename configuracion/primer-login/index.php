<?php
include('../../assets/php/template.php');
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="h-100">
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fa fa-shield-alt"></i> Cambio de Contraseña Obligatorio
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fa fa-exclamation-triangle"></i> Primer inicio de sesión</h5>
                            <p class="mb-0">Por seguridad, debes cambiar tu contraseña temporal antes de continuar.</p>
                        </div>

                        <div class="alert alert-info mb-4">
                            <strong><i class="fa fa-info-circle"></i> Requisitos de seguridad:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Mínimo 6 caracteres</li>
                                <li>Usa una contraseña que puedas recordar fácilmente</li>
                                <li>No compartas tu contraseña con nadie</li>
                            </ul>
                        </div>

                        <form autocomplete="off" id="form-cambiar-pass">
                            <input type="hidden" name="nombre" value="<?php echo $temp->usuario_nombre ?>">

                            <div class="mb-4">
                                <label class="form-label fw-bold">Usuario actual</label>
                                <input type="text" class="form-control" value="<?php echo $temp->usuario_nombre ?>" disabled />
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Contraseña temporal *</label>
                                <input name="pass_ant" type="password" class="form-control form-control-lg" required placeholder="Ingresa tu contraseña temporal" />
                                <small class="text-muted">Esta es la contraseña que te proporcionó el administrador</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Nueva contraseña *</label>
                                <input name="pass" id="pass1" type="password" class="form-control form-control-lg" required placeholder="Ingresa tu nueva contraseña" />
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Confirmar nueva contraseña *</label>
                                <input id="pass2" type="password" class="form-control form-control-lg" required placeholder="Confirma tu nueva contraseña" />
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-lg" onclick="cambiarPassword()">
                                    <i class="fa fa-check-circle"></i> Cambiar Contraseña y Continuar
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <a href="<?php echo $temp->siteURL ?>assets/API/sesion/cerrar/" class="text-muted">
                                <i class="fa fa-sign-out-alt"></i> Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->scripts() ?>
    <script>
        function cambiarPassword() {
            var pass1 = $("#pass1").val();
            var pass2 = $("#pass2").val();

            if (pass1 === "" || pass2 === "") {
                jQuery.notify({
                    title: 'Error',
                    message: 'Por favor, completa todos los campos'
                }, {
                    type: 'warning',
                    delay: 5000,
                    placement: {
                        from: 'top',
                        align: 'right',
                    }
                });
                return;
            }

            if (pass1 !== pass2) {
                jQuery.notify({
                    title: 'Error',
                    message: 'Las contraseñas no coinciden'
                }, {
                    type: 'warning',
                    delay: 5000,
                    placement: {
                        from: 'top',
                        align: 'right',
                    }
                });
                return;
            }

            if (pass1.length < 6) {
                jQuery.notify({
                    title: 'Error',
                    message: 'La contraseña debe tener al menos 6 caracteres'
                }, {
                    type: 'warning',
                    delay: 5000,
                    placement: {
                        from: 'top',
                        align: 'right',
                    }
                });
                return;
            }

            var ruta = '<?php echo $temp->siteURL ?>assets/API/usuarios/cambiarPasswordPrimerLogin.php';
            const data = new FormData(document.getElementById('form-cambiar-pass'));

            fetch(ruta, {
                    method: "POST",
                    body: data
                })
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if (response['success'] == 1) {
                        jQuery.notify({
                            title: '¡Éxito!',
                            message: 'Contraseña actualizada correctamente'
                        }, {
                            type: 'success',
                            delay: 3000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            }
                        });
                        setTimeout(function() {
                            window.location = '<?php echo $temp->siteURL ?>';
                        }, 2000);
                    } else {
                        jQuery.notify({
                            title: 'Error',
                            message: response['message'] || 'Por favor, verifica tu contraseña temporal'
                        }, {
                            type: 'danger',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    jQuery.notify({
                        title: 'Error',
                        message: 'Ocurrió un error al cambiar la contraseña'
                    }, {
                        type: 'danger',
                        delay: 5000,
                        placement: {
                            from: 'top',
                            align: 'right',
                        }
                    });
                });
        }

        // Prevenir navegación hacia atrás
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    </script>
</body>

</html>
