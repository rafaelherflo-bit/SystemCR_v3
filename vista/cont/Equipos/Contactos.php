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

  $sqlC = "SELECT * FROM equipos_contactos WHERE equCon_equipo_id = '$equipoId' ORDER BY equCon_nombre ASC";
  $resC = consultaData($sqlC);
?>

  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-baseline mb-4">
      <div>
        <h4 class="fw-bold mb-0 text-uppercase" style="color: #6610f2;">
          <i class="fas fa-users me-2"></i> Agenda de Contactos
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
          <a class="nav-link bg-light text-dark shadow-sm" href="<?= SERVERURL . 'Equipos/Contabilidad/' . $pagina[2]; ?>">
            <i class="fas fa-calculator"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link active shadow-sm mx-1 fw-bold" style="background-color: #6610f2;" href="#">
            <i class="fas fa-address-book me-1"></i> CONTACTOS
          </a>
        </li>
      </ul>
    </div>

    <div class="card border-0 shadow-sm mb-5">
      <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" id="form-title"><i class="fas fa-user-plus me-2 text-indigo"></i> AGREGAR CONTACTO DE ESCANEO</h5>
        <hr class="mb-0">
      </div>
      <div class="card-body p-4">
        <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
          <input type="hidden" name="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
          <input type="hidden" name="clave_admin" value="<?= $_SESSION['passclave']; ?>">
          <input type="hidden" id="frmEquipoContacto" name="equipoContactoAdd" value="<?= $pagina[2]; ?>">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Nombre (Display)</label>
              <input type="text" class="form-control" id="equCon_nombre" name="equCon_nombre" maxlength="150" required autofocus>
            </div>
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Correo Electrónico</label>
              <input type="email" class="form-control" id="equCon_correo" name="equCon_correo" placeholder="ejemplo@correo.com">
            </div>
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Hostname / IP</label>
              <input type="text" class="form-control" id="equCon_host" name="equCon_host" placeholder="DESKTOP-PC o 192.168.1.10">
            </div>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Ruta Compartida</label>
              <input type="text" class="form-control" id="equCon_ruta" name="equCon_ruta" placeholder="Carpeta (ej: Scans)">
            </div>
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Usuario Host</label>
              <input type="text" class="form-control" id="equCon_usuario" name="equCon_usuario">
            </div>
            <div class="col-md-4">
              <label class="fw-bold small text-muted text-uppercase">Contraseña Host</label>
              <input type="text" class="form-control" id="equCon_clave" name="equCon_clave">
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm" id="btn-submit">
              <i class="fas fa-save me-2"></i> REGISTRAR CONTACTO
            </button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($resC['numRows'] > 0): ?>
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr class="text-muted small">
                  <th class="ps-4">CONTACTO</th>
                  <th>CORREO</th>
                  <th>RUTA / SMB</th>
                  <th>CREDENCIALES</th>
                  <th class="text-center">ACCIONES</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($resC['dataFetch'] as $con): ?>
                  <tr>
                    <td class="ps-4">
                      <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-indigo-light text-indigo rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:35px; height:35px; background:#e0d4f7; color:#6610f2;">
                          <i class="fas fa-user small"></i>
                        </div>
                        <span class="fw-bold text-dark"><?= $con['equCon_nombre']; ?></span>
                      </div>
                    </td>
                    <td><span class="small"><?= $con['equCon_correo'] ?: '---'; ?></span></td>
                    <td>
                      <code class="small text-primary">
                        <?= ($con['equCon_host'] == "") ? 'N/A' : "\\\\" . $con['equCon_host'] . "\\" . $con['equCon_ruta']; ?>
                      </code>
                    </td>
                    <td>
                      <div class="small">
                        <i class="fas fa-user-circle text-muted"></i> <?= $con['equCon_usuario'] ?: '---'; ?><br>
                        <i class="fas fa-key text-muted"></i> <?= $con['equCon_clave'] ?: '---'; ?>
                      </div>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-outline-warning border-0 btn-editEquContacto" data-value="<?= encryption($con['equCon_id']); ?>">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger border-0 btn-delRegWithID" data-table="equipos_contactos" data-colname="equCon_id" data-value="<?= encryption($con['equCon_id']); ?>">
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
<?php } ?>