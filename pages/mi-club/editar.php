<?php
include('../../assets/php/template.php');
$temp = new Template('Editar Mi Club');
$db = new Conexion();

// Validar sesión y permisos
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

if (!$temp->es_director_club()) {
    echo "Acceso denegado. Solo para directores de club.";
    exit();
}

// Obtener información del club
$club = $temp->obtener_club_asignado();
if (!$club) {
    echo "No tienes un club asignado.";
    exit();
}

// Obtener imágenes del club desde VRE_GALERIA
$imagenes_query = $db->query("
    SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
    FROM VRE_GALERIA
    WHERE MODULO = 'clubes'
    AND ID_REGISTRO = {$club['ID']}
    AND ACTIVO = 'S'
    ORDER BY ORDEN ASC
");

$imagenes = [];
$imagen_principal = null;

if ($imagenes_query) {
    while ($img = $imagenes_query->fetch_assoc()) {
        $imagenes[] = $img;
        if ($img['TIPO'] == 'principal' && !$imagen_principal) {
            $imagen_principal = $img['URL_IMAGEN'];
        }
    }
}

// Mantener compatibilidad temporal
$galeria_existente = array_values(array_filter(array_map(function($img) {
    return $img['TIPO'] == 'galeria' ? $img['URL_IMAGEN'] : null;
}, $imagenes)));
?>
<!DOCTYPE html>
<html lang="es" data-footer="true">
<head>
    <?php $temp->head() ?>
</head>
<body>
    <div id="root">
        <?php $temp->nav() ?>
        <main>
            <div class="container">
                <!-- Header -->
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <h1 class="mb-0 pb-0 display-4">Editar Club</h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="/cpanel/pages/mi-club/">Mi Club</a></li>
                                    <li class="breadcrumb-item active">Editar</li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-start justify-content-end">
                            <a href="/cpanel/pages/mi-club/" class="btn btn-outline-secondary">
                                <i data-acorn-icon="chevron-left"></i> Volver a Mi Club
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Edición -->
                <form id="formEditarClub" enctype="multipart/form-data">
                    <input type="hidden" name="club_id" value="<?php echo $club['ID']; ?>">
                    
                    <!-- Información Básica -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Información Básica del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="nombre" class="form-label">Nombre del Club <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                   value="<?php echo htmlspecialchars($club['NOMBRE']); ?>" required>
                                            <small class="text-muted">Este campo solo puede ser editado por administradores</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Logo del Club</label>
                                            <div>
                                                <?php if($imagen_principal): ?>
                                                <img src="<?php echo htmlspecialchars($imagen_principal); ?>"
                                                     class="img-thumbnail"
                                                     style="max-height: 150px; width: auto;">
                                                <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                                    <div class="text-center">
                                                        <i data-acorn-icon="image" class="text-muted" data-acorn-size="32"></i>
                                                        <p class="text-muted small mt-2">Sin logo</p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                <small class="text-muted d-block mt-2">
                                                    <i data-acorn-icon="info" class="me-1"></i>
                                                    Las imágenes solo pueden ser editadas por administradores desde el panel de Clubes
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($club['DESCRIPCION']); ?></textarea>
                                        <small class="text-muted">Describe brevemente el propósito y actividades del club</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="objetivo" class="form-label">Objetivo</label>
                                        <textarea class="form-control" id="objetivo" name="objetivo" rows="3"><?php echo htmlspecialchars($club['OBJETIVO'] ?? ''); ?></textarea>
                                        <small class="text-muted">¿Cuál es el objetivo principal del club?</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Reuniones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Información de Reuniones</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="horario" class="form-label">Horario</label>
                                            <input type="text" class="form-control" id="horario" name="horario" 
                                                   value="<?php echo htmlspecialchars($club['HORARIO'] ?? ''); ?>"
                                                   placeholder="Ej: 7:00 - 8:00 PM">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="dia_reunion" class="form-label">Día de Reunión</label>
                                            <select class="form-select" id="dia_reunion" name="dia_reunion">
                                                <option value="">Seleccionar día</option>
                                                <?php 
                                                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                                foreach($dias as $dia) {
                                                    $selected = ($club['DIA_REUNION'] == $dia) ? 'selected' : '';
                                                    echo "<option value='$dia' $selected>$dia</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="lugar" class="form-label">Lugar</label>
                                            <input type="text" class="form-control" id="lugar" name="lugar" 
                                                   value="<?php echo htmlspecialchars($club['LUGAR'] ?? ''); ?>"
                                                   placeholder="Ej: Aula 301, Sala de conferencias">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Información Adicional</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                                            <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo"
                                                   min="1" value="<?php echo $club['CUPO_MAXIMO'] ?? ''; ?>"
                                                   placeholder="Dejar vacío para cupo ilimitado">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="responsable_nombre" class="form-label">Nombre del Responsable <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="responsable_nombre" name="responsable_nombre"
                                                   value="<?php echo htmlspecialchars($club['RESPONSABLE_NOMBRE'] ?? ''); ?>"
                                                   placeholder="Ej: Dr. Juan Pérez" required>
                                            <small class="text-muted">Persona responsable del club</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="responsable_contacto" class="form-label">Contacto del Responsable</label>
                                            <input type="text" class="form-control" id="responsable_contacto" name="responsable_contacto"
                                                   value="<?php echo htmlspecialchars($club['RESPONSABLE_CONTACTO'] ?? ''); ?>"
                                                   placeholder="Extensión o contacto adicional">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email del Club</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($club['EMAIL'] ?? ''); ?>"
                                                   placeholder="club@universidad.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                                   value="<?php echo htmlspecialchars($club['TELEFONO'] ?? ''); ?>"
                                                   placeholder="Número de contacto">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="requisitos" class="form-label">Requisitos</label>
                                            <textarea class="form-control" id="requisitos" name="requisitos" rows="3"
                                                      placeholder="¿Qué requisitos necesita un estudiante para unirse?"><?php echo htmlspecialchars($club['REQUISITOS'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="beneficios" class="form-label">Beneficios</label>
                                            <textarea class="form-control" id="beneficios" name="beneficios" rows="3"
                                                      placeholder="¿Qué beneficios obtienen los miembros del club?"><?php echo htmlspecialchars($club['BENEFICIOS'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Galería de Imágenes -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Galería de Imágenes del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Galería</label>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarGaleria">
                                                <i data-acorn-icon="plus"></i> Agregar Imagen
                                            </button>
                                        </div>
                                        <small class="text-muted d-block mb-3">Agrega fotos de actividades, eventos o instalaciones del club (JPG, PNG, GIF - Máx. 5MB)</small>
                                        <div id="galeriaContainer">
                                            <!-- Se agregarán imágenes aquí -->
                                        </div>
                                        <input type="hidden" id="galeria" name="galeria" value='<?php echo htmlspecialchars(json_encode($galeria_existente)); ?>'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Club -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Estado del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label">Estado del Club</label>
                                            <div class="p-3 bg-light rounded">
                                                <h5 class="mb-2">
                                                    <span class="badge <?php echo $club['ACTIVO'] == 'S' ? 'bg-success' : 'bg-secondary'; ?> me-2">
                                                        <?php echo $club['ACTIVO'] == 'S' ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                </h5>
                                                <small class="text-muted">Solo el administrador puede cambiar el estado activo/inactivo del club</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-info mb-0">
                                                <small>
                                                    <i data-acorn-icon="info" class="me-1"></i>
                                                    <strong>Para que tu club sea visible en la web:</strong><br>
                                                    • Completa toda la información requerida<br>
                                                    • El administrador debe activarlo
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <a href="<?php echo $temp->siteURL; ?>pages/mi-club/" class="btn btn-secondary">
                                            <i data-acorn-icon="chevron-left"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                                            <i data-acorn-icon="save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
        <?php $temp->footer() ?>
    </div>
    
    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>

    <!-- Input file oculto para galería -->
    <input type="file" id="fileInputGaleria" style="display:none;" accept="image/*">

    <?php $temp->scripts() ?>
    
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';

        // Galería de imágenes
        let galeria = <?php echo json_encode($galeria_existente); ?>;

        // Función para agregar imagen a la galería
        function agregarImagenGaleria(url) {
            const container = document.getElementById('galeriaContainer');
            const div = document.createElement('div');
            div.className = 'mb-2 p-2 border rounded';
            div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${url}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    <small class="flex-grow-1" style="word-break: break-all;">${url}</small>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarImagenGaleria(this, '${url.replace(/'/g, "\\'")}')">
                        <i data-acorn-icon="bin"></i> Eliminar
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        // Función para eliminar imagen de la galería
        function eliminarImagenGaleria(btn, url) {
            btn.closest('.mb-2').remove();
            galeria = galeria.filter(g => g !== url);
            document.getElementById('galeria').value = JSON.stringify(galeria);
        }

        // Cargar galería existente al iniciar
        galeria.forEach(url => agregarImagenGaleria(url));

        // Botón agregar imagen a galería
        document.getElementById('btnAgregarGaleria').addEventListener('click', function(e) {
            e.preventDefault();
            const fileInput = document.getElementById('fileInputGaleria');

            fileInput.onchange = function() {
                if (fileInput.files.length > 0) {
                    const formData = new FormData();
                    formData.append('archivo', fileInput.files[0]);
                    formData.append('tipo', 'galeria_club');

                    // Mostrar loading
                    jQuery.notify({
                        title: 'Subiendo...',
                        message: 'Subiendo imagen a la galería'
                    }, { type: 'info' });

                    fetch(siteURL + 'assets/API/upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == 1) {
                            const imagenURL = data.url || data.url_relativa;
                            galeria.push(imagenURL);
                            agregarImagenGaleria(imagenURL);
                            document.getElementById('galeria').value = JSON.stringify(galeria);

                            // Actualizar iconos de Acorn
                            if (typeof acorn !== 'undefined') {
                                acorn.icons();
                            }

                            jQuery.notify({
                                title: 'Éxito',
                                message: 'Imagen agregada a la galería'
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
                            message: 'Error al subir imagen'
                        }, { type: 'danger' });
                    });
                }
            };

            fileInput.click();
        });

        // Preview de imagen cuando se selecciona un archivo
        const nuevaImagenInput = document.getElementById('nueva_imagen');
        if (nuevaImagenInput) {
            nuevaImagenInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar que sea una imagen
                    if (!file.type.startsWith('image/')) {
                        alert('Por favor selecciona un archivo de imagen');
                        this.value = '';
                        return;
                    }

                    // Validar tamaño (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('La imagen es muy grande. Máximo 5MB');
                        this.value = '';
                        return;
                    }

                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const previewImg = document.getElementById('preview_imagen');
                        const noImagenText = document.getElementById('no_imagen_text');

                        if (previewImg) {
                            previewImg.src = event.target.result;
                            previewImg.style.display = 'block';
                        }

                        if (noImagenText) {
                            noImagenText.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        document.getElementById('formEditarClub').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btnGuardar = document.getElementById('btnGuardar');
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(siteURL + 'assets/API/mi-club/actualizar.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success == 1) {
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success'
                    });
                    
                    // Redireccionar después de 2 segundos
                    setTimeout(() => {
                        window.location.href = '/cpanel/pages/mi-club/';
                    }, 2000);
                } else {
                    jQuery.notify({
                        title: 'Error',
                        message: data.message
                    }, {
                        type: 'danger'
                    });
                }
                
            } catch (error) {
                console.error('Error:', error);
                jQuery.notify({
                    title: 'Error',
                    message: 'Error de conexión. Intente nuevamente.'
                }, {
                    type: 'danger'
                });
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = textoOriginal;
            }
        });

        // Deshabilitar edición del nombre para directores
        document.getElementById('nombre').disabled = true;
    </script>
</body>
</html>