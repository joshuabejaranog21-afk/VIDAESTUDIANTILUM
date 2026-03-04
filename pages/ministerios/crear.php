<?php
include('../../assets/php/template.php');
$temp = new Template('Crear Ministerio');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('ministerios', 'crear')) {
    echo "No tienes permiso para crear ministerios";
    exit();
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
                                    <li class="breadcrumb-item"><a href="./">Ministerios</a></li>
                                    <li class="breadcrumb-item active">Crear</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <form id="formCrearMinisterio" enctype="multipart/form-data">
                    <!-- Información Básica del Ministerio -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Información Básica del Ministerio</h5>
                                    <small class="text-muted">Información fundamental para crear el ministerio</small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="nombre" class="form-label">Nombre del Ministerio <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción Inicial <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required placeholder="Descripción breve del propósito del ministerio"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Crear Usuario Director (Opcional) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Crear Usuario Director (Opcional)</h5>
                                    <small class="text-muted">Si deseas crear un nuevo usuario director, completa estos campos</small>
                                </div>
                                <div class="card-body">
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
                                        <strong>Nota:</strong> Si completas estos campos, se creará un nuevo usuario director y se enviará un email con las credenciales de acceso. Si los dejas en blanco, el ministerio se creará sin director asignado.
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
                                        <a href="./" class="btn btn-secondary">
                                            <i data-acorn-icon="chevron-left"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" id="btnGuardar">
                                            <i data-acorn-icon="save"></i> Crear Ministerio
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
        
        // Validar formulario antes de envío
        function validarFormulario() {
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            
            if (!nombre || !descripcion) {
                alert('Por favor complete todos los campos obligatorios.');
                return false;
            }
            
            // Validar campos opcionales del director
            const nuevoNombre = document.getElementById('nuevo_nombre').value.trim();
            const nuevoEmail = document.getElementById('nuevo_email').value.trim();
            const nuevoPassword = document.getElementById('nuevo_password').value.trim();
            const nombreCompleto = document.getElementById('nombre_completo').value.trim();
            
            // Si alguno de los campos del director está completo, todos deben estarlo
            const tieneAlgunCampoDirector = nuevoNombre || nuevoEmail || nuevoPassword || nombreCompleto;
            
            if (tieneAlgunCampoDirector) {
                if (!nuevoNombre || !nuevoEmail || !nuevoPassword || !nombreCompleto) {
                    alert('Si deseas crear un director, debes completar todos los campos del formulario.');
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
        document.getElementById('formCrearMinisterio').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar antes de enviar
            if (!validarFormulario()) {
                return;
            }
            
            const btnGuardar = document.getElementById('btnGuardar');
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando ministerio...';
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch(siteURL + 'assets/API/ministerios/crear.php', {
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
                        window.location.href = './';
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
