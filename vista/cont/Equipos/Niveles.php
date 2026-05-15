<?php

/**
 * CONSULTA OPTIMIZADA (SÓLO EQUIPOS)
 * Eliminamos los JOINs de Rentas, Contratos y Clientes para evitar el error de GROUP BY
 * y mejorar el rendimiento.
 */
$SQL = "SELECT * FROM Equipos E
        INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
        INNER JOIN ProveedoresE P ON E.equipo_provE_id = P.provE_id
        ORDER BY E.equipo_estado ASC, E.equipo_fingreso DESC";

$QRY = consultaData($SQL);

// Configuración de conteo
$conteoEstados = [
  "Rentado"      => 0,
  "Espera"       => 0,
  "Reparacion"   => 0,
  "Vendido"      => 0,
];

$badgesEstado = [
  "Rentado"      => "bg-success",
  "Espera"       => "bg-primary",
  "Reparacion"   => "bg-warning text-dark",
  "Vendido"      => "bg-dark"
];

foreach ($QRY['dataFetch'] as $equipo) {
  $est = $equipo['equipo_estado'];
  if (isset($conteoEstados[$est])) $conteoEstados[$est]++;
}
?>
  <div class="container-fluid py-3">
    <h4 class="fw-bold mb-0 text-uppercase">Inventario de Equipos</h4>
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <ul class="nav nav-pills small">
      <li class="nav-item">
        <a class="nav-link bg-light text-dark me-2" href="<?= SERVERURL . 'Equipos/Agregar'; ?>">
          <i class="fas fa-plus"></i> NUEVO EQUIPO
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="#"><i class="fas fa-list"></i> VISTA SIMPLE</a>
      </li>
    </ul>
  </div>

  <?php if ($QRY['numRows'] > 0): ?>
    <div class="row g-2 mb-4">
      <?php foreach ($conteoEstados as $nombre => $total): ?>
        <div class="col">
          <div class="card border-0 shadow-sm text-center py-2 bg-white">
            <div class="card-body p-1">
              <span class="d-block text-muted small text-uppercase"><?= $nombre ?></span>
              <span class="h4 fw-bold mb-0 text-primary"><?= $total ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-dark text-white">
            <tr class="small text-uppercase">
              <th class="ps-3">Ingreso / Prov</th>
              <th>Modelo / Serie / Código</th>
              <th>Estado</th>
              <th>Niveles de Tóner</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <?php foreach ($QRY['dataFetch'] as $row):
              $est = $row['equipo_estado'];
              $badge = $badgesEstado[$est] ?? "bg-secondary";

              $niveles = [
                'K' => ['n' => $row['equipo_nivel_K'], 'c' => 'bg-dark'],
                'C' => ['n' => $row['equipo_nivel_C'], 'c' => 'bg-info'],
                'M' => ['n' => $row['equipo_nivel_M'], 'c' => 'bg-danger'],
                'Y' => ['n' => $row['equipo_nivel_Y'], 'c' => 'bg-warning']
              ];
            ?>
              <tr>
                <td class="ps-3">
                  <span class="d-block fw-bold small"><?= dateFormat($row['equipo_fingreso'], 'completa') ?></span>
                  <small class="text-muted"><?= $row['provE_nombre'] ?></small>
                </td>
                <td>
                  <strong class="text-uppercase"><?= $row['modelo_linea'] ?> <?= $row['modelo_modelo'] ?></strong>
                  <div class="mt-1">
                    <span class="badge bg-light text-dark border fw-normal">S: <?= $row['equipo_serie'] ?></span>
                    <span class="badge bg-light text-dark border fw-normal">C: <?= $row['equipo_codigo'] ?></span>
                  </div>
                </td>
                <td>
                  <span class="badge <?= $badge ?> rounded-pill px-3"><?= $est ?></span>
                </td>
                <td>
                  <div style="min-width: 150px;">
                    <div class="row g-1">
                      <?php foreach ($niveles as $key => $data):
                        if ($key !== 'K' && $row['modelo_tipo'] !== "Multicolor") continue;
                        $val = ($data['n'] < 0) ? 0 : $data['n'];
                        $label = ($data['n'] < 0) ? "N/A" : $data['n'] . "%";
                      ?>
                        <div class="col-6">
                          <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height: 5px; background-color: #eee;">
                              <div class="progress-bar <?= $data['c'] ?>" style="width: <?= $val ?>%"></div>
                            </div>
                            <span class="ms-1 fw-bold" style="font-size: 10px;"><?= $label ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <?php if ($row['modelo_resi'] == "1"): ?>
                      <small class="text-muted d-block mt-1" style="font-size: 10px;">Depósito Residuo (R): <?= $row['equipo_nivel_R'] ?>%</small>
                    <?php endif; ?>
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