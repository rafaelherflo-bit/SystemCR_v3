<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}

$anio = $_GET['anio'];
$mes  = $_GET['mes'];
$mesL = dateFormat($anio . "-" . $mes . "-" . date("d"), 'mesL');

class PDF extends FPDF
{

  function NbLines($w, $txt)
  {
    // Calcula el número de líneas que ocupará un MultiCell de ancho $w
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n") $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ') $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j) $i++;
        } else
          $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else
        $i++;
    }
    return $nl;
  }

  // Dibujamos el bloque manualmente asegurando alturas iguales
  function DrawGroupedRow($cliente, $contratos, $widths)
  {
    // 1. Calcular altura requerida por las rentas (6mm por cada fila de renta)
    $totalRentas = 0;
    foreach ($contratos as $cont) $totalRentas += count($cont['rentas']);
    $hRentas = 6 * $totalRentas;

    // 2. Calcular altura requerida por el texto del cliente
    $hCliente = 6 * $this->NbLines($widths[0], $cliente);

    // 3. La altura total del bloque es el máximo entre rentas y nombre del cliente
    $hTotal = max($hRentas, $hCliente);

    // 4. Salto de página preventivo
    if ($this->GetY() + $hTotal > 250) { // 250 es un margen de seguridad antes del footer
      $this->AddPage();
    }

    $x = $this->GetX();
    $y = $this->GetY();

    // 3. Dibujar bordes del bloque (Caja externa)
    $this->Rect($x, $y, array_sum($widths), $hTotal);

    // 4. Dibujar separadores verticales
    $this->Line($x + $widths[0], $y, $x + $widths[0], $y + $hTotal); // Línea después de Cliente
    $this->Line($x + $widths[0] + $widths[1], $y, $x + $widths[0] + $widths[1], $y + $hTotal); // Después de Contrato

    // 5. Rellenar datos
    // Cliente (MultiCell contenido en el Rect)
    $this->SetXY($x, $y);
    $this->MultiCell($widths[0], 6, $cliente, 0, 'L');

    // Contratos y Rentas
    $y_cursor = $y;
    foreach ($contratos as $cont) {
      $hCont = 6 * count($cont['rentas']);

      // Contrato
      $this->SetXY($x + $widths[0], $y_cursor);
      $this->MultiCell($widths[1], $hCont, $cont['folio'], 0, 'C');

      // Rentas (Líneas horizontales dentro del bloque)
      foreach ($cont['rentas'] as $renta) {
        $this->SetXY($x + $widths[0] + $widths[1], $y_cursor);
        $this->Cell($widths[2], 6, $renta['info'], 0, 0, 'L');
        $this->Cell($widths[3], 6, $renta['folio'], 1, 1, 'C');

        // Línea divisoria horizontal para rentas
        if ($y_cursor + 6 < ($y + $hTotal)) {
          $this->Line($x + $widths[0] + $widths[1], $y_cursor + 6, $x + array_sum($widths), $y_cursor + 6);
        }
        $y_cursor += 6;
      }
    }
    $this->SetXY($this->lMargin, $y + $hTotal);
  }
}

// --- Lógica Principal ---
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AddPage();

// --- ENCABEZADO FIJO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Image(LOGOCR, 160, 10, 40, 0, '', WEBSITE); // Ajustado a la derecha
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(20);
$pdf->Cell(10, 10, utf8_decode('FACTURACION ' . strtoupper($mesL) . ' DE ' . $anio), 0, 1, 'L');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 6, "CLIENTE", 1, 0, 'C');
$pdf->Cell(20, 6, "CONTRATO", 1, 0, 'C');
$pdf->Cell(60, 6, "RENTA", 1, 0, 'C');
$pdf->Cell(15, 6, "FOLIO", 1, 1, 'C');

// Obtener datos
$sqlLCHP = "SELECT
                -- Datos del Cliente (Agrupación nivel 1)
                Cl.cliente_id,
                Cl.cliente_rs, 
                Cl.cliente_rfc,
                
                -- Datos del Contrato (Agrupación nivel 2)
                C.contrato_id,
                C.contrato_folio,
                
                -- Datos de la Renta (Datos individuales)
                R.renta_id,
                R.renta_folio,
                R.renta_depto,
                
                -- Datos de la lectura (Folio)
                RF.identificador AS LChP_folio
            FROM Rentas R
            INNER JOIN Contratos C ON R.renta_contrato_id = C.contrato_id
            INNER JOIN Clientes Cl ON C.contrato_cliente_id = Cl.cliente_id
            LEFT JOIN rentas_facturas RF ON RF.renta_id = R.renta_id 
              AND RF.mes = $mes 
              AND RF.anio = $anio
            WHERE R.renta_estado = 'Activo'
            ORDER BY 
                Cl.cliente_id ASC, 
                C.contrato_id ASC, 
                R.renta_id ASC;"; // Tu consulta SQL original
$qryLCHP = consultaData($sqlLCHP);

// Pre-procesar: Agrupar por Cliente -> Contrato -> Renta
$datosAgrupados = [];
foreach ($qryLCHP['dataFetch'] as $L) {
  $cId = $L['cliente_id'];
  $coId = $L['contrato_id'];
  $datosAgrupados[$cId]['nombre'] = utf8_decode($L['cliente_rs'] . " (" . $L['cliente_rfc'] . ")");
  $datosAgrupados[$cId]['contratos'][$coId]['folio'] = $L['contrato_folio'];
  $datosAgrupados[$cId]['contratos'][$coId]['rentas'][] = [
    'info' => utf8_decode($L['renta_folio'] . " | " . $L['renta_depto']),
    'folio' => $L['LChP_folio'] ?? ''
  ];
}

// Dibujar
$widths = [100, 20, 60, 15];
$pdf->SetFont('Arial', '', 9);
foreach ($datosAgrupados as $cliente) {
  $pdf->DrawGroupedRow($cliente['nombre'], $cliente['contratos'], $widths);
}

$pdf->Output('I', "Reporte.pdf");
