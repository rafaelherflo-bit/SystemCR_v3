<?php
$equipoId = decryption($pagina[2]);

$SQL = "SELECT * FROM Equipos E 
          INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id 
          WHERE E.equipo_id = '$equipoId'";
$QRY = consultaData($SQL);

if ($QRY['numRows'] != 1) {
  require_once SERVERDIR . "vista/cont/404.php";
} else {
  $Data = $QRY['dataFetch'][0];

  $sqlEther = "SELECT * FROM equipos_ether WHERE equEther_equipo_id = '$equipoId'";
  $resEther = consultaData($sqlEther);

  $existeConfig = ($resEther['numRows'] == 1);
  $DataEther = ($existeConfig) ? $resEther['dataFetch'][0] : [
    'equEther_IP'   => '',
    'equEther_MASK' => '',
    'equEther_PE'   => ''
  ];
?>

  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-baseline mb-4">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase text-primary">
          <i class="fas fa-ethernet me-2"></i> Configuración de Red
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
          <a class="nav-link active bg-primary shadow-sm mx-1 fw-bold" href="#">
            <i class="fas fa-network-wired me-1"></i> ETHERNET
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
      </ul>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-network-wired me-2 text-primary"></i> Parámetros de Red</h5>
        <hr class="mb-0">
      </div>
      <div class="card-body p-4">
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="<?= ($existeConfig) ? 'update' : 'save'; ?>" autocomplete="off">
          <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
          <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
          <input type="hidden" name="configuracionEthernet" value="<?= $pagina[2]; ?>">

          <div class="row g-4">
            <div class="col-12 col-md-4">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Dirección IP</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-primary"></i></span>
                  <input type="text" class="form-control" name="equEther_IP"
                    value="<?= $DataEther['equEther_IP']; ?>"
                    pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$"
                    placeholder="Ej: 192.168.1.50">
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Máscara de Red</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-mask text-primary"></i></span>
                  <input type="text" class="form-control" name="equEther_MASK"
                    value="<?= $DataEther['equEther_MASK']; ?>"
                    pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$"
                    placeholder="Ej: 255.255.255.0">
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Puerta de Enlace</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-door-open text-primary"></i></span>
                  <input type="text" class="form-control" name="equEther_PE"
                    value="<?= $DataEther['equEther_PE']; ?>"
                    pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$"
                    placeholder="Ej: 192.168.1.1">
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-<?= ($existeConfig) ? 'success' : 'primary'; ?> px-4 shadow-sm fw-bold">
                <i class="far fa-save me-2"></i> <?= ($existeConfig) ? 'ACTUALIZAR CONFIGURACIÓN' : 'GUARDAR CONFIGURACIÓN'; ?>
              </button>

              <?php if ($existeConfig): ?>
                <button type="button" class="btn btn-outline-danger ms-2 px-4 btn-delRegWithID"
                  data-table="equipos_ether"
                  data-colname="equEther_equipo_id"
                  data-value="<?= $pagina[2]; ?>">
                  <i class="fas fa-trash-alt me-2"></i> ELIMINAR
                </button>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>