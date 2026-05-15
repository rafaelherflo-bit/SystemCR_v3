<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;

        // Ajuste de salto de página para tamaño Carta (Letter) Horizontal
        // El alto de la hoja carta es 215.9mm, restando márgenes usamos 185mm como límite
        if ($this->GetY() + $h > 185) {
            $this->AddPage($this->CurOrientation);
        }

        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h, 'F');
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function NbLines($w, $txt)
    {
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
                } else $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else $i++;
        }
        return $nl;
    }
}

// Especificamos 'Letter' para asegurar tamaño Carta
$pdf = new PDF_MC_Table('L', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10); // Márgenes de 1cm
$pdf->AddPage();

// --- ENCABEZADO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(10);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(10);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
// Posicionamos el logo más a la derecha según el nuevo ancho
$pdf->Image(LOGOCR, 235, 8, 40, 0, '', WEBSITE);
$pdf->Ln(8);

$SQL = "SELECT * FROM Rentas
INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
WHERE renta_estado = 'Activo'
ORDER BY cliente_rs ASC";
$QUERY = consultaData($SQL);

// REDISTRIBUCIÓN DE ANCHOS (Total: 259mm)
// Aprovechamos para darle más espacio al Cliente y a la columna Renta (Depto)
$pdf->SetWidths([10, 95, 46, 26, 22, 20, 20, 20]);
$pdf->aligns = ['C', 'L', 'L', 'C', 'R', 'C', 'C', 'C'];

// Render Encabezado
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(45, 45, 45);
$pdf->SetTextColor(255, 255, 255);
$cols = ['NO', 'CLIENTE', 'DEPTO / RENTA', 'MODELO', 'MENSUAL', 'ESC (I/E)', 'BN (I/E)', 'COL (I/E)'];
foreach ($cols as $i => $v) $pdf->Cell($pdf->widths[$i], 8, $v, 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 8);

$NOrow = 1;
$TOTAL = 0;
$cli_actual = 0;
$color_toggle = 0;
$incompletos = [];

foreach ($QUERY['dataFetch'] as $ROW) {
    if ($ROW['cliente_id'] != $cli_actual) {
        $color_toggle++;
        $cli_actual = $ROW['cliente_id'];
    }
    $pdf->SetFillColor(($color_toggle % 2 == 0) ? 235 : 255);

    // --- LÓGICA DE AUDITORÍA DINÁMICA ---
    $faltan = [];

    // Función interna rápida para evaluar cada categoría
    $evaluar = function ($inc, $exc, $label) use (&$faltan) {
        if ($inc <= 0 && $exc <= 0) {
            $faltan[] = "$label (INC/EXC)";
        } elseif ($inc <= 0) {
            $faltan[] = "$label (INC)";
        } elseif ($exc <= 0) {
            $faltan[] = "$label (EXC)";
        }
    };

    // Evaluaciones
    if ($ROW['renta_costo'] <= 0) $faltan[] = "COSTO";
    $evaluar($ROW['renta_inc_esc'], $ROW['renta_exc_esc'], "ESCANEO");
    $evaluar($ROW['renta_inc_bn'], $ROW['renta_exc_bn'], "B&N");

    if ($ROW['modelo_tipo'] == "Multicolor") {
        $evaluar($ROW['renta_inc_col'], $ROW['renta_exc_col'], "COLOR");
    }

    if (!empty($faltan)) {
        $incompletos[] = $ROW['cliente_rs'] . " (" . $ROW['renta_folio'] . "): " . implode(", ", $faltan);
    }

    // --- PREPARACIÓN DE DATOS PARA PDF ---
    $col_data = ($ROW['modelo_tipo'] == "Monocromatico")
        ? "---"
        : $ROW['renta_inc_col'] . " / $" . (int)$ROW['renta_exc_col'];

    $pdf->Row([
        $NOrow++,
        utf8_decode($ROW['cliente_rs']),
        utf8_decode($ROW['renta_depto']),
        $ROW['modelo_modelo'],
        "$ " . number_format($ROW['renta_costo'], 2),
        $ROW['renta_inc_esc'] . " / $" . (int)$ROW['renta_exc_esc'],
        $ROW['renta_inc_bn'] . " / $" . (int)$ROW['renta_exc_bn'],
        $col_data
    ]);

    $TOTAL += $ROW['renta_costo'];
}

// Fila de Total más ancha
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(80, 80, 80);
$pdf->SetTextColor(255, 255, 255);
$w_total_label = array_sum(array_slice($pdf->widths, 0, 4));
$pdf->Cell($w_total_label, 9, "TOTAL A RECAUDAR MENSUALMENTE: ", 1, 0, 'R', true);
$pdf->Cell($pdf->widths[4], 9, "$ " . number_format($TOTAL, 2), 1, 1, 'R', true);

// Auditoría
if (!empty($incompletos)) {
    $pdf->Ln(8);
    $pdf->SetTextColor(180, 0, 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, utf8_decode("DATOS PENDIENTES DE REVISIÓN"), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(70, 70, 70);
    foreach ($incompletos as $item) $pdf->Cell(0, 4, utf8_decode("- " . $item), 0, 1, 'L');
}

$pdf->Output('I', "Reporte_Rentas_Carta.pdf");
