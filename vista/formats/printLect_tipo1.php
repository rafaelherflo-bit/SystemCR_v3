<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

$pdf = new FPDF();

list($anio, $mes, $dia) = explode('-', $_POST['lectura_fecha']);

// ==================================================================================================================================================================== //

$rutaFormato = SERVERDIR . "DocsCR/Lecturas/" . $anio . "/" . $mes . "/Formatos" . "/" . decryption($_POST['prev_lectura_id']) . ".jpg";
$rutaPE = SERVERDIR . "DocsCR/Lecturas/" . $anio . "/" . $mes . "/PE" . "/" . decryption($_POST['prev_lectura_id']) . ".jpg";

$fileFormatoExists = file_exists($rutaFormato);
$filePEExists = file_exists($rutaPE);

if (!$fileFormatoExists && !$filePEExists) {
  echo '
    <script>
      alert("No se encontraron archivos de lectura fisica para visualizar.");
      window.close();
    </script>    
    ';
  exit();
}

if ($fileFormatoExists) {
  $pdf->AddPage();
  $pdf->Image($rutaFormato, 2, 10, 210, 290, "JPEG");
}
if ($filePEExists) {
  $pdf->AddPage();
  $pdf->Image($rutaPE, 2, 10, 210, 290, "JPEG");
}

// ==================================================================================================================================================================== //

$output = $mes . "-" . $anio . " - " . $_POST["cliente_rs"] . " (" . $_POST["contrato_folio"] . "-" . $_POST["renta_folio"] . ") " . $_POST["renta_depto"] . " - Tomar lectura.pdf";
$pdf->Output('I', $output);
