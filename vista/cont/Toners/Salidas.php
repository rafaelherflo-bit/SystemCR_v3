<?php
$DT_orderType = "desc";
$DT_orderCol = 0;
$consulta = "SELECT * FROM TonersRegistrosS
            INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
            INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id";
$query = consultaData($consulta); ?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salida"><i class="fas fa-sign-out-alt"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
  </ul>
</div>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>FECHA REGISTRO</center>
        </th>
        <th>
          <center>FOLIO</center>
        </th>
        <th>
          <center>CODIGO</center>
        </th>
        <th>
          <center>COMPATIBILIDAD</center>
        </th>
        <th>
          <center>NO. DE PARTE</center>
        </th>
        <th>
          <center>CANTIDAD</center>
        </th>
        <th>
          <center>PROVEEDOR</center>
        </th>
        <th>
          <center>DETALLES</center>
        </th>
        <th>
          <center>COMENTARIOS</center>
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($query['numRows'] != 0) {
        foreach ($query['dataFetch'] as $row) {
      ?>
          <tr>
            <td>
              <center>
                <?= str_replace(" ", "", dateFormat($row['tonerRO_fecha'], "numeros")); ?>
              </center>
            </td>
            <td style="width: 100px;">
              <center>
                <?php
                $DIR = SERVERDIR . "DocsCR/ALMACEN/";
                $TipoReg = ($row['tonerRO_tipo'] == "Interno") ? "INTERNOS" : (($row['tonerRO_tipo'] == "Venta") ? "VENTAS" : (($row['tonerRO_tipo'] == "Renta") ? "RENTAS" : ""));
                list($anioRST, $mesRST, $diaRST) = explode("-", $row['tonerRO_fecha']);
                $DIR = $DIR . $TipoReg . "/" . $anioRST . "/" . $mesRST . "/" . $row['tonerRO_folio'] . ".pdf";

                if (file_exists($DIR)) {
                  echo "<span class='btn btn-info btn-sm pdf-RST' data-tipo='" . $TipoReg . "' data-folio='" . $row['tonerRO_folio'] . "' data-fecha='" . $row['tonerRO_fecha'] . "'>" . $row['tonerRO_folio'] . "</span>";
                } else {
                  echo "<b>" . $row['tonerRO_folio'] . "</b>";
                }
                ?>
              </center>
            </td>
            <td style="width: 100px;">
              <center>
                <?= $row['toner_codigo']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $row['provT_nombre']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $row['toner_parte']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $row['tonerRO_cantidad']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $row['toner_comp']; ?>
              </center>
            </td>
            <?php if ($row['tonerRO_tipo'] == "Venta") {
              $clienteData = consultaData("SELECT * FROM Clientes WHERE cliente_id = " . $row['tonerRO_identificador'])['dataFetch'][0];
            ?>
              <td>
                <center>
                  <?= "<b>Tipo de salida:</b> " . $row['tonerRO_tipo'] . "<br>(" . $clienteData['cliente_rfc'] . ") " . $clienteData['cliente_rs'] . "<br><b>Retirado Por:</b> " . $row['tonerRO_empleado']; ?>
                </center>
              </td>
            <?php } else if ($row['tonerRO_tipo'] == "Renta") {
              $sqlRenta = "SELECT * FROM Rentas
                            INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                            INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                            WHERE renta_id = " . $row['tonerRO_identificador'];
              $rentaData = consultaData($sqlRenta)['dataFetch'][0];
            ?>
              <td>
                <center>
                  <?= "<b>Tipo de salida:</b> " . $row['tonerRO_tipo'] . "<br>" . $rentaData['contrato_folio'] . "-" . $rentaData['renta_folio'] . " | " . $rentaData['renta_depto'] . "<br><b>Retirado Por:</b> " . $row['tonerRO_empleado']; ?>
                </center>
              </td>
            <?php } else { ?>
              <td>
                <center>
                  <?= "<b>Tipo de salida:</b> " . $row['tonerRO_tipo'] . "<br><b>Retirado Por:</b> " . $row['tonerRO_empleado']; ?>
                </center>
              </td>
            <?php } ?>
            <td>
              <center>
                <?= $row['tonerRO_comm']; ?>
              </center>
            </td>
            <td>
              <spaw class="btn btn-warning btn-edit-RST" id="<?= encryption($row['tonerRO_id']); ?>">Editar</spaw>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>