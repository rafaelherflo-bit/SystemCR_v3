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
?>

  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-baseline mb-4">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase text-dark">
          <i class="fas fa-edit me-2"></i> Datos Básicos
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
          <a class="nav-link active bg-warning text-dark fw-bold shadow-sm" href="#">
            <i class="fas fa-edit me-1"></i> BÁSICOS
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Wifi/' . $pagina[2]; ?>">
            <i class="fas fa-wifi"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Ethernet/' . $pagina[2]; ?>">
            <i class="fas fa-ethernet"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Contabilidad/' . $pagina[2]; ?>">
            <i class="fas fa-calculator"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Contactos/' . $pagina[2]; ?>">
            <i class="fas fa-users"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Contadores/' . $pagina[2]; ?>">
            <i class="fas fa-list"></i>
          </a>
        </li>
      </ul>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update" autocomplete="off">
          <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
          <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
          <input type="hidden" name="actualizarEquipo" value="<?= $pagina[2]; ?>">
          <input type="hidden" id="equipo_actual_id" value="<?= $equipoId; ?>">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Fecha Ingreso</label>
              <input type="date" class="form-control" name="equipo_fingreso" value="<?= $Data['equipo_fingreso']; ?>">
            </div>

            <div class="col-md-8">
              <label class="fw-bold small text-muted text-uppercase">Número de Serie</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fas fa-barcode text-warning"></i></span>
                <input type="text" class="form-control fw-bold" list="codigos" id="equipo_serie_edit" name="equipo_serie" pattern="[A-Z0-9]{9,15}" value="<?= $Data['equipo_serie']; ?>" required>
              </div>
              <datalist id="codigos">
                <?php
                $sql = "SELECT E.equipo_serie, M.modelo_linea, M.modelo_modelo FROM Equipos E INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id ORDER BY equipo_serie ASC";
                $query = consultaData($sql);
                foreach ($query['dataFetch'] as $dato) {
                  echo "<option value='{$dato['equipo_serie']}'>{$dato['modelo_linea']} {$dato['modelo_modelo']}</option>";
                }
                ?>
              </datalist>
              <div id="msg-error-serie"></div>
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Modelo</label>
              <select class="form-select" name="equipo_modelo_id">
                <?php
                $modelos = consultaData("SELECT modelo_id, modelo_linea, modelo_modelo FROM Modelos");
                foreach ($modelos['dataFetch'] as $mod) {
                  $sel = ($mod['modelo_id'] == $Data['equipo_modelo_id']) ? "selected" : "";
                  echo "<option value='" . encryption($mod['modelo_id']) . "' $sel>{$mod['modelo_linea']} | {$mod['modelo_modelo']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Proveedor</label>
              <select class="form-select" name="equipo_provE_id">
                <?php
                $provs = consultaData("SELECT provE_id, provE_nombre FROM ProveedoresE");
                foreach ($provs['dataFetch'] as $pv) {
                  $sel = ($pv['provE_id'] == $Data['equipo_provE_id']) ? "selected" : "";
                  echo "<option value='" . encryption($pv['provE_id']) . "' $sel>{$pv['provE_nombre']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Estado</label>
              <?php if ($Data['equipo_estado'] == "Rentado"): ?>
                <div class="input-group">
                  <span class="input-group-text bg-success text-white border-0"><i class="fas fa-handshake"></i></span>
                  <input type="text" class="form-control bg-light" value="RENTADO (BLOQUEADO)" disabled>
                </div>
                <input type="hidden" name="equipo_estado" value="Rentado">
              <?php else: ?>
                <select class="form-select" name="equipo_estado">
                  <?php
                  $estados = ["Espera", "Reparacion", "Inhabilitado", "Vendido"];
                  foreach ($estados as $e) {
                    $sel = ($Data['equipo_estado'] == $e) ? "selected" : "";
                    echo "<option value='$e' $sel>$e</option>";
                  }
                  ?>
                </select>
              <?php endif; ?>
            </div>
          </div>


          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
              <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-fill-drip me-2"></i>Estado de Suministros
              </h6>
            </div>
            <div class="card-body">
              <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">

                <?php
                // 1. Definimos la configuración base (Negro siempre va)
                $toners = [
                  ['K', 'bg-dark', 'labelK', 'equipo_nivel_K', 'chip_k', 'NEGRO']
                ];

                // 2. Si es multicolor, añadimos los demás de forma correcta
                if ($Data['modelo_tipo'] == "Multicolor") {
                  $toners[] = ['M', 'bg-danger', 'labelM', 'equipo_nivel_M', 'chip_m', 'MAGENTA'];
                  $toners[] = ['C', 'bg-info',   'labelC', 'equipo_nivel_C', 'chip_c', 'CYAN'];
                  $toners[] = ['Y', 'bg-warning', 'labelY', 'equipo_nivel_Y', 'chip_y', 'AMARILLO'];
                }

                // 3. Iteramos con referencias claras
                foreach ($toners as $t):
                  $letra      = $t[0]; // K, M, C, Y
                  $colorClass = $t[1]; // bg-dark, etc
                  $labelId    = $t[2]; // labelK
                  $nameNivel  = $t[3]; // equipo_nivel_K
                  $nameChip   = $t[4]; // chip_k
                  $nombre     = $t[5]; // NEGRO, MAGENTA...
                ?>
                  <div class="col">
                    <div class="card border-dark shadow-sm h-100">
                      <div class="card-header <?= $colorClass ?> text-white text-center py-1 small fw-bold">
                        <?= $nombre ?> (<?= $letra ?>)
                      </div>
                      <div class="card-body p-2 text-center">
                        <label class="form-label mb-0 small fw-bold">Nivel
                          <span id="<?= $labelId ?>"><?= $Data[$nameNivel] ?>%</span>
                        </label>
                        <input
                          type="range"
                          name="<?= $nameNivel ?>"
                          class="form-range"
                          min="0"
                          max="100"
                          value="<?= $Data[$nameNivel] ?>"
                          oninput="updateRangeLabel(this, '<?= $labelId ?>')" />

                        <label class="d-block small fw-bold mb-1">¿Tiene Chip?</label>
                        <div class="btn-group btn-group-sm w-100" role="group">
                          <input
                            type="radio"
                            class="btn-check"
                            name="<?= $nameChip ?>"
                            id="<?= $nameChip ?>_si"
                            value="1"
                            <?= ($Data[$nameChip] == 1) ? 'checked' : ''; ?> />
                          <label class="btn btn-outline-success" for="<?= $nameChip ?>_si">SÍ</label>

                          <input
                            type="radio"
                            class="btn-check"
                            name="<?= $nameChip ?>"
                            id="<?= $nameChip ?>_no"
                            value="0"
                            <?= ($Data[$nameChip] == 0) ? 'checked' : ''; ?> />
                          <label class="btn btn-outline-danger" for="<?= $nameChip ?>_no">NO</label>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

                <?php if ($Data['modelo_resi'] == 1) { ?>
                  <div class="col">
                    <div class="card h-100 border-secondary shadow-sm">
                      <div class="card-header bg-secondary text-white text-center py-1 small fw-bold">
                        RESIDUAL (R)
                      </div>
                      <div class="card-body p-2 text-center">
                        <label class="form-label mb-0 small fw-bold">Nivel
                          <span id="labelR"><?= $Data['equipo_nivel_R'] ?>%</span>
                        </label>
                        <input
                          type="range"
                          name="equipo_nivel_R"
                          class="form-range"
                          min="0"
                          max="100"
                          value="<?= $Data['equipo_nivel_R'] ?>"
                          oninput="updateRangeLabel(this, 'labelR')" />
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="text-center mt-5">
            <button type="submit" id="btn-actualizar" class="btn btn-warning px-5 fw-bold shadow-sm">
              <i class="fas fa-sync-alt me-2"></i> ACTUALIZAR DATOS
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>