<?php
include('../../assets/php/template.php');
$temp = new Template();
$db = new Conexion();
if ($temp->validate_session()) :
?>
    <table id="datatableRows" class="data-table hover">
        <thead>
            <tr>
                <th class="text-muted text-small">Nombre</th>
                <th class="text-muted text-small">Correo</th>
                <th class="text-muted text-small">Observaciones</th>
                <th class="empty">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cad = "SELECT *FROM EXAMPLE";
            $sql = $db->query($cad);
            if ($db->rows($sql) > 0) {
                foreach ($sql as $key) {
            ?>
                    <tr>
                        <td><?php echo $key['NOMBRE'] ?></td>
                        <td><?php echo $key['CORREO'] ?></td>
                        <td><?php echo $key['OBSERVACIONES'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-secondary ms-1" type="button" onclick="initModificar('<?php echo $key['ID'] ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-edit-square undefined">
                                    <path d="M11 2L5.5 2C4.09554 2 3.39331 2 2.88886 2.33706C2.67048 2.48298 2.48298 2.67048 2.33706 2.88886C2 3.39331 2 4.09554 2 5.5L2 14.5C2 15.9045 2 16.6067 2.33706 17.1111C2.48298 17.3295 2.67048 17.517 2.88886 17.6629C3.39331 18 4.09554 18 5.5 18L14.5 18C15.9045 18 16.6067 18 17.1111 17.6629C17.3295 17.517 17.517 17.3295 17.6629 17.1111C18 16.6067 18 15.9045 18 14.5L18 11"></path>
                                    <path d="M15.4978 3.06224C15.7795 2.78052 16.1616 2.62225 16.56 2.62225C16.9585 2.62225 17.3405 2.78052 17.6223 3.06224C17.904 3.34396 18.0623 3.72605 18.0623 4.12446C18.0623 4.52288 17.904 4.90497 17.6223 5.18669L10.8949 11.9141L8.06226 12.6223L8.7704 9.78966L15.4978 3.06224Z"></path>
                                </svg>
                                <span class="d-none d-xxl-inline-block">Editar</span>
                            </button>
                            <button class="btn btn-sm btn-icon btn-icon-start btn-outline-danger ms-1" type="button" onclick="borrar('<?php echo $key['ID'] ?>')">
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
                    [0, 'asc']
                ],
                dom: `
                        <"row"<"col-sm-12 text-center"B>>
                        <"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>
                        <"row"<"col-sm-12"tr>>
                        <"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7 text-end"p>>
                    `,
                buttons: [{
                        extend: 'csv',
                        text: '<i class="fa-solid fa-file-csv"></i> <span>CSV</span>',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> <span>Excel</span>',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> <span>Imprimir</span>',
                        exportOptions: {
                            columns: ':visible:not(:last-child)'
                        }
                    }
                ],
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
        baguetteBox.run('.lightbox');
    </script>
<?php endif; ?>