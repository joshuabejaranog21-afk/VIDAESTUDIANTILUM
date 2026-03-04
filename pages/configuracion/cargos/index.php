<?php
session_start();
include('../../../assets/php/template.php');
$temp = new Template('Gestión de Cargos');

// Solo admins pueden acceder
if (!$temp->validate_session()) {
    header('Location: ../../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

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
                <!-- Title Start -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">
                                <i class="fa fa-briefcase"></i> Gestión de Cargos
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cargos</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="showAddModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined">
                                    <path d="M10 17 10 3M3 10 17 10"></path>
                                </svg>
                                <span>Nuevo Cargo</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title End -->

                <!-- Info Alert -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Gestión de Cargos:</strong> Define los cargos que se usarán en Federación, Equipo Pulso y otros módulos.
                        </div>
                    </div>
                </div>

                <!-- Cargos Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fa fa-list"></i> Lista de Cargos
                                </h5>
                            </div>
                            <div class="card-body">
                                <table id="tablaCargos" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>Tipo</th>
                                            <th>Orden</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Se llena con AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="cargoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cargoModalTitle">
                        <i class="fa fa-briefcase"></i> Nuevo Cargo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="cargoForm">
                        <input type="hidden" id="cargoId" name="id">

                        <div class="mb-3">
                            <label class="form-label">Nombre del Cargo *</label>
                            <input type="text" class="form-control" id="cargoNombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="cargoDescripcion" name="descripcion" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select class="form-select" id="cargoTipo" name="tipo" required>
                                    <option value="GENERAL">General (Todos)</option>
                                    <option value="FEDERACION">Federación</option>
                                    <option value="PULSO">Equipo Pulso</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" class="form-control" id="cargoOrden" name="orden" value="0" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="cargoActivo" name="activo">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveCargo()">
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
        const apiUrl = "<?php echo $temp->siteURL ?>assets/API/configuracion/cargos/";
        let dataTable;

        // Initialize DataTable
        function initDataTable() {
            if (dataTable) {
                dataTable.destroy();
            }

            dataTable = $('#tablaCargos').DataTable({
                ajax: {
                    url: apiUrl + 'listar.php',
                    dataSrc: function(response) {
                        if (response.success) {
                            return response.data;
                        } else {
                            console.error('Error:', response.message);
                            return [];
                        }
                    }
                },
                columns: [
                    { data: 'ID' },
                    { data: 'NOMBRE' },
                    { data: 'DESCRIPCION' },
                    {
                        data: 'TIPO',
                        render: function(data) {
                            const badges = {
                                'GENERAL': '<span class="badge bg-primary">General</span>',
                                'FEDERACION': '<span class="badge bg-success">Federación</span>',
                                'PULSO': '<span class="badge bg-info">Pulso</span>'
                            };
                            return badges[data] || data;
                        }
                    },
                    { data: 'ORDEN' },
                    {
                        data: 'ACTIVO',
                        render: function(data) {
                            return data === 'S'
                                ? '<span class="badge bg-success">Activo</span>'
                                : '<span class="badge bg-secondary">Inactivo</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-warning" onclick="editCargo(${row.ID})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCargo(${row.ID}, '${row.NOMBRE}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                order: [[4, 'asc'], [0, 'desc']] // Ordenar por ORDEN y luego por ID
            });
        }

        // Show add modal
        function showAddModal() {
            $('#cargoModalTitle').html('<i class="fa fa-briefcase"></i> Nuevo Cargo');
            $('#cargoForm')[0].reset();
            $('#cargoId').val('');
            $('#cargoModal').modal('show');
        }

        // Edit cargo
        function editCargo(id) {
            $.ajax({
                url: apiUrl + 'obtener.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#cargoModalTitle').html('<i class="fa fa-edit"></i> Editar Cargo');
                        $('#cargoId').val(response.data.ID);
                        $('#cargoNombre').val(response.data.NOMBRE);
                        $('#cargoDescripcion').val(response.data.DESCRIPCION);
                        $('#cargoTipo').val(response.data.TIPO);
                        $('#cargoOrden').val(response.data.ORDEN);
                        $('#cargoActivo').val(response.data.ACTIVO);
                        $('#cargoModal').modal('show');
                    } else {
                        alert('Error al cargar el cargo: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error de conexión al cargar el cargo');
                }
            });
        }

        // Save cargo
        function saveCargo() {
            const formData = {
                id: $('#cargoId').val(),
                nombre: $('#cargoNombre').val(),
                descripcion: $('#cargoDescripcion').val(),
                tipo: $('#cargoTipo').val(),
                orden: $('#cargoOrden').val(),
                activo: $('#cargoActivo').val()
            };

            const url = formData.id ? apiUrl + 'actualizar.php' : apiUrl + 'crear.php';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#cargoModal').modal('hide');
                        dataTable.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error de conexión al guardar el cargo');
                }
            });
        }

        // Delete cargo
        function deleteCargo(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar el cargo "${nombre}"?\n\nNOTA: Los colaboradores asignados a este cargo no se eliminarán, solo perderán la referencia al cargo.`)) {
                return;
            }

            $.ajax({
                url: apiUrl + 'eliminar.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        dataTable.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error de conexión al eliminar el cargo');
                }
            });
        }

        // Initialize on load
        $(document).ready(function() {
            initDataTable();
        });
    </script>
</body>

</html>
