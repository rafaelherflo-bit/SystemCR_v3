<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();

class PDF_MC_Stock extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w) { $this->widths = $w; }

    function Row($data, $fill = false, $specificColors = [])
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;

        // Salto de página automático para tamaño Carta Vertical (279mm alto)
        if ($this->GetY() + $h > 250) $this->AddPage();

        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            $x = $this->GetX();
            $y = $this->GetY();

            // Aplicar color específico si existe para esta celda
            if (isset($specificColors[$i])) {
                $c = $specificColors[$i];
                $this->SetFillColor($c[0], $c[1], $c[2]);
                $this->Rect($x, $y, $w, $h, 'F');
            } elseif ($fill) {
                $this->Rect($x, $y, $w, $h, 'F');
            }

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
        $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") { $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue; }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) { if ($i == $j) $i++; } else $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else $i++;
        }
        return $nl;
    }
}

$pdf = new PDF_MC_Stock('P', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// --- ENCABEZADO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 155, 10, 45, 0, '', WEBSITE);
$pdf->Ln(10);

// --- CONFIGURACIÓN DE TABLA ---
// Total ancho carta: 215.9mm - 20mm margenes = 195.9mm disponibles
$pdf->SetWidths([22, 28, 18, 55, 23, 16, 17, 17]);
$pdf->aligns = ['C', 'C', 'C', 'L', 'C', 'C', 'C', 'C'];

function imprimirHeaders($pdf) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(60, 60, 60);
    $pdf->SetTextColor(255);
    $cols = ['N. PARTE', 'CÓDIGO', 'RENDI', 'COMPATIBILIDAD', 'TIPO', 'STOCK', 'AJUSTE', 'COMPRA'];
    foreach ($cols as $i => $v) $pdf->Cell($pdf->widths[$i], 7, $v, 1, 0, 'C', true);
    $pdf->Ln();
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 8);
}

// 1. SECCIÓN MONOCROMÁTICO
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, "INVENTARIO TONER MONOCROMATICO", 0, 1, 'L');
imprimirHeaders($pdf);

$sqlMono = "SELECT * FROM Toners INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id WHERE toner_tipo = 0 AND toner_estado = 'Activo' ORDER BY toner_codigo ASC";
$queryMono = consultaData($sqlMono);

foreach ($queryMono['dataFetch'] as $row) {
    // Calculo de stock
    $et = consultaData("SELECT SUM(tonerR_cant) AS t FROM TonersRegistrosE WHERE tonerR_toner_id = " . $row['toner_id'])['dataFetch'][0]['t'] ?? 0;
    $st = consultaData("SELECT SUM(tonerRO_cantidad) AS t FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $row['toner_id'])['dataFetch'][0]['t'] ?? 0;
    $stock = $et - $st;

    $pdf->SetFillColor(255);
    $pdf->Row([
        $row['toner_parte'],
        $row['toner_codigo'],
        $row['toner_rendi'],
        utf8_decode($row['toner_comp']),
        "MONO",
        $stock,
        "",
        ""
    ]);
}

$pdf->Ln(10);

// 2. SECCIÓN COLOR
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, "INVENTARIO TONER COLOR", 0, 1, 'L');
imprimirHeaders($pdf);

$sqlColor = "SELECT * FROM Toners INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id WHERE toner_tipo != 0 AND toner_estado = 'Activo' ORDER BY toner_codigo ASC, toner_tipo ASC";
$queryColor = consultaData($sqlColor);

$last_base_code = "";

foreach ($queryColor['dataFetch'] as $row) {
    $et = consultaData("SELECT SUM(tonerR_cant) AS t FROM TonersRegistrosE WHERE tonerR_toner_id = " . $row['toner_id'])['dataFetch'][0]['t'] ?? 0;
    $st = consultaData("SELECT SUM(tonerRO_cantidad) AS t FROM TonersRegistrosS WHERE tonerRO_toner_id = " . $row['toner_id'])['dataFetch'][0]['t'] ?? 0;
    $stock = $et - $st;

    // Espaciado visual entre grupos de toner
    $current_base_code = substr($row['toner_codigo'], 0, -3);
    if ($last_base_code != "" && $last_base_code != $current_base_code) {
        $pdf->Cell(array_sum($pdf->widths), 2, "", "T", 1); // Línea divisoria
    }
    $last_base_code = $current_base_code;

    // Colores de tipo
    $typeColor = [255, 255, 255];
    $typeName = "";
    switch($row['toner_tipo']) {
        case 1: $typeColor = [230, 230, 230]; $typeName = "NEGRO"; break;
        case 2: $typeColor = [255, 200, 200]; $typeName = "MAGENTA"; break;
        case 3: $typeColor = [200, 230, 255]; $typeName = "CYAN"; break;
        case 4: $typeColor = [255, 255, 200]; $typeName = "AMARILLO"; break;
    }

    // Color de alerta por stock
    $stockColors = [255, 255, 255];
    if ($stock == 0) $stockColors = [255, 210, 210]; // Rojo claro
    elseif ($stock <= 2) $stockColors = [255, 250, 210]; // Amarillo claro

    $pdf->SetFillColor(255);
    $pdf->Row(
        [
            $row['toner_parte'],
            $row['toner_codigo'],
            $row['toner_rendi'],
            utf8_decode($row['toner_comp']),
            $typeName,
            $stock,
            "",
            ""
        ], 
        false, 
        [4 => $typeColor, 5 => $stockColors] // Aplicar colores solo a Tipo y Stock
    );
}

$pdf->Output('I', "Stock_Toners_" . date("d-m-Y") . ".pdf");