<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}

class PDF_MC_Table extends FPDF
{
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

$pdf = new PDF_MC_Table('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// Encabezado
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 230, 5, 40, 0, '', WEBSITE);
$pdf->Ln(2);

$SQL = "SELECT * FROM Rentas R
        INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id 
        INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id 
        INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id 
        INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id 
        WHERE R.renta_estado = 'Activo' ORDER BY Cl.cliente_rs ASC";
$QUERY = consultaData($SQL);

// --- AJUSTE DE COLUMNAS ---
// Agregamos dos columnas de 8mm al inicio (después de NO)
// Reducimos ligeramente la columna de CLIENTE y DEPTO para no salirnos del margen
$w = [8, 8, 8, 35, 25, 100, 48, 7, 25];

// Encabezados
$pdf->SetFont('Arial', 'B', 7.5); // Fuente ligeramente más pequeña para que quepa todo
$pdf->SetFillColor(240, 240, 240);
$headers = ['NO', 'C1', 'C2', 'EMISOR', 'FOLIO', 'CLIENTE', 'DEPTO', 'T', 'RENTA'];
for ($i = 0; $i < count($headers); $i++) {
    $pdf->Cell($w[$i], 6, $headers[$i], 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
$TOTAL = 0;
$count = 1;

$nCol = 255;
$no = 1;
$cli = 0;
foreach ($QUERY['dataFetch'] as $ROW) {
    // ---- INICIO ---- ALTERNA COLOR DEPENDIENDO DE ID CLIENTE ---- INICIO ---- //
    if ($ROW['cliente_id'] != $cli) {
        $no++;
    }
    $cli = $ROW['cliente_id'];
    if ($no % 2 === 0) {
        $nCol = 225;
    } else {
        $nCol = 255;
    }
    $pdf->SetFillColor($nCol, $nCol, $nCol);
    // ---- FIN ---- ALTERNA COLOR DEPENDIENDO DE ID CLIENTE ---- FIN ---- //

    $emisor = ($ROW['cliente_emiFact'] == 1) ? "RENAN ARMANDO" : "MIMI FLORES";
    $folio = $ROW['contrato_folio'] . "-" . $ROW['renta_folio'];
    $cliente = utf8_decode($ROW['cliente_rs']);
    $depto = utf8_decode($ROW['renta_depto']);
    $tipo = ($ROW['modelo_tipo'] == "Monocromatico") ? "M" : "C";
    $monto = "$ " . number_format($ROW['renta_costo'], 2);
    $TOTAL += $ROW['renta_costo'];

    // CALCULO DE ALTURA (Basado en la columna de Cliente que ahora es el índice 5)
    $lineas = $pdf->NbLines($w[5], $cliente);
    $alto_linea = 6;
    $h = max($lineas * $alto_linea, 5);

    if (($pdf->GetY() + $h) > ($pdf->GetPageHeight() - 20)) {
        $pdf->AddPage('L');
        $pdf->SetFont('Arial', 'B', 7.5);
        $pdf->SetFillColor(240, 240, 240);
        for ($i = 0; $i < count($headers); $i++) {
            $pdf->Cell($w[$i], 5, $headers[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Dibujar columnas iniciales
    $pdf->Cell($w[0], $h, $count++, 1, 0, 'C', true);
    $pdf->Cell($w[1], $h, '', 1, 0, 'C', true); // Nueva columna 1
    $pdf->Cell($w[2], $h, '', 1, 0, 'C', true); // Nueva columna 2
    $pdf->Cell($w[3], $h, $emisor, 1, 0, 'C', true);
    $pdf->Cell($w[4], $h, $folio, 1, 0, 'C', true);

    // MULTICELL PARA EL CLIENTE
    $pdf->MultiCell($w[5], $alto_linea, $cliente, 1, 'L', true);

    // Reposicionar después de MultiCell
    // Sumamos los anchos de las primeras 6 columnas (índices 0 al 5)
    $pdf->SetXY($x + array_sum(array_slice($w, 0, 6)), $y);

    $pdf->Cell($w[6], $h, $depto, 1, 0, 'L', true);
    $pdf->Cell($w[7], $h, $tipo, 1, 0, 'C', true);
    $pdf->Cell($w[8], $h, $monto, 1, 1, 'R', true);
}

// Total
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(60, 60, 60);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(array_sum(array_slice($w, 0, 8)), 7, "TOTAL MENSUAL ", 1, 0, 'R', true);
$pdf->Cell($w[8], 7, "$ " . number_format($TOTAL, 2), 1, 1, 'R', true);

$pdf->Output('I', "Rentas_" . date("d-m-Y") . ".pdf");
