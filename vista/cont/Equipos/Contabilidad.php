<?php
$equipoId = decryption($pagina[2]);

// 1. Validamos que el equipo exista
$SQL = "SELECT * FROM Equipos E 
          INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id 
          WHERE E.equipo_id = '$equipoId'";
$QRY = consultaData($SQL);

if ($QRY['numRows'] != 1) {
  require_once SERVERDIR . "vista/cont/404.php";
} else {
  $Data = $QRY['dataFetch'][0];

  // 2. Traemos los usuarios de contabilidad
  $sqlC = "SELECT * FROM equipo_contabilidad WHERE equConta_equipo_id = '$equipoId' ORDER BY equConta_ident ASC";
  $resC = consultaData($sqlC);

  // Lógica de tipo de modelo
  $esMulticolor = ($Data['modelo_tipo'] === "Multicolor");
?>

  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-baseline mb-4">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase text-success">
          <i class="fas fa-calculator me-2"></i> Contabilidad y Contadores
        </h4>
        <span class="text-muted small">
          <?= "{$Data['equipo_codigo']} | {$Data['modelo_linea']} {$Data['modelo_modelo']} | {$Data['equipo_serie']}" ?>
        </span>
      </div>

      <ul class="nav nav-pills small">
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm border mx-1" href="<?= SERVERURL . 'Equipos/Agregar'; ?>">
            <i class="fas fa-plus"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm border me-3" href="<?= SERVERURL . 'Equipos/Lista'; ?>">
            <i class="fas fa-list"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm border mx-1" href="<?= SERVERURL . 'Equipos/ID/' . $pagina[2]; ?>">
            <i class="fas fa-edit"></i>
          </a>
        </li>
        <?php if ($Data['modelo_wifi']): ?>
          <li class="nav-item">
            <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Wifi/' . $pagina[2]; ?>">
              <i class="fas fa-wifi"></i>
            </a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Ethernet/' . $pagina[2]; ?>">
            <i class="fas fa-ethernet"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-success shadow-sm mx-1 fw-bold" href="#">
            <i class="fas fa-calculator me-1"></i> CONTABILIDAD
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Contactos/' . $pagina[2]; ?>">
            <i class="fas fa-users"></i>
          </a>
        </li>
      </ul>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0 text-dark">
          <i class="fas fa-user-lock me-2 text-success"></i> Nueva Cuenta de Contabilidad
        </h5>
        <hr class="mb-0">
      </div>
      <div class="card-body p-4">
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
          <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
          <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
          <input type="hidden" name="agregarContabilidad" value="<?= $pagina[2]; ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="fw-bold small text-muted text-uppercase mb-1">ID / Código de Usuario</label>
              <input type="text" class="form-control fw-bold" name="equConta_ident" pattern="[a-zA-Z0-9]{1,155}" required placeholder="Ej: 1010">
            </div>
            <div class="col-md-6">
              <label class="fw-bold small text-muted text-uppercase mb-1">Nombre del Usuario</label>
              <input type="text" class="form-control" name="equConta_nombre" required placeholder="Ej: Juan Pérez">
            </div>
          </div>

          <div class="bg-light p-3 rounded mt-4">
            <h6 class="fw-bold text-uppercase small text-muted mb-3">
              <i class="fas fa-tachometer-alt me-2"></i> Límites de Uso <span class="text-lowercase fw-normal">(0 = Sin límite)</span>
            </h6>
            <div class="row g-2 text-center">
              <?php if ($esMulticolor): ?>
                <div class="col">
                  <label class="d-block small fw-bold">Copia Total</label>
                  <input type="number" class="form-control form-control-sm text-center" name="equConta_restCT" value="0" min="0" required>
                </div>
              <?php else: ?>
                <input type="hidden" name="equConta_restCT" value="0">
              <?php endif; ?>

              <div class="col">
                <label class="d-block small fw-bold">Copia B&N</label>
                <input type="number" class="form-control form-control-sm text-center" name="equConta_restCU" value="0" min="0" required>
              </div>

              <?php if ($esMulticolor): ?>
                <div class="col">
                  <label class="d-block small fw-bold">Copia Color</label>
                  <input type="number" class="form-control form-control-sm text-center" name="equConta_restCF" value="0" min="0" required>
                </div>
              <?php else: ?>
                <input type="hidden" name="equConta_restCF" value="0">
              <?php endif; ?>

              <div class="col">
                <label class="d-block small fw-bold">Impr. Total</label>
                <input type="number" class="form-control form-control-sm text-center" name="equConta_restIT" value="0" min="0" required>
              </div>

              <?php if ($esMulticolor): ?>
                <div class="col">
                  <label class="d-block small fw-bold">Impr. Color</label>
                  <input type="number" class="form-control form-control-sm text-center" name="equConta_restIF" value="0" min="0" required>
                </div>
              <?php else: ?>
                <input type="hidden" name="equConta_restIF" value="0">
              <?php endif; ?>

              <div class="col">
                <label class="d-block small fw-bold">Escaneo</label>
                <input type="number" class="form-control form-control-sm text-center" name="equConta_restEO" value="0" min="0" required>
              </div>
              <div class="col">
                <label class="d-block small fw-bold">FAX</label>
                <input type="number" class="form-control form-control-sm text-center" name="equConta_restFAX" value="0" min="0" required>
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-5 shadow-sm fw-bold">
              <i class="fas fa-save me-2"></i> REGISTRAR USUARIO
            </button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($resC['numRows'] > 0): ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
              <thead class="bg-dark text-white text-center">
                <tr>
                  <th rowspan="2" class="align-middle ps-3">ID</th>
                  <th rowspan="2" class="align-middle">NOMBRE</th>
                  <th colspan="<?= ($esMulticolor ? '3' : '1') ?>" class="border-bottom border-secondary">COPIADO</th>
                  <th colspan="<?= ($esMulticolor ? '2' : '1') ?>" class="border-bottom border-secondary">IMPRESIÓN</th>
                  <th colspan="2" class="border-bottom border-secondary">OTROS</th>
                  <th rowspan="2" class="align-middle pe-3">ELIMINAR</th>
                </tr>
                <tr class="small text-uppercase" style="font-size: 0.75rem; background: #2c3e50;">
                  <?php if ($esMulticolor): ?> <th>TOTAL</th> <?php endif; ?>
                  <th>B&N</th>
                  <?php if ($esMulticolor): ?> <th>FULL</th> <?php endif; ?>
                  <th>TOTAL</th>
                  <?php if ($esMulticolor): ?> <th>FULL</th> <?php endif; ?>
                  <th>ESC</th>
                  <th>FAX</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php
                foreach ($resC['dataFetch'] as $con):
                  $printLimit = function ($val) {
                    return ($val == 0) ? '<span class="badge rounded-pill bg-success-light text-success border border-success" style="background: #e8f5e9;">Sin límite</span>' : '<span class="fw-bold text-dark">' . $val . '</span>';
                  };
                ?>
                  <tr>
                    <td class="ps-3"><code><?= $con['equConta_ident'] ?></code></td>
                    <td class="text-start fw-bold"><?= $con['equConta_nombre'] ?></td>

                    <?php if ($esMulticolor): ?> <td><?= $printLimit($con['equConta_restCT']) ?></td> <?php endif; ?>
                    <td><?= $printLimit($con['equConta_restCU']) ?></td>
                    <?php if ($esMulticolor): ?> <td><?= $printLimit($con['equConta_restCF']) ?></td> <?php endif; ?>

                    <td><?= $printLimit($con['equConta_restIT']) ?></td>
                    <?php if ($esMulticolor): ?> <td><?= $printLimit($con['equConta_restIF']) ?></td> <?php endif; ?>

                    <td><?= $printLimit($con['equConta_restEO']) ?></td>
                    <td><?= $printLimit($con['equConta_restFAX']) ?></td>

                    <td class="pe-3">
                      <button class="btn btn-outline-danger btn-sm border-0 btn-delRegWithID"
                        data-table="equipo_contabilidad"
                        data-colname="equConta_id"
                        data-value="<?= encryption($con['equConta_id']) ?>">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php
}
?>