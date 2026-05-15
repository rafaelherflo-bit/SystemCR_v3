<?php
// ===================================================================
// 1. PREPARACIÓN Y CÁLCULO DE FECHAS
// Este bloque inicializa las variables de tiempo (año, mes, fecha actual y anterior)
// que serán usadas en las consultas SQL y en la lógica de cálculo de lecturas.
// ===================================================================

// Se extraen el año y el mes del array $GLOBALS['contenido#']e asume que es el input de la URL o formulario).
// Se asegura que los valores sean tratados como enteros (casting a int) para seguridad en la BD.
$anio = (int)$GLOBALS['contenido1'];
$mes = (int)$GLOBALS['contenido2'];

// Se calcula el último día del mes y se construye la fecha actual en formato YYYY-MM-DD.
// Esta fecha ($currFecha) se usa para filtrar rentas cuya fecha de inicio sea menor o igual (activas).
$diaFinal = (int)diasDelMes($anio, $mes); // Función externa para obtener el último día del mes.
$currFecha = "$anio-$mes-$diaFinal";

// Formatea el período (Mes y Año) para ser mostrado en el encabezado HTML.
$periodoFormateado = ucfirst(dateFormat($currFecha, "mesanio"));
$encabezadoPeriodo = strtoupper("DE " . $periodoFormateado);

// Cálculo del mes anterior (simplificado y más robusto)
// Lógica para retroceder un mes, manejando correctamente el cambio de año (diciembre a enero).
$mesAnt = ($mes == 1) ? 12 : $mes - 1; // Si es enero (1), el mes anterior es 12. Si no, es mes actual - 1.
$anioAnt = ($mes == 1) ? $anio - 1 : $anio; // Si cambiamos a diciembre (12), el año también retrocede.

// ===================================================================
// 2. OBTENCIÓN DE DATOS CENTRALIZADA (Optimización de Consultas)
// El objetivo de este bloque es reducir la cantidad de consultas SQL que se ejecutan
// dentro del bucle principal de la tabla (un "N+1 query problem"), mejorando la performance.
// ===================================================================

// --- 2.1 Obtener TODAS las zonas de una sola vez ---
$queryZonas = consultaData("SELECT * FROM Zonas");
$zonasData = $queryZonas['dataFetch'] ?? [];
$zonasIDs = array_column($zonasData, 'zona_id'); // No usado directamente, pero útil para chequeos.

// Se crea un array asociativo (Map/Diccionario) con zona_id como clave y zona_nombre como valor.
// Esto permite buscar el nombre de la zona instantáneamente por ID dentro del bucle de la tabla.
$zonasNombres = array_column($zonasData, 'zona_nombre', 'zona_id');


// --- 2.2 Obtener TODAS las Rentas Activas y sus lecturas (Consulta ÚNICA) ---
// Consulta SQL compleja que trae todos los datos necesarios (Cliente, Contrato, Equipo, Consumibles,
// Lectura Actual y Lectura Anterior) en una sola llamada a la base de datos.
$sqlRentasOptimizada = "
    SELECT 
        Cl.cliente_id, Cl.cliente_rs, Cl.cliente_rfc, R.renta_id, R.renta_estado, R.renta_depto, R.renta_coor, R.renta_finicio, R.renta_folio, R.renta_zona_id, 
        Co.contrato_folio, Mo.modelo_linea, Mo.modelo_modelo, Mo.modelo_tipo, Mo.modelo_resi, Eq.equipo_id, Eq.equipo_serie, Eq.equipo_codigo,
        R.renta_stock_K, R.renta_stock_M, R.renta_stock_C, R.renta_stock_Y, R.renta_stock_R,
        Eq.equipo_nivel_K, Eq.equipo_nivel_M, Eq.equipo_nivel_C, Eq.equipo_nivel_Y, Eq.equipo_nivel_R,
        Eq.chip_k, Eq.chip_m, Eq.chip_c, Eq.chip_y,
        
        -- Lectura Actual
        LA.lectura_id AS lectura_id_act, LA.lectura_fecha AS lectura_fecha_act, 
        LA.lectura_esc AS lectura_esc_act, LA.lectura_bn AS lectura_bn_act, 
        LA.lectura_col AS lectura_col_act, LA.lectura_equipo_id AS lectura_equipo_id,

        -- Lectura Anterior
        LANT.lectura_id AS lectura_id_ant, LANT.lectura_fecha AS lectura_fecha_ant, 
        LANT.lectura_esc AS lectura_esc_ant, LANT.lectura_bn AS lectura_bn_ant, 
        LANT.lectura_col AS lectura_col_ant
        
    FROM Rentas R
    INNER JOIN Equipos Eq ON R.renta_equipo_id = Eq.equipo_id
    INNER JOIN Modelos Mo ON Eq.equipo_modelo_id = Mo.modelo_id
    INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id
    INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id
    
    -- LEFT JOIN para Lectura Actual (puede no existir)
    LEFT JOIN Lecturas LA ON LA.lectura_renta_id = R.renta_id 
        AND MONTH(LA.lectura_fecha) = $mes AND YEAR(LA.lectura_fecha) = $anio
    
    -- LEFT JOIN para Lectura Anterior (puede no existir)
    LEFT JOIN Lecturas LANT ON LANT.lectura_renta_id = R.renta_id 
        AND MONTH(LANT.lectura_fecha) = $mesAnt AND YEAR(LANT.lectura_fecha) = $anioAnt

    WHERE R.renta_estado = 'Activo'
        AND R.renta_finicio <= '$currFecha'
    
    ORDER BY R.renta_zona_id, Cl.cliente_rs, R.renta_folio
