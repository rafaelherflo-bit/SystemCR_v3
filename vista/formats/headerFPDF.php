<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}

class PDF extends FPDF
{
    public function Header()
    {
        $ziseHeader = (orientacionPDF == "Horizontal") ? 280 : 190;
        $this->AddLink();
        $this->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($ziseHeader, 3, COMPANYNAME, 0, 1, 'R');
        $this->SetFont('Arial', 'I', 6);
        $this->Cell($ziseHeader, 2, dataRFC1, 0, 1, 'R');
        $this->Cell($ziseHeader, 2, dataRFC2, 0, 1, 'R');
        $this->Cell($ziseHeader, 2, dataRFC3, 0, 1, 'R');
        $this->Cell($ziseHeader, 2, dataRFC4, 0, 1, 'R');
        $this->Ln(2);
        if (fechaPDF) {
            $this->Ln(5);
            $this->SetFont('Arial', '', 10);
            $this->Cell(80);
            $this->Cell(170, 6, 'FECHA', 0, 1, 'C');
            $this->Cell(80);
            $this->Cell(170, 6, "______ / ______ / ______", 0, 1, 'C');
        }
    }
    public function Footer()
    {
        if (firmaPDF) {
            $this->SetY(-25);
            $this->SetFont('Arial', 'I', 11);
            $this->Cell(95, 6, utf8_decode('____________________________            '), 0, 0, 'C');
            $this->Cell(95, 6, utf8_decode('            ____________________________'), 0, 1, 'C');
            $this->Ln(2);
            $this->Cell(80, 6, utf8_decode('Nombre y Firma de Cliente'), 0, 0, 'C');
            if ($this->page > 0) {
                $this->InFooter = false;
                // Close page
                $this->_endpage();
            }
        }
    }
}

if (orientacionPDF == "Horizontal") {
    $pdf = new PDF('L');
} else {
    $pdf = new PDF();
}

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(5);

function basicInfo($pdf, $title, $subtitle, $cliente_rs = "", $cliente_rfc = "", $renta_depto = "", $zona_nombre = "", $renta_contacto = "", $renta_telefono = "", $MODELO = "", $EQUIPO = "")
{
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(70, 4, $title, 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(190, 5, $subtitle, 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'RAZON SOCIAL:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $cliente_rs, 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'RFC:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $cliente_rfc, 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'DEPARTAMENTO:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $renta_depto, 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'ZONA:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $zona_nombre, 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'CONTACTO:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $renta_contacto, 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'TELEFONO:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $renta_telefono, 1, 1, 'C');

    $pdf->Ln(4);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 5, 'DATOS DEL EQUIPO', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'MODELO:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $MODELO, 1, 1, 'C');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 5, 'NO. EQUIPO:', 1, 0, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(140, 5, $EQUIPO, 1, 1, 'C');
}
