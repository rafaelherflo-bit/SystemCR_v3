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
        $this->AddLink();
        $this->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(190, 3, COMPANYNAME, 0, 1, 'R');
        $this->SetFont('Arial', 'I', 6);
        $this->Cell(190, 2, dataRFC1, 0, 1, 'R');
        $this->Cell(190, 2, dataRFC2, 0, 1, 'R');
        $this->Cell(190, 2, dataRFC3, 0, 1, 'R');
        $this->Cell(190, 2, dataRFC4, 0, 1, 'R');
        $this->Ln(2);
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(170, 6, 'FECHA DE CAMBIO', 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(170, 6, "______ / ______ / ______", 0, 1, 'C');
    }
    public function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 11);
        $this->Cell(95, 6, utf8_decode('____________________________            '), 0, 0, 'C');
        $this->Cell(95, 6, utf8_decode('            ____________________________'), 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(80, 6, utf8_decode('FIRMA CLIENTE'), 0, 0, 'C');
        if ($this->page > 0) {
            $this->InFooter = false;
            // Close page
            $this->_endpage();
        }
    }
}
$pdf = new PDF();

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(5);

$pdf->SetFillColor(225, 225, 225);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, 'CAMBIO DE EQUIPO', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 14);

// Datos del Contrato.
$pdf->Cell(0, 6, 'DATOS DEL CLIENTE', 1, 1, 'C', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'RAZON SOCIAL:', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(110, 6, '', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, 'RFC:', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, '', 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'SERVICIO:', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 6, '', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, 'CONTACTO:', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(55, 6, '', 1, 1, 'C');

// $pdf->SetFont('Arial', 'B', 10);
// $pdf->Cell(25, 6, 'DIRECCION:', 1, 0, 'C');
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(165, 6, utf8_decode(""), 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 6, 'FECHA:', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(53, 6, '', 1, 0, 'C');
$pdf->Cell(36, 6, '', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 6, utf8_decode("H.INICIO:"), 1, 0, 'C', true);
$pdf->Cell(28, 6, "", 1, 0, 'L');
$pdf->Cell(15, 6, utf8_decode("H.FINAL:"), 1, 0, 'C', true);
$pdf->Cell(28, 6, "", 1, 1, 'L');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(10, 6, '', 0, 0, 'C');
$pdf->Cell(75, 6, 'EQUIPO INGRESADO', 0, 0, 'C');
$pdf->Cell(20, 6, '', 0, 0, 'C');
$pdf->Cell(75, 6, 'EQUIPO RETIRADO', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 0, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->Ln(2);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 6, 'MODELO:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(30, 6, 'CONTADORES', 1, 0, 'C');
$pdf->Cell(4, 6, '', 0, 0, 'C');
$pdf->Cell(25, 6, 'MODELO:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(30, 6, 'CONTADORES', 1, 1, 'C');

$pdf->Cell(25, 6, 'NO. SERIE:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'ESC:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 0, 'C');
$pdf->Cell(4, 6, '', 0, 0, 'C');
$pdf->Cell(25, 6, 'NO. SERIE:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'ESC:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 1, 'C');

$pdf->Cell(25, 6, 'NO. EQUIPO:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'B&N:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 0, 'C');
$pdf->Cell(4, 6, '', 0, 0, 'C');
$pdf->Cell(25, 6, 'NO. EQUIPO:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'B&N:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 1, 'C');

$pdf->Cell(25, 6, 'MOD. TONER:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'COL:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 0, 'C');
$pdf->Cell(4, 6, '', 0, 0, 'C');
$pdf->Cell(25, 6, 'MOD. TONER:', 0, 0, 'R');
$pdf->Cell(38, 6, '', 0, 0, 'C');
$pdf->Cell(10, 6, 'COL:', 1, 0, 'C');
$pdf->Cell(20, 6, '', 1, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 4, '-------------------------------------------------------------', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 4, 'DESCRIPCION DEL CAMBIO', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 4, '--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');






$pdf->Output('I', "CAMBIO DE EQUIPO.pdf");
