<?php
include('../../../assets/php/template.php');
$temp = new Template('Administrar Anuarios');
if (!$temp->validate_session(2)) { // Solo admin o superior
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>assets/css/vendor/datatables.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 15px;
        }

        /* Mejorar apariencia de Select2 tags */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #5e72e4 !important;
            border-color: #5e72e4 !important;
            color: #fff !important;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
            margin-right: 5px;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffd !important;
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
                            <h1 class="mb-0 pb-0 display-4">Administrar Anuarios</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/anuarios/">Anuarios</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Administrar</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="openCreateModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined">
                                    <path d="M10 17 10 3M3 10 17 10"></path>
                                </svg>
                                <span>Nuevo Anuario</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Anuarios Table Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="tableContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Anuarios Table End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <!-- Modal Create/Edit -->
    <div class="modal fade" id="anuarioModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Anuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="anuarioForm">
                        <input type="hidden" id="form-id">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="form-titulo" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Año *</label>
                                    <input type="number" class="form-control" id="form-anio" min="1900" max="2100" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="form-descripcion" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Década</label>
                                    <select class="form-select" id="form-decada">
                                        <option value="">Seleccionar...</option>
                                        <option value="2020">2020s</option>
                                        <option value="2010">2010s</option>
                                        <option value="2000">2000s</option>
                                        <option value="1990">1990s</option>
                                        <option value="1980">1980s</option>
                                        <option value="1970">1970s</option>
                                        <option value="1960">1960s</option>
                                        <option value="1950">1950s</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Total de Páginas</label>
                                    <input type="number" class="form-control" id="form-paginas" min="0">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="form-conmemorativo">
                                    <label class="form-check-label" for="form-conmemorativo">
                                        Es Conmemorativo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="conmemorativoRazon" style="display: none;">
                            <label class="form-label">Razón Conmemorativa</label>
                            <input type="text" class="form-control" id="form-razon-conmemorativa">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PDF del Anuario *</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pdfOption" id="pdfOptionUrl" value="url" checked>
                                    <label class="form-check-label" for="pdfOptionUrl">
                                        Usar URL
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pdfOption" id="pdfOptionFile" value="file">
                                    <label class="form-check-label" for="pdfOptionFile">
                                        Subir archivo
                                    </label>
                                </div>
                            </div>

                            <!-- URL Option -->
                            <div id="urlSection">
                                <input type="url" class="form-control" id="form-pdf-url" placeholder="https://...">
                                <small class="text-muted">Puedes pegar URL de FlipHTML5, Google Drive, Dropbox, etc.</small>
                            </div>

                            <!-- File Upload Option -->
                            <div id="fileSection" style="display: none;">
                                <input type="file" class="form-control" id="form-pdf-file" accept=".pdf">
                                <small class="text-muted">Selecciona un archivo PDF (máx. 50MB)</small>
                                <div id="uploadProgress" style="display: none;" class="mt-2">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="form-pdf-final-url">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imagen de Portada</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="portadaOption" id="portadaOptionAuto" value="auto" checked>
                                    <label class="form-check-label" for="portadaOptionAuto">
                                        Extraer de PDF (1ª página)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="portadaOption" id="portadaOptionUrl" value="url">
                                    <label class="form-check-label" for="portadaOptionUrl">
                                        Usar URL
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="portadaOption" id="portadaOptionFile" value="file">
                                    <label class="form-check-label" for="portadaOptionFile">
                                        Subir imagen
                                    </label>
                                </div>
                            </div>

                            <!-- Auto Extract Option -->
                            <div id="portadaAutoSection">
                                <small class="text-muted d-block mb-2">
                                    <i class="fa fa-magic"></i>
                                    Se generará automáticamente la portada a partir de la primera página del PDF al guardar.
                                </small>
                                <div id="portadaAutoPreview" class="mt-2" style="display:none;">
                                    <img id="portadaAutoPreviewImg" src="" alt="Preview primera página" class="img-thumbnail" style="max-height: 220px;">
                                    <div class="small text-success mt-1"><i class="fa fa-check"></i> Primera página extraída correctamente</div>
                                </div>
                                <div id="portadaAutoStatus" class="mt-2 small text-muted" style="display:none;">
                                    <i class="fa fa-spinner fa-spin"></i> <span>Procesando PDF...</span>
                                </div>
                            </div>

                            <!-- URL Option -->
                            <div id="portadaUrlSection" style="display:none;">
                                <input type="url" class="form-control" id="form-imagen-portada-url" placeholder="https://...">
                                <small class="text-muted">URL de la imagen de portada</small>
                            </div>

                            <!-- File Upload Option -->
                            <div id="portadaFileSection" style="display: none;">
                                <input type="file" class="form-control" id="form-portada-file" accept="image/jpeg,image/jpg,image/png,image/webp">
                                <small class="text-muted">Selecciona una imagen JPG, PNG o WebP (máx. 10MB)</small>
                                <div id="portadaUploadProgress" style="display: none;" class="mt-2">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                                <!-- Preview de la imagen -->
                                <div id="portadaPreview" class="mt-2" style="display: none;">
                                    <img id="portadaPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>

                            <input type="hidden" id="form-portada-final-url">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fotógrafos</label>
                            <select class="form-select select2" id="form-fotografos" multiple="multiple" data-placeholder="Selecciona fotógrafos del equipo...">
                            </select>
                            <small class="text-muted">Selecciona uno o varios fotógrafos del equipo de Pulso</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contribuyentes</label>
                            <textarea class="form-control" id="form-contribuyentes" rows="2" placeholder="Nombres de los contribuyentes separados por coma"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveAnuario()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/anuarios/admin/";
        let editingId = null;
        let fotografos = []; // Lista de fotógrafos disponibles

        // Load fotógrafos from Pulso team
        function loadFotografos() {
            fetch("<?php echo $temp->siteURL ?>assets/API/pulso/listar.php")
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.miembros) {
                        // Filtrar solo fotógrafos o todos los miembros activos
                        // Puedes ajustar este filtro según tus necesidades
                        fotografos = data.miembros
                            .filter(m => m.ACTIVO === 'S')
                            .map(m => m.NOMBRE)
                            .sort();

                        // Llenar el select de fotógrafos
                        const select = $('#form-fotografos');
                        select.empty();
                        fotografos.forEach(nombre => {
                            select.append(`<option value="${nombre}">${nombre}</option>`);
                        });
                    }
                });
        }

        // Load table
        function loadTable() {
            fetch(url + 'listar.php')
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        renderTable(result.data);
                    }
                });
        }

        // Render table
        function renderTable(anuarios) {
            // Destruir DataTable existente si existe
            if ($.fn.DataTable.isDataTable('#datatableRows')) {
                $('#datatableRows').DataTable().destroy();
            }

            let html = `
                <table id="datatableRows" class="data-table nowrap hover">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>Título</th>
                            <th>Páginas</th>
                            <th>Likes</th>
                            <th>Vistas</th>
                            <th>Conmemorativo</th>
                            <th>Estado</th>
                            <th class="empty">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            anuarios.forEach(a => {
                html += `
                    <tr>
                        <td><strong>${a.ANIO}</strong></td>
                        <td>${a.TITULO}</td>
                        <td>${a.TOTAL_PAGINAS || 0}</td>
                        <td><i class="fa fa-heart text-danger"></i> ${a.LIKES}</td>
                        <td><i class="fa fa-eye text-info"></i> ${a.VISTAS}</td>
                        <td>${a.ES_CONMEMORATIVO === 'S' ? '<span class="badge bg-warning text-dark">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                        <td>${a.ACTIVO === 'S' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editAnuario(${a.ID})">
                                <i class="fa fa-edit"></i>
                                <span class="d-none d-xxl-inline-block">Editar</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="deleteAnuario(${a.ID}, '${a.TITULO}')">
                                <i class="fa fa-trash"></i>
                                <span class="d-none d-xxl-inline-block">Eliminar</span>
                            </button>
                            ${a.ACTIVO === 'S' ?
                                `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-warning ms-1" type="button" onclick="toggleActive(${a.ID}, 'N')">
                                    <i class="fa fa-eye-slash"></i>
                                    <span class="d-none d-xxl-inline-block">Desactivar</span>
                                </button>` :
                                `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="toggleActive(${a.ID}, 'S')">
                                    <i class="fa fa-eye"></i>
                                    <span class="d-none d-xxl-inline-block">Activar</span>
                                </button>`
                            }
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
                "order": [[0, 'desc']], // Ordenar por año descendente
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

        // Open create modal
        function openCreateModal() {
            editingId = null;
            $('#modalTitle').text('Nuevo Anuario');
            $('#anuarioForm')[0].reset();
            $('#form-id').val('');
            $('#form-fotografos').val([]).trigger('change'); // Limpiar select2 de fotógrafos

            // Reset opciones de PDF y Portada (auto por defecto)
            $('#pdfOptionUrl').prop('checked', true).trigger('change');
            $('#portadaOptionAuto').prop('checked', true).trigger('change');
            $('#portadaPreview').hide();
            $('#portadaAutoPreview').hide();
            $('#portadaAutoStatus').hide();
            portadaAutoBlob = null;

            $('#anuarioModal').modal('show');
        }

        // Edit anuario
        function editAnuario(id) {
            editingId = id;
            $('#modalTitle').text('Editar Anuario');

            fetch(url + 'obtener.php?id=' + id)
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        const a = result.data;
                        $('#form-id').val(a.ID);
                        $('#form-titulo').val(a.TITULO);
                        $('#form-anio').val(a.ANIO);
                        $('#form-descripcion').val(a.DESCRIPCION);
                        $('#form-decada').val(a.DECADA);
                        $('#form-paginas').val(a.TOTAL_PAGINAS);
                        $('#form-conmemorativo').prop('checked', a.ES_CONMEMORATIVO === 'S');
                        $('#form-razon-conmemorativa').val(a.RAZON_CONMEMORATIVA);
                        $('#form-pdf-url').val(a.PDF_URL);
                        $('#form-imagen-portada-url').val(a.IMAGEN_PORTADA);

                        // Configurar opciones de portada (siempre URL al editar para conservar la existente)
                        $('#portadaOptionUrl').prop('checked', true).trigger('change');
                        $('#portadaPreview').hide();
                        $('#portadaAutoPreview').hide();
                        $('#portadaAutoStatus').hide();
                        portadaAutoBlob = null;

                        // Cargar fotógrafos seleccionados (convertir string separado por comas en array)
                        if (a.FOTOGRAFOS) {
                            const fotografosSeleccionados = a.FOTOGRAFOS.split(',').map(f => f.trim());
                            $('#form-fotografos').val(fotografosSeleccionados).trigger('change');
                        } else {
                            $('#form-fotografos').val([]).trigger('change');
                        }

                        $('#form-contribuyentes').val(a.CONTRIBUYENTES);

                        if (a.ES_CONMEMORATIVO === 'S') {
                            $('#conmemorativoRazon').show();
                        }

                        $('#anuarioModal').modal('show');
                    }
                });
        }

        // Save anuario
        async function saveAnuario() {
            const pdfOption = $('input[name="pdfOption"]:checked').val();
            let pdfUrl = '';

            // Si se eligió subir archivo
            if (pdfOption === 'file') {
                const fileInput = document.getElementById('form-pdf-file');
                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Por favor selecciona un archivo PDF');
                    return;
                }

                // Subir archivo primero
                $('#saveBtn').prop('disabled', true).text('Subiendo PDF...');
                $('#uploadProgress').show();

                const uploadFormData = new FormData();
                const pdfFile = fileInput.files[0];

                console.log('Archivo seleccionado:', {
                    name: pdfFile.name,
                    size: pdfFile.size,
                    type: pdfFile.type
                });

                uploadFormData.append('pdf', pdfFile);

                // Verificar FormData
                console.log('FormData entries:');
                for (let pair of uploadFormData.entries()) {
                    console.log(pair[0], pair[1]);
                }

                try {
                    console.log('Enviando a:', url + 'upload-pdf.php');
                    const uploadResponse = await fetch(url + 'upload-pdf.php', {
                        method: 'POST',
                        body: uploadFormData
                    });

                    console.log('Upload response status:', uploadResponse.status);
                    const responseText = await uploadResponse.text();
                    console.log('Upload response text:', responseText);

                    let uploadResult;
                    try {
                        uploadResult = JSON.parse(responseText);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response was:', responseText);
                        alert('Error al subir el PDF: Respuesta inválida del servidor\n\nRevisa la consola del navegador (F12) para más detalles');
                        $('#saveBtn').prop('disabled', false).text('Guardar');
                        $('#uploadProgress').hide();
                        return;
                    }

                    if (!uploadResult.success) {
                        console.error('Upload failed:', uploadResult);
                        let errorMsg = 'Error al subir el PDF: ' + uploadResult.message;
                        if (uploadResult.debug) {
                            console.error('Debug info:', uploadResult.debug);
                            errorMsg += '\n\nDetalles en la consola (F12)';
                        }
                        alert(errorMsg);
                        $('#saveBtn').prop('disabled', false).text('Guardar');
                        $('#uploadProgress').hide();
                        return;
                    }

                    console.log('Upload successful:', uploadResult);
                    pdfUrl = uploadResult.url;
                } catch (error) {
                    console.error('Error completo:', error);
                    alert('Error al subir el archivo: ' + error.message + '\n\nRevisa la consola del navegador (F12) para más detalles');
                    $('#saveBtn').prop('disabled', false).text('Guardar');
                    $('#uploadProgress').hide();
                    return;
                }

                $('#uploadProgress').hide();
            } else {
                // Usar URL
                pdfUrl = $('#form-pdf-url').val();
                if (!pdfUrl) {
                    alert('Por favor ingresa la URL del PDF');
                    return;
                }
            }

            // Manejar la imagen de portada
            const portadaOption = $('input[name="portadaOption"]:checked').val();
            let portadaUrl = '';

            if (portadaOption === 'auto') {
                // Si aún no tenemos blob (ej: PDF vía URL o edición), intentar generarlo ahora
                if (!portadaAutoBlob && pdfUrl) {
                    $('#saveBtn').text('Extrayendo portada del PDF...');
                    try {
                        portadaAutoBlob = await extraerPrimeraPaginaPDF(pdfUrl);
                    } catch (err) {
                        console.error('Error extrayendo portada desde URL del PDF:', err);
                        alert('No se pudo extraer la primera página del PDF: ' + err.message + '\nElige "Subir imagen" o "Usar URL" manualmente.');
                        $('#saveBtn').prop('disabled', false).text('Guardar');
                        return;
                    }
                }

                if (portadaAutoBlob) {
                    $('#saveBtn').text('Subiendo portada...');
                    const portadaFormData = new FormData();
                    portadaFormData.append('portada', portadaAutoBlob, 'portada_auto_' + Date.now() + '.jpg');

                    try {
                        const portadaResponse = await fetch(url + 'upload-portada.php', {
                            method: 'POST',
                            body: portadaFormData
                        });
                        const portadaResult = await portadaResponse.json();

                        if (!portadaResult.success) {
                            alert('Error al subir la portada auto-extraída: ' + portadaResult.message);
                            $('#saveBtn').prop('disabled', false).text('Guardar');
                            return;
                        }
                        portadaUrl = portadaResult.url;
                    } catch (error) {
                        console.error('Error al subir portada auto:', error);
                        alert('Error al subir la portada: ' + error.message);
                        $('#saveBtn').prop('disabled', false).text('Guardar');
                        return;
                    }
                }
            } else if (portadaOption === 'file') {
                const portadaFileInput = document.getElementById('form-portada-file');
                if (portadaFileInput.files && portadaFileInput.files.length > 0) {
                    // Subir imagen de portada
                    $('#saveBtn').text('Subiendo portada...');
                    $('#portadaUploadProgress').show();

                    const portadaFormData = new FormData();
                    portadaFormData.append('portada', portadaFileInput.files[0]);

                    try {
                        const portadaResponse = await fetch(url + 'upload-portada.php', {
                            method: 'POST',
                            body: portadaFormData
                        });

                        const portadaResult = await portadaResponse.json();

                        if (!portadaResult.success) {
                            alert('Error al subir la portada: ' + portadaResult.message);
                            $('#saveBtn').prop('disabled', false).text('Guardar');
                            $('#portadaUploadProgress').hide();
                            return;
                        }

                        portadaUrl = portadaResult.url;
                        $('#portadaUploadProgress').hide();
                    } catch (error) {
                        console.error('Error al subir portada:', error);
                        alert('Error al subir la imagen de portada: ' + error.message);
                        $('#saveBtn').prop('disabled', false).text('Guardar');
                        $('#portadaUploadProgress').hide();
                        return;
                    }
                }
            } else {
                // Usar URL
                portadaUrl = $('#form-imagen-portada-url').val();
            }

            // Crear/actualizar anuario
            $('#saveBtn').text('Guardando...');

            const formData = new FormData();
            formData.append('id', $('#form-id').val());
            formData.append('titulo', $('#form-titulo').val());
            formData.append('anio', $('#form-anio').val());
            formData.append('descripcion', $('#form-descripcion').val());
            formData.append('decada', $('#form-decada').val());
            formData.append('total_paginas', $('#form-paginas').val());
            formData.append('es_conmemorativo', $('#form-conmemorativo').is(':checked') ? 'S' : 'N');
            formData.append('razon_conmemorativa', $('#form-razon-conmemorativa').val());
            formData.append('pdf_url', pdfUrl);
            formData.append('imagen_portada', portadaUrl);

            // Convertir array de fotógrafos a string separado por comas
            const fotografosArray = $('#form-fotografos').val() || [];
            formData.append('fotografos', fotografosArray.join(', '));

            formData.append('contribuyentes', $('#form-contribuyentes').val());

            const endpoint = editingId ? 'actualizar.php' : 'crear.php';

            try {
                const response = await fetch(url + endpoint, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    $('#anuarioModal').modal('hide');
                    loadTable();
                    alert(editingId ? 'Anuario actualizado correctamente' : 'Anuario creado correctamente');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar el anuario');
            } finally {
                $('#saveBtn').prop('disabled', false).text('Guardar');
            }
        }

        // Delete anuario
        async function deleteAnuario(id, titulo) {
            if (!confirm(`¿Estás seguro de eliminar el anuario "${titulo}"?`)) return;

            try {
                const response = await fetch(url + 'eliminar.php?id=' + id, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    loadTable();
                    alert('Anuario eliminado correctamente');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el anuario');
            }
        }

        // Toggle active
        async function toggleActive(id, activo) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('activo', activo);

            try {
                const response = await fetch(url + 'toggle-active.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    loadTable();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cambiar el estado');
            }
        }

        // Toggle conmemorativo reason field
        $('#form-conmemorativo').on('change', function() {
            if ($(this).is(':checked')) {
                $('#conmemorativoRazon').show();
            } else {
                $('#conmemorativoRazon').hide();
            }
        });

        // Toggle between URL and File upload for PDF
        $('input[name="pdfOption"]').on('change', function() {
            if ($(this).val() === 'url') {
                $('#urlSection').show();
                $('#fileSection').hide();
                $('#form-pdf-url').prop('required', true);
                $('#form-pdf-file').prop('required', false);
            } else {
                $('#urlSection').hide();
                $('#fileSection').show();
                $('#form-pdf-url').prop('required', false);
                $('#form-pdf-file').prop('required', true);
            }
        });

        // Toggle entre Auto-extraer / URL / Subir imagen para la Portada
        $('input[name="portadaOption"]').on('change', function() {
            const val = $(this).val();
            $('#portadaAutoSection, #portadaUrlSection, #portadaFileSection').hide();
            $('#portadaPreview').hide();
            if (val === 'auto') {
                $('#portadaAutoSection').show();
            } else if (val === 'url') {
                $('#portadaUrlSection').show();
            } else {
                $('#portadaFileSection').show();
            }
        });

        // ── Extracción de primera página del PDF con pdf.js ──
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        // Blob de la portada auto-extraída (lista para subir)
        let portadaAutoBlob = null;

        // Renderiza la primera página del PDF a un blob PNG
        async function extraerPrimeraPaginaPDF(source) {
            if (typeof pdfjsLib === 'undefined') {
                throw new Error('pdf.js no cargó. Verifica tu conexión.');
            }
            const loadingTask = pdfjsLib.getDocument(source);
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);

            // Renderizado a alta resolución (para que la portada se vea bien)
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            const ctx = canvas.getContext('2d');

            await page.render({ canvasContext: ctx, viewport: viewport }).promise;

            return new Promise((resolve, reject) => {
                canvas.toBlob(blob => {
                    if (blob) resolve(blob);
                    else reject(new Error('No se pudo generar la imagen'));
                }, 'image/jpeg', 0.92);
            });
        }

        // Cuando el usuario seleccione un PDF, intenta generar la preview automáticamente
        $('#form-pdf-file').on('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if ($('input[name="portadaOption"]:checked').val() !== 'auto') return;

            const $status = $('#portadaAutoStatus');
            const $preview = $('#portadaAutoPreview');
            $preview.hide();
            $status.show().find('span').text('Extrayendo primera página del PDF...');
            portadaAutoBlob = null;

            try {
                const buf = await file.arrayBuffer();
                const blob = await extraerPrimeraPaginaPDF({ data: new Uint8Array(buf) });
                portadaAutoBlob = blob;
                const url = URL.createObjectURL(blob);
                $('#portadaAutoPreviewImg').attr('src', url);
                $preview.show();
                $status.hide();
            } catch (err) {
                console.error('Error extrayendo primera página:', err);
                $status.find('span').text('No se pudo extraer la primera página: ' + err.message);
            }
        });

        // Preview de la imagen de portada al seleccionar archivo
        $('#form-portada-file').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#portadaPreviewImg').attr('src', event.target.result);
                    $('#portadaPreview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#portadaPreview').hide();
            }
        });

        // Load on ready
        $(document).ready(function() {
            // Inicializar Select2 para fotógrafos
            $('#form-fotografos').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Selecciona fotógrafos del equipo...',
                allowClear: true,
                dropdownParent: $('#anuarioModal')
            });

            // Cargar datos iniciales
            loadTable();
            loadFotografos();
        });
    </script>
</body>

</html>
