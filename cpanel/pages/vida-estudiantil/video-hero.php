<?php
include('../../assets/php/template.php');
$temp = new Template('Video Hero — Vida Estudiantil');

if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

$videoDir  = dirname(__DIR__, 3) . '/vidaEstudiantil/assets/videos/';
$videoPath = $videoDir . 'hero.mp4';
$videoWebm = $videoDir . 'hero.webm';
$urlFile   = $videoDir . 'hero-url.txt';

$tieneVideo = file_exists($videoPath) || file_exists($videoWebm);
$tieneURL   = file_exists($urlFile) && trim(file_get_contents($urlFile)) !== '';
$urlGuardada = $tieneURL ? trim(file_get_contents($urlFile)) : '';

$videoActual = null;
$videoExt    = null;
if (file_exists($videoPath)) {
    $videoActual = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.mp4';
    $videoExt    = 'mp4';
} elseif (file_exists($videoWebm)) {
    $videoActual = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.webm';
    $videoExt    = 'webm';
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"attributes": {"placement": "vertical"}}'>
<head>
    <?php $temp->head() ?>
    <style>
        .video-preview-wrap {
            background: #000;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
            max-height: 360px;
        }
        .video-preview-wrap video,
        .video-preview-wrap iframe {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
            display: block;
        }
        .drop-zone {
            border: 2px dashed #adb5bd;
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .drop-zone.drag-over {
            border-color: var(--primary);
            background: rgba(94,114,228,.05);
        }
        .drop-zone input[type=file] { display: none; }
        #progressWrap { display: none; }
        .nav-tabs .nav-link { font-weight: 600; }
    </style>
</head>
<body>
<div id="root">
    <?php $temp->nav() ?>
    <main>
        <div class="container">

            <!-- Título -->
            <div class="page-title-container mb-4">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <h1 class="mb-0 pb-0 display-4">Video Hero</h1>
                        <nav class="breadcrumb-container d-inline-block">
                            <ul class="breadcrumb pt-0">
                                <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                <li class="breadcrumb-item">Vida Estudiantil</li>
                                <li class="breadcrumb-item active">Video Hero</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <!-- Columna izquierda: subida -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Configurar video</h5>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-4" id="videoTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo !$tieneURL ? 'active' : ''; ?>"
                                            id="tab-upload" data-bs-toggle="tab" data-bs-target="#pane-upload"
                                            type="button" role="tab">
                                        <i class="fas fa-upload me-2"></i>Subir archivo
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?php echo $tieneURL ? 'active' : ''; ?>"
                                            id="tab-url" data-bs-toggle="tab" data-bs-target="#pane-url"
                                            type="button" role="tab">
                                        <i class="fas fa-link me-2"></i>Usar URL
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">

                                <!-- Panel: Subir archivo -->
                                <div class="tab-pane fade <?php echo !$tieneURL ? 'show active' : ''; ?>"
                                     id="pane-upload" role="tabpanel">
                                    <p class="text-muted small mb-4">
                                        Formatos: <strong>MP4</strong> o <strong>WebM</strong> · Máximo <strong>200 MB</strong><br>
                                        Recomendado: 1920×1080, duración 15–30 s, sin audio.
                                    </p>

                                    <div class="drop-zone" id="dropZone" onclick="document.getElementById('videoInput').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3 d-block"></i>
                                        <p class="mb-1 fw-semibold">Arrastra aquí tu video o haz clic para seleccionarlo</p>
                                        <p class="text-muted small mb-0">MP4 / WebM · hasta 200 MB</p>
                                        <input type="file" id="videoInput" accept="video/mp4,video/webm,.mp4,.webm">
                                    </div>

                                    <!-- Progreso -->
                                    <div id="progressWrap" class="mt-3">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span id="progressLabel">Subiendo…</span>
                                            <span id="progressPct">0%</span>
                                        </div>
                                        <div class="progress" style="height:8px;">
                                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                                 role="progressbar" style="width:0%"></div>
                                        </div>
                                    </div>

                                    <button id="btnSubir" class="btn btn-primary mt-3 w-100" disabled onclick="subirVideo()">
                                        <i class="fas fa-upload me-2"></i>Subir video
                                    </button>
                                </div>

                                <!-- Panel: URL externa -->
                                <div class="tab-pane fade <?php echo $tieneURL ? 'show active' : ''; ?>"
                                     id="pane-url" role="tabpanel">
                                    <p class="text-muted small mb-3">
                                        Ingresa la URL directa de un video <strong>MP4 o WebM</strong> (ej: desde tu servidor o CDN).<br>
                                        También puedes pegar la URL de un video de <strong>YouTube</strong> para incrustarlo.
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">URL del video</label>
                                        <input type="url" id="videoURL" class="form-control"
                                               placeholder="https://ejemplo.com/video.mp4  o  https://youtu.be/..."
                                               value="<?php echo htmlspecialchars($urlGuardada); ?>">
                                        <div class="form-text">Pega la URL completa del video.</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary flex-grow-1" onclick="guardarURL()">
                                            <i class="fas fa-save me-2"></i>Guardar URL
                                        </button>
                                        <?php if ($tieneURL): ?>
                                        <button class="btn btn-outline-danger" onclick="eliminarURL()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <div id="urlMsg" class="mt-2"></div>
                                </div>

                            </div><!-- /tab-content -->
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: video actual -->
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Video actual</h5>

                            <div id="videoActualWrap">
                                <?php if ($tieneVideo): ?>
                                <div class="video-preview-wrap mb-3">
                                    <video autoplay muted loop playsinline>
                                        <source src="<?php echo $videoActual; ?>" type="video/<?php echo $videoExt; ?>">
                                    </video>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Archivo activo (<?php echo strtoupper($videoExt); ?>)
                                    </span>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarVideo()">
                                        <i class="fas fa-trash me-1"></i>Eliminar
                                    </button>
                                </div>
                                <?php elseif ($tieneURL): ?>
                                <div class="video-preview-wrap mb-3" id="urlPreviewWrap">
                                    <?php
                                    // Detectar si es YouTube
                                    $ytId = null;
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]{11})/', $urlGuardada, $m)) {
                                        $ytId = $m[1];
                                    }
                                    ?>
                                    <?php if ($ytId): ?>
                                        <iframe src="https://www.youtube.com/embed/<?php echo $ytId; ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo $ytId; ?>"
                                                style="width:100%;height:360px;border:0;"
                                                allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                    <?php else: ?>
                                        <video autoplay muted loop playsinline>
                                            <source src="<?php echo htmlspecialchars($urlGuardada); ?>">
                                        </video>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-info">
                                        <i class="fas fa-link me-1"></i>URL activa
                                    </span>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarURL()">
                                        <i class="fas fa-trash me-1"></i>Quitar URL
                                    </button>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-5 text-muted" id="sinVideoMsg">
                                    <i class="fas fa-film fa-3x mb-3 d-block opacity-25"></i>
                                    <p class="mb-0">No hay video activo.<br>El hero mostrará el fondo de gradiente.</p>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>

            </div><!-- /row -->
        </div><!-- /container -->
    </main>
