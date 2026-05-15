<?php ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active" class=""><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR CANCELACION</a>
    </li>
    <li>
      <a href="<?php echo SERVERURL; ?>Retiros/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE CANCELACIONES</a>
    </li>
    <li>
      <?php filtroCustom("Retiros"); ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <input type="hidden" name="retiro_motivo_add" value="Cancelacion">
    <fieldset>
      <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-3">
            <div class="form-group">
              <input type="date" class="form-control" name="retiro_fecha_add" id="retiro_fecha_add" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <select class="form-select" id="retiro_renta_id" name="retiro_renta_id_add" data-placeholder="Selecciona una Renta">
                <option></option>
                <?php
                $SQLrentas = "SELECT * FROM Rentas
                                                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                                                INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                                                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                                                WHERE renta_estado = 'Activo'
                                                ORDER BY contrato_folio ASC";
                $query = consultaData($SQLrentas);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?php echo $dato['renta_id']; ?>"><?php echo $dato['contrato_folio'] . "-" . $dato['renta_folio'] . " | " . $dato['cliente_rs'] . " | " . $dato['renta_depto'] . " | (" . $dato['equipo_codigo'] . " - " . $dato['equipo_serie'] . ") - " . $dato['modelo_linea'] . " " . $dato['modelo_modelo']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col">
            <div class="form-group">
              <select class="form-select" id="equipo_estado" name="equipo_estado_add" data-placeholder="Estado del equipo retirado">
                <option></option>
                <option value="Espera">Sin Problemas</option>
                <option value="Reparacion">Con Problemas</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="retiro_esc_add" class="bmd-label-floating">CONTADOR ESCANEO</label>
              <input type="number" class="form-control" name="retiro_esc_add" id="retiro_esc_add">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="retiro_bn_add" class="bmd-label-floating">CONTADOR B&N</label>
              <input type="number" class="form-control" name="retiro_bn_add" id="retiro_bn_add">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="retiro_col_add" class="bmd-label-floating">CONTADOR COLOR</label>
              <input type="number" class="form-control" name="retiro_col_add" id="retiro_col_add">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="retiro_comm_add" class="bmd-label-floating">COMENTARIOS</label>
              <textarea class="form-control" name="retiro_comm_add" id="retiro_comm_add"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <input type="file" class="form-control" name="retiro_file_add" id="retiro_file_add">
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>