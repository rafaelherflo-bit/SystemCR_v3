<?php
$sqlRepMY = "SELECT * FROM Reportes
INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE reporte_estado = 0";
$queryReportes = consultaData($sqlRepMY); ?>

<div class="container-fluid">
    <center>
        <h3><i class="fas fa-hourglass fa-fw"></i> &nbsp; <b><?= consultaData("SELECT * FROM Reportes WHERE reporte_estado = 0")['numRows']; ?></b> &nbsp; ACTIVOS</h3>
    </center>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVERURL . $pagina[0]; ?>/Iniciar"><i class="fas fa-play fa-fw"></i> &nbsp; INICIAR</a>
        </li>
        <li>
            <a href="<?= SERVERURL . $pagina[0]; ?>/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
        </li>
        <li>
            <?= customDMY($pagina[0]); ?>
        </li>
    </ul>
</div>

<div class="container-fluid table-responsive">
    <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
        <thead class="table-dark">
            <tr>
                <th>
                    <center>FECHA INICIO</center>
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
                    <center>ACCIONES</center>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($queryReportes['numRows'] != 0) {
                foreach ($queryReportes['dataFetch'] as $rowRenta) { ?>
                    <tr>
                        <td>
                            <?= strtoupper(dateFormat($rowRenta['reporte_fecha'], 'simple')) . "<br>" . explode(" ", $rowRenta['reporte_fecha'])[1]; ?>
                        </td>
                        <td><?= "(" . $rowRenta['cliente_rfc'] . ") - " . $rowRenta['cliente_rs'] . "<br>" . $rowRenta['contrato_folio'] . "-" . $rowRenta['renta_folio'] . " | " . $rowRenta['renta_depto']; ?></td>
                        <td><?= $rowRenta['reporte_wmakes']; ?></td>
                        <td><?= $rowRenta['modelo_linea'] . " " . $rowRenta['modelo_modelo'] . "<br>" . $rowRenta['equipo_codigo'] . " | " . $rowRenta['equipo_serie']; ?></td>
                        <td><?= $rowRenta['zona_nombre']; ?></td>
                        <td><?= $rowRenta['reporte_reporte']; ?></td>
                        <td>
                            <button class="btn btn-danger btnRepAction" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="0"><i class="fas fa-trash"></i></button>
                            <button class="btn btn-warning btnRepAction" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="1"><i class="fas fa-pen"></i></button>
                            <button class="btn btn-success btnRepAction" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="2"><i class="fas fa-check-double"></i></button>
                            <button class="btn btn-info btnRepAction" data-id="<?= encryption($rowRenta['reporte_id']); ?>" data-tipo="3"><i class="fas fa-print"></i></button>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>