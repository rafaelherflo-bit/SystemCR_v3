<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}
$SQL = "SELECT * FROM AlmacenD
        INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
        INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
        INNER JOIN AlmacenProvs ON AlmacenP.AlmP_prov_id = AlmacenProvs.AlmProv_id
        INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
        WHERE AlmDM_id = '" . decryption($_GET['iD']) . "'
        ORDER BY AlmP_descripcion ASC";
$dataFetch = consultaData($SQL)['dataFetch'];

// ----------------------------------------------------------
$AlmM_folio = $dataFetch[0]['AlmM_folio'];
$AlmM_fecha = $dataFetch[0]['AlmM_fecha'];
$AlmM_comentario = $dataFetch[0]['AlmM_comentario'];
$AlmM_tipo = $dataFetch[0]['AlmM_tipo'];
$AlmM_IVA = $dataFetch[0]['AlmM_IVA'];
// ----------------------------------------------------------
$uS_MKR_QRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $dataFetch[0]['AlmM_uS_id'])['dataFetch'][0];
$MKR_nombre = $uS_MKR_QRY['usuario_nombre'];
$MKR_apellido = $uS_MKR_QRY['usuario_apellido'];
$MKR_telefono = $uS_MKR_QRY['usuario_telefono'];
// ----------------------------------------------------------
$uS_EMP_QRY = consultaData("SELECT * FROM Usuarios WHERE usuario_id = " . $dataFetch[0]['AlmM_empleado'])['dataFetch'][0];
$EMP_nombre = $uS_EMP_QRY['usuario_nombre'];
$EMP_apellido = $uS_EMP_QRY['usuario_apellido'];
$EMP_telefono = $uS_EMP_QRY['usuario_telefono'];
// ----------------------------------------------------------
$AlmM_identificador = $dataFetch[0]['AlmM_identificador'];
if ($AlmM_tipo == 1) {
  $tituloRequi = "REQUISICION INTERNA";
  $nombreSalida = $uS_EMP_QRY['usuario_apellido'] . " " . $uS_EMP_QRY['usuario_apellido'];
  $recibeNAME = "NOMBRE DE EMPLEADO";
} else if ($AlmM_tipo == 2) {
  $tituloRequi = "ENTREGA PARA RENTA";
  $renta_SQL = "SELECT * FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE renta_id = " . $dataFetch[0]['AlmM_identificador'];
  $renta_QRY = consultaData($renta_SQL)['dataFetch'][0];
  $nombreSalida = $renta_QRY['cliente_rs'] . " | " . $renta_QRY['contrato_folio'] . "-" . $renta_QRY['renta_folio'] . " | " . $renta_QRY['renta_depto'];
  $recibeNAME = "NOMBRE DEL CLIENTE Y DEPARTAMENTO";
} else if ($AlmM_tipo == 3) {
  $tituloRequi = "NOTA DE ENTREGA";
  $cliente_QRY = consultaData("SELECT * FROM Clientes WHERE cliente_id = " . $dataFetch[0]['AlmM_identificador'])['dataFetch'][0];
  $nombreSalida = $cliente_QRY['cliente_rfc'] . " | " . $cliente_QRY['cliente_rs'];
  $recibeNAME = "NOMBRE DEL CLIENTE";
}
// ----------------------------------------------------------


class pdf extends FPDF
{
  protected $widths;
  protected $aligns;

  function SetWidths($w)
  {
    // Establecer el array de anchos de columna
    $this->widths = $w;
  }

  function SetAligns($a)
  {
    // Establecer el array de alineaciones de columna
    $this->aligns = $a;
  }