";

// Ejecuta la consulta optimizada y almacena todos los resultados en $rentasActivas.
$rentasActivas = consultaData($sqlRentasOptimizada)['dataFetch'] ?? [];


// --- 2.3 Pre-calcular el estado de lecturas por zona para la sección de resumen ---
// Este bloque recorre los datos obtenidos UNA SOLA VEZ para construir un resumen rápido
// que se muestra en la parte superior de la página (e.g., "Zona 1: 5/10 leídas").
$resumenZonas = [];
// Inicializa el contador de rentas y lecturas a cero para cada zona.
foreach ($zonasData as $zona) {
  $resumenZonas[$zona['zona_id']] = ['rentas' => 0, 'lecturas' => 0];
}

// Recorre todas las rentas activas obtenidas.
foreach ($rentasActivas as $renta) {
  $zonaId = $renta['renta_zona_id'];

  // Incrementa el contador total de rentas en la zona.
  if (isset($resumenZonas[$zonaId])) {
    $resumenZonas[$zonaId]['rentas']++;

    // Si 'lectura_id_act' NO es NULL, significa que el LEFT JOIN encontró una lectura para el periodo actual.
    if ($renta['lectura_id_act'] != NULL) {
      $resumenZonas[$zonaId]['lecturas']++; // Incrementa el contador de lecturas realizadas.
    }
  }
}


// --- 2.4 Cache de datos de equipos para las lecturas pasadas (evita consulta repetitiva) ---
// Función para obtener los detalles de un equipo (modelo, serie) si la lectura se hizo
// con un equipo diferente al de la renta principal (lectura_equipo_id).
// El uso de caché ($equiposCache) asegura que si el mismo equipo aparece en varias lecturas,
// solo se consulta a la BD la primera vez.

$equiposCache = []; // Inicialización del array de caché

function getEquipoData($equipoId, &$cache) // La caché se pasa por referencia (&) para modificarla globalmente.
{
  if (!$equipoId) return null;
  if (isset($cache[$equipoId])) return $cache[$equipoId]; // Retorna inmediatamente si ya está en caché.

  // Si no está en caché, consultar y almacenar
  $SQL = "SELECT Mo.modelo_linea, Mo.modelo_modelo, Eq.equipo_codigo, Eq.equipo_serie 
FROM Equipos Eq
INNER JOIN Modelos Mo ON Eq.equipo_modelo_id = Mo.modelo_id
WHERE Eq.equipo_id = " . $equipoId;

  $QRY = consultaData($SQL)['dataFetch'][0] ?? null; // Ejecuta la consulta.
  $cache[$equipoId] = $QRY; // Guarda el resultado en la caché antes de devolverlo.
  return $QRY;
}

?>

