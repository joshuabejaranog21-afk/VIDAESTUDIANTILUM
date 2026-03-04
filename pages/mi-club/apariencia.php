<?php
include('../../assets/php/template.php');
$temp = new Template('Apariencia del Club');
$db   = new Conexion();

if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

if (!$temp->es_director_club()) {
    echo "Acceso denegado. Solo para directores de club.";
    exit();
}

$club = $temp->obtener_club_asignado();
if (!$club) {
    echo "No tienes un club asignado.";
    exit();
}

$club_id = intval($club['ID']);

// Obtener TODAS las imágenes (activas e inactivas) para gestionarlas
$imagenes_q = $db->query("
    SELECT ID, URL_IMAGEN, TIPO, TITULO, ORDEN, ACTIVO, FECHA_SUBIDA
    FROM VRE_GALERIA
    WHERE MODULO = 'clubes' AND ID_REGISTRO = $club_id
    ORDER BY TIPO ASC, ORDEN ASC, FECHA_SUBIDA DESC
");

$imagen_principal = null;
$fotos_galeria    = [];

if ($imagenes_q) {
    while ($img = $imagenes_q->fetch_assoc()) {
        if ($img['TIPO'] === 'principal') {
            $imagen_principal = $img;
        } elseif ($img['TIPO'] === 'galeria') {
            $fotos_galeria[] = $img;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true">
<head>
    <?php $temp->head() ?>
    <style>
        /* ---- Preview pública ---- */
        .preview-card {
            border: 2px dashed #ccc;
            border-radius: 12px;
            padding: 1rem;
            background: #f8f9fa;
            max-width: 260px;
        }
        .preview-card .preview-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 0.5rem;
            border: 1px solid #e0e0e0;
        }
        .preview-card .preview-nombre {
            font-weight: 700;
            color: #5e35b1;
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        /* ---- Imagen principal ---- */
        .img-principal-wrap {
            position: relative;
            display: inline-block;
        }
        .img-principal-wrap img {
            width: 220px;
            height: 160px;
            object-fit: contain;
            border-radius: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 0.5rem;
        }
        .img-principal-wrap .badge-principal {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #5e35b1;
            color: #fff;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 20px;
        }

        /* ---- Grid galería ---- */
        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }
        .foto-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            position: relative;
            transition: box-shadow 0.2s;
            cursor: grab;
        }
        .foto-card:active { cursor: grabbing; }
        .foto-card.inactiva { opacity: 0.45; }
        .foto-card .foto-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
            pointer-events: none;
        }
        .foto-card .foto-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 8px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }
        .foto-card .drag-handle {
            color: #bbb;
            cursor: grab;
            font-size: 1.1rem;
            padding: 0 4px;
        }
        .foto-card .drag-handle:active { cursor: grabbing; }
        .sortable-ghost {
            opacity: 0.3;
            border: 2px dashed #5e35b1 !important;
        }
        .orden-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 0.7rem;
            padding: 2px 7px;
            border-radius: 20px;
        }
        .status-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            font-size: 0.7rem;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* ---- Zona vacía ---- */
        .zona-vacia {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            color: #aaa;
        }

        /* ---- Feedback toast ---- */
        #toast-apariencia {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            min-width: 220px;
        }
    </style>
