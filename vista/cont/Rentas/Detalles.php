<?php
// --- LÓGICA DE PROCESAMIENTO ---

$renta_id = decryption($GLOBALS['pagina2']);
$query_base = consultaData("SELECT * FROM Rentas WHERE renta_id = '$renta_id'");

// Redirección si no existe la renta
if ($query_base['numRows'] == 0) {
  redirect(SERVERURL . "Rentas/Lista");
  exit();
}

$dataRenta = $query_base['dataFetch'][0];
$esActivo = ($dataRenta['renta_estado'] == "Activo");

// Construcción de consulta optimizada con JOINs según el estado
$sql = "SELECT R.*, Z.zona_nombre, C.contrato_folio, CL.cliente_rs, CL.cliente_rfc, CL.cliente_id ";
if ($esActivo) {
  $sql .= ", E.*, M.modelo_linea, M.modelo_modelo, M.modelo_tipo, M.modelo_wifi, M.modelo_resi ";
}
$sql .= "FROM Rentas R 
          INNER JOIN Zonas Z ON R.renta_zona_id = Z.zona_id 
          INNER JOIN Contratos C ON R.renta_contrato_id = C.contrato_id 
          INNER JOIN Clientes CL ON C.contrato_cliente_id = CL.cliente_id ";
if ($esActivo) {
  $sql .= "INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id 
            INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id ";
}
$sql .= "WHERE R.renta_id = '$renta_id'";

$result = consultaData($sql);
if ($result['numRows'] == 0) {
  redirect(SERVERURL . "Rentas/Lista");
  exit();
}

$row = $result['dataFetch'][0];

// Función auxiliar para valores predeterminados
function checkVal($valRaw, $sufijo = "")
{
  if ($sufijo != "") {
    $val = $sufijo . number_format($valRaw, 2);
  } else {
    $val = $valRaw;
  }
  return (empty($valRaw) || $valRaw == 0) ? "No Especificado" : $val;
}

