<?php ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <?php filtroCustom("Lecturas"); ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <input type="hidden" name="agregarLectura" value="0">
    <fieldset>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="lectura_fecha_add" class="bmd-label-floating">FECHA</label>
              <input type="date" class="form-control" name="lectura_fecha" id="lectura_fecha_add" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col-12 col-md-6" id="renta_lectura"></div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label for="lectura_esc" class="bmd-label-floating">ESCANEO</label>
              <input type="number" class="form-control" id="lectura_esc" name="lectura_esc" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label for="lectura_bn" class="bmd-label-floating">B&N</label>
              <input type="number" class="form-control" id="lectura_bn" name="lectura_bn" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col-12" id="col_lectura_col">
            <div class="form-group">
              <label for="lectura_col" class="bmd-label-floating">COLOR</label>
              <input type="number" class="form-control" id="lectura_col" name="lectura_col" maxlength="50" pattern="\d+">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="comments" class="bmd-label-floating">COMENTARIOS</label>
              <textarea class="form-control" id="comments" name="comments" maxlength="250"></textarea>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <legend id="renta_equipo">&nbsp; CONSUMIBLES</legend>
          <div class="col" id="col_renta_stock_K">
            <div class="form-group">
              <label for="renta_stock_K" class="bmd-label-floating">STOCK NEGRO</label>
              <input type="number" class="form-control" id="renta_stock_K" name="renta_stock_K" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_renta_stock_M">
            <div class="form-group">
              <label for="renta_stock_M" class="bmd-label-floating">STOCK MAGENTA</label>
              <input type="number" class="form-control" id="renta_stock_M" name="renta_stock_M" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_renta_stock_C">
            <div class="form-group">
              <label for="renta_stock_C" class="bmd-label-floating">STOCK CYAN</label>
              <input type="number" class="form-control" id="renta_stock_C" name="renta_stock_C" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_renta_stock_Y">
            <div class="form-group">
              <label for="renta_stock_Y" class="bmd-label-floating">STOCK AMARILLO</label>
              <input type="number" class="form-control" id="renta_stock_Y" name="renta_stock_Y" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_renta_stock_R">
            <div class="form-group">
              <label for="renta_stock_R" class="bmd-label-floating">STOCK RESIDUAL</label>
              <input type="number" class="form-control" id="renta_stock_R" name="renta_stock_R" maxlength="50" pattern="\d+">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col" id="col_equipo_nivel_K">
            <div class="form-group">
              <label for="equipo_nivel_K" class="bmd-label-floating">NIVEL NEGRO</label>
              <input type="number" class="form-control" id="equipo_nivel_K" name="equipo_nivel_K" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_M">
            <div class="form-group">
              <label for="equipo_nivel_M" class="bmd-label-floating">NIVEL MAGENTA</label>
              <input type="number" class="form-control" id="equipo_nivel_M" name="equipo_nivel_M" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_C">
            <div class="form-group">
              <label for="equipo_nivel_C" class="bmd-label-floating">NIVEL CYAN</label>
              <input type="number" class="form-control" id="equipo_nivel_C" name="equipo_nivel_C" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_Y">
            <div class="form-group">
              <label for="equipo_nivel_Y" class="bmd-label-floating">NIVEL AMARILLO</label>
              <input type="number" class="form-control" id="equipo_nivel_Y" name="equipo_nivel_Y" maxlength="50" pattern="\d+">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_R">
            <div class="form-group">
              <label for="equipo_nivel_R" class="bmd-label-floating">NIVEL RESIDUAL</label>
              <input type="number" class="form-control" id="equipo_nivel_R" name="equipo_nivel_R" maxlength="50" pattern="\d+">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <input type="checkbox" class="btn-check" id="lectura_formato" autocomplete="off">
              <label class="btn btn-outline-primary" for="lectura_formato">FORMATO DE LECTURA</label>
              <div id="div_lectura_formato">
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
              <input type="checkbox" class="btn-check" id="lectura_estado" autocomplete="off">
              <label class="btn btn-outline-primary" for="lectura_estado">PAGINA DE ESTADO</label>
            </div>
            <div class="form-group">
              <div id="div_lectura_estado">
              </div>
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <!-- <p class="text-center">Para poder guardar los cambios, debes ser usuario autorizado.</p> -->
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group">
              <!-- <label for="usuario_admin" class="bmd-label-floating">Nombre de usuario</label> -->
              <input type="hidden" class="form-control" name="usuario_admin" id="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="form-group">
              <!-- <label for="clave_admin" class="bmd-label-floating">Contraseña</label> -->
              <input type="hidden" class="form-control" name="clave_admin" id="clave_admin" value="<?= $_SESSION['passclave']; ?>">
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <p class="text-center" style="margin-top: 40px;">
      <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; GUARDAR</button>
      &nbsp; &nbsp;
      <button id="resetBtn" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
    </p>
  </form>
</div>