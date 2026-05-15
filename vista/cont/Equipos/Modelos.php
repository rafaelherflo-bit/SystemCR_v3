<?php
$SQL = "SELECT * FROM Modelos M
        ORDER BY M.modelo_tipo ASC";
$QRY = consultaData($SQL);
?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0 text-uppercase text-primary">
        <i class="fas fa-print me-2"></i> Modelos De Equipos
      </h4>
      <p class="text-muted small mb-0">Gestión y listado de todos los modelos de equipos disponibles en el sistema.</p>
    </div>

    <ul class="nav nav-pills small">
      <li class="nav-item">
        <div class="btn-group" role="group" aria-label="Basic example">
          <span class="btn btn-danger" id="btnPrintModelos"><i class="fas fa-file-pdf"></i></span>
          <a class="btn btn-light shadow-sm" href="<?= SERVERURL . 'Equipos/Lista'; ?>">
            <i class="fas fa-clipboard-list me-1"></i> LISTA DE EQUIPOS
          </a>
        </div>
      </li>
    </ul>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="dataTable table table-sm table-hover table-secondary table-striped">
        <thead class="table-dark">
          <tr class="small text-uppercase">
            <th class="ps-3 text-center">TIPO</th>
            <th class="text-center">LINEA</th>
            <th class="text-center">MODELO</th>
            <th class="text-center">TONER</th>
            <th class="text-center">DK</th>
            <th class="text-center">DV</th>
            <th class="text-center">FK</th>
            <th class="text-center">DP</th>
            <th class="text-center">TR</th>
            <th class="text-center">DR</th>
            <th style="width: 5%;" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <?php foreach ($QRY['dataFetch'] as $row) { ?>
            <tr>
              <td><?= $row['modelo_tipo'] ?></td>
              <td><?= $row['modelo_linea'] ?></td>
              <td><?= $row['modelo_modelo'] ?></td>
              <td><?= $row['modelo_toner'] != NULL ? $row['modelo_toner'] : "---" ?></td>
              <td><?= $row['modelo_DK'] != NULL ? $row['modelo_DK'] : "---" ?></td>
              <td><?= $row['modelo_DV'] != NULL ? $row['modelo_DV'] : "---" ?></td>
              <td><?= $row['modelo_FK'] != NULL ? $row['modelo_FK'] : "---" ?></td>
              <td><?= $row['modelo_DP'] != NULL ? $row['modelo_DP'] : "---" ?></td>
              <td><?= $row['modelo_TR'] != NULL ? $row['modelo_TR'] : '---'; ?></td>
              <td><?= $row['modelo_DR'] != NULL ? $row['modelo_DR'] : '---'; ?></td>
              <td>
                <a href="/Equipos/Modeloid/<?= encryption($row['modelo_id']) ?>" class="btn btn-warning">
                  <i class="fas fa-pen"></i>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
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