<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th style="text-align: center;">NOMBRE</th>
        <th style="text-align: center;">ESTADO</th>
        <?php if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) { ?>
          <th style="text-align: center;">ACCIONES</th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $SQL = "SELECT * FROM  AlmacenProvs ORDER BY AlmProv_nombre ASC";
      $QRY = consultaData($SQL);
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
      ?>
          <tr>
            <td><?= $row['AlmProv_nombre']; ?></td>
            <td>
              <?php
              if ($row['AlmProv_estado'] == 1) {
                echo "Activo";
              } else {
                echo "Inactivo";
              }
              ?>
            </td>
            <?php if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) { ?>
              <td>
                <button class="btn btn-warning btnAction" data-tipo="edit" data-cont="<?= $GLOBALS['pagina1']; ?>" id="<?= encryption($row['AlmProv_id']); ?>"><i class="fas fa-pen"></i></button>
                <button class="btn btn-danger btnAction" data-tipo="delete" data-cont="<?= $GLOBALS['pagina1']; ?>" id="<?= encryption($row['AlmProv_id']); ?>"><i class="fas fa-trash"></i></button>
              </td>
            <?php } ?>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>