</head>
<body>
<div id="root">
    <?php $temp->nav() ?>
    <main>
        <div class="container">
            <div class="page-title-container mb-4">
                <div class="row">
                    <div class="col">
                        <h1 class="mb-0 pb-0 display-4">Apariencia del Club</h1>
                        <nav class="breadcrumb-container d-inline-flex" aria-label="breadcrumb">
                            <ol class="breadcrumb pt-0">
                                <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL; ?>pages/mi-club/">Mi Club</a></li>
                                <li class="breadcrumb-item active">Apariencia</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <a href="http://localhost:8888/sitio/detalle-club.php?id=<?php echo $club_id; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i data-acorn-icon="eye" class="me-1"></i> Ver en el sitio
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- ===================== COLUMNA IZQUIERDA ===================== -->
                <div class="col-xl-8">

                    <!-- IMAGEN PRINCIPAL -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-acorn-icon="image" class="me-2 text-primary"></i>
                                Logo / Imagen Principal
                            </h5>
                            <p class="text-muted small mb-0 mt-1">Es la imagen que aparece en la lista de clubes del sitio web</p>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-4 flex-wrap">
                                <div>
                                    <div class="img-principal-wrap" id="wrapPrincipal">
                                        <?php if ($imagen_principal): ?>
                                            <img id="imgPrincipalPreview"
                                                 src="<?php echo htmlspecialchars($imagen_principal['URL_IMAGEN']); ?>"
                                                 alt="Imagen principal">
                                            <span class="badge-principal">Principal</span>
                                        <?php else: ?>
                                            <div id="imgPrincipalVacio" style="width:220px;height:160px;background:#f5f5f5;border-radius:10px;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;flex-direction:column;color:#aaa;">
                                                <i data-acorn-icon="image" style="font-size:2rem;"></i>
                                                <small class="mt-2">Sin imagen</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($imagen_principal): ?>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="eliminarImagen(<?php echo $imagen_principal['ID']; ?>, 'principal')">
                                                <i data-acorn-icon="bin"></i> Quitar logo
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-muted small mb-3">
                                        Sube el logo o imagen representativa del club.<br>
                                        Formatos: JPG, PNG, GIF &mdash; Máx. 5MB
                                    </p>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('filePrincipal').click()">
                                        <i data-acorn-icon="upload" class="me-1"></i>
                                        <?php echo $imagen_principal ? 'Cambiar imagen' : 'Subir imagen'; ?>
                                    </button>
                                    <input type="file" id="filePrincipal" accept="image/*" style="display:none;">
                                    <div id="loadingPrincipal" class="d-none mt-2">
                                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                        <small>Subiendo...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GALERÍA DE FOTOS -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-0">
                                    <i data-acorn-icon="gallery" class="me-2 text-primary"></i>
                                    Galería de Fotos
                                </h5>
                                <p class="text-muted small mb-0 mt-1">Arrastra para reordenar &bull; Ojo para mostrar/ocultar &bull; Las inactivas no aparecen en el sitio</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('fileGaleria').click()">
                                <i data-acorn-icon="plus" class="me-1"></i> Agregar foto
                            </button>
                            <input type="file" id="fileGaleria" accept="image/*" style="display:none;" multiple>
                        </div>
                        <div class="card-body">
                            <div class="galeria-grid" id="galeriaGrid">
                                <?php if (empty($fotos_galeria)): ?>
                                    <div class="zona-vacia" id="zonaVacia" style="grid-column: 1/-1;">
                                        <i data-acorn-icon="image" style="font-size:2.5rem;"></i>
                                        <p class="mt-2 mb-0">Aún no hay fotos en la galería.<br>Haz clic en "Agregar foto" para subir.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($fotos_galeria as $i => $foto): ?>
                                        <?php $activa = $foto['ACTIVO'] === 'S'; ?>
                                        <div class="foto-card <?php echo $activa ? '' : 'inactiva'; ?>"
                                             data-id="<?php echo $foto['ID']; ?>"
                                             data-activo="<?php echo $foto['ACTIVO']; ?>">
                                            <span class="orden-badge"><?php echo $i + 1; ?></span>
                                            <span class="status-badge <?php echo $activa ? 'bg-success' : 'bg-secondary'; ?> text-white">
                                                <?php echo $activa ? 'Visible' : 'Oculta'; ?>
                                            </span>
                                            <img class="foto-img"
                                                 src="<?php echo htmlspecialchars($foto['URL_IMAGEN']); ?>"
                                                 alt="Foto galería"
                                                 onerror="this.src='<?php echo $temp->siteURL; ?>assets/img/placeholder.jpg'">
                                            <div class="foto-acciones">
                                                <span class="drag-handle" title="Arrastra para reordenar">
                                                    <i data-acorn-icon="menu"></i>
                                                </span>
                                                <div>
                                                    <button type="button" class="btn btn-icon btn-icon-only btn-sm btn-flat-secondary"
                                                            title="<?php echo $activa ? 'Ocultar' : 'Mostrar'; ?>"
                                                            onclick="toggleFoto(this, <?php echo $foto['ID']; ?>)">
                                                        <i data-acorn-icon="<?php echo $activa ? 'eye' : 'eye-off'; ?>"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-icon btn-icon-only btn-sm btn-flat-danger"
                                                            title="Eliminar foto"
                                                            onclick="eliminarImagen(<?php echo $foto['ID']; ?>, 'galeria')">
                                                        <i data-acorn-icon="bin"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ===================== COLUMNA DERECHA: PREVIEW ===================== -->
                <div class="col-xl-4">
                    <div class="card sticky-top" style="top: 80px;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i data-acorn-icon="screen" class="me-2 text-primary"></i>
                                Vista previa en el sitio
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Así se verá la tarjeta de tu club en la página pública:</p>

                            <!-- Preview tarjeta club -->
                            <div class="preview-card mx-auto">
                                <div style="aspect-ratio:4/3; background:#f5f5f5; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:0.5rem; border:1px solid #e0e0e0; overflow:hidden;">
                                    <?php if ($imagen_principal): ?>
                                        <img id="previewPrincipalImg"
                                             src="<?php echo htmlspecialchars($imagen_principal['URL_IMAGEN']); ?>"
                                             style="max-width:100%; max-height:100%; object-fit:contain;">
                                    <?php else: ?>
                                        <div id="previewPrincipalVacio" style="text-align:center;color:#bbb;">
                                            <i data-acorn-icon="image" style="font-size:2rem;"></i><br>
                                            <small>Sin imagen</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="preview-nombre"><?php echo htmlspecialchars($club['NOMBRE']); ?></p>
                                <?php if (!empty($club['DESCRIPCION'])): ?>
                                    <p style="font-size:0.78rem; color:#777; margin:0;">
                                        <?php
                                        $desc = htmlspecialchars($club['DESCRIPCION']);
                                        echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc;
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Preview galería mini -->
                            <?php if (!empty($fotos_galeria)): ?>
                                <p class="text-muted small mt-4 mb-2">Galería en la página del club:</p>
                                <div style="display:flex; flex-wrap:wrap; gap:6px;" id="previewGaleriaGrid">
                                    <?php foreach ($fotos_galeria as $foto): ?>
                                        <?php if ($foto['ACTIVO'] === 'S'): ?>
                                            <img src="<?php echo htmlspecialchars($foto['URL_IMAGEN']); ?>"
                                                 style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #ddd;"
                                                 data-preview-id="<?php echo $foto['ID']; ?>"
                                                 onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <hr>
                            <a href="http://localhost:8888/sitio/detalle-club.php?id=<?php echo $club_id; ?>"
                               target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i data-acorn-icon="external-link" class="me-1"></i>
                                Abrir página del club
                            </a>
                        </div>
                    </div>
                </div>
            </div><!-- /row -->

        </div><!-- /container -->
    </main>
    <?php $temp->footer() ?>
