<?php
include('../../assets/php/template.php');
$temp = new Template('Galería');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('galeria', 'ver')) {
    echo "No tienes permiso para acceder a este módulo";
    exit();
}

// Verificar permisos específicos
$puede_crear = $temp->tiene_permiso('galeria', 'crear');
$puede_editar = $temp->tiene_permiso('galeria', 'editar');
$puede_eliminar = $temp->tiene_permiso('galeria', 'eliminar');
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }, "showSettings":true}'>

<head>
    <?php $temp->head() ?>
    <style>
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .galeria-card {
            border: none;
            border-radius: 15px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .galeria-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }

        .galeria-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f5f5f5;
            cursor: pointer;
        }

        .galeria-info {
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .galeria-titulo {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .galeria-modulo {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .galeria-registro {
            font-size: 12px;
            color: #007bff;
            margin-bottom: 8px;
        }

        .galeria-tipo {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .tipo-principal {
            background: #28a745;
            color: white;
        }

        .tipo-galeria {
            background: #007bff;
            color: white;
        }

        .tipo-banner {
            background: #ffc107;
            color: #333;
        }

        .tipo-responsable {
            background: #6f42c1;
            color: white;
        }

        .galeria-actions {
            display: flex;
            gap: 5px;
            margin-top: auto;
        }

        .galeria-actions button {
            flex: 1;
            padding: 6px 4px;
            font-size: 11px;
        }

        .sin-imagenes {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .badge-orden {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .galeria-imagen-container {
            position: relative;
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
                            <h1 class="mb-0 pb-0 display-4" id="title"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Galería</li>
                                </ul>
                            </nav>
                        </div>
                        <!-- Title End -->

                        <!-- Top Buttons Start -->
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end">
                            <?php if($puede_crear): ?>
                            <button type="button" class="btn btn-outline-primary btn-icon btn-icon-start w-100 w-md-auto" onclick="abrirModalSubir()">
                                <i data-acorn-icon="plus"></i>
                                <span>Subir Imagen</span>
                            </button>
                            <?php endif; ?>
                        </div>
                        <!-- Top Buttons End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Filtros Start -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3">Filtros</h6>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Módulo</label>
                                        <select id="filtroModulo" class="form-select">
                                            <option value="">Todos los módulos</option>
                                            <option value="clubes">Clubes</option>
                                            <option value="ministerios">Ministerios</option>
                                            <option value="deportes">Deportes</option>
                                            <option value="ligas">Ligas</option>
                                            <option value="instalaciones">Instalaciones</option>
                                            <option value="cocurriculares">Co-Curriculares</option>
                                            <option value="eventos">Eventos</option>
                                            <option value="banners">Banners</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Registro</label>
                                        <select id="filtroRegistro" class="form-select" disabled>
                                            <option value="">Selecciona módulo primero</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Tipo</label>
                                        <select id="filtroTipo" class="form-select">
                                            <option value="">Todos los tipos</option>
                                            <option value="principal">Principal</option>
                                            <option value="galeria">Galería</option>
                                            <option value="banner">Banner</option>
                                            <option value="responsable">Responsable</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">Estado</label>
                                        <select id="filtroActivo" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="S" selected>Activos</option>
                                            <option value="N">Inactivos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarGaleria()">
                                            <i data-acorn-icon="search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Filtros End -->

                <!-- Galería Grid Start -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="contadorImagenes" class="mb-3 text-muted">
                                    <small>Cargando...</small>
                                </div>
                                <div id="galeriaGrid" class="galeria-grid">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                                <div id="sinImagenes" class="sin-imagenes" style="display:none;">
                                    <i data-acorn-icon="image" data-acorn-size="64" class="text-muted"></i>
                                    <h5 class="mt-3">No hay imágenes</h5>
                                    <p>Selecciona diferentes filtros o sube una nueva imagen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Galería Grid End -->
            </div>
        </main>
        <!-- Layout Footer Start -->
        <?php $temp->footer() ?>
        <!-- Layout Footer End -->
    </div>

    <!-- Modal Subir Imagen -->
    <div class="modal fade" id="modalSubir" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subir Nueva Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSubir">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Módulo *</label>
                                <select id="subirModulo" name="modulo" class="form-select" required>
                                    <option value="">Selecciona módulo</option>
                                    <option value="clubes">Clubes</option>
                                    <option value="ministerios">Ministerios</option>
                                    <option value="deportes">Deportes</option>
                                    <option value="ligas">Ligas</option>
                                    <option value="instalaciones">Instalaciones</option>
                                    <option value="cocurriculares">Co-Curriculares</option>
                                    <option value="eventos">Eventos</option>
                                    <option value="banners">Banners</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registro *</label>
                                <select id="subirRegistro" name="id_registro" class="form-select" disabled required>
                                    <option value="">Selecciona módulo primero</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="galeria">Galería</option>
                                    <option value="principal">Principal</option>
                                    <option value="banner">Banner</option>
                                    <option value="responsable">Responsable</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orden</label>
                                <input type="number" name="orden" class="form-control" value="0" min="0">
                                <small class="text-muted">0 = automático</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" name="titulo" class="form-control" maxlength="200">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Imagen *</label>
                                <input type="file" id="archivoSubir" class="form-control" accept="image/*" required>
                                <input type="hidden" name="url_imagen" id="urlImagenSubir">
                                <div id="previewSubir" class="mt-2" style="display:none;">
                                    <img id="imgPreviewSubir" src="" style="max-width: 200px; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-acorn-icon="upload"></i> Subir Imagen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Imagen -->
    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditar">
                    <div class="modal-body">
                        <input type="hidden" id="editarId">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" id="editarTitulo" class="form-control" maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea id="editarDescripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select id="editarTipo" class="form-select">
                                <option value="galeria">Galería</option>
                                <option value="principal">Principal</option>
                                <option value="banner">Banner</option>
                                <option value="responsable">Responsable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" id="editarOrden" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select id="editarActivo" class="form-select">
                                <option value="S">Activo</option>
                                <option value="N">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Imagen Grande -->
    <div class="modal fade" id="modalVerImagen" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verImagenTitulo">Imagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="verImagenSrc" src="" style="max-width: 100%; max-height: 80vh;">
                    <div class="mt-3">
                        <p id="verImagenInfo" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Settings Modal Start -->
    <?php $temp->modalSettings() ?>
    <!-- Theme Settings Modal End -->

    <!-- Search Modal Start -->
    <?php $temp->modalSearch() ?>
    <!-- Search Modal End -->

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        const puedeEditar = <?php echo $puede_editar ? 'true' : 'false' ?>;
        const puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false' ?>;

        const modalSubir = new bootstrap.Modal(document.getElementById('modalSubir'));
        const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
        const modalVerImagen = new bootstrap.Modal(document.getElementById('modalVerImagen'));

        let imagenes = [];

        // Cargar galería
        function cargarGaleria() {
            const modulo = document.getElementById('filtroModulo').value;
            const id_registro = document.getElementById('filtroRegistro').value;
            const tipo = document.getElementById('filtroTipo').value;
            const activo = document.getElementById('filtroActivo').value;

            let url = siteURL + 'assets/API/galeria/listar.php?';
            if (modulo) url += 'modulo=' + modulo + '&';
            if (id_registro) url += 'id_registro=' + id_registro + '&';
            if (tipo) url += 'tipo=' + tipo + '&';
            if (activo) url += 'activo=' + activo + '&';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        imagenes = data.data;
                        mostrarGaleria(data.data);
                        document.getElementById('contadorImagenes').innerHTML =
                            `<small><i data-acorn-icon="image"></i> ${data.total} imagen${data.total != 1 ? 'es' : ''} encontrada${data.total != 1 ? 's' : ''}</small>`;

                        // Actualizar iconos
                        if (typeof acorn !== 'undefined') {
                            acorn.icons();
                        }
                    } else {
                        mostrarError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error al cargar la galería');
                });
        }

        // Mostrar galería
        function mostrarGaleria(data) {
            const grid = document.getElementById('galeriaGrid');
            const sinImagenes = document.getElementById('sinImagenes');

            if (!data || data.length === 0) {
                grid.innerHTML = '';
                sinImagenes.style.display = 'block';
                return;
            }

            sinImagenes.style.display = 'none';
            grid.innerHTML = '';

            data.forEach(img => {
                const card = crearTarjetaImagen(img);
                grid.appendChild(card);
            });

            // Actualizar iconos
            if (typeof acorn !== 'undefined') {
                acorn.icons();
            }
        }

        // Crear tarjeta de imagen
        function crearTarjetaImagen(img) {
            const div = document.createElement('div');
            div.className = 'galeria-card';

            const tipoClass = 'tipo-' + img.TIPO;
            const tipoTexto = {
                'principal': 'Principal',
                'galeria': 'Galería',
                'banner': 'Banner',
                'responsable': 'Responsable'
            }[img.TIPO] || img.TIPO;

            const estadoBadge = img.ACTIVO == 'S'
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-secondary">Inactivo</span>';

            div.innerHTML = `
                <div class="galeria-imagen-container">
                    <img src="${img.URL_IMAGEN}" class="galeria-imagen" onclick="verImagenGrande(${img.ID})" onerror="this.src='${siteURL}assets/img/placeholder.png'">
                    <span class="badge-orden">#${img.ORDEN}</span>
                </div>
                <div class="galeria-info">
                    <div class="galeria-titulo">${img.TITULO || 'Sin título'}</div>
                    <div class="galeria-modulo">
                        <strong>${img.MODULO.toUpperCase()}</strong>
                        ${estadoBadge}
                    </div>
                    <div class="galeria-registro">${img.NOMBRE_REGISTRO || 'Registro #' + img.ID_REGISTRO}</div>
                    <span class="galeria-tipo ${tipoClass}">${tipoTexto}</span>
                    <div class="galeria-actions">
                        <button class="btn btn-sm btn-icon btn-icon-start btn-outline-success ms-1" type="button" onclick="verImagenGrande(${img.ID})">
                            <i class="fa fa-eye"></i>
                            <span class="d-none d-xxl-inline-block">Ver</span>
                        </button>
                        ${puedeEditar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="editarImagen(${img.ID})">
                            <i class="fa fa-edit"></i>
                            <span class="d-none d-xxl-inline-block">Editar</span>
                        </button>` : ''}
                        ${puedeEliminar ? `<button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="eliminarImagen(${img.ID}, '${(img.TITULO || 'Imagen').replace(/'/g, "\\'")}')">
                            <i class="fa fa-trash"></i>
                            <span class="d-none d-xxl-inline-block">Eliminar</span>
                        </button>` : ''}
                    </div>
                </div>
            `;

            return div;
        }

        // Abrir modal subir
        function abrirModalSubir() {
            document.getElementById('formSubir').reset();
            document.getElementById('previewSubir').style.display = 'none';
            document.getElementById('subirRegistro').disabled = true;
            document.getElementById('subirRegistro').innerHTML = '<option value="">Selecciona módulo primero</option>';
            modalSubir.show();
        }

        // Cargar registros según módulo (para filtro y subir)
        function cargarRegistrosPorModulo(moduloId, selectId) {
            const modulo = document.getElementById(moduloId).value;
            const select = document.getElementById(selectId);

            if (!modulo) {
                select.disabled = true;
                select.innerHTML = '<option value="">Selecciona módulo primero</option>';
                return;
            }

            // Mapear módulo a tabla/API
            const apis = {
                'clubes': 'clubes/listar.php',
                'ministerios': 'ministerios/listar.php',
                'deportes': 'deportes/listar.php',
                'ligas': 'ligas/listar.php',
                'instalaciones': 'instalaciones/listar.php',
                'cocurriculares': 'cocurriculares/listar.php',
                'eventos': 'eventos/listar.php',
                'banners': 'banners/listar.php'
            };

            const apiUrl = apis[modulo];
            if (!apiUrl) return;

            fetch(siteURL + 'assets/API/' + apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success == 1) {
                        select.disabled = false;
                        select.innerHTML = '<option value="">Todos los registros</option>';
                        data.data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.ID;
                            // Eventos y banners usan TITULO en lugar de NOMBRE
                            const nombre = item.NOMBRE || item.TITULO || `ID: ${item.ID}`;
                            option.textContent = nombre;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Event listeners para cambios de módulo
        document.getElementById('filtroModulo').addEventListener('change', function() {
            cargarRegistrosPorModulo('filtroModulo', 'filtroRegistro');
        });

        document.getElementById('subirModulo').addEventListener('change', function() {
            cargarRegistrosPorModulo('subirModulo', 'subirRegistro');
        });

        // Preview de imagen al subir
        document.getElementById('archivoSubir').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreviewSubir').src = e.target.result;
                document.getElementById('previewSubir').style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Subir archivo
            const formData = new FormData();
            formData.append('archivo', file);
            formData.append('tipo', 'galeria');

            fetch(siteURL + 'assets/API/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    const url = data.url || data.url_relativa;
                    document.getElementById('urlImagenSubir').value = url;
                    mostrarExito('Archivo subido correctamente');
                } else {
                    mostrarError('Error al subir archivo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al subir el archivo');
            });
        });

        // Submit formulario subir
        document.getElementById('formSubir').addEventListener('submit', function(e) {
            e.preventDefault();

            const urlImagen = document.getElementById('urlImagenSubir').value;
            if (!urlImagen) {
                mostrarError('Primero sube una imagen');
                return;
            }

            const formData = new FormData(this);

            fetch(siteURL + 'assets/API/galeria/subir.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    mostrarExito(data.message);
                    modalSubir.hide();
                    cargarGaleria();
                } else {
                    mostrarError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al subir la imagen');
            });
        });

        // Editar imagen
        function editarImagen(id) {
            const img = imagenes.find(i => i.ID == id);
            if (!img) {
                mostrarError('Imagen no encontrada');
                return;
            }

            document.getElementById('editarId').value = img.ID;
            document.getElementById('editarTitulo').value = img.TITULO || '';
            document.getElementById('editarDescripcion').value = img.DESCRIPCION || '';
            document.getElementById('editarTipo').value = img.TIPO;
            document.getElementById('editarOrden').value = img.ORDEN;
            document.getElementById('editarActivo').value = img.ACTIVO;

            modalEditar.show();
        }

        // Submit formulario editar
        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('id', document.getElementById('editarId').value);
            formData.append('titulo', document.getElementById('editarTitulo').value);
            formData.append('descripcion', document.getElementById('editarDescripcion').value);
            formData.append('tipo', document.getElementById('editarTipo').value);
            formData.append('orden', document.getElementById('editarOrden').value);
            formData.append('activo', document.getElementById('editarActivo').value);

            fetch(siteURL + 'assets/API/galeria/editar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    mostrarExito(data.message);
                    modalEditar.hide();
                    cargarGaleria();
                } else {
                    mostrarError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al actualizar la imagen');
            });
        });

        // Eliminar imagen
        function eliminarImagen(id, titulo) {
            if (!confirm(`¿Eliminar la imagen "${titulo}"?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch(siteURL + 'assets/API/galeria/eliminar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    mostrarExito(data.message);
                    cargarGaleria();
                } else {
                    mostrarError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al eliminar la imagen');
            });
        }

        // Ver imagen grande
        function verImagenGrande(id) {
            const img = imagenes.find(i => i.ID == id);
            if (!img) return;

            document.getElementById('verImagenTitulo').textContent = img.TITULO || 'Imagen';
            document.getElementById('verImagenSrc').src = img.URL_IMAGEN;
            document.getElementById('verImagenInfo').innerHTML = `
                <strong>Módulo:</strong> ${img.MODULO} |
                <strong>Registro:</strong> ${img.NOMBRE_REGISTRO || img.ID_REGISTRO} |
                <strong>Tipo:</strong> ${img.TIPO} |
                <strong>Orden:</strong> ${img.ORDEN}
                ${img.DESCRIPCION ? '<br>' + img.DESCRIPCION : ''}
            `;

            modalVerImagen.show();
        }

        // Notificaciones
        function mostrarExito(mensaje) {
            jQuery.notify({
                title: 'Éxito',
                message: mensaje
            }, { type: 'success' });
        }

        function mostrarError(mensaje) {
            jQuery.notify({
                title: 'Error',
                message: mensaje
            }, { type: 'danger' });
        }

        // Cargar al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarGaleria();
        });
    </script>
</body>

</html>
