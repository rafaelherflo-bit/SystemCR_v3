<?php
$QRY = consultaData("SELECT * FROM Clientes 
        INNER JOIN catRegimenFiscal ON Clientes.cliente_regFis_id = catRegimenFiscal.regFis_id 
        INNER JOIN catCFDI ON Clientes.cliente_cfdi_id = catCFDI.CFDI_id 
        WHERE cliente_id != 1");

?>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0 text-uppercase text-primary">
        <i class="fas fa-users me-2"></i> Directorio de Clientes
      </h4>
      <p class="text-muted small mb-0">Gestión de cartera, datos fiscales y estados de cuenta.</p>
    </div>

    <ul class="nav nav-pills small">
      <li class="nav-item">
        <a class="nav-link bg-light text-dark shadow-sm border mx-1" href="/Clientes/Fiscal">
          <i class="fas fa-file-invoice me-2 text-danger"></i> CONSTANCIA
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-light text-dark shadow-sm border mx-1" href="/Clientes/Agregar">
          <i class="fas fa-plus me-1 text-primary"></i> AGREGAR CLIENTE
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active bg-primary shadow-sm mx-1 fw-bold" href="#">
          <i class="fas fa-list me-1"></i> LISTA DE CLIENTES
        </a>
      </li>
    </ul>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="dataTable table-sm table-hover table table-secondary">
          <thead class="table-dark">
            <tr>
              <th style="text-align: center;" class="ps-3">Activos / Estado</th>
              <th style="text-align: center;">Emisor</th>
              <th style="text-align: center;">Razón Social / RFC</th>
              <th style="text-align: center;">Contacto y Redes</th>
              <th style="text-align: center;" class="text-center">C.P.</th>
              <th style="text-align: center;">Régimen Fiscal</th>
              <th style="text-align: center; min-width: 120px;">Uso de CFDI</th>
              <th style="text-align: center;" class="text-center pe-3">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($QRY['numRows'] != 0) {
              foreach ($QRY['dataFetch'] as $row) {
                // Limpieza de datos
                $cliente_contacto = ($row['cliente_contacto'] == "0") ? "N/A" : $row['cliente_contacto'];
                $cliente_telefono = ($row['cliente_telefono'] == "0") ? "" : $row['cliente_telefono'];
                $cliente_correo   = ($row['cliente_correo'] == "0")   ? "" : $row['cliente_correo'];

                // $cliente_id   = $row['cliente_id'];
                // $cliente_tipo = (strlen($row['cliente_rfc']) === 13) ? "Fisica" : "Moral";

                // $SQLupdateTipo = "UPDATE Clientes SET cliente_tipo = '$cliente_tipo' WHERE cliente_id = $cliente_id";
                // sentenciaData($SQLupdateTipo);

                // Lógica de Activos
                // 1. Contar solo Contratos con estado "Activo"
                $sqlContratos = "SELECT * FROM Contratos 
                                  WHERE contrato_cliente_id = '" . $row['cliente_id'] . "' 
                                  AND contrato_estado = 'Activo'";
                $noContrs = consultaData($sqlContratos)['numRows'];

                if ($noContrs == 0) {
                  $activos = '<span class="badge bg-light text-muted border">Sin Contratos Activos</span>';
                } else {
                  // 2. Contar solo Rentas con estado "Activo" vinculadas a ese cliente
                  $sqlRentas = "SELECT * FROM Rentas 
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id 
                  WHERE Contratos.contrato_cliente_id = '" . $row['cliente_id'] . "' 
                  AND Rentas.renta_estado = 'Activo'";
                  $noRents = consultaData($sqlRentas)['numRows'];

                  // Construcción visual del indicador
                  $activos = '<div class="small fw-bold text-primary">Contratos: ' . $noContrs . '</div>';

                  if ($noRents == 0) {
                    $activos .= '<span class="badge bg-warning text-dark" style="font-size: 0.65rem;">SIN RENTAS ACTIVAS</span>';
                  } else {
                    $activos .= '<div class="small text-success fw-bold">Rentas: ' . $noRents . '</div>';
                  }
                }
            ?>
                <tr>
                  <td class="ps-3"><?= $activos; ?></td>
                  <td>
                    <span class="badge <?= ($row['cliente_emiFact'] == 1) ? 'bg-info' : 'bg-secondary'; ?> small">
                      <?= ($row['cliente_emiFact'] == 1) ? 'RENAN' : 'MIMI'; ?>
                    </span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark text-uppercase"><?= $row['cliente_rs']; ?></div>
                    <small class="text-muted fw-bold"><?= $row['cliente_rfc']; ?></small>
                    <br>
                    <small class="text-muted fw-bold"><?= $row['cliente_tipo']; ?></small>
                  </td>
                  <td>
                    <div class="small"><i class="fas fa-user-circle me-1 text-muted"></i> <?= $cliente_contacto; ?></div>
                    <div class="small"><i class="fas fa-phone-alt me-1 text-muted"></i> <?= $cliente_telefono; ?></div>
                    <div class="small text-lowercase text-primary"><?= $cliente_correo; ?></div>
                  </td>
                  <td class="text-center fw-bold text-muted" style="min-width: 100px;"><?= $row['cliente_cp']; ?></td>
                  <td>
                    <div class="fw-bold" style="font-size: 0.75rem;"><?= $row['regFis_codigo']; ?></div>
                    <div class="text-muted italic" style="font-size: 0.7rem; line-height: 1;"><?= $row['regFis_descripcion']; ?></div>
                  </td>
                  <td>
                    <div class="fw-bold" style="font-size: 0.75rem;"><?= $row['CFDI_codigo']; ?></div>
                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1;"><?= $row['CFDI_descripcion']; ?></div>
                  </td>
                  <td style="min-width: 100px;" class="text-center pe-3">
                    <a class="btn btn-warning btn-sm border-0 shadow-sm" href="<?= SERVERURL . "Clientes/Editar/" . encryption($row['cliente_id']); ?>">
                      <i class="fas fa-pen"></i>
                    </a>
                    <a class="btn btn-dark btn-sm border-0 shadow-sm" href="#">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
            <?php
              }
            } else {
              echo '<tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron clientes registrados.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>