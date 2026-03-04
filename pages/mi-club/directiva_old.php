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
                            <div class="btn-group" role="group">
                                <a href="/cpanel/pages/mi-club/" class="btn btn-outline-secondary me-2">
                                    <i data-acorn-icon="chevron-left"></i> Volver al Panel
                                </a>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarMiembro">
                                    <i data-acorn-icon="user-plus"></i> Agregar Miembro
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de miembros de la directiva -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i data-acorn-icon="users"></i> Miembros de la Directiva
                                    </h5>
                                    <small class="text-muted">Gestiona los miembros de la directiva de tu club</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                // Obtener miembros de la directiva desde la nueva tabla
                                $sql_miembros = $db->query("SELECT 
                                                                ID,
                                                                NOMBRE,
                                                                CARGO,
                                                                EMAIL,
                                                                TELEFONO,
                                                                ESTADO,
                                                                DATE_FORMAT(FECHA_REGISTRO, '%d/%m/%Y') as FECHA_REGISTRO_FORMATTED
                                                            FROM VRE_DIRECTIVA_CLUBES 
                                                            WHERE ID_CLUB = " . $club['ID'] . " 
                                                            AND ESTADO = 'activo'
                                                            ORDER BY 
                                                                CASE CARGO
                                                                    WHEN 'Presidente' THEN 1
                                                                    WHEN 'Vicepresidente' THEN 2
                                                                    WHEN 'Secretario' THEN 3
                                                                    WHEN 'Tesorero' THEN 4
                                                                    WHEN 'Coordinador' THEN 5
                                                                    WHEN 'Vocal' THEN 6
                                                                    WHEN 'Delegado' THEN 7
                                                                    ELSE 8
                                                                END,
                                                                NOMBRE ASC");
                                
                                $miembros_directiva = [];
                                if ($sql_miembros) {
                                    while($miembro = $sql_miembros->fetch_assoc()) {
                                        $miembros_directiva[] = $miembro;
                                    }
                                }
                                
                                // Debug: Mostrar información
                                echo "<!-- Debug: Consulta ejecutada. Miembros encontrados: " . count($miembros_directiva) . " -->";
                                echo "<!-- Debug: Club ID: " . $club['ID'] . " -->";
                                if (!$sql_miembros) {
                                    echo "<!-- Debug: Error en consulta SQL -->";
                                }
                                
                                // Preparar datos para JavaScript
                                $miembros_js = [];
                                foreach($miembros_directiva as $miembro) {
                                    $miembros_js[$miembro['ID']] = [
                                        'ID' => $miembro['ID'],
                                        'NOMBRE' => $miembro['NOMBRE'],
                                        'CARGO' => $miembro['CARGO'],
                                        'EMAIL' => $miembro['EMAIL'] ?? '',
                                        'TELEFONO' => $miembro['TELEFONO'] ?? '',
                                        'ESTADO' => $miembro['ESTADO'] ?? 'activo',
                                        'OBSERVACIONES' => $miembro['OBSERVACIONES'] ?? ''
                                    ];
                                }
                                
                                // Agregar miembro de prueba si no hay miembros
                                if (count($miembros_directiva) == 0) {
                                    $miembros_directiva[] = [
                                        'ID' => 999,
                                        'NOMBRE' => 'Miembro de Prueba',
                                        'CARGO' => 'Presidente',
                                        'EMAIL' => 'prueba@test.com',
                                        'TELEFONO' => '123456789',
                                        'ESTADO' => 'activo',
                                        'FECHA_REGISTRO_FORMATTED' => '01/01/2024'
                                    ];
                                    
                                    // Agregar también a los datos JS
                                    $miembros_js[999] = [
                                        'ID' => 999,
                                        'NOMBRE' => 'Miembro de Prueba',
                                        'CARGO' => 'Presidente',
                                        'EMAIL' => 'prueba@test.com',
                                        'TELEFONO' => '123456789',
                                        'ESTADO' => 'activo',
                                        'OBSERVACIONES' => 'Este es un miembro de prueba'
                                    ];
                                }
                                ?>
                                
                                <?php if (count($miembros_directiva) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Cargo</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
                                                    <th>Estado</th>
                                                    <th>Registrado</th>
                                                    <th width="150">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($miembros_directiva as $miembro): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary bg-gradient rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <span class="text-white fw-bold" style="font-size: 14px;">
                                                                    <?php 
                                                                    $nombres = explode(' ', trim($miembro['NOMBRE']));
                                                                    echo strtoupper(substr($nombres[0], 0, 1));
                                                                    if (isset($nombres[1])) {
                                                                        echo strtoupper(substr($nombres[1], 0, 1));
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </div>
                                                            <strong><?php echo htmlspecialchars($miembro['NOMBRE']); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($miembro['CARGO']); ?></span></td>
                                                    <td><?php echo $miembro['EMAIL'] ? htmlspecialchars($miembro['EMAIL']) : '<span class="text-muted">-</span>'; ?></td>
                                                    <td><?php echo $miembro['TELEFONO'] ? htmlspecialchars($miembro['TELEFONO']) : '<span class="text-muted">-</span>'; ?></td>
                                                    <td><span class="badge bg-success">Activo</span></td>
                                                    <td><small class="text-muted"><?php echo $miembro['FECHA_REGISTRO_FORMATTED']; ?></small></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarMiembro(<?php echo $miembro['ID']; ?>)" title="Editar">
                                                                <i data-acorn-icon="edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarMiembro(<?php echo $miembro['ID']; ?>, '<?php echo addslashes($miembro['NOMBRE']); ?>')" title="Eliminar">
                                                                <i data-acorn-icon="bin"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i data-acorn-icon="users" data-acorn-size="64" class="text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay miembros en la directiva</h5>
                                        <p class="text-muted mb-4">Aún no se han agregado miembros a la directiva de este club.</p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarMiembro">
                                            <i data-acorn-icon="user-plus"></i> Agregar Primer Miembro
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
</div>

        </main>
        
        <!-- Modal para Agregar/Editar Miembro -->
        <div class="modal fade" id="modalAgregarMiembro" tabindex="-1" aria-labelledby="modalAgregarMiembroLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAgregarMiembroLabel">
                            <i data-acorn-icon="user-plus"></i> 
                            <span id="modal-titulo">Agregar Miembro</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formMiembro">
                        <div class="modal-body">
                            <input type="hidden" id="miembro-id" name="id" value="">
                            <input type="hidden" id="club-id" name="club_id" value="<?php echo $club['ID']; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                                        <div class="form-text">Nombre y apellidos del miembro de la directiva</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="cargo" name="cargo" required>
                                            <option value="">Seleccionar cargo...</option>
                                            <option value="Presidente">Presidente</option>
                                            <option value="Vicepresidente">Vicepresidente</option>
                                            <option value="Secretario">Secretario</option>
                                            <option value="Tesorero">Tesorero</option>
                                            <option value="Coordinador">Coordinador</option>
                                            <option value="Vocal">Vocal</option>
                                            <option value="Delegado">Delegado</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                        <div class="form-text">Los cargos de Presidente, Vicepresidente, Secretario y Tesorero solo pueden ser ocupados por una persona</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" maxlength="100">
                                        <div class="form-text">Email de contacto del miembro</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" maxlength="20">
                                        <div class="form-text">Número de teléfono de contacto</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado">
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>
                                        <div class="form-text">Estado actual del miembro en la directiva</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="500"></textarea>
                                <div class="form-text">Información adicional sobre el miembro (opcional)</div>
                            </div>
                            
                            <!-- Alert para mostrar mensajes -->
                            <div id="alert-container"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btn-guardar">
                                <i data-acorn-icon="save"></i> 
                                <span id="btn-texto">Guardar Miembro</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php $temp->footer() ?>
    </div>
    
    <?php $temp->modalSettings() ?>
    <?php $temp->modalSearch() ?>
    <?php $temp->scripts() ?>
<script>
    // Variables globales
    let editandoMiembro = false;
    let miembroActual = null;
    
    // Detectar ruta base automáticamente
    const siteURL = '<?php echo $temp->siteURL; ?>';
    const basePath = window.location.pathname.includes('vida-estudiantil_Hithan') ? '/vida-estudiantil_Hithan' : '';
    
    // Datos de miembros embebidos desde PHP
    const miembrosData = <?php echo json_encode($miembros_js); ?>;
    
    $(document).ready(function() {
        
        // Funciones de utilidad
        function mostrarAlert(mensaje, tipo = 'info') {
            const alertContainer = $('#alert-container');
            const alertHtml = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.html(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                alertContainer.find('.alert').alert('close');
            }, 5000);
        }
        
        function limpiarAlert() {
            $('#alert-container').empty();
        }
        
        function resetearModal() {
            editandoMiembro = false;
            miembroActual = null;
            $('#formMiembro')[0].reset();
            $('#miembro-id').val('');
            $('#modal-titulo').text('Agregar Miembro');
            $('#btn-texto').text('Guardar Miembro');
            $('#estado').val('activo');
            $('#cargo').val('');
            window.updateCargoOptions();
            limpiarAlert();
        }
        
        function validarFormulario() {
            const nombre = $('#nombre').val().trim();
            const cargo = $('#cargo').val();
            const email = $('#email').val().trim();
            
            if (nombre === '') {
                mostrarAlert('El nombre es obligatorio', 'warning');
                $('#nombre').focus();
                return false;
            }
            
            if (cargo === '') {
                mostrarAlert('Debe seleccionar un cargo', 'warning');
                $('#cargo').focus();
                return false;
            }
            
            if (email !== '' && !validarEmail(email)) {
                mostrarAlert('El formato del email no es válido', 'warning');
                $('#email').focus();
                return false;
            }
            
            return true;
        }
        
        function validarEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        function deshabilitarBoton(deshabilitar = true) {
            const btnGuardar = $('#btn-guardar');
            const btnTexto = $('#btn-texto');
            
            if (deshabilitar) {
                btnGuardar.prop('disabled', true);
                btnTexto.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Guardando...');
            } else {
                btnGuardar.prop('disabled', false);
                btnTexto.text(editandoMiembro ? 'Actualizar Miembro' : 'Guardar Miembro');
            }
        }
        
        // Deshabilitar u ocultar cargos únicos ya ocupados (expuesto globalmente)
        window.updateCargoOptions = function() {
            const cargosUnicos = ['Presidente','Vicepresidente','Secretario','Tesorero'];
            const ocupados = new Set();
            // Construir set de cargos activos ocupados
            Object.keys(miembrosData || {}).forEach((id) => {
                const m = miembrosData[id];
                if (!m) return;
                const estado = (m.ESTADO || 'activo').toLowerCase();
                if (estado === 'activo' && m.CARGO) {
                    ocupados.add(m.CARGO);
                }
            });
            const selectedCargo = $('#cargo').val();
            $('#cargo option').each(function() {
                const val = $(this).val();
                if (!val) { $(this).prop('disabled', false).text('Seleccionar cargo...'); return; }
                const baseText = $(this).text().replace(' (ocupado)','');
                if (cargosUnicos.includes(val) && ocupados.has(val) && val !== selectedCargo) {
                    $(this).prop('disabled', true).text(baseText + ' (ocupado)');
                } else {
                    $(this).prop('disabled', false).text(baseText);
                }
            });
        }
        
        // Event listeners
        $('#modalAgregarMiembro').on('show.bs.modal', function() {
            if (!editandoMiembro) {
                resetearModal();
            }
        });
        
        $('#modalAgregarMiembro').on('hidden.bs.modal', function() {
            resetearModal();
        });
        
        // Manejar envío del formulario
        $('#formMiembro').on('submit', function(e) {
            e.preventDefault();
            
            limpiarAlert();
            
            if (!validarFormulario()) {
                return;
            }
            
            deshabilitarBoton(true);
            
            const formData = new FormData(this);
            const url = editandoMiembro 
                ? siteURL + 'pages/mi-club/actualizar-miembro.php'
                : siteURL + 'pages/mi-club/agregar-miembro.php';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const mensaje = editandoMiembro 
                            ? 'Miembro actualizado correctamente' 
                            : 'Miembro agregado correctamente';
                        
                        mostrarAlert(mensaje, 'success');
                        
                        // Cerrar modal y recargar página después de 2 segundos
                        setTimeout(() => {
                            $('#modalAgregarMiembro').modal('hide');
                            location.reload();
                        }, 2000);
                    } else {
                        mostrarAlert('Error: ' + (response.message || 'Error desconocido'), 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    mostrarAlert('Error de conexión. Por favor, inténtalo de nuevo.', 'danger');
                },
                complete: function() {
                    deshabilitarBoton(false);
                }
            });
        });
        
        console.log('Gestión de Directiva cargada - CRUD completo');
    });
    
    // Funciones globales
    window.editarMiembro = function(id) {
        editandoMiembro = true;
        
        // Hacer llamada a la API real
        console.log('Enviando ID:', id);
        $.ajax({
            url: siteURL + 'pages/mi-club/obtener-datos.php?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const miembro = response.data;
                    
                    // Llenar formulario con datos reales de la BD
                    $('#miembro-id').val(miembro.ID);
                    $('#nombre').val(miembro.NOMBRE);
                    $('#cargo').val(miembro.CARGO);
                    $('#email').val(miembro.EMAIL || '');
                    $('#telefono').val(miembro.TELEFONO || '');
                    $('#estado').val(miembro.ESTADO);
                    $('#observaciones').val(miembro.OBSERVACIONES || '');
                    
                    // Actualizar opciones de cargos permitiendo el cargo actual
                    window.updateCargoOptions();
                    
                    // Cambiar títulos
                    $('#modal-titulo').text('Editar Miembro');
                    $('#btn-texto').text('Actualizar Miembro');
                    
                    // Abrir modal
                    $('#modalAgregarMiembro').modal('show');
                } else {
                    console.log('Respuesta de error:', response);
                    alert('Error: ' + (response.message || 'No se encontraron datos'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                alert('Error de conexión al obtener los datos.');
            }
        });
    };
    
    window.eliminarMiembro = function(id, nombre) {
        if (confirm('¿Estás seguro de que deseas eliminar a "' + nombre + '" de la directiva?\n\nEsta acción no se puede deshacer.')) {
            $.ajax({
                url: siteURL + 'pages/mi-club/eliminar-miembro.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Miembro eliminado correctamente');
                        location.reload();
                    } else {
                        alert('Error al eliminar miembro: ' + (response.message || 'Error desconocido'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    alert('Error al eliminar el miembro. Por favor, inténtalo de nuevo.');
                }
            });
        }
    };
    </script>
</body>
</html>
