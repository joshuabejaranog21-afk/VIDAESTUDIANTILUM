<?php
include('../../assets/php/template.php');
$temp = new Template('Crear Club');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('clubes', 'crear')) {
    echo "No tienes permiso para crear clubes";
    exit();
}

// Obtener usuarios disponibles para ser directores
$usuarios_disponibles = [];
$sql = $db->query("SELECT ID, NOMBRE, EMAIL FROM SYSTEM_USUARIOS WHERE ACTIVO = 'S' AND ID_CLUB_ASIGNADO IS NULL ORDER BY NOMBRE");
while($usuario = $sql->fetch_assoc()) {
    $usuarios_disponibles[] = $usuario;
}
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
                <div class="page-title-container">
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4"><?php echo $temp->titulo ?></h1>
                            <nav class="breadcrumb-container d-inline-block">
                                <ul class="breadcrumb pt-0">
                                    <li class="breadcrumb-item"><a href="<?php echo $temp->siteURL ?>">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../">Clubes</a></li>
                                    <li class="breadcrumb-item active">Crear</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <form id="formCrearClub" enctype="multipart/form-data">
                    <!-- Información Básica del Club -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Información Básica del Club</h5>
                                    <small class="text-muted">El director asignado completará el resto de la información</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="nombre" class="form-label">Nombre del Club <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="imagen" class="form-label">Logo del Club</label>
                                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                            <small class="text-muted">Opcional - JPG, PNG (Máx. 5MB)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción Inicial <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required placeholder="Descripción breve del propósito del club"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Asignación de Director -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Asignar Director del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_director" id="director_existente" value="existente" checked>
                                                <label class="form-check-label" for="director_existente">
                                                    <strong>Asignar usuario existente</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="seccion_existente">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="director_usuario" class="form-label">Seleccionar Usuario</label>
                                                <select class="form-select" id="director_usuario" name="director_usuario">
                                                    <option value="">Seleccionar usuario</option>
                                                    <?php foreach($usuarios_disponibles as $user): ?>
                                                    <option value="<?php echo $user['ID']; ?>">
                                                        <?php echo htmlspecialchars($user['NOMBRE']); ?> (<?php echo htmlspecialchars($user['EMAIL']); ?>)
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipo_director" id="director_nuevo" value="nuevo">
                                                <label class="form-check-label" for="director_nuevo">
                                                    <strong>Crear nuevo usuario director</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="seccion_nuevo" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nuevo_nombre" class="form-label">Nombre de Usuario</label>
                                                <input type="text" class="form-control" id="nuevo_nombre" name="nuevo_nombre" placeholder="usuario.director">
                                                <small class="text-muted">Solo letras, números, puntos y guiones</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nuevo_email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="nuevo_email" name="nuevo_email" placeholder="director@universidad.com">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nuevo_password" class="form-label">Contraseña Temporal</label>
                                                <input type="password" class="form-control" id="nuevo_password" name="nuevo_password" placeholder="Mínimo 8 caracteres">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nombre_completo" class="form-label">Nombre Completo del Director</label>
                                                <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" placeholder="Juan Pérez">
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i data-acorn-icon="info"></i>
                                            <strong>Nota:</strong> Se enviará un email al director con sus credenciales de acceso.
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
                                        <a href="../" class="btn btn-secondary">
                                            <i data-acorn-icon="chevron-left"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                                            <i data-acorn-icon="save"></i> Crear Club
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
    <?php $temp->scripts() ?>
    <script>
        const siteURL = '<?php echo $temp->siteURL ?>';
        
        // Manejar cambio de tipo de director
        document.querySelectorAll('input[name="tipo_director"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const seccionExistente = document.getElementById('seccion_existente');
                const seccionNuevo = document.getElementById('seccion_nuevo');
                
                if (this.value === 'existente') {
                    seccionExistente.style.display = 'block';
                    seccionNuevo.style.display = 'none';
                    // Limpiar campos de nuevo usuario
                    document.getElementById('nuevo_nombre').value = '';
                    document.getElementById('nuevo_email').value = '';
                    document.getElementById('nuevo_password').value = '';
                    document.getElementById('nombre_completo').value = '';
                } else {
                    seccionExistente.style.display = 'none';
                    seccionNuevo.style.display = 'block';
                    // Limpiar selección de usuario existente
                    document.getElementById('director_usuario').value = '';
                }
            });
        });

        // Validar formulario antes de envío
        function validarFormulario() {
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const tipoDirector = document.querySelector('input[name="tipo_director"]:checked').value;
            
            if (!nombre || !descripcion) {
                alert('Por favor complete todos los campos obligatorios.');
                return false;
            }
            
            if (tipoDirector === 'existente') {
                const directorUsuario = document.getElementById('director_usuario').value;
                if (!directorUsuario) {
                    alert('Por favor seleccione un usuario para asignar como director.');
                    return false;
                }
            } else if (tipoDirector === 'nuevo') {
                const nuevoNombre = document.getElementById('nuevo_nombre').value.trim();
                const nuevoEmail = document.getElementById('nuevo_email').value.trim();
                const nuevoPassword = document.getElementById('nuevo_password').value;
                const nombreCompleto = document.getElementById('nombre_completo').value.trim();
                
                if (!nuevoNombre || !nuevoEmail || !nuevoPassword || !nombreCompleto) {
                    alert('Por favor complete todos los campos del nuevo director.');
                    return false;
                }
                
                if (nuevoPassword.length < 8) {
                    alert('La contraseña debe tener al menos 8 caracteres.');
                    return false;
                }
            }
            
            return true;
        }

        // Manejar envío del formulario
        document.getElementById('formCrearClub').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar antes de enviar
            if (!validarFormulario()) {
                return;
            }
            
            const btnGuardar = document.getElementById('btnGuardar');
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando club y asignando director...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(siteURL + 'assets/API/clubes/crear.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success == 1) {
                    // Éxito
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success',
                        delay: 4000
                    });
                    
                    // Redireccionar después de 3 segundos
                    setTimeout(() => {
                        window.location.href = '../';
                    }, 3000);
                } else {
                    // Error
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