<?php
include('../../../assets/php/template.php');
$temp = new Template('Usuarios');
$db = new Conexion();
if (!$temp->validate_session(2)) {
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
                                <i class="fa fa-users"></i> <?php echo $temp->titulo ?>
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#addEditModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined">
                                    <path d="M10 17 10 3M3 10 17 10"></path>
                                </svg>
                                <span>Nuevo Usuario</span>
                            </button>
                        </div>
                        <!-- Title End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="contentTable"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addEditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fa fa-user-plus"></i> Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" id="form-crear">
                        <div class="mb-3">
                            <label class="form-label">Nombre del usuario *</label>
                            <input name="nombre" type="text" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input name="pass" type="password" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nivel de usuario *</label>
                            <select class="form-select" name="id_cat" required>
                                <option value="">Selecciona un nivel...</option>
                                <?php
                                $cad = "SELECT * FROM SYSTEM_CAT_USUARIOS";
                                $sql = $db->query($cad);
                                if ($db->rows($sql) > 0) {
                                    foreach ($sql as $key) {
                                        print <<<SILVER
                                        <option value="{$key['ID']}">{$key['NOMBRE']}</option>
                                        SILVER;
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnCrear" onclick="crear()">
                        <i class="fa fa-save"></i> Crear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="modalMod" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-edit"></i> Modificar Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" id="form-modificar">
                        <input type="hidden" id="mod-id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nombre del usuario *</label>
                            <input name="nombre" id="mod-nombre" type="text" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nivel de usuario *</label>
                            <select class="form-select" name="id_cat" id="mod-id_cat" required>
                                <option value="">Selecciona un nivel...</option>
                                <?php
                                $cad = "SELECT * FROM SYSTEM_CAT_USUARIOS";
                                $sql = $db->query($cad);
                                if ($db->rows($sql) > 0) {
                                    foreach ($sql as $key) {
                                        print <<<SILVER
                                        <option value="{$key['ID']}">{$key['NOMBRE']}</option>
                                        SILVER;
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="modificar()">
                        <i class="fa fa-save"></i> Modificar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <!-- Search Modal End -->

    <?php $temp->scripts() ?>
    <script>
        function leer() {
            var ruta = "tabla.php";
            var contentTable = $('#contentTable');
            //GET
            fetch(ruta)
                .then(response => response.text())
                .then(response => contentTable.html(response));
        }

        function crear() {
            var ruta = '/cpanel/assets/API/usuarios/crear/';
            const data = new FormData(document.getElementById('form-crear'));
            // POST
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
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        }, );
                        resetform();
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

        function initModificar(id, nombre, id_cat) {
            $('#mod-id').val(id);
            $('#mod-nombre').val(nombre);
            $('#mod-id_cat').val(id_cat);
        }

        function modificar() {
            var ruta = '/cpanel/assets/API/usuarios/actualizar/';
            const data = new FormData(document.getElementById('form-modificar'));
            // POST
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
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        }, );
                        leer();
                        resetform();
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

        function borrar(id) {
            var ruta = '/cpanel/assets/API/usuarios/borrar/' + id;
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

        function usuarioActivo(id, activo) {
            var formdata = new FormData();
            formdata.append("id", id);
            formdata.append("activo", activo);

            var requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'follow'
            };

            fetch("/cpanel/assets/API/usuarios/activo/", requestOptions)
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

        function resetPassword(id) {
            var formdata = new FormData();
            formdata.append("id", id);
            formdata.append("pass", '1234');

            var requestOptions = {
                method: 'POST',
                body: formdata,
                redirect: 'follow'
            };

            fetch("/cpanel/assets/API/usuarios/password/", requestOptions)
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

        function resetform() {
            $("form select").each(function() {
                this.selectedIndex = 0
            });
            $("form input[type=text], form input[type=number], form textarea").each(function() {
                this.value = ''
            });
        }
        window.addEventListener('ready', leer());
    </script>
</body>

</html>