</div>

<!-- Toast de feedback -->
<div id="toast-apariencia" class="toast align-items-center text-white border-0 d-none" role="alert" style="z-index:9999;">
    <div class="d-flex">
        <div class="toast-body" id="toastMsg">Guardado</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="cerrarToast()"></button>
    </div>
</div>

<script src="<?php echo $temp->siteURL; ?>assets/js/vendor/sortable.min.js"></script>
<script>
const apiURL = '<?php echo $temp->siteURL; ?>assets/API/mi-club/galeria-accion.php';
const uploadURL = '<?php echo $temp->siteURL; ?>assets/API/upload.php';

// ==============================
// TOAST
// ==============================
function mostrarToast(msg, tipo = 'success') {
    const toast = document.getElementById('toast-apariencia');
    toast.className = 'toast align-items-center text-white border-0';
    toast.classList.add(tipo === 'success' ? 'bg-success' : 'bg-danger');
    toast.classList.remove('d-none');
    document.getElementById('toastMsg').textContent = msg;
    setTimeout(() => toast.classList.add('d-none'), 2500);
}
function cerrarToast() {
    document.getElementById('toast-apariencia').classList.add('d-none');
}

// ==============================
// TOGGLE VISIBILIDAD FOTO
// ==============================
function toggleFoto(btn, id) {
    const card = btn.closest('.foto-card');
    const activo = card.dataset.activo === 'S' ? 'N' : 'S';

    fetch(apiURL, {
        method: 'POST',
        body: new URLSearchParams({ accion: 'toggle', id, activo })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            card.dataset.activo = activo;
            card.classList.toggle('inactiva', activo === 'N');

            const badge = card.querySelector('.status-badge');
            badge.textContent = activo === 'S' ? 'Visible' : 'Oculta';
            badge.className = 'status-badge text-white ' + (activo === 'S' ? 'bg-success' : 'bg-secondary');

            const icon = btn.querySelector('i');
            icon.setAttribute('data-acorn-icon', activo === 'S' ? 'eye' : 'eye-off');
            btn.title = activo === 'S' ? 'Ocultar' : 'Mostrar';

            if (typeof acorn !== 'undefined') acorn.icons();

            // Actualizar preview de galería
            actualizarPreviewGaleria();

            mostrarToast(activo === 'S' ? 'Foto visible en el sitio' : 'Foto oculta del sitio');
        } else {
            mostrarToast(data.message || 'Error', 'error');
        }
    })
    .catch(() => mostrarToast('Error de conexión', 'error'));
}

