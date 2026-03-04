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
                                <a class="nav-link" data-bs-toggle="tab" href="#tabSecciones" role="tab">
                                    <i class="fa fa-th-large"></i> Secciones
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabEstadisticas" role="tab">
                                    <i class="fa fa-chart-bar"></i> Estadísticas
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Texto del Botón</label>
                                                <input type="text" class="form-control" id="hero_boton_texto"
                                                       value="<?php echo $config['hero']['boton_texto']['valor'] ?? ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">URL del Botón</label>
                                                <input type="text" class="form-control" id="hero_boton_url"
                                                       value="<?php echo $config['hero']['boton_url']['valor'] ?? ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Color Gradiente Inicio</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="color" class="form-control form-control-color" id="hero_color_inicio"
                                                           value="<?php echo $config['hero']['color_inicio']['valor'] ?? '#667eea'; ?>">
                                                    <span class="color-preview" id="preview_color_inicio"
                                                          style="background: <?php echo $config['hero']['color_inicio']['valor'] ?? '#667eea'; ?>;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="config-item">
                                                <label class="form-label fw-bold">Color Gradiente Fin</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="color" class="form-control form-control-color" id="hero_color_fin"
                                                           value="<?php echo $config['hero']['color_fin']['valor'] ?? '#764ba2'; ?>">
                                                    <span class="color-preview" id="preview_color_fin"
                                                          style="background: <?php echo $config['hero']['color_fin']['valor'] ?? '#764ba2'; ?>;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="config-item">
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

                            <!-- Secciones Tab -->
                            <div class="tab-pane fade" id="tabSecciones" role="tabpanel">
                                <!-- Sección Clubes -->
                                <div class="config-card">
                                    <h4 class="mb-3"><i class="fa fa-users text-primary"></i> Sección de Clubes</h4>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="seccion_clubes_mostrar"
                                               <?php echo ($config['seccion_clubes']['mostrar']['valor'] ?? 'S') == 'S' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold">Mostrar esta sección</label>
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Título</label>
                                        <input type="text" class="form-control" id="seccion_clubes_titulo"
                                               value="<?php echo $config['seccion_clubes']['titulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Subtítulo</label>
                                        <input type="text" class="form-control" id="seccion_clubes_subtitulo"
                                               value="<?php echo $config['seccion_clubes']['subtitulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Cantidad a Mostrar</label>
                                        <input type="number" class="form-control" id="seccion_clubes_cantidad" min="1" max="20"
                                               value="<?php echo $config['seccion_clubes']['cantidad']['valor'] ?? 6; ?>">
                                    </div>
                                </div>

                                <!-- Sección Ministerios -->
                                <div class="config-card">
                                    <h4 class="mb-3"><i class="fa fa-church text-primary"></i> Sección de Ministerios</h4>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="seccion_ministerios_mostrar"
                                               <?php echo ($config['seccion_ministerios']['mostrar']['valor'] ?? 'S') == 'S' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold">Mostrar esta sección</label>
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Título</label>
                                        <input type="text" class="form-control" id="seccion_ministerios_titulo"
                                               value="<?php echo $config['seccion_ministerios']['titulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Subtítulo</label>
                                        <input type="text" class="form-control" id="seccion_ministerios_subtitulo"
                                               value="<?php echo $config['seccion_ministerios']['subtitulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Cantidad a Mostrar</label>
                                        <input type="number" class="form-control" id="seccion_ministerios_cantidad" min="1" max="20"
                                               value="<?php echo $config['seccion_ministerios']['cantidad']['valor'] ?? 3; ?>">
                                    </div>
                                </div>

                                <!-- Sección Eventos -->
                                <div class="config-card">
                                    <h4 class="mb-3"><i class="fa fa-calendar text-primary"></i> Sección de Eventos</h4>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="seccion_eventos_mostrar"
                                               <?php echo ($config['seccion_eventos']['mostrar']['valor'] ?? 'S') == 'S' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold">Mostrar esta sección</label>
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Título</label>
                                        <input type="text" class="form-control" id="seccion_eventos_titulo"
                                               value="<?php echo $config['seccion_eventos']['titulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Subtítulo</label>
                                        <input type="text" class="form-control" id="seccion_eventos_subtitulo"
                                               value="<?php echo $config['seccion_eventos']['subtitulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div class="config-item">
                                        <label class="form-label fw-bold">Cantidad a Mostrar</label>
                                        <input type="number" class="form-control" id="seccion_eventos_cantidad" min="1" max="20"
                                               value="<?php echo $config['seccion_eventos']['cantidad']['valor'] ?? 3; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas Tab -->
                            <div class="tab-pane fade" id="tabEstadisticas" role="tabpanel">
                                <div class="config-card">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="mb-0"><i class="fa fa-chart-bar text-primary"></i> Contador de Estadísticas</h4>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="agregarEstadistica()">
                                            <i class="fa fa-plus"></i> Agregar Estadística
                                        </button>
                                    </div>

                                    <div class="form-check form-switch mb-4">
                                        <input class="form-check-input" type="checkbox" id="seccion_stats_mostrar"
                                               <?php echo ($config['seccion_stats']['mostrar']['valor'] ?? 'S') == 'S' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold">Mostrar sección de estadísticas</label>
                                    </div>

                                    <div class="config-item mb-4">
                                        <label class="form-label fw-bold">Título de la Sección</label>
                                        <input type="text" class="form-control" id="seccion_stats_titulo"
                                               value="<?php echo $config['seccion_stats']['titulo']['valor'] ?? ''; ?>">
                                    </div>

                                    <div id="estadisticasContainer">
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </div>
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
    </script>
    <script src="script.js"></script>
</body>

</html>
