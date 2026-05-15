<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th style="text-align: center;"># DE PRODUCTO</th>
        <th style="text-align: center;">DESCRIPCION</th>
        <th style="text-align: center;">PRECIO</th>
        <th style="text-align: center;">ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $SQL = "SELECT * FROM AlmacenP
              WHERE AlmP_cat_id = 4
              ORDER BY AlmP_codigo ASC";
      $QRY = consultaData($SQL);
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
      ?>
          <tr>
            <td>
              <center>
                <?= $row['AlmP_codigo']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $row['AlmP_descripcion']; ?>
              </center>
            </td>
            <td>
              <?= $row['AlmP_precio']; ?>
            </td>
            <td>
              <?php
              if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) {
              ?>
                <button class="btn btn-warning btnAction" data-tipo="edit" id="<?= encryption($row['AlmP_id']); ?>"><i class="fas fa-pen"></i></button>
              <?php
              }
              $QRY_cots_exist = consultaData("SELECT * FROM cotizadorD WHERE cotD_prod_id = '" . $row['AlmP_id'] . "'");
              if ($QRY_cots_exist['numRows'] == 0) {
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