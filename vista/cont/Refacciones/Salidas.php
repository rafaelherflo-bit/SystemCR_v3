<?php
$consulta = "SELECT * FROM RefaccionesRegistrosS
                INNER JOIN Refacciones ON RefaccionesRegistrosS.refRS_ref_id = Refacciones.ref_id
                INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
                INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id";
$query = consultaData($consulta); ?>
<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
        </li>
        <li>
            <a href="<?= SERVERURL; ?>Refacciones/Salida"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR SALIDA</a>
        </li>
        <!-- <li>
            <a class="active"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
        </li> -->
    </ul>
</div>

<center>
    <h4><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Registros de Salida</h4>
</center>

<div class="container-fluid table-responsive">
    <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
        <thead class="table-dark">
            <tr>
                <th>
                    <center>FECHA</center>
                </th>
                <th>
                    <center>CATEGORIA</center>
                </th>
                <th>
                    <center>CODIGO</center>
                </th>
                <th>
                    <center>CANTIDAD</center>
                </th>
                <th>
                    <center>COMPATIBILIDAD</center>
                </th>
                <th>
                    <center>PROVEEDOR</center>
                </th>
                <th>
                    <center>EMPLEADO</center>
                </th>
                <th>
                    <center>COMENTARIOS</center>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($query['numRows'] != 0) {
                foreach ($query['dataFetch'] as $row) {
            ?>
                    <tr>
                        <td><?= strtoupper(dateFormat($row['refRS_fecha'], 'simple')); ?></td>
                        <td><?= $row['catR_nombre']; ?></td>
                        <td><?= $row['ref_codigo']; ?></td>
                        <td>
                            <center><?= $row['refRS_cant']; ?></center>
                        </td>
                        <td><?= $row['ref_comp']; ?></td>
                        <td><?= $row['provR_nombre']; ?></td>
                        <td><?= $row['refRS_empleado']; ?></td>
                        <td><?= $row['refRS_comm']; ?></td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>