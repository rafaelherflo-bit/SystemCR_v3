<div class="container-fluid table-responsive">
    <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
        <thead class="table-dark">
            <tr>
                <th>
                    <center>FECHA REPORTE</center>
                </th>
                <th>
                    <center>INICIO</center>
                </th>
                <th>
                    <center>FINAL</center>
                </th>
                <th>
                    <center>SERVICIO</center>
                </th>
                <th>
                    <center>AUTOR DE REPORTE</center>
                </th>
                <th>
                    <center>EQUIPO</center>
                </th>
                <th>
                    <center>ZONA</center>
                </th>
                <th>
                    <center>REPORTE</center>
                </th>
                <th>
                    <center>RESOLUCION</center>
                </th>
                <th>
                    <center>ACCIONES</center>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($queryReportes['numRows'] != 0) {
                foreach ($queryReportes['dataFetch'] as $rowRenta) { ?>
                    <tr class="filaTBody" id="<?= encryption($rowRenta['reporte_id']); ?>">
                        <td><?= strtoupper(dateFormat($rowRenta['reporte_fecha'], 'simple')) . "<br>" . explode(" ", $rowRenta['reporte_fecha'])[1]; ?></td>
                        <td>
                            <?php
                            if ($rowRenta['reporte_fecha_inicio'] == NULL) {
                                echo "";
                            } else {
                                echo strtoupper(dateFormat($rowRenta['reporte_fecha_inicio'], 'simple')) . "<br>" . explode(" ", $rowRenta['reporte_fecha_inicio'])[1];
                            }
                            ?>
                        </td>
                        <td><?= strtoupper(dateFormat($rowRenta['reporte_fecha_fin'], 'simple')) . "<br>" . explode(" ", $rowRenta['reporte_fecha_fin'])[1]; ?></td>
                        <td><?= "(" . $rowRenta['cliente_rfc'] . ") - " . $rowRenta['cliente_rs'] . "<br>" . $rowRenta['contrato_folio'] . "-" . $rowRenta['renta_folio'] . " | " . $rowRenta['renta_depto']; ?></td>
                        <td><?= ($rowRenta['reporte_wmakes'] == 0) ? "No Especificado" : "<b>" . $rowRenta['reporte_wmakes'] . "</b>"; ?></td>
                        <td><?= $rowRenta['modelo_linea'] . " " . $rowRenta['modelo_modelo'] . "<br>" . $rowRenta['equipo_codigo'] . " | " . $rowRenta['equipo_serie']; ?></td>
                        <td><?= $rowRenta['zona_nombre']; ?></td>
                        <td><?= $rowRenta['reporte_reporte']; ?></td>
                        <td><?= $rowRenta['reporte_resolucion']; ?></td>
                        <td>
                            <button class="btn btn-warning btnRepComp" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="0"><i class="fas fa-pen"></i></button>
                            <?php if ($rowRenta['reporte_archivo'] != "") { ?>
                                <button class="btn btn-success btnRepComp" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="1"><i class="fas fa-file-pdf"></i></button>
                            <?php } ?>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>