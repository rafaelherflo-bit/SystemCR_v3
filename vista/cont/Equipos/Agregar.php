<div class="container-fluid py-3">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
        <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
        <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
        <input type="hidden" name="agregarEquipo" value="1">

        <div class="row g-3">
          <div class="col-md-4">
            <label class="fw-bold small text-muted text-uppercase">Fecha de Ingreso</label>
            <input type="date" class="form-control" name="equipo_fingreso" value="<?= date("Y-m-d"); ?>" required>
          </div>

          <div class="col-md-8">
            <label class="fw-bold small text-muted text-uppercase">Número de Serie</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="fas fa-barcode text-primary"></i></span>
              <input type="text" class="form-control fw-bold" name="equipo_serie" id="equipo_serie" placeholder="V788Z07728" required>
            </div>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-4">
            <label class="fw-bold small text-muted text-uppercase">Modelo</label>
            <select class="form-select" name="equipo_modelo_id" required>
              <option value="" selected disabled>Seleccione Modelo</option>
              <?php
              $modelos = consultaData("SELECT modelo_id, modelo_linea, modelo_modelo FROM Modelos");
              foreach ($modelos['dataFetch'] as $mod) {
                echo "<option value='" . encryption($mod['modelo_id']) . "'>{$mod['modelo_linea']} | {$mod['modelo_modelo']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="fw-bold small text-muted text-uppercase">Proveedor</label>
            <select class="form-select" name="equipo_provE_id" required>
              <option value="" selected disabled>Seleccione Proveedor</option>
              <?php
              $provs = consultaData("SELECT provE_id, provE_nombre FROM ProveedoresE");
              foreach ($provs['dataFetch'] as $pv) {
                echo "<option value='" . encryption($pv['provE_id']) . "'>{$pv['provE_nombre']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="fw-bold small text-muted text-uppercase">Estado Inicial</label>
            <select class="form-select" name="equipo_estado" required>
              <option value="Espera">Espera</option>
              <option value="Reparacion">Reparación</option>
              <option value="Inhabilitado">Inhabilitado</option>
            </select>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
          <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">
              <i class="fas fa-fill-drip me-2"></i>Estado de Suministros (Niveles Iniciales)
            </h6>
          </div>
          <div class="card-body">
            <div class="row row-cols-1 row-cols-md-5 g-3 mb-4">

              <?php
              // Definimos la configuración para la generación dinámica
              // [Letra, Clase Color, ID Label, Name Nivel, Name Chip, Nombre Largo]
              $toners = [
                ['K', 'bg-dark', 'labelK_add', 'equipo_nivel_K', 'chip_k', 'NEGRO'],
                ['M', 'bg-danger', 'labelM_add', 'equipo_nivel_M', 'chip_m', 'MAGENTA'],
                ['C', 'bg-info', 'labelC_add', 'equipo_nivel_C', 'chip_c', 'CYAN'],
                ['Y', 'bg-warning', 'labelY_add', 'equipo_nivel_Y', 'chip_y', 'AMARILLO']
              ];

              foreach ($toners as $t):
                $letra      = $t[0];
                $colorClass = $t[1];
                $labelId    = $t[2];
                $nameNivel  = $t[3];
                $nameChip   = $t[4];
                $nombre     = $t[5];
              ?>
                <div class="col seccion-toner-<?= $letra ?>">
                  <div class="card border-dark shadow-sm h-100">
                    <div class="card-header <?= $colorClass ?> text-white text-center py-1 small fw-bold">
                      <?= $nombre ?> (<?= $letra ?>)
                    </div>
                    <div class="card-body p-2 text-center">
                      <label class="form-label mb-0 small fw-bold">Nivel
                        <span id="<?= $labelId ?>">100%</span>
                      </label>
                      <input
                        type="range"
                        name="<?= $nameNivel ?>"
                        class="form-range"
                        min="0"
                        max="100"
                        value="0"
                        oninput="updateRangeLabel(this, '<?= $labelId ?>')" />

                      <label class="d-block small fw-bold mb-1">¿Tiene Chip?</label>
                      <div class="btn-group btn-group-sm w-100" role="group">
                        <input
                          type="radio"
                          class="btn-check"
                          name="<?= $nameChip ?>"
                          id="<?= $nameChip ?>_si_add"
                          value="1"/>
                        <label class="btn btn-outline-success" for="<?= $nameChip ?>_si_add">SÍ</label>

                        <input
                          type="radio"
                          class="btn-check"
                          name="<?= $nameChip ?>"
                          id="<?= $nameChip ?>_no_add"
                          value="0" 
                          checked />
                        <label class="btn btn-outline-danger" for="<?= $nameChip ?>_no_add">NO</label>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>

              <div class="col" id="seccion-residual-add">
                <div class="card h-100 border-secondary shadow-sm">
                  <div class="card-header bg-secondary text-white text-center py-1 small fw-bold">
                    RESIDUAL (R)
                  </div>
                  <div class="card-body p-2 text-center">
                    <label class="form-label mb-0 small fw-bold">Nivel
                      <span id="labelR_add">0%</span>
                    </label>
                    <input
                      type="range"
                      name="equipo_nivel_R"
                      class="form-range"
                      min="0"
                      max="100"
                      value="0"
                      oninput="updateRangeLabel(this, 'labelR_add')" />
                    <div class="py-2 mt-2">
                      <small class="text-muted italic">No requiere chip</small>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="text-center mt-5">
          <button type="submit" class="btn btn-primary px-5 shadow">
            <i class="far fa-save me-2"></i> REGISTRAR EQUIPO
          </button>
        </div>
      </form>
    </div>
  </div>
</div>