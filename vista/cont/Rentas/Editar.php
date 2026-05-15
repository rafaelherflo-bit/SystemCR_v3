<?php
$QRY0 = consultaData("SELECT * FROM Rentas WHERE renta_id = '" . decryption($GLOBALS['pagina2']) . "'");
if ($QRY0['numRows'] == 0) {
  redirect(SERVERURL . "Rentas/Lista");
} else {
  $dataQRY0 = $QRY0['dataFetch'][0];
  if ($dataQRY0['renta_estado'] == "Activo") {
    $consulta = "SELECT * FROM Rentas
                  INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                  INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                  INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                  WHERE renta_id = '" . decryption($GLOBALS['pagina2']) . "'";
  } else {
    $consulta = "SELECT * FROM Rentas
                  INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                  WHERE renta_id = '" . decryption($GLOBALS['pagina2']) . "'";
  }

  $result = consultaData($consulta);
  if ($result['numRows'] == 0) {
    redirect(SERVERURL . "Rentas/Lista");
  } else {
    $dataFetch = $result['dataFetch'][0]; ?>
    <div class="container-fluid mb-4">

      <div class="container-fluid">
        <ul class="full-box list-unstyled page-nav-tabs">
          <li>
            <a href="/Rentas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
          </li>
          <li>
            <a href="/Rentas/Detalles/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-info-circle fa-fw"></i> &nbsp; DETALLES</a>
          </li>
          <li>
            <a class="active" href="/Rentas/Editar/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-info-circle fa-fw"></i> &nbsp; EDITAR</a>
          </li>
          <!-- <li>
            <a href="/Rentas/Contadores/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-print fa-fw"></i> &nbsp; CONTADORES</a>
          </li> -->
          <li>
            <a href="/Rentas/Lecturas/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-print fa-fw"></i> &nbsp; LECTURAS</a>
          </li>
          <li>
            <a href="/Rentas/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA</a>
          </li>
          <li>
            <a href="/Rentas/Otros"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; NO ACTIVOS</a>
          </li>
        </ul>
      </div>

      <div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded">
        <div>
          <h5 class="m-0 fw-bold text-dark">
            <i class="fas fa-edit me-2 text-warning"></i>Editar Renta: <?= $dataFetch['contrato_folio'] . "-" . $dataFetch['renta_folio']; ?>
          </h5>
          <small class="text-muted"><?= $dataFetch['cliente_rs']; ?> | <?= $dataFetch['renta_depto']; ?></small>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
        <input type="hidden" name="renta_id_edit" value="<?= $GLOBALS['pagina2']; ?>">

        <div class="row g-4">
          <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Ubicación y Contacto</h6>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">DEPARTAMENTO</label>
                    <input type="text" class="form-control" list="deptos_list" name="renta_depto" value="<?= $dataFetch['renta_depto']; ?>" required>
                    <datalist id="deptos_list">
                      <option value="Oficina">Oficina</option>
                      <option value="Sistemas">Sistemas</option>
                      <option value="Recepción">Recepción</option>
                      <option value="Obra">Obra</option>
                    </datalist>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">COORDENADAS</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-crosshairs"></i></span>
                      <input type="text" class="form-control" name="renta_coor" id="renta_coor" data-tipo="edit" value="<?= $dataFetch['renta_coor']; ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">CONTACTO DIRECTO</label>
                    <input type="text" class="form-control" name="renta_contacto" value="<?= $dataFetch['renta_contacto']; ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">TELÉFONO</label>
                    <input type="tel" class="form-control" name="renta_telefono" value="<?= $dataFetch['renta_telefono']; ?>">
                  </div>
                </div>
                <div id="map_addRentas" class="mt-3 border rounded bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                  <small class="text-muted">Cargando mapa...</small>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-header bg-dark py-3">
                <h6 class="m-0 fw-bold text-white"><i class="fas fa-box me-2"></i>Stock de Reserva (Piezas)</h6>
              </div>
              <div class="card-body">
                <p class="text-muted small">Cantidad de tóners que el cliente tiene en su almacén físico.</p>
                <div class="row g-3 text-center">
                  <div class="col">
                    <label class="badge bg-dark d-block mb-1">K</label>
                    <input type="number" class="form-control text-center fw-bold" name="renta_stock_K" value="<?= $dataFetch['renta_stock_K']; ?>" min="0" max="9">
                  </div>
                  <?php if ($dataFetch['modelo_tipo'] == "Multicolor"): ?>
                    <div class="col">
                      <label class="badge bg-danger d-block mb-1">M</label>
                      <input type="number" class="form-control text-center fw-bold" name="renta_stock_M" value="<?= $dataFetch['renta_stock_M']; ?>" min="0" max="9">
                    </div>
                    <div class="col">
                      <label class="badge bg-info d-block mb-1">C</label>
                      <input type="number" class="form-control text-center fw-bold" name="renta_stock_C" value="<?= $dataFetch['renta_stock_C']; ?>" min="0" max="9">
                    </div>
                    <div class="col">
                      <label class="badge bg-warning text-dark d-block mb-1">Y</label>
                      <input type="number" class="form-control text-center fw-bold" name="renta_stock_Y" value="<?= $dataFetch['renta_stock_Y']; ?>" min="0" max="9">
                    </div>
                  <?php endif; ?>
                  <?php if ($dataFetch['modelo_resi'] == 1): ?>
                    <div class="col">
                      <label class="badge bg-secondary d-block mb-1">R</label>
                      <input type="number" class="form-control text-center fw-bold" name="renta_stock_R" value="<?= $dataFetch['renta_stock_R']; ?>" min="0" max="9">
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-success text-white py-3">
                <h6 class="m-0 fw-bold"><i class="fas fa-dollar-sign me-2"></i>Configuración de Costos</h6>
              </div>
              <div class="card-body">
                <div class="row g-4">
                  <div class="col-md-3 border-end">
                    <label class="form-label fw-bold text-success">COSTO MENSUAL</label>
                    <div class="input-group input-group-lg">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control fw-bold" name="renta_costo" value="<?= $dataFetch['renta_costo']; ?>">
                    </div>
                  </div>

                  <div class="col-md-4 border-end">
                    <label class="form-label fw-bold">VOLUMEN INCLUIDO</label>
                    <div class="row g-2">
                      <div class="col-6">
                        <small class="d-block text-muted">Escaneos</small>
                        <input type="number" class="form-control form-control-sm" name="renta_inc_esc" value="<?= $dataFetch['renta_inc_esc']; ?>">
                      </div>
                      <div class="col-6">
                        <small class="d-block text-muted">B&N</small>
                        <input type="number" class="form-control form-control-sm" name="renta_inc_bn" value="<?= $dataFetch['renta_inc_bn']; ?>">
                      </div>
                      <?php if ($dataFetch['modelo_tipo'] == "Multicolor"): ?>
                        <div class="col-12">
                          <small class="d-block text-muted">Color</small>
                          <input type="number" class="form-control form-control-sm" name="renta_inc_col" value="<?= $dataFetch['renta_inc_col']; ?>">
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="col-md-5">
                    <label class="form-label fw-bold text-danger">PRECIO EXCEDENTE (UNITARIO)</label>
                    <div class="row g-2">
                      <div class="col-4">
                        <small class="d-block text-muted">Exced. Esc</small>
                        <input type="number" step="0.001" class="form-control form-control-sm" name="renta_exc_esc" value="<?= $dataFetch['renta_exc_esc']; ?>">
                      </div>
                      <div class="col-4">
                        <small class="d-block text-muted">Exced. B&N</small>
                        <input type="number" step="0.001" class="form-control form-control-sm" name="renta_exc_bn" value="<?= $dataFetch['renta_exc_bn']; ?>">
                      </div>
                      <?php if ($dataFetch['modelo_tipo'] == "Multicolor"): ?>
                        <div class="col-4">
                          <small class="d-block text-muted">Exced. Col</small>
                          <input type="number" step="0.001" class="form-control form-control-sm" name="renta_exc_col" value="<?= $dataFetch['renta_exc_col']; ?>">
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 text-center">
          <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
        </div>
      </form>
    </div>
<?php
  }
}
