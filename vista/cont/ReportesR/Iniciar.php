  <div class="container-fluid">
    <center>
      <h3><i class="fas fa-play fa-fw"></i> &nbsp; INICIAR</h3>
    </center>
  </div>

  <div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
      <li>
        <a href="<?= SERVERURL . $pagina[0]; ?>/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
      </li>
      <li>
        <a href="<?= SERVERURL . $pagina[0]; ?>/Activos"><i class="fas fa-hourglass fa-fw"></i> &nbsp; ACTIVOS</a>
      </li>
      <li>
        <?= customDMY($pagina[0]); ?>
      </li>
    </ul>
  </div>

  <div class="container-fluid">
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="startRep" autocomplete="off">
      <input type="hidden" name="reporte_estado" value="0">
      <fieldset class="form-neon">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 col-md-2">
              <div class="form-group">
                <label for="reporte_fecha" class="bmd-label-floating">INICIO</label>
                <input type="datetime-local" class="form-control" name="reporte_fecha" value="<?= date("Y-m-d\TH:i"); ?>">
              </div>
            </div>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="reporte_renta_id" class="bmd-label-floating">RENTA</label>
                <select name="reporte_renta_id" id="reporte_renta_id" class="form-select" data-placeholder="Selecciona una Renta">
                  <option></option>
                  <?php
                  $sql = "SELECT * FROM Rentas
                                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                    ORDER BY contrato_folio ASC";
                  $query = consultaData($sql);
                  $dataTon = $query['dataFetch'];
                  foreach ($dataTon as $dato) { ?>
                    <option value="<?= encryption($dato['renta_id']); ?>"><?= $dato['contrato_folio'] . "-" . $dato['renta_folio'] . " | " . $dato['cliente_rs'] . "  ( " . $dato['cliente_rfc'] . " ) | " . $dato['renta_depto']; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-2">
              <div class="form-group">
                <label for="reporte_wmakes" class="bmd-label-floating">REPORTO</label>
                <input type="text" class="form-control" name="reporte_wmakes" placeholder="Persona quien realizo el reporte">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="reporte_reporte" class="bmd-label-floating">REPORTE</label>
                <textarea class="form-control" id="reporte_reporte" name="reporte_reporte"></textarea>
              </div>
            </div>
          </div>
        </div>
      </fieldset>
      <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
    </form>
  </div>