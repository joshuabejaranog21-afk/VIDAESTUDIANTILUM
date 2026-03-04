<?php
include('../../../assets/php/template.php');
$temp = new Template('Extraer Fotos de Flickr');
if (!$temp->validate_session(2)) { // Solo admin o superior
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":true,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <style>
        .photo-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .photo-item img {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }
        .url-input {
            font-size: 11px;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div id="root">
        <?php $temp->nav() ?>

        <main>
            <div class="container">
                <!-- Title -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4">Extraer Fotos de Flickr</h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>pages/anuarios/admin/">Anuarios</a></li>
                                    <li class="breadcrumb-item active">Extraer de Flickr</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Instrucciones</h5>
                            <ol class="mb-0">
                                <li>Abre tu álbum de Flickr en otra pestaña</li>
                                <li>Abre las herramientas de desarrollo (F12)</li>
                                <li>Ve a la pestaña "Console"</li>
                                <li>Copia y pega el script de abajo</li>
                                <li>Copia el resultado y pégalo aquí</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Script Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">1. Script para copiar en Flickr (F12 → Console)</h5>
                            </div>
                            <div class="card-body">
                                <pre id="flickrScript" class="bg-light p-3" style="max-height: 300px; overflow-y: auto;"><code>// Script para extraer fotos de álbum de Flickr
// Ejecuta esto en la consola de Flickr (F12)

(() => {
    const photos = [];

    // Método 1: Buscar en thumbs
    document.querySelectorAll('img[src*="staticflickr"]').forEach(img => {
        let src = img.src;
        // Convertir thumbnail a imagen grande
        src = src.replace('_m.jpg', '_b.jpg')
                 .replace('_n.jpg', '_b.jpg')
                 .replace('_s.jpg', '_b.jpg')
                 .replace('_t.jpg', '_b.jpg')
                 .replace('_q.jpg', '_b.jpg');

        if (!photos.includes(src)) {
            photos.push(src);
        }
    });

    // Método 2: Buscar en enlaces de fotos
    document.querySelectorAll('a[href*="/photos/"]').forEach(link => {
        const href = link.href;
        if (href.match(/\/photos\/[^\/]+\/\d+\/?$/)) {
            // Extraer el ID de la foto
            const match = href.match(/\/(\d+)\/?$/);
            if (match) {
                const photoId = match[1];
                console.log('Foto ID encontrado:', photoId);
            }
        }
    });

    console.log('=== RESULTADO - COPIA TODO LO DE ABAJO ===');
    console.log(photos.join('\n'));
    console.log('=== TOTAL: ' + photos.length + ' fotos ===');

    // También copiar al portapapeles si es posible
    if (navigator.clipboard) {
        navigator.clipboard.writeText(photos.join('\n')).then(() => {
            console.log('✅ URLs copiadas al portapapeles!');
        });
    }

    return photos;
})();
</code></pre>
                                <button class="btn btn-primary" onclick="copyScript()">
                                    <i class="fa fa-copy"></i> Copiar Script
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Input Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">2. Pega las URLs aquí (una por línea)</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" id="urlsInput" rows="10"
                                    placeholder="Pega aquí las URLs de las fotos, una por línea...&#10;Ejemplo:&#10;https://live.staticflickr.com/65535/123_abc.jpg&#10;https://live.staticflickr.com/65535/456_def.jpg"></textarea>
                                <div class="mt-3">
                                    <button class="btn btn-success" onclick="processUrls()">
                                        <i class="fa fa-check"></i> Procesar URLs
                                    </button>
                                    <button class="btn btn-secondary" onclick="clearAll()">
                                        <i class="fa fa-trash"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="row mb-4" id="resultsSection" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">3. Fotos Detectadas</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success" id="statsAlert"></div>

                                <div class="mb-3">
                                    <label class="form-label">Anuario al que pertenecen estas fotos:</label>
                                    <select class="form-select" id="selectAnuario">
                                        <option value="">Seleccionar anuario...</option>
                                    </select>
                                </div>

                                <div id="photosList"></div>

                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="exportToCSV()">
                                        <i class="fa fa-download"></i> Exportar a CSV
                                    </button>
                                    <button class="btn btn-warning" onclick="copyCSVToClipboard()">
                                        <i class="fa fa-copy"></i> Copiar CSV al Portapapeles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alternative Method -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0">Método Alternativo (Manual pero más confiable)</h5>
                            </div>
                            <div class="card-body">
                                <h6>Si el script no funciona, haz esto:</h6>
                                <ol>
                                    <li>Abre tu álbum en Flickr: <code>https://www.flickr.com/gp/universidaddemontemorelos/e24v5Vi63h</code></li>
                                    <li>Por cada foto:
                                        <ul>
                                            <li>Haz clic en la foto para abrirla</li>
                                            <li>Clic derecho → "Copiar dirección de imagen"</li>
                                            <li>Pégala arriba</li>
                                            <li>Regresa al álbum y repite con la siguiente</li>
                                        </ul>
                                    </li>
                                </ol>
                                <div class="alert alert-info mb-0">
                                    <strong>Tip:</strong> Puedes abrir varias fotos en pestañas diferentes (Ctrl+Clic)
                                    y copiar todos los enlaces de golpe.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>

    <script>
        const anuariosUrl = "<?php echo $temp->siteURL ?>assets/API/anuarios/admin/";
        let processedPhotos = [];

        // Load anuarios
        function loadAnuarios() {
            fetch(anuariosUrl + 'listar.php')
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        let options = '<option value="">Seleccionar anuario...</option>';
                        result.data.forEach(a => {
                            options += `<option value="${a.ID}">${a.ANIO} - ${a.TITULO}</option>`;
                        });
                        $('#selectAnuario').html(options);
                    }
                });
        }

        // Copy script to clipboard
        function copyScript() {
            const script = document.getElementById('flickrScript').textContent;
            navigator.clipboard.writeText(script).then(() => {
                alert('Script copiado al portapapeles!\n\nAhora:\n1. Abre tu álbum de Flickr\n2. Presiona F12\n3. Ve a la pestaña Console\n4. Pega el script y presiona Enter');
            });
        }

        // Process URLs
        function processUrls() {
            const input = $('#urlsInput').val().trim();
            if (!input) {
                alert('Por favor pega las URLs primero');
                return;
            }

            const lines = input.split('\n').filter(l => l.trim());
            const urls = lines.map(l => l.trim()).filter(url => {
                return url.startsWith('http') && (url.includes('flickr') || url.match(/\.(jpg|jpeg|png|gif)$/i));
            });

            if (urls.length === 0) {
                alert('No se encontraron URLs válidas de imágenes');
                return;
            }

            processedPhotos = urls.map((url, index) => ({
                index: index + 1,
                url: url,
                matricula: '',
                nombre: '',
                carrera: '',
                facultad: '',
                anio: new Date().getFullYear()
            }));

            renderPhotosList();
            $('#resultsSection').show();
            $('#statsAlert').html(`<i class="fa fa-check-circle"></i> Se encontraron <strong>${urls.length}</strong> fotos válidas`);

            // Scroll to results
            $('html, body').animate({
                scrollTop: $("#resultsSection").offset().top - 100
            }, 500);
        }

        // Render photos list
        function renderPhotosList() {
            let html = '';

            processedPhotos.forEach((photo, idx) => {
                html += `
                    <div class="photo-item">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="${photo.url}" alt="Foto ${idx + 1}" class="img-thumbnail"
                                     onerror="this.src='<?php echo $temp->siteURL ?>assets/images/no-image.png'">
                                <small class="text-muted d-block mt-1">Foto #${idx + 1}</small>
                            </div>
                            <div class="col-md-10">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control form-control-sm"
                                               placeholder="Matrícula"
                                               value="${photo.matricula}"
                                               onchange="updatePhoto(${idx}, 'matricula', this.value)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm"
                                               placeholder="Nombre completo"
                                               value="${photo.nombre}"
                                               onchange="updatePhoto(${idx}, 'nombre', this.value)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control form-control-sm"
                                               placeholder="Carrera"
                                               value="${photo.carrera}"
                                               onchange="updatePhoto(${idx}, 'carrera', this.value)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control form-control-sm"
                                               placeholder="Facultad"
                                               value="${photo.facultad}"
                                               onchange="updatePhoto(${idx}, 'facultad', this.value)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control form-control-sm"
                                               placeholder="Año"
                                               value="${photo.anio}"
                                               onchange="updatePhoto(${idx}, 'anio', this.value)">
                                    </div>
                                    <div class="col-12">
                                        <input type="text" class="form-control form-control-sm url-input"
                                               value="${photo.url}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#photosList').html(html);
        }

        // Update photo data
        function updatePhoto(index, field, value) {
            processedPhotos[index][field] = value;
        }

        // Export to CSV
        function exportToCSV() {
            const csv = generateCSV();
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `fotos_flickr_${Date.now()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Copy CSV to clipboard
        function copyCSVToClipboard() {
            const csv = generateCSV();
            navigator.clipboard.writeText(csv).then(() => {
                alert('CSV copiado al portapapeles!\n\nAhora puedes:\n1. Ir a "Importar Múltiples" en Fotos de Estudiantes\n2. Pegar este CSV\n3. Importar todo');
            });
        }

        // Generate CSV
        function generateCSV() {
            let csv = 'matricula,nombre_estudiante,carrera,facultad,foto_url,anio\n';
            processedPhotos.forEach(photo => {
                csv += `${photo.matricula},"${photo.nombre}","${photo.carrera}","${photo.facultad}",${photo.url},${photo.anio}\n`;
            });
            return csv;
        }

        // Clear all
        function clearAll() {
            if (confirm('¿Limpiar todo y empezar de nuevo?')) {
                $('#urlsInput').val('');
                $('#resultsSection').hide();
                processedPhotos = [];
            }
        }

        // Load on ready
        $(document).ready(function() {
            loadAnuarios();
        });
    </script>
</body>

</html>
