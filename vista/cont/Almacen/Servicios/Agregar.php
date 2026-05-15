
<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
      <input type="hidden" name="nuevoRegistro_AlmP" value="<?= encryption("Servicios"); ?>">
      <center>
        <legend><i class="fas fa-spray-can"></i> &nbsp; AGREGAR NUEVO REGISTRO</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmP_precio" class="bmd-label-floating">PRECIO</label>
              <input type="number" class="form-control" id="AlmP_precio" name="AlmP_precio" pattern="^\d+\.\d{2}$" value="0.00" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="AlmP_descripcion" class="bmd-label-floating">DESCRIPCION</label>
              <textarea type="text" class="form-control" id="AlmP_descripcion" name="AlmP_descripcion" required></textarea>
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