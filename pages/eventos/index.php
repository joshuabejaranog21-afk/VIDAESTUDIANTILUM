<?php
include('../../assets/php/template.php');
$temp = new Template('Eventos');
$db = new Conexion();

if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

if (!$temp->tiene_permiso('eventos', 'ver')) {
    echo "No tienes permiso";
    exit();
}

$puede_crear = $temp->tiene_permiso('eventos', 'crear');
$puede_editar = $temp->tiene_permiso('eventos', 'editar');
$puede_eliminar = $temp->tiene_permiso('eventos', 'eliminar');
$puede_ver_galeria = $temp->tiene_permiso('galeria', 'ver');
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"attributes": {"placement": "vertical"}}'>
<head>
    <?php $temp->head() ?>
</head>
<body>
    <div id="root">
        <?php $temp->nav() ?>
        <main>
            <div class="container">
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active">Eventos</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end gap-2">
                            <?php if($puede_ver_galeria): ?>
                            <a href="<?php echo $temp->siteURL ?>pages/galeria/?modulo=eventos" class="btn btn-outline-info btn-icon btn-icon-start">
                                <i data-acorn-icon="image"></i>
                                <span>Gestionar Imágenes</span>
                            </a>
                            <?php endif; ?>
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nuevo Evento</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card">
                            <div class="card-body">
                                <table id="tablaEventos" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Fecha</th>
                                            <th>Lugar</th>
                                            <th>Organizador</th>
                                            <th>Estado</th>
                                            <th>Destacado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modalEvento" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEventoTitle">Nuevo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEvento">
                    <div class="modal-body">
                        <input type="hidden" id="eventoId">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" id="eventoTitulo" name="titulo" class="form-control" required maxlength="300">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado *</label>
                                <select id="eventoEstado" name="estado" class="form-select">
                                    <option value="PROXIMO">Próximo</option>
                                    <option value="EN_CURSO">En Curso</option>
                                    <option value="FINALIZADO">Finalizado</option>
                                    <option value="CANCELADO">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea id="eventoDescripcion" name="descripcion" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y Hora Inicio</label>
                                <input type="datetime-local" id="eventoFechaEvento" name="fecha_evento" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y Hora Fin</label>
                                <input type="datetime-local" id="eventoFechaFin" name="fecha_fin" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lugar</label>
                                <input type="text" id="eventoLugar" name="lugar" class="form-control" maxlength="200">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Organizador</label>
                                <select id="eventoOrganizador" name="organizador" class="form-select">
                                    <option value="FEDERACION">Federación Estudiantil</option>
                                    <option value="CULTURALES">Comisión de Culturales</option>
                                    <option value="DEPORTIVO">Departamento Deportivo</option>
                                    <option value="ESPIRITUAL">Ministerio</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Destacado</label>
                                <select id="eventoDestacado" name="destacado" class="form-select">
                                    <option value="N">No</option>
                                    <option value="S">Sí</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Activo</label>
                                <select id="eventoActivo" name="activo" class="form-select">
                                    <option value="S">Activo</option>
                                    <option value="N">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span id="btnTexto">Crear Evento</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;

        let modalEvento;

        function abrirFormularioCrear() {
            document.getElementById('formEvento').reset();
            document.getElementById('eventoId').value = '';
            document.getElementById('modalEventoTitle').textContent = 'Nuevo Evento';
            document.getElementById('btnTexto').textContent = 'Crear Evento';
            modalEvento.show();
        }

        function editarEvento(id) {
            fetch(siteURL + 'assets/API/eventos/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const evt = data.data;
                        document.getElementById('eventoId').value = evt.ID;
                        document.getElementById('eventoTitulo').value = evt.TITULO;
                        document.getElementById('eventoDescripcion').value = evt.DESCRIPCION || '';
                        document.getElementById('eventoFechaEvento').value = evt.FECHA_EVENTO ? evt.FECHA_EVENTO.replace(' ', 'T').substring(0, 16) : '';
                        document.getElementById('eventoFechaFin').value = evt.FECHA_FIN ? evt.FECHA_FIN.replace(' ', 'T').substring(0, 16) : '';
                        document.getElementById('eventoLugar').value = evt.LUGAR || '';
                        document.getElementById('eventoOrganizador').value = evt.ORGANIZADOR;
                        document.getElementById('eventoEstado').value = evt.ESTADO;
                        document.getElementById('eventoDestacado').value = evt.DESTACADO;
                        document.getElementById('eventoActivo').value = evt.ACTIVO;

                        document.getElementById('modalEventoTitle').textContent = 'Editar: ' + evt.TITULO;
                        document.getElementById('btnTexto').textContent = 'Actualizar Evento';
                        modalEvento.show();
                    }
                });
        }

        document.getElementById('formEvento').addEventListener('submit', function(e) {
            e.preventDefault();

            const eventoId = document.getElementById('eventoId').value;
            const formData = new FormData(this);

            let url;
            if (eventoId) {
                formData.append('id', eventoId);
                url = siteURL + 'assets/API/eventos/editar.php';
            } else {
                url = siteURL + 'assets/API/eventos/crear.php';
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    jQuery.notify({title: 'Éxito', message: data.message}, {type: 'success'});
                    modalEvento.hide();
                    cargarEventos();
                } else {
                    jQuery.notify({title: 'Error', message: data.message}, {type: 'danger'});
                }
            });
        });

        function eliminarEvento(id, titulo) {
            if (!confirm(`¿Eliminar "${titulo}"?`)) return;

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/eventos/eliminar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    jQuery.notify({title: 'Éxito', message: data.message}, {type: 'success'});
                    cargarEventos();
                } else {
                    jQuery.notify({title: 'Error', message: data.message}, {type: 'danger'});
                }
            });
        }

        function cargarEventos() {
            fetch(siteURL + 'assets/API/eventos/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        if ($.fn.DataTable.isDataTable('#tablaEventos')) {
                            $('#tablaEventos').DataTable().clear().destroy();
                        }

                        const tbody = document.querySelector('#tablaEventos tbody');
                        let htmlRows = '';

                        data.data.forEach(evt => {
                            const estadoLabels = {
                                'PROXIMO': '<span class="badge bg-primary">Próximo</span>',
                                'EN_CURSO': '<span class="badge bg-success">En Curso</span>',
                                'FINALIZADO': '<span class="badge bg-secondary">Finalizado</span>',
                                'CANCELADO': '<span class="badge bg-danger">Cancelado</span>'
                            };

                            htmlRows += `
                                <tr>
                                    <td>${evt.ID}</td>
                                    <td>
                                        ${evt.IMAGEN_PRINCIPAL ? `<img src="${evt.IMAGEN_PRINCIPAL}" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;">` : ''}
                                        <strong>${evt.TITULO}</strong>
                                    </td>
                                    <td>${evt.FECHA_EVENTO ? new Date(evt.FECHA_EVENTO).toLocaleDateString() : '-'}</td>
                                    <td>${evt.LUGAR || '-'}</td>
                                    <td>${evt.ORGANIZADOR}</td>
                                    <td>${estadoLabels[evt.ESTADO] || evt.ESTADO}</td>
                                    <td><span class="badge ${evt.DESTACADO == 'S' ? 'bg-warning' : 'bg-secondary'}">${evt.DESTACADO == 'S' ? 'Destacado' : 'Normal'}</span></td>
                                    <td class="text-center">
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarEvento(${evt.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="eliminarEvento(${evt.ID}, '${evt.TITULO.replace(/'/g, "\\'")}')">
                                            <i class="fa fa-trash"></i>
                                            <span class="d-none d-xxl-inline-block">Eliminar</span>
                                        </button>` : ''}
                                    </td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = htmlRows;

                        $('#tablaEventos').DataTable({
                            language: {url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'},
                            order: [[0, 'desc']],
                            pageLength: 25
                        });

                        if (typeof acorn !== 'undefined') acorn.icons();
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            modalEvento = new bootstrap.Modal(document.getElementById('modalEvento'));
            cargarEventos();
        });
    </script>
</body>
</html>
