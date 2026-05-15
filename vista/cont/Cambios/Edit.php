<?php
$SQL = "SELECT * FROM Cambios
        INNER JOIN Rentas ON Cambios.cambio_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        WHERE cambio_id = '" . decryption($contenido[1]) . "'";
$query = consultaData($SQL);

if ($query['numRows'] == 0) {
  redirect(SERVERURL . "Cambios/Lista");
} else {
  $row = $query['dataFetch'][0];
}
?>
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
  <div class="row">
    <div class="col">
      <center>
        <h2><?= $row['contrato_folio'] . "-" . $row['renta_folio'] . " | " . $row['renta_depto'] . " | " . $row['cliente_rs'] . " ( " . $row['cliente_rfc'] . " )"; ?></h2>
      </center>
    </div>
  </div>
</div>
<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?php echo SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
    <input type="hidden" name="cambioEdit" value="<?= $contenido[1]; ?>">
    <fieldset>
      <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="cambio_fecha" class="bmd-label-floating">FECHA</label>
              <input type="date" class="form-control" id="cambio_fecha" name="cambio_fecha" value="<?= $row['cambio_fecha']; ?>">
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
              <input type="number" class="form-control" id="cambio_Ing_esc" name="cambio_Ing_esc" value="<?= $row['cambio_Ing_esc']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ing_bn" class="bmd-label-floating">CONTADOR B&N</label>
              <input type="number" class="form-control" id="cambio_Ing_bn" name="cambio_Ing_bn" value="<?= $row['cambio_Ing_bn']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ing_col" class="bmd-label-floating">CONTADOR COLOR</label>
              <input type="number" class="form-control" id="cambio_Ing_col" name="cambio_Ing_col" value="<?= $row['cambio_Ing_col']; ?>">
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
              <input type="number" class="form-control" id="cambio_Ret_esc" name="cambio_Ret_esc" value="<?= $row['cambio_Ret_esc']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ret_bn" class="bmd-label-floating">CONTADOR B&N</label>
              <input type="number" class="form-control" id="cambio_Ret_bn" name="cambio_Ret_bn" value="<?= $row['cambio_Ret_bn']; ?>">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="cambio_Ret_col" class="bmd-label-floating">CONTADOR COLOR</label>
              <input type="number" class="form-control" id="cambio_Ret_col" name="cambio_Ret_col" value="<?= $row['cambio_Ret_col']; ?>">
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
              <textarea class="form-control" id="cambio_comm" name="cambio_comm"><?= $row['cambio_comm']; ?></textarea>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-12 col-md">
            <?php
            list($cambio_anio, $cambio_mes, $cambio_dia) = explode("-", $row['cambio_fecha']);

            if (!file_exists(SERVERDIR . "DocsCR/CambiosDeEquipos/" . $cambio_anio . "/" . $cambio_mes . "/" . $row['cambio_folio'] . ".pdf")) { ?>
              <div class="form-group">
                <input type="checkbox" class="btn-check" id="cambio_file_box" autocomplete="off">
                <label class="btn btn-outline-primary" for="cambio_file_box">EVIDENCIA</label>
                <div id="div_cambio_file">
                </div>
              </div>
            <?php } else { ?>
              <?php if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) { ?>
                <button class="btn btn-danger" id="delCamPDF" data-id="<?= $pagina[2]; ?>" data-folio="<?= $row['cambio_folio']; ?>" data-fecha="<?= $row['cambio_fecha']; ?>">BORRAR</button>
              <?php } ?>
              <embed src="<?= SERVERURL . "DocsCR/CambiosDeEquipos/" . $cambio_anio . "/" . $cambio_mes . "/" . $row['cambio_folio'] . ".pdf"; ?>" height="450px" width="100%">
            <?php } ?>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>