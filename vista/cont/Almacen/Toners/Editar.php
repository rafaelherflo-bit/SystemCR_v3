<?php
$SQL = "SELECT * FROM AlmacenP
              INNER JOIN AlmacenProvs ON AlmP_prov_id = AlmProv_id
              INNER JOIN unidadesList ON AlmP_unidadM = unList_id
              WHERE AlmP_id = '" . decryption($pagina[3]) . "'";
$QRY = consultaData($SQL);
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];

  $AlmP_prov_id = $row['AlmP_prov_id'];
  $tonersStock = 0;
  list($tonerCodigo, $noParteToner, $rendimientoToner, $compatibilidadToner) = explode(" | ", $row['AlmP_descripcion']);
  list($tonerCodigo1, $tonerCodigo2) = explode("-", $tonerCodigo);
  if ($row['AlmP_subcat_id'] > 0) {
    $tonerCodigo2 = substr($tonerCodigo2, 0, -1);
  }

  $arrayColor = [
    0 => "Monocromatico",
    1 => "Negro",
    2 => "Magenta",
    3 => "Cyan",
    4 => "Amarillo",
  ];
} else {
  redirect($redirect);
}
?>
<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
      <input type="hidden" name="editarRegistro_AlmP_Toners" value="<?= $pagina[3]; ?>">
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
              <label for="tonerCodigo1" class="bmd-label-floating">MARCA DE TONER</label>
              <select class="form-select" id="tonerCodigo1" name="tonerCodigo1">
                <option value="TK" <?= ($tonerCodigo1 == "TK") ? "selected" : ""; ?>>Toner Kyocera</option>
                <option value="TKR" <?= ($tonerCodigo1 == "TKR") ? "selected" : ""; ?>>Toner Kyocera Rellenado</option>
                <option value="ES" <?= ($tonerCodigo1 == "ES") ? "selected" : ""; ?>>Toner OKI</option>
                <option value="ESR" <?= ($tonerCodigo1 == "ESR") ? "selected" : ""; ?>>Toner OKI Rellenado</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="tonerCodigo2" class="bmd-label-floating">CODIGO</label>
              <input type="number" class="form-control patternVal" list="tonerCodigos" id="tonerCodigo2" name="tonerCodigo2" maxlength="50" pattern="^([0-9]{3,5})$" value="<?= $tonerCodigo2; ?>" required>
              <datalist id="tonerCodigos">
                <?php
                $sql = 'SELECT * FROM Toners
                        INNER JOIN ProveedoresT ON ProveedoresT.provT_id = Toners.toner_provT_id
                        WHERE toner_estado = "Activo" ORDER BY toner_codigo ASC';
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $datTon) { ?>
                  <option value="<?= explode("-", $datTon['toner_codigo'])[1]; ?>"><?= $datTon['toner_codigo'] . " | " . $datTon['toner_comp'] . " | " . $datTon['provT_nombre']; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="colorToner" class="bmd-label-floating">COLOR DE TONER</label>
              <select class="form-select" id="colorToner" name="colorToner">
                <option value="0" <?= ($row['AlmP_subcat_id'] == "0") ? "selected" : ""; ?>>Monocromatico</option>
                <option value="1" <?= ($row['AlmP_subcat_id'] == "1") ? "selected" : ""; ?>>Color Negro</option>
                <option value="2" <?= ($row['AlmP_subcat_id'] == "2") ? "selected" : ""; ?>>Color Magenta</option>
                <option value="3" <?= ($row['AlmP_subcat_id'] == "3") ? "selected" : ""; ?>>Color Cyan</option>
                <option value="4" <?= ($row['AlmP_subcat_id'] == "4") ? "selected" : ""; ?>>Color Amarillo</option>
              </select>
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
              <label for="noParteToner" class="bmd-label-floating">NO. DE PARTE</label>
              <input type="number" class="form-control patternVal" id="noParteToner" name="noParteToner" maxlength="50" value="<?= $noParteToner; ?>" pattern="\d+" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="rendimientoToner" class="bmd-label-floating">RENDIMENTO</label>
              <input type="number" class="form-control" id="rendimientoToner" name="rendimientoToner" value="<?= $rendimientoToner; ?>" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_prov_id" class="bmd-label-floating">PROVEEDOR</label>
              <select class="form-select" id="AlmP_prov_id" name="AlmP_prov_id">
                <option value="<?= encryption($row['AlmP_prov_id']); ?>" selected><?= $row['AlmProv_nombre']; ?></option>
                <?php
                $sql = "SELECT * FROM AlmacenProvs WHERE AlmProv_id != " . $row['AlmP_prov_id'] . " ORDER BY AlmProv_nombre ASC";
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
                $QRY_uL = consultaData("SELECT * FROM unidadesList WHERE unList_id != '" . $row['AlmP_unidadM'] . "'");
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
              <label for="compatibilidadToner" class="bmd-label-floating">COMPATIBILIDAD</label>
              <textarea type="text" class="form-control" id="compatibilidadToner" name="compatibilidadToner" required><?= $compatibilidadToner; ?></textarea>
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


</div>