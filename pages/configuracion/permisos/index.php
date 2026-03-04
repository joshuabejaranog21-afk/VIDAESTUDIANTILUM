<?php
include('../../../assets/php/template.php');
$temp = new Template('Gestión de Permisos');
$db = new Conexion();

// Solo SUPERUSUARIO puede acceder
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

if ($temp->usuario_categoria != 1) {
    header('Location: ' . $temp->siteURL);
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
        .modulo-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .modulo-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .permiso-checkbox {
            margin-right: 15px;
        }
        .badge-permiso {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-right: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .badge-permiso.active {
            background: #667eea !important;
            color: white;
        }
        .badge-permiso:hover {
            transform: translateY(-2px);
        }
        .rol-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">
                                <i class="fa fa-shield-alt"></i> <?php echo $temp->titulo ?>
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Permisos</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <button type="button" class="btn btn-success btn-icon btn-icon-start" onclick="guardarPermisos()">
                                <i class="fa fa-save"></i>
                                <span>Guardar Cambios</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <!-- Selector de Rol -->
                    <div class="col-12 mb-4">
                        <div class="rol-selector">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h5 class="mb-0"><i class="fa fa-user-tag"></i> Selecciona un Rol:</h5>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-select form-select-lg" id="selectRol" onchange="cargarPermisos()">
                                        <option value="">-- Selecciona un rol --</option>
                                        <?php
                                        $cad = "SELECT * FROM SYSTEM_CAT_USUARIOS WHERE ACTIVO = 'S' ORDER BY ID";
                                        $sql = $db->query($cad);
                                        if ($db->rows($sql) > 0) {
                                            foreach ($sql as $key) {
                                                echo '<option value="'.$key['ID'].'">'.$key['NOMBRE'].' - '.$key['DESCRIPCION'].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos por Módulo -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fa fa-th-list"></i> Asignar Permisos por Módulo
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="permisosContainer">
                                    <div class="text-center text-muted py-5">
                                        <i class="fa fa-arrow-up" style="font-size: 48px;"></i>
                                        <p class="mt-3 h5">Selecciona un rol para gestionar sus permisos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>

    <?php $temp->scripts() ?>
    <script>
        let permisosActuales = [];

        function cargarPermisos() {
            const idRol = $('#selectRol').val();

            if (!idRol) {
                $('#permisosContainer').html(`
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-arrow-up" style="font-size: 48px;"></i>
                        <p class="mt-3 h5">Selecciona un rol para gestionar sus permisos</p>
                    </div>
                `);
                return;
            }

            fetch(`<?php echo $temp->siteURL ?>assets/API/permisos/obtener-permisos-usuario.php?id_rol=${idRol}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success == 1) {
                        permisosActuales = result.data;
                        renderizarPermisos(result.data);
                    } else {
                        jQuery.notify({
                            title: 'Error',
                            message: 'No se pudieron cargar los permisos'
                        }, {
                            type: 'danger'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function renderizarPermisos(modulos) {
            let html = '';

            modulos.forEach(modulo => {
                html += `
                    <div class="modulo-card">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h5 class="mb-0">
                                    <i class="fa fa-${modulo.icono || 'cube'}"></i>
                                    ${modulo.nombre}
                                </h5>
                                <small class="text-muted">${modulo.descripcion || ''}</small>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex flex-wrap gap-2">
                                    ${modulo.permisos.map(permiso => `
                                        <span class="badge badge-permiso ${permiso.activo ? 'active' : 'bg-light text-dark'}"
                                              onclick="togglePermiso(${modulo.id}, ${permiso.id}, this)">
                                            <i class="fa fa-${getIconoPermiso(permiso.slug)}"></i>
                                            ${permiso.nombre}
                                        </span>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#permisosContainer').html(html);
        }

        function getIconoPermiso(slug) {
            const iconos = {
                'ver': 'eye',
                'crear': 'plus-circle',
                'editar': 'edit',
                'eliminar': 'trash-alt'
            };
            return iconos[slug] || 'check';
        }

        function togglePermiso(idModulo, idPermiso, elemento) {
            $(elemento).toggleClass('active bg-light text-dark');

            // Actualizar el array de permisos actuales
            const modulo = permisosActuales.find(m => m.id == idModulo);
            if (modulo) {
                const permiso = modulo.permisos.find(p => p.id == idPermiso);
                if (permiso) {
                    permiso.activo = !permiso.activo;
                }
            }
        }

        function guardarPermisos() {
            const idRol = $('#selectRol').val();

            if (!idRol) {
                jQuery.notify({
                    title: 'Error',
                    message: 'Selecciona un rol primero'
                }, {
                    type: 'warning'
                });
                return;
            }

            // Construir array de permisos activos
            const permisosActivos = [];
            permisosActuales.forEach(modulo => {
                modulo.permisos.forEach(permiso => {
                    if (permiso.activo) {
                        permisosActivos.push({
                            id_modulo: modulo.id,
                            id_permiso: permiso.id
                        });
                    }
                });
            });

            const formData = new FormData();
            formData.append('id_rol', idRol);
            formData.append('permisos', JSON.stringify(permisosActivos));

            fetch('<?php echo $temp->siteURL ?>assets/API/permisos/actualizar-permisos-usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success == 1) {
                    jQuery.notify({
                        title: '¡Éxito!',
                        message: result.message
                    }, {
                        type: 'success',
                        delay: 5000,
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                } else {
                    jQuery.notify({
                        title: 'Error',
                        message: result.message
                    }, {
                        type: 'danger'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                jQuery.notify({
                    title: 'Error',
                    message: 'Error al guardar los permisos'
                }, {
                    type: 'danger'
                });
            });
        }
    </script>
</body>

</html>
