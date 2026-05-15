<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="nuevoRegistro_AlmP" value="<?= encryption("Refacciones"); ?>">
      <center>
        <legend><i class="fas fa-spray-can"></i> &nbsp; AGREGAR NUEVO REGISTRO</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_subcat_id" class="bmd-label-floating">CATEGORIA</label>
              <select class="form-select" id="AlmP_subcat_id" name="AlmP_subcat_id">
                <?php
                $sql = "SELECT * FROM CategoriasR ORDER BY catR_nombre ASC";
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
              <input type="number" class="form-control" list="refaccionCodigos" id="refaccionCodigo" name="refaccionCodigo" maxlength="50" pattern="^([0-9]{3,5})$" required>
              <datalist id="refaccionCodigos">
                <?php
                $codigo = 0;
                $QRY = consultaData('SELECT * FROM AlmacenP WHERE AlmP_cat_id = 3 ORDER BY AlmP_codigo ASC');
                foreach ($QRY['dataFetch'] as $AlmP) {
                  $AlmP_desc = explode(" | ", $AlmP['AlmP_descripcion']);
                  $codigoProd = explode("-", $AlmP_desc[0]);
                  if (strlen($codigoProd[1]) > 4) {
                    $codigoProd = substr($codigoProd[1], 0, -1);
                  } else {
                    $codigoProd = $codigoProd[1];
                  }
                  $codigoComp = $AlmP_desc[3];
                  if ($codigoProd != $codigo) {
                ?>
                    <option value="<?= $codigoProd; ?>"><?= $codigoComp; ?></option>
                <?php
                  }
                }
                ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="stockMin" class="bmd-label-floating">STOCK MINIMO</label>
              <input type="number" class="form-control" id="stockMin" name="stockMin" maxlength="50" value="0" min="0" pattern="^[0-9]+" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_prov_id" class="bmd-label-floating">PROVEEDOR</label>
              <select class="form-select" id="AlmP_prov_id" name="AlmP_prov_id">
                <?php
                $sql = "SELECT * FROM AlmacenProvs WHERE AlmProv_estado = 1 ORDER BY AlmProv_nombre ASC";
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
              <input type="number" class="form-control" id="AlmP_precio" name="AlmP_precio" pattern="^\d+\.\d{2}$" value="0.00" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_unidadM" class="bmd-label-floating">UNIDAD DE MEDIDA</label>
              <select class="form-select" id="AlmP_unidadM" name="AlmP_unidadM">
                <?php
                $QRY_uL = consultaData("SELECT * FROM unidadesList");
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
              <textarea type="text" class="form-control" id="refaccionDescripcion" name="refaccionDescripcion" required></textarea>
            </div>
          </div>
        </div>
        <fieldset>
          <?= endForm("GUARDAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>