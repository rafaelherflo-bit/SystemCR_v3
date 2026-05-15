<?php
$consulta = "SELECT * FROM Rentas
                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE renta_estado = 'Activo'";
$query = consultaData($consulta);

// Cálculo del total para evitar doble consulta (optimización)
$total_acumulado = 0;
?>

<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded-3">
    <div>
      <h4 class="fw-bold text-dark mb-0"><i class="fas fa-concierge-bell fa-fw"></i> Gestión de Rentas Activas</h4>
      <p class="text-muted small mb-0">Listado detallado de servicios y equipos en campo</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= SERVERURL; ?>Rentas/Agregar" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Nueva Renta
      </a>
      <a href="<?= SERVERURL; ?>Rentas/Otros" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-thumbs-down me-1"></i> Inactivos
      </a>
      <button id="btnCostos" class="btn btn-danger shadow-sm">
        <i class="fas fa-file-pdf me-1"></i> Reporte Costos
      </button>
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="dataTable table table-hover align-middle mb-0" style="width:100%">
          <thead class="table-dark">
            <tr>
              <th style="text-align: center;" class="ps-4">CLIENTE / RFC</th>
              <th style="text-align: center;">CONTRATO</th>
              <th style="text-align: center;">SERVICIO / DEPTO</th>
              <th style="text-align: center;" class="text-center">UBICACIÓN</th>
              <th style="text-align: center;">EQUIPO INSTALADO</th>
              <th style="text-align: center;">CONTACTO</th>
              <th style="text-align: center;" class="text-center">ESTADO</th>
              <th style="text-align: center;" class="pe-4 text-end">MENSUALIDAD</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($query['numRows'] != 0):
              foreach ($query['dataFetch'] as $row):
                $total_acumulado += $row['renta_costo'];
            ?>
                <tr>
                  <td class="ps-4">
                    <div class="fw-bold text-primary"><?= $row['cliente_rs']; ?></div>
                    <a href="/Clientes/Editar/<?= encryption($row['cliente_id']) ?>" class="text-muted small text-decoration-none">
                      <i class="fas fa-id-card me-1"></i><?= $row['cliente_rfc']; ?>
                    </a>
                  </td>
                  <td>
                    <a href="/Contratos/Detalles/<?= encryption($row['contrato_id']) ?>" target="_blanck" class="fw-bold text-dark text-decoration-none">
                      <?= $row['contrato_folio'] . "-" . $row['renta_folio']; ?>
                    </a>
                  </td>
                  <td>
                    <a href="/Rentas/Detalles/<?= encryption($row['renta_id']) ?>" target="_blanck" class="fw-bold text-dark text-decoration-none">
                      <?= $row['renta_depto']; ?>
                    </a>
                  </td>
                  <td class="text-center">
                    <a href="https://www.google.com/maps?q=<?= $row['renta_coor'] ?>" class="btn btn-sm btn-outline-danger rounded-pill" target="_blank">
                      <i class="fas fa-map-marked-alt"></i> Ver
                    </a>
                  </td>
                  <td>
                    <div class="small">
                      <span class="d-block fw-bold"><?= $row['modelo_linea'] . " " . $row['modelo_modelo']; ?></span>
                      <a href="/Equipos/ID/<?= encryption($row['equipo_id']) ?>" class="text-decoration-none text-muted" target="_blank">
                        <i class="fas fa-print me-1"></i><?= $row['equipo_serie']; ?>
                      </a>
                    </div>
                  </td>
                  <td>
                    <div class="small">
                      <span class="d-block fw-bold"><i class="fas fa-user-tie me-1 text-secondary"></i><?= $row['renta_contacto']; ?></span>
                      <span class="text-muted"><i class="fas fa-phone-alt me-1 text-secondary"></i><?= $row['renta_telefono']; ?></span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3">
                      <?= strtoupper($row['renta_estado']); ?>
                    </span>
                  </td>
                  <td class="pe-4 text-end fw-bold text-<?= $row['renta_costo'] == 0 ? "danger" : "dark"; ?>">
                    $<?= number_format($row['renta_costo'], 2); ?>
                  </td>
                </tr>
            <?php endforeach;
            endif; ?>
          </tbody>
          <tfoot class="table-light border-top">
            <tr>
              <td colspan="6" class="text-end fw-bold py-3 ps-4">TOTAL MENSUALIZADO:</td>
              <td class="pe-4 text-end py-3">
                <span class="fs-5 fw-bold text-primary">$<?= number_format($total_acumulado, 2); ?></span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>