<?php
include('assets/php/template.php');
$temp = new Template();
$db = new Conexion();
if ($temp->validate_session()) :
?>
    <table id="datatableRows" class="data-table nowrap hover">
        <thead>
            <tr>
                <th class="text-muted text-small">Fecha</th>
                <th class="text-muted text-small">Suscriptor</th>
                <th class="text-muted text-small">Correo</th>
                <th class="text-muted text-small">Archivo</th>
                <th class="text-muted text-small">Categoría</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $cad = "SELECT PM_DESCARGA.FECHA, PM_SUSCRIPTOR.NOMBRE, PM_SUSCRIPTOR.CORREO, PM_ARCHIVO.TITULO, PM_CATEGORIA.NOMBRE AS CATEGORIA FROM PM_DESCARGA INNER JOIN PM_ARCHIVO ON PM_DESCARGA.ID_ARCHIVO = PM_ARCHIVO.ID INNER JOIN PM_SUSCRIPTOR ON PM_DESCARGA.ID_SUSCRIPTOR = PM_SUSCRIPTOR.ID INNER JOIN PM_CATEGORIA ON PM_ARCHIVO.ID_CATEGORIA = PM_CATEGORIA.ID";
            $sql = $db->query($cad);
            if ($db->rows($sql) > 0) {
                foreach ($sql as $key) {
                    if ($key['IVA'] == 'S') {
                        $key['IVA'] = 'Si';
                    } else {
                        $key['IVA'] = 'No';
                    }
            ?>
                    <tr>
                        <td><?php echo $key['FECHA'] ?></td>
                        <td><?php echo $key['NOMBRE'] ?></td>
                        <td><?php echo $key['CORREO'] ?></td>
                        <td><?php echo $key['TITULO'] ?></td>
                        <td><?php echo $key['CATEGORÍA'] ?></td>
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