// Variables globales
let destacados = {
    club: [],
    ministerio: [],
    evento: []
};

// Inicializar al cargar la página
$(document).ready(function() {
    // Cargar datos al cambiar de tab
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr('href');

        if (target === '#tabDestacados') {
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
        hero_cantidad_botones: $('#hero_cantidad_botones').val(),
        hero_boton1_texto: $('#hero_boton1_texto').val(),
        hero_boton1_enlace: $('#hero_boton1_enlace').val(),
        hero_boton2_texto: $('#hero_boton2_texto').val(),
        hero_boton2_enlace: $('#hero_boton2_enlace').val(),
        hero_usar_video: $('#hero_usar_video').is(':checked') ? '1' : '0',
        hero_video_url: $('#hero_video_url').val(),

        // Eventos
        eventos_mostrar: $('#eventos_mostrar').is(':checked') ? '1' : '0',
        eventos_titulo: $('#eventos_titulo').val(),
        eventos_subtitulo: $('#eventos_subtitulo').val(),
        eventos_cantidad: $('#eventos_cantidad').val(),
        eventos_estilo: $('#eventos_estilo').val(),
        eventos_solo_destacados: $('#eventos_solo_destacados').is(':checked') ? '1' : '0',

        // Footer
        footer_descripcion: $('#footer_descripcion').val(),
        footer_direccion: $('#footer_direccion').val(),
        footer_telefono: $('#footer_telefono').val(),
        footer_email: $('#footer_email').val(),
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
            // Recargar la página después de 1 segundo para mostrar los cambios
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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
