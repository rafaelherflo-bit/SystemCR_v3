<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}

$pdf = new FPDF('P', 'mm', 'Letter'); // Tamaño Carta Vertical
$pdf->SetMargins(10, 10, 10);
$pdf->AliasNbPages();
$pdf->AddPage();

// --- ENCABEZADO FIJO ---
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(20);
$pdf->Cell(30, 6, 'FECHA', 0, 1, 'C');
$pdf->Cell(20);
$pdf->Cell(30, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Image(LOGOCR, 160, 10, 40, 0, '', WEBSITE); // Ajustado a la derecha
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('ALMACÉN | STOCK DE CHIPS'), 0, 1, 'C');
$pdf->Ln(2);

// --- FUNCIÓN PARA HEADERS REUTILIZABLES ---
function imprimirHeaders($pdf, $esColor = false)
{
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->SetFillColor(50, 50, 50); // Gris oscuro profesional
  $pdf->SetTextColor(255);

  // Anchos ajustados para sumar ~196mm (ancho útil de hoja carta)
  $pdf->Cell(20, 7, 'CODIGO', 1, 0, 'C', true);
  $pdf->Cell(20, 7, 'RENDI', 1, 0, 'C', true);
  $pdf->Cell(25, 7, 'PROV', 1, 0, 'C', true);

  if ($esColor) {
    $pdf->Cell(70, 7, 'COMPATIBILIDAD', 1, 0, 'C', true);
    $pdf->Cell(10, 7, 'TIPO', 1, 0, 'C', true);
  } else {
    $pdf->Cell(80, 7, 'COMPATIBILIDAD', 1, 0, 'C', true);
  }

  $pdf->Cell(20, 7, 'STOCK (M|R)', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'AJUSTE', 1, 0, 'C', true);
  $pdf->Cell(15, 7, 'COMPRA', 1, 1, 'C', true);

  $pdf->SetTextColor(0);
  $pdf->SetFont('Arial', '', 8);
}

// ==========================================
// 1. SECCIÓN MONOCROMÁTICO
// ==========================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 8, "CHIPS MONOCROMATICOS", 0, 1, 'L');
imprimirHeaders($pdf, false);

$consultaMono = consultaAlmacenP("WHERE AlmP_cat_id = 2 AND AlmP_subcat_id = 0", "ORDER BY AlmP_descripcion ASC");
for ($i = 0; $i < $consultaMono['numRows']; $i++) {
  $AlmP = $consultaMono['dataFetch'][$i];
  list($chipCodigo, $chipRrendimiento, $chipCompatibilidad) = explode(" | ", $AlmP['AlmP_descripcion']);

  $pdf->Cell(20, 5, $chipCodigo, 1, 0, 'C', false, SERVERURL . 'Almacen/Chips/Editar/' . encryption($AlmP['AlmP_id']));
  $pdf->Cell(20, 5, $chipRrendimiento, 1, 0, 'C');

  // -------------------------------------- Proveedor KYOCERA -------------------------------------
  if ($AlmP['AlmProv_nombre'] == "KYOCERA") {
    $pdf->SetFillColor(50); // Gris oscuro profesional
    $pdf->SetTextColor(255);
  } else {
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
  }
  $pdf->Cell(25, 5, substr(utf8_decode($AlmP['AlmProv_nombre']), 0, 15), 1, 0, 'C', true);
  $pdf->SetFillColor(255);
  $pdf->SetTextColor(0);
  // ----------------------------------------------------------------------------------------------

  $pdf->Cell(80, 5, substr(utf8_decode($chipCompatibilidad), 0, 50), 1, 0, 'L');

  // --------------------------------------- Color de Stock ---------------------------------------
  $pdf->SetFillColor(255);
  $stockMinimo = $AlmP['AlmP_stock_min'];
  $stockActual = $AlmP['AlmP_stock'];
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
  $pdf->Cell(10, 5, $stockMinimo, 1, 0, 'C');
  $pdf->Cell(10, 5, $stockActual, 1, 0, 'C', true);
  $pdf->SetFillColor(255);
  // --------------------------------------- Color de Stock ---------------------------------------

  $pdf->SetFillColor(255);
  $pdf->Cell(15, 5, "", 1, 0, 'C');
  $pdf->Cell(15, 5, "", 1, 1, 'C');
}

