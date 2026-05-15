<div class="container_fluid">
  <?php
  $QRY1 = consultaData("SELECT * FROM Modelos");
  foreach ($QRY1['dataFetch'] as $row1) {


    // -------------------------------------- Obtener el Folio ------------------------------------- //
    $codigoLibre = FALSE;
    while (!$codigoLibre) {
      $AlmP_codigo = random_int(10000, 99999);
      $check_codigo = consultaData("SELECT AlmP_codigo FROM AlmacenP WHERE AlmP_codigo = '$AlmP_codigo'");
      if ($check_codigo['numRows'] == 0) {
        $codigoLibre = TRUE;
      }
    }
    // -------------------------- ^^^ FIN ^^^ Obtener el Folio ^^^ FIN ^^^ ------------------------- //

    $AlmP_descripcion = $row1['modelo_tipo'] . " | " . $row1['modelo_linea'] . " " . $row1['modelo_modelo'] . " | " . $row1['modelo_toner'];

    if ($row1['modelo_tipo'] == "Monocromatico") {
      $AlmP_subcat_id = 1;
    } else {
      $AlmP_subcat_id = 2;
    }


    $QRY2 = consultaData("SELECT * FROM AlmacenP WHERE AlmP_cat_id = 5 AND AlmP_descripcion = '$AlmP_descripcion' ORDER BY AlmP_codigo ASC");
    if ($QRY2['numRows'] == 0) {
      sentenciaData("INSERT INTO AlmacenP (AlmP_estado, AlmP_codigo, AlmP_descripcion, AlmP_precio, AlmP_unidadM, AlmP_cat_id, AlmP_subcat_id, AlmP_prov_id) VALUES ('1', '$AlmP_codigo', '$AlmP_descripcion', '0.00', '2', '5', '$AlmP_subcat_id', '10')");
    }
  }
  ?>
</div>
<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th style="text-align: center;"># DE PRODUCTO</th>
        <th style="text-align: center;">TIPO</th>
        <th style="text-align: center;">DESCRIPCION</th>
        <th style="text-align: center;">TONER</th>
        <th style="text-align: center;">PRECIO</th>
        <th style="text-align: center;">ACCIONES</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $SQL = "SELECT * FROM AlmacenP
              WHERE AlmP_cat_id = 5";
      $QRY = consultaData($SQL);
      if ($QRY['numRows'] != 0) {
        foreach ($QRY['dataFetch'] as $row) {
          list($AlmP_equipo_tipo, $AlmP_equipo_modelo, $AlmP_equipo_toner) = explode(" | ", $row['AlmP_descripcion']);
      ?>
          <tr>
            <td>
              <center>
                <?= $row['AlmP_codigo']; ?>
              </center>
            </td>
            <td>
              <center>
                <?= strtoupper($AlmP_equipo_tipo); ?>
              </center>
            </td>
            <td>
              <center>
                <?= $AlmP_equipo_modelo; ?>
              </center>
            </td>
            <td>
              <center>
                <?= $AlmP_equipo_toner; ?>
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