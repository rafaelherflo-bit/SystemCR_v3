<?php
$QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = '" . decryption($pagina[3]) . "'");
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];
} else {
  redirect($redirect);
}

?>

<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <center>
      <legend><i class="fas fa-people-carry"></i> &nbsp; EDITAR DATOS REGISTRO PRINCIPAL CON NO. FOLIO: <b><?= $row['AlmM_folio']; ?></b>
        <button class="btn btn-info btnAction" data-tipo="details" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-info"></i></button>
        <?php if (file_exists(SERVERDIR . 'DocsCR/ALMACEN/Evidencias/' . $row['AlmM_folio'] . '.pdf')) { ?>
          <button class="btn btn-danger btnAction" data-tipo="filePDF" data-action="delete" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-file-pdf"></i></button>
        <?php } ?>
      </legend>
    </center>
    <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
      <input type="hidden" name="editar_AlmM" value="<?= $pagina[3]; ?>">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmM_fecha" class="bmd-label-floating">FECHA</label>
              <input type="date" class="form-control" name="AlmM_fecha" id="AlmM_fecha" value="<?= $row['AlmM_fecha']; ?>">
            </div>
          </div>
          <div class="col-12 col-md">
            <div class="form-group">
              <label for="AlmM_tipo" class="bmd-label-floating">TIPO DE REGISTRO</label>
              <select class="form-select" id="AlmM_tipo" name="AlmM_tipo">
                <option value="0" <?= ($row['AlmM_tipo'] == "0") ? "selected" : ""; ?>>Entrada</option>
                <option value="1" <?= ($row['AlmM_tipo'] == "1") ? "selected" : ""; ?>>Salida Interna</option>
                <option value="2" <?= ($row['AlmM_tipo'] == "2") ? "selected" : ""; ?>>Salida Renta</option>
                <option value="3" <?= ($row['AlmM_tipo'] == "3") ? "selected" : ""; ?>>Salida Venta</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row" id="AlmM_identificador_DIV">

          <?php if ($row['AlmM_tipo'] == 0) { ?>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                <select class="form-select" id="AlmM_empleado" name="AlmM_empleado">
                  <?php
                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'];
                  $uS_QRY = consultaData($uS_SQL)['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($uS_QRY['usuario_id']); ?>" selected><?= $uS_QRY['usuario_nombre']  . " " . $uS_QRY['usuario_apellido']; ?></option>
                  <?php

                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                  WHERE usuario_id != '" . $row['AlmM_empleado'] . "'
                                  AND usuario_id != 1
                                  AND usuario_estado = 'Activo'
                                  ORDER BY usuario_id ASC";
                  $uS_QRY = consultaData($uS_SQL);
                  foreach ($uS_QRY['dataFetch'] as $uS) {
                  ?>
                    <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " | " . $uS['usuario_apellido']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          <?php } else if ($row['AlmM_tipo'] == 1) { ?>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_empleado" class="bmd-label-floating">Empleado</label>
                <select class="form-select" id="AlmM_empleado" name="AlmM_empleado">
                  <?php
                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'];
                  $uS_QRY = consultaData($uS_SQL)['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($uS_QRY['usuario_id']); ?>" selected><?= $uS_QRY['usuario_nombre']  . " " . $uS_QRY['usuario_apellido']; ?></option>
                  <?php
                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                  WHERE usuario_id != '" . $row['AlmM_empleado'] . "'
                                  AND usuario_id != 1
                                  AND usuario_estado = 'Activo'
                                  ORDER BY usuario_id ASC";
                  $uS_QRY = consultaData($uS_SQL);
                  foreach ($uS_QRY['dataFetch'] as $uS) {
                  ?>
                    <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " | " . $uS['usuario_apellido']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          <?php } else if ($row['AlmM_tipo'] == 2) { ?>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_identificador" class="bmd-label-floating">RENTA</label>
                <select class="form-select" id="AlmM_identificador" name="AlmM_identificador">
                  <?php
                  $rent_SQL = "SELECT * FROM Rentas
                                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                WHERE renta_id = " . $row['AlmM_identificador'];
                  $rentQRY = consultaData($rent_SQL)['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($row['AlmM_identificador']); ?>" selected><?= $rentQRY['contrato_folio'] . "-" . $rentQRY['renta_folio'] . " | " . $rentQRY['renta_depto']  . " | " . $rentQRY['cliente_rfc']  . " | " . $rentQRY['cliente_rs']; ?></option>
                  <?php
                  $rentas_SQL = "SELECT * FROM Rentas
                                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                  WHERE renta_estado = 'Activo'
                                  AND renta_id != '" . $row['AlmM_identificador'] . "'
                                  ORDER BY contrato_folio ASC";
                  $rentas_QRY = consultaData($rentas_SQL);
                  foreach ($rentas_QRY['dataFetch'] as $renta) {
                  ?>
                    <option value="<?= encryption($renta['renta_id']); ?>"><?= $renta['contrato_folio'] . "-" . $renta['renta_folio'] . " | " . $renta['renta_depto']  . " | " . $renta['cliente_rfc']  . " | " . $renta['cliente_rs']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                <select class="form-select" id="AlmM_empleado" name="AlmM_empleado">
                  <?php
                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'];
                  $uS_QRY = consultaData($uS_SQL)['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($uS_QRY['usuario_id']); ?>" selected><?= $uS_QRY['usuario_nombre']  . " " . $uS_QRY['usuario_apellido']; ?></option>
                  <?php

                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                  WHERE usuario_id != '" . $row['AlmM_empleado'] . "'
                                  AND usuario_id != 1
                                  AND usuario_estado = 'Activo'
                                  ORDER BY usuario_id ASC";
                  $uS_QRY = consultaData($uS_SQL);
                  foreach ($uS_QRY['dataFetch'] as $uS) {
                  ?>
                    <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " | " . $uS['usuario_apellido']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          <?php } else if ($row['AlmM_tipo'] == 3) { ?>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_identificador" class="bmd-label-floating">CLIENTE</label>
                <select class="form-select" id="AlmM_identificador" name="AlmM_identificador">
                  <?php
                  $clieQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = '" . $row['AlmM_identificador'] . "'")['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($row['AlmM_identificador']); ?>" selected><?= $clieQRY['cliente_rfc']  . " | " . $clieQRY['cliente_rs']; ?></option>
                  <?php
                  $clientes_QRY = consultaData("SELECT * FROM Clientes WHERE cliente_id != '" . $row['AlmM_identificador'] . "' ORDER BY cliente_rfc ASC");
                  foreach ($clientes_QRY['dataFetch'] as $cliente) {
                  ?>
                    <option value="<?= encryption($cliente['cliente_id']); ?>"><?= $cliente['cliente_rfc']  . " | " . $cliente['cliente_rs']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                <select class="form-select" id="AlmM_empleado" name="AlmM_empleado">
                  <?php
                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'];
                  $uS_QRY = consultaData($uS_SQL)['dataFetch'][0];
                  ?>
                  <option value="<?= encryption($uS_QRY['usuario_id']); ?>" selected><?= $uS_QRY['usuario_nombre']  . " " . $uS_QRY['usuario_apellido']; ?></option>
                  <?php

                  $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                  WHERE usuario_id != '" . $row['AlmM_empleado'] . "'
                                  AND usuario_id != 1
                                  AND usuario_estado = 'Activo'
                                  ORDER BY usuario_id ASC";
                  $uS_QRY = consultaData($uS_SQL);
                  foreach ($uS_QRY['dataFetch'] as $uS) {
                  ?>
                    <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " | " . $uS['usuario_apellido']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmM_IVA" id="AlmM_IVA_label" class="bmd-label-floating">IVA AL 16%</label>
                <input class="form-control" id="AlmM_IVA" name="AlmM_IVA" type="range" value="16" min="0" max="100" pattern="[0-9]{0,100}" title="Ingrese un número entre 0 y 100">
              </div>
            </div>
          <?php } ?>

        </div>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="AlmM_comentario" class="bmd-label-floating">COMENTARIOS</label>
              <textarea type="text" class="form-control" id="AlmM_comentario" name="AlmM_comentario" required><?= $row['AlmM_comentario']; ?></textarea>
            </div>
          </div>
        </div>
        <fieldset>
          <?= endForm("ACTUALIZAR"); ?>
        </fieldset>
      </div>
    </form>
  </fieldset>
</div>