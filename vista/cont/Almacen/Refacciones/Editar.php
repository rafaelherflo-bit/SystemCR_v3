<?php
$SQL = "SELECT * FROM AlmacenP
              INNER JOIN AlmacenProvs ON AlmP_prov_id = AlmProv_id
              INNER JOIN unidadesList ON AlmP_unidadM = unList_id
              INNER JOIN CategoriasR ON AlmP_subcat_id = catR_id
              WHERE AlmP_id = '" . decryption($pagina[3]) . "'";
$QRY = consultaData($SQL);
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];

  $AlmP_prov_id = $row['AlmP_prov_id'];
  $tonersStock = 0;
  list($refaccionCodigo, $refaccionDescripcion) = explode(" | ", $row['AlmP_descripcion']);
  $refaccionCodigo = explode("-", $refaccionCodigo)[1];
} else {
  redirect($redirect);
}
?>
<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
      <input type="hidden" name="editarRegistro_AlmP_Refacciones" value="<?= $pagina[3]; ?>">
      <center>
        <legend><i class="fas fa-spray-can"></i> &nbsp; ACTUALIZAR DATOS DE PRODUCTO NO.: <b><?= $row['AlmP_codigo']; ?></b></legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_estado" class="bmd-label-floating">ESTADO</label>
              <select class="form-select" id="AlmP_estado" name="AlmP_estado">
                <option value="1" <?= ($row['AlmP_estado'] == "1") ? "selected" : ""; ?>>Activo</option>
                <option value="0" <?= ($row['AlmP_estado'] == "0") ? "selected" : ""; ?>>Inactivo</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_subcat_id" class="bmd-label-floating">CATEGORIA</label>
              <select class="form-select" id="AlmP_subcat_id" name="AlmP_subcat_id">
                <option value="<?= encryption($row['AlmP_subcat_id']); ?>"><?= $row['catR_codigo'] . " - " . $row['catR_nombre']; ?></option>
                <?php
                $sql = "SELECT * FROM CategoriasR WHERE catR_id != " . $row['AlmP_subcat_id'] . " ORDER BY catR_nombre ASC";
                $query = consultaData($sql);
                $dataRefs = $query['dataFetch'];
                foreach ($dataRefs as $datRef) { ?>
                  <option value="<?= encryption($datRef['catR_id']); ?>"><?= $datRef['catR_codigo'] . " - " . $datRef['catR_nombre']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="refaccionCodigo" class="bmd-label-floating">CODIGO</label>
              <input type="number" class="form-control" list="refaccionCodigos" id="refaccionCodigo" name="refaccionCodigo" maxlength="50" pattern="^([0-9]{3,5})$" value="<?= $refaccionCodigo; ?>" required>
              <datalist id="refaccionCodigos">
                <?php
                $codigo = 0;
                $QRY = consultaData('SELECT * FROM AlmacenP WHERE AlmP_cat_id = 3 ORDER BY AlmP_codigo ASC');
                foreach ($QRY['dataFetch'] as $AlmP) {
                  $AlmP_desc = explode(" | ", $AlmP['AlmP_descripcion']);
                ?>
                  <option value="<?= explode("-", $AlmP_desc[0])[1]; ?>"><?= $AlmP['AlmP_descripcion']; ?></option>
                <?php
                }
                ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="stockMin" class="bmd-label-floating">STOCK MINIMO</label>
              <input type="number" class="form-control patternVal" id="stockMin" name="stockMin" maxlength="50" value="<?= $row['AlmP_stock_min']; ?>" min="0" pattern="^[0-9]+" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_prov_id" class="bmd-label-floating">PROVEEDOR</label>
              <select class="form-select" id="AlmP_prov_id" name="AlmP_prov_id">
                <option value="<?= encryption($row['AlmProv_id']); ?>" selected><?= $row['AlmProv_nombre']; ?></option>
                <?php
                $sql = "SELECT * FROM AlmacenProvs WHERE AlmProv_estado = 1 AND AlmProv_id != " . $row['AlmProv_id'] . " ORDER BY AlmProv_nombre ASC";
                $query = consultaData($sql);
                $dataProvT = $query['dataFetch'];
                foreach ($dataProvT as $datProv) { ?>
                  <option value="<?= encryption($datProv['AlmProv_id']); ?>"><?= $datProv['AlmProv_nombre']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_precio" class="bmd-label-floating">PRECIO</label>
              <input type="number" class="form-control" id="AlmP_precio" name="AlmP_precio" pattern="^\d+\.\d{2}$" value="<?= $row['AlmP_precio']; ?>" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_unidadM" class="bmd-label-floating">UNIDAD DE MEDIDA</label>
              <select class="form-select" id="AlmP_unidadM" name="AlmP_unidadM">
                <option value="<?= encryption($row['AlmP_unidadM']); ?>" selected><?= $row['unList_uni'] . " - " . $row['unList_unidad']; ?></option>
                <?php
                $QRY_uL = consultaData("SELECT * FROM unidadesList WHERE unList_id != " . $row['AlmP_unidadM']);
                foreach ($QRY_uL['dataFetch'] as $uL) { ?>
                  <option value="<?= encryption($uL['unList_id']); ?>"><?= $uL['unList_uni'] . " - " . $uL['unList_unidad']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="refaccionDescripcion" class="bmd-label-floating">DESCRIPCION</label>
              <textarea type="text" class="form-control" id="refaccionDescripcion" name="refaccionDescripcion" required><?= $refaccionDescripcion; ?></textarea>
            </div>
          </div>
        </div>
        <fieldset>
          <?= endForm("ACTUALIZAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>