<?php
$QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = '" . decryption($pagina[3]) . "'");
if ($QRY['numRows'] == 1) {
  $row = $QRY['dataFetch'][0];
} else {
  redirect(SERVERURL . 'Almacen/Movimientos/CustomMonth/' . date('Y') . '/' . ucfirst(dateFormat(date('d-n-Y'), "mesL")));
}

?>

<div class="container-fluid">
  <fieldset class="form-neon container-fluid">
    <center>
      <legend><i class="fas fa-dolly"></i> &nbsp; DETALLES DE REGISTRO DE <b><?= ($row['AlmM_tipo'] == "0") ? "ENTRADA" : (($row['AlmM_tipo'] == "1") ? "SALIDA INTERNA" : (($row['AlmM_tipo'] == "2") ? "SALIDA DE RENTA" : (($row['AlmM_tipo'] == "3") ? "SALIDA DE VENTA" : "DESCONOCIDO"))); ?></b> CON NO. FOLIO <b><?= $row['AlmM_folio']; ?></b>
        <?php if ($_SESSION["id"] == 1 || $_SESSION["id"] == 2) { ?>
          <spaw class="btn btn-danger btnAction" data-tipo="delete" data-delete="delAlmM" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-trash"></i></spaw>
        <?php } ?>
        <?php if ($row['AlmM_estado'] == 1 && ($_SESSION["id"] == 1 || $_SESSION["id"] == 2)) { ?>
          <spaw class="btn btn-secondary btnAction" data-tipo="noinit" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-exclamation-triangle"></i></spaw>
        <?php } ?>
        <?php if (file_exists(SERVERDIR . 'DocsCR/ALMACEN/Evidencias/' . $row['AlmM_folio'] . '.pdf')) { ?>
          <spaw class="btn btn-primary btnAction" data-tipo="filePDF" data-action="watch" data-folio="<?= $row['AlmM_folio']; ?>" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-file-pdf"></i></spaw>
        <?php } ?>
        <?php if ($row['AlmM_estado'] == 0) { ?>
          <spaw class="btn btn-success" id="btn-active_AlmM"><i class="fas fa-check"></i></spaw>
          <?php if (consultaData("SELECT * FROM AlmacenD WHERE AlmDM_id = '" . decryption($pagina[3]) . "'")['numRows'] > 0 && $row['AlmM_tipo'] != 0) { ?>
            <spaw class="btn btn-info" data-id="<?= encryption($row['AlmM_id']); ?>" id="btnRequi"><i class="fas fa-print"></i></spaw>
          <?php } ?>
          <spaw class="btn btn-warning btnAction" data-tipo="edit" id="<?= encryption($row['AlmM_id']); ?>"><i class="fas fa-pen"></i></spaw>
        <?php } ?>
      </legend>
    </center>
    <div class="container-fluid" style="text-align: center;">
      <div class="row">
        <div class="col-12 col-md">
          <label for="AlmM_fecha" class="bmd-label-floating">FECHA DE MOVIMIENTO</label>
          <p><b><?= strtoupper(dateFormat($row['AlmM_fecha'], "full")); ?></b></p>
        </div>
        <div class="col-12 col-md">
          <label for="AlmM_fecha" class="bmd-label-floating">REGISTRO REALIZADO POR</label>
          <?php
          $DATA_uS = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $row['AlmM_uS_id'])['dataFetch'][0];
          ?>
          <p><b><?= $DATA_uS['usuario_nombre'] . " " . $DATA_uS['usuario_apellido']; ?></b></p>
        </div>
        <?php
        if ($row['AlmM_tipo'] == 0) {

          $uS_SQL = "SELECT usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_identificador'];
          $uSQRY = consultaData($uS_SQL)['dataFetch'][0];
          echo '
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">VERIFICADO POR EMPLEADO</label>
                  <p><b>' . $uSQRY['usuario_nombre'] . " " . $uSQRY['usuario_apellido'] . '</b></p>
                </div>
              ';
        } else if ($row['AlmM_tipo'] == 1) {

          $uS_SQL = "SELECT usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_identificador'];
          $uSQRY = consultaData($uS_SQL)['dataFetch'][0];
          echo '
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">ENTRAGADO A EMPLEADO</label>
                  <p><b>' . $uSQRY['usuario_nombre'] . " " . $uSQRY['usuario_apellido'] . '</b></p>
                </div>
              ';
        } else if ($row['AlmM_tipo'] == 2) {

          $uS_SQL = "SELECT usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'];
          $uSQRY = consultaData($uS_SQL)['dataFetch'][0];
          $rent_SQL = "SELECT * FROM Rentas
                          INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                          INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                          WHERE renta_id = " . $row['AlmM_identificador'];
          $rentQRY = consultaData($rent_SQL)['dataFetch'][0];
          echo '
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">ENTRAGADO A EMPLEADO</label>
                  <p><b>' . $uSQRY['usuario_nombre'] . " " . $uSQRY['usuario_apellido'] . '</b></p>
                </div>
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">RENTA</label>
                  <p><b>' . $rentQRY['cliente_rfc'] . " <b>|</b> " . $rentQRY['cliente_rs'] . "<br>" . $rentQRY['contrato_folio'] . "-" . $rentQRY['renta_folio'] . " | " . $rentQRY['renta_depto'] . '</b></p>
                </div>
              ';
        } else if ($row['AlmM_tipo'] == 3) {

          $uSQRY = consultaData("SELECT usuario_nombre, usuario_apellido FROM Usuarios WHERE usuario_id = " . $row['AlmM_empleado'])['dataFetch'][0];
          $clieQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = '" . $row['AlmM_identificador'] . "'")['dataFetch'][0];
          echo '
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">ENTRAGADO A EMPLEADO</label>
                  <p><b>' . $uSQRY['usuario_nombre'] . " " . $uSQRY['usuario_apellido'] . '</b></p>
                </div>
                <div class="col-12 col-md">
                  <label for="AlmM_fecha" class="bmd-label-floating">CLIENTE</label>
                  <p><b>' . $clieQRY['cliente_rfc'] . " <b>|</b> " . $clieQRY['cliente_rs'] . '</b></p>
                </div>
              ';
        }
        ?>
        <div class="col-12 col-md">
          <label for="AlmM_fecha" class="bmd-label-floating">COMENTARIO</label>
          <p><b><?= $row['AlmM_comentario']; ?></b></p>
        </div>
      </div>
    </div>
  </fieldset>
