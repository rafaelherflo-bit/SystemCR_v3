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
        $this->Cell(170, 6, 'FECHA DE VISITA', 0, 1, 'C');
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

$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 8, 'SERVICIO', 0, 1, 'C');

$pdf->Ln(1); // Salto de linea.

$pdf->SetFont('Arial', 'B', 14);

// Datos del Contrato.
$pdf->Cell(0, 6, 'DATOS DEL CLIENTE', 1, 1, 'C', true);


$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'RAZON SOCIAL:', 1, 0, 'C', true);
$pdf->Cell(105, 6, "", 1, 0, 'C');
$pdf->Cell(10, 6, 'RFC:', 1, 0, 'C', true);
$pdf->Cell(45, 6, "", 1, 1, 'C');


$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'CONTACTO:', 1, 0, 'C', true);
$pdf->Cell(50, 6, '', 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 6, 'H.INICIO:', 1, 0, 'C', true);
$pdf->Cell(40, 6, '', 1, 0, 'C');
$pdf->Cell(15, 6, 'H.FINAL:', 1, 0, 'C', true);
$pdf->Cell(40, 6, '', 1, 1, 'C');

$pdf->Ln(5); // Salto de linea.

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(70, 8, 'DATOS DEL EQUIPO', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(35, 7, 'CONTADORES', 0, 0, 'C');
$pdf->Cell(25, 7, 'ESCANEO', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'B&N', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'COLOR', 1, 1, 'C', true);

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, 'MODELO:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 7, '', 0, 0, 'L');
$pdf->Cell(35, 7, 'INICIAL', 1, 0, 'C', true);
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 1, 'L');

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(25, 7, 'NO. DE SERIE:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 7, '', 0, 0, 'L');
$pdf->Cell(35, 7, 'FINAL', 1, 0, 'C', true);
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 1, 'L');

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(25, 7, 'NO. DE EQUIPO:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 7, '', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11); // ABASTECIMIENTO
$pdf->Cell(35, 7, 'NIVELES', 0, 0, 'C');
$pdf->Cell(15, 7, "K", 1, 0, 'C', true);
$pdf->Cell(15, 7, "Y", 1, 0, 'C', true);
$pdf->Cell(15, 7, "C", 1, 0, 'C', true);
$pdf->Cell(15, 7, "M", 1, 0, 'C', true);
$pdf->Cell(15, 7, "R", 1, 1, 'C', true);

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(25, 7, 'MOD. TONER:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(45, 7, '', 0, 0, 'L');
$pdf->Cell(35, 7, 'INICIAL', 1, 0, 'C', true);
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 1, 'L');

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(70, 7, '', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 7, 'FINAL', 1, 0, 'C', true);
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 0, 'L');
$pdf->Cell(15, 7, "", 1, 1, 'L');



$pdf->Ln(5); // Salto de linea.

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 0, '-----------------------------------------------------', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, '------------------------------------------------------------ REPORTE ------------------------------------------------------------', 0, 1, 'C');

$pdf->Ln(30); // Salto de linea.

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(16);
$pdf->Cell(60, 0, '-----------------------------------------------------', 0, 0, 'C');
$pdf->Cell(40, 0, '', 0, 0, 'C');
$pdf->Cell(60, 0, '-----------------------------------------------------', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, '------------------------- DIAGNOSTICO --------------------------------------- RESOLUCION -----------------------', 0, 1, 'C');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');
$pdf->Cell(105, 4, '|', 0, 1, 'R');

$pdf->Output('I', utf8_decode("RepForaneo.pdf"));