$renta_telefono = !empty($row['renta_telefono']) ? $row['renta_telefono'] : "Sin Teléfono";
?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="/Rentas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <a class="active" class=""><i class="fas fa-info-circle fa-fw"></i> &nbsp; DETALLES</a>
    </li>
    <!-- <li>
      <a href="/Rentas/Contadores/<?= encryption($row['renta_id']) ?>"><i class="fas fa-print fa-fw"></i> &nbsp; CONTADORES</a>
    </li> -->
    <li>
      <a href="/Rentas/Lecturas/<?= encryption($row['renta_id']) ?>"><i class="fas fa-print fa-fw"></i> &nbsp; LECTURAS</a>
    </li>
    <li>
      <a href="/Rentas/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA</a>
    </li>
    <li>
      <a href="/Rentas/Otros"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; NO ACTIVOS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
      <h5 class="mb-0 text-uppercase fw-bold"><i class="fas fa-user-circle me-2 text-primary"></i> Información de Renta</h5>
      <div>
        <?php if ($row['renta_estado'] == "Cancelado"): ?>
          <span class="badge bg-danger p-2">RENTA CANCELADA</span>
        <?php else: ?>
          <button class="btn btn-outline-warning btn-sm fw-bold" id="btnEdit" value="<?= $GLOBALS['pagina2']; ?>">
            <i class="fas fa-edit"></i> EDITAR
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-body bg-light">
      <div class="row g-3 text-center">
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Fecha Inicio</small>
          <span class="fw-bold"><?= ucwords(dateFormat($row['renta_finicio'], "completa")); ?></span>
        </div>
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Tipo</small>
          <span class="fw-bold"><?= ucfirst($row['renta_tipo']); ?></span>
        </div>
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Estado</small>
          <span class="badge bg-success"><?= $row['renta_estado']; ?></span>
        </div>
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Zona</small>
          <span class="fw-bold"><?= ucwords($row['zona_nombre']); ?>
          </span><i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Folio</small>
          <span class="fw-bold text-primary"><?= $row['contrato_folio'] . "-" . $row['renta_folio']; ?></span>
        </div>
        <div class="col-6 col-md-2">
          <small class="text-muted d-block text-uppercase">Departamento</small>
          <span class="fw-bold"><?= ucwords($row['renta_depto']); ?></span>
        </div>
      </div>
      <hr>
      <div class="row align-items-center">
        <div class="col-md-4">
          <small class="text-muted d-block">RAZÓN SOCIAL</small>
          <h6 class="fw-bold"><?= ucwords($row['cliente_rs']); ?></h6>
        </div>
        <div class="col">
          <small class="text-muted d-block">RFC</small>
          <h6 class="fw-bold"><?= strtoupper($row['cliente_rfc']); ?></h6>
        </div>
        <div class="col">
          <small class="text-muted d-block">CONTACTO / TEL</small>
          <h6><?= ucwords($row['renta_contacto']); ?> | <span class="text-primary"><?= $renta_telefono; ?></span></h6>
        </div>
        <div class="col">
          <small class="text-muted-d-block">UBICACION</small>
          <h6 class="text-muted-d-block"><a href="https://google.com/maps/search/<?= $row['renta_coor'] ?>" class="badge bg-danger" target="_blank"><i class="fas fa-map-marker-alt"></i> <?= ucwords($row['renta_coor']); ?></a></h6>
        </div>
        <div class="col-md-2 text-end">
          <button class="btn btn-sm btn-info text-white" id="btnCliente" data-valor="<?= encryption($row['cliente_id']); ?>">
            <i class="fas fa-address-card"></i> Ficha
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
          <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i> Costos y Cuotas de Lecturas</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-dark small">
                <tr>
                  <th>MENSUALIDAD</th>
                  <th>CONCEPTO</th>
                  <th>INCLUIDO</th>
                  <th>EXCEDENTE</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td rowspan="3" class="h5 fw-bold text-primary"><?= checkVal($row['renta_costo'], "$"); ?></td>
                  <td class="fw-bold text-success">ESCANEOS</td>
                  <td><?= checkVal($row['renta_inc_esc']); ?></td>
                  <td><?= checkVal($row['renta_exc_esc'], "$"); ?></td>
                </tr>
                <tr>
                  <td class="fw-bold text-dark">B&N</td>
                  <td><?= checkVal($row['renta_inc_bn']); ?></td>
                  <td><?= checkVal($row['renta_exc_bn'], "$"); ?></td>
                </tr>
                  <tr>
                    <td class="fw-bold text-danger">COLOR</td>
                    <td><?= checkVal($row['renta_inc_col']); ?></td>
                    <td><?= checkVal($row['renta_exc_col'], "$"); ?></td>
                  </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($esActivo): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0"><i class="fas fa-print me-2"></i> Estado del Equipo y Tóner</h6>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-md-4 border-end">
                <h6 class="text-muted text-uppercase small">Equipo Asignado</h6>
                <p class="h5 fw-bold"><?= $row['modelo_linea'] . " " . $row['modelo_modelo']; ?></p>
                <a href="/Equipos/ID/<?= encryption($row['equipo_id']) ?>" class="btn btn-dark w-100" target="_blank">
                  <i class="fas fa-barcode"></i> SERIE: <?= $row['equipo_serie']; ?>
                </a>
              </div>

              <div class="col-md-8">
                <div class="row text-center">
                  <?php
                  $colores = [
                    'K' => ['bg' => 'bg-dark', 'label' => 'Negro (K)', 'nivel' => $row['equipo_nivel_K'], 'stock' => $row['renta_stock_K'], 'chip' => $row['chip_k']],
                    'M' => ['bg' => 'bg-danger', 'label' => 'Magenta (M)', 'nivel' => $row['equipo_nivel_M'], 'stock' => $row['renta_stock_M'], 'chip' => $row['chip_m']],
                    'C' => ['bg' => 'bg-info', 'label' => 'Cyan (C)', 'nivel' => $row['equipo_nivel_C'], 'stock' => $row['renta_stock_C'], 'chip' => $row['chip_c']],
                    'Y' => ['bg' => 'bg-warning', 'label' => 'Yellow (Y)', 'nivel' => $row['equipo_nivel_Y'], 'stock' => $row['renta_stock_Y'], 'chip' => $row['chip_y']]
                  ];

                  foreach ($colores as $key => $color):
                    if ($key !== 'K' && $row['modelo_tipo'] !== "Multicolor") continue;
                    $porcentaje = $color['nivel'];
                    $txtNivel = ($color['chip'] == 1) ? '<span class="badge ' . $color['bg'] . '"><i class="fas fa-microchip"></i></span> | ' : '<span class="badge bg-light"><i class="fas fa-microchip"></i></span> | ';
                    $txtNivel .= $color['nivel'] . "%";
                  ?>
                    <div class="col">
                      <small class="fw-bold"><?= $color['label']; ?></small>
                      <div class="progress my-2" style="height: 10px;">
                        <div class="progress-bar <?= $color['bg']; ?>" style="width: <?= $porcentaje; ?>%"></div>
                      </div>
                      <span class="small d-block"><?= $txtNivel; ?></span>
                      <span class="badge rounded-pill bg-light text-dark border">Stock: <?= $color['stock']; ?></span>
                    </div>
                  <?php endforeach; ?>

                  <?php if ($row['modelo_resi'] == 1) { ?>
                    <div class="col">
                      <small class="fw-bold">Residual (R)</small>
                      <div class="progress my-2" style="height: 10px;">
                        <div class="progress-bar bg-secondary" style="width: <?= $row['equipo_nivel_R']; ?>%"></div>
                      </div>
                      <span class="small d-block"><?= $row['equipo_nivel_R']; ?>%</span>
                      <span class="badge rounded-pill bg-light text-dark border">Stock: <?= $row['renta_stock_R'] ?></span>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>