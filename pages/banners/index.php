<?php
include('../../assets/php/template.php');
$temp = new Template('Banners');
$db = new Conexion();

if (!$temp->validate_session() || !$temp->tiene_permiso('banners', 'ver')) {
    echo "No tienes permiso";
    exit();
}

$puede_crear = $temp->tiene_permiso('banners', 'crear');
$puede_editar = $temp->tiene_permiso('banners', 'editar');
$puede_eliminar = $temp->tiene_permiso('banners', 'eliminar');
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
                                    <li class="breadcrumb-item active">Banners</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start" onclick="abrirFormularioCrear()">
                                <i data-acorn-icon="plus"></i>
                                <span>Nuevo Banner</span>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card">
                            <div class="card-body">
                                <table id="tablaBanners" class="data-table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Banner</th>
                                            <th>Tipo</th>
                                            <th>Ubicación</th>
                                            <th>Vigencia</th>
                                            <th>Estado</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modalBanner" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBannerTitle">Nuevo Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formBanner">
                    <div class="modal-body">
                        <input type="hidden" id="bannerId">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" id="bannerTitulo" name="titulo" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea id="bannerDescripcion" name="descripcion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Imagen del Banner *</label>
                                <input type="hidden" id="bannerUrlImagen" name="url_imagen" required>
                                <div class="d-flex gap-2 align-items-start flex-column">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnUploadBanner">
                                        <i data-acorn-icon="upload"></i> Subir Imagen
                                    </button>
                                    <div id="previewBanner" style="display:none;">
                                        <img id="bannerPreview" src="" style="max-width:200px; max-height:100px; border-radius:4px; object-fit:cover;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">URL Enlace (opcional)</label>
                                <input type="text" id="bannerUrlEnlace" name="url_enlace" class="form-control" placeholder="https://ejemplo.com">
                                <small class="text-muted">Enlace al que redirige el banner al hacer clic</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo</label>
                                <select id="bannerTipo" name="tipo" class="form-select">
                                    <option value="INFORMATIVO">Informativo</option>
                                    <option value="EVENTO">Evento</option>
                                    <option value="PROMOCIONAL">Promocional</option>
                                    <option value="URGENTE">Urgente</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ubicación</label>
                                <select id="bannerPosicion" name="posicion" class="form-select">
                                    <option value="HOME">Home</option>
                                    <option value="INVOLUCRATE">Involúcrate</option>
                                    <option value="EVENTOS">Eventos</option>
                                    <option value="TODAS">Todas</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <select id="bannerActivo" name="activo" class="form-select">
                                    <option value="S">Activo</option>
                                    <option value="N">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" id="bannerFechaInicio" name="fecha_inicio" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" id="bannerFechaFin" name="fecha_fin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><span id="btnTexto">Crear Banner</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>

    <!-- Input file oculto para upload -->
    <input type="file" id="fileInputBanner" style="display:none;" accept="image/*">

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;
        let modalBanner;

        function abrirFormularioCrear() {
            document.getElementById('formBanner').reset();
            document.getElementById('bannerId').value = '';
            document.getElementById('bannerUrlImagen').value = '';
            document.getElementById('previewBanner').style.display = 'none';
            document.getElementById('modalBannerTitle').textContent = 'Nuevo Banner';
            document.getElementById('btnTexto').textContent = 'Crear Banner';
            modalBanner.show();
        }

        // Botón de subir imagen del banner
        document.getElementById('btnUploadBanner').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInputBanner');

            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'banner');

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            document.getElementById('bannerUrlImagen').value = imagenURL;
                            document.getElementById('bannerPreview').src = imagenURL;
                            document.getElementById('previewBanner').style.display = 'block';

                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Imagen subida correctamente'
                            }, { type: 'success' });
                        } else {
                            jQuery.notify({
                                title: 'Error',
                                message: data.message || 'Error al subir imagen'
                            }, { type: 'danger' });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        jQuery.notify({
                            title: 'Error',
                            message: 'Error al subir la imagen'
                        }, { type: 'danger' });
                    });
                }
            };
            fileInput.click();
        });

        function editarBanner(id) {
            fetch(siteURL + 'assets/API/banners/obtener.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        const b = data.data;
                        document.getElementById('bannerId').value = b.ID;
                        document.getElementById('bannerTitulo').value = b.TITULO;
                        document.getElementById('bannerDescripcion').value = b.DESCRIPCION || '';
                        document.getElementById('bannerUrlImagen').value = b.IMAGEN_URL;
                        document.getElementById('bannerUrlEnlace').value = b.ENLACE || '';
                        document.getElementById('bannerTipo').value = b.TIPO;
                        document.getElementById('bannerPosicion').value = b.UBICACION;
                        document.getElementById('bannerActivo').value = b.ACTIVO;
                        document.getElementById('bannerFechaInicio').value = b.FECHA_INICIO || '';
                        document.getElementById('bannerFechaFin').value = b.FECHA_FIN || '';

                        // Mostrar preview de la imagen
                        if (b.IMAGEN_URL) {
                            document.getElementById('bannerPreview').src = b.IMAGEN_URL;
                            document.getElementById('previewBanner').style.display = 'block';
                        }

                        document.getElementById('modalBannerTitle').textContent = 'Editar Banner';
                        document.getElementById('btnTexto').textContent = 'Actualizar Banner';
                        modalBanner.show();
                    }
                });
        }

        document.getElementById('formBanner').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validar campos requeridos manualmente
            const titulo = document.getElementById('bannerTitulo').value.trim();
            const urlImagen = document.getElementById('bannerUrlImagen').value.trim();

            if (!titulo) {
                jQuery.notify({
                    title: 'Error',
                    message: 'El título es obligatorio'
                }, {type: 'danger'});
                return;
            }

            if (!urlImagen) {
                jQuery.notify({
                    title: 'Error',
                    message: 'Debes subir una imagen del banner haciendo clic en "Subir Imagen"'
                }, {type: 'danger'});
                return;
            }

            const bannerId = document.getElementById('bannerId').value;
            const formData = new FormData(this);

            let url = bannerId
                ? (formData.append('id', bannerId), siteURL + 'assets/API/banners/editar.php')
                : siteURL + 'assets/API/banners/crear.php';

            fetch(url, {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    jQuery.notify({title: 'Éxito', message: data.message || 'Guardado'}, {type: 'success'});
                    modalBanner.hide();
                    cargarBanners();
                } else {
                    jQuery.notify({title: 'Error', message: data.message}, {type: 'danger'});
                }
            })
            .catch(error => {
                console.error('Error:', error);
                jQuery.notify({
                    title: 'Error',
                    message: 'Error al procesar la solicitud'
                }, {type: 'danger'});
            });
        });

        function eliminarBanner(id, titulo) {
            if (!confirm(`¿Eliminar "${titulo}"?`)) return;
            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/banners/eliminar.php', {method: 'POST', body: formData})
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    jQuery.notify({title: 'Éxito', message: data.message}, {type: 'success'});
                    cargarBanners();
                } else {
                    jQuery.notify({title: 'Error', message: data.message}, {type: 'danger'});
                }
            });
        }

        function cargarBanners() {
            fetch(siteURL + 'assets/API/banners/listar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        if ($.fn.DataTable.isDataTable('#tablaBanners')) {
                            $('#tablaBanners').DataTable().clear().destroy();
                        }

                        const tbody = document.querySelector('#tablaBanners tbody');
                        let htmlRows = '';

                        data.data.forEach(b => {
                            const vigencia = b.FECHA_INICIO && b.FECHA_FIN
                                ? `${b.FECHA_INICIO} al ${b.FECHA_FIN}`
                                : (b.FECHA_INICIO ? `Desde ${b.FECHA_INICIO}` : 'Sin límite');

                            htmlRows += `
                                <tr>
                                    <td>${b.ID}</td>
                                    <td>
                                        <img src="${b.IMAGEN_URL}" class="rounded me-2" style="width:60px;height:30px;object-fit:cover;" onerror="this.src='https://via.placeholder.com/60x30'">
                                        <strong>${b.TITULO}</strong>
                                    </td>
                                    <td><span class="badge bg-info">${b.TIPO}</span></td>
                                    <td><span class="badge bg-secondary">${b.UBICACION}</span></td>
                                    <td><small>${vigencia}</small></td>
                                    <td><span class="badge ${b.ACTIVO == 'S' ? 'bg-success' : 'bg-secondary'}">${b.ACTIVO == 'S' ? 'Activo' : 'Inactivo'}</span></td>
                                    <td class="text-center">
                                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarBanner(${b.ID})">
                                            <i class="fa fa-edit"></i>
                                            <span class="d-none d-xxl-inline-block">Editar</span>
                                        </button>` : ''}
                                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="eliminarBanner(${b.ID}, '${b.TITULO.replace(/'/g, "\\'")}')">
                                            <i class="fa fa-trash"></i>
                                            <span class="d-none d-xxl-inline-block">Eliminar</span>
                                        </button>` : ''}
                                    </td>
                                </tr>
                            `;
                        });

                        tbody.innerHTML = htmlRows;
                        $('#tablaBanners').DataTable({
                            language: {url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'},
                            order: [[0, 'desc']],
                            pageLength: 25
                        });
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            modalBanner = new bootstrap.Modal(document.getElementById('modalBanner'));
            cargarBanners();
        });
    </script>
</body>
</html>
