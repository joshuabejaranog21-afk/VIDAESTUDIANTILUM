<?php
include('../../../assets/php/template.php');
$temp = new Template('Fotos de Estudiantes - Anuarios');
if (!$temp->validate_session(2)) { // Solo admin o superior
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
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
                            <h1 class="mb-0 pb-0 display-4">Fotos de Estudiantes</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/anuarios/">Anuarios</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/anuarios/admin/">Administrar</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Fotos Estudiantes</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 text-end">
                            <button class="btn btn-primary" onclick="openCreateModal()">
                                <i class="fa fa-plus"></i> Agregar Foto
                            </button>
                            <button class="btn btn-success" onclick="openBulkModal()">
                                <i class="fa fa-upload"></i> Importar Múltiples
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Filtrar por Anuario:</label>
                                        <select class="form-select" id="filterAnuario" onchange="loadTable()">
                                            <option value="">Todos los anuarios</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Buscar por Matrícula:</label>
                                        <input type="text" class="form-control" id="searchMatricula" placeholder="Ej: 1220593">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Buscar por Nombre:</label>
                                        <input type="text" class="form-control" id="searchNombre" placeholder="Nombre del estudiante">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photos Table Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="statsContainer" class="mb-3"></div>
                                <div id="tableContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Photos Table End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Modal Create/Edit Single Photo -->
    <div class="modal fade" id="fotoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Foto de Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="fotoForm">
                        <input type="hidden" id="form-id">

                        <div class="mb-3">
                            <label class="form-label">Anuario *</label>
                            <select class="form-select" id="form-anuario" required>
                                <option value="">Seleccionar anuario...</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Matrícula *</label>
                                    <input type="text" class="form-control" id="form-matricula" required placeholder="Ej: 1220593">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Año</label>
                                    <input type="number" class="form-control" id="form-anio" min="1900" max="2100">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Estudiante</label>
                            <input type="text" class="form-control" id="form-nombre">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Carrera</label>
                                    <input type="text" class="form-control" id="form-carrera">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Facultad</label>
                                    <input type="text" class="form-control" id="form-facultad">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL de Flickr (o cualquier URL de imagen) *</label>
                            <input type="url" class="form-control" id="form-foto-url" required placeholder="https://live.staticflickr.com/...">
                            <small class="text-muted">
                                Pega aquí el enlace directo de la foto de Flickr.
                                <a href="#" onclick="showFlickrHelp(); return false;">¿Cómo obtener el enlace?</a>
                            </small>
                        </div>

                        <!-- Preview de la foto -->
                        <div class="mb-3" id="fotoPreview" style="display: none;">
                            <label class="form-label">Vista previa:</label>
                            <div class="text-center">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 300px; max-height: 300px;" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="form-activo" checked>
                                <label class="form-check-label" for="form-activo">
                                    Foto activa (visible)
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveFoto()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bulk Import -->
    <div class="modal fade" id="bulkModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Importar Múltiples Fotos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Anuario *</label>
                        <select class="form-select" id="bulk-anuario" required>
                            <option value="">Seleccionar anuario...</option>
                        </select>
                    </div>

                    <div class="alert alert-info">
                        <strong>Formato CSV esperado:</strong><br>
                        <code>matricula,nombre_estudiante,carrera,facultad,foto_url,anio</code><br>
                        <small>Ejemplo:</small><br>
                        <code>1220593,Juan Pérez,Ingeniería en Sistemas,Ingeniería,https://flickr.com/foto1.jpg,2024</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pegar datos CSV:</label>
                        <textarea class="form-control" id="bulk-csv" rows="10" placeholder="Pega aquí los datos en formato CSV..."></textarea>
                    </div>

                    <div class="mb-3">
                        <button class="btn btn-sm btn-secondary" onclick="downloadTemplate()">
                            <i class="fa fa-download"></i> Descargar Plantilla CSV
                        </button>
                    </div>

                    <div id="bulkPreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="validateBulk()">Validar Datos</button>
                    <button type="button" class="btn btn-success" id="importBtn" onclick="importBulk()" style="display: none;">Importar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Flickr Help -->
    <div class="modal fade" id="flickrHelpModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Cómo obtener el enlace de Flickr?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Abre la foto en Flickr</li>
                        <li>Haz clic derecho sobre la foto</li>
                        <li>Selecciona "Copiar dirección de imagen"</li>
                        <li>Pega el enlace en el campo de arriba</li>
                    </ol>
                    <p class="mt-3"><strong>Formato del enlace:</strong></p>
                    <code>https://live.staticflickr.com/{server-id}/{photo-id}_{secret}.jpg</code>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/anuarios/admin/fotos-estudiantes/";
        const anuariosUrl = "<?php echo $temp->siteURL ?>assets/API/anuarios/admin/";
        let editingId = null;
        let allPhotos = [];

        // Load anuarios for dropdown
        function loadAnuarios() {
            fetch(anuariosUrl + 'listar.php')
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        let options = '<option value="">Seleccionar anuario...</option>';
                        let filterOptions = '<option value="">Todos los anuarios</option>';
                        result.data.forEach(a => {
                            options += `<option value="${a.ID}">${a.ANIO} - ${a.TITULO}</option>`;
                            filterOptions += `<option value="${a.ID}">${a.ANIO} - ${a.TITULO}</option>`;
                        });
                        $('#form-anuario, #bulk-anuario').html(options);
                        $('#filterAnuario').html(filterOptions);
                    }
                });
        }

        // Load table
        function loadTable() {
            const anuarioId = $('#filterAnuario').val();
            const urlParams = anuarioId ? `?id_anuario=${anuarioId}` : '';

            fetch(url + 'listar.php' + urlParams)
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        allPhotos = result.data;
                        filterAndRenderTable();
                    }
                });
        }

        // Filter and render table
        function filterAndRenderTable() {
            const searchMatricula = $('#searchMatricula').val().toLowerCase();
            const searchNombre = $('#searchNombre').val().toLowerCase();

            let filtered = allPhotos.filter(f => {
                const matchMatricula = !searchMatricula || (f.MATRICULA && f.MATRICULA.toLowerCase().includes(searchMatricula));
                const matchNombre = !searchNombre || (f.NOMBRE_ESTUDIANTE && f.NOMBRE_ESTUDIANTE.toLowerCase().includes(searchNombre));
                return matchMatricula && matchNombre;
            });

            renderStats(filtered.length, allPhotos.length);
            renderTable(filtered);
        }

        // Render stats
        function renderStats(showing, total) {
            const html = `
                <div class="alert alert-primary">
                    Mostrando <strong>${showing}</strong> de <strong>${total}</strong> fotos
                </div>
            `;
            $('#statsContainer').html(html);
        }

        // Render table
        function renderTable(fotos) {
            if (fotos.length === 0) {
                $('#tableContainer').html('<div class="alert alert-warning">No se encontraron fotos</div>');
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>Anuario</th>
                                <th>Año</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            fotos.forEach(f => {
                html += `
                    <tr>
                        <td>
                            <img src="${f.FOTO_URL}" alt="${f.NOMBRE_ESTUDIANTE}"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                 onerror="this.src='<?php echo $temp->siteURL ?>assets/images/no-image.png'">
                        </td>
                        <td><strong>${f.MATRICULA}</strong></td>
                        <td>${f.NOMBRE_ESTUDIANTE || '-'}</td>
                        <td>${f.CARRERA || '-'}</td>
                        <td>${f.NOMBRE_ANUARIO || '-'}</td>
                        <td>${f.ANIO || '-'}</td>
                        <td>${f.ACTIVO === 'S' ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-danger">Inactiva</span>'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editFoto(${f.ID})" title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <a href="${f.FOTO_URL}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver foto">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteFoto(${f.ID}, '${f.MATRICULA}')" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#tableContainer').html(html);
        }

        // Open create modal
        function openCreateModal() {
            editingId = null;
            $('#modalTitle').text('Agregar Foto de Estudiante');
            $('#fotoForm')[0].reset();
            $('#form-id').val('');
            $('#form-activo').prop('checked', true);
            $('#form-anio').val(new Date().getFullYear());
            $('#fotoPreview').hide();
            $('#fotoModal').modal('show');
        }

        // Edit foto
        function editFoto(id) {
            editingId = id;
            $('#modalTitle').text('Editar Foto de Estudiante');

            const foto = allPhotos.find(f => f.ID === id);
            if (foto) {
                $('#form-id').val(foto.ID);
                $('#form-anuario').val(foto.ID_ANUARIO);
                $('#form-matricula').val(foto.MATRICULA);
                $('#form-nombre').val(foto.NOMBRE_ESTUDIANTE);
                $('#form-carrera').val(foto.CARRERA);
                $('#form-facultad').val(foto.FACULTAD);
                $('#form-foto-url').val(foto.FOTO_URL);
                $('#form-anio').val(foto.ANIO);
                $('#form-activo').prop('checked', foto.ACTIVO === 'S');

                // Show preview
                $('#previewImg').attr('src', foto.FOTO_URL);
                $('#fotoPreview').show();

                $('#fotoModal').modal('show');
            }
        }

        // Save foto
        async function saveFoto() {
            const formData = new FormData();
            formData.append('id', $('#form-id').val());
            formData.append('id_anuario', $('#form-anuario').val());
            formData.append('matricula', $('#form-matricula').val());
            formData.append('nombre_estudiante', $('#form-nombre').val());
            formData.append('carrera', $('#form-carrera').val());
            formData.append('facultad', $('#form-facultad').val());
            formData.append('foto_url', $('#form-foto-url').val());
            formData.append('anio', $('#form-anio').val());
            formData.append('activo', $('#form-activo').is(':checked') ? 'S' : 'N');

            const endpoint = editingId ? 'actualizar.php' : 'crear.php';

            $('#saveBtn').prop('disabled', true).text('Guardando...');

            try {
                const response = await fetch(url + endpoint, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    $('#fotoModal').modal('hide');
                    loadTable();
                    alert(editingId ? 'Foto actualizada correctamente' : 'Foto agregada correctamente');
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar la foto');
            } finally {
                $('#saveBtn').prop('disabled', false).text('Guardar');
            }
        }

        // Delete foto
        async function deleteFoto(id, matricula) {
            if (!confirm(`¿Estás seguro de eliminar la foto de la matrícula "${matricula}"?`)) return;

            try {
                const response = await fetch(url + 'eliminar.php?id=' + id, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    loadTable();
                    alert('Foto eliminada correctamente');
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar la foto');
            }
        }

        // Open bulk modal
        function openBulkModal() {
            $('#bulkModal').modal('show');
            $('#bulk-csv').val('');
            $('#bulkPreview').html('');
            $('#importBtn').hide();
        }

        // Show Flickr help
        function showFlickrHelp() {
            $('#flickrHelpModal').modal('show');
        }

        // Download CSV template
        function downloadTemplate() {
            const csv = 'matricula,nombre_estudiante,carrera,facultad,foto_url,anio\n1220593,Juan Pérez,Ingeniería en Sistemas,Ingeniería,https://live.staticflickr.com/foto.jpg,2024';
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'plantilla_fotos_estudiantes.csv';
            a.click();
        }

        // Validate bulk import
        function validateBulk() {
            const csv = $('#bulk-csv').val().trim();
            const anuarioId = $('#bulk-anuario').val();

            if (!anuarioId) {
                alert('Por favor selecciona un anuario');
                return;
            }

            if (!csv) {
                alert('Por favor pega los datos CSV');
                return;
            }

            const lines = csv.split('\n').filter(l => l.trim());
            const data = [];
            let errors = [];

            lines.forEach((line, index) => {
                const parts = line.split(',').map(p => p.trim());
                if (parts.length >= 5) {
                    data.push({
                        matricula: parts[0],
                        nombre: parts[1],
                        carrera: parts[2],
                        facultad: parts[3],
                        url: parts[4],
                        anio: parts[5] || new Date().getFullYear()
                    });
                } else {
                    errors.push(`Línea ${index + 1}: formato incorrecto`);
                }
            });

            if (errors.length > 0) {
                alert('Errores encontrados:\n' + errors.join('\n'));
                return;
            }

            // Show preview
            let html = `
                <div class="alert alert-success">
                    Se encontraron ${data.length} registros válidos
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            data.slice(0, 5).forEach(d => {
                html += `
                    <tr>
                        <td>${d.matricula}</td>
                        <td>${d.nombre}</td>
                        <td>${d.carrera}</td>
                        <td><small>${d.url.substring(0, 50)}...</small></td>
                    </tr>
                `;
            });

            if (data.length > 5) {
                html += `<tr><td colspan="4" class="text-center">... y ${data.length - 5} más</td></tr>`;
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            $('#bulkPreview').html(html);
            $('#importBtn').show().data('records', data);
        }

        // Import bulk
        async function importBulk() {
            const data = $('#importBtn').data('records');
            const anuarioId = $('#bulk-anuario').val();

            $('#importBtn').prop('disabled', true).text('Importando...');

            let success = 0;
            let errors = [];

            for (let item of data) {
                const formData = new FormData();
                formData.append('id_anuario', anuarioId);
                formData.append('matricula', item.matricula);
                formData.append('nombre_estudiante', item.nombre);
                formData.append('carrera', item.carrera);
                formData.append('facultad', item.facultad);
                formData.append('foto_url', item.url);
                formData.append('anio', item.anio);
                formData.append('activo', 'S');

                try {
                    const response = await fetch(url + 'crear.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        success++;
                    } else {
                        errors.push(`${item.matricula}: ${result.error}`);
                    }
                } catch (error) {
                    errors.push(`${item.matricula}: Error de conexión`);
                }
            }

            $('#bulkModal').modal('hide');
            loadTable();

            let message = `Importación completada:\n${success} registros importados correctamente`;
            if (errors.length > 0) {
                message += `\n\n${errors.length} errores:\n` + errors.slice(0, 5).join('\n');
                if (errors.length > 5) {
                    message += `\n... y ${errors.length - 5} más`;
                }
            }

            alert(message);
        }

        // Preview image on URL change
        $('#form-foto-url').on('blur', function() {
            const url = $(this).val();
            if (url) {
                $('#previewImg').attr('src', url);
                $('#fotoPreview').show();
            }
        });

        // Search filters
        $('#searchMatricula, #searchNombre').on('keyup', function() {
            filterAndRenderTable();
        });

        // Load on ready
        $(document).ready(function() {
            loadAnuarios();
            loadTable();
        });
    </script>
</body>

</html>