// ==============================
// ELIMINAR IMAGEN
// ==============================
function eliminarImagen(id, tipo) {
    if (!confirm('¿Eliminar esta imagen? Esta acción no se puede deshacer.')) return;

    fetch(apiURL, {
        method: 'POST',
        body: new URLSearchParams({ accion: 'eliminar', id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (tipo === 'principal') {
                // Recargar para actualizar la sección de imagen principal
                location.reload();
            } else {
                // Quitar la tarjeta del DOM
                const card = document.querySelector(`.foto-card[data-id="${id}"]`);
                if (card) card.remove();
                actualizarNumeros();
                actualizarPreviewGaleria();
                mostrarToast('Foto eliminada');
            }
        } else {
            mostrarToast(data.message || 'Error', 'error');
        }
    })
    .catch(() => mostrarToast('Error de conexión', 'error'));
}

// ==============================
// SORTABLE - Drag & Drop
// ==============================
const grid = document.getElementById('galeriaGrid');
if (grid) {
    Sortable.create(grid, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        handle: '.drag-handle',
        onEnd: function() {
            actualizarNumeros();
            guardarOrden();
        }
    });
}

function actualizarNumeros() {
    const cards = document.querySelectorAll('#galeriaGrid .foto-card');
    cards.forEach((card, i) => {
        const badge = card.querySelector('.orden-badge');
        if (badge) badge.textContent = i + 1;
    });
}

function guardarOrden() {
    const cards = document.querySelectorAll('#galeriaGrid .foto-card');
    const imagenes = Array.from(cards).map((card, i) => ({
        id: parseInt(card.dataset.id),
        orden: i + 1
    }));

    fetch(apiURL, {
        method: 'POST',
        body: new URLSearchParams({ accion: 'reordenar', imagenes: JSON.stringify(imagenes) })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mostrarToast('Orden guardado');
        } else {
            mostrarToast('Error al guardar orden', 'error');
        }
    })
    .catch(() => mostrarToast('Error de conexión', 'error'));
}

