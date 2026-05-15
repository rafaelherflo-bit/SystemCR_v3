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

  // 2. Intentamos buscar la configuración WiFi
  $sqlWifi = "SELECT * FROM equipos_wifi WHERE equWifi_equipo_id = '$equipoId'";
  $resWifi = consultaData($sqlWifi);

  $existeConfig = ($resWifi['numRows'] == 1);
  $DataWifi = ($existeConfig) ? $resWifi['dataFetch'][0] : [
    'equWifi_SSID' => '',
    'equWifi_WPA'  => '',
    'equWifi_IP'   => '',
    'equWifi_MASK' => '',
    'equWifi_PE'   => ''
  ];
?>

  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-baseline mb-4">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase text-primary">
          <i class="fas fa-wifi me-2"></i> Configuración Inalámbrica
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
        <li class="nav-item">
          <a class="nav-link active bg-primary shadow-sm mx-1 fw-bold text-white" href="#">
            <i class="fas fa-wifi me-1"></i> WIFI
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Ethernet/' . $pagina[2]; ?>">
            <i class="fas fa-ethernet"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Contabilidad/' . $pagina[2]; ?>">
            <i class="fas fa-calculator"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link bg-light text-dark shadow-sm mx-1" href="<?= SERVERURL . 'Equipos/Contactos/' . $pagina[2]; ?>">
            <i class="fas fa-users"></i>
          </a>
        </li>
      </ul>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0 text-dark">
          <i class="fas fa-broadcast-tower me-2 text-primary"></i> Gestión de Red WLAN
        </h5>
        <hr class="mb-0">
      </div>
      <div class="card-body p-4">
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="<?= ($existeConfig) ? 'update' : 'save'; ?>" autocomplete="off">
          <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
          <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
          <input type="hidden" name="configuracionWIFI" value="<?= $pagina[2]; ?>">

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Nombre de la Red (SSID)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-signal text-primary"></i></span>
                  <input type="text" class="form-control" name="equWifi_SSID" value="<?= $DataWifi['equWifi_SSID']; ?>" maxlength="255" required placeholder="Ej: Oficina_Principal">
                </div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Contraseña WiFi (WPA)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="fas fa-key text-primary"></i></span>
                  <input type="text" class="form-control" name="equWifi_WPA" value="<?= $DataWifi['equWifi_WPA']; ?>" maxlength="255" placeholder="Contraseña de red">
                </div>
              </div>
            </div>

            <div class="col-12 col-md-4 mt-3">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Dirección IP</label>
                <input type="text" class="form-control" name="equWifi_IP" value="<?= $DataWifi['equWifi_IP']; ?>" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" placeholder="192.168.1.50">
              </div>
            </div>
            <div class="col-12 col-md-4 mt-3">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Máscara de Red</label>
                <input type="text" class="form-control" name="equWifi_MASK" value="<?= $DataWifi['equWifi_MASK']; ?>" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" placeholder="255.255.255.0">
              </div>
            </div>
            <div class="col-12 col-md-4 mt-3">
              <div class="form-group">
                <label class="fw-bold small text-muted text-uppercase mb-1">Puerta de Enlace</label>
                <input type="text" class="form-control" name="equWifi_PE" value="<?= $DataWifi['equWifi_PE']; ?>" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" placeholder="192.168.1.1">
              </div>
            </div>
          </div>

          <div class="text-center mt-5">
            <button type="submit" class="btn btn-<?= ($existeConfig) ? 'success' : 'primary text-white'; ?> px-4 fw-bold shadow-sm">
              <i class="far fa-save me-2"></i> <?= ($existeConfig) ? 'ACTUALIZAR CONFIGURACIÓN' : 'GUARDAR CONFIGURACIÓN'; ?>
            </button>

            <?php if ($existeConfig): ?>
              <button type="button" class="btn btn-outline-danger ms-2 px-4 btn-delRegWithID"
                data-table="equipos_wifi"
                data-colname="equWifi_equipo_id"
                data-value="<?= $pagina[2]; ?>">
                <i class="fas fa-trash-alt me-2"></i> ELIMINAR
              </button>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>