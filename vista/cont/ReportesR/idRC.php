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
    <h5><i class="fas fa-check fa-fw"></i> &nbsp; COMPLETAR REPORTE</h5>
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
  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="finish" autocomplete="off">
    <input type="hidden" name="reporte_activo_completar" value="<?= $pagina[2]; ?>">
    <fieldset class="form-neon">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporte_fecha" class="bmd-label-floating">FECHA DE REPORTE</label>
              <input type="datetime-local" class="form-control" name="reporte_fecha" value="<?= $Data['reporte_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporte_fecha_inicio" class="bmd-label-floating">INICIO</label>
              <input type="datetime-local" class="form-control" name="reporte_fecha_inicio" id="reporte_fecha_inicio" value="<?= $Data['reporte_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporte_fecha_fin" class="bmd-label-floating">FINAL</label>
              <input type="datetime-local" class="form-control" name="reporte_fecha_fin" id="reporte_fecha_fin" value="<?= $Data['reporte_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporte_fecha_fin" class="bmd-label-floating">RENTA</label>
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
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporte_resolucion" class="bmd-label-floating">RESOLUCION</label>
              <textarea class="form-control" id="reporte_resolucion" name="reporte_resolucion"></textarea>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="comments" class="bmd-label-floating">COMENTARIOS</label>
              <textarea class="form-control" id="comments" name="comments" maxlength="250"></textarea>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <legend id="renta_equipo">&nbsp; CONSUMIBLES</legend>
          <div class="col">
            <div class="form-group">
              <label for="renta_stock_K" class="bmd-label-floating">STOCK NEGRO</label>
              <input type="number" class="form-control" id="renta_stock_K" name="renta_stock_K" maxlength="50" value="<?= $Data['renta_stock_K']; ?>">
            </div>
          </div>
          <?php if ($Data['modelo_tipo'] == "Multicolor") { ?>
            <div class="col">
              <div class="form-group">
                <label for="renta_stock_M" class="bmd-label-floating">STOCK MAGENTA</label>
                <input type="number" class="form-control" id="renta_stock_M" name="renta_stock_M" maxlength="50" value="<?= $Data['renta_stock_M']; ?>">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="renta_stock_C" class="bmd-label-floating">STOCK CYAN</label>
                <input type="number" class="form-control" id="renta_stock_C" name="renta_stock_C" maxlength="50" value="<?= $Data['renta_stock_C']; ?>">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label for="renta_stock_Y" class="bmd-label-floating">STOCK AMARILLO</label>
                <input type="number" class="form-control" id="renta_stock_Y" name="renta_stock_Y" maxlength="50" value="<?= $Data['renta_stock_Y']; ?>">
              </div>
            </div>
          <?php } ?>
          <?php if ($Data['modelo_resi'] == "1") { ?>
            <div class="col">
              <div class="form-group">
                <label for="renta_stock_R" class="bmd-label-floating">STOCK RESIDUAL</label>
                <input type="number" class="form-control" id="renta_stock_R" name="renta_stock_R" maxlength="50" value="<?= $Data['renta_stock_R']; ?>">
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="equipo_nivel_K" class="bmd-label-floating">NIVEL NEGRO</label>
              <input type="number" class="form-control" id="equipo_nivel_K" name="equipo_nivel_K" maxlength="50" value="<?= $Data['equipo_nivel_K']; ?>">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_M" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="equipo_nivel_M" class="bmd-label-floating">NIVEL MAGENTA</label>
              <input type="number" class="form-control" id="equipo_nivel_M" name="equipo_nivel_M" maxlength="50" value="<?= $Data['equipo_nivel_M']; ?>">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_C" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="equipo_nivel_C" class="bmd-label-floating">NIVEL CYAN</label>
              <input type="number" class="form-control" id="equipo_nivel_C" name="equipo_nivel_C" maxlength="50" value="<?= $Data['equipo_nivel_C']; ?>">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_Y" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="equipo_nivel_Y" class="bmd-label-floating">NIVEL AMARILLO</label>
              <input type="number" class="form-control" id="equipo_nivel_Y" name="equipo_nivel_Y" maxlength="50" value="<?= $Data['equipo_nivel_Y']; ?>">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_R" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="equipo_nivel_R" class="bmd-label-floating">NIVEL RESIDUAL</label>
              <input type="number" class="form-control" id="equipo_nivel_R" name="equipo_nivel_R" maxlength="50" value="<?= $Data['equipo_nivel_R']; ?>">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label class="bmd-label-floating">EVIDENCIA</label>
              <input type="file" class="form-control" name="reporte_archivo" accept="application/pdf">
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>