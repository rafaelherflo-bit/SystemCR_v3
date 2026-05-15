<?php
$SQL = "SELECT * FROM AlmacenP
              INNER JOIN AlmacenProvs ON AlmP_prov_id = AlmProv_id
              INNER JOIN unidadesList ON AlmP_unidadM = unList_id
              WHERE AlmP_id = '" . decryption($pagina[3]) . "'";
$QRY = consultaData($SQL);
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];
}
?>
<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
      <input type="hidden" name="editarRegistro_AlmP_Servicios" value="<?= $pagina[3]; ?>">
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
              <label for="AlmP_precio" class="bmd-label-floating">PRECIO</label>
              <input type="number" class="form-control" id="AlmP_precio" name="AlmP_precio" pattern="^\d+\.\d{2}$" value="<?= $row['AlmP_precio']; ?>" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="AlmP_descripcion" class="bmd-label-floating">DESCRIPCION</label>
              <textarea type="text" class="form-control" id="AlmP_descripcion" name="AlmP_descripcion" required><?= $row['AlmP_descripcion']; ?></textarea>
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