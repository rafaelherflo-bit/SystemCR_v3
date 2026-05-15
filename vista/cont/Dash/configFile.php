<?php
// Consulta para Reportes pendientes
$sqlReportes = "SELECT * FROM Reportes reps
  INNER JOIN Rentas rens ON reps.reporte_renta_id = rens.renta_id
  INNER JOIN Contratos cons ON rens.renta_contrato_id = cons.contrato_id
  INNER JOIN Equipos eq ON reps.reporte_equipo_id = eq.equipo_id
  INNER JOIN Modelos mods ON eq.equipo_modelo_id = mods.modelo_id
WHERE reporte_estado = 0";
$qryReportes = consultaData($sqlReportes);
if ($qryReportes['numRows'] > 0) {
?>
  <div class="row full-box tile-container justify-content-center">
    <div class="col">
      <div class="container-fluid">
        <div class="row" style="background:#fff3cd; text-align: left; padding:15px; border:1px solid #ffeeba; margin-bottom:20px; border-radius:5px;">
          <h3 style='color:#856404; margin-top:0;'>⚠️ REPORTES PENDIENTES DE REVISIÓN</h3>
          <p style='font-size:0.9em;'>Los siguientes reportes han sido creados pero aún no han sido revisados por el equipo de soporte. Por favor, haz clic en "Completar" para revisar y cerrar cada reporte.</p>

          <?php foreach ($qryReportes['dataFetch'] as $reporte) { ?>
            <div class="col-md-4 mb-4 d-flex">
              <div class="card h-100 w-100 d-flex flex-column">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?= $reporte['contrato_folio'] . '-' . $reporte['renta_folio'] . ' | ' . $reporte['renta_depto'] ?></h5>
                  <p class="card-text">
                    Fecha: <?= $reporte['reporte_fecha'] ?><br>
                    Quien Reporta: <?= $reporte['reporte_wmakes'] ?><br>
                    Equipo: <?= $reporte['modelo_linea'] . ' ' . $reporte['modelo_modelo'] . ' | ' . $reporte['equipo_serie'] ?>
                    <hr>
                  <h4>Descripción del Reporte</h4>
                  <?= $reporte['reporte_reporte'] ?>
                  </p>
                </div>
                <div class="card-footer">
                  <a href="/ReportesR/idRC/<?= encryption($reporte['reporte_id']) ?>" class="btn btn-primary">Completar</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>



<?php // Seccion para ver movimietos de rentas por concluir...
$sqlAlmacen = "SELECT * FROM AlmacenM WHERE AlmM_estado = 0";
$qryAlmacen = consultaData($sqlAlmacen);
if ($qryAlmacen['numRows'] > 0) {
?>
  <div class="row full-box tile-container justify-content-center">
    <div class="col">
      <div class="container-fluid">
        <div class="row" style="background:#fff3cd; text-align: left; padding:15px; border:1px solid #ffeeba; margin-bottom:20px; border-radius:5px;">
          <h3 style='color:#856404; margin-top:0;'>⚠️ MOVIMIENTOS PENDIENTES DE REVISIÓN</h3>
          <p style='font-size:0.9em;'>Los siguientes movimientos han sido creados pero aún no han sido revisados por el equipo de soporte. Por favor, haz clic en "Completar" para revisar y cerrar cada movimiento.</p>
          <?php foreach ($qryAlmacen['dataFetch'] as $dataAM) { ?>
            <div class="col-md-4 mb-4 d-flex">
              <div class="card h-100 w-100 d-flex flex-column">
                <div class="card-body d-flex flex-column">
                  <?php
                  /*
            Tipos de Movimientos
              0 = Entrada
              1 = Salida Interna
              2 = Salida Renta
              3 = Salida Venta
            */
                  $tipoM = $dataAM['AlmM_tipo'];
                  $indentM = $dataAM['AlmM_identificador'];
                  $AlmM_comentario = $dataAM['AlmM_comentario'];

                  $tituloMovimiento = "";
                  $tipoMovimiento = "";

                  switch ($tipoM) {

                    case '0':
                      $tipoMovimiento = "ENTRADA";
                      $dataQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = $indentM")['dataFetch'][0];
                      $tituloMovimiento = $dataQRY['usuario_nombre'] . " " . $dataQRY['usuario_apellido'];
                      break;

                    case '1':
                      $tipoMovimiento = "SALIDA INTERNA";
                      $dataQRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = $indentM")['dataFetch'][0];
                      $tituloMovimiento = $dataQRY['usuario_nombre'] . " " . $dataQRY['usuario_apellido'];
                      break;

                    case '2':
                      $tipoMovimiento = "SALIDA PARA RENTA";
                      $dataQRY = consultaData("SELECT * FROM Rentas INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id WHERE renta_id = $indentM")['dataFetch'][0];
                      $tituloMovimiento = $dataQRY['contrato_folio'] . "-" . $dataQRY['renta_folio'] . " " . $dataQRY['renta_depto'];
                      break;

                    case '3':
                      $tipoMovimiento = "SALIDA PARA VENTA";
                      $dataQRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = $indentM")['dataFetch'][0];
                      $tituloMovimiento = "(" . $dataQRY['cliente_rfc'] . ") " . $dataQRY['cliente_rs'];
                      break;
                  }
                  ?>
                  <h3 class="card-title"><?= $tipoMovimiento ?></h3>
                  <p class="card-text"><?= $tituloMovimiento ?></p>
                  <hr>
                  <h5 class="card-title"><?= $dataAM['AlmM_folio'] ?></h5>
                  <h6 class="card-subtitle mb-2 text-body-secondary"><?= $dataAM['AlmM_fecha'] ?></h6>
                  <hr>
                  <p class="card-text"><?= $dataAM['AlmM_comentario'] ?></p>
                  <hr>

                  <div class="mb-3">
                    <?php
                    $sqlADs = "SELECT * FROM AlmacenD AD INNER JOIN AlmacenP AP ON AP.AlmP_id = AD.AlmDP_id WHERE AD.AlmDM_id = '" . $dataAM['AlmM_id'] . "'";
                    foreach (consultaData($sqlADs)['dataFetch'] as $dataADs) {
                      switch ($dataADs['AlmP_cat_id']) {
                        case 1:
                          $categoriaP = "TONER";
                          break;
                        case 2:
                          $categoriaP = "CHIP";
                          break;
                        case 3:
                          $categoriaP = "REFACCION";
                          break;
                        case 4:
                          $categoriaP = "SERVICIOS";
                          break;

                        default:
                          $categoriaP = "OTRO";
                          break;
                      }
                    ?>
                      <p class="card-text"><?= $dataADs['AlmD_cantidad'] ?> pzs | <?= $categoriaP ?> - <?= $dataADs['AlmP_codigo'] ?> - <?= $dataADs['AlmD_comentario'] ?></p>
                    <?php } ?>
                  </div>

                  <div class="mt-auto pt-3">
                    <div class="d-grid gap-2">
                      <a href="/Almacen/Movimientos/Detalles/<?= encryption($dataAM['AlmM_id']) ?>" class="btn btn-primary" target="_blank"> Detalles</a>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>


<?php
// 1. Consulta optimizada: Calculamos el stock real con un solo query usando JOINs y agregaciones
$AlmP_SQL = "SELECT
                p.*,
                prov.AlmProv_nombre,
                u.unList_unidad,
                -- Sumamos entradas (tipo 0) y restamos salidas (tipo != 0)
                SUM(CASE WHEN m.AlmM_tipo = 0 THEN d.AlmD_cantidad ELSE -d.AlmD_cantidad END) as realStock
             FROM AlmacenP p
              INNER JOIN AlmacenProvs prov ON p.AlmP_prov_id = prov.AlmProv_id
              INNER JOIN unidadesList u ON p.AlmP_unidadM = u.unList_id
              LEFT JOIN AlmacenD d ON p.AlmP_id = d.AlmDP_id
              LEFT JOIN AlmacenM m ON d.AlmDM_id = m.AlmM_id AND m.AlmM_estado = 1
             WHERE p.AlmP_cat_id IN (1, 2, 3)
             GROUP BY p.AlmP_id
             ORDER BY p.AlmP_cat_id ASC, p.AlmP_subcat_id ASC";

$AlmP_QRY = consultaData($AlmP_SQL);
$rawProductos = $AlmP_QRY['dataFetch'] ?? [];

$colores = [
  '0' => 'Monocromatico',
  '1' => 'Color Negro',
  '2' => 'Color Magenta',
  '3' => 'Color Cyan',
  '4' => 'Color Amarillo'
];

$resultadoFinal = [];

foreach ($rawProductos as $p) {
  // Solo procesar si el stock es menor o igual al mínimo
  if (($p['realStock'] > $p['AlmP_stock_min']) || $p['AlmP_stock_min'] == 0) continue;

  $catID = $p['AlmP_cat_id'];
  $descParts = explode(' | ', $p['AlmP_descripcion']);

  // Inicialización de variables de descripción
  $codigo = $descParts[0] ?? '';
  $noParte = $rendi = $compatibilidad = '';
  $subCategoria = '';

  switch ($catID) {
    case '1': // TONERS
      $categoria = "TONERS";
      $noParte = $descParts[1] ?? '';
      $rendi = $descParts[2] ?? '';
      $compatibilidad = $descParts[3] ?? '';
      $subCategoria = $colores[$p['AlmP_subcat_id']] ?? 'N/A';
      break;

    case '2': // CHIPS
      $categoria = "CHIPS";
      $rendi = $descParts[1] ?? '';
      $compatibilidad = $descParts[2] ?? '';
      $subCategoria = $colores[$p['AlmP_subcat_id']] ?? 'N/A';
      break;

    case '3': // REFACCIONES
      $categoria = "REFACCIONES";
      $compatibilidad = $descParts[1] ?? '';
      // Aquí podrías optimizar trayendo el nombre de la subcat en el JOIN inicial
      $qryCatsRef = consultaData("SELECT * FROM CategoriasR WHERE catR_id = " . $p['AlmP_subcat_id']);
      $subCategoria = $qryCatsRef['dataFetch'][0]['catR_nombre'];
      break;
  }

  // Estructuramos el array
  $resultadoFinal[$categoria]['subcategorias'][$subCategoria]['productos'][] = [
    'idEnc' => encryption($p['AlmP_id']),
    'codigo' => $codigo,
    'noParte' => $noParte,
    'rendi' => $rendi,
    'compatibilidad' => $compatibilidad,
    'realStock' => $p['realStock'] == 0 ? 0 : $p['realStock'],
    'minStock' => $p['AlmP_stock_min'] == 0 ? 0 : $p['AlmP_stock_min']
  ];
}

// --- NUEVA LÓGICA DE CONTEO ---
$totalCategorias = count($resultadoFinal);
$totalGeneralProductos = 0;
// 1. Ordenar las Categorías alfabéticamente (TONERS, CHIPS, etc.)
ksort($resultadoFinal);

foreach ($resultadoFinal as $catNombre => &$catData) {
  // 2. Ordenar las Subcategorías alfabéticamente (Color Negro, Cyan, etc.)
  // ksort($catData['subcategorias']);

  $countProdsEnCat = 0;
  $catData['total_subcats'] = count($catData['subcategorias']);

  foreach ($catData['subcategorias'] as $subNombre => &$subData) {

    // 3. Ordenar los PRODUCTOS internamente
    usort($subData['productos'], function ($a, $b) {
      // Primero comparamos el código de forma alfanumérica (natural)
      // strnatcasecmp ignora mayúsculas y ordena "2" antes que "10"
      $res = strnatcasecmp($a['codigo'], $b['codigo']);

      // Si el código es el mismo, ordenamos por realStock de menor a mayor
      if ($res === 0) {
        return $a['realStock'] <=> $b['realStock'];
      }
      return $res;
    });

    // Continuamos con tus conteos...
    $numProds = count($subData['productos']);
    $subData['total_prods'] = $numProds;
    $countProdsEnCat += $numProds;
  }

  $catData['total_prods_cat'] = $countProdsEnCat;
  $totalGeneralProductos += $countProdsEnCat;
}
unset($catData, $subData); // Importante limpiar referencias

// Visualización limpia
?>
<div class="row full-box tile-container justify-content-center">
  <div class="col">
    <div class="container-fluid">
      <div class="row" style="background:#fff3cd; text-align: left; padding:15px; border:1px solid #ffeeba; margin-bottom:20px; border-radius:5px;">
        <h3 style='color:#856404; margin-top:0;'>
          ⚠️ STOCK CRÍTICO (Total: <?= $totalGeneralProductos ?> productos)
        </h3>

        <?php
        $catActual = 0;
        foreach ($resultadoFinal as $nombreCat => $catData) {
          $catActual++;
        ?>
          <div class="col">
            <div class="container-fluid">
              <?php
              // Cabecera de Categoría
              if ($nombreCat == "TONERS") $iconCat = "fa-spray-can";
              if ($nombreCat == "CHIPS") $iconCat = "fa-microchip";
              if ($nombreCat == "REFACCIONES") $iconCat = "fa-toolbox";

              echo '<strong>●    <span class="btn btn-sm btn-dark btn-PDF" data-PDF="' . $nombreCat . '"><i class="fas ' . $iconCat . '" style=" vertical-align: middle;"></i>  (' . $catData['total_prods_cat'] . ' ítems)    <i class="fas fa-file-pdf fa-fw"></i></span></strong><br>';

              $subActual = 0;
              $totalSub = $catData['total_subcats'];

              foreach ($catData['subcategorias'] as $nombreSubCat => $subData) {
                $subActual++;
                $esUltimaSub = ($subActual === $totalSub);

                // Símbolo de la subcategoría
                $simboloSub = $esUltimaSub ? "└──" : "├──";
                // Si la subcategoría NO es la última, las líneas de sus productos deben empezar con │
                $conectorVerticalSub = $esUltimaSub ? "         " : "│      ";

                echo "<strong>$simboloSub ● $nombreSubCat ({$subData['total_prods']})</strong><br>";

                $prodActual = 0;
                $totalProds = $subData['total_prods'];

                foreach ($subData['productos'] as $prod) {
                  $prodActual++;
                  $esUltimoProd = ($prodActual === $totalProds);

                  // Símbolo del producto
                  $simboloProd = $esUltimoProd ? " └──" : " ├──";
                  // Conector para las líneas de detalle (descripción y stock)
                  $conectorDetalle = $esUltimoProd ? "           " : " │       ";

                  $info = array_filter([$prod['noParte'], $prod['rendi'], $prod['compatibilidad']]);

                  // Línea 1: Botón y Código
                  // echo '<span>' . $conectorVerticalSub . $simboloProd . ' ● </span><a class="btn btn-sm btn-dark" target="_blank" href="/Almacen/' . ucfirst(strtolower($nombreCat)) . '/Editar' . '/' . $prod["idEnc"] . '">' . $prod["codigo"] . '    <i class="fas fa-edit fa-fw"></i></a><br>';

                  // Línea 2: Descripción (conector vertical del producto si no es el último)
                  // echo '<span>' . $conectorVerticalSub . $conectorDetalle . '      ' . (empty($info) ? 'Sin descripción' : implode(' | ', $info)) . '</span><br>';

                  // // Línea 3: Stock
                  // echo '<span>' . $conectorVerticalSub . $conectorDetalle . '      Stock <i style="color:#0e8aff;">(Min: ' . $prod['minStock'] . ')</i> / <i style="color:#ff7979;">(Real: ' . $prod['realStock'] . ')</i></span><br>';
                }
              }
              ?>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</div>


<?php // Seccion para mostrar la falta de datos en rentas... 
?>
<div class="full-box tile-container row">
  <div class="col">
    <div class="container-fluid">
      <?php
      // ===================================================================
      // 1. OBTENCIÓN DE DATOS Y FILTRADO INICIAL
      // ===================================================================
      $SQLrentas = "SELECT R.*, Cl.cliente_rs, Co.contrato_folio, M.modelo_tipo, E.equipo_serie
                FROM Rentas R
                INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
                INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
                INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id
                INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id
                WHERE R.renta_estado = 'Activo'
                AND Cl.cliente_rs != 'FMEDICAL'
                ORDER BY cliente_rs";
      $QRYrentas = consultaData($SQLrentas);

      $rentasIncompletas = [];
      $rentasParaAnalisis = [];
      $equiposIds = [];
      $rentasIds = [];

      foreach ($QRYrentas['dataFetch'] as $renta) {
        $esColor = ($renta['modelo_tipo'] == "Multicolor");
        $faltante = [];

        if ($renta['renta_costo'] <= 0) $faltante[] = "Costo ($0)";
        if ($renta['renta_inc_bn'] <= 0) $faltante[] = "Inc. B&N";
        if ($renta['renta_exc_bn'] <= 0) $faltante[] = "Exc. B&N";
        if ($esColor && ($renta['renta_inc_col'] <= 0)) $faltante[] = "Inc. COL";

        if (!empty($faltante)) {
          $rentasIncompletas[] = [
            'info' => "<a target='_blank' href='/Rentas/Editar/" . encryption($renta['renta_id']) . "' class='btn btn-dark btn-sm'>{$renta['contrato_folio']}-{$renta['renta_folio']}</a> - {$renta['cliente_rs']} - {$renta['renta_depto']}",
            'campos' => implode(", ", $faltante)
          ];
        } else {
          $rentasParaAnalisis[$renta['renta_id']] = $renta;
          $equiposIds[] = $renta['renta_equipo_id'];
          $rentasIds[] = $renta['renta_id'];
        }
      }

      // ===================================================================
      // 2. MOSTRAR AUDITORÍA (Mantenemos el aviso de configuración)
      // ===================================================================
      if (!empty($rentasIncompletas)) {
        echo "<div style='background:#fff3cd; text-align: left; padding:15px; border:1px solid #ffeeba; margin-bottom:20px; border-radius:5px;'>";
        echo "<h3 style='color:#856404; margin-top:0;'>⚠️ RENTAS QUE REQUIEREN CONFIGURACIÓN</h3>";
        echo "<p style='font-size:0.9em;'>Las siguientes rentas no pueden ser analizadas porque sus límites o costos están en cero:</p>";
        echo "<ul style='font-size:1.25em;'>";
        foreach ($rentasIncompletas as $ri) {
          echo "<li><b>{$ri['info']}:</b> <span style='color:red;'>Falta: {$ri['campos']}</span></li>";
        }
        echo "</ul></div>";
      }

      // ===================================================================
      // 3. ANÁLISIS DE PREDICCIÓN Y VALIDACIÓN DE LECTURA REAL
      // ===================================================================
      // if (!empty($equiposIds)) {
      //   $idsEquiposString = implode("','", $equiposIds);
      //   $idsRentasString = implode("','", $rentasIds);
      //   $fechaLimite = date('Y-m-d', strtotime("-45 days"));
      //   $mesActual = date('m');
      //   $anioActual = date('Y');

      //   // Traer Historial para Predicción
      //   $SQLhistorial = "SELECT * FROM historial_reportes WHERE equipo_id IN ('$idsEquiposString') AND date_of_receipt >= '$fechaLimite' ORDER BY date_of_receipt ASC";
      //   $QRYhistorial = consultaData($SQLhistorial);

      //   // Traer Lecturas Reales del Mes Actual para validación
      //   $SQLlecturasMes = "SELECT * FROM Lecturas WHERE lectura_renta_id IN ('$idsRentasString') AND MONTH(lectura_fecha) = '$mesActual' AND YEAR(lectura_fecha) = '$anioActual'";
      //   $QRYlecturasMes = consultaData($SQLlecturasMes);

      //   $reportesAgrupados = [];
      //   foreach ($QRYhistorial['dataFetch'] as $h) {
      //     $reportesAgrupados[$h['equipo_id']][] = $h;
      //   }

      //   $lecturasMesAgrupadas = [];
      //   foreach ($QRYlecturasMes['dataFetch'] as $l) {
      //     $lecturasMesAgrupadas[$l['lectura_renta_id']] = $l;
      //   }

      //   echo "<h2>Predicción de Excedentes Críticos</h2>";

      //   foreach ($rentasParaAnalisis as $renta) {
      //     $equipoId = $renta['renta_equipo_id'];
      //     $rentaId = $renta['renta_id'];
      //     $historial = $reportesAgrupados[$equipoId] ?? [];
      //     $lecturaRealMes = $lecturasMesAgrupadas[$rentaId] ?? null;

      //     if (count($historial) < 2) continue;

      //     $primeraH = $historial[0];
      //     $ultimaH = end($historial);
      //     $diasAnalizados = (new DateTime($primeraH['date_of_receipt']))->diff(new DateTime($ultimaH['date_of_receipt']))->days ?: 1;

      //     $confianzaColor = ($diasAnalizados >= 15) ? "#27ae60" : (($diasAnalizados >= 7) ? "#f1c40f" : "#e67e22");
      //     $porcentajeConfianza = min(100, ($diasAnalizados / 20) * 100);

      //     $tiposContadores = [
      //       'esc'   => ['label' => 'ESC', 'inc' => $renta['renta_inc_esc'], 'db_h' => 'scan_total', 'db_l' => 'lectura_esc'],
      //       'bn'   => ['label' => 'B&N', 'inc' => $renta['renta_inc_bn'], 'db_h' => 'bw_total', 'db_l' => 'lectura_bn'],
      //       'col'  => ['label' => 'COLOR', 'inc' => $renta['renta_inc_col'], 'db_h' => 'color_total', 'db_l' => 'lectura_col']
      //     ];

      //     $analisisResultados = [];
      //     $esCritico = false;
      //     $prediccionCumplida = false;

      //     foreach ($tiposContadores as $key => $conf) {
      //       $totalConsumido = $ultimaH[$conf['db_h']] - $primeraH[$conf['db_h']];
      //       $cdp = $totalConsumido / $diasAnalizados;
      //       $proyeccionMes = $cdp * 30.4;
      //       $limite = $conf['inc'];

      //       if ($limite > 0 && $proyeccionMes > ($limite * 1.05)) {
      //         $esCritico = true;
      //       }

      //       // Validar si la lectura real ya existe y superó el límite
      //       if ($lecturaRealMes && $limite > 0) {
      //         // Nota: Aquí se asume que la tabla Lecturas guarda el consumo del mes o se resta con la anterior. 
      //         // Si lectura_bn es el contador acumulado, deberías restarlo contra la lectura del mes pasado.
      //         if ($lecturaRealMes[$conf['db_l']] > $limite) {
      //           $prediccionCumplida = true;
      //         }
      //       }

      //       $analisisResultados[$key] = [
      //         'label' => $conf['label'],
      //         'cdp' => $cdp,
      //         'proyeccion' => $proyeccionMes,
      //         'limite' => $limite,
      //         'excedente' => max(0, $proyeccionMes - $limite)
      //       ];
      //     }

      //     if ($esCritico) {
      //       echo "<div style='border-left:6px solid #ff4d4d; padding:20px; background:#fff; margin-bottom:25px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius:8px;'>";

      //       echo "<div style='display:flex; justify-content:space-between;'>";
      //       echo "<div><strong style='font-size:1.2em;'>{$renta['cliente_rs']}</strong><br><small>{$renta['equipo_serie']} | {$renta['renta_depto']}</small></div>";
      //       echo "<div style='text-align:right;'><div style='background:#eee; height:8px; width:100px; border-radius:4px;'><div style='background:$confianzaColor; width:$porcentajeConfianza%; height:100%;'></div></div><small>$diasAnalizados días de historial</small></div>";
      //       echo "</div>";

      //       // MENSAJE DE PREDICCIÓN CUMPLIDA
      //       if ($prediccionCumplida) {
      //         echo "<div style='margin-top:10px; padding:10px; background:#d4edda; color:#155724; border-radius:5px; border:1px solid #c3e6cb; font-size:0.85em;'>";
      //         echo "🎯 <b>Predicción Confirmada:</b> La lectura oficial de este mes ya superó el límite contratado.";
      //         echo "</div>";
      //       }

      //       echo "<table style='width:100%; margin-top:15px; font-size:0.9em;'>
      //                       <tr style='text-align:left; color:#888;'><th>CONTADOR</th><th>LÍMITE</th><th>DIARIO</th><th>PROY. MES</th><th>EXCEDENTE</th></tr>";
      //       foreach ($analisisResultados as $res) {
      //         if ($res['limite'] <= 0 && $res['label'] != 'B&N') continue;
      //         echo "<tr><td><b>{$res['label']}</b></td><td>" . number_format($res['limite']) . "</td><td>" . number_format($res['cdp'], 1) . "</td><td style='color:#e74c3c; font-weight:bold;'>" . number_format($res['proyeccion']) . "</td><td><span style='color:#e74c3c;'>+ " . number_format($res['excedente']) . "</span></td></tr>";
      //       }
      //       echo "</table></div>";
      //     }
      //   }
      // }
      ?>
    </div>
  </div>
</div>


<?php // Seccion para mostrar consumibles... 
?>
<div class="full-box tile-container row">
  <div class="col">
    <div class="container-fluid">
      <?php
      echo "<div style='background:#fff3cd; text-align: left; padding:15px; border:1px solid #ffeeba; margin-bottom:20px; border-radius:5px;'>";
      echo "<h3 style='color:#856404; margin-top:0;'>⚠️ RENTAS SIN CHIP O CON BAJO NIVEL DE TONER</h3>";
      echo "<p style='font-size:0.9em;'>Los niveles de toner son los actuales, recuerda mantener abastecidas las rentas y que los cartuchos contengan chip para una mejor verificacion:</p>";
      $SQLrentas = "SELECT * FROM Rentas
                INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                WHERE renta_estado = 'Activo'
                ORDER BY cliente_rs ASC,
                contrato_folio ASC,
                renta_folio ASC";
      $QRYrentas = consultaData($SQLrentas);
      $cliID = 0;
      $contrID = 0;
      $statusM = $statusC = $statusY = $statusR = FALSE;

      foreach ($QRYrentas['dataFetch'] as $renta) {
        $rentaStatus = "";
        $statusK = (($renta['equipo_nivel_K'] <= 30 && $renta['renta_stock_K'] == 0) || $renta['chip_k'] == 0) ? TRUE : FALSE;
        if ($statusK) {
          $chipK = $renta['chip_k'] == 0 ? '' : '<i class="fas fa-microchip"></i>  ';
          $rentaStatus = " <b>|</b> <span class='badge rounded-pill bg-dark text-bg-ligth'><b>" . $chipK . $renta['equipo_nivel_K'] . "%</b></span>";
        }
        $isColor = ($renta['modelo_tipo'] == "Multicolor") ? TRUE : FALSE;
        if ($isColor) {
          $statusM = (($renta['equipo_nivel_M'] <= 30 && $renta['renta_stock_M'] == 0) || $renta['chip_m'] == 0) ? TRUE : FALSE;
          if ($statusM) {
            $chipM = $renta['chip_m'] == 0 ? '' : '<i class="fas fa-microchip"></i>  ';
            $rentaStatus = $rentaStatus . " <b>|</b>  <span class='badge rounded-pill text-bg-danger'><b>" . $chipM . $renta['equipo_nivel_M'] . "%</b></span>";
          }

          $statusC = ($renta['equipo_nivel_C'] <= 30 && $renta['renta_stock_C'] == 0) ? TRUE : FALSE;
          if ($statusC) {
            $chipC = $renta['chip_c'] == 0 ? '' : '<i class="fas fa-microchip"></i>  ';
            $rentaStatus = $rentaStatus . " <b>|</b>  <span class='badge rounded-pill bg-primary text-bg-dark'><b>" . $chipC . $renta['equipo_nivel_C'] . "%</b></span>";
          }

          $statusY = ($renta['equipo_nivel_Y'] <= 30 && $renta['renta_stock_Y'] == 0) ? TRUE : FALSE;
          if ($statusY) {
            $chipY = $renta['chip_y'] == 0 ? '' : '<i class="fas fa-microchip"></i>  ';
            $rentaStatus = $rentaStatus . " <b>|</b>  <span class='badge rounded-pill bg-warning text-bg-dark'><b>" . $chipY . $renta['equipo_nivel_Y'] . "%</b></span>";
          }
        }

        $statusR = ($renta['modelo_resi'] == 1 && $renta['equipo_nivel_R'] >= 50 && $renta['renta_stock_R'] == 0) ? TRUE : FALSE;
        if ($statusR) {
          $rentaStatus = $rentaStatus . " <b>|</b>  <span class='badge rounded-pill bg-secondary text-bg-dark'><b>" . $renta['equipo_nivel_R'] . " %" . "</b></span>";
        }

        if ($statusK || ($isColor && ($statusM || $statusC || $statusY)) || $statusR) {
          if ($cliID == 0 || $cliID != $renta['cliente_id']) {
            if ($cliID != 0) {
              echo "<hr>";
            }
            echo "<p style='font-size:1.25em;'><b>| • <a target='_blank' href='/Clientes/Editar/" . encryption($renta['cliente_id']) . "' class='badge rounded-pill text-bg-dark'>CLIENTE</a> </b>" . $renta['cliente_rs'] . "<br>";
            $cliID = $renta['cliente_id'];
          }

          if ($contrID == 0 || $contrID != $renta['contrato_id']) {
            echo "<b>   | • <a target='_blank' href='/Contratos/Detalles/" . encryption($renta['contrato_id']) . "' class='badge rounded-pill text-bg-dark'>CONTRATO</a> </b>" . $renta['contrato_folio'] . "<br>";
            $contrID = $renta['contrato_id'];
          }

          // ------------------------ Calculo de tiempo desde el ultimo registro...
          $sqlLastReg = "SELECT * FROM historial_reportes WHERE equipo_id = " . $renta['renta_equipo_id'] . " ORDER BY fecha_registro DESC LIMIT 1";
          $qryLastReg = consultaData($sqlLastReg);
          if ($qryLastReg['numRows'] > 0) {
            $qryLastReg = $qryLastReg['dataFetch'][0];
            $fecha_registro = new DateTime($qryLastReg['fecha_registro']);
            $fecha_actual = new DateTime();
            $diferencia = $fecha_actual->diff($fecha_registro);

            // Extraemos los valores
            $dias = $diferencia->days;
            $horas = $diferencia->h;
            $minutos = $diferencia->i;

            $lastReg = "Hace";
            if ($dias > 0) {
              $lastReg .= " " . $dias . " día" . ($dias > 1 ? "s" : "");
            }
            if ($horas > 0) {
              $lastReg .= " " . $horas . " hora" . ($horas > 1 ? "s" : "");
            }
            if ($minutos > 0 && $dias == 0) { // Solo mostrar minutos si no han pasado días para no saturar el texto
              $lastReg .= " " . $minutos . " minuto" . ($minutos > 1 ? "s" : "");
            }
          } else {
            // 3. Valor por defecto si la tabla está vacía para ese equipo
            $lastReg = "Sin registros históricos";
          }

          // ------------------------ Fin del calculo de tiempo del ultimo registro

          echo "<b>      | • </b><a target='_blank' href='/Rentas/Detalles/" . encryption($renta['renta_id']) . "' class='badge rounded-pill text-bg-dark'>RENTA</a> {$renta['renta_folio']} <b>|</b> {$renta['renta_depto']} <b>|</b> <u>{$lastReg}</u>{$rentaStatus}<br>";
        }
      }
      echo "</p></div>";

      ?>
    </div>
  </div>
</div>