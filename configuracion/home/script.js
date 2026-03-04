// Variables globales
let estadisticas = [];
let destacados = {
    club: [],
    ministerio: [],
    evento: []
};

// Inicializar al cargar la página
$(document).ready(function() {
    // Actualizar previews de colores en tiempo real
    $('#hero_color_inicio').on('input', function() {
        $('#preview_color_inicio').css('background', $(this).val());
    });

    $('#hero_color_fin').on('input', function() {
        $('#preview_color_fin').css('background', $(this).val());
    });

    // Cargar datos al cambiar de tab
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');

        if (target === '#tabEstadisticas' && estadisticas.length === 0) {
            cargarEstadisticas();
        } else if (target === '#tabDestacados') {
            if (destacados.club.length === 0) {
                cargarDestacados('club');
            }
        }
    });

    // Cargar destacados al cambiar de pill
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');
        if (target === '#destacadosMinisterios' && destacados.ministerio.length === 0) {
            cargarDestacados('ministerio');
        } else if (target === '#destacadosEventos' && destacados.evento.length === 0) {
            cargarDestacados('evento');
        }
    });

    // Preview de imagen al seleccionar archivo
    $('#hero_imagen_fondo').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = $('#preview_hero_imagen');
                if (preview.length === 0) {
                    const html = `
                        <div class="mt-2">
                            <img src="${e.target.result}" class="image-preview" id="preview_hero_imagen">
                        </div>
                    `;
                    $('#hero_imagen_fondo').after(html);
                } else {
                    preview.attr('src', e.target.result);
                }
            };
            reader.readAsDataURL(file);
        }
    });
});

// ===================================
// ESTADÍSTICAS
// ===================================

function cargarEstadisticas() {
    fetch(`${siteURL}assets/API/home/estadisticas.php?accion=listar`)
        .then(response => response.json())
        .then(result => {
            if (result.success == 1) {
                estadisticas = result.data;
                renderizarEstadisticas();
            } else {
                $('#estadisticasContainer').html(`
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No hay estadísticas configuradas. Haz clic en "Agregar Estadística" para crear una.
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarNotificacion('Error', 'No se pudieron cargar las estadísticas', 'danger');
        });
}

function renderizarEstadisticas() {
    if (estadisticas.length === 0) {
        $('#estadisticasContainer').html(`
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No hay estadísticas configuradas.
            </div>
        `);
        return;
    }

    let html = '';
    estadisticas.forEach((stat, index) => {
        html += `
            <div class="stat-card" data-index="${index}">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div style="width: 60px; height: 60px; background: ${stat.COLOR}; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="${stat.ICONO} fa-2x text-white"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-0">${stat.NUMERO}</h3>
                        <p class="mb-0 text-muted">${stat.TITULO}</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editarEstadistica(${index})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-${stat.ACTIVO == 'S' ? 'success' : 'secondary'}"
                                    onclick="toggleEstadistica(${stat.ID}, '${stat.ACTIVO}')">
                                <i class="fa fa-eye${stat.ACTIVO == 'S' ? '' : '-slash'}"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="eliminarEstadistica(${stat.ID})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $('#estadisticasContainer').html(html);
}

function agregarEstadistica() {
    const modalHtml = `
        <div class="modal fade" id="modalEstadistica" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Estadística</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título</label>
                            <input type="text" class="form-control" id="modal_titulo" placeholder="Ej: Estudiantes Activos">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Número</label>
                            <input type="text" class="form-control" id="modal_numero" placeholder="Ej: 2,500+">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Icono (Font Awesome)</label>
                            <input type="text" class="form-control" id="modal_icono" placeholder="Ej: fas fa-users" value="fas fa-chart-bar">
                            <small class="text-muted">Visita <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Color</label>
                            <input type="color" class="form-control form-control-color w-100" id="modal_color" value="#667eea">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarNuevaEstadistica()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Eliminar modal anterior si existe
    $('#modalEstadistica').remove();
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modalEstadistica'));
    modal.show();
}

function guardarNuevaEstadistica() {
    const titulo = $('#modal_titulo').val();
    const numero = $('#modal_numero').val();
    const icono = $('#modal_icono').val();
    const color = $('#modal_color').val();

    if (!titulo || !numero) {
        mostrarNotificacion('Error', 'Por favor completa los campos obligatorios', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'crear');
    formData.append('titulo', titulo);
    formData.append('numero', numero);
    formData.append('icono', icono);
    formData.append('color', color);

    fetch(`${siteURL}assets/API/home/estadisticas.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            mostrarNotificacion('¡Éxito!', 'Estadística creada correctamente', 'success');
            $('#modalEstadistica').modal('hide');
            cargarEstadisticas();
        } else {
            mostrarNotificacion('Error', result.message, 'danger');
        }
    });
}

function editarEstadistica(index) {
    const stat = estadisticas[index];

    const modalHtml = `
        <div class="modal fade" id="modalEditarEstadistica" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Estadística</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" value="${stat.ID}">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título</label>
                            <input type="text" class="form-control" id="edit_titulo" value="${stat.TITULO}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Número</label>
                            <input type="text" class="form-control" id="edit_numero" value="${stat.NUMERO}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Icono (Font Awesome)</label>
                            <input type="text" class="form-control" id="edit_icono" value="${stat.ICONO}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Color</label>
                            <input type="color" class="form-control form-control-color w-100" id="edit_color" value="${stat.COLOR}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarEdicionEstadistica()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#modalEditarEstadistica').remove();
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modalEditarEstadistica'));
    modal.show();
}

function guardarEdicionEstadistica() {
    const formData = new FormData();
    formData.append('accion', 'actualizar');
    formData.append('id', $('#edit_id').val());
    formData.append('titulo', $('#edit_titulo').val());
    formData.append('numero', $('#edit_numero').val());
    formData.append('icono', $('#edit_icono').val());
    formData.append('color', $('#edit_color').val());

    fetch(`${siteURL}assets/API/home/estadisticas.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            mostrarNotificacion('¡Éxito!', 'Estadística actualizada', 'success');
            $('#modalEditarEstadistica').modal('hide');
            cargarEstadisticas();
        } else {
            mostrarNotificacion('Error', result.message, 'danger');
        }
    });
}

