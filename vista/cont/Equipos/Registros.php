<?php

/**
 * 1. CONSULTA OPTIMIZADA
 * Usamos LEFT JOIN para traer los datos de renta (si existen) en una sola petición.
 * Si el equipo no está rentado, los campos de renta vendrán como NULL.
 */
$SQL = "SELECT * FROM Equipos E
              INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
              INNER JOIN ProveedoresE P ON E.equipo_provE_id = P.provE_id
              LEFT JOIN Rentas R ON E.equipo_id = R.renta_equipo_id
              LEFT JOIN Contratos C ON R.renta_contrato_id = C.contrato_id
              LEFT JOIN Clientes CL ON C.contrato_cliente_id = CL.cliente_id";

$QRY = consultaData($SQL);

/**
 * 2. LÓGICA DE CONTEO Y CONFIGURACIÓN
 */
$conteoEstados = [
  "Rentado"      => 0,
  "Espera"       => 0,
  "Reparacion"   => 0,
  "Desmantelado" => 0,
  "Vendido"      => 0,
];

// Mapeo de clases CSS para las filas de la tabla
$clasesFila = [
  "Rentado"       => "table-success",
  "Espera"       => "table-primary",
  "Reparacion"   => "table-warning",
  "Desmantelado" => "table-danger",
  "Vendido"      => "table-dark"
];

foreach ($QRY['dataFetch'] as $equipo) {
  $est = $equipo['equipo_estado'];
  if (isset($conteoEstados[$est])) $conteoEstados[$est]++;
}
?>

<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li><a href="<?= SERVERURL . 'Equipos/Agregar'; ?>"><i class="fas fa-plus fa-fw"></i>&nbsp;AGREGAR EQUIPO</a></li>
    <li><a class="active"><i class="fas fa-clipboard-list fa-fw"></i>&nbsp;LISTA DE EQUIPOS</a></li>
    <!-- <li><a href="<?= SERVERURL . 'Equipos/Historial'; ?>"><i class="fas fa-clipboard-list fa-fw"></i>&nbsp;AGREGAR EQUIPO</a></li> -->
  </ul>
</div>

<?php if ($QRY['numRows'] > 0) { ?>
  <div class="container-fluid mb-4">
    <div class="row text-center">
      <?php foreach ($conteoEstados as $nombre => $total) { ?>
        <div class="col border-end">
          <small class="text-muted d-block"><?= strtoupper($nombre) ?></small>
          <span class="h5"><?= $total ?></span>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="container-fluid table-responsive">
    <table class="dataTable table table-sm table-hover table-secondary table-striped-columns">
      <thead class="table-dark">
        <tr>
          <th>INGRESO</th>
          <th>EQUIPO</th>
          <th>NIVELES</th>
          <th>ESTADO / DETALLES</th>
          <th>ACCIONES</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($QRY['dataFetch'] as $row) {
          $estadoActual = $row['equipo_estado'];
          $claseTr = $clasesFila[$estadoActual] ?? "";
          $nivel_K = $row['equipo_nivel_K'] < 0 ? "Sin Chip" : $row['equipo_nivel_K'] . "%";
          $nivel_M = $row['equipo_nivel_M'] < 0 ? "Sin Chip" : $row['equipo_nivel_M'] . "%";
          $nivel_C = $row['equipo_nivel_C'] < 0 ? "Sin Chip" : $row['equipo_nivel_C'] . "%";
          $nivel_Y = $row['equipo_nivel_Y'] < 0 ? "Sin Chip" : $row['equipo_nivel_Y'] . "%";
        ?>
          <tr class="<?= $claseTr ?>">
            <td class="small">
              <strong>FECHA:</strong> <?= dateFormat($row['equipo_fingreso'], 'completa') ?><br>
              <strong>PROV:</strong> <?= $row['provE_nombre'] ?>
            </td>

            <td class="small">
              <strong><?= "{$row['modelo_linea']}-{$row['modelo_modelo']}" ?></strong><br>
              <span class="text-muted"><?= "S: {$row['equipo_serie']} | C: {$row['equipo_codigo']}" ?></span>
            </td>

            <td class="small text-nowrap">
              <span class="badge bg-dark"><?= $nivel_K ?></span>
              <?php if ($row['modelo_tipo'] == "Multicolor") { ?>
                <span class="badge bg-info"><?= $nivel_C ?></span><br>
                <span class="badge bg-danger"><?= $nivel_M ?></span>
                <span class="badge bg-warning text-dark"><?= $nivel_Y ?></span>
              <?php }
              if ($row['modelo_resi'] == "1") { ?>
                <span class="badge bg-secondary">R:<?= $row['equipo_nivel_R'] ?>%</span>
              <?php } ?>
            </td>

            <td class="small">
              <?php if ($estadoActual == "Rentado") { ?>
                <?php if ($row['renta_folio']) { ?>
                  <b class="text-success text-uppercase">Rentado</b><br>
                  <?= $row['contrato_folio'] . "-" . $row['renta_folio'] . " | " . $row['cliente_rs'] . " (" . $row['renta_depto'] . ")" ?>
                <?php } else { ?>
                  <b class="text-danger small">⚠️ ERROR DE ESTADO: SIN RENTA ASIGNADA ⚠️</b>
                <?php } ?>
              <?php } else { ?>
                <span class="badge bg-secondary"><?= $estadoActual ?></span>
              <?php } ?>
            </td>

            <td>
              <a class="btn btn-warning btn-sm" href="<?= SERVERURL . $pagina[0] . "/ID/" . encryption($row['equipo_id']); ?>">
                <i class="fas fa-edit"></i>
              </a>
              <a class="btn btn-warning btn-sm" href="<?= SERVERURL . $pagina[0] . "/Registros/" . encryption($row['equipo_id']); ?>">
                <i class="fas fa-edit"></i>
              </a>
              <a class="btn btn-warning btn-sm" href="<?= SERVERURL . $pagina[0] . "/Niveles/" . encryption($row['equipo_id']); ?>">
                <i class="fas fa-edit"></i>
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>