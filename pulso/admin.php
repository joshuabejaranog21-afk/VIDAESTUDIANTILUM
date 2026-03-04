<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include('../assets/php/template.php');
$temp = new Template('Administrar Equipo - Pulso');
$db = new Conexion();
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }, "showSettings":true}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>assets/css/vendor/datatables.min.css" />
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>pulso/pulso.css" />
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
                            <h1 class="mb-0 pb-0 display-4" id="title"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pulso/">Pulso</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Administrar</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <button type="button" class="btn btn-primary" id="btnNuevo">
                                <i class="cs-plus"></i> Nuevo Colaborador
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Tabla de Colaboradores Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-5">
                            <div class="card-body">
                                <table id="tablaColaboradores" class="data-table table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Cargo</th>
                                            <th>Año</th>
                                            <th>Periodo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tabla de Colaboradores End -->
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>

    <!-- Modal Formulario Start -->
    <div class="modal fade" id="modalColaborador" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formColaborador">
                        <input type="hidden" id="colaboradorId" name="id">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cargo" class="form-label">Cargo *</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="anio" class="form-label">Año *</label>
                                <input type="number" class="form-control" id="anio" name="anio" min="2000" max="2100" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="periodo" class="form-label">Periodo</label>
                            <input type="text" class="form-control" id="periodo" name="periodo"
                                   placeholder="Ej: Enero - Diciembre 2024">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fotografía</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fotoTipo" id="fotoTipoArchivo" value="archivo" checked>
                                    <label class="form-check-label" for="fotoTipoArchivo">
                                        Subir archivo
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="fotoTipo" id="fotoTipoUrl" value="url">
                                    <label class="form-check-label" for="fotoTipoUrl">
                                        Usar URL
                                    </label>
                                </div>
                            </div>

                            <!-- Subir archivo -->
                            <div id="fotoArchivoContainer">
                                <input type="file" class="form-control" id="foto_file" accept="image/*">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB</small>
                                <div id="previewContainer" class="mt-2" style="display: none;">
                                    <img id="fotoPreview" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>

                            <!-- URL -->
                            <div id="fotoUrlContainer" style="display: none;">
                                <input type="url" class="form-control" id="foto_url" name="foto_url"
                                       placeholder="https://ejemplo.com/foto.jpg">
                            </div>

                            <input type="hidden" id="foto_url_hidden" name="foto_url_final">
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografía</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="activo" class="form-label">Estado</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                            <small class="form-text text-muted">Nota: El orden de visualización es automático por ID</small>
                        </div>
                        <input type="hidden" id="orden" name="orden" value="0">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Formulario End -->

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>
    <script src="<?php echo $temp->siteURL ?>assets/js/vendor/datatables.min.js"></script>

    <script>
        let tabla;
        const modal = new bootstrap.Modal(document.getElementById('modalColaborador'));

        // Inicializar DataTable
        function inicializarTabla() {
            tabla = $('#tablaColaboradores').DataTable({
                ajax: {
                    url: '<?php echo $temp->siteURL ?>assets/API/pulso/leer.php',
                    dataSrc: function(json) {
                        return json.success === 1 ? json.data : [];
                    }
                },
                columns: [
                    { data: 'ID', width: '50px' },
                    {
                        data: 'FOTO_URL',
                        width: '80px',
                        orderable: false,
                        render: function(data, type, row) {
                            let fotoUrl;
                            if (data) {
                                if (data.startsWith('http')) {
                                    fotoUrl = data;
                                } else {
                                    fotoUrl = '<?php echo $temp->siteURL ?>' + data;
                                }
                            } else {
                                fotoUrl = '<?php echo $temp->siteURL ?>pulso/default-avatar.svg';
                            }
                            return `<img src="${fotoUrl}" alt="${row.NOMBRE}"
                                    class="rounded-circle" style="width:50px;height:50px;object-fit:cover;"
                                    onerror="this.src='<?php echo $temp->siteURL ?>pulso/default-avatar.svg'">`;
                        }
                    },
                    { data: 'NOMBRE' },
                    { data: 'CARGO' },
                    { data: 'ANIO', width: '80px' },
                    { data: 'PERIODO' },
                    {
                        data: 'ACTIVO',
                        width: '100px',
                        render: function(data) {
                            return data === 'S'
                                ? '<span class="badge bg-success">Activo</span>'
                                : '<span class="badge bg-secondary">Inactivo</span>';
                        }
                    },
                    {
                        data: null,
                        width: '180px',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-info btn-editar" data-id="${row.ID}" title="Editar">
                                    <i class="cs-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.ID}" title="Eliminar">
                                    <i class="cs-bin"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [[4, 'desc'], [2, 'asc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        }

        // Alternar entre archivo y URL
        document.querySelectorAll('input[name="fotoTipo"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const isArchivo = e.target.value === 'archivo';
                document.getElementById('fotoArchivoContainer').style.display = isArchivo ? 'block' : 'none';
                document.getElementById('fotoUrlContainer').style.display = isArchivo ? 'none' : 'block';
            });
        });

        // Vista previa de imagen
        document.getElementById('foto_file').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('fotoPreview').src = e.target.result;
                    document.getElementById('previewContainer').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('previewContainer').style.display = 'none';
            }
        });

        // Nuevo colaborador
        document.getElementById('btnNuevo').addEventListener('click', () => {
            document.getElementById('formColaborador').reset();
            document.getElementById('colaboradorId').value = '';
            document.getElementById('modalTitle').textContent = 'Nuevo Colaborador';
            document.getElementById('anio').value = new Date().getFullYear();
            document.getElementById('foto_url_hidden').value = '';
            document.getElementById('previewContainer').style.display = 'none';
            document.getElementById('fotoTipoArchivo').checked = true;
            document.getElementById('fotoArchivoContainer').style.display = 'block';
            document.getElementById('fotoUrlContainer').style.display = 'none';
            modal.show();
        });

        // Guardar colaborador
        document.getElementById('btnGuardar').addEventListener('click', async () => {
            const form = document.getElementById('formColaborador');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const fotoTipo = document.querySelector('input[name="fotoTipo"]:checked').value;
            let fotoUrl = '';

            try {
                // Si es archivo, primero subir la imagen
                if (fotoTipo === 'archivo') {
                    const fotoFile = document.getElementById('foto_file').files[0];

                    if (fotoFile) {
                        const uploadFormData = new FormData();
                        uploadFormData.append('foto', fotoFile);

                        const uploadResponse = await fetch('<?php echo $temp->siteURL ?>assets/API/pulso/upload_foto.php', {
                            method: 'POST',
                            body: uploadFormData
                        });

                        const uploadResult = await uploadResponse.json();

                        if (uploadResult.success === 1) {
                            fotoUrl = uploadResult.url;
                        } else {
                            mostrarNotificacion('error', 'Error al subir la foto: ' + uploadResult.message);
                            return;
                        }
                    } else {
                        // Si está editando y no seleccionó archivo nuevo, mantener la URL existente
                        fotoUrl = document.getElementById('foto_url_hidden').value || '';
                    }
                } else {
                    // Si es URL, obtener el valor del input
                    fotoUrl = document.getElementById('foto_url').value || '';
                }

                // Preparar datos del colaborador
                const data = {
                    id: document.getElementById('colaboradorId').value,
                    nombre: document.getElementById('nombre').value,
                    cargo: document.getElementById('cargo').value,
                    anio: parseInt(document.getElementById('anio').value),
                    periodo: document.getElementById('periodo').value,
                    foto_url: fotoUrl,
                    bio: document.getElementById('bio').value,
                    orden: parseInt(document.getElementById('orden').value || 0),
                    activo: document.getElementById('activo').value
                };

                const id = document.getElementById('colaboradorId').value;
                const url = id
                    ? '<?php echo $temp->siteURL ?>assets/API/pulso/actualizar.php'
                    : '<?php echo $temp->siteURL ?>assets/API/pulso/crear.php';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success === 1) {
                    modal.hide();
                    tabla.ajax.reload();
                    mostrarNotificacion('success', result.message);
                } else {
                    mostrarNotificacion('error', result.message);
                }
            } catch (error) {
                mostrarNotificacion('error', 'Error al guardar el colaborador');
                console.error(error);
            }
        });

        // Editar colaborador
        $(document).on('click', '.btn-editar', async function() {
            const id = $(this).data('id');
            const rowData = tabla.rows().data().toArray().find(row => row.ID == id);

            if (rowData) {
                // Limpiar formulario
                document.getElementById('formColaborador').reset();
                document.getElementById('previewContainer').style.display = 'none';

                // Llenar datos
                document.getElementById('colaboradorId').value = rowData.ID;
                document.getElementById('nombre').value = rowData.NOMBRE;
                document.getElementById('cargo').value = rowData.CARGO;
                document.getElementById('anio').value = rowData.ANIO;
                document.getElementById('periodo').value = rowData.PERIODO || '';
                document.getElementById('bio').value = rowData.BIO || '';
                document.getElementById('orden').value = rowData.ORDEN || 0;
                document.getElementById('activo').value = rowData.ACTIVO;

                // Manejar foto existente
                const fotoUrl = rowData.FOTO_URL || '';
                document.getElementById('foto_url_hidden').value = fotoUrl;

                if (fotoUrl) {
                    // Si tiene foto, mostrar opción URL y llenar el campo
                    document.getElementById('fotoTipoUrl').checked = true;
                    document.getElementById('foto_url').value = fotoUrl;
                    document.getElementById('fotoArchivoContainer').style.display = 'none';
                    document.getElementById('fotoUrlContainer').style.display = 'block';

                    // Mostrar preview si es posible
                    document.getElementById('fotoPreview').src = '<?php echo $temp->siteURL ?>' + fotoUrl;
                    document.getElementById('previewContainer').style.display = 'block';
                } else {
                    // Si no tiene foto, dejar en modo archivo
                    document.getElementById('fotoTipoArchivo').checked = true;
                    document.getElementById('fotoArchivoContainer').style.display = 'block';
                    document.getElementById('fotoUrlContainer').style.display = 'none';
                }

                document.getElementById('modalTitle').textContent = 'Editar Colaborador';
                modal.show();
            }
        });

        // Eliminar colaborador
        $(document).on('click', '.btn-eliminar', async function() {
            const id = $(this).data('id');

            if (!confirm('¿Está seguro de eliminar este colaborador?')) {
                return;
            }

            try {
                const response = await fetch('<?php echo $temp->siteURL ?>assets/API/pulso/borrar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const result = await response.json();

                if (result.success === 1) {
                    tabla.ajax.reload();
                    mostrarNotificacion('success', result.message);
                } else {
                    mostrarNotificacion('error', result.message);
                }
            } catch (error) {
                mostrarNotificacion('error', 'Error al eliminar el colaborador');
                console.error(error);
            }
        });

        // Función para mostrar notificaciones
        function mostrarNotificacion(tipo, mensaje) {
            const iconos = {
                success: 'cs-check',
                error: 'cs-close',
                warning: 'cs-warning'
            };

            $.notify({
                icon: iconos[tipo],
                message: mensaje
            }, {
                type: tipo === 'success' ? 'success' : 'danger',
                placement: {
                    from: 'top',
                    align: 'right'
                },
                delay: 3000
            });
        }

        // Inicializar
        window.addEventListener('DOMContentLoaded', () => {
            inicializarTabla();
        });
    </script>
</body>

</html>
