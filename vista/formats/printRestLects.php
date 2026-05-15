<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}

// 1. Sanitización básica de inputs
$zona_id = (int)$_GET['zona_id'];
$custom_mes = (int)$_GET['custom_mes'];
$custom_anio = (int)$_GET['custom_anio'];

$pdf = new FPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

/* 2. MEJORA DE SQL: 
  Usamos LEFT JOIN para saber si existe la lectura en una sola consulta.
  Si 'lectura_id' es NULL, significa que no hay lectura para ese mes/año.
*/
$sqlRentas = "SELECT R.*, C.*, Cl.*, Z.zona_nombre, E.*, M.*, L.lectura_id 
    FROM Rentas R
    INNER JOIN Contratos C ON R.renta_contrato_id = C.contrato_id
    INNER JOIN Clientes Cl ON C.contrato_cliente_id = Cl.cliente_id
    INNER JOIN Zonas Z ON R.renta_zona_id = Z.zona_id
    INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
    INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
    LEFT JOIN Lecturas L ON L.lectura_renta_id = R.renta_id 
        AND MONTH(L.lectura_fecha) = $custom_mes 
        AND YEAR(L.lectura_fecha) = $custom_anio
    WHERE R.renta_estado = 'Activo'
    AND R.renta_zona_id = '$zona_id' 
    ORDER BY C.contrato_folio ASC, R.renta_folio ASC";

$resRentas = consultaData($sqlRentas);

// Validación de datos
if ($resRentas['numRows'] == 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "No se encontraron registros.", 0, 1, 'C');
    $pdf->Output('I', "Lecturas.pdf");
    exit();
}

// Encabezado
$pdf->SetFont('Arial', 'B', 18);
$titulo = "Lecturas de " . $resRentas['dataFetch'][0]['zona_nombre'] . " - " . $custom_mes . "/" . $custom_anio;
$pdf->Cell(0, 8, strtoupper(utf8_decode($titulo)), 0, 1, 'C');
$pdf->Ln(5);

// Cabecera de Tabla
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230); // Color gris claro para encabezado
$pdf->Cell(25, 7, utf8_decode("FOLIO"), 1, 0, 'C', true);
$pdf->Cell(40, 7, utf8_decode("DEPARTAMENTO"), 1, 0, 'C', true);
$pdf->Cell(95, 7, utf8_decode("RAZON SOCIAL"), 1, 0, 'C', true); // Ajustado ancho
$pdf->Cell(32, 7, utf8_decode("RFC"), 1, 1, 'C', true);

// 3. Iteración optimizada
$pdf->SetFont('Arial', '', 9);

foreach ($resRentas['dataFetch'] as $resRenta) {
    
    // Si lectura_id existe, pintamos la fila (Ya no consultamos a la DB aquí)
    if (is_null($resRenta['lectura_id'])) {
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
    } else {
        $pdf->SetFillColor(128, 128, 128);
        $pdf->SetTextColor(255, 255, 255);
    }

    $pdf->Cell(25, 6, utf8_decode($resRenta['contrato_folio'] . '-' . $resRenta['renta_folio']), 1, 0, 'C', true);
    $pdf->Cell(40, 6, utf8_decode($resRenta['renta_depto']), 1, 0, 'C', true);
    $pdf->Cell(95, 6, utf8_decode($resRenta['cliente_rs']), 1, 0, 'L', true); // Alineado a la izquierda para nombres largos
    $pdf->Cell(32, 6, utf8_decode($resRenta['cliente_rfc']), 1, 1, 'C', true);
}

$pdf->Output('I', "Lecturas_Zona_".$zona_id.".pdf");