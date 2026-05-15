<?php
$id_contrato = decryption($pagina[2]);

// 1. Datos Generales del Contrato y Cliente
$sqlC = "SELECT * FROM Contratos 
          INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id 
          WHERE contrato_id = '$id_contrato'";
$datosC = consultaData($sqlC);

if ($datosC['numRows'] != 1) {
  require_once "./vista/cont/404.php";
  exit();
}
$c = $datosC['dataFetch'][0];

// 2. Rentas Activas (con datos de equipo y último reporte)
$sqlActivas = "SELECT R.*, E.equipo_serie, E.equipo_codigo, M.modelo_tipo, 
                (SELECT date_of_receipt FROM historial_reportes WHERE renta_id = R.renta_id ORDER BY date_of_receipt DESC LIMIT 1) as ultima_lec
                FROM Rentas R
                LEFT JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
                LEFT JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
                WHERE R.renta_contrato_id = '$id_contrato' AND R.renta_estado = 'Activo'";
$rentasActivas = consultaData($sqlActivas);

// 3. Rentas Históricas (Canceladas)
$sqlHistoricas = "SELECT * FROM Rentas WHERE renta_contrato_id = '$id_contrato' AND renta_estado = 'Cancelado'";
$rentasHistoricas = consultaData($sqlHistoricas);