  function Row($data)
  {
    // Calcular la altura de la fila (el máximo de las alturas de MultiCell)
    $nb = 0;
    for ($i = 0; $i < count($data); $i++)
      $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
    $h = 6 * $nb; // 6 es la altura de línea de base

    // Emitir un salto de página si es necesario
    $this->CheckPageBreak($h);

    // Dibujar las celdas de la fila
    for ($i = 0; $i < count($data); $i++) {
      $w = $this->widths[$i];
      $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

      // Guardar la posición actual
      $x = $this->GetX();
      $y = $this->GetY();

      // Dibujar el borde de la celda (el borde se dibuja antes de MultiCell)
      $this->Rect($x, $y, $w, $h);

      // Imprimir el texto con MultiCell
      $this->MultiCell($w, 6, $data[$i], 0, $a); // 6 es la altura de línea

      // Poner la posición a la derecha de la celda
      $this->SetXY($x + $w, $y);
    }

    // Ir a la siguiente línea (salto de línea de la fila)
    $this->Ln($h);
  }

  function CheckPageBreak($h)
  {
    // Si la altura $h causaría un salto de página, añade una página
    if ($this->GetY() + $h > $this->PageBreakTrigger)
      $this->AddPage($this->CurOrientation);
  }

  function NbLines($w, $txt)
  {
    // Calcula el número de líneas que ocupará un MultiCell de ancho $w
    // (Esta es una función interna de FPDF, necesaria para el cálculo de altura)
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 and $s[$nb - 1] == "\n") $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ') $sep = $i;
      $l += $cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j) $i++;
        } else
          $i = $sep + 1;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else
        $i++;
    }
    return $nl;
  }
}


// ------------------------------------------- HEADER ------------------------------------------ //
$pdf = new pdf();
$pdf->AddPage();
$pdf->AddLink();
$pdf->Image(LOGOCR, 10, 2, 50, 0, '', WEBSITE);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(190, 3, COMPANYNAME, 0, 1, 'R');
$pdf->SetFont('Arial', 'I', 6);
$pdf->Cell(190, 2, dataRFC1, 0, 1, 'R');
$pdf->Cell(190, 2, dataRFC2, 0, 1, 'R');
$pdf->Cell(190, 2, dataRFC3, 0, 1, 'R');
$pdf->Cell(190, 2, dataRFC4, 0, 1, 'R');
$pdf->Ln(5);

$pdf->SetFillColor(215, 215, 215);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(45, 6, 'FECHA', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(95, 6, utf8_decode($tituloRequi), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(45, 6, "______ / ______ / ______", 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(120, 5, utf8_decode($recibeNAME), "LT", 0, 'C', TRUE);
$pdf->Cell(20, 5, '', "TR", 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, "FOLIO: ", "LTB", 0, 'R', TRUE);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 5, $AlmM_folio, "TBR", 1, 'C');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, utf8_decode($nombreSalida), "LBR", 1, 'L');
$pdf->Ln(5);
// ------------------------------------------- HEADER ------------------------------------------ //


// ------------------------------------- CABEZERA DE TABLA ------------------------------------- //
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 6, "ID", 1, 0, 'C', TRUE);
$pdf->Cell(20, 6, "CANTIDAD", 1, 0, 'C', TRUE);
$pdf->Cell(15, 6, "UDM", 1, 0, 'C', TRUE);
$pdf->Cell(100, 6, "DESCRIPCION", 1, 0, 'C', TRUE);
$pdf->Cell(20, 6, "IMPORTE", 1, 0, 'C', TRUE);
$pdf->Cell(20, 6, "MONTO", 1, 1, 'C', TRUE);
$pdf->SetFont('Arial', '', 10);
// ------------------------------------- CABEZERA DE TABLA ------------------------------------- //