</div>
<br>
<?php
// Seccion Para Agregar Productos
if ($row['AlmM_estado'] == 0) {
?>
  <div class="container-fluid">
    <fieldset class="form-neon container-fluid">
      <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="AlmDMadd" autocomplete="off">
        <input type="hidden" name="agregar_AlmDM" value="<?= $pagina[3]; ?>">
        <center>
          <legend><i class="fas fa-box"></i> &nbsp; AGREGANDO PRODUCTO</legend>
        </center>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmDP_id" class="bmd-label-floating">PRODUCTO</label>
                <select class="form-select" id="AlmDP_id" name="AlmDP_id" data-placeholder="Selecciona un Producto de <?= ($row['AlmM_tipo'] == "0") ? "Entrada" : (($row['AlmM_tipo'] == "1") ? "Salida Interna" : (($row['AlmM_tipo'] == "2") ? "Salida de Renta" : (($row['AlmM_tipo'] == "3") ? "Salida de Venta" : "DESCONOCIDO"))); ?>">
                  <option></option>
                  <optgroup label="TONERS">
                    <?php
                    foreach (consultaAlmacenP("WHERE AlmP_estado = 1 AND AlmP_cat_id = 1", "ORDER BY AlmP_cat_id ASC")['dataFetch'] as $AlmP) {
                    ?>
                      <option value="<?= encryption($AlmP['AlmP_id']); ?>"><?= $AlmP['AlmP_codigo'] . " | " . $AlmP['AlmP_descripcion'] . " | " . $AlmP['unList_unidad'] . " | " . $AlmP['AlmProv_nombre'] . " | Stock: " . $AlmP['AlmP_stock']; ?></option>
                    <?php
                    }
                    ?>
                  </optgroup>
                  <?php
                  // if ($row['AlmM_tipo'] != 2) {
                  ?>
                  <optgroup label="CHIPS">
                    <?php
                    foreach (consultaAlmacenP("WHERE AlmP_estado = 1 AND AlmP_cat_id = 2", "ORDER BY AlmP_cat_id ASC")['dataFetch'] as $AlmP) {
                    ?>
                      <option value="<?= encryption($AlmP['AlmP_id']); ?>"><?= $AlmP['AlmP_codigo'] . " | " . $AlmP['AlmP_descripcion'] . " | " . $AlmP['unList_unidad'] . " | " . $AlmP['AlmProv_nombre'] . " | Stock: " . $AlmP['AlmP_stock']; ?></option>
                    <?php
                    }
                    ?>
                  </optgroup>
                  <optgroup label="REFACCIONES">
                    <?php
                    foreach (consultaAlmacenP("WHERE AlmP_estado = 1 AND AlmP_cat_id = 3", "ORDER BY AlmP_cat_id ASC")['dataFetch'] as $AlmP) {
                    ?>
                      <option value="<?= encryption($AlmP['AlmP_id']); ?>"><?= $AlmP['AlmP_codigo'] . " | " . $AlmP['AlmP_descripcion'] . " | " . $AlmP['unList_unidad'] . " | " . $AlmP['AlmProv_nombre'] . " | Stock: " . $AlmP['AlmP_stock']; ?></option>
                    <?php
                    }
                    ?>
                  </optgroup>
                  <?php
                  // }
                  $stockOtros = consultaAlmacenP("WHERE AlmP_estado = 1 AND AlmP_cat_id = 6", "ORDER BY AlmP_cat_id ASC");
                  if ($stockOtros['numRows'] > 0) {
                  ?>
                    <optgroup label="OTROS">
                      <?php
                      foreach ($stockOtros['dataFetch'] as $AlmP) {
                      ?>
                        <option value="<?= encryption($AlmP['AlmP_id']); ?>"><?= $AlmP['AlmP_codigo'] . " | " . $AlmP['AlmP_descripcion'] . " | " . $AlmP['unList_unidad'] . " | " . $AlmP['AlmProv_nombre'] . " | Stock: " . $AlmP['AlmP_stock']; ?></option>
                      <?php
                      }
                      ?>
                    </optgroup>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md">
              <div class="form-group">
                <label for="AlmD_cantidad" class="bmd-label-floating">CANTIDAD</label>
                <input type="number" class="form-control" id="AlmD_cantidad" name="AlmD_cantidad" pattern="[0-9]+" value="0" required>
              </div>
            </div>
            <?php if ($row['AlmM_tipo'] == 3) { ?>
              <div class="col-12 col-md">
                <div class="form-group">
                  <label for="AlmD_precio" class="bmd-label-floating">PRECIO</label>
                  <input type="number" class="form-control" id="AlmD_precio" name="AlmD_precio" pattern="^\d+\.\d{2}$" value="0.00" required>
                </div>
              </div>
            <?php } ?>
          </div>
          <div class="row">
            <div class="col-12 col-md-12">
              <div class="form-group">
                <label for="AlmD_comentario" class="bmd-label-floating">COMENTARIO</label>
                <textarea type="text" class="form-control" id="AlmD_comentario" name="AlmD_comentario" required><?= $row['AlmM_comentario'] ?></textarea>
              </div>
            </div>
          </div>
          <fieldset>
            <?= endForm("AGREGAR"); ?>
          </fieldset>
        </div>
      </form>
    </fieldset>
  </div>
  <br>
<?php
}
// Seccion de Tabla de Productos Agregados
$SQL = "SELECT * FROM AlmacenD
                  INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
                  INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
                  INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
                  WHERE AlmDM_id = '" . decryption($pagina[3]) . "'
                  ORDER BY AlmP_descripcion ASC";
$QRY = consultaData($SQL);
if ($QRY['numRows'] != 0) {
?>
  <div class="container-fluid">
    <fieldset class="form-neon container-fluid">
      <center>
        <legend><i class="fas fa-boxes"></i> &nbsp; PRODUCTOS AGREGADOS</legend>
      </center>
      <div class="container-fluid table-responsive">
        <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
          <thead class="table-dark">
            <tr>
              <th style="text-align: center;"># DE PRODUCTO</th>
              <th style="text-align: center;">CANTIDAD</th>
              <?= ($row['AlmM_tipo'] == 3) ? '<th style="text-align: center;">PRECIO</th>' : ''; ?>
              <th style="text-align: center;">CATEGORIA</th>
              <th style="text-align: center;">PROVEEDOR</th>
              <th style="text-align: center;">DESCRIPCION DE PRODUCTO</th>
              <th style="text-align: center;">COMENTARIO</th>
              <th style="text-align: center;"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($QRY['dataFetch'] as $rowDet) {
            ?>
              <tr>
                <td>
                  <center>
                    <?= $rowDet['AlmP_codigo']; ?>
                  </center>
                </td>
                <td>
                  <center>
                    <?= $rowDet['AlmD_cantidad']; ?>
                  </center>
                </td>
                <?php if ($row['AlmM_tipo'] == 3) { ?>
                  <td><?= $rowDet['AlmD_precio']; ?></td>
                <?php } ?>
                <td>
                  <?php
                  $arrayCategoria = [
                    1 => "Toners",
                    2 => "Chips",
                    3 => "Refacciones",
                    4 => "Servicios"
                  ];
                  echo (array_key_exists($rowDet['AlmP_cat_id'], $arrayCategoria)) ? $arrayCategoria[$rowDet['AlmP_cat_id']] : "Desconocido";
                  ?>
                </td>
                <td><?= $rowDet['AlmProv_nombre']; ?></td>
                <td>
                  <center>
                    <?php
                    if ($rowDet['AlmP_cat_id'] == 1) {
                      list($tonerCodigo, $noParteToner, $rendimientoToner, $compatibilidadToner) = explode(" | ", $rowDet['AlmP_descripcion']);
                      $arrayColor = [
                        0 => "Monocromatico",
                        1 => "Negro",
                        2 => "Magenta",
                        3 => "Cyan",
                        4 => "Amarillo",
                      ];
                      $tonerColor = (array_key_exists($rowDet['AlmP_subcat_id'], $arrayColor)) ? $arrayColor[$rowDet['AlmP_subcat_id']] : "Desconocido";
                      echo $tonerCodigo . " <b>|</b> " . $tonerColor . " <b>|</b> " . $compatibilidadToner . " <b>|</b> No. Parte: " . $noParteToner . " <b>|</b> Rendimiento: " . $rendimientoToner . " <b>|</b> UDM: " . $rowDet['unList_unidad'];
                    } else if ($rowDet['AlmP_cat_id'] == 2) {
                      list($chipCodigo, $rendimientoChip, $compatibilidadChip) = explode(" | ", $rowDet['AlmP_descripcion']);
                      $arrayColor = [
                        0 => "Monocromatico",
                        1 => "Negro",
                        2 => "Magenta",
                        3 => "Cyan",
                        4 => "Amarillo",
                      ];
                      $tonerColor = (array_key_exists($rowDet['AlmP_subcat_id'], $arrayColor)) ? $arrayColor[$rowDet['AlmP_subcat_id']] : "Desconocido";
                      echo $chipCodigo . " <b>|</b> " . $tonerColor . " <b>|</b> " . $compatibilidadChip . " <b>|</b> Rendimiento: " . $rendimientoChip . " <b>|</b> UDM: " . $rowDet['unList_unidad'];
                    } else if ($rowDet['AlmP_cat_id'] == 3) {
                      list($codigoRefaccion, $compatibilidadRefaccion) = explode(" | ", $rowDet['AlmP_descripcion']);
                      echo $codigoRefaccion . " <b>|</b> " . $compatibilidadRefaccion . " <b>|</b> UDM: " . $rowDet['unList_unidad'];
                    }
                    ?>
                  </center>
                </td>
                <td><?= $rowDet['AlmD_comentario']; ?></td>
                <td>
                  <?php if (($_SESSION["id"] == 1 || $_SESSION["id"] == 2 || $_SESSION["id"] == 6) && $row['AlmM_estado'] == 0) { ?>
                    <button class="btn btn-danger btnAction" data-tipo="delete" data-delete="delAlmD" id="<?= encryption($rowDet['AlmD_id']); ?>"><i class="fas fa-trash"></i></button>
                  <?php } ?>
                </td>
              </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </fieldset>
  </div>
<?php
}
?>