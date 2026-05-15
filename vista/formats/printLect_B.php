<?php
define('orientacionPDF', 'Vertical');
define('fechaPDF', TRUE);
define('firmaPDF', FALSE);

require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/formats/printLect_header.php';

// Preparar datos para headerPDF
$data = array(
  'cliente_rs' => '',
  'cliente_rfc' => '',
  'CFDI_codigo' => '',
  'CFDI_descripcion' => '',
  'cliente_cp' => '',
  'regFis_codigo' => '',
  'regFis_descripcion' => '',
  'renta_depto' => '',
  'renta_telefono' => '',
  'renta_contacto' => '',
  'modelo_linea' => '',
  'modelo_modelo' => '',
  'equipo_serie' => '',
  'curr_lectura_fecha' => date('Y-m-d'),
  'modelo_tipo' => 'Multicolor'
);

// Llamar a headerPDF con los parámetros especificados
headerPDF($pdf, $data, TRUE, FALSE);

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
$pdf->Cell(190, 6, 'PROCESO DE _________________________ A _________________________', 0, 1, 'C');
$pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(31);
$pdf->Cell(31, 6, "", 0, 0, 'C');
$pdf->Cell(31, 6, "", 0, 0, 'C');
$pdf->Cell(31, 6, "", 0, 0, 'C');
$pdf->Cell(31, 6, 'PROSC. TOTAL', 1, 1, 'C');

$pdf->SetFont('Arial', '', 13);
$pdf->Cell(31);
$pdf->Cell(31, 8, 'ESCANEO', 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 1, 'C');

$pdf->Cell(31);
$pdf->Cell(31, 8, 'B&N', 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 1, 'C');

$pdf->Cell(31);
$pdf->Cell(31, 8, 'COLOR', 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 0, 'C');
$pdf->Cell(31, 8, "", 1, 1, 'C');

$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 1, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');
$pdf->Cell(95, 6, 'COMENTARIOS', 0, 0, 'C');
$pdf->Cell(95, 6, 'ABASTECIMIENTO', 0, 1, 'C');
$pdf->Cell(190, 0, '-----------------------------------------------------------------------------------------------------------------------------------', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(95);
$pdf->Cell(95, 7, "CANTIDAD EN EQUIPO", 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(95);
$pdf->Cell(19, 7, '%K', 1, 0, 'R');
$pdf->Cell(19, 7, '%M', 1, 0, 'R');
$pdf->Cell(19, 7, '%C', 1, 0, 'R');
$pdf->Cell(19, 7, '%Y', 1, 0, 'R');
$pdf->Cell(19, 7, '%R', 1, 1, 'R');
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(95);
$pdf->Cell(95, 7, "RESERVA EN STOCK", 0, 1, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(95);
$pdf->Cell(19, 7, 'K', 1, 0, 'L');
$pdf->Cell(19, 7, 'M', 1, 0, 'L');
$pdf->Cell(19, 7, 'C', 1, 0, 'L');
$pdf->Cell(19, 7, 'Y', 1, 0, 'L');
$pdf->Cell(19, 7, 'R', 1, 1, 'L');


$pdf->SetY(260);
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(95, 5, utf8_decode('____________________________            '), 0, 0, 'C');
$pdf->Cell(95, 5, utf8_decode('            ____________________________'), 0, 1, 'C');
$pdf->Cell(110);
$pdf->Cell(80, 3, utf8_decode('FIRMA CLIENTE'), 0, 0, 'C');


$pdf->Output('I', "Toma de lectura.pdf");