// -------------------------------------- CUERPO DE TABLA -------------------------------------- //
$rows = [];
$SUBTOTAL = 0;
foreach ($dataFetch as $row) {
  $CANTIDAD = $row['AlmD_cantidad'];
  $PRECIO = $row['AlmD_precio'];

  $MONTO = $PRECIO * $CANTIDAD;
  $SUBTOTAL += $MONTO;

  if (!isset(explode(".", $MONTO)[1])) {
    $MONTO = $MONTO . ".00";
  } else if (isset(explode(".", $MONTO)[1]) && strlen(explode(".", $MONTO)[1]) < 2) {
    $MONTO = $MONTO . "0";
  }

  if ($row['AlmP_cat_id'] == 1) {
    list($codigoToner, $noParteToner, $rendimientoToner, $compatibilidadToner) = explode(" | ", $row['AlmP_descripcion']);
    $AlmP_descripcion = "TONER | " . $codigoToner . " | " . $compatibilidadToner;
  } else if ($row['AlmP_cat_id'] == 2) {
    list($codigoChip, $rendimientoChip, $compatibilidadChip) = explode(" | ", $row['AlmP_descripcion']);
    $AlmP_descripcion = "CHIP | " . $codigoChip . " | " . $compatibilidadChip;
  } else if ($row['AlmP_cat_id'] == 3) {
    list($codigoRef, $compatibilidadRef) = explode(" | ", $row['AlmP_descripcion']);
    $AlmP_descripcion = "REFACCION | " . $codigoRef . " | " . $compatibilidadRef;
  }

  $newRow = [
    $row['AlmP_codigo'],
    $CANTIDAD,
    $row['unList_unidad'],
    utf8_decode(ucfirst($AlmP_descripcion)),
    "$" . $PRECIO,
    "$" . $MONTO,
  ];
  array_push($rows, $newRow);
}


$IVA = $AlmM_IVA / 100;
$IVA = $SUBTOTAL * $IVA;
$TOTAL = $SUBTOTAL + $IVA;
if (!isset(explode(".", $SUBTOTAL)[1])) {
  $SUBTOTAL = $SUBTOTAL . ".00";
} else if (isset(explode(".", $SUBTOTAL)[1]) && strlen(explode(".", $SUBTOTAL)[1]) < 2) {
  $SUBTOTAL = $SUBTOTAL . "0";
}


// Definir los anchos de las columnas (ej: 30mm, 50mm, 30mm, 40mm)
$pdf->SetWidths(array(15, 20, 15, 100, 20, 20));
// (Opcional) Definir alineación de columnas (ej: Izquierda, Justificado, Centro, Derecha)
$pdf->SetAligns(array('C', 'C', 'C', 'L', 'L', 'L'));
foreach ($rows as $row) {
  // La función Row() de la clase extendida se encarga de usar MultiCell
  $pdf->Row($row);
}
// -------------------------------------- CUERPO DE TABLA -------------------------------------- //



// ------------------------------------------- FOOTER ------------------------------------------ //
$pdf->SetY(215);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 8, 'OBSERVACIONES:', "LT", 0, 'C', TRUE);
$pdf->Cell(100, 8, '', "TR", 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(25, 8, 'SUBTOTAL', "LT", 0, 'C', TRUE);
$pdf->Cell(25, 8, "$" . $SUBTOTAL, "TR", 1, 'L');

$pdf->Cell(140, 8, '', "LR", 0, 'L');
$pdf->Cell(25, 8, $AlmM_IVA . '% IVA', "L", 0, 'C', TRUE);
$pdf->Cell(25, 8, '$' . $IVA, "R", 1, 'L');

$pdf->Cell(140, 8, '', "LBR", 0, 'L');
$pdf->Cell(25, 8, 'TOTAL', "LB", 0, 'C', TRUE);
$pdf->Cell(25, 8, '$' . $TOTAL, "BR", 1, 'L');

$pdf->Ln(3);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(55, 9, '', "LTR", 0, 'L');
$pdf->Cell(55, 9, '', "LTR", 0, 'L');
$pdf->Cell(40, 9, '', "LTR", 0, 'L');
$pdf->Cell(40, 9, '', "LTR", 0, 'L');
$pdf->Ln();
$pdf->Cell(55, 6, 'ALMACEN', "LBR", 0, 'L');
$pdf->Cell(55, 6, 'CLIENTE', "LBR", 0, 'L');
$pdf->Cell(40, 6, 'CONTACTO', "LBR", 0, 'L');
$pdf->Cell(40, 6, 'TELEFONO', "LBR", 1, 'L');

$pdf->Ln(1);

$pdf->Cell(55, 9, '', "LTR", 0, 'L');
$pdf->Ln();
$pdf->Cell(55, 6, 'TECNICO', "LBR", 0, 'L');
// ------------------------------------------- FOOTER ------------------------------------------ //

$pdf->Output();
