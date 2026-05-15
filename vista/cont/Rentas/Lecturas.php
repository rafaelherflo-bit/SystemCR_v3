<?php
$rentaIDdec = decryption($GLOBALS['pagina2']);

// 1. Obtener datos base de la Renta
$sqlCheckRenta = "SELECT R.*, Co.contrato_folio, Cl.cliente_rs, Cl.cliente_rfc, E.equipo_serie, M.modelo_linea, M.modelo_modelo 
                  FROM Rentas R
                  INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id
                  INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id
                  LEFT JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
                  LEFT JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
                  WHERE R.renta_id = '$rentaIDdec'";
$qryCheckRenta = consultaData($sqlCheckRenta);

if ($qryCheckRenta['numRows'] == 0) {
  redirect(SERVERURL . "Rentas/Lista");
}

$rentaData = $qryCheckRenta['dataFetch'][0];

// 2. Obtener TODAS las lecturas de esta renta (Sin Union)
$sqlLecturas = "SELECT L.*, E.equipo_serie, M.modelo_linea, M.modelo_modelo
                FROM Lecturas L
                INNER JOIN Equipos E ON L.lectura_equipo_id = E.equipo_id
                INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
                WHERE L.lectura_renta_id = '$rentaIDdec'
                ORDER BY L.lectura_fecha DESC";
$resLecturas = consultaData($sqlLecturas);

// Mapeamos las lecturas por mes para fácil acceso
$lecturasMap = [];
foreach ($resLecturas['dataFetch'] as $l) {
  $mesKey = date("Y-m", strtotime($l['lectura_fecha']));
  $lecturasMap[$mesKey][] = $l;
}

// 3. Generar el periodo de tiempo
$fechaInicio = new DateTime($rentaData['renta_finicio']);
$fechaFin = new DateTime();
$intervalo = new DateInterval('P1M');
$periodo = new DatePeriod($fechaInicio, $intervalo, $fechaFin);

$periodosInvertidos = array_reverse(iterator_to_array($periodo));
?>
<div class="container-fluid mb-4">

  <div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
      <li>
        <a href="/Rentas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
      </li>
      <li>
        <a href="/Rentas/Detalles/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-info-circle fa-fw"></i> &nbsp; DETALLES</a>
      </li>
      <li>
        <a href="/Rentas/Editar/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-info-circle fa-fw"></i> &nbsp; EDITAR</a>
      </li>
      <!-- <li>
        <a href="/Rentas/Contadores/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-print fa-fw"></i> &nbsp; CONTADORES</a>
      </li> -->
      <li>
        <a class="active" href="/Rentas/Lecturas/<?= $GLOBALS['pagina2'] ?>"><i class="fas fa-print fa-fw"></i> &nbsp; LECTURAS</a>
      </li>
      <li>
        <a href="/Rentas/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA</a>
      </li>
      <li>
        <a href="/Rentas/Otros"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; NO ACTIVOS</a>
      </li>
    </ul>
  </div>

  <h1 class="h3 mb-2 text-gray-800">Historial de Vida de la Renta</h1>
  <p class="mb-4 text-uppercase">
    Cliente: <strong><?= $rentaData['cliente_rfc']; ?></strong> | <?= $rentaData['cliente_rs']; ?> | Depto: <?= $rentaData['renta_depto']; ?>
  </p>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm">
          <thead class="table-dark">
            <tr>
              <th>Fecha</th>
              <th>Equipo / Movimiento</th>
              <th>B&N</th>
              <th>Color</th>
              <th>Escaneo</th>
              <th>Evidencia</th>
            </tr>
          </thead>
          <tbody>
            <?php
            setlocale(LC_TIME, 'es_ES.UTF-8', 'esp');

            foreach ($periodosInvertidos as $fechaObj) {
              $mesKey = $fechaObj->format("Y-m");
              $nombreMes = strftime("%B %Y", $fechaObj->getTimestamp());

              $inicioMes = $fechaObj->format("Y-m-01");
              $finMes = $fechaObj->format("Y-m-t");

              // BUSCAMOS SI HUBO CAMBIOS EN ESTE MES
              $sqlCambios = "SELECT * FROM Cambios
                              WHERE cambio_renta_id = '$rentaIDdec'
                              AND cambio_fecha BETWEEN '$inicioMes' AND '$finMes' LIMIT 1";
              $resCambios = consultaData($sqlCambios);
              $tieneCambio = ($resCambios['numRows'] > 0);
              $datosCambio = $tieneCambio ? $resCambios['dataFetch'][0] : null;

              // Si hay lecturas en este mes
              if (isset($lecturasMap[$mesKey])) {
                foreach ($lecturasMap[$mesKey] as $row) {
                  $lecturaID_enc = encryption($row['lectura_id']);
            ?>
                  <tr>
                    <td><span class="font-weight-bold"><?= date("d/m/Y", strtotime($row['lectura_fecha'])) ?></span></td>
                    <td>
                      <strong><?= $row['modelo_modelo'] ?></strong><br>
                      <small class="text-muted">S/N: <?= $row['equipo_serie'] ?></small>
                    </td>
                    <td class="font-weight-bold"><?= number_format($row['lectura_bn']) ?></td>
                    <td><?= number_format($row['lectura_col']) ?></td>
                    <td><?= number_format($row['lectura_esc']) ?></td>
                    <td>
                      <div class="btn-group" role="group">
                        <button type="button"
                          class="btn btn-info btn-sm btn-lect"
                          data-tipo="lectura"
                          data-lectura="<?= encryption($row['lectura_id']) ?>"
                          data-rentaid="<?= encryption($rentaIDdec) ?>"
                          data-anio="<?= explode('-', $row['lectura_fecha'])[0] ?>"
                          data-mes="<?= explode('-', $row['lectura_fecha'])[1] ?>"
                          title="Ver Lectura">
                          <i class="fas fa-print"></i> Lectura
                        </button>

                        <?php if ($tieneCambio) {
                          // Construimos la ruta del PDF de cambio basado en tu estructura
                          // http://localhost/DocsCR/CambiosDeEquipos/AÑO/MES/FOLIO.pdf
                          $anioC = date("Y", strtotime($datosCambio['cambio_fecha']));
                          $mesC = date("m", strtotime($datosCambio['cambio_fecha']));
                          $folioC = $datosCambio['cambio_folio'];
                          $urlCambio = SERVERURL . "DocsCR/CambiosDeEquipos/$anioC/$mesC/$folioC.pdf";
                        ?>
                          <button type="button"
                            class="btn btn-dark btn-sm btn-lect"
                            data-tipo="cambio"
                            data-url="<?= $urlCambio ?>"
                            title="Ver Cambio de Equipo">
                            <i class="fas fa-exchange-alt"></i> Cambio
                          </button>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr class="table-light">
                  <td class="text-muted small"><?= $nombreMes ?></td>
                  <td colspan="5" class="text-center text-danger font-italic small">
                    <i class="fas fa-exclamation-triangle"></i> LECTURA FALTANTE
                  </td>
                </tr>
            <?php }
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>