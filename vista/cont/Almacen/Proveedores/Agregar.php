
<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="nuevoRegistro_AlmProv" value="<?= encryption("nuevoRegistro_AlmProv"); ?>">
      <center>
        <legend><i class="fas fa-parachute-box"></i> &nbsp; AGREGAR NUEVO REGISTRO</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmProv_cat" class="bmd-label-floating">CATEGORIA</label>
              <select class="form-select" id="AlmProv_cat" name="AlmProv_cat">
                <option value="1">Toners</option>
                <option value="2">Chips</option>
                <option value="3">Refacciones</option>
                <option value="4">Servicios</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmProv_nombre" class="bmd-label-floating">NOMBRE</label>
              <input type="text" class="form-control" list="AlmProvsNombres" id="AlmProv_nombre" name="AlmProv_nombre" maxlength="50" required>
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
          <?php endForm("GUARDAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>