$pdf->Ln(8);

// ==========================================
// 2. SECCIÓN COLOR
// ==========================================
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 8, "CHIPS MULTICOLOR", 0, 1, 'L');
imprimirHeaders($pdf, true);

$substr = "";
$firsRow = 0;
$consultaColor = consultaAlmacenP("WHERE AlmP_cat_id = 2 AND AlmP_subcat_id != 0", "ORDER BY AlmP_descripcion ASC, AlmP_subcat_id ASC");

for ($i = 0; $i < $consultaColor['numRows']; $i++) {
  $AlmP = $consultaColor['dataFetch'][$i];
  list($chipCodigo, $chipRrendimiento, $chipCompatibilidad) = explode(" | ", $AlmP['AlmP_descripcion']);

  // Configuración de Colores de Tipo
  $arrayColorL = [
    1 => ["K", 230, 230, 230], // Negro
    2 => ["M", 255, 210, 210], // Magenta
    3 => ["C", 210, 240, 255], // Cyan
    4 => ["Y", 255, 255, 210], // Amarillo
  ];
  $tipoConfig = $arrayColorL[$AlmP['AlmP_subcat_id']] ?? ["?", 255, 255, 255];
  $pdf->SetFillColor($tipoConfig[1], $tipoConfig[2], $tipoConfig[3]);

  // Separador visual entre modelos de toner (ej: cambia de TN310 a TN311)
  $current_base = substr($chipCodigo, 0, -1);
  if ($firsRow != 0 && $substr != $current_base) {
    $pdf->Cell(196, 1, "", 0, 1); // Pequeño espacio en blanco
  }
  $substr = $current_base;

  // Fila de datos
  $pdf->Cell(20, 5, $chipCodigo, 1, 0, 'C', true, SERVERURL . 'Almacen/Chips/Editar/' . encryption($AlmP['AlmP_id']));
  $pdf->Cell(20, 5, $chipRrendimiento, 1, 0, 'C', true);

  // -------------------------------------- Proveedor KYOCERA -------------------------------------
  if ($AlmP['AlmProv_nombre'] == "KYOCERA") {
    $pdf->SetFillColor(50); // Gris oscuro profesional
    $pdf->SetTextColor(255);
  } else {
    $pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
  }
  $pdf->Cell(25, 5, substr(utf8_decode($AlmP['AlmProv_nombre']), 0, 15), 1, 0, 'C', true);
  $pdf->SetFillColor(255);
  $pdf->SetTextColor(0);
  // ----------------------------------------------------------------------------------------------

  $tipoConfig = $arrayColorL[$AlmP['AlmP_subcat_id']] ?? ["?", 255, 255, 255];
  $pdf->SetFillColor($tipoConfig[1], $tipoConfig[2], $tipoConfig[3]);
  $pdf->Cell(70, 5, substr(utf8_decode($chipCompatibilidad), 0, 42), 1, 0, 'L', true);
  $pdf->Cell(10, 5, $tipoConfig[0], 1, 0, 'C', true);

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

  $pdf->Cell(10, 5, $stockMinimo, 1, 0, 'C');
  $pdf->Cell(10, 5, $stockActual, 1, 0, 'C', true);

  $pdf->SetFillColor(255);
  $pdf->Cell(15, 5, "", 1, 0, 'C');
  $pdf->Cell(15, 5, "", 1, 1, 'C');

  $firsRow++;
}

$pdf->Output('I', "Almacen_Toner_" . date("d-m-Y") . ".pdf");