<input type="hidden" id="custom_anio" value="<?= $anio; ?>">
<input type="hidden" id="custom_mes" value="<?= $mes; ?>">
<input type="hidden" id="periodoCustom" value="<?= $periodoFormateado; ?>">

<div class="container-fluid">
  <center>
    <h3><i class="fas fa-clipboard-list fa-fw"></i>&nbsp;<?= $encabezadoPeriodo; ?></h3>
  </center>
</div>

<div class="container-fluid div-page-nav-tabs">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a href="#" id="btnMapaLecturas"><i class="fas fa-map-marked-alt"></i> &nbsp; MAPA</a>
    </li>
    <li>
      <a href="#" id="btnFacturas"><i class="fas fa-file-invoice-dollar"></i> &nbsp; FACTURAS</a>
    </li>
    <?php if (!isset($dia)) { // Condición para mostrar el botón de impresión solo si no es un filtro por día específico 
    ?>
      <li>
        <div class="row">
          <div class="col">
            <div class="col btn-group btn-group-sm" role="group" aria-label="Basic mixed styles example">
              <select class="mi-select" id="printLectZona">
                <?php foreach ($zonasData as $zona) { // Itera sobre las zonas obtenidas en el punto 2.1 
                ?>
                  <option value="<?= $zona['zona_id'] ?>"><?= $zona['zona_nombre'] ?></option>
                <?php } ?>
              </select>
              <a href="#" id="btnPrintLectMonth"><i class="fas fa-print"></i> &nbsp; IMPRIMIR</a>
            </div>
          </div>
        </div>
      </li>
    <?php } ?>
    <li>
      <a href="<?= SERVERURL; ?>Lecturas/Agregar"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR</a>
    </li>
    <li>
      <?php filtroCustom("Lecturas"); // Función externa para mostrar el filtro de fecha/periodo 
      ?>
    </li>
  </ul>
</div>

<br><br>

