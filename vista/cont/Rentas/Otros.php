<?php
$consulta = "SELECT * FROM Rentas
                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE renta_estado != 'Activo'";
$query = consultaData($consulta);
?>

<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3 border-start border-4 border-warning">
    <div>
      <h4 class="fw-bold text-dark mb-0">Historial de Rentas Inactivas</h4>
      <p class="text-muted small mb-0">Equipos retirados, contratos finalizados o cancelados</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= SERVERURL; ?>Rentas/Agregar" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Agregar
      </a>
      <a href="<?= SERVERURL; ?>Rentas/Lista" class="btn btn-outline-dark shadow-sm">
        <i class="fas fa-thumbs-up me-1"></i> Activos
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="dataTable table table-hover align-middle mb-0" style="width:100%">
          <thead class="table-dark">
            <tr>
              <th style="text-align: center;" class="ps-4">CLIENTE / RS</th>
              <th style="text-align: center;">CONTRATO</th>
              <th style="text-align: center;">SERVICIO / DEPTO</th>
              <th style="text-align: center;">CONTACTO</th>
              <th style="text-align: center;" class="text-center">ESTADO</th>
              <th style="text-align: center;" class="pe-4 text-center">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($query['numRows'] != 0):
              foreach ($query['dataFetch'] as $row):
                // Definir color de badge según estado
                $badgeColor = ($row['renta_estado'] == "Cancelado") ? "bg-danger" : "bg-secondary";
            ?>
                <tr class="<?= ($row['renta_estado'] == "Cancelado") ? 'table-light' : ""; ?>">
                  <td class="ps-4">
                    <div class="fw-bold"><?= $row['cliente_rs']; ?></div>
                    <span class="text-muted small"><i class="fas fa-fingerprint me-1"></i><?= $row['cliente_rfc']; ?></span>
                  </td>
                  <td>
                    <div class="small fw-bold text-dark"><?= $row['contrato_folio'] . "-" . $row['renta_folio']; ?></div>
                  </td>
                  <td>
                    <a href="/Rentas/Detalles/<?= encryption($row['renta_id']) ?>" class="fw-bold text-dark text-decoration-none">
                      <?= $row['renta_depto']; ?>
                    </a>
                  </td>
                  <td>
                    <div class="small">
                      <span class="d-block"><i class="fas fa-user me-1 text-muted"></i><?= $row['renta_contacto']; ?></span>
                      <span class="text-muted"><i class="fas fa-phone me-1 text-muted"></i><?= $row['renta_telefono']; ?></span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge <?= $badgeColor; ?> bg-opacity-10 <?= str_replace('bg-', 'text-', $badgeColor); ?> border <?= str_replace('bg-', 'border-', $badgeColor); ?> px-3 py-2">
                      <i class="fas fa-exclamation-circle me-1"></i><?= strtoupper($row['renta_estado']); ?>
                    </span>
                  </td>
                  <td class="pe-4 text-center">
                    <button class="btn btn-sm btn-info text-white shadow-sm btnDetails" value="<?= encryption($row['renta_id']); ?>">
                      <i class="fas fa-search-plus me-1"></i> Detalles
                    </button>
                  </td>
                </tr>
            <?php endforeach;
            endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>