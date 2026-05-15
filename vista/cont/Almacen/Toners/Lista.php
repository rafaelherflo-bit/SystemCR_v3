<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th style="text-align: center;"># DE PRODUCTO</th>
        <th style="text-align: center;">NO. DE PARTE</th>
        <th style="text-align: center;">MODELO DE TONER</th>
        <th style="text-align: center;">TIPO</th>
        <th style="text-align: center;">PROVEEDOR</th>
        <th style="text-align: center;">COMPATIBILIDAD</th>
        <th style="text-align: center;">RENDIMIENTO</th>
        <th style="text-align: center;">STOCK</th>
        <th style="text-align: center;">UNIDAD DE MEDIDA</th>
        <th style="text-align: center;">PRECIO</th>
        <th style="text-align: center;">ESTADO</th>
        <th style="text-align: center;">ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $QRY = consultaAlmacenP("WHERE AlmP_cat_id = 1", "ORDER BY AlmP_codigo ASC");
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
          list($tonerCodigo, $noParteToner, $rendimientoToner, $compatibilidadToner) = explode(" | ", $row['AlmP_descripcion']);
          $arrayColor = [
            0 => "Monocromatico",
            1 => "Negro",
            2 => "Magenta",
            3 => "Cyan",
            4 => "Amarillo",
          ];
      ?>
          <tr>
            <td>
              <center>
                <?= $row['AlmP_codigo']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $noParteToner; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $tonerCodigo; ?>
              </center>
            </td>
            <td>
              <?= (array_key_exists($row['AlmP_subcat_id'], $arrayColor)) ? $arrayColor[$row['AlmP_subcat_id']] : "Desconocido"; ?>
            </td>
            <td><?= $row['AlmProv_nombre']; ?></td>
            <td>
              <center>
                <?= $compatibilidadToner; ?>
              </center>
            </td>
            <td>
              <center><?= $rendimientoToner; ?></center>
            </td>
            <td>
              <div class="row">
                <div class="col">
                  M: <?= $row['AlmP_stock_min'] ?>
                </div>
                <div class="col">
                  R: <?= $row['AlmP_stock']; ?>
                </div>
            </td>
            <td><?= $row['unList_unidad']; ?></td>
            <td><?= $row['AlmP_precio']; ?></td>
            <td>
              <?= ($row['AlmP_estado'] == 1) ? "Activo" : "Inactivo"; ?>
            </td>
            <td>
              <?php
              if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) {
              ?>
                <button class="btn btn-warning btnAction" data-tipo="edit" id="<?= encryption($row['AlmP_id']); ?>"><i class="fas fa-pen"></i></button>
              <?php
              }
              $QRY_movs_exist = consultaData("SELECT * FROM AlmacenD WHERE AlmDP_id = '" . $row['AlmP_id'] . "'");
              $QRY_cots_exist = consultaData("SELECT * FROM cotizadorD WHERE cotD_prod_id = '" . $row['AlmP_id'] . "'");
              if ($QRY_movs_exist['numRows'] == 0 && $QRY_cots_exist['numRows'] == 0) {
              ?>
                <button class="btn btn-danger btnAction" data-tipo="delete" id="<?= encryption($row['AlmP_id']); ?>"><i class="fas fa-trash"></i></button>
              <?php } else { ?>
                <b>En Uso</b>
              <?php } ?>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>