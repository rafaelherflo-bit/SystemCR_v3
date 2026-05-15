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
        $this->Cell(170, 6, 'FECHA', 0, 1, 'C');
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
$pdf->Cell(0, 6, 'ENTREGA DE EQUIPO', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(0, 6, 'NUEVA RENTA', 0, 1, 'C');

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
$pdf->Cell(30, 6, 'AREA:', 1, 0, 'C', true);
$pdf->Cell(70, 6, '', 1, 0, 'C');
$pdf->Cell(25, 6, 'CONTACTO:', 1, 0, 'C', true);
$pdf->Cell(65, 6, '', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, '-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(95, 8, 'DATOS DEL EQUIPO', 0, 0, 'C');
$pdf->Cell(95, 7, 'CONTADORES', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, 'MODELO:', 0, 0, 'R');
$pdf->Cell(70, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, 'ESCANEO', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'B&N', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'COLOR', 1, 1, 'C', true);

$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, 'NO. DE SERIE:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(70, 7, '', 0, 0, 'L');
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 0, 'L');
$pdf->Cell(25, 7, "", 1, 1, 'L');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, 'MOD. TONER:', 0, 0, 'R');
$pdf->Cell(45, 7, '', 0, 0, 'L');
$pdf->Cell(110, 7, "NIVELES DE CONSUMIBLES", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 8, '', 0, 0, 'C');
$pdf->Cell(25, 7, '', 0, 0, 'R');
$pdf->Cell(50, 7, '', 0, 0, 'L');
$pdf->Cell(20, 7, "K", 1, 0, 'L');
$pdf->Cell(20, 7, "Y", 1, 0, 'L');
$pdf->Cell(20, 7, "C", 1, 0, 'L');
$pdf->Cell(20, 7, "M", 1, 0, 'L');
$pdf->Cell(20, 7, "R", 1, 1, 'L');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, '-----------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 6, 'ESPECIFICACIONES DE LA RENTA', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 6, '-----------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(30, 0, 'Tipo de Renta: ', 0, 1, 'C');
$pdf->Cell(30);
$pdf->SetFont('Arial', 'I', 10);

$pdf->MultiCell(155, 4, "____________________ MENSUAL DE $ __________ +IVA, CON _____________ IMPRECIONES A B&N POR MES MAS EXCEDENTES EN $ ________ +IVA, _____________  IMPRECIONES A COLOR POR MES MAS EXCEDENTES EN $ ________ +IVA, _____________ ESCANEOS MAS EXCEDENTES EN $ ________ +IVA.", 0);

$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);

$pdf->Cell(15);
$pdf->MultiCell(160, 6, "Yo, _______________________________ declaro haber recibido el equipo multifuncional descrito en la parte superior, me comprometo a cuidarlo y utilizarlo correctamente de acuerdo a las actividades para las que son asignadas.", 0);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 0, '------------------------------------------------------', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, '----------------------------------------------------- COMENTARIOS -----------------------------------------------------', 0, 1, 'C');

$pdf->Output('I', "EntregaDeEquipoNUEVARENTA.pdf");
