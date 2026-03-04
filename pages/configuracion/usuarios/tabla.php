<?php
include('../../../assets/php/template.php');
$temp = new Template();
$db = new Conexion();
if ($temp->validate_session()) :
?>
    <table id="datatableRows" class="data-table nowrap hover">
        <thead>
            <tr>
                <th class="text-muted text-small">Nombre</th>
                <th class="empty">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cad = "SELECT *FROM SYSTEM_USUARIOS WHERE ID_CAT != 1";
            $sql = $db->query($cad);
            if ($db->rows($sql) > 0) {
                foreach ($sql as $key) {
                    if($key['ACTIVO'] == 'S'){
                        $valActivo = "N";
                        $legActivo = "Desactivar";
                        $icActivo = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-eye-off undefined"><path d="M14.5 5.68074C15.9466 6.81369 16.9725 8.2601 17.5253 9.15822C17.8463 9.67971 17.8463 10.3203 17.5253 10.8418C16.5086 12.4936 13.8918 16 9.99998 16C8.30107 16 6.84513 15.3318 5.6592 14.4414M3.33851 12.0973C2.98012 11.6292 2.69112 11.1935 2.47464 10.8418C2.15363 10.3203 2.15363 9.67971 2.47464 9.15822C3.49141 7.50643 6.10817 4 9.99998 4C10.3433 4 10.6767 4.02729 11 4.07819"></path><path d="M11.4256 8.59735C11.7808 8.95833 11.9999 9.45358 11.9999 10C11.9999 11.1046 11.1045 12 9.99995 12C9.4777 12 9.0022 11.7998 8.646 11.472"></path><path d="M18 2L2 18"></path></svg>';
                    } else {
                        $valActivo = "S";
                        $legActivo = "Reactivar";
                        $icActivo = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-eye undefined"><path d="M2.47466 10.8418C2.15365 10.3203 2.15365 9.67971 2.47466 9.15822C3.49143 7.50643 6.10818 4 10 4C13.8918 4 16.5086 7.50644 17.5253 9.15822C17.8464 9.67971 17.8464 10.3203 17.5253 10.8418C16.5086 12.4936 13.8918 16 10 16C6.10818 16 3.49143 12.4936 2.47466 10.8418Z"></path><path d="M10 12C11.1046 12 12 11.1046 12 10C12 8.89543 11.1046 8 10 8C8.89543 8 8 8.89543 8 10C8 11.1046 8.89543 12 10 12Z"></path></svg>';
                    }
            ?>
                    <tr>
                        <td><?php echo $key['NOMBRE'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" data-bs-toggle="modal" data-bs-target="#modalMod" onclick="initModificar('<?php echo $key['ID'] ?>','<?php echo $key['NOMBRE'] ?>','<?php echo $key['ID_CAT'] ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-edit-square undefined">
                                    <path d="M11 2L5.5 2C4.09554 2 3.39331 2 2.88886 2.33706C2.67048 2.48298 2.48298 2.67048 2.33706 2.88886C2 3.39331 2 4.09554 2 5.5L2 14.5C2 15.9045 2 16.6067 2.33706 17.1111C2.48298 17.3295 2.67048 17.517 2.88886 17.6629C3.39331 18 4.09554 18 5.5 18L14.5 18C15.9045 18 16.6067 18 17.1111 17.6629C17.3295 17.517 17.517 17.3295 17.6629 17.1111C18 16.6067 18 15.9045 18 14.5L18 11"></path>
                                    <path d="M15.4978 3.06224C15.7795 2.78052 16.1616 2.62225 16.56 2.62225C16.9585 2.62225 17.3405 2.78052 17.6223 3.06224C17.904 3.34396 18.0623 3.72605 18.0623 4.12446C18.0623 4.52288 17.904 4.90497 17.6223 5.18669L10.8949 11.9141L8.06226 12.6223L8.7704 9.78966L15.4978 3.06224Z"></path>
                                </svg>
                                <span class="d-none d-xxl-inline-block">Editar</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="resetPassword('<?php echo $key['ID'] ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-lock-on undefined"><path d="M5 12.6667C5 12.0467 5 11.7367 5.06815 11.4824C5.25308 10.7922 5.79218 10.2531 6.48236 10.0681C6.73669 10 7.04669 10 7.66667 10H12.3333C12.9533 10 13.2633 10 13.5176 10.0681C14.2078 10.2531 14.7469 10.7922 14.9319 11.4824C15 11.7367 15 12.0467 15 12.6667V13C15 13.9293 15 14.394 14.9231 14.7804C14.6075 16.3671 13.3671 17.6075 11.7804 17.9231C11.394 18 10.9293 18 10 18V18C9.07069 18 8.60603 18 8.21964 17.9231C6.63288 17.6075 5.39249 16.3671 5.07686 14.7804C5 14.394 5 13.9293 5 13V12.6667Z"></path><path d="M11 15H10 9M13 10V5C13 3.34315 11.6569 2 10 2V2C8.34315 2 7 3.34315 7 5V10"></path></svg>
                                <span class="d-none d-xxl-inline-block">Reiniciar contraseña</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="usuarioActivo('<?php echo $key['ID'] ?>','<?php echo $valActivo ?>')">
                                <?php echo $icActivo ?>
                                <span class="d-none d-xxl-inline-block"><?php echo $legActivo ?></span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-primary ms-1" type="button" onclick="borrar('<?php echo $key['ID'] ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-bin undefined">
                                    <path d="M4 5V14.5C4 15.9045 4 16.6067 4.33706 17.1111C4.48298 17.3295 4.67048 17.517 4.88886 17.6629C5.39331 18 6.09554 18 7.5 18H12.5C13.9045 18 14.6067 18 15.1111 17.6629C15.3295 17.517 15.517 17.3295 15.6629 17.1111C16 16.6067 16 15.9045 16 14.5V5"></path>
                                    <path d="M14 5L13.9424 4.74074C13.6934 3.62043 13.569 3.06028 13.225 2.67266C13.0751 2.50368 12.8977 2.36133 12.7002 2.25164C12.2472 2 11.6734 2 10.5257 2L9.47427 2C8.32663 2 7.75281 2 7.29981 2.25164C7.10234 2.36133 6.92488 2.50368 6.77496 2.67266C6.43105 3.06028 6.30657 3.62044 6.05761 4.74074L6 5"></path>
                                    <path d="M2 5H18M12 9V13M8 9V13"></path>
                                </svg>
                                <span class="d-none d-xxl-inline-block">Borrar</span>
                            </button>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
    <script type="text/javascript">
        $(document).ready(function() {
            $('table#datatableRows').DataTable({
                "order": [
                    [0, 'desc']
                ],
                buttons: ['copy', 'excel', 'csv', 'print'],
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 20, -1],
                    [10, 20, "All"]
                ],
                responsive: true,
                language: {
                    "decimal": "",
                    "emptyTable": "Sin datos disponibles",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ campos",
                    "infoEmpty": "Mostrando 0 a 0 de 0 datos",
                    "infoFiltered": "(Buscado entre _MAX_ datos totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Ver _MENU_ filas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se han encontrado resultados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                }
            });
        });
    </script>
<?php endif; ?>