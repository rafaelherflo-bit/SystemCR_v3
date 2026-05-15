<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}

$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- ENCABEZADO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 160, 10, 40, 0, '', WEBSITE);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('ALMACÉN | STOCK DE REFACCIONES'), 0, 1, 'C');
$pdf->Ln(2);

// --- 1. DICCIONARIO DE CATEGORÍAS ---
$queryCats = consultaData("SELECT * FROM CategoriasR");
$catsIndex = [];
foreach ($queryCats['dataFetch'] as $cat) {
  $catsIndex[$cat['catR_id']] = $cat['catR_nombre'];
}

// --- 2. CONSULTA ORDENADA ---
$consultaAlmacenP = consultaAlmacenP("WHERE AlmP_cat_id = 3", "ORDER BY AlmP_subcat_id ASC, AlmP_descripcion ASC");

// --- FUNCIÓN PARA HEADERS (SIN COLUMNA CATEGORÍA) ---
function imprimirHeaders($pdf)
{
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->SetFillColor(50, 50, 50);
  $pdf->SetTextColor(255);
  // Redistribución de los 196mm totales
  $pdf->Cell(30, 7, 'CODIGO', 1, 0, 'C', true);
  $pdf->Cell(35, 7, 'PROVEEDOR', 1, 0, 'C', true);
  $pdf->Cell(80, 7, 'COMPATIBILIDAD', 1, 0, 'C', true); // Columna ensanchada
  $pdf->Cell(20, 7, 'STOCK (M|R)', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'AJUSTE', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'COMPRA', 1, 1, 'C', true);
  $pdf->SetTextColor(0);
}

imprimirHeaders($pdf);

$pdf->SetFont('Arial', '', 8);
$id_cat_actual = -1;

for ($i = 0; $i < $consultaAlmacenP['numRows']; $i++) {
  $AlmP = $consultaAlmacenP['dataFetch'][$i];

  // Lógica de Ruptura de Control para el Título del Grupo
  if ($id_cat_actual != $AlmP['AlmP_subcat_id']) {
    $id_cat_actual = $AlmP['AlmP_subcat_id'];

    if ($i > 0) $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(235, 235, 235);
    $nombreCat = $catsIndex[$id_cat_actual] ?? 'OTRAS REFACCIONES';
    $pdf->Cell(195, 6, " CATEGORIA: " . utf8_decode(strtoupper($nombreCat)), 1, 1, 'L', true);
    $pdf->SetFont('Arial', '', 8);
  }

  // Desestructuración de descripción
  $partes = explode(" | ", $AlmP['AlmP_descripcion']);
  $refaccionCodigo = $partes[0] ?? 'N/A';
  $compatibilidad = $partes[1] ?? 'N/A';


  // Color de Stock
  $pdf->SetFillColor(255);
  $stockActual = $AlmP['AlmP_stock'];
  $stockMinimo = $AlmP['AlmP_stock_min'];
  // Evitamos división por cero si el stock mínimo no está configurado
  $porcentaje = ($stockMinimo > 0) ? ($stockActual / $stockMinimo) * 100 : 101;
  // 2. Aplicamos la lógica de colores por rangos
  if ($porcentaje > 100) {
    // SIN COLOR (Blanco o transparente)
    $pdf->SetFillColor(255, 255, 255);
  } elseif ($porcentaje >= 75) {
    // VERDE (75% a 100%)
    $pdf->SetFillColor(200, 255, 200);
  } elseif ($porcentaje >= 50) {
    // AMARILLO (50% a 74.9%)
    $pdf->SetFillColor(255, 255, 200);
  } elseif ($porcentaje >= 25) {
    // NARANJA (25% a 49.9%)
    $pdf->SetFillColor(255, 210, 150);
  } else {
    // ROJO (Menos de 25%)
    $pdf->SetFillColor(255, 200, 200);
  }

  // Dibujado de celdas (Sin la columna categoría interna)
  $pdf->Cell(30, 6, $refaccionCodigo, 1, 0, 'C', true, SERVERURL . 'Almacen/Refacciones/Editar/' . encryption($AlmP['AlmP_id']));
  $pdf->Cell(35, 6, substr(utf8_decode($AlmP['AlmProv_nombre']), 0, 25), 1, 0, 'C');
  $pdf->Cell(80, 6, substr(utf8_decode($compatibilidad), 0, 65), 1, 0, 'L');

  $pdf->Cell(10, 6, $stockMinimo, 1, 0, 'C');
  $pdf->Cell(10, 6, $stockActual, 1, 0, 'C', true);

  $pdf->SetFillColor(255);
  $pdf->Cell(15, 6, "", 1, 0, 'C');
  $pdf->Cell(15, 6, "", 1, 1, 'C');
}

$pdf->Output('I', "Almacen_Refacciones_" . date("d-m-Y") . ".pdf");
