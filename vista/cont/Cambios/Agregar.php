<?php ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active" class=""><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Cambios/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; TODAS</a>
    </li>
    <li>
      <?php filtroCustom($pagina[0]); ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <input type="hidden" name="cambioAdd" value="1">
    <fieldset>
      <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-2">
            <div class="form-group">
              <input type="date" class="form-control" id="cambio_fecha" name="cambio_fecha" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <select class="form-select" id="cambio_motivo" name="cambio_motivo" data-placeholder="Motivo del Cambio">
                <option></option>
                <option value="Por Reparacion">Por Reparacion</option>
                <option value="Fallos Constantes">Fallos Constantes</option>
                <option value="Peticion del Cliente">Peticion del Cliente</option>
                <option value="Decicion Interna">Decicion Interna</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="form-group">
              <select name="cambio_renta_id" id="cambio_renta_id" class="form-select" data-placeholder="Selecciona una Renta">
                <option></option>
                <?php
                $sql = "SELECT * FROM Rentas
                        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                        WHERE renta_estado = 'Activo' ORDER BY contrato_folio ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['renta_id']); ?>"><?= $dato['contrato_folio'] . "-" . $dato['renta_folio'] . " | " . $dato['cliente_rs'] . " | " . $dato['renta_depto'] . " | " . $dato['equipo_serie']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="form-group">
              <select class="form-select" id="equipo_id" name="cambio_equipoIng_id" data-placeholder="Selecciona un Equipo">
                <option></option>
                <?php
                $sql = "SELECT * FROM Equipos
                        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                        WHERE equipo_estado = 'Espera'
                        ORDER BY equipo_estado ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['equipo_id']); ?>"><?= $dato['modelo_linea'] . " " . $dato['modelo_modelo'] . " (" . $dato['equipo_codigo'] . " | " . $dato['equipo_serie'] . ") - " . $dato['equipo_estado']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <select class="form-select" id="equipo_estado" name="equipo_estado" data-placeholder="Estado del equipo retirado">
                <option></option>
                <option value="Espera">Sin Problemas</option>
                <option value="Reparacion">Con Problemas</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="container-fluid form-neon">
        <div class="row">
        <legend><i class=""></i> &nbsp; Contadores del equipo ingresado</legend>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ing_esc" class="bmd-label-floating">CONTADOR ESCANEO</label>
              <input type="number" class="form-control" id="cambio_Ing_esc" name="cambio_Ing_esc">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ing_bn" class="bmd-label-floating">CONTADOR B&N</label>
              <input type="number" class="form-control" id="cambio_Ing_bn" name="cambio_Ing_bn">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ing_col" class="bmd-label-floating">CONTADOR COLOR</label>
              <input type="number" class="form-control" id="cambio_Ing_col" name="cambio_Ing_col">
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="container-fluid form-neon">
        <div class="row">
        <legend><i class=""></i> &nbsp; Contadores del equipo retirado</legend>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ret_esc" class="bmd-label-floating">CONTADOR ESCANEO</label>
              <input type="number" class="form-control" id="cambio_Ret_esc" name="cambio_Ret_esc">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ret_bn" class="bmd-label-floating">CONTADOR B&N</label>
              <input type="number" class="form-control" id="cambio_Ret_bn" name="cambio_Ret_bn">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ret_col" class="bmd-label-floating">CONTADOR COLOR</label>
              <input type="number" class="form-control" id="cambio_Ret_col" name="cambio_Ret_col">
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="cambio_comm" class="bmd-label-floating">COMENTARIOS</label>
              <textarea class="form-control" id="cambio_comm" name="cambio_comm"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <input type="file" class="form-control" id="cambio_file" name="cambio_file">
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>