<div class="container-fluid">
  <center>
    <div class="row">
      <?php
      foreach ($resumenZonas as $zonaId => $data) {
        $rentasTotal = $data['rentas'];
        $lecturasRealizadas = $data['lecturas'];
        $faltantes = $rentasTotal - $lecturasRealizadas;
        $zonaNombre = $zonasNombres[$zonaId] ?? "N/A";

        // 1. Calcular el porcentaje de avance (evitando división por cero)
        $porcentajeAvance = ($rentasTotal > 0) ? ($lecturasRealizadas / $rentasTotal) * 100 : 0;

        // 2. Lógica de colores basada en promedios/proporciones
        // Verde: 100% Completado
        // Amarillo: Más del 50% completado (Falta la mitad o menos)
        // Naranja: Menos del 50% completado (Faltan más de la mitad)
        // Gris: Sin rentas

        if ($rentasTotal == 0) {
          $bgColor = '#96969679'; // Gris: Sin actividad
          $statusMsg = "Sin rentas activas";
        } elseif ($porcentajeAvance == 100) {
          $bgColor = '#49ff6479'; // Verde: ¡Listo!
          $statusMsg = "Lecturas completas";
        } elseif ($porcentajeAvance >= 50) {
          $bgColor = '#fff64979'; // Amarillo: Falta la mitad o menos
          $statusMsg = "Faltan pocas ($faltantes)";
        } else {
          $bgColor = '#ff9e4979'; // Naranja: Falta más de la mitad (Urgente)
          $statusMsg = "Falta más del 50%";
        }
      ?>
        <div class="col-12 col-md-3 col-lg-2">
          <div class="card border-0 shadow-sm h-100 zona_id"
            style="background-color: <?= $bgColor ?>; cursor: pointer;"
            id="<?= $zonaId; ?>">
            <div class="card-body p-3 text-center">
              <h6 class="fw-bold mb-1"><?= $zonaNombre; ?></h6>

              <?php if ($rentasTotal > 0): ?>
                <div class="small mb-2">
                  <span class="d-block">Progreso: <b><?= round($porcentajeAvance); ?>%</b></span>
                </div>

                <div class="progress mb-2" style="height: 6px; background: rgba(0,0,0,0.1);">
                  <div class="progress-bar bg-dark" style="width: <?= $porcentajeAvance; ?>%"></div>
                </div>

                <div class="small text-dark">
                  R: <?= $rentasTotal; ?> | L: <?= $lecturasRealizadas; ?> | <b class="text-danger">F: <?= $faltantes; ?></b>
                </div>
              <?php else: ?>
                <span class="small text-muted italic"><?= $statusMsg; ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </center>
</div>


<br>

<div class="container-fluid table-responsive">
  <table class="dataTable table-sm table-hover table table-secondary table-striped-columns">
    <thead class="table-dark">
      <tr>
        <th>
          <center>FECHA</center>
        </th>
        <th>
          <center>RENTA</center>
        </th>
        <th style="width: 175px;">
          <center>EQUIPO EN LECTURA</center>
        </th>
        <th>
          <center>ZONA</center>
        </th>
        <th>
          <center>DATOS</center>
        </th>
        <th>
          <center>ACCIONES</center>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rentasActivas as $rowRenta) { // Bucle principal para pintar cada fila de la tabla
        // Verifica si la renta aplica al periodo (filtro ya hecho en SQL, pero se mantiene la verificación de fecha de inicio por seguridad)
        if (!dateCompare($rowRenta['renta_finicio'], "igualOmenor", $currFecha)) continue;

        // =========================================================
        // Lógica de cálculo de diferencia de lecturas
        // Asigna 0 si la lectura (anterior o actual) no existe (NULL en la BD, gracias al LEFT JOIN)
        // =========================================================

        // Lecturas Anteriores
        $lectura_esc_Ant = $rowRenta['lectura_fecha_ant'] != NULL ? (int)$rowRenta['lectura_esc_ant'] : 0;
        $lectura_bn_Ant = $rowRenta['lectura_fecha_ant'] != NULL ? (int)$rowRenta['lectura_bn_ant'] : 0;
        $lectura_col_Ant = $rowRenta['lectura_fecha_ant'] != NULL ? (int)$rowRenta['lectura_col_ant'] : 0;

        // Lecturas Actuales
        $lectura_esc_Act = $rowRenta['lectura_fecha_act'] != NULL ? (int)$rowRenta['lectura_esc_act'] : 0;
        $lectura_bn_Act = $rowRenta['lectura_fecha_act'] != NULL ? (int)$rowRenta['lectura_bn_act'] : 0;
        $lectura_col_Act = $rowRenta['lectura_fecha_act'] != NULL ? (int)$rowRenta['lectura_col_act'] : 0;

        // Cálculo del total de copias/impresiones (Asegura que el total nunca sea negativo con max(0, ...))
        $TOTALesc = max(0, $lectura_esc_Act - $lectura_esc_Ant);
        $TOTALbn = max(0, $lectura_bn_Act - $lectura_bn_Ant);
        $TOTALcol = max(0, $lectura_col_Act - $lectura_col_Ant);

        // Determinar clase de fila: Si no hay lectura actual, resalta la fila en amarillo (warning).
        $rowClass = ($rowRenta['lectura_fecha_act'] == NULL) ? 'class="table-warning"' : "";
      ?>
        <tr <?= $rowClass; ?>>
          <td>
            <?= ($rowRenta['lectura_fecha_act'] != NULL) ? strtoupper(dateFormat($rowRenta['lectura_fecha_act'], "simple")) : ""; ?>
          </td>

          <td>
            <div class="row">
              <div class="col">
                <a href="/Clientes/Editar/<?= encryption($rowRenta['cliente_id']) ?>" class="btn btn-dark btn-sm rounded-pill" target="_blank"><?= "(" . $rowRenta['cliente_rfc'] . ") - " . $rowRenta['cliente_rs']; ?></a>
                <a href="/Rentas/Detalles/<?= encryption($rowRenta['renta_id']) ?>" class="btn btn-dark btn-sm mt-1 rounded-pill" target="_blank"><?= $rowRenta['contrato_folio'] . "-" . $rowRenta['renta_folio'] . " | " . $rowRenta['renta_depto']; ?></a>
              </div>
              <div class="col">
                <?= $rowRenta['modelo_linea'] . " " . $rowRenta['modelo_modelo']; ?>
                <br>
                <a href="/Equipos/ID/<?= encryption($rowRenta['equipo_id']) ?>" class="btn btn-dark btn-sm mt-1 rounded-pill" target="_blank"><?= $rowRenta['equipo_codigo'] . " | " . $rowRenta['equipo_serie']; ?></a>
              </div>
              <div class="col">
                <?php
                if ($mes == date("n") && $anio == date("Y")) {
                };
                ?>
              </div>
            </div>
          </td>

          <td>
            <div class="row">
              <div class="col">
                <?php
                // Usa la función de caché para obtener los datos del equipo que tomó la lectura.
                if ($rowRenta['lectura_equipo_id'] != NULL) {
                  $equipoLectura = getEquipoData($rowRenta['lectura_equipo_id'], $equiposCache); // Uso de la función con caché.
                  if ($equipoLectura) {
                ?>
                    <?= $equipoLectura['modelo_linea'] . " " . $equipoLectura['modelo_modelo'] ?>
                    <br>
                    <a href="/Equipos/ID/<?= encryption($rowRenta['lectura_equipo_id']) ?>" class="btn btn-dark btn-sm mt-1 rounded-pill" target="_blank"><?= $rowRenta['equipo_codigo'] . " | " . $rowRenta['equipo_serie']; ?></a>
                <?php
                  }
                }
                ?>
              </div>
            </div>
          </td>

          <td>
            <?= $zonasNombres[$rowRenta['renta_zona_id']] ?? $rowRenta['renta_zona_id']; ?>
          </td>

          <td>
            <table class="table table-dark table-sm">
              <thead>
                <tr>
                  <th style="text-align: center;"> &nbsp; </th>
                  <th style="text-align: center;">
                    <?= $rowRenta['lectura_fecha_ant'] != NULL ? ucfirst(dateFormat($rowRenta['lectura_fecha_ant'], 'diaNmesLcorto')) : dateFormat("01-$mesAnt-2000", 'mesL'); ?>
                  </th>
                  <th style="text-align: center;">
                    <?= $rowRenta['lectura_fecha_act'] != NULL ? ucfirst(dateFormat($rowRenta['lectura_fecha_act'], 'diaNmesLcorto')) : dateFormat("01-$mes-2000", 'mesL'); ?>
                  </th>
                  <th style="text-align: center;">TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><b>ESC</b></td>
                  <td><?= $rowRenta['lectura_fecha_ant'] != NULL ? number_format($lectura_esc_Ant) : "<code>0</code>"; ?></td>
                  <td><?= $rowRenta['lectura_fecha_act'] != NULL ? number_format($lectura_esc_Act) : "<code>0</code>"; ?></td>
                  <td><?= $rowRenta['lectura_fecha_ant'] != NULL && $rowRenta['lectura_fecha_act'] != NULL ? number_format($TOTALesc) : "<code>0</code>"; ?></td>
                </tr>
                <tr>
                  <td><b>B&N</b></td>
                  <td><?= $rowRenta['lectura_fecha_ant'] != NULL ? number_format($lectura_bn_Ant) : "<code>0</code>"; ?></td>
                  <td><?= $rowRenta['lectura_fecha_act'] != NULL ? number_format($lectura_bn_Act) : "<code>0</code>"; ?></td>
                  <td><?= $rowRenta['lectura_fecha_ant'] != NULL && $rowRenta['lectura_fecha_act'] != NULL ? number_format($TOTALbn) : "<code>0</code>"; ?></td>
                </tr>
                <?php if ($rowRenta['modelo_tipo'] == "Multicolor") { // Solo muestra color si el equipo es multicolor 
                ?>
                  <tr>
                    <td><b>COL</b></td>
                    <td><?= $rowRenta['lectura_fecha_ant'] != NULL ? number_format($lectura_col_Ant) : "<code>0</code>"; ?></td>
                    <td><?= $rowRenta['lectura_fecha_act'] != NULL ? number_format($lectura_col_Act) : "<code>0</code>"; ?></td>
                    <td><?= $rowRenta['lectura_fecha_ant'] != NULL && $rowRenta['lectura_fecha_act'] != NULL ? number_format($TOTALcol) : "<code>0</code>"; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </td>

          <td>
            <!-- <button class="btn btn-<?= ($rowRenta['lectura_fecha_act'] != NULL) ? "success" : "warning"; ?> btn-lect" data-lectactid="<?= $rowRenta['lectura_id_act'] === NULL ? 0 : encryption($rowRenta['lectura_id_act']); ?>" data-lectantid="<?= $rowRenta['lectura_id_ant'] === NULL ? 0 : encryption($rowRenta['lectura_id_ant']); ?>" data-rentaid="<?= encryption($rowRenta['renta_id']); ?>">LECTURA</button> -->


            <button class="btn btn-sm btn-<?= ($rowRenta['lectura_fecha_act'] != NULL) ? "success" : "warning"; ?> btn-lect"
              id="<?= encryption($rowRenta['renta_id']); ?>" // ID de la renta encriptado
              <?= ($rowRenta['lectura_fecha_act'] != NULL) ? 'data-lectura="' . encryption($rowRenta['lectura_id_act']) . '"' : ""; ?>>
              LECTURA
            </button>
            <?php
            // Lógica para el botón LectChP (Lectura Chequeada para Pagar/Cobrar)
            // Consulta si existe un registro de chequeo para esta renta en el mes/año actual
            $sqlLChP = "SELECT LChP_id, LChP_folio FROM LectChP WHERE LChP_renta_id = '" . $rowRenta['renta_id'] . "' AND LChP_month = '$mes' AND LChP_year = '$anio'";
            $queryLChP = consultaData($sqlLChP);

            if ($queryLChP['numRows'] == 0) {
              // Si no existe el registro de chequeo, muestra "No Checado"
              echo '<button class="btn btn-dark btn-LectChP" data-id="' . encryption($rowRenta['renta_id']) . '" data-mes="' . $mes . '" data-anio="' . $anio . '" data-value="No Checado">No Checado</button>';
            } else {
              $LChP = $queryLChP['dataFetch'][0];
              // Si existe, busca el folio en la tabla de Cobranzas
              $sqlChFolio = consultaData("SELECT cobM_id, cobM_status FROM cobranzasM WHERE cobM_folio = '" . $LChP['LChP_folio'] . "'");

              if ($sqlChFolio['numRows'] > 0) {
                $cobM = $sqlChFolio['dataFetch'][0];
                // Determina el color del botón basado en el estado (status) de la cobranza
                $statusClass = ($cobM['cobM_status'] == 0) ? "warning" : (($cobM['cobM_status'] == 1) ? "info" : (($cobM['cobM_status'] == 2) ? "success" : "light"));
                $dataId = encryption($LChP['LChP_id']);
                $dataValue = encryption($cobM['cobM_id']);
                $folio = $LChP['LChP_folio'];
                echo '<button class="btn btn-' . $statusClass . ' btn-LectChP" data-id="' . $dataId . '" data-value="' . $dataValue . '">' . $folio . '</button>';
              } else {
                // Folio registrado en LectChP, pero no encontrado en CobranzasM
                $dataId = encryption($LChP['LChP_id']);
                $folio = $LChP['LChP_folio'];
                echo '<button class="btn btn-secondary btn-LectChP" data-id="' . $dataId . '" data-value="0">' . $folio . '</button>';
              }
            }

            $sqlRenFact = "SELECT * FROM rentas_facturas WHERE renta_id = '" . $rowRenta['renta_id'] . "' AND anio = '$anio' AND mes = '$mes'";
            $queryRenFact = consultaData($sqlRenFact);

            if ($queryRenFact['numRows'] > 0) {
              $dat = $queryRenFact['dataFetch'][0];
            ?>
              <button type="button" class="btn btn-sm btn-primary btn-renta-factura"
                data-estado="1"
                data-renta="<?= encryption($rowRenta['renta_id']) ?>"
                data-anio="<?= $anio ?>"
                data-mes="<?= $mes ?>"
                data-folio="<?= $dat['folio'] ?>"
                data-identificador="<?= $dat['identificador'] ?>">
                <?= $dat['identificador'] ?>
              </button>
            <?php
            } else {
            ?>
              <button type="button" class="btn btn-sm btn-dark btn-renta-factura"
                data-estado="0"
                data-renta="<?= encryption($rowRenta['renta_id']) ?>"
                data-anio="<?= $anio ?>"
                data-mes="<?= $mes ?>">
                SUBIR
              </button>
            <?php
            }
            ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>