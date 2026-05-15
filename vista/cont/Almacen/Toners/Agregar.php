<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="nuevoRegistro_AlmP" value="<?= encryption("Toners"); ?>">
      <center>
        <legend><i class="fas fa-spray-can"></i> &nbsp; AGREGAR NUEVO REGISTRO</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="tonerCodigo1" class="bmd-label-floating">MARCA DE TONER</label>
              <select class="form-select" id="tonerCodigo1" name="tonerCodigo1">
                <option value="TK">Toner Kyocera</option>
                <option value="TKR">Toner Kyocera Rellenado</option>
                <option value="ES">Toner OKI</option>
                <option value="ESR">Toner OKI Rellenado</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="tonerCodigo2" class="bmd-label-floating">CODIGO</label>
              <input type="number" class="form-control" list="tonerCodigos" id="tonerCodigo2" name="tonerCodigo2" maxlength="50" pattern="^([0-9]{3,5})$" value="0" required>
              <datalist id="tonerCodigos">
                <?php
                $codigo = 0;
                $QRY = consultaData('SELECT * FROM AlmacenP WHERE AlmP_cat_id = 1 ORDER BY AlmP_codigo ASC');
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
              <label for="colorToner" class="bmd-label-floating">COLOR DE TONER</label>
              <select class="form-select" id="colorToner" name="colorToner">
                <option value="0">Monocromatico</option>
                <option value="1">Color Negro</option>
                <option value="2">Color Magenta</option>
                <option value="3">Color Cyan</option>
                <option value="4">Color Amarillo</option>
              </select>
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
              <label for="noParteToner" class="bmd-label-floating">NO. DE PARTE</label>
              <input type="number" class="form-control" id="noParteToner" name="noParteToner" maxlength="50" value="0" pattern="\d+" required>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="rendimientoToner" class="bmd-label-floating">RENDIMENTO</label>
              <input type="number" class="form-control" id="rendimientoToner" name="rendimientoToner" value="0" pattern="\d+" required>
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
              <label for="compatibilidadToner" class="bmd-label-floating">COMPATIBILIDAD</label>
              <textarea type="text" class="form-control" id="compatibilidadToner" name="compatibilidadToner" required></textarea>
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