<br><br>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>FECHA</th>
        <th>FOLIO</th>
        <th>TIPO</th>
        <th>CANT. PRODS</th>
        <th>COMENTARIO</th>
        <th>ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
      ?>
          <tr <?= ($row['AlmM_estado'] == 0) ? 'class="table-warning"' : ''; ?>>
            <td>
              <?= strtoupper(dateFormat($row['AlmM_fecha'], "numeros")); ?>
            </td>
            <td>
              <?= $row['AlmM_folio']; ?>
            </td>
            <td>
              <?php if ($row['AlmM_tipo'] == 0) {
                echo "<b>ENTRADA</b>";
              } else if ($row['AlmM_tipo'] == 1) {
                $uS_SQL = "SELECT usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_identificador'];
                $uSQRY = consultaData($uS_SQL)['dataFetch'][0];
                echo "<b>SALIDA INTERNA</b><br>";
                echo $uSQRY['usuario_nombre'] . " " . $uSQRY['usuario_apellido'];
              } else if ($row['AlmM_tipo'] == 2) {
                $rent_SQL = "SELECT * FROM Rentas
                                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                WHERE renta_id = " . $row['AlmM_identificador'];
                $rentQRY = consultaData($rent_SQL)['dataFetch'][0];
                echo "<b>SALIDA DE RENTA</b><br>";
                echo $rentQRY['cliente_rfc'] . " <b>|</b> " . $rentQRY['cliente_rs'] . " | " . $rentQRY['contrato_folio'] . "-" . $rentQRY['renta_folio'] . " | " . $rentQRY['renta_depto'];
              } else if ($row['AlmM_tipo'] == 3) {
                $clieQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = '" . $row['AlmM_identificador'] . "'")['dataFetch'][0];
                echo "<b>SALIDA DE VENTA</b><br>";
                echo $clieQRY['cliente_rfc'] . " <b>|</b> " . $clieQRY['cliente_rs'];
              } ?>
            </td>
            <td>
              <?php
              $cantProds = 0;
              $QRYcantProds = consultaData("SELECT * FROM AlmacenD WHERE AlmDM_id = " . $row['AlmM_id']);
              foreach ($QRYcantProds['dataFetch'] as $AlmD) {
                $cantProds = $cantProds + $AlmD['AlmD_cantidad'];
              }
              echo $cantProds;
              ?>
            </td>
            <td><?= $row['AlmM_comentario']; ?></td>
            <td>
              <button class="btn btn-info btnAction" data-tipo="details" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-info"></i></button>
              &nbsp;
              <?= ($row['AlmM_estado'] == 1) ? "<b>Activado</b>" : "<b>En Espera</b>"; ?>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>
<?php
// $QRY = consultaData("SELECT * FROM AlmacenP");
// $productos = [];
// foreach ($QRY['dataFetch'] as $row) {
//   $SQL2 = "SELECT * FROM AlmacenD
//           INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
//           WHERE AlmM_estado = 1
//           AND AlmM_tipo = 0
//           AND AlmDP_id = " . $row['AlmP_id'];
//   $QRY2 = consultaData($SQL2);
//   $AlmP_stock = 0;
//   if ($QRY2['numRows'] > 0) {
//     foreach ($QRY2['dataFetch'] as $row2) {
//       $AlmP_stock = $AlmP_stock + $row2['AlmD_cantidad'];
//     }
//   }

//   $arrayProd = [
//     "AlmP_id" => $row['AlmP_id'],
//     "AlmP_estado" => $row['AlmP_estado'],
//     "AlmP_stock" => $AlmP_stock
//   ];
//   array_push($productos, $arrayProd);
//   $AlmP_stock = 0;
// }

// print_r($productos);


?>