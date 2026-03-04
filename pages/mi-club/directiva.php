<?php
include('../../assets/php/template.php');
$temp = new Template('Gestión de Directiva');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Solo directores de club pueden acceder
if (!$temp->es_director_club()) {
    echo "<h3>Acceso Denegado</h3>";
    echo "<p>Esta página es solo para directores de club.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Obtener información del club asignado
$club = $temp->obtener_club_asignado();
if (!$club) {
    echo "<h3>Club No Asignado</h3>";
    echo "<p>No tienes un club asignado. Contacta al administrador.</p>";
    echo "<a href='" . $temp->siteURL . "'>Volver al inicio</a>";
    exit();
}

// Obtener directiva del club
$sql = $db->query("SELECT * FROM VRE_DIRECTIVA_CLUBES WHERE ID_CLUB = " . $club['ID']);
$directiva = $sql->fetch_assoc();

if (!$directiva) {
    echo "<h3>Error</h3>";
    echo "<p>No se encontró la directiva del club.</p>";
    exit();
}

// Definir cargos disponibles
$cargos = [
    'DIRECTOR' => 'Director',
    'SUBDIRECTOR' => 'Subdirector',
    'SECRETARIO' => 'Secretario',
    'TESORERO' => 'Tesorero',
    'CAPELLAN' => 'Capellán',
    'CONSEJERO_GENERAL' => 'Consejero General',
    'LOGISTICA' => 'Logística',
    'MEDIA' => 'Media'
];

// Contar cargos ocupados
$cargos_ocupados = [];
foreach ($cargos as $slug => $nombre) {
    $campo = strtoupper($slug) . '_NOMBRE';
    if (!empty($directiva[$campo])) {
        $cargos_ocupados[$slug] = $directiva[$campo];
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true">
<head>
    <?php $temp->head() ?>
    <!-- International Telephone Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }
        .iti__flag-container {
            z-index: 2;
        }
        /* Ocultar marca de agua de intl-tel-input */
        .iti__country-list::after {
            display: none !important;
        }
        .iti__divider {
            display: none !important;
        }
    </style>
</head>
<body>
    <div id="root">
        <?php $temp->nav() ?>
        <main>
            <div class="container">
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <h1 class="mb-0 pb-0 display-4">Gestión de Directiva</h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="/cpanel/pages/mi-club/">Mi Club</a></li>
                                    <li class="breadcrumb-item active">Directiva</li>
                                </ul>
                            </nav>
                            <p class="text-muted"><?php echo htmlspecialchars($club['NOMBRE']); ?></p>
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-start justify-content-end">
                            <a href="/cpanel/pages/mi-club/" class="btn btn-outline-secondary">
                                <i data-acorn-icon="chevron-left"></i> Volver al Panel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i data-acorn-icon="users"></i> Miembros de la Directiva
                                </h5>
                                <small class="text-muted">Gestiona los miembros de la directiva de tu club</small>
                            </div>
                            <div class="card-body">
                                <!-- Resumen de cargos -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3">Cargos Asignados (<?php echo count($cargos_ocupados); ?>/<?php echo count($cargos); ?>)</h5>
                                        <?php if (count($cargos_ocupados) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Foto</th>
                                                        <th>Cargo</th>
                                                        <th>Nombre</th>
                                                        <th>Email</th>
                                                        <th>Teléfono</th>
                                                        <th width="100">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($cargos_ocupados as $slug => $nombre): 
                                                        $campo_nombre = strtoupper($slug) . '_NOMBRE';
                                                        $campo_email = strtoupper($slug) . '_EMAIL';
                                                        $campo_telefono = strtoupper($slug) . '_TELEFONO';
                                                        $campo_foto = strtoupper($slug) . '_FOTO';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php if (!empty($directiva[$campo_foto])): ?>
                                                                <img src="<?php echo htmlspecialchars($directiva[$campo_foto]); ?>"
                                                                     alt="Foto"
                                                                     style="max-width: 40px; max-height: 40px; border-radius: 50%; object-fit: cover;"
                                                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                <div style="display: none; width: 40px; height: 40px; border-radius: 50%; background-color: #e9ecef; align-items: center; justify-content: center;">
                                                                    <i data-acorn-icon="user" style="font-size: 20px; color: #6c757d;"></i>
                                                                </div>
                                                            <?php else: ?>
                                                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                                                    <i data-acorn-icon="user" style="font-size: 20px; color: #6c757d;"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><strong><?php echo $cargos[$slug]; ?></strong></td>
                                                        <td><?php echo htmlspecialchars($directiva[$campo_nombre] ?? '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($directiva[$campo_email] ?? '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($directiva[$campo_telefono] ?? '-'); ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="editarCargo('<?php echo $slug; ?>')">
                                                                <i data-acorn-icon="edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarCargo('<?php echo $slug; ?>', '<?php echo $cargos[$slug]; ?>')">
                                                                <i data-acorn-icon="bin"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-info">
                                            <i data-acorn-icon="info" class="me-2"></i>
                                            No hay cargos asignados aún.
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <hr>

                                <!-- Formulario para agregar/editar -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="mb-3">Agregar/Editar Miembro</h5>
                                        <form id="formDirectiva">
                                            <input type="hidden" name="id_club" value="<?php echo $club['ID']; ?>">
                                            <input type="hidden" name="id_directiva" value="<?php echo $directiva['ID']; ?>">
                                            <input type="hidden" id="cargoEditar" name="cargo_editar" value="">
                                            
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Seleccionar Cargo <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="selectCargo" name="cargo" required onchange="validarCargoDisponible()">
                                                        <option value="">-- Seleccionar cargo --</option>
                                                        <?php foreach ($cargos as $slug => $nombre_cargo): 
                                                            $ocupado = isset($cargos_ocupados[$slug]);
                                                        ?>
                                                        <option value="<?php echo $slug; ?>" <?php echo $ocupado ? 'disabled' : ''; ?>>
                                                            <?php echo $nombre_cargo; ?><?php echo $ocupado ? ' (Ocupado)' : ''; ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <small class="text-muted d-block mt-1">Los cargos ocupados no se pueden seleccionar</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="inputNombre" name="nombre" placeholder="Nombre completo" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="inputEmail" name="email" placeholder="email@example.com">
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="tel" class="form-control" id="inputTelefono" name="telefono">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Foto</label>
                                                    <input type="file" class="form-control" id="inputFoto" name="foto" accept="image/*">
                                                    <small class="text-muted d-block mt-1">Formatos: JPG, PNG, GIF (máx 5MB)</small>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div id="previewFoto" style="margin-bottom: 10px;">
                                                        <!-- Preview de foto va aquí -->
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                                                            <i data-acorn-icon="save"></i> Guardar
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                                                            <i data-acorn-icon="close"></i> Limpiar
                                                        </button>
                                                        <a href="/cpanel/pages/mi-club/" class="btn btn-outline-secondary">
                                                            <i data-acorn-icon="chevron-left"></i> Volver
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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

    <!-- International Telephone Input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

    <script>
        const siteURL = '<?php echo $temp->siteURL; ?>';
        let cargoEnEdicion = null;
        let itiInstance = null;

        // Inicializar selector de teléfono internacional
        document.addEventListener('DOMContentLoaded', function() {
            const inputTelefono = document.getElementById('inputTelefono');
            if (inputTelefono) {
                itiInstance = window.intlTelInput(inputTelefono, {
                    initialCountry: "mx",
                    preferredCountries: ["mx", "us", "ca"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js",
                    autoPlaceholder: "off",
                    formatOnDisplay: true,
                    nationalMode: false
                });
            }
        });

        // Preview de foto al seleccionar archivo
        document.getElementById('inputFoto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById('previewFoto');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewDiv.innerHTML = `<img src="${event.target.result}" alt="Preview" style="max-width: 80px; max-height: 80px; border-radius: 8px; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = '';
            }
        });

        // Validar cargo disponible
        function validarCargoDisponible() {
            const cargo = document.getElementById('selectCargo').value;
            if (!cargo) {
                limpiarFormulario();
                return;
            }
            // El select ya tiene disabled los ocupados, así que esto es solo un doble check
        }

        // Limpiar formulario
        function limpiarFormulario() {
            document.getElementById('formDirectiva').reset();

            // Limpiar el teléfono en el plugin
            if (itiInstance) {
                itiInstance.setNumber('');
            }

            document.getElementById('selectCargo').focus();
            cargoEnEdicion = null;
            document.getElementById('cargoEditar').value = '';
            document.getElementById('btnGuardar').innerHTML = '<i data-acorn-icon="save"></i> Guardar';
            document.getElementById('previewFoto').innerHTML = '';
            document.getElementById('selectCargo').disabled = false;
        }

        // Editar cargo
        window.editarCargo = function(slug) {
            cargoEnEdicion = slug;
            document.getElementById('cargoEditar').value = slug;
            document.getElementById('selectCargo').value = slug;
            document.getElementById('selectCargo').disabled = true;
            
            // Cargar datos actuales desde PHP embebidos en data attributes
            const nombreField = slug.toUpperCase() + '_NOMBRE';
            const emailField = slug.toUpperCase() + '_EMAIL';
            const telefonoField = slug.toUpperCase() + '_TELEFONO';
            const fotoField = slug.toUpperCase() + '_FOTO';
            
            // Los datos están en el objeto directiva de PHP
            const directivaData = <?php echo json_encode($directiva); ?>;
            
            if (directivaData[nombreField]) {
                document.getElementById('inputNombre').value = directivaData[nombreField] || '';
                document.getElementById('inputEmail').value = directivaData[emailField] || '';

                // Cargar teléfono con el plugin intl-tel-input
                const telefonoValue = directivaData[telefonoField] || '';
                if (itiInstance && telefonoValue) {
                    itiInstance.setNumber(telefonoValue);
                } else {
                    document.getElementById('inputTelefono').value = telefonoValue;
                }

                // Mostrar preview de foto si existe
                const previewDiv = document.getElementById('previewFoto');
                if (directivaData[fotoField]) {
                    previewDiv.innerHTML = `<img src="${directivaData[fotoField]}" alt="Foto actual" style="max-width: 80px; max-height: 80px; border-radius: 8px; object-fit: cover;">`;
                } else {
                    previewDiv.innerHTML = '';
                }
            }
            
            document.getElementById('btnGuardar').innerHTML = '<i data-acorn-icon="save"></i> Actualizar';
            document.getElementById('inputNombre').focus();
        }

        // Eliminar cargo
        window.eliminarCargo = function(slug, nombre) {
            if (!confirm(`¿Eliminar a ${nombre}?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('id_club', document.querySelector('input[name="id_club"]').value);
            formData.append('id_directiva', document.querySelector('input[name="id_directiva"]').value);
            formData.append('cargo', slug);
            formData.append('accion', 'eliminar');

            fetch(siteURL + 'pages/mi-club/actualizar-directiva.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success',
                        delay: 2000
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    jQuery.notify({
                        title: 'Error',
                        message: data.message
                    }, {
                        type: 'danger'
                    });
                }
            });
        }

        // Enviar formulario
        document.getElementById('formDirectiva').addEventListener('submit', async function(e) {
            e.preventDefault();

            const cargo = document.getElementById('selectCargo').value;
            const nombre = document.getElementById('inputNombre').value.trim();
            const email = document.getElementById('inputEmail').value.trim();
            // Obtener el número completo con código de país usando intl-tel-input
            const telefono = itiInstance ? itiInstance.getNumber() : document.getElementById('inputTelefono').value.trim();

            if (!cargo || !nombre) {
                alert('Cargo y Nombre son obligatorios');
                return;
            }

            const btnGuardar = document.getElementById('btnGuardar');
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

            try {
                const formData = new FormData();
                formData.append('id_club', document.querySelector('input[name="id_club"]').value);
                formData.append('id_directiva', document.querySelector('input[name="id_directiva"]').value);
                formData.append('cargo', cargo);
                formData.append('nombre', nombre);
                formData.append('email', email);
                formData.append('telefono', telefono);
                formData.append('accion', cargoEnEdicion ? 'actualizar' : 'agregar');
                
                // Agregar archivo de foto si existe
                const fotoInput = document.getElementById('inputFoto');
                if (fotoInput.files.length > 0) {
                    formData.append('foto', fotoInput.files[0]);
                }

                const response = await fetch(siteURL + 'pages/mi-club/actualizar-directiva.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success',
                        delay: 3000
                    });

                    setTimeout(() => {
                        location.reload();
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
    </script>
</body>
</html>
