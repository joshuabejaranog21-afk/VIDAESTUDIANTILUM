<?php
include('assets/php/template.php');
$temp = new Template('Login');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $temp->head() ?>
</head>

<body class="h-100">
    <div id="root" class="h-100">
        <!-- Background Start -->
        <div class="fixed-background"></div>
        <!-- Background End -->

        <div class="container-fluid p-0 h-100 position-relative">
            <div class="row g-0 h-100">
                <!-- Left Side Start -->
                <div class="offset-0 col-12 d-none d-lg-flex offset-md-1 col-lg h-lg-100">
                    <!-- <div class="min-h-100 d-flex align-items-center">
                        <div class="w-100 w-lg-75 w-xxl-50">
                            <div>
                                <div class="mb-5">
                                    <h1 class="display-3 text-white">Bienvenido</h1>
                                    <h1 class="display-3 text-white">Ready for Your Project</h1>
                                </div>
                                <p class="h6 text-white lh-1-5 mb-5">
                                    Dynamically target high-payoff intellectual capital for customized technologies. Objectively integrate emerging core competencies before
                                    process-centric communities...
                                </p>
                                <div class="mb-5">
                                    <a class="btn btn-lg btn-outline-white" href="index.html">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!-- Left Side End -->

                <!-- Right Side Start -->
                <div class="col-12 col-lg-auto h-100 pb-4 px-4 pt-0 p-lg-0">
                    <div class="sw-lg-70 min-h-100 bg-foreground d-flex justify-content-center align-items-center shadow-deep py-5 full-page-content-right-border">
                        <div class="sw-lg-50 text-center px-5">
                            <div class="w-50 mb-3 mx-auto">
                                <a href="<?php echo $temp->siteURL; ?>">
                                    <img src="<?php echo $temp->siteURL; ?>favicon.svg" class="img-fluid theme-filter" alt="">
                                </a>
                            </div>
                            <div class="mb-5">
                                <h2 class="cta-1 mb-0 text-primary">Bienvenido</h2>
                                <!-- <h2 class="cta-1 text-primary">let's get started!</h2> -->
                            </div>
                            <div class="mb-5">
                                <p class="h6">Por favor, introduzca su usuario y contraseña para iniciar sesión.</p>
                            </div>
                            <div>
                                <form id="loginForm" class="tooltip-end-bottom" novalidate>
                                    <div class="mb-3 filled form-group tooltip-end-top">
                                        <i data-acorn-icon="user"></i>
                                        <input class="form-control" id="inp-name" placeholder="Nombre de usuario" name="usuario" />
                                    </div>
                                    <div class="mb-3 filled form-group tooltip-end-top">
                                        <i data-acorn-icon="lock-off"></i>
                                        <input class="form-control pe-7" id="inp-pass" name="pass" type="password" placeholder="Contraseña" />
                                    </div>
                                </form>
                                <button onclick="iniciarSesion()" class="btn btn-lg btn-primary">Entrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Side End -->
            </div>
        </div>
    </div>
    <?php $temp->scripts() ?>
    <script>
        function iniciarSesion() {
            console.log('Función iniciarSesion ejecutada');
            var nombre = $("#inp-name").val();
            var pass = $("#inp-pass").val();
            console.log('Usuario:', nombre, 'Pass:', pass);

            var formdata = new FormData();
            formdata.append("usuario", nombre);
            formdata.append("pass", pass);

            var requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'follow'
            };

            fetch("<?php echo $temp->siteURL; ?>assets/API/sesion/iniciar.php", requestOptions)
                .then(response => response.json())
                .then(result => {
                    console.log('Respuesta del servidor:', result);
                    if (result['success'] == 1) {
                        // Verificar si es primer login
                        if (result['primer_login'] == 'S') {
                            jQuery.notify({
                                title: 'Primer inicio de sesión',
                                message: 'Por seguridad, debes cambiar tu contraseña'
                            }, {
                                type: 'info',
                                delay: 5000,
                                placement: {
                                    from: 'top',
                                    align: 'right',
                                },
                            });
                            setTimeout(function() {
                                window.location = "<?php echo $temp->siteURL ?>pages/configuracion/primer-login/";
                            }, 2000);
                        } else {
                            jQuery.notify({
                                title: 'Iniciando sesión',
                                message: 'Bienvenido ' + nombre
                            }, {
                                type: 'success',
                                delay: 5000,
                                placement: {
                                    from: 'top',
                                    align: 'right',
                                },
                            });
                            setTimeout(redireccionar, 3000);
                        }
                    } else {
                        jQuery.notify({
                            title: 'Acceso incorrecto',
                            message: 'Usuario y/o contraseña incorrectos, intente nuevamente'
                        }, {
                            type: 'warning',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        });
                    }
                })
                .catch(error => console.log('❌ error:', error));
        }

        function redireccionar() {
            window.location = "<?php echo $temp->siteURL ?>";
        }

        function cerrarSesion() {
            var ruta = "<?php echo $temp->siteURL ?>assets/API/sesion/cerrar/";
            fetch(ruta)
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if (response['success'] == 1) {
                        jQuery.notify({
                            title: 'Cerrando sesión',
                            message: 'Vuelve pronto'
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        });
                    }
                });
        }
    </script>

</body>

</html>