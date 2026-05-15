<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

$pdf = new FPDF();
$pdf->AliasNbPages();


$lectura_id = decryption($_GET['lectura_id']) ?? '';

$anio = $_GET['anio'] ?? '';
$mes = $_GET['mes'] ?? '';
$lectura = $_GET['lectura'] ?? '';
$reporte_id = decryption($_GET['reporte']) ?? '';

$impreso = false;

// 1. INTENTAR IMPRIMIR IMÁGENES FÍSICAS (Tu código actual)
if (!empty($lectura)) {
  $rutas = [
    SERVERDIR . "DocsCR/Lecturas/$anio/$mes/Formatos/$lectura",
    SERVERDIR . "DocsCR/Lecturas/$anio/$mes/PE/$lectura"
  ];

  foreach ($rutas as $ruta) {
    if (file_exists($ruta) && !is_dir($ruta)) {
      $pdf->AddPage();
      $pdf->Image($ruta, 2, 10, 210, 290);
      $impreso = true;
    }
  }
}

// 2. SI NO HUBO IMAGEN, BUSCAR EN LA TABLA historial_reportes
if (!empty($reporte_id)) {
  // Aquí debes usar tu función de consulta para traer el body_correo
  $sql = "SELECT body_correo FROM historial_reportes WHERE id = '$reporte_id'";
  $data = consultaData($sql); // Usa tu función de BD

  if ($data['numRows'] > 0) {
    $pdf->AddPage();

    // Configuramos fuente Monoespaciada (Courier) para que las columnas alineen
    $pdf->SetFont('Courier', '', 9);

    $texto = $data['dataFetch'][0]['body_correo'];

    // MultiCell respeta los saltos de línea (\n) del campo de texto
    // Usamos utf8_decode para caracteres especiales si es necesario
    $pdf->MultiCell(0, 4, utf8_decode($texto == "" ? "Sin Body" : $texto));
    $impreso = true;
  }
}

if (!$impreso) {
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 12);
  $pdf->Cell(0, 10, utf8_decode("No se encontró evidencia física ni reporte digital."), 0, 1, 'C');
}

$pdf->Output('I', "lectura.pdf");
