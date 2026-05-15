<?php

/**
 * CONSULTA MANTENIDA
 * Se mantiene la relación con Rentas, Contratos y Clientes.
 */
$SQL = "SELECT * FROM Equipos E
        INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
        INNER JOIN ProveedoresE P ON E.equipo_provE_id = P.provE_id
        LEFT JOIN Rentas R ON E.equipo_id = R.renta_equipo_id
        LEFT JOIN Contratos C ON R.renta_contrato_id = C.contrato_id
        LEFT JOIN Clientes CL ON C.contrato_cliente_id = CL.cliente_id
        ORDER BY E.equipo_estado ASC, E.equipo_fingreso DESC";

$QRY = consultaData($SQL);

// Configuración de contadores y estilos
$conteoEstados = [
  "Rentado"      => 0,
  "Espera"       => 0,
  "Reparacion"   => 0,
  "Desmantelado" => 0,
  "Vendido"      => 0,
];

$badgesEstado = [
  "Rentado"      => "bg-success",
  "Espera"       => "bg-primary",
  "Reparacion"   => "bg-warning text-dark",
  "Desmantelado" => "bg-danger",
  "Vendido"      => "bg-dark"
];

foreach ($QRY['dataFetch'] as $equipo) {
  $est = $equipo['equipo_estado'];
  if (isset($conteoEstados[$est])) $conteoEstados[$est]++;
}
?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0 text-uppercase text-primary">
        <i class="fas fa-print me-2"></i> Control de Inventario
      </h4>
      <p class="text-muted small mb-0">Gestión general y listado de todos los equipos registrados en el sistema.</p>
    </div>

    <ul class="nav nav-pills small">
      <li class="nav-item">
        <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Agregar'; ?>">
          <i class="fas fa-plus me-1"></i> AGREGAR EQUIPO
        </a>
      </li>
      <li class="nav-item">
        <div class="btn-group" role="group" aria-label="Basic example">
          <span class="btn btn-danger" id="btnPrintModelos"><i class="fas fa-file-pdf"></i></span>
          <a class="btn btn-light shadow-sm" href="<?= SERVERURL . 'Equipos/Modelos'; ?>">
            <i class="fas fa-print me-1"></i> LISTA DE MODELOS
          </a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link active bg-primary shadow-sm border mx-1" href="#">
          <i class="fas fa-file-download me-1"></i> REPORTES
        </a>
      </li>
    </ul>
  </div>

  <?php if ($QRY['numRows'] > 0): ?>
    <div class="row g-2 mb-4">
      <?php foreach ($conteoEstados as $nombre => $total): ?>
        <div class="col">
          <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-2">
              <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;"><?= $nombre ?></small>
              <span class="h5 fw-bold mb-0"><?= $total ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="dataTable table table-sm table-hover table-secondary table-striped-columns">
          <thead class="table-dark">
            <tr class="small text-uppercase">
              <th style="width: 10%;" class="ps-3 text-center">Ingreso / Proveedor</th>
              <th style="width: 10%;" class="text-center">Equipo / Identificadores</th>
              <th style="width: 40%;" class="text-center">Niveles de Tóner</th>
              <th style="width: 35%;" class="text-center">Estado / Cliente / Detalles</th>
              <th style="width: 5%;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <?php foreach ($QRY['dataFetch'] as $row):
              $est = $row['equipo_estado'];
              $badgeClass = $badgesEstado[$est] ?? "bg-secondary";

              // Preparar datos de niveles para el bucle de barras
              $niveles = [
                ['k' => 'K', 'n' => $row['equipo_nivel_K'], 'c' => 'bg-dark'],
                ['k' => 'C', 'n' => $row['equipo_nivel_C'], 'c' => 'bg-info'],
                ['k' => 'M', 'n' => $row['equipo_nivel_M'], 'c' => 'bg-danger'],
                ['k' => 'Y', 'n' => $row['equipo_nivel_Y'], 'c' => 'bg-warning']
              ];
              if ($row['modelo_resi'] == "1") {
                $niveles[] = ['k' => 'R', 'n' => $row['equipo_nivel_R'], 'c' => 'bg-secondary'];
              }
            ?>
              <tr>
                <td class="ps-3">
                  <span class="d-block fw-bold small text-primary"><?= dateFormat($row['equipo_fingreso'], 'completa') ?></span>
                  <small class="text-muted text-uppercase" style="font-size: 0.75rem;"><?= $row['provE_nombre'] ?></small>
                </td>
                <td>
                  <div class="fw-bold text-uppercase"><?= "{$row['modelo_linea']} {$row['modelo_modelo']}" ?></div>
                  <div class="d-flex gap-1 mt-1">
                    <span class="badge bg-light text-dark border fw-normal">S: <?= $row['equipo_serie'] ?></span>
                    <span class="badge bg-light text-dark border fw-normal">C: <?= $row['equipo_codigo'] ?></span>
                  </div>
                </td>
                <td>
                  <div class="row g-1">
                    <?php foreach ($niveles as $toner):
                      if ($toner['k'] !== 'K' && $row['modelo_tipo'] !== "Multicolor") continue;
                      $pct = ($toner['n'] < 0) ? 0 : $toner['n'];
                      $display = ($toner['n'] < 0) ? "N/A" : $toner['n'] . "%";
                      $column = $toner['k'] === 'K' || $toner['k'] === 'R' ? "col-12" : "col";
                    ?>
                      <div class="<?= $column ?>">
                        <div class="d-flex align-items-center">
                          <div class="progress flex-grow-1" style="height: 6px; background-color: #eee;">
                            <div class="progress-bar <?= $toner['c'] ?>" role="progressbar" style="width: <?= $pct ?>%"></div>
                          </div>
                          <span class="ms-1 fw-bold" style="font-size: 0.65rem;"><?= $display ?></span>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <span class="badge <?= $badgeClass ?> me-2 px-3 rounded-pill"><?= $est ?></span>
                    <div class="small">
                      <?php if ($est == "Rentado"): ?>
                        <?php if ($row['renta_folio']): ?>
                          <strong class="text-dark d-block"><?= $row['cliente_rs'] ?></strong>
                          <strong class="text-dark d-block"><?= "{$row['contrato_folio']}-{$row['renta_folio']}" ?></strong>
                          <a href="<?= SERVERURL . "Rentas/Detalles/" . encryption($row['renta_id']) ?>" class="badge bg-dark me-2 px-3 rounded-pill" target="_blank"><?= "{$row['renta_depto']}" ?></a>
                        <?php else: ?>
                          <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> SIN ASIGNACIÓN</span>
                        <?php endif; ?>
                      <?php elseif ($est == "Reparacion"): ?>
                        <span class="text-muted italic">Equipo no disponible</span>
                      <?php elseif ($est == "Espera"): ?>
                        <span class="text-muted italic">Disponible en Almacén</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm shadow-sm">
                    <a class="btn btn-warning" title="Editar" href="<?= SERVERURL . $pagina[0] . "/ID/" . encryption($row['equipo_id']); ?>">
                      <i class="fas fa-edit"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
  /* Estilos adicionales para limpieza visual */
  .table thead th {
    border: none;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 12px 8px;
  }

  .table tbody td {
    border-bottom: 1px solid #f2f2f2;
    padding: 12px 8px;
  }

  .progress-bar {
    transition: width 0.6s ease;
  }

  .btn-group .btn {
    border: none;
  }

  .card {
    border-radius: 10px;
  }
</style>