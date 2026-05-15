<?php
// Supongamos que obtienes los parámetros de la URL
$anio = (int)$contenido[1];
$mes = (int)$contenido[2];

// Consulta SQL mejorada con LEFT JOIN a la tabla de checks
$SQL = "SELECT R.*, C.cliente_rs, C.cliente_emiFact, 
        CK.check_facturado, CK.check_pagado ,
        CON.contrato_folio
        FROM Rentas R
        INNER JOIN Contratos CON ON R.renta_contrato_id = CON.contrato_id
        INNER JOIN Clientes C ON CON.contrato_cliente_id = C.cliente_id
        LEFT JOIN Rentas_Checks CK ON R.renta_id = CK.renta_id 
        AND CK.check_anio = '$anio' 
        AND CK.check_mes = '$mes'
        WHERE R.renta_estado = 'Activo' 
        ORDER BY C.cliente_rs ASC";

$datos = consultaData($SQL);
?>
<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <ul class="full-box list-unstyled page-nav-tabs">
        <li>
          <a class="active" class=""><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR RENTA</a>
        </li>
        <li>
          <a href="<?= SERVERURL; ?>Rentas/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE RENTAS</a>
        </li>
        <li>
          <a href="<?= SERVERURL; ?>Rentas/Otros"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; NO ACTIVOS</a>
        </li>
        <li>
          <a id="btnCostos"><i class="fas fa-file-pdf fa-fw"></i> &nbsp; COSTOS POR RENTAS</a>
        </li>
        <li>
          <?= filtroCheck("") ?>
        </li>
      </ul>
      <center>
        <h2><b>CHEQUEO DE <?= strtoupper(dateFormat($anio . "-" . $mes . "-" . date("d"), "mesanio")) ?></b></h2>
      </center>
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text bg-dark text-white"><i class="fas fa-search"></i></span>
            <input type="text" id="inputBuscador" class="form-control" placeholder="Buscar por folio, cliente, renta o costo...">
          </div>
        </div>
      </div>

      <div class="table-responsive shadow-sm rounded">
        <table id="tablaRentasCheck" class="table table-sm table-hover table-secondary table-striped w-100 align-middle">
          <thead class="table-dark">
            <tr>
              <th class="text-center">NO</th>
              <th class="text-center">EMISOR</th>
              <th class="text-center">FACTURADO</th>
              <th class="text-center">PAGADO</th>
              <th class="text-center">FOLIO</th>
              <th class="text-center">CLIENTE</th>
              <th class="text-center">RENTA</th>
              <th class="text-center">COSTO</th>
            </tr>
          </thead>
          <thead class="table-dark">
            <tr>
              <th colspan="6"></th>
              <th style="text-align: right;">MONTO TOTAL: </th>
              <th id="montoTotalHTML" class="text-left">
                $<?= number_format(consultaData("SELECT SUM(renta_costo) AS monto_total FROM Rentas WHERE renta_estado = 'Activo'")['dataFetch'][0]['monto_total'], 2) ?>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php
            $n = 1;
            foreach ($datos['dataFetch'] as $row):
            ?>
              <tr>
                <td class="text-center"><?= $n++; ?></td>
                <td><?= $row['cliente_emiFact'] == 1 ? "RENAN ARMANDO" : "MIMI FLORES"; ?></td>
                <td class="text-center">
                  <input type="checkbox" class="form-check-input check-renta" id="<?= $row['renta_id']; ?>" data-tipo="facturado" data-anio="<?= $anio ?>" data-mes="<?= $mes ?>" <?= ($row['check_facturado'] == 1) ? 'checked' : ''; ?>>
                </td>
                <td class="text-center">
                  <input type="checkbox" class="form-check-input check-renta" id="<?= $row['renta_id']; ?>" data-tipo="pagado" data-anio="<?= $anio ?>" data-mes="<?= $mes ?>" <?= ($row['check_pagado'] == 1) ? 'checked' : ''; ?>>
                </td>
                <td><?= $row['contrato_folio'] . "-" . $row['renta_folio']; ?></td>
                <td><?= $row['cliente_rs']; ?></td>
                <td><?= $row['renta_depto']; ?></td>
                <td class="monto-fila" data-valor="<?= $row['renta_costo']; ?>">
                  $<?= number_format($row['renta_costo'], 2); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>