// Función auxiliar para valores predeterminados
function checkVal($valRaw, $sufijo = "")
{
  if ($sufijo != "") {
    $val = $sufijo . number_format($valRaw, 2);
  } else {
    $val = $valRaw;
  }
  return (empty($valRaw) || $valRaw == 0) ? "N/E" : $val;
}
?>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="fw-bold text-dark mb-0">CONTRATO: <?= $c['contrato_folio'] ?></h3>
      <span class="text-muted small text-uppercase">Gestión Integral de Cliente y Suministros</span>
    </div>
    <div>
      <?php
      $btn = [
        'Activo' => 'btn-success',
        'Espera' => 'btn-warning text-dark',
        'Cancelado' => 'btn-danger'
      ];
      ?>
      <?php if ($c['contrato_estado'] == 'Activo') { ?>
        <button id="btnPrintDetalleContrato" class="btn btn-<?= $c['contrato_firma_estatus'] == 1 ? "success" : "primary" ?> btn-sm" data-contrato="<?= encryption($c['contrato_id']) ?>" data-estatus="<?= $c['contrato_firma_estatus'] ?>" data-folio="<?= $c['contrato_folio'] ?>">
          <?= $c['contrato_firma_estatus'] == 1 ? '<i class="fas fa-file-pdf mx-2"></i>' : '<i class="fas fa-print mx-2"></i>' ?>
        </button>
      <?php } ?>
      <span class="btn <?= $btn[$c['contrato_estado']] ?> btn-sm fw-bold">
        ESTADO: <?= strtoupper($c['contrato_estado']) ?>
      </span>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12 col-lg-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 fw-bold"><i class="fas fa-building me-2 text-primary"></i>Detalles del Cliente</h6>
        </div>
        <div class="card-body">
          <h5 class="fw-bold text-primary mb-1"><?= $c['cliente_rs'] ?></h5>
          <p class="text-muted small mb-3">RFC: <?= $c['cliente_rfc'] ?></p>

          <hr>

          <div class="mb-2">
            <label class="text-muted small d-block">Contacto Principal</label>
            <span class="fw-bold"><?= $c['cliente_contacto'] ?></span>
          </div>
          <div class="mb-2">
            <label class="text-muted small d-block">Teléfono / Correo</label>
            <i class="fas fa-phone-alt me-1 small"></i> <?= $c['cliente_telefono'] ?><br>
            <i class="fas fa-envelope me-1 small"></i> <?= $c['cliente_correo'] ?>
          </div>
          <div class="mt-3">
            <label class="text-muted small d-block">Vigencia de Contrato</label>
            <span class="badge bg-light text-dark border">
              <i class="far fa-calendar-alt me-1"></i> <?= $c['contrato_finicio'] ?> al <?= $c['contrato_ffin'] ?? 'Indefinido' ?>
            </span>
          </div>
          <?php if ($c['contrato_estado'] == 'Activo' && $c['contrato_firma_estatus'] == 0) { ?>
            <hr>
            <div class="bg-light p-3 rounded">
              <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="upload" autocomplete="off">
                <input type="hidden" value="<?= encryption($c['contrato_id']) ?>" name="contrato_id_upload" required>
                <input type="file" name="contrato_file_upload" class="form-control" accept=".pdf" required>
                <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">SUBIR CONTRATO FIRMADO</button>
              </form>
            </div>
          <?php } else { ?>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 fw-bold text-dark"><i class="fas fa-print me-2 text-info"></i>Equipos en Renta (Activos)</h6>
          <span class="badge bg-info text-dark"><?= $rentasActivas['numRows'] ?> Unidades</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table-sm table-hover table table-secondary">
              <thead class="table-dark">
                <tr>
                  <th class="ps-4">Folio Renta / Depto</th>
                  <th>Equipo / Serie</th>
                  <th>Costo</th>
                  <th>Incluido</th>
                  <th>Última Lectura</th>
                  <th class="text-end pe-4">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($rentasActivas['numRows'] > 0):
                  foreach ($rentasActivas['dataFetch'] as $ra): ?>
                    <tr>
                      <td class="ps-4">
                        <span class="fw-bold d-block text-info"><?= $ra['renta_folio'] ?></span>
                        <small class="text-muted"><?= $ra['renta_depto'] ?></small>
                      </td>
                      <td>
                        <span class="badge bg-dark mb-1"><?= $ra['equipo_codigo'] ?></span><br>
                        <small class="fw-bold"><?= $ra['equipo_serie'] ?></small>
                      </td>
                      <td>
                        <span class="small d-block"><b><?= checkVal($ra['renta_costo'], "$") ?></b></span>
                      </td>
                      <td>
                        <span class="text-muted" style="font-size: 0.7rem;">ESC: <?= checkVal($ra['renta_inc_esc']) ?> / <?= checkVal($ra['renta_exc_esc'], "$") ?></span>
                        <br>
                        <span class="text-muted" style="font-size: 0.7rem;">BN: <?= checkVal($ra['renta_inc_bn']) ?> / <?= checkVal($ra['renta_exc_bn'], "$") ?></span>
                        <?php if ($ra['modelo_tipo'] == 'Multicolor') { ?>
                          <br>
                          <span class="text-muted" style="font-size: 0.7rem;">COL: <?= checkVal($ra['renta_inc_col']) ?> / <?= checkVal($ra['renta_exc_col'], "$") ?></span>
                        <?php } ?>
                      </td>
                      <td>
                        <div class="small">
                          <i class="far fa-clock me-1 text-warning"></i>
                          <?= $ra['ultima_lec'] ? date("d/m/y H:i", strtotime($ra['ultima_lec'])) : 'Sin registros' ?>
                        </div>
                      </td>
                      <td class="text-end pe-4">
                        <div class="btn-group" role="group" aria-label="Basic outlined example">
                          <a href="<?= SERVERURL ?>Rentas/Detalles/<?= encryption($ra['renta_id']) ?>" class="btn btn-dark btn-sm border shadow-sm">
                            <i class="fas fa-eye text-light"></i>
                          </a>
                          <a href="<?= SERVERURL ?>Rentas/Editar/<?= encryption($ra['renta_id']) ?>" class="btn btn-warning btn-sm border shadow-sm">
                            <i class="fas fa-edit text-light"></i>
                          </a>
                          <a href="https://google.com/maps/search/<?= $ra['renta_coor'] ?>" class="btn btn-sm btn-danger border shadow-sm" target="_blank">
                            <i class="fas fa-map-marker-alt"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach;
                else: ?>
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted small">No hay rentas activas en este contrato.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light py-2">
          <h6 class="m-0 small fw-bold text-muted text-uppercase">Historial de Rentas Finalizadas</h6>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm table-borderless small mb-0">
            <tbody class="text-muted">
              <?php if ($rentasHistoricas['numRows'] > 0):
                foreach ($rentasHistoricas['dataFetch'] as $rh): ?>
                  <tr class="border-bottom">
                    <td class="ps-4 py-2 opacity-75"><?= $rh['renta_folio'] ?> - <?= $rh['renta_depto'] ?></td>
                    <td class="py-2">Finalizó: <?= $rh['renta_ffin'] ?? 'S/F' ?></td>
                    <td class="text-end pe-4 py-2"><span class="badge bg-secondary">Cancelado</span></td>
                    <td class="text-end pe-4 py-2"><a href="/Rentas/Detalles/<?= encryption($rh['renta_id']) ?>" target="_blanck" class="badge bg-info">Detalles</a></td>
                  </tr>
                <?php endforeach;
              else: ?>
                <tr>
                  <td class="ps-4 py-3 opacity-50">No hay historial de rentas previas.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>