<?php
include('../../assets/php/template.php');
$temp = new Template('Configuración del Home');
$db = new Conexion();

// Validar sesión y permisos
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Verificar si tiene permisos para ver este módulo
if (!$temp->tiene_permiso('home', 'ver')) {
    header('Location: ' . $temp->siteURL);
    exit();
}

// Obtener configuración actual
$config = [];
$sql = $db->query("SELECT SECCION, CLAVE, VALOR, TIPO, DESCRIPCION FROM VRE_HOME_CONFIG WHERE ACTIVO = 'S' ORDER BY SECCION, ORDEN");
if ($db->rows($sql) > 0) {
    foreach ($sql as $row) {
        $config[$row['SECCION']][$row['CLAVE']] = [
            'valor' => $row['VALOR'],
            'tipo' => $row['TIPO'],
            'descripcion' => $row['DESCRIPCION']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <style>
        .nav-tabs .nav-link {
            border-radius: 10px 10px 0 0;
            font-weight: 500;
            color: #6c757d;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-color: transparent;
        }
        .config-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 20px;
        }
        .config-item {
            margin-bottom: 20px;
        }
        .color-preview {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            display: inline-block;
            vertical-align: middle;
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .stat-card {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .stat-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }
        .destacado-item {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
                            <h1 class="mb-0 pb-0 display-4">
                                <i class="fa fa-home"></i> <?php echo $temp->titulo ?>
                            </h1>
                            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-5 d-flex align-items-start justify-content-end gap-2">
                            <button type="button" class="btn btn-info btn-icon btn-icon-start" onclick="previsualizarHome()">
                                <i class="fa fa-eye"></i>
                                <span>Vista Previa</span>
                            </button>
                            <button type="button" class="btn btn-success btn-icon btn-icon-start" onclick="guardarConfiguracion()">
                                <i class="fa fa-save"></i>
                                <span>Guardar Todo</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <div class="col-12">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs nav-tabs-line mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tabHero" role="tab">
                                    <i class="fa fa-image"></i> Hero Section
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabEventos" role="tab">
                                    <i class="fa fa-calendar-alt"></i> Eventos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabDestacados" role="tab">
                                    <i class="fa fa-star"></i> Destacados
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabFooter" role="tab">
                                    <i class="fa fa-link"></i> Footer
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Hero Section Tab -->
                            <div class="tab-pane fade show active" id="tabHero" role="tabpanel">
                                <div class="config-card">
                                    <h4 class="mb-4"><i class="fa fa-image text-primary"></i> Configuración del Hero Section</h4>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Título Principal</label>
                                        <input type="text" class="form-control" id="hero_titulo"
                                               value="<?php echo $config['hero']['titulo']['valor'] ?? ''; ?>">
                                        <small class="text-muted"><?php echo $config['hero']['titulo']['descripcion'] ?? ''; ?></small>
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Subtítulo/Descripción</label>
                                        <textarea class="form-control" id="hero_subtitulo" rows="3"><?php echo $config['hero']['subtitulo']['valor'] ?? ''; ?></textarea>
                                        <small class="text-muted"><?php echo $config['hero']['subtitulo']['descripcion'] ?? ''; ?></small>
                                    </div>

                                    <!-- Botones del Hero -->
                                    <div class="config-item">
                                        <label class="form-label fw-bold">Cantidad de Botones</label>
                                        <select class="form-select" id="hero_cantidad_botones">
                                            <option value="1" <?php echo ($config['hero']['cantidad_botones']['valor'] ?? '2') == '1' ? 'selected' : ''; ?>>1 Botón (Grande)</option>
                                            <option value="2" <?php echo ($config['hero']['cantidad_botones']['valor'] ?? '2') == '2' ? 'selected' : ''; ?>>2 Botones</option>
                                        </select>
                                    </div>

                                    <!-- Botón 1 -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Texto Botón 1</label>
                                                <input type="text" class="form-control" id="hero_boton1_texto"
                                                       value="<?php echo $config['hero']['boton1_texto']['valor'] ?? 'Ver Eventos'; ?>"
                                                       placeholder="Ver Eventos">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Enlace Botón 1</label>
                                                <select class="form-select" id="hero_boton1_enlace">
                                                    <option value="#eventos" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '#eventos') == '#eventos' ? 'selected' : ''; ?>>Eventos (en esta página)</option>
                                                    <option value="clubes" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '') == 'clubes' ? 'selected' : ''; ?>>Clubes</option>
                                                    <option value="ministerios" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '') == 'ministerios' ? 'selected' : ''; ?>>Ministerios</option>
                                                    <option value="instalaciones" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '') == 'instalaciones' ? 'selected' : ''; ?>>Instalaciones</option>
                                                    <option value="deportes" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '') == 'deportes' ? 'selected' : ''; ?>>Deportes</option>
                                                    <option value="anuarios" <?php echo ($config['hero']['boton1_enlace']['valor'] ?? '') == 'anuarios' ? 'selected' : ''; ?>>Anuarios</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón 2 -->
                                    <div id="boton2_config" style="display: <?php echo ($config['hero']['cantidad_botones']['valor'] ?? '2') == '2' ? 'block' : 'none'; ?>;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="config-item">
                                                    <label class="form-label fw-bold">Texto Botón 2</label>
                                                    <input type="text" class="form-control" id="hero_boton2_texto"
                                                           value="<?php echo $config['hero']['boton2_texto']['valor'] ?? 'Comunidad'; ?>"
                                                           placeholder="Comunidad">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="config-item">
                                                    <label class="form-label fw-bold">Enlace Botón 2</label>
                                                    <select class="form-select" id="hero_boton2_enlace">
                                                        <option value="clubes" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? 'clubes') == 'clubes' ? 'selected' : ''; ?>>Clubes</option>
                                                        <option value="ministerios" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? '') == 'ministerios' ? 'selected' : ''; ?>>Ministerios</option>
                                                        <option value="instalaciones" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? '') == 'instalaciones' ? 'selected' : ''; ?>>Instalaciones</option>
                                                        <option value="deportes" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? '') == 'deportes' ? 'selected' : ''; ?>>Deportes</option>
                                                        <option value="anuarios" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? '') == 'anuarios' ? 'selected' : ''; ?>>Anuarios</option>
                                                        <option value="#eventos" <?php echo ($config['hero']['boton2_enlace']['valor'] ?? '') == '#eventos' ? 'selected' : ''; ?>>Eventos (en esta página)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold d-flex align-items-center gap-2">
                                            <input type="checkbox" class="form-check-input" id="hero_usar_video"
                                                   <?php echo ($config['hero']['usar_video']['valor'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                            Usar Video de Fondo
                                        </label>
                                        <small class="text-muted">Activar para usar un video en lugar de imagen o gradiente</small>
                                    </div>

                                    <div class="config-item" id="video_config" style="display: <?php echo ($config['hero']['usar_video']['valor'] ?? '0') === '1' ? 'block' : 'none'; ?>;">
                                        <label class="form-label fw-bold">URL del Video</label>
                                        <input type="text" class="form-control" id="hero_video_url"
                                               value="<?php echo $config['hero']['video_url']['valor'] ?? ''; ?>"
                                               placeholder="https://www.youtube.com/watch?v=VIDEO_ID o https://ejemplo.com/video.mp4">
                                        <small class="text-muted">
                                            <i class="fab fa-youtube text-danger me-1"></i><strong>YouTube:</strong> Pega el link completo (ej: https://www.youtube.com/watch?v=dQw4w9WgXcQ)<br>
                                            <i class="fas fa-file-video me-1"></i><strong>MP4:</strong> URL directa al archivo (ej: https://ejemplo.com/video.mp4)
                                        </small>
                                    </div>

                                    <div class="config-item" id="imagen_config" style="display: <?php echo ($config['hero']['usar_video']['valor'] ?? '0') === '0' ? 'block' : 'none'; ?>;">
                                        <label class="form-label fw-bold">Imagen de Fondo (Opcional)</label>
                                        <input type="file" class="form-control" id="hero_imagen_fondo" accept="image/*">
                                        <small class="text-muted">Si se sube una imagen, reemplazará el gradiente de colores</small>
                                        <?php if (!empty($config['hero']['imagen_fondo']['valor'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo $temp->siteURL . $config['hero']['imagen_fondo']['valor']; ?>"
                                                     class="image-preview" id="preview_hero_imagen">
                                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="eliminarImagen('hero')">
                                                    <i class="fa fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Eventos Tab -->
                            <div class="tab-pane fade" id="tabEventos" role="tabpanel">
                                <div class="config-card">
                                    <h4 class="mb-4"><i class="fa fa-calendar-alt text-primary"></i> Configuración de Eventos</h4>

                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="eventos_mostrar"
                                               <?php echo ($config['eventos']['mostrar']['valor'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold">Mostrar sección de eventos en el Home</label>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Título de la Sección</label>
                                                <input type="text" class="form-control" id="eventos_titulo"
                                                       value="<?php echo $config['eventos']['titulo']['valor'] ?? 'Próximos Eventos'; ?>"
                                                       placeholder="Próximos Eventos">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Subtítulo</label>
                                                <input type="text" class="form-control" id="eventos_subtitulo"
                                                       value="<?php echo $config['eventos']['subtitulo']['valor'] ?? 'No te pierdas ninguna actividad del campus.'; ?>"
                                                       placeholder="No te pierdas ninguna actividad del campus.">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Cantidad de Eventos a Mostrar</label>
                                                <input type="number" class="form-control" id="eventos_cantidad" min="1" max="12"
                                                       value="<?php echo $config['eventos']['cantidad']['valor'] ?? 6; ?>">
                                                <small class="text-muted">Máximo: 12 eventos</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Estilo de Visualización</label>
                                                <select class="form-select" id="eventos_estilo">
                                                    <option value="auto" <?php echo ($config['eventos']['estilo']['valor'] ?? 'auto') == 'auto' ? 'selected' : ''; ?>>Automático (se adapta)</option>
                                                    <option value="grid" <?php echo ($config['eventos']['estilo']['valor'] ?? '') == 'grid' ? 'selected' : ''; ?>>Grid (cuadrícula siempre)</option>
                                                    <option value="lista" <?php echo ($config['eventos']['estilo']['valor'] ?? '') == 'lista' ? 'selected' : ''; ?>>Lista horizontal</option>
                                                </select>
                                                <small class="text-muted">
                                                    <strong>Auto:</strong> 1 evento = card grande, 2 = mitad y mitad, 3+ = grid<br>
                                                    <strong>Grid:</strong> Siempre en cuadrícula de 3 columnas<br>
                                                    <strong>Lista:</strong> Eventos en fila horizontal con scroll
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="config-item">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="eventos_solo_destacados"
                                                   <?php echo ($config['eventos']['solo_destacados']['valor'] ?? '0') == '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold">Mostrar solo eventos destacados</label>
                                        </div>
                                        <small class="text-muted">Si está activado, solo se mostrarán los eventos marcados como "Destacado" en el panel de Eventos</small>
                                    </div>

                                    <hr class="my-4">

                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle me-2"></i>
                                        <strong>Vista Previa de Estilos:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Automático con 1 evento:</strong> Tarjeta grande horizontal destacada con imagen a la izquierda</li>
                                            <li><strong>Automático con 2 eventos:</strong> Dos tarjetas medianas lado a lado (50% cada una)</li>
                                            <li><strong>Automático con 3+ eventos:</strong> Grid de 3 columnas responsive</li>
                                            <li><strong>Grid:</strong> Siempre muestra los eventos en cuadrícula de 3 columnas</li>
                                            <li><strong>Lista:</strong> Eventos en fila horizontal con scroll lateral</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Destacados Tab -->
                            <div class="tab-pane fade" id="tabDestacados" role="tabpanel">
                                <div class="config-card">
                                    <h4 class="mb-4"><i class="fa fa-star text-warning"></i> Elementos Destacados</h4>
                                    <p class="text-muted mb-4">Selecciona los clubes, ministerios o eventos que quieres destacar en la página de inicio</p>

                                    <ul class="nav nav-pills mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="pill" href="#destacadosClubes">Clubes</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="pill" href="#destacadosMinisterios">Ministerios</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="pill" href="#destacadosEventos">Eventos</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="destacadosClubes">
                                            <div id="destacadosClubesContainer">Cargando...</div>
                                        </div>
                                        <div class="tab-pane fade" id="destacadosMinisterios">
                                            <div id="destacadosMinisteriosContainer">Cargando...</div>
                                        </div>
                                        <div class="tab-pane fade" id="destacadosEventos">
                                            <div id="destacadosEventosContainer">Cargando...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Tab -->
                            <div class="tab-pane fade" id="tabFooter" role="tabpanel">
                                <div class="config-card">
                                    <h4 class="mb-4"><i class="fa fa-link text-primary"></i> Footer y Redes Sociales</h4>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Descripción</label>
                                        <textarea class="form-control" id="footer_descripcion" rows="3"><?php echo $config['footer']['descripcion']['valor'] ?? ''; ?></textarea>
                                    </div>

                                    <hr class="my-4">
                                    <h5 class="mb-3"><i class="fa fa-address-card text-primary"></i> Información de Contacto</h5>

                                    <div class="config-item">
                                        <label class="form-label fw-bold"><i class="fa fa-map-marker-alt text-danger"></i> Dirección</label>
                                        <input type="text" class="form-control" id="footer_direccion"
                                               value="<?php echo $config['footer']['direccion']['valor'] ?? ''; ?>"
                                               placeholder="Ave. Ignacio Morones Prieto 4500">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fa fa-phone text-success"></i> Teléfono</label>
                                                <input type="text" class="form-control" id="footer_telefono"
                                                       value="<?php echo $config['footer']['telefono']['valor'] ?? ''; ?>"
                                                       placeholder="+52 81 8215-1000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fa fa-envelope text-info"></i> Email</label>
                                                <input type="email" class="form-control" id="footer_email"
                                                       value="<?php echo $config['footer']['email']['valor'] ?? ''; ?>"
                                                       placeholder="vidaestudiantil@um.edu.mx">
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h5 class="mb-3"><i class="fa fa-share-alt text-primary"></i> Redes Sociales</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fab fa-facebook text-primary"></i> Facebook</label>
                                                <input type="url" class="form-control" id="footer_facebook"
                                                       value="<?php echo $config['footer']['facebook']['valor'] ?? ''; ?>"
                                                       placeholder="https://facebook.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fab fa-instagram text-danger"></i> Instagram</label>
                                                <input type="url" class="form-control" id="footer_instagram"
                                                       value="<?php echo $config['footer']['instagram']['valor'] ?? ''; ?>"
                                                       placeholder="https://instagram.com/...">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fab fa-twitter text-info"></i> Twitter</label>
                                                <input type="url" class="form-control" id="footer_twitter"
                                                       value="<?php echo $config['footer']['twitter']['valor'] ?? ''; ?>"
                                                       placeholder="https://twitter.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold"><i class="fab fa-youtube text-danger"></i> YouTube</label>
                                                <input type="url" class="form-control" id="footer_youtube"
                                                       value="<?php echo $config['footer']['youtube']['valor'] ?? ''; ?>"
                                                       placeholder="https://youtube.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->
            </div>
        </main>

        <?php $temp->footer() ?>
    </div>

    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>

    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';

        // Toggle entre video e imagen
        document.getElementById('hero_usar_video').addEventListener('change', function() {
            const usarVideo = this.checked;
            document.getElementById('video_config').style.display = usarVideo ? 'block' : 'none';
            document.getElementById('imagen_config').style.display = usarVideo ? 'none' : 'block';
        });

        // Toggle cantidad de botones
        document.getElementById('hero_cantidad_botones')?.addEventListener('change', function() {
            const cantidad = this.value;
            document.getElementById('boton2_config').style.display = cantidad === '2' ? 'block' : 'none';
        });

        // Preview de video de YouTube
        document.getElementById('hero_video_url')?.addEventListener('input', function() {
            const url = this.value;
            const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/;
            const match = url.match(youtubeRegex);

            // Remover preview anterior si existe
            const oldPreview = document.getElementById('video_preview');
            if (oldPreview) oldPreview.remove();

            if (match && match[1]) {
                const videoId = match[1];
                const embedUrl = `https://www.youtube.com/embed/${videoId}`;
                const previewHtml = `
                    <div id="video_preview" class="mt-3">
                        <p class="text-success mb-2"><i class="fas fa-check-circle me-1"></i>Video de YouTube detectado</p>
                        <iframe src="${embedUrl}" width="100%" height="200" style="border-radius:10px;border:2px solid #28a745;" allowfullscreen></iframe>
                    </div>
                `;
                this.insertAdjacentHTML('afterend', previewHtml);
            } else if (url && (url.endsWith('.mp4') || url.endsWith('.webm'))) {
                const previewHtml = `
                    <div id="video_preview" class="mt-2">
                        <p class="text-info mb-0"><i class="fas fa-info-circle me-1"></i>Video MP4/WebM detectado</p>
                    </div>
                `;
                this.insertAdjacentHTML('afterend', previewHtml);
            }
        });
    </script>
    <script src="script.js"></script>
</body>

</html>