</div>

<script>
const API_URL = '<?php echo $temp->siteURL ?>assets/API/vida-estudiantil/video-hero.php';
let archivoSeleccionado = null;

// ── Drag & Drop ──
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    const f = e.dataTransfer.files[0];
    if (f) seleccionarArchivo(f);
});

document.getElementById('videoInput').addEventListener('change', e => {
    if (e.target.files[0]) seleccionarArchivo(e.target.files[0]);
});

function seleccionarArchivo(f) {
    const ext = f.name.split('.').pop().toLowerCase();
    if (!['mp4','webm'].includes(ext)) {
        alert('Solo se permiten archivos .mp4 o .webm');
        return;
    }
    if (f.size > 200 * 1024 * 1024) {
        alert('El archivo supera 200 MB');
        return;
    }
    archivoSeleccionado = f;
    dropZone.innerHTML = `<i class="fas fa-file-video fa-2x text-primary mb-2 d-block"></i>
        <p class="mb-0 fw-semibold">${f.name}</p>
        <p class="text-muted small">${(f.size/1024/1024).toFixed(1)} MB · ${ext.toUpperCase()}</p>`;
    document.getElementById('btnSubir').disabled = false;
}

function subirVideo() {
    if (!archivoSeleccionado) return;

    const fd = new FormData();
    fd.append('video', archivoSeleccionado);

    const progressWrap = document.getElementById('progressWrap');
    const bar   = document.getElementById('progressBar');
    const pct   = document.getElementById('progressPct');
    const label = document.getElementById('progressLabel');

    progressWrap.style.display = 'block';
    document.getElementById('btnSubir').disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', API_URL, true);

    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            const p = Math.round(e.loaded / e.total * 100);
            bar.style.width = p + '%';
            pct.textContent = p + '%';
        }
    };

    xhr.onload = () => {
        try {
            const res = JSON.parse(xhr.responseText);
            if (res.success) {
                label.textContent = '¡Video subido!';
                bar.classList.remove('progress-bar-animated','progress-bar-striped');
                bar.classList.add('bg-success');
                setTimeout(() => location.reload(), 1200);
            } else {
                label.textContent = 'Error: ' + res.message;
                bar.classList.add('bg-danger');
                document.getElementById('btnSubir').disabled = false;
            }
        } catch(e) {
            label.textContent = 'Respuesta inesperada del servidor';
            bar.classList.add('bg-danger');
            document.getElementById('btnSubir').disabled = false;
        }
    };

    xhr.onerror = () => {
        label.textContent = 'Error de red';
        bar.classList.add('bg-danger');
        document.getElementById('btnSubir').disabled = false;
    };

    xhr.send(fd);
}

function eliminarVideo() {
    if (!confirm('¿Eliminar el video del hero? El fondo volverá al gradiente.')) return;

    fetch(API_URL, { method: 'DELETE' })
        .then(r => r.json())
        .then(res => {
            if (res.success) location.reload();
            else alert('Error: ' + res.message);
        })
        .catch(() => alert('Error de red'));
}

// ── URL ──
function guardarURL() {
    const url = document.getElementById('videoURL').value.trim();
    if (!url) { alert('Ingresa una URL válida'); return; }

    fetch(API_URL + '?action=save-url', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ video_url: url })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('urlMsg').innerHTML =
                '<div class="alert alert-success py-2 mb-0">URL guardada. Recargando…</div>';
            setTimeout(() => location.reload(), 1000);
        } else {
            document.getElementById('urlMsg').innerHTML =
                '<div class="alert alert-danger py-2 mb-0">Error: ' + res.message + '</div>';
        }
    })
    .catch(() => {
        document.getElementById('urlMsg').innerHTML =
            '<div class="alert alert-danger py-2 mb-0">Error de red</div>';
    });
}

function eliminarURL() {
    if (!confirm('¿Quitar la URL del video? El hero volverá al gradiente.')) return;

    fetch(API_URL + '?action=delete-url', { method: 'DELETE' })
        .then(r => r.json())
        .then(res => {
            if (res.success) location.reload();
            else alert('Error: ' + res.message);
        })
        .catch(() => alert('Error de red'));
}
</script>

<?php $temp->footer() ?>
<?php $temp->modalSettings() ?>
<?php $temp->modalSearch() ?>
<?php $temp->scripts() ?>
</body>
</html>
