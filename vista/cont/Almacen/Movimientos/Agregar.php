<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="start" autocomplete="off">
      <input type="hidden" name="iniciar_AlmM" value="<?= encryption("iniciar_AlmM"); ?>">
      <center>
        <legend><i class="fas fa-parachute-box"></i> &nbsp; INICIANDO REGISTRO</legend>
      </center>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmM_fecha" class="bmd-label-floating">FECHA</label>
              <input type="date" class="form-control" name="AlmM_fecha" id="AlmM_fecha" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmM_tipo" class="bmd-label-floating">TIPO DE REGISTRO</label>
              <select class="form-select" id="AlmM_tipo" name="AlmM_tipo" data-placeholder="Selecciona un tipo de movimiento">
                <option></option>
                <option value="0">Entrada</option>
                <option value="1">Salida Interna</option>
                <option value="2">Salida Renta</option>
                <option value="3">Salida Venta</option>
              </select>
            </div>
          </div>
          <div class="row" id="AlmM_identificador_DIV">
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="AlmM_comentario" class="bmd-label-floating">COMENTARIOS</label>
              <textarea type="text" class="form-control" id="AlmM_comentario" name="AlmM_comentario" required></textarea>
            </div>
          </div>
        </div>
        <fieldset>
          <?= endForm("INICIAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>