function toggleEstadistica(id, estadoActual) {
    const nuevoEstado = estadoActual === 'S' ? 'N' : 'S';
    const formData = new FormData();
    formData.append('accion', 'toggle');
    formData.append('id', id);
    formData.append('activo', nuevoEstado);

    fetch(`${siteURL}assets/API/home/estadisticas.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            cargarEstadisticas();
        }
    });
}

function eliminarEstadistica(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta estadística? Esta acción no se puede deshacer.')) {
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', id);

    fetch(`${siteURL}assets/API/home/estadisticas.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            mostrarNotificacion('¡Eliminado!', 'Estadística eliminada correctamente', 'success');
            cargarEstadisticas();
        } else {
            mostrarNotificacion('Error', result.message, 'danger');
        }
    });
}

// ===================================
// DESTACADOS
// ===================================

function cargarDestacados(tipo) {
    fetch(`${siteURL}assets/API/home/destacados.php?accion=listar&tipo=${tipo}`)
        .then(response => response.json())
        .then(result => {
            if (result.success == 1) {
                destacados[tipo] = result.data;
                renderizarDestacados(tipo);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function renderizarDestacados(tipo) {
    const containerId = tipo === 'club' ? 'destacadosClubesContainer' :
                        tipo === 'ministerio' ? 'destacadosMinisteriosContainer' :
                        'destacadosEventosContainer';

    const items = destacados[tipo];

    if (items.length === 0) {
        $(`#${containerId}`).html(`
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No hay ${tipo}s disponibles para destacar.
            </div>
        `);
        return;
    }

    let html = '';
    items.forEach(item => {
        const isDestacado = item.DESTACADO === 'S';
        html += `
            <div class="destacado-item ${isDestacado ? 'border-primary' : ''}">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    ${item.IMAGEN ? `<img src="${siteURL}${item.IMAGEN}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">` : ''}
                    <div>
                        <h6 class="mb-0">${item.NOMBRE}</h6>
                        <small class="text-muted">${item.DESCRIPCION || ''}</small>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox"
                           ${isDestacado ? 'checked' : ''}
                           onchange="toggleDestacado('${tipo}', ${item.ID}, this.checked)">
                    <label class="form-check-label">Destacar</label>
                </div>
            </div>
        `;
    });

    $(`#${containerId}`).html(html);
}

function toggleDestacado(tipo, idRegistro, destacado) {
    const formData = new FormData();
    formData.append('accion', destacado ? 'agregar' : 'quitar');
    formData.append('tipo', tipo);
    formData.append('id_registro', idRegistro);

    fetch(`${siteURL}assets/API/home/destacados.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            cargarDestacados(tipo);
        } else {
            mostrarNotificacion('Error', result.message, 'danger');
        }
    });
}

// ===================================
// GUARDAR CONFIGURACIÓN
// ===================================

function guardarConfiguracion() {
    const configuracion = {
        // Hero Section
        hero_titulo: $('#hero_titulo').val(),
        hero_subtitulo: $('#hero_subtitulo').val(),
        hero_boton_texto: $('#hero_boton_texto').val(),
        hero_boton_url: $('#hero_boton_url').val(),
        hero_color_inicio: $('#hero_color_inicio').val(),
        hero_color_fin: $('#hero_color_fin').val(),

        // Secciones
        seccion_clubes_mostrar: $('#seccion_clubes_mostrar').is(':checked') ? 'S' : 'N',
        seccion_clubes_titulo: $('#seccion_clubes_titulo').val(),
        seccion_clubes_subtitulo: $('#seccion_clubes_subtitulo').val(),
        seccion_clubes_cantidad: $('#seccion_clubes_cantidad').val(),

        seccion_ministerios_mostrar: $('#seccion_ministerios_mostrar').is(':checked') ? 'S' : 'N',
        seccion_ministerios_titulo: $('#seccion_ministerios_titulo').val(),
        seccion_ministerios_subtitulo: $('#seccion_ministerios_subtitulo').val(),
        seccion_ministerios_cantidad: $('#seccion_ministerios_cantidad').val(),

        seccion_eventos_mostrar: $('#seccion_eventos_mostrar').is(':checked') ? 'S' : 'N',
        seccion_eventos_titulo: $('#seccion_eventos_titulo').val(),
        seccion_eventos_subtitulo: $('#seccion_eventos_subtitulo').val(),
        seccion_eventos_cantidad: $('#seccion_eventos_cantidad').val(),

        seccion_stats_mostrar: $('#seccion_stats_mostrar').is(':checked') ? 'S' : 'N',
        seccion_stats_titulo: $('#seccion_stats_titulo').val(),

        // Footer
        footer_descripcion: $('#footer_descripcion').val(),
        footer_facebook: $('#footer_facebook').val(),
        footer_instagram: $('#footer_instagram').val(),
        footer_twitter: $('#footer_twitter').val(),
        footer_youtube: $('#footer_youtube').val()
    };

    const formData = new FormData();
    formData.append('configuracion', JSON.stringify(configuracion));

    // Agregar imagen si existe
    const imagenFondo = $('#hero_imagen_fondo')[0].files[0];
    if (imagenFondo) {
        formData.append('hero_imagen_fondo', imagenFondo);
    }

    // Mostrar indicador de carga
    const btnGuardar = event.target;
    const textoOriginal = btnGuardar.innerHTML;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    fetch(`${siteURL}assets/API/home/configuracion.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = textoOriginal;

        if (result.success == 1) {
            mostrarNotificacion('¡Éxito!', 'Configuración guardada correctamente', 'success');
        } else {
            mostrarNotificacion('Error', result.message, 'danger');
        }
    })
    .catch(error => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = textoOriginal;
        console.error('Error:', error);
        mostrarNotificacion('Error', 'Error al guardar la configuración', 'danger');
    });
}

// ===================================
// OTRAS FUNCIONES
// ===================================

function previsualizarHome() {
    window.open(siteURL, '_blank');
}

function eliminarImagen(seccion) {
    if (!confirm('¿Eliminar imagen? Se restaurará el gradiente de colores.')) {
        return;
    }

    const formData = new FormData();
    formData.append('accion', 'eliminar_imagen');
    formData.append('seccion', seccion);

    fetch(`${siteURL}assets/API/home/configuracion.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success == 1) {
            $('#preview_hero_imagen').parent().remove();
            mostrarNotificacion('¡Eliminada!', 'Imagen eliminada correctamente', 'success');
        }
    });
}

function mostrarNotificacion(titulo, mensaje, tipo) {
    jQuery.notify({
        title: titulo,
        message: mensaje
    }, {
        type: tipo,
        delay: 5000,
        placement: {
            from: 'top',
            align: 'right'
        }
    });
}
