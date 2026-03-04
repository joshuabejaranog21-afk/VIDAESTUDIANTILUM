<?php
include('../../assets/php/template.php');
$temp = new Template('Ejemplo');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es" data-footer="true" data-override='{"showSettings":false,"attributes": {"placement": "vertical" }}'>

<head>
    <?php $temp->head() ?>
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>assets/css/vendor/bootstrap-datepicker3.standalone.min.css" />
    <link rel="stylesheet" href="<?php echo $temp->siteURL ?>assets/css/vendor/baguetteBox.min.css">
</head>

<body>
    <div id="root">
        <?php $temp->nav() ?>

        <main>
            <div class="container">
                <!-- Title and Top Buttons Start -->
                <div class="page-title-container">
                    <div class="row">
                        <!-- Title Start -->
                        <div class="col-12 col-md-7">
                            <h1 class="mb-0 pb-0 display-4" id="title"><?php echo $temp->titulo ?></h1>
                        </div>
                        <!-- Title End -->
                    </div>
                </div>
                <!-- Title and Top Buttons End -->

                <!-- Content Start -->
                <div class="row">
                    <div id="col-create" class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <form autocomplete="off">
                                    <input type="hidden" id="form-id">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input id="form-nombre" type="text" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Correo</label>
                                        <input id="form-correo" type="text" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <input id="form-observaciones" type="text" class="form-control" />
                                    </div>
                                </form>
                                <div id="btnCrear">
                                    <button type="button" class="btn btn-outline-primary" onclick="resetform()">Limpiar</button>
                                    <button type="button" class="btn btn-primary" onclick="crear()">Crear</button>
                                </div>
                                <div id="btnMod" class="d-none">
                                    <button type="button" class="btn btn-outline-primary" onclick='$("#btnCrear").removeClass("d-none");$("#btnMod").addClass("d-none");resetform()'>Cancelar</button>
                                    <button type="button" class="btn btn-primary" onclick="modificar()">Actualizar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="data-table-rows slim">
                            <div class="data-table-responsive-wrapper" id="contentTable"></div>
                        </div>
                    </div>
                </div>
                <!-- Content End -->
            </div>
        </main>
        <!-- Layout Footer Start -->
        <?php $temp->footer() ?>
        <!-- Layout Footer End -->
    </div>
    <!-- Theme Settings Modal Start -->
    <?php $temp->modalSettings() ?>
    <!-- Theme Settings Modal End -->

    <!-- Search Modal Start -->
    <?php $temp->modalSearch() ?>
    <!-- Search Modal End -->

    <?php $temp->scripts() ?>
    <script src="<?php echo $temp->siteURL ?>assets/js/vendor/baguetteBox.min.js"></script>
    <script src="<?php echo $temp->siteURL ?>assets/js/vendor/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $temp->siteURL ?>assets/js/vendor/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="<?php echo $temp->siteURL ?>assets/js/forms/controls.datepicker.js"></script>
    <script src="<?php echo $temp->siteURL ?>assets/API/apiFetcher.js"></script>
    <script>
        const url = "<?php echo $temp->siteURL ?>assets/API/example/"
        let fetcher = new apiFetcher(url)

        function leer() {
            fetcher.getHTML('tabla.php', '#contentTable')
        }

        async function crear() {
            const info = [{
                    name: "nombre",
                    value: $("#form-nombre").val()
                },
                {
                    name: "correo",
                    value: $("#form-correo").val()
                },
                {
                    name: "observaciones",
                    value: $("#form-observaciones").val()
                }
            ]
            if (await fetcher.sendPost('crear/', info)) {
                leer()
                resetform()
            }
        }

        function initModificar(id) {
            fetch(url + "leer/" + id)
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        $('#form-id').val(id);
                        $('#form-nombre').val(result.data.NOMBRE);
                        $('#form-correo').val(result.data.CORREO);
                        $('#form-observaciones').val(result.data.OBSERVACIONES);

                        $("#btnCrear").addClass("d-none");
                        $("#btnMod").removeClass("d-none");
                    }
                })
        }

        async function modificar() {
            let contenido = ""
            if ($("#form-tipo").val() == 1) {
                contenido = $("#form-video").val()
            }
            const info = [{
                    name: "id",
                    value: $("#form-id").val()
                },
                {
                    name: "nombre",
                    value: $("#form-nombre").val()
                },
                {
                    name: "correo",
                    value: $("#form-correo").val()
                },
                {
                    name: "observaciones",
                    value: $("#form-observaciones").val()
                }
            ]
            if (await fetcher.sendPost('actualizar/', info)) {
                leer()
                resetform()
                $("#btnCrear").removeClass("d-none")
                $("#btnMod").addClass("d-none")
            }
        }

        async function borrar(id) {
            var ruta = 'borrar/' + id;
            if (await fetcher.sendGet(ruta)) {
                leer()
                console.log("elemento borrado");
            }
        }

        function resetform() {
            $("form select").each(function() {
                this.selectedIndex = 0
            });
            $("form input[type=text], form input[type=number], form textarea").each(function() {
                this.value = ''
            });
            $('.searchRangePicker input').each(function() {
                $(this).datepicker('clearDates');
            });
        }

        window.addEventListener('ready', leer());
    </script>
</body>

</html>