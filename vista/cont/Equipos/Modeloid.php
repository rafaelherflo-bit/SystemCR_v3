<?php
$id_modelo = decryption($pagina[2]);
$data = consultaData("SELECT * FROM Modelos WHERE modelo_id = '$id_modelo'");
if ($data['numRows'] == 1) {
  $modelo = $data['dataFetch'][0];
?>
  <div class="container-fluid">
    <div class="container-fluid form-neon">
      <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="POST" data-form="update" autocomplete="off">
        <input type="hidden" name="update_modelo_id" value="<?= $pagina[2]; ?>">
        <fieldset>
          <legend><i class="fas fa-edit"></i> &nbsp; Editar Modelo de Equipo</legend>
          <div class="container-fluid">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="modelo_tipo">Tipo</label>
                  <select class="form-control" name="modelo_tipo" id="modelo_tipo">
                    <option value="Monocromatico" <?= $modelo['modelo_tipo'] == "Monocromatico" ? "selected" : "" ?>>Monocromatico</option>
                    <option value="Multicolor" <?= $modelo['modelo_tipo'] == "Multicolor" ? "selected" : "" ?>>Multicolor</option>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="modelo_linea">Línea</label>
                  <select class="form-control" name="modelo_linea" id="modelo_linea">
                    <option value="ECOSYS" <?= $modelo['modelo_linea'] == "ECOSYS" ? "selected" : "" ?>>ECOSYS</option>
                    <option value="TASKalfa" <?= $modelo['modelo_linea'] == "TASKalfa" ? "selected" : "" ?>>TASKalfa</option>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="modelo_modelo">Modelo</label>
                  <input type="text" class="form-control" name="modelo_modelo" value="<?= $modelo['modelo_modelo']; ?>" maxlength="25">
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="modelo_toner">Toner Principal</label>
                  <input type="text" class="form-control" name="modelo_toner" value="<?= $modelo['modelo_toner']; ?>" maxlength="25">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_DK">Drum Kit (DK)</label>
                  <input type="number" class="form-control" name="modelo_DK" value="<?= $modelo['modelo_DK']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_DV">Developer Unit (DV)</label>
                  <input type="number" class="form-control" name="modelo_DV" value="<?= $modelo['modelo_DV']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_FK">Fuser Kit (FK)</label>
                  <input type="number" class="form-control" name="modelo_FK" value="<?= $modelo['modelo_FK']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_DP">Document Processor (DP)</label>
                  <input type="number" class="form-control" name="modelo_DP" value="<?= $modelo['modelo_DP']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_TR">Transfer Unit/Belt (TR)</label>
                  <input type="number" class="form-control" name="modelo_TR" value="<?= $modelo['modelo_TR']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="modelo_DR">Toner Transfer (DR)</label>
                  <input type="number" class="form-control" name="modelo_DR" value="<?= $modelo['modelo_DR']; ?>" maxlength="50">
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="modelo_resi">Tiene Contenedor Residual</label>
                  <select class="form-control" name="modelo_resi" id="modelo_resi">
                    <option value="1" <?= $modelo['modelo_resi'] == "1" ? "selected" : "" ?>>SI</option>
                    <option value="0" <?= $modelo['modelo_resi'] == "0" ? "selected" : "" ?>>NO</option>
                  </select>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group">
                  <label for="modelo_wifi">Tiene WIFI</label>
                  <select class="form-control" name="modelo_wifi" id="modelo_wifi">
                    <option value="1" <?= $modelo['modelo_wifi'] == "1" ? "selected" : "" ?>>SI</option>
                    <option value="0" <?= $modelo['modelo_wifi'] == "0" ? "selected" : "" ?>>NO</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </fieldset>
        <p class="text-center" style="margin-top: 40px;">
          <button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
        </p>
      </form>
    </div>
  </div>
<?php } else { ?>
  <div class="alert alert-danger text-center" role="alert">
    <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
    <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
    <p class="mb-0">Lo sentimos, no pudimos encontrar los datos del modelo solicitado.</p>
  </div>
<?php } ?>