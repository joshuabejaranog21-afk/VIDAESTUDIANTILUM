<?php
include('../../assets/php/template.php');
$temp = new Template('Editar Club');
$db = new Conexion();

// Validar sesión
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

// Validar permiso
if (!$temp->tiene_permiso('clubes', 'editar')) {
    echo "No tienes permiso para editar clubes";
    exit();
}

// Obtener ID del club
$id_club = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_club <= 0) {
    header('Location: ../');
    exit();
}

// Obtener información del club
$sql = $db->query("SELECT * FROM VRE_CLUBES WHERE ID = $id_club");
$club = $sql->fetch_assoc();

if (!$club) {
    header('Location: ../');
    exit();
}

// Obtener usuarios disponibles para ser directores
$usuarios_disponibles = [];
$sql_usuarios = $db->query("SELECT ID, NOMBRE, EMAIL FROM SYSTEM_USUARIOS WHERE ACTIVO = 'S' ORDER BY NOMBRE");
while($usuario = $sql_usuarios->fetch_assoc()) {
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
                                    <li class="breadcrumb-item active">Editar: <?php echo htmlspecialchars($club['NOMBRE']); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <form id="formEditarClub" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id_club; ?>">
                    
                    <!-- Información Básica del Club -->
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
                                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($club['NOMBRE']); ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="imagen" class="form-label">Logo del Club</label>
                                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                            <small class="text-muted">Opcional - JPG, PNG (Máx. 5MB)</small>
                                            <?php if($club['IMAGEN_URL']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($club['IMAGEN_URL']); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px;" alt="Logo actual">
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($club['DESCRIPCION'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del Club -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Detalles del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="horario" class="form-label">Horario de Reunión</label>
                                            <input type="text" class="form-control" id="horario" name="horario" value="<?php echo htmlspecialchars($club['HORARIO'] ?? ''); ?>" placeholder="Ej: 7:00 am - 9:00 am">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dia_reunion" class="form-label">Día de Reunión</label>
                                            <input type="text" class="form-control" id="dia_reunion" name="dia_reunion" value="<?php echo htmlspecialchars($club['DIA_REUNION'] ?? ''); ?>" placeholder="Ej: Martes">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="lugar" class="form-label">Lugar de Reunión</label>
                                            <input type="text" class="form-control" id="lugar" name="lugar" value="<?php echo htmlspecialchars($club['LUGAR'] ?? ''); ?>" placeholder="Ej: Auditorio FACSA">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                                            <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo" value="<?php echo htmlspecialchars($club['CUPO_MAXIMO'] ?? ''); ?>" placeholder="Sin límite si se deja vacío" min="1">
                                        </div>
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
                                    <h5 class="card-title">Director del Club</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="director_usuario" class="form-label">Seleccionar Usuario</label>
                                            <select class="form-select" id="director_usuario" name="director_usuario">
                                                <option value="">Sin director asignado</option>
                                                <?php foreach($usuarios_disponibles as $user): ?>
                                                <option value="<?php echo $user['ID']; ?>" <?php echo (isset($club['ID_DIRECTOR_USUARIO']) && $club['ID_DIRECTOR_USUARIO'] == $user['ID']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($user['NOMBRE']); ?> (<?php echo htmlspecialchars($user['EMAIL']); ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
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
                                    <h5 class="card-title">Estado</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo" name="activo" <?php echo ($club['ACTIVO'] == 'S') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activo">
                                            Club activo
                                        </label>
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
                                        <div>
                                            <button type="button" onclick="eliminarClub(<?php echo $id_club; ?>, '<?php echo htmlspecialchars(addslashes($club['NOMBRE'])); ?>')" class="btn btn-danger me-2" id="btnEliminar">
                                                <i data-acorn-icon="bin"></i> Eliminar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                                <i data-acorn-icon="save"></i> Guardar Cambios
                                            </button>
                                        </div>
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
        const clubId = <?php echo $id_club; ?>;
        
        // Manejar envío del formulario
        document.getElementById('formEditarClub').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            
            if (!nombre || !descripcion) {
                alert('Por favor complete los campos obligatorios.');
                return;
            }
            
            const btnGuardar = document.getElementById('btnGuardar');
            const textoOriginal = btnGuardar.innerHTML;
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
            
            try {
                const formData = new FormData(this);
                formData.append('activo', document.getElementById('activo').checked ? 'S' : 'N');
                
                const response = await fetch(siteURL + 'assets/API/clubes/editar.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success == 1) {
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success',
                        delay: 4000
                    });
                    
                    setTimeout(() => {
                        window.location.href = '../';
                    }, 3000);
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
        
        // Función para eliminar club
        function eliminarClub(id, nombre) {
            if (!confirm(`¿Estás seguro de eliminar el club "${nombre}"? Esta acción no se puede deshacer.`)) {
                return;
            }
            
            const formData = new FormData();
            formData.append('id', id);
            
            fetch(siteURL + 'assets/API/clubes/eliminar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success == 1) {
                    jQuery.notify({
                        title: 'Éxito',
                        message: data.message
                    }, {
                        type: 'success',
                        delay: 4000
                    });
                    
                    setTimeout(() => {
                        window.location.href = '../';
                    }, 3000);
                } else {
                    jQuery.notify({
                        title: 'Error',
                        message: data.message
                    }, {
                        type: 'danger'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                jQuery.notify({
                    title: 'Error',
                    message: 'Error al eliminar el club'
                }, {
                    type: 'danger'
                });
            });
        }
    </script>
</body>
</html>
