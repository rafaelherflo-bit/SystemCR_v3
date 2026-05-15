<?php
$sqlRepMY = "SELECT * FROM ReportesF
            INNER JOIN Clientes ON ReportesF.reporteF_cliente_id = Clientes.cliente_id
            INNER JOIN Modelos ON ReportesF.reporteF_equ_modelo_id = Modelos.modelo_id
            WHERE MONTH(reporteF_fecha) = $mes
            AND YEAR(reporteF_fecha) = $anio";
$queryReportesF = consultaData($sqlRepMY); ?>

<input type="hidden" id="custom_anio" value="<?= $anio; ?>">
<input type="hidden" id="custom_mes" value="<?= $mes; ?>">
<input type="hidden" id="periodoCustom" value="<?= ucfirst(dateFormat($anio . "-" . $mes . "-" . date("d"), "mesanio")); ?>">

<div class="container-fluid">
  <center>
    <h3><?= strtoupper(dateFormat($anio . "-" . $mes . "-" . date("d"), "mesanio")); ?></h3>
  </center>
</div>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL . $pagina[0]; ?>/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <?php
      filtroCustom("ReportesF"); // Función externa para mostrar el filtro de fecha/periodo 
      ?>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>FOLIO</center>
        </th>
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
      if ($queryReportesF['numRows'] != 0) {
        foreach ($queryReportesF['dataFetch'] as $ROW) { ?>
          <tr class="filaTBody" id="<?= encryption($ROW['reporteF_id']); ?>">
            <td><?= $ROW['reporteF_folio']; ?></td>
            <td><?= strtoupper(dateFormat($ROW['reporteF_fecha'], 'simple')) . "<br>" . explode(" ", $ROW['reporteF_fecha'])[1]; ?></td>
            <td>
              <?php
              if ($ROW['reporteF_fecha_inicio'] == NULL) {
                echo "";
              } else {
                echo strtoupper(dateFormat($ROW['reporteF_fecha_inicio'], 'simple')) . "<br>" . explode(" ", $ROW['reporteF_fecha_inicio'])[1];
              }
              ?>
            </td>
            <td><?= strtoupper(dateFormat($ROW['reporteF_fecha_fin'], 'simple')) . "<br>" . explode(" ", $ROW['reporteF_fecha_fin'])[1]; ?></td>
            <td><?= "(" . $ROW['cliente_rfc'] . ") - " . $ROW['cliente_rs']; ?></td>
            <td><?= ($ROW['reporteF_wmakes'] == 0) ? "No Especificado" : "<b>" . $ROW['reporteF_wmakes'] . "</b>"; ?></td>
            <td><?= $ROW['modelo_linea'] . " " . $ROW['modelo_modelo'] . "<br>" . $ROW['reporteF_equ_serie']; ?></td>
            <td><?= $ROW['reporteF_reporte']; ?></td>
            <td><?= $ROW['reporteF_resolucion']; ?></td>
            <td>
              <button class="btn btn-warning btnRepFComp" data-id="<?= encryption($ROW['reporteF_id']); ?>" data-tipo="0"><i class="fas fa-pen"></i></button>
              <?php if (file_exists(SERVERDIR . "DocsCR/" . $pagina[0] . "/" . explode("-", $ROW['reporteF_fecha'])[0] . "/" . explode("-", $ROW['reporteF_fecha'])[1] . "/" . $ROW['reporteF_folio'] . ".pdf")) { ?>
                <button class="btn btn-success btnRepFComp" data-id="<?= encryption($ROW['reporteF_id']); ?>" data-tipo="1"><i class="fas fa-file-pdf"></i></button>
              <?php } ?>
            </td>
          </tr>
      <?php }
      } ?>
    </tbody>
  </table>
</div>