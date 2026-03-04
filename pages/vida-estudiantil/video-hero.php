<?php
include('../../assets/php/template.php');
$temp = new Template('Video Hero — Vida Estudiantil');

if (!$temp->validate_session()) {
    echo "Sin sesión activa.";
    exit();
}

$videoPath = __DIR__ . '/../../vidaEstudiantil/assets/videos/hero.mp4';
$videoWebm = __DIR__ . '/../../vidaEstudiantil/assets/videos/hero.webm';
$tieneVideo = file_exists($videoPath) || file_exists($videoWebm);

$videoActual = null;
$videoExt    = null;
if (file_exists($videoPath)) { $videoActual = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.mp4'; $videoExt = 'mp4'; }
elseif (file_exists($videoWebm)) { $videoActual = '/cpanel/cpanel_Hithan-main/vidaEstudiantil/assets/videos/hero.webm'; $videoExt = 'webm'; }
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
        .video-preview-wrap video {
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
                            <h5 class="card-title mb-1">Subir nuevo video</h5>
                            <p class="text-muted small mb-4">Formatos: <strong>MP4</strong> o <strong>WebM</strong> · Máximo <strong>200 MB</strong><br>
                                Recomendado: 1920×1080, duración 15–30 s, sin audio.</p>

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

                            <!-- Botón subir -->
                            <button id="btnSubir" class="btn btn-primary mt-3 w-100" disabled onclick="subirVideo()">
                                <i class="fas fa-upload me-2"></i>Subir video
                            </button>
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
                                        <i class="fas fa-check-circle me-1"></i>Video activo (<?php echo strtoupper($videoExt); ?>)
                                    </span>
                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarVideo()">
                                        <i class="fas fa-trash me-1"></i>Eliminar
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
</script>

<?php $temp->footer() ?>
</body>
</html>
