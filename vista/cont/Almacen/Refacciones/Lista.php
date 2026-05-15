<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th style="text-align: center;"># DE PRODUCTO</th>
        <th style="text-align: center;">CATEGORIA</th>
        <th style="text-align: center;">CODIGO</th>
        <th style="text-align: center;">COMPATIBILIDAD</th>
        <th style="text-align: center;">STOCK</th>
        <th style="text-align: center;">UNIDAD DE MEDIDA</th>
        <th style="text-align: center;">PRECIO</th>
        <th style="text-align: center;">PROVEEDOR</th>
        <th style="text-align: center;">ESTADO</th>
        <th style="text-align: center;">ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $SQL = "SELECT * FROM AlmacenP
              INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
              INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
              INNER JOIN CategoriasR ON AlmacenP.AlmP_subcat_id = CategoriasR.catR_id
              WHERE AlmP_cat_id = 3
              ORDER BY AlmP_codigo ASC";
      $QRY = consultaData($SQL);
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
          list($refaccionCodigo, $compatibilidadRefaccion) = explode(" | ", $row['AlmP_descripcion']);

          $stock = 0;

          // Sumando las entradas al STOCK
          $AlmCheckStock_SQL = "SELECT * FROM AlmacenD
                      INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                      WHERE AlmM_tipo = 0
                      AND AlmM_estado = 1
                      AND AlmDP_id = " . $row['AlmP_id'];
          $AlmCheckStock_QRY = consultaData($AlmCheckStock_SQL);
          foreach ($AlmCheckStock_QRY['dataFetch'] as $AlmCS) {
            $stock = $stock + $AlmCS['AlmD_cantidad'];
          }

          // Restando las salidas al STOCK
          $AlmCheckStock_SQL = "SELECT * FROM AlmacenD
                      INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                      WHERE AlmM_tipo != 0
                      AND AlmM_estado = 1
                      AND AlmDP_id = " . $row['AlmP_id'];
          $AlmCheckStock_QRY = consultaData($AlmCheckStock_SQL);
          foreach ($AlmCheckStock_QRY['dataFetch'] as $AlmCS) {
            $stock = $stock - $AlmCS['AlmD_cantidad'];
          }
      ?>
          <tr>
            <td>
              <center>
                <?= $row['AlmP_codigo']; ?>
              </center>
            </td>
            <td>
              <?= $row['catR_nombre']; ?>
            </td>
            <td>
              <center>
                <?= $refaccionCodigo; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $compatibilidadRefaccion; ?>
              </center>
            </td>
            <td>
              <div class="row">
                <div class="col">
                  M: <?= $row['AlmP_stock_min'] ?>
                </div>
                <div class="col">
                  R: <?= $stock; ?>
                </div>
            </td>
            <td><?= $row['unList_unidad']; ?></td>
            <td><?= $row['AlmP_precio']; ?></td>
            <td><?= $row['AlmProv_nombre']; ?></td>
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