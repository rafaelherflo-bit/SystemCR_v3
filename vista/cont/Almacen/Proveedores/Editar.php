<?php
$SQL = "SELECT * FROM AlmacenProvs WHERE AlmProv_id = '" . decryption($GLOBALS['pagina3']) . "'";
$QRY = consultaData($SQL);
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];
} else {
  redirect($GLOBALS['redirect']);
  exit();
}
?>
<div class="container-fluid">
  <fieldset class="form-neon container-fluid" id="AddNewT">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="editarRegistro_AlmProv" value="<?= $GLOBALS['pagina3']; ?>">
      <center>
        <legend><i class="fas fa-parachute-box"></i> &nbsp; EDITAR REGISTRO DE PROVEEDOR</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmProv_estado" class="bmd-label-floating">ESTADO</label>
              <select class="form-select" id="AlmProv_estado" name="AlmProv_estado">
                <option value="1" <?= ($row['AlmProv_estado'] == "1") ? "selected" : ""; ?>>Activo</option>
                <option value="0" <?= ($row['AlmProv_estado'] == "0") ? "selected" : ""; ?>>Inactivo</option>
              </select>
            </div>
          </div>
          <!-- <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmProv_cat" class="bmd-label-floating">CATEGORIA</label>
              <select class="form-select" id="AlmProv_cat" name="AlmProv_cat">
                <option value="1">Toners</option>
                <option value="2">Chips</option>
                <option value="3">Refacciones</option>
                <option value="4">Servicios</option>
              </select>
            </div>
          </div> -->
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmProv_nombre" class="bmd-label-floating">NOMBRE</label>
              <input type="text" class="form-control" list="AlmProvsNombres" id="AlmProv_nombre" name="AlmProv_nombre" maxlength="50" value="<?= $row['AlmProv_nombre']; ?>" required>
              <datalist id="AlmProvsNombres">
                <?php
                $codigo = 0;
                $QRY = consultaData('SELECT * FROM AlmacenProvs ORDER BY AlmProv_nombre ASC');
                foreach ($QRY['dataFetch'] as $AlmProv) {
                ?>
                  <option value="<?= $AlmProv['AlmProv_nombre']; ?>"><?= $AlmProv['AlmProv_nombre']; ?></option>
                <?php
                }
                ?>
              </datalist>
            </div>
          </div>
        </div>
        <fieldset>
          <?php endForm("ACTUALIZAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>