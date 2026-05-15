<?php
$SQL = "SELECT * FROM TonersRegistrosS
        INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
        WHERE tonerRO_id = " . decryption($pagina[2]);
$QRY = consultaData($SQL);
if ($QRY['numRows'] > 1) {
  redirect($redirect);
} else {
  $Data = $QRY['dataFetch'][0];

  $tonerETAct = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $Data['tonerRO_toner_id'])['dataFetch'][0]['tonerET'];
  $tonerSTAct = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $Data['tonerRO_toner_id'])['dataFetch'][0]['tonerST'];
  $tonersStockAct = $tonerETAct - $tonerSTAct;
  if ($Data['toner_tipo'] == 0) {
    $toner_tipoAct = "MONOCROMATICO";
  } elseif ($Data['toner_tipo'] == 1) {
    $toner_tipoAct = "NEGRO";
  } elseif ($Data['toner_tipo'] == 2) {
    $toner_tipoAct = "MAGENTA";
  } elseif ($Data['toner_tipo'] == 3) {
    $toner_tipoAct = "CYAN";
  } elseif ($Data['toner_tipo'] == 4) {
    $toner_tipoAct = "AMARILLO";
  }
}
?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entradas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE ENTRADAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Entrada"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ENTRADA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; EXISTENCIAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salida"><i class="fas fa-sign-out-alt"></i> &nbsp; AGREGAR SALIDA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Toners/Salidas"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; REGISTROS DE SALIDAS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
    <input type="hidden" id="tonerRO_id" name="actualizarRST" value="<?= $pagina[2]; ?>">
    <center>
      <h4><i class="fas fa-pen"></i> &nbsp; EDITAR REGISTRO DE SALIDA (<b><?= $Data['tonerRO_folio']; ?></b>) &nbsp; <i class="fas fa-pen"></i></h4>
    </center>
    <fieldset class="form-neon container-fluid">
      <div class="row">
        <div class="col">
          <label for="tonerRO_fecha" class="bmd-label-floating">FECHA</label>
          <input class="form-control" type="date" id="tonerRO_fecha" name="tonerRO_fecha_edit" value="<?= $Data['tonerRO_fecha']; ?>">
        </div>
        <div class="col">
          <div class="form-group">
            <label for="tonerRO_toner_id" class="bmd-label-floating">CODIGO DE TONER</label>
            <select class="form-select" id="tonerRO_toner_id" name="tonerRO_toner_id_edit">
              <option value="<?= encryption($Data['toner_id']); ?>"><?= $Data['toner_parte'] . ' | ' . $Data['toner_codigo'] . ' | ' . $toner_tipoAct . ' | STOCK: ' . $tonersStockAct . ' | ' . $Data['toner_comp']; ?></option>
              <?php
              $sql = 'SELECT * FROM Toners
                        INNER JOIN ProveedoresT ON ProveedoresT.provT_id = Toners.toner_provT_id
                        WHERE toner_estado = "Activo"
                        ORDER BY toner_codigo ASC';
              $query = consultaData($sql);
              $dataTon = $query['dataFetch'];
              foreach ($dataTon as $dato) {
                $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . $dato['toner_id'])['dataFetch'][0]['tonerET'];
                $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $dato['toner_id'])['dataFetch'][0]['tonerST'];
                $tonersStock = $tonerET - $tonerST;
                if ($dato['toner_tipo'] == 0) {
                  $toner_tipo = "MONOCROMATICO";
                } elseif ($dato['toner_tipo'] == 1) {
                  $toner_tipo = "NEGRO";
                } elseif ($dato['toner_tipo'] == 2) {
                  $toner_tipo = "MAGENTA";
                } elseif ($dato['toner_tipo'] == 3) {
                  $toner_tipo = "CYAN";
                } elseif ($dato['toner_tipo'] == 4) {
                  $toner_tipo = "AMARILLO";
                }
                if ($tonersStock > 0) {
                  echo '<option value="' . encryption($dato['toner_id']) . '">' . $dato['toner_parte'] . ' | ' . $dato['toner_codigo'] . ' | ' . $toner_tipo . ' | STOCK: ' . $tonersStock . ' | ' . $dato['toner_comp'] . '</option>';
                }
              } ?>
            </select>
          </div>
        </div>
        <div class="col">
          <label for="tonerRO_cantidad" class="bmd-label-floating">CANTIDAD</label>
          <input class="form-control" type="number" id="tonerRO_cantidad" name="tonerRO_cantidad" placeholder="CANTIDAD" value="<?= $Data['tonerRO_cantidad']; ?>">
        </div>
        <div class="col">
          <label for="tonerRO_comm" class="bmd-label-floating">COMENTARIO</label>
          <input class="form-control" type="text" id="tonerRO_comm" name="tonerRO_comm" placeholder="COMENTARIO" value="<?= $Data['tonerRO_comm']; ?>">
        </div>
      </div>
      <input type="hidden" id="requestTipo" value="<?= $Data['tonerRO_tipo']; ?>">
      <input type="hidden" id="requestEmp" value="<?= $Data['tonerRO_empleado']; ?>">
      <div class="row">
        <div class="col-12 col-md-2">
          <div class="form-group">
            <div class="custom-control custom-radio">
              <input type="radio" id="tonerRO_tipo1" name="tonerRO_tipo" value="Venta" class="custom-control-input tonerRO_edit" <?= ($Data['tonerRO_tipo'] == "Venta") ? "checked" : ""; ?>>
              <label class="custom-control-label" for="tonerRO_tipo1">Venta</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="tonerRO_tipo2" name="tonerRO_tipo" value="Renta" class="custom-control-input tonerRO_edit" <?= ($Data['tonerRO_tipo'] == "Renta") ? "checked" : ""; ?>>
              <label class="custom-control-label" for="tonerRO_tipo2">Renta</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="tonerRO_tipo3" name="tonerRO_tipo" value="Interno" class="custom-control-input tonerRO_edit" <?= ($Data['tonerRO_tipo'] == "Interno") ? "checked" : ""; ?>>
              <label class="custom-control-label" for="tonerRO_tipo3">Interno</label>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-10" id="divContTipoEdit">
        </div>
      </div>
    </fieldset>
    <fieldset class="form-neon container-fluid">
      <center>
        <legend><i class="fas fa-file-pdf"></i> &nbsp; PDF &nbsp; <i class="fas fa-file-pdf"></i></legend>
      </center>
      <div>
        <div class="row">
          <div class="col-12 col-md">
            <?php
            $DIR = "DocsCR/ALMACEN/";
            $TipoReg = ($Data['tonerRO_tipo'] == "Interno") ? "INTERNOS" : (($Data['tonerRO_tipo'] == "Venta") ? "VENTAS" : (($Data['tonerRO_tipo'] == "Renta") ? "RENTAS" : ""));
            list($anioRST, $mesRST, $diaRST) = explode("-", $Data['tonerRO_fecha']);
            $DIR = $DIR . $TipoReg . "/" . $anioRST . "/" . $mesRST . "/" . $Data['tonerRO_folio'] . ".pdf";

            $url = SERVERURL . $DIR;
            $dir = SERVERDIR . $DIR;

            if (file_exists($dir)) { ?>
              <?php if ($_SESSION['id'] == 1 || $_SESSION['id'] == 2) { ?>
                <button class="btn btn-danger" id="delEviPDF" data-tipo="<?= $TipoReg; ?>" data-folio="<?= $Data['tonerRO_folio']; ?>" data-fecha="<?= $Data['tonerRO_fecha']; ?>">BORRAR</button>
              <?php } ?>
              <embed src="<?= $url; ?>" height="450px" width="100%">
            <?php } else { ?>
              <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="evidencia_PDF" autocomplete="off">
                <label class="btn btn-outline-primary" for="evidencia_PDF">EVIDENCIA</label>
              </div>
              <div class="form-group">
                <div id="div_evidencia_PDF">
                </div>
              </div>
            <?php } ?>
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
      <button type="submit" class="btn btn-raised btn-info btn-sm"><i class="far fa-save"></i> &nbsp; ACTUALIZAR</button>
      &nbsp; &nbsp;
      <button id="resetBtn" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
    </p>
  </form>
</div>