// ==============================
// SUBIR IMAGEN PRINCIPAL
// ==============================
document.getElementById('filePrincipal').addEventListener('change', function() {
    if (!this.files.length) return;
    const file = this.files[0];
    const loading = document.getElementById('loadingPrincipal');
    loading.classList.remove('d-none');

    const fd = new FormData();
    fd.append('archivo', file);
    fd.append('tipo', 'galeria_club');

    fetch(uploadURL, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success == 1) {
            const url = data.url || data.url_relativa;
            // Registrar en VRE_GALERIA como principal
            return fetch(apiURL, {
                method: 'POST',
                body: new URLSearchParams({ accion: 'subir_galeria', url, tipo: 'principal' })
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    loading.classList.add('d-none');
                    // Actualizar preview
                    let previewImg = document.getElementById('previewPrincipalImg');
                    let previewVacio = document.getElementById('previewPrincipalVacio');
                    if (previewVacio) previewVacio.remove();
                    if (!previewImg) {
                        // Crear el img en el preview
                        const container = document.querySelector('.preview-card div');
                        previewImg = document.createElement('img');
                        previewImg.id = 'previewPrincipalImg';
                        previewImg.style = 'max-width:100%;max-height:100%;object-fit:contain;';
                        container.appendChild(previewImg);
                    }
                    previewImg.src = url;
                    mostrarToast('Logo actualizado');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        } else {
            loading.classList.add('d-none');
            mostrarToast(data.message || 'Error al subir', 'error');
        }
    })
    .catch(() => {
        loading.classList.add('d-none');
        mostrarToast('Error de conexión', 'error');
    });
    this.value = '';
});

// ==============================
// SUBIR FOTO A GALERÍA
// ==============================
document.getElementById('fileGaleria').addEventListener('change', function() {
    const files = Array.from(this.files);
    if (!files.length) return;

    let subidos = 0;
    files.forEach(file => {
        const fd = new FormData();
        fd.append('archivo', file);
        fd.append('tipo', 'galeria_club');

        fetch(uploadURL, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success == 1) {
                const url = data.url || data.url_relativa;
                return fetch(apiURL, {
                    method: 'POST',
                    body: new URLSearchParams({ accion: 'subir_galeria', url, tipo: 'galeria' })
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        agregarFotoDOM(res.id, url);
                        subidos++;
                        if (subidos === files.length) {
                            mostrarToast(subidos === 1 ? 'Foto agregada' : `${subidos} fotos agregadas`);
                        }
                    }
                });
            } else {
                mostrarToast('Error al subir imagen', 'error');
            }
        })
        .catch(() => mostrarToast('Error de conexión', 'error'));
    });
    this.value = '';
});

function agregarFotoDOM(id, url) {
    const zonaVacia = document.getElementById('zonaVacia');
    if (zonaVacia) zonaVacia.remove();

    const cards = document.querySelectorAll('#galeriaGrid .foto-card');
    const numero = cards.length + 1;

    const div = document.createElement('div');
    div.className = 'foto-card';
    div.dataset.id = id;
    div.dataset.activo = 'S';
    div.innerHTML = `
        <span class="orden-badge">${numero}</span>
        <span class="status-badge bg-success text-white">Visible</span>
        <img class="foto-img" src="${url}" alt="Foto galería" onerror="this.src='<?php echo $temp->siteURL; ?>assets/img/placeholder.jpg'">
        <div class="foto-acciones">
            <span class="drag-handle" title="Arrastra para reordenar">
                <i data-acorn-icon="menu"></i>
            </span>
            <div>
                <button type="button" class="btn btn-icon btn-icon-only btn-sm btn-flat-secondary"
                        title="Ocultar" onclick="toggleFoto(this, ${id})">
                    <i data-acorn-icon="eye"></i>
                </button>
                <button type="button" class="btn btn-icon btn-icon-only btn-sm btn-flat-danger"
                        title="Eliminar foto" onclick="eliminarImagen(${id}, 'galeria')">
                    <i data-acorn-icon="bin"></i>
                </button>
            </div>
        </div>
    `;
    grid.appendChild(div);

    if (typeof acorn !== 'undefined') acorn.icons();
    actualizarPreviewGaleria();
}

// ==============================
// ACTUALIZAR PREVIEW GALERÍA
// ==============================
function actualizarPreviewGaleria() {
    const previewGrid = document.getElementById('previewGaleriaGrid');
    if (!previewGrid) return;

    const cards = document.querySelectorAll('#galeriaGrid .foto-card');
    previewGrid.innerHTML = '';
    cards.forEach(card => {
        if (card.dataset.activo === 'S') {
            const img = card.querySelector('.foto-img');
            if (img) {
                const mini = document.createElement('img');
                mini.src = img.src;
                mini.style = 'width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;';
                mini.dataset.previewId = card.dataset.id;
                previewGrid.appendChild(mini);
            }
        }
    });
}
</script>
</body>
</html>
