<?php
session_start();
include('../../assets/php/template.php');
$temp = new Template('Administrar Equipo - Pulso');
if (!$temp->validate_session(2)) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>assets/css/vendor/datatables.min.css" />
    <style>
        .member-photo-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f5f5f5;
        }

        .member-photo {
            max-width: 200px;
            border-radius: 8px;
        }

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
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">Administrar Equipo Pulso</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/pulso/">Pulso</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Administrar</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 text-end">
                            <button class="btn btn-outline-primary" onclick="showAddModal()">
                                <i class="fa fa-plus"></i> Nuevo Colaborador
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title End -->

                <!-- Members Table Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="tableContainer">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="mt-3">Cargando equipo...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Members Table End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="memberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fa fa-user-plus"></i> Nuevo Colaborador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="memberForm" enctype="multipart/form-data">
                        <input type="hidden" id="memberId" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo *</label>
                                <select class="form-select" name="id_cargo" id="cargoSelect" required>
                                    <option value="">Selecciona un cargo...</option>
                                </select>
                                <small class="text-muted">Si no encuentras el cargo, agrégalo en <a href="<?php echo $temp->siteURL ?>pages/configuracion/cargos/" target="_blank">Gestión de Cargos</a></small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Año *</label>
                                <input type="number" class="form-control" name="anio" required
                                       min="2000" max="2100" value="<?php echo date('Y'); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Periodo</label>
                                <input type="text" class="form-control" name="periodo"
                                       placeholder="Ej: Enero-Diciembre 2025">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control" name="orden" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Biografía</label>
                            <textarea class="form-control" name="bio" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fotografía (JPG, PNG - Máx. 2MB)</label>
                            <input type="file" class="form-control" name="foto" accept="image/*"
                                   onchange="previewImage(event)">
                            <div id="currentPhoto" style="display:none;" class="mt-2">
                                <p class="text-muted">Foto actual:</p>
                                <img id="currentPhotoImg" src="" class="member-photo">
                            </div>
                            <div id="photoPreview" style="display:none;" class="mt-2">
                                <p class="text-muted">Vista previa:</p>
                                <img id="previewImg" src="" class="member-photo">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL de Flickr (Opcional)</label>
                            <input type="url" class="form-control" name="flickr_url" id="flickrUrl"
                                   placeholder="https://live.staticflickr.com/...">
                            <small class="text-muted">
                                <i class="fab fa-flickr"></i> Si prefieres usar una foto de Flickr, pega aquí la URL directa de la imagen.
                                Si se proporciona, se usará en lugar de la foto subida localmente.
                            </small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="activo" value="S"
                                   id="activoCheck" checked>
                            <label class="form-check-label" for="activoCheck">
                                Activo
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveMember()">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const apiUrl = "<?php echo $temp->siteURL ?>assets/API/pulso/";

        // Load cargos for dropdown
        function loadCargos() {
            fetch("<?php echo $temp->siteURL ?>assets/API/configuracion/cargos/listar.php")
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const select = $('#cargoSelect');
                        select.html('<option value="">Selecciona un cargo...</option>');
                        // Filtrar solo cargos activos de PULSO o GENERAL
                        const cargosPulso = data.data.filter(c =>
                            c.ACTIVO === 'S' && (c.TIPO === 'PULSO' || c.TIPO === 'GENERAL')
                        );
                        cargosPulso.forEach(cargo => {
                            select.append(`<option value="${cargo.ID}">${cargo.NOMBRE}</option>`);
                        });
                    }
                });
        }

        // Load table
        function loadTable() {
            fetch(apiUrl + 'listar.php')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.miembros.length > 0) {
                        renderTable(data.miembros);
                    } else {
                        $('#tableContainer').html(`
                            <div class="text-center py-5">
                                <i class="fa fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay colaboradores registrados</p>
                            </div>
                        `);
                    }
                });
        }

        // Render table
        function renderTable(members) {
            let html = `
                <table id="datatableRows" class="data-table nowrap hover">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Año</th>
                            <th>Periodo</th>
                            <th>Estado</th>
                            <th class="empty">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            members.forEach(member => {
                const fotoUrl = member.FLICKR_URL || member.FOTO_URL || '<?php echo $temp->siteURL ?>assets/img/profile/profile-1.webp';
                const estadoBadge = member.ACTIVO === 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';

                html += `
                    <tr>
                        <td>
                            <img src="${fotoUrl}" class="member-photo-thumb" alt="${member.NOMBRE}">
                        </td>
                        <td><strong>${member.NOMBRE}</strong></td>
                        <td><span class="badge bg-primary">${member.CARGO}</span></td>
                        <td>${member.ANIO}</td>
                        <td>${member.PERIODO || '-'}</td>
                        <td>${estadoBadge}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="viewMember(${member.ID})">
                                <i class="fa fa-eye"></i>
                                <span class="d-none d-xxl-inline-block">Ver</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editMember(${member.ID})">
                                <i class="fa fa-edit"></i>
                                <span class="d-none d-xxl-inline-block">Editar</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="deleteMember(${member.ID}, '${member.NOMBRE.replace(/'/g, "\\'")}')">
                                <i class="fa fa-trash"></i>
                                <span class="d-none d-xxl-inline-block">Eliminar</span>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            $('#tableContainer').html(html);

            // Initialize DataTable
            $('#datatableRows').DataTable({
                "order": [[3, 'desc']], // Ordenar por año descendente
                buttons: ['copy', 'excel', 'csv', 'print'],
                "pagingType": "full_numbers",
                "lengthMenu": [[10, 20, -1], [10, 20, "Todos"]],
                responsive: true,
                language: {
                    "decimal": "",
                    "emptyTable": "Sin datos disponibles",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ campos",
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

        // Show add modal
        function showAddModal() {
            $('#modalTitle').html('<i class="fa fa-user-plus"></i> Nuevo Colaborador');
            $('#memberForm')[0].reset();
            $('#memberId').val('');
            $('#currentPhoto').hide();
            $('#photoPreview').hide();
            $('#activoCheck').prop('checked', true);

            // Habilitar todos los campos
            $('#memberForm input, #memberForm select, #memberForm textarea').prop('disabled', false);
            $('.modal-footer .btn-primary').show();

            $('#memberModal').modal('show');
        }

        // View member
        function viewMember(id) {
            fetch(apiUrl + 'obtener.php?id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const m = data.miembro;
                        $('#modalTitle').html('<i class="fa fa-eye"></i> Ver Colaborador');
                        $('#memberId').val(m.ID);
                        $('[name="nombre"]').val(m.NOMBRE);
                        $('#cargoSelect').val(m.ID_CARGO || '');
                        $('[name="anio"]').val(m.ANIO);
                        $('[name="periodo"]').val(m.PERIODO);
                        $('[name="bio"]').val(m.BIO);
                        $('[name="orden"]').val(m.ORDEN);
                        $('#flickrUrl').val(m.FLICKR_URL);
                        $('#activoCheck').prop('checked', m.ACTIVO === 'S');

                        if (m.FOTO_URL || m.FLICKR_URL) {
                            const fotoUrl = m.FLICKR_URL || m.FOTO_URL;
                            $('#currentPhotoImg').attr('src', fotoUrl);
                            $('#currentPhoto').show();
                        }

                        $('#photoPreview').hide();

                        // Deshabilitar todos los campos para solo lectura
                        $('#memberForm input, #memberForm select, #memberForm textarea').prop('disabled', true);
                        $('.modal-footer .btn-primary').hide();

                        $('#memberModal').modal('show');
                    } else {
                        alert('Error al cargar el colaborador');
                    }
                });
        }

        // Edit member
        function editMember(id) {
            fetch(apiUrl + 'obtener.php?id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const m = data.miembro;
                        $('#modalTitle').html('<i class="fa fa-edit"></i> Editar Colaborador');
                        $('#memberId').val(m.ID);
                        $('[name="nombre"]').val(m.NOMBRE);
                        $('#cargoSelect').val(m.ID_CARGO || '');
                        $('[name="anio"]').val(m.ANIO);
                        $('[name="periodo"]').val(m.PERIODO);
                        $('[name="bio"]').val(m.BIO);
                        $('[name="orden"]').val(m.ORDEN);
                        $('#flickrUrl').val(m.FLICKR_URL);
                        $('#activoCheck').prop('checked', m.ACTIVO === 'S');

                        if (m.FOTO_URL || m.FLICKR_URL) {
                            const fotoUrl = m.FLICKR_URL || m.FOTO_URL;
                            $('#currentPhotoImg').attr('src', fotoUrl);
                            $('#currentPhoto').show();
                        }

                        $('#photoPreview').hide();

                        // Habilitar todos los campos para edición
                        $('#memberForm input, #memberForm select, #memberForm textarea').prop('disabled', false);
                        $('.modal-footer .btn-primary').show();

                        $('#memberModal').modal('show');
                    } else {
                        alert('Error al cargar el colaborador');
                    }
                });
        }

        // Save member
        function saveMember() {
            const formData = new FormData($('#memberForm')[0]);

            const url = $('#memberId').val() ? apiUrl + 'actualizar.php' : apiUrl + 'crear.php';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#memberModal').modal('hide');
                        loadTable();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        }

        // Delete member
        function deleteMember(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar a ${nombre}?`)) {
                return;
            }

            $.post(apiUrl + 'eliminar.php', { id: id }, function(response) {
                if (response.success) {
                    alert(response.message);
                    loadTable();
                } else {
                    alert('Error: ' + response.message);
                }
            }, 'json');
        }

        // Preview image
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#photoPreview').show();
                }
                reader.readAsDataURL(file);
            }
        }

        // Initialize
        $(document).ready(function() {
            loadTable();
            loadCargos();
        });
    </script>
</body>
</html>
