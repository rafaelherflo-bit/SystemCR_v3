<?php
$result = consultaData("SELECT * FROM cotizadorM WHERE cotM_id = '" . decryption($contenido[1]) . "'");
if ($result['numRows'] == 0) {
  redirect(SERVERURL . "Cotizador/Lista");
} else {
  $dataFetch = $result['dataFetch'][0];
?>
  <div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
      <li>
        <a href="<?= SERVERURL; ?>Cotizador/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
      </li>
      <li>
        <a href="<?= SERVERURL; ?>Cotizador/Lista"><i class="fas fa-list"></i> &nbsp; LISTA</a>
      </li>
      <li>
        <a href="<?= SERVERURL; ?>Cotizador/Vencidos"><i class="fas fa-list"></i> &nbsp; VENCIDOS</a>
      </li>
      <li>
        <a href="<?= SERVERURL; ?>Almacen/Toners/Lista"><i class="fas fa-boxes"></i> &nbsp; ALMACEN</a>
      </li>
    </ul>
  </div>

  <div class="container-fluid">
    <!--- INICIO =============== SECCION DE INFORMACION =============== INICIO --->
    <fieldset class="form-neon">
      <legend><i class="fas fa-info-circle"></i> &nbsp; INFORMACION BASICA &nbsp;
        <?php if ($dataFetch['cotM_estatus'] == 1) { ?>
          <span class="btn btn-success" id="btnPDFcotM" data-id="<?= $contenido[1]; ?>"><i class="fas fa-file-pdf"></i></span>
          <span class="btn btn-warning" id="btnEditcotM" data-id="<?= $contenido[1]; ?>"><i class="fas fa-pen"></i></span>
          <span class="btn btn-danger" id="btnDELcotM" data-id="<?= $contenido[1]; ?>"><i class="fas fa-trash"></i></span>
        <?php } else { ?>
          <b>Cotizacion Vencida</b>
        <?php } ?>
      </legend>
      <div class="row justify-content-md-center">
        <div class="col-md-auto">
          <b>FECHA</b>
          <br>
          <?= strtoupper(dateFormat($dataFetch['cotM_fecha'], "completa")); ?>
        </div>
        <div class="col-md-auto">
          <?= "<b>FOLIO</b><br>" . $dataFetch['cotM_folio']; ?>
        </div>
        <div class="col-md-auto">
          <b>IVA</b>
          <br>
          <?= $dataFetch['cotM_IVA']; ?>%
        </div>
        <div class="col-md-auto">
          <b>ESTADO</b>
          <br>
          <?= ($dataFetch['cotM_estatus'] == 0) ? "VENCIDO" : "ACTIVO"; ?>
        </div>
        <div class="col-md-auto">
          <b>RAZON SOCIAL</b>
          <br>
          <?= $dataFetch['cotM_cliRS']; ?>
        </div>
        <div class="col-md-auto">
          <b>RFC</b>
          <br>
          <?= $dataFetch['cotM_cliRFC']; ?>
        </div>
        <div class="col-md-auto">
          <b>COMENTARIO</b>
          <br>
          <?= $dataFetch['cotM_comm']; ?>
        </div>
      </div>
    </fieldset>
    <!---  FIN  ===============  SECCION DE INFORMACION  ===============  FIN  --->


    <?php if ($dataFetch['cotM_estatus'] == 1) { ?>
      <!--- INICIO =============== SECCION DE PRODUCTOS =============== INICIO --->
      <fieldset class="form-neon">
        <legend><i class="fas fa-file-invoice-dollar"></i> &nbsp; AGREGAR NUEVO PRODUCTO &nbsp; </legend>

        <!--- INICIO --- FORMULARIO --- INICIO --->
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="cotDadd" autocomplete="off">
          <!--- INICIO --- Input oculto con ID de registro de CobranzaM --- INICIO --->
          <input type="hidden" class="form-control" name="agregarProdCotD" value="<?= $contenido[1]; ?>">
          <!---  FIN  ---  Input oculto con ID de registro de CobranzaM  ---  FIN  --->

          <!--- INICIO --- Inputs de Formulario --- INICIO --->
          <div class="row justify-content-md-center">
            <div class="col-md-auto">
              <div class="form-group">
                <label for="cotD_prod_id" class="bmd-label-floating">PRODUCTO</label>
                <select class="form-select" name="cotD_prod_id" id="cotD_prod_id" data-placeholder="Selecciona un Producto">
                  <option></option>
                  <?php
                  $cat_id = 1;
                  $cat = TRUE;
                  $AlmP_SQL = "SELECT * FROM AlmacenP
                              INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
                              INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
                              ORDER BY AlmP_cat_id ASC";
                  foreach (consultaData($AlmP_SQL)['dataFetch'] as $AlmP) {
                    if ($cat_id != $AlmP['AlmP_cat_id']) {
                      $cat_id = $AlmP['AlmP_cat_id'];
                      $cat = TRUE;
                    }
                    if ($cat_id == $AlmP['AlmP_cat_id'] && $AlmP['AlmP_cat_id'] == 1) {
                      $categoria = "TONERS";
                      if ($cat) {
                        echo '<optgroup label="' . $categoria . '">';
                      }
                      $cat = FALSE;
                    } else if ($cat_id == $AlmP['AlmP_cat_id'] && $AlmP['AlmP_cat_id'] == 2) {
                      $categoria = "CHIPS";
                      if ($cat) {
                        echo '<optgroup label="' . $categoria . '">';
                      }
                      $cat = FALSE;
                    } else if ($cat_id == $AlmP['AlmP_cat_id'] && $AlmP['AlmP_cat_id'] == 3) {
                      $categoria = "REFACCIONES";
                      if ($cat) {
                        echo '<optgroup label="' . $categoria . '">';
                      }
                      $cat = FALSE;
                    } else if ($cat_id == $AlmP['AlmP_cat_id'] && $AlmP['AlmP_cat_id'] == 4) {
                      $categoria = "SERVICIOS";
                      if ($cat) {
                        echo '<optgroup label="' . $categoria . '">';
                      }
                      $cat = FALSE;
                    } else if ($cat_id == $AlmP['AlmP_cat_id'] && $AlmP['AlmP_cat_id'] == 5) {
                      $categoria = "EQUIPOS";
                      if ($cat) {
                        echo '<optgroup label="' . $categoria . '">';
                      }
                      $cat = FALSE;
                    } else {
                      $categoria = "DESCONOCIDA";
                    }
                  ?>
                    <option value="<?= encryption($AlmP['AlmP_id']); ?>"><?= $AlmP['AlmP_codigo'] . " | " . $AlmP['AlmP_descripcion'] . " | " . $AlmP['unList_unidad'] . " | " . $AlmP['AlmProv_nombre']; ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-auto">
              <div class="form-group">
                <label for="cotD_cantidad" class="bmd-label-floating">CANTIDAD</label>
                <input type="number" class="form-control" id="cotD_cantidad" name="cotD_cantidad" title="Ingresa una cantidad" value="0">
              </div>
            </div>
            <div class="col-md-auto">
              <div class="form-group">
                <label for="cotD_descuento" class="bmd-label-floating">DESCUENTO</label>
                <input type="number" class="form-control" id="cotD_descuento" name="cotD_descuento" title="Ingresa una cantidad de descuento" pattern="^\d+\.\d{2}$" value="0.00">
              </div>
            </div>
            <div class="col-md-auto">
              <div class="form-group">
                <label for="cotD_monto" class="bmd-label-floating">MONTO POR UNIDAD</label>
                <input type="number" class="form-control" id="cotD_monto" name="cotD_monto" step="0.00" title="Monto a cobrar" pattern="^\d+\.\d{2}$" value="0.00">
              </div>
            </div>
          </div>
          <!---  FIN  ---  Inputs de Formulario  ---  FIN  --->

          <!--- INICIO --- Botonera para Agregar nuevo registro --- INICIO --->
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
            <button type="submit" class="btn btn-raised btn-primary btn-sm"><i class="fas fa-plus"></i> &nbsp; AGREGAR</button>
            &nbsp; &nbsp;
            <button id="resetBtn" class="btn btn-raised btn-secondary btn-sm"><i class="fas fa-paint-roller"></i> &nbsp; LIMPIAR</button>
          </p>
          <!---  FIN  ---  Botonera para Agregar nuevo registro  ---  FIN  --->

        </form>
      </fieldset>
      <!---  FIN  ---  FORMULARIO  ---  FIN  --->
    <?php } ?>

    <!--- INICIO --- TABLA DE REGISTROS --- INICIO --->
    <fieldset class="form-neon">
      <legend><i class="fas fa-file-invoice-dollar"></i> &nbsp; PRODUCTOS AGREGADOS &nbsp; </legend>
      <div class="container-fluid table-responsive">
        <table class="table table-secondary table-sm table-hover">
          <thead class="table-dark">
            <tr>
              <th>CODIGO DE PRODUCTO</th>
              <th>DESCRIPCION</th>
              <th>CANTIDAD</th>
              <th>PRECIO UNITARIO</th>
              <th>IMPORTE</th>
              <th>DESCUENTO</th>
              <th>ACCION</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $QRYprods = consultaData("SELECT * FROM cotizadorD INNER JOIN AlmacenP ON cotizadorD.cotD_prod_id = AlmacenP.AlmP_id WHERE cotD_cotM_id = '" . decryption($contenido[1]) . "'");
            if ($QRYprods['numRows'] > 0) {
            ?>
              <?php
              $Total = 0;
              $Descuento = 0;
              $Precio = 0;
              foreach ($QRYprods['dataFetch'] as $rowD) {
                $Precio = $rowD['cotD_monto'] * $rowD['cotD_cantidad'];
                $subTotal = $rowD['cotD_monto'] * $rowD['cotD_cantidad'];
                $Descuento = $Descuento + $rowD['cotD_descuento'];
                $Total = $subTotal + $Total;
              ?>
                <tr>
                  <td><?= $rowD['AlmP_codigo']; ?></td>
                  <td><?= $rowD['AlmP_descripcion']; ?></td>
                  <td><?= $rowD['cotD_cantidad']; ?></td>
                  <td><?= $rowD['cotD_monto']; ?></td>
                  <td><?= $Precio; ?></td>
                  <td><?= $rowD['cotD_descuento']; ?></td>
                  <td>
                    <?php if ($dataFetch['cotM_estatus'] == 1) { ?>
                      <button class="btn btn-danger btnDelCotD" value="<?= encryption($rowD['cotD_id']); ?>"><i class="fas fa-trash"></i></button>
                    <?php } ?>
                  </td>
                </tr>
              <?php
              }

              $IVA = $dataFetch['cotM_IVA'] / 100;
              $SubTotal = $Total;
              $Total = $Total - $Descuento;
              $IVA = $Total * $IVA;
              $TotalIVA = round($Total + $IVA, 3);
              ?>
              <tr class="table-dark">
                <td colspan="3"></td>
                <td style="text-align: right;">SUBTOTAL &nbsp; </td>
                <td><?= round($SubTotal, 3); ?></td>
                <td colspan="2"></td>
              </tr>
              <tr class="table-dark">
                <td colspan="3"></td>
                <td style="text-align: right;">DESCUENTO &nbsp; </td>
                <td><?= round($Descuento, 3); ?></td>
                <td colspan="2"></td>
              </tr>
              <tr class="table-dark">
                <td colspan="3"></td>
                <td style="text-align: right;">IVA &nbsp; </td>
                <td><?= $IVA; ?></td>
                <td colspan="2"></td>
              </tr>
              <tr class="table-dark">
                <td colspan="3"></td>
                <td style="text-align: right;">TOTAL &nbsp; </td>
                <td><?= $TotalIVA; ?></td>
                <td colspan="2"></td>
              </tr>
            <?php } else { ?>
              <tr>
                <td colspan="6">
                  <center>SIN PRODUCTOS AGREGADOS</center>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <!---  FIN  ---  TABLA DE REGISTROS  ---  FIN  --->

    </fieldset>
    <!---  FIN  ===============  SECCION DE PRODUCTOS  ===============  FIN  --->
  </div>
<?php }
