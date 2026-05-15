<?php
$SQL = "SELECT * FROM ReportesF
            INNER JOIN Clientes ON ReportesF.reporteF_cliente_id = Clientes.cliente_id
            INNER JOIN Modelos ON ReportesF.reporteF_equ_modelo_id = Modelos.modelo_id
            WHERE reporteF_id = " . decryption($pagina[2]);
$QRY = consultaData($SQL);
if ($QRY['numRows'] > 1) {
  redirect($redirect);
} else {
  $Data = $QRY['dataFetch'][0];
}
?>
<div class="container-fluid">
  <center>
    <h3><i class="fas fa-pen fa-fw"></i> &nbsp; EDITANDO</h3>
    <h4><b><?= $Data['reporteF_folio'] . " - " . $Data['cliente_rfc'] . " | " . $Data['cliente_rs']; ?></b></h4>
  </center>
</div>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL . $pagina[0]; ?>/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <?php
      filtroCustom("ReportesF"); // Función externa para mostrar el filtro de fecha/periodo 
      ?>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <input type="hidden" name="reporteF_completo_update" value="<?= $pagina[2]; ?>">
    <fieldset class="form-neon">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_estado" class="bmd-label-floating">ESTADO DEL REPORTE</label>
              <select name="reporteF_estado" id="reporteF_estado" class="form-select" data-placeholder="ESTADO DEL REPORTE">
                <option></option>
                <option value="1" <?= ($Data['reporteF_equ_estado'] == 1) ? "selected" : ""; ?>>SOLUCIONADO</option>
                <option value="0" <?= ($Data['reporteF_equ_estado'] == 0) ? "selected" : ""; ?>>NO SOLUCIONADO</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporteF_fecha" class="bmd-label-floating">FECHA DE REPORTE</label>
              <input type="datetime-local" class="form-control" name="reporteF_fecha" value="<?= $Data['reporteF_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporteF_fecha_inicio" class="bmd-label-floating">INICIO</label>
              <input type="datetime-local" class="form-control" name="reporteF_fecha_inicio" value="<?= $Data['reporteF_fecha_inicio']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporteF_fecha_fin" class="bmd-label-floating">FINAL</label>
              <input type="datetime-local" class="form-control" name="reporteF_fecha_fin" value="<?= $Data['reporteF_fecha_fin']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_cliente_id" class="bmd-label-floating">CLIENTE</label>
              <select name="reporteF_cliente_id" id="reporteF_cliente_id" class="form-select" data-placeholder="SELECCIONA UN CLIENTE">
                <option value="<?= encryption($Data['cliente_id']); ?>"><?= $Data['cliente_rfc'] . " | " . $Data['cliente_rs']; ?></option>
                <option value="<?= encryption("1"); ?>">XAXX010101000 | Publico en General</option>
                <?php
                $sql = "SELECT * FROM Clientes WHERE cliente_id != 1 AND cliente_id != " . $Data['cliente_id'] . " ORDER BY cliente_rfc ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['cliente_id']); ?>"><?= $dato['cliente_rfc'] . " | " . $dato['cliente_rs']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="reporteF_wmakes" class="bmd-label-floating">REPORTO</label>
              <input type="text" class="form-control" name="reporteF_wmakes" placeholder="Persona quien realizo el reporte" value="<?= $Data['reporteF_wmakes']; ?>">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_reporte" class="bmd-label-floating">REPORTE</label>
              <textarea class="form-control" id="reporteF_reporte" name="reporteF_reporte"><?= $Data['reporteF_reporte']; ?></textarea>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_resolucion" class="bmd-label-floating">RESOLUCION</label>
              <textarea class="form-control" id="reporteF_resolucion" name="reporteF_resolucion"><?= $Data['reporteF_resolucion']; ?></textarea>
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
        <legend>&nbsp; DATOS DEL EQUIPO</legend>
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_equ_serie" class="bmd-label-floating">NO. SERIE</label>
              <input type="text" class="form-control" name="reporteF_equ_serie" id="reporteF_equ_serie" placeholder="NO. DE SERIE DEL EQUIPO" value="<?= $Data['reporteF_equ_serie']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_equ_modelo_id" class="bmd-label-floating">MODELO</label>
              <select name="reporteF_equ_modelo_id" id="reporteF_equ_modelo_id" class="form-select">
                <option value="<?= encryption($Data['modelo_id']); ?>"><?= $Data['modelo_linea'] . " " . $Data['modelo_modelo']; ?></option>
                <?php
                $sql = "SELECT * FROM Modelos WHERE modelo_id != " . $Data['modelo_id'] . " ORDER BY modelo_modelo ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['modelo_id']); ?>"><?= $dato['modelo_linea'] . " " . $dato['modelo_modelo']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_equ_estado" class="bmd-label-floating">ESTADO</label>
              <select name="reporteF_equ_estado" id="reporteF_equ_estado" class="form-select" data-placeholder="ESTADO DEL EQUIPO">
                <option value="1" <?= ($Data['reporteF_equ_estado'] == 1) ? "selected" : ""; ?>>Reparado</option>
                <option value="0" <?= ($Data['reporteF_equ_estado'] == 0) ? "selected" : ""; ?>>No Reparado</option>
              </select>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <legend>&nbsp; NIVELES INICIALES</legend>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_nivelK_ini" class="bmd-label-floating">NIVEL COLOR NEGRO</label>
              <input type="number" class="form-control" id="reporteF_nivelK_ini" name="reporteF_nivelK_ini" maxlength="50" value="<?= $Data['reporteF_nivelK_ini']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelM_ini" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelM_ini" class="bmd-label-floating">NIVEL COLOR MAGENTA</label>
              <input type="number" class="form-control" id="reporteF_nivelM_ini" name="reporteF_nivelM_ini" maxlength="50" value="<?= $Data['reporteF_nivelM_ini']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelC_ini" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelC_ini" class="bmd-label-floating">NIVEL COLOR CYAN</label>
              <input type="number" class="form-control" id="reporteF_nivelC_ini" name="reporteF_nivelC_ini" maxlength="50" value="<?= $Data['reporteF_nivelC_ini']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelY_ini" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelY_ini" class="bmd-label-floating">NIVEL COLOR AMARILLO</label>
              <input type="number" class="form-control" id="reporteF_nivelY_ini" name="reporteF_nivelY_ini" maxlength="50" value="<?= $Data['reporteF_nivelY_ini']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelR_ini" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelR_ini" class="bmd-label-floating">NIVEL RESIDUAL</label>
              <input type="number" class="form-control" id="reporteF_nivelR_ini" name="reporteF_nivelR_ini" maxlength="50" value="<?= $Data['reporteF_nivelR_ini']; ?>">
            </div>
          </div>
        </div>
        <div class="row">
          <legend>&nbsp; NIVELES FINALES</legend>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_nivelK_fin" class="bmd-label-floating">NIVEL COLOR NEGRO</label>
              <input type="number" class="form-control" id="reporteF_nivelK_fin" name="reporteF_nivelK_fin" maxlength="50" value="<?= $Data['reporteF_nivelK_fin']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelM_fin" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelM_fin" class="bmd-label-floating">NIVEL COLOR MAGENTA</label>
              <input type="number" class="form-control" id="reporteF_nivelM_fin" name="reporteF_nivelM_fin" maxlength="50" value="<?= $Data['reporteF_nivelM_fin']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelC_fin" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelC_fin" class="bmd-label-floating">NIVEL COLOR CYAN</label>
              <input type="number" class="form-control" id="reporteF_nivelC_fin" name="reporteF_nivelC_fin" maxlength="50" value="<?= $Data['reporteF_nivelC_fin']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelY_fin" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelY_fin" class="bmd-label-floating">NIVEL COLOR AMARILLO</label>
              <input type="number" class="form-control" id="reporteF_nivelY_fin" name="reporteF_nivelY_fin" maxlength="50" value="<?= $Data['reporteF_nivelY_fin']; ?>">
            </div>
          </div>
          <div class="col" id="DIV_reporteF_nivelR_fin" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_nivelR_fin" class="bmd-label-floating">NIVEL RESIDUAL</label>
              <input type="number" class="form-control" id="reporteF_nivelR_fin" name="reporteF_nivelR_fin" maxlength="50" value="<?= $Data['reporteF_nivelR_fin']; ?>">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <legend>&nbsp; CONTADORES INICIALES</legend>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_esc_ini" class="bmd-label-floating">ESCANEO</label>
              <input type="number" class="form-control" id="reporteF_esc_ini" name="reporteF_esc_ini" maxlength="50" value="<?= $Data['reporteF_esc_ini']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_bn_ini" class="bmd-label-floating">BLANCO Y NEGRO</label>
              <input type="number" class="form-control" id="reporteF_bn_ini" name="reporteF_bn_ini" maxlength="50" value="<?= $Data['reporteF_bn_ini']; ?>">
            </div>
          </div>
          <div class="col-12 col-md" id="DIV_reporteF_col_ini" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_col_ini" class="bmd-label-floating">COLOR</label>
              <input type="number" class="form-control" id="reporteF_col_ini" name="reporteF_col_ini" maxlength="50" value="<?= $Data['reporteF_col_ini']; ?>">
            </div>
          </div>
        </div>
        <div class="row">
          <legend>&nbsp; CONTADORES FINALES</legend>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_esc_fin" class="bmd-label-floating">ESCANEO</label>
              <input type="number" class="form-control" id="reporteF_esc_fin" name="reporteF_esc_fin" maxlength="50" value="<?= $Data['reporteF_esc_fin']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="reporteF_bn_fin" class="bmd-label-floating">BLANCO Y NEGRO</label>
              <input type="number" class="form-control" id="reporteF_bn_fin" name="reporteF_bn_fin" maxlength="50" value="<?= $Data['reporteF_bn_fin']; ?>">
            </div>
          </div>
          <div class="col-12 col-md" id="DIV_reporteF_col_fin" <?= ($Data['modelo_tipo'] == "Monocromatico") ? 'style="display: none;"' : ''; ?>>
            <div class="form-group">
              <label for="reporteF_col_fin" class="bmd-label-floating">COLOR</label>
              <input type="number" class="form-control" id="reporteF_col_fin" name="reporteF_col_fin" maxlength="50" value="<?= $Data['reporteF_col_fin']; ?>">
            </div>
          </div>
        </div>
        <hr>
        <?php if (!file_exists(SERVERDIR . "DocsCR/" . $pagina[0] . "/" . explode("-", $Data['reporteF_fecha'])[0] . "/" . explode("-", $Data['reporteF_fecha'])[1] . "/" . $Data['reporteF_folio'] . ".pdf")) { ?>
          <div class="row">
            <div class="col-12 col-md">
              <div class="form-group">
                <label class="bmd-label-floating">EVIDENCIA</label>
                <input type="file" class="form-control" name="reporteF_archivo" accept="application/pdf">
              </div>
            </div>
          </div>
        <?php } else { ?>
          <div class="row">
            <div class="col-12 col-md">
              <?php if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) { ?>
                <button class="btn btn-danger" id="delRepFPDF" data-id="<?= $pagina[2]; ?>" data-archivo="<?= $Data['reporteF_folio']; ?>" data-fecha="<?= $Data['reporteF_fecha']; ?>">BORRAR</button>
              <?php } ?>
              <embed src="<?= SERVERURL . "DocsCR/" . $pagina[0] . "/" . explode("-", $Data['reporteF_fecha'])[0] . "/" . explode("-", $Data['reporteF_fecha'])[1] . "/" . $Data['reporteF_folio'] . ".pdf"; ?>" height="450px" width="100%">
            </div>
          </div>
        <?php } ?>
    </fieldset>
</div>
<?php include SERVERDIR . "vista/inc/endForm.php"; ?>
</form>
</div>