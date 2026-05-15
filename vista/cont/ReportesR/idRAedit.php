<?php
$SQL = "SELECT * FROM Reportes
INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
WHERE reporte_id = " . decryption($pagina[2]);
$QRY = consultaData($SQL);
if ($QRY['numRows'] > 1) {
  redirect($redirect);
} else {
  $Data = $QRY['dataFetch'][0];
}
?>


<div class="container-fluid">
  <center>
    <h5><i class="fas fa-play fa-fw"></i> &nbsp; EDITAR REPORTE ACTIVO</h5>
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
      <a href="<?= SERVERURL . $pagina[0]; ?>/Iniciar"><i class="fas fa-play fa-fw"></i> &nbsp; INICIAR</a>
    </li>
    <li>
      <?= customDMY($pagina[0]); ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
    <input type="hidden" name="reporte_activo_update" value="<?= $pagina[2]; ?>">
    <fieldset class="form-neon">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporte_fecha" class="bmd-label-floating">INICIO</label>
              <input type="datetime-local" class="form-control" name="reporte_fecha" value="<?= $Data['reporte_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporte_renta_id" class="bmd-label-floating">RENTA</label>
              <select name="reporte_renta_id" id="reporte_renta_id" class="form-select">
                <option value="<?= encryption($Data['renta_id']); ?>"><?= $Data['contrato_folio'] . "-" . $Data['renta_folio'] . " | " . $Data['cliente_rs'] . "  ( " . $Data['cliente_rfc'] . " ) | " . $Data['renta_depto']; ?></option>
                <?php
                $sql = "SELECT * FROM Rentas
                        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                        WHERE renta_id != " . $Data['renta_id'] . "
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
              <input type="text" class="form-control" name="reporte_wmakes" placeholder="Persona quien realizo el reporte" value="<?= $Data['reporte_wmakes']; ?>">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporte_reporte" class="bmd-label-floating">REPORTE</label>
              <textarea class="form-control" id="reporte_reporte" name="reporte_reporte"><?= $Data['reporte_reporte']; ?></textarea>
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>