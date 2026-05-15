<?php
$consulta = "SELECT 
                C.*,
                CL.cliente_rs, CL.cliente_rfc,
                COUNT(CASE WHEN R.renta_estado = 'Activo' THEN 1 END) as total_activas,
                COUNT(CASE WHEN R.renta_estado = 'Cancelado' THEN 1 END) as total_inactivas,
                COUNT(R.renta_id) as total_rentas
            FROM Contratos C
            INNER JOIN Clientes CL ON C.contrato_cliente_id = CL.cliente_id
            LEFT JOIN Rentas R ON C.contrato_id = R.renta_contrato_id
            WHERE C.contrato_estado = 'Activo'
            GROUP BY C.contrato_id";
$query = consultaData($consulta);
?>
<div class="container-fluid mb-4">
  <div class="d-flex justify-content-between align-items-center bg-white p-3 shadow-sm rounded">
    <h5 class="m-0 fw-bold text-dark"><i class="fas fa-file-contract me-2 text-primary"></i>Listado de Contratos Activos</h5>
    <div class="btn-group">
      <a href="<?= SERVERURL; ?>Contratos/Agregar" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> AGREGAR
      </a>
      <button id="btnContrAct" class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf me-1"></i> PDF ACTIVOS
      </button>
      <a href="<?= SERVERURL; ?>Contratos/Otros" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-thumbs-down me-1"></i> NO ACTIVOS
      </a>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="dataTable table-sm table-hover table table-secondary">
          <thead class="table-dark">
            <tr>
              <th class="ps-4">FOLIO & CLIENTE</th>
              <th style="text-align: center;">ESTADÍSTICAS RENTAS</th>
              <th style="text-align: center;">DETALLE DE RENTAS (ACCESOS)</th>
              <th style="text-align: center;" class="text-center">CONTACTO</th>
              <th style="text-align: center;" class="text-end pe-4">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($query['numRows'] > 0):
              foreach ($query['dataFetch'] as $row): ?>
                <tr>
                  <td style="max-width: 250px;">
                    <span class="d-block fw-bold text-primary"><?= $row['contrato_folio'] ?></span>
                    <small class="text-dark fw-bold"><?= $row['cliente_rs'] ?></small><br>
                    <small class="text-muted">RFC: <?= $row['cliente_rfc'] ?></small>
                  </td>
                  <td>
                    <div class="d-flex flex-column gap-1">
                      <span class="badge bg-info text-dark w-100 text-start">
                        <i class="fas fa-check-circle me-1"></i> Activas: <?= $row['total_activas'] ?>
                      </span>
                      <span class="badge bg-light text-muted border w-100 text-start">
                        <i class="fas fa-times-circle me-1"></i> Inactivas: <?= $row['total_inactivas'] ?>
                      </span>
                    </div>
                  </td>
                  <td style="max-width: 450px;">
                      <?php
                      // Aquí sí consultamos los folios específicos (o podrías usar GROUP_CONCAT en el SQL)
                      $rentas = consultaData("SELECT renta_id, renta_folio, renta_depto, renta_estado FROM Rentas WHERE renta_contrato_id = " . $row['contrato_id']);
                      foreach ($rentas['dataFetch'] as $r):
                        $btnClass = ($r['renta_estado'] == "Activo") ? "btn-info" : "btn-danger";
                      ?>
                          <a href="<?= SERVERURL ?>Rentas/Detalles/<?= encryption($r['renta_id']) ?>"
                            class="btn <?= $btnClass ?> btn-xs fw-bold m-1"
                            style="font-size: 0.7rem;" target="_blank">
                            <?= $r['renta_folio'] . " | " . $r['renta_depto']  ?>
                          </a>
                      <?php endforeach; ?>
                  </td>
                  <td style="max-width: 50px;" class="text-center">
                    <span class="small fw-bold d-block"><?= $row['contrato_contacto'] ?></span>
                    <a href="tel:<?= $row['contrato_telefono'] ?>" class="text-decoration-none small text-muted">
                      <i class="fas fa-phone-alt me-1 small"></i><?= $row['contrato_telefono'] ?>
                    </a>
                  </td>
                  <td style="max-width: 50px;"  class="text-center pe-4">
                    <a href="<?= SERVERURL ?>Contratos/Detalles/<?= encryption($row['contrato_id']) ?>" target="_blank" class="btn btn-sm btn-info shadow-sm">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach;
            else: ?>
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">No hay contratos activos registrados.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>