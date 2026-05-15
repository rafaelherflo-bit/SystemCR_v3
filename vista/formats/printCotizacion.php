<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}


// ================================ | DATOS DE COTIZADOR MAIN | ================================ //
$ID = decryption($_POST['encIDcotM']);
$QRY = consultaData("SELECT * FROM cotizadorM WHERE cotM_id = '$ID'");
if ($QRY['numRows'] == 0 || $QRY['numRows'] >= 2) {
  echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
} else {
  define("dataM", $QRY['dataFetch'][0]);
  // =========================================================================================== //


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

  $pdf = new pdf();

  $pdf->AliasNbPages();
  $pdf->AddPage();

  // ------------------------------------------- HEADER ------------------------------------------ //
  $pdf->AddLink();
  $pdf->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(190, 3, COMPANYNAME, 0, 1, 'R');
  $pdf->SetFont('Arial', 'I', 6);
  $pdf->Cell(190, 2, dataRFC1, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC2, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC3, 0, 1, 'R');
  $pdf->Cell(190, 2, dataRFC4, 0, 1, 'R');
  $pdf->Ln(2);

  $pdf->SetFont('Arial', 'B', 18);
  $pdf->Cell(190, 8, "COTIZACION", 0, 1, 'C');

  $pdf->Ln(5);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(120, 6, utf8_decode("DATOS DEL CLIENTE"), 1, 0, 'C');
  $pdf->Cell(10);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(60, 6, utf8_decode("FOLIO"), 1, 1, 'C');

  $pdf->Cell(40, 6, utf8_decode("RAZON SOCIAL"), 1, 0, 'C');
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(80, 6, utf8_decode(dataM['cotM_cliRS']), 1, 0, 'C');
  $pdf->Cell(10);
  $pdf->Cell(60, 6, utf8_decode(dataM['cotM_folio']), 1, 1, 'C');

  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(40, 6, utf8_decode("RFC"), 1, 0, 'C');
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(80, 6, utf8_decode(dataM['cotM_cliRFC']), 1, 0, 'C');
  $pdf->Cell(10);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(60, 6, utf8_decode("FECHA DE EMISION"), 1, 1, 'C');

  $pdf->Cell(130);
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(60, 6, utf8_decode(strtoupper(dateFormat(dataM['cotM_fecha'], "completa"))), 1, 1, 'C');

  $pdf->Cell(130);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(60, 6, utf8_decode("VALIDA HASTA"), 1, 1, 'C');

  // Calcular la fecha límite (fecha de registro + 30 días)
  $fecha_registro = new DateTime(dataM['cotM_fecha']);
  $fecha_limite = (clone $fecha_registro)->modify("+29 days")->format('Y-m-d H:m:s');

  $pdf->Cell(130);
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(60, 6, utf8_decode(strtoupper(dateFormat($fecha_limite, "completa"))), 1, 1, 'C');

  $pdf->Ln(2);
  // ------------------------------------------- HEADER ------------------------------------------ //



  // ============================== | DATOS DE COTIZADOR DETALLES | ============================== //
  $SQL = "SELECT * FROM cotizadorD
          INNER JOIN AlmacenP ON cotizadorD.cotD_prod_id = AlmacenP.AlmP_id
          INNER JOIN unidadesList ON AlmacenP.AlmP_unidadM = unidadesList.unList_id
          WHERE cotD_cotM_id = '$ID'";
  $QRY = consultaData($SQL);
  if ($QRY['numRows'] == 0 || $QRY['numRows'] == 0) {
    echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
  } else {
    $datasD = $QRY['dataFetch'];
    // =========================================================================================== //

    // ------------------------------------- CABEZERA DE TABLA ------------------------------------- //
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(215, 215, 215);
    $pdf->Cell(15, 6, utf8_decode("CLAVE"), 1, 0, 'C', true);
    $pdf->Cell(15, 6, utf8_decode("CANT"), 1, 0, 'C', true);
    $pdf->Cell(74, 6, utf8_decode("DESCRIPCION"), 1, 0, 'C', true);
    $pdf->Cell(20, 6, utf8_decode("UNIDAD"), 1, 0, 'C', true);
    $pdf->Cell(23, 6, utf8_decode("IMPORTE"), 1, 0, 'C', true);
    $pdf->Cell(20, 6, utf8_decode("MONTO"), 1, 0, 'C', true);
    $pdf->Cell(23, 6, utf8_decode("DESCUENTO"), 1, 1, 'C', true);
    // ------------------------------------- CABEZERA DE TABLA ------------------------------------- //
    $totalDESCUENTO = 0;
    $SUBTOTAL = 0;
    $rows = [];
    // -------------------------------------- CUERPO DE TABLA -------------------------------------- //
    $pdf->SetFont('Arial', '', 9);
    foreach ($datasD as $row) {

      $DESCUENTO = $row['cotD_descuento'];
      $totalDESCUENTO += $DESCUENTO;
      $CANTIDAD = $row['cotD_cantidad'];
      $PRECIO = $row['cotD_monto'];
      $MONTO = $PRECIO * $CANTIDAD;
      $SUBTOTAL += $MONTO;

      if (!isset(explode(".", $MONTO)[1])) {
        $MONTO = $MONTO . ".00";
      } else if (isset(explode(".", $MONTO)[1]) && strlen(explode(".", $MONTO)[1]) < 2) {
        $MONTO = $MONTO . "0";
      }

      if (!isset(explode(".", $PRECIO)[1])) {
        $PRECIO = $PRECIO . ".00";
      } else if (isset(explode(".", $PRECIO)[1]) && strlen(explode(".", $PRECIO)[1]) < 2) {
        $PRECIO = $PRECIO . "0";
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
      } else if ($row['AlmP_cat_id'] == 4) {
        $AlmP_descripcion = "SERVICIO | " . $row['AlmP_descripcion'];
      } else if ($row['AlmP_cat_id'] == 5) {
        $AlmP_descripcion = "EQUIPO | " . $row['AlmP_descripcion'];
      } else if ($row['AlmP_cat_id'] == 6) {
        $AlmP_descripcion = "OTRO | " . $row['AlmP_descripcion'];
      }

      $newRow = [
        $row['AlmP_codigo'],
        $CANTIDAD,
        utf8_decode($AlmP_descripcion),
        $row['unList_unidad'],
        "$" . $PRECIO,
        "$" . $MONTO,
        "$" . $DESCUENTO,
      ];
      array_push($rows, $newRow);
    }


    // Definir los anchos de las columnas (ej: 30mm, 50mm, 30mm, 40mm)
    $pdf->SetWidths(array(15, 15, 74, 20, 23, 20, 23));
    // (Opcional) Definir alineación de columnas (ej: Izquierda, Justificado, Centro, Derecha)
    $pdf->SetAligns(array('C', 'C', 'L', 'C', 'L', 'L', 'L'));
    foreach ($rows as $row) {
      // La función Row() de la clase extendida se encarga de usar MultiCell
      $pdf->Row($row);
    }


    $pdf->Ln(5);




    $IVA = dataM['cotM_IVA'] / 100;
    $IVA = $SUBTOTAL * $IVA;
    $TOTAL = $SUBTOTAL + $IVA;
    $TOTAL -= $totalDESCUENTO;

    if (!isset(explode(".", $SUBTOTAL)[1])) {
      $SUBTOTAL = $SUBTOTAL . ".00";
    } else if (isset(explode(".", $SUBTOTAL)[1]) && strlen(explode(".", $SUBTOTAL)[1]) < 2) {
      $SUBTOTAL = $SUBTOTAL . "0";
    }

    if (!isset(explode(".", $totalDESCUENTO)[1])) {
      $totalDESCUENTO = $totalDESCUENTO . ".00";
    } else if (isset(explode(".", $totalDESCUENTO)[1]) && strlen(explode(".", $totalDESCUENTO)[1]) < 2) {
      $totalDESCUENTO = $totalDESCUENTO . "0";
    }

    if (!isset(explode(".", $IVA)[1])) {
      $IVA = $IVA . ".00";
    } else if (isset(explode(".", $IVA)[1]) && strlen(explode(".", $IVA)[1]) < 2) {
      $IVA = $IVA . "0";
    }

    if (!isset(explode(".", $TOTAL)[1])) {
      $TOTAL = $TOTAL . ".00";
    } else if (isset(explode(".", $TOTAL)[1]) && strlen(explode(".", $TOTAL)[1]) < 2) {
      $TOTAL = $TOTAL . "0";
    }

    $pdf->Cell(130);
    $pdf->SetFillColor(215, 215, 215);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 6, utf8_decode("SUBTOTAL"), 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 6, "$" . $SUBTOTAL, 1, 1, 'L', true);

    $pdf->Cell(130);
    $pdf->SetFillColor(215, 215, 215);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 6, utf8_decode("DESCUENTO"), 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 6, "$" . $totalDESCUENTO, 1, 1, 'L', true);

    $pdf->Cell(130);
    $pdf->SetFillColor(215, 215, 215);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 6, utf8_decode(dataM['cotM_IVA'] . "% IVA "), 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 6, "$" . $IVA, 1, 1, 'L', true);

    $pdf->Cell(130);
    $pdf->SetFillColor(215, 215, 215);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 6, utf8_decode("TOTAL"), 1, 0, 'C', true);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(25, 6, "$" . $TOTAL, 1, 1, 'L', true);
  }
  // -------------------------------------- CUERPO DE TABLA -------------------------------------- //


  // ------------------------------------------- FOOTER ------------------------------------------ //
  $pdf->SetY(-45);
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(80, 6, 'DATOS DE DEPOSITO', 1, 1, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'BANCO', 1, 0, 'C');
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->Cell(50, 6, utf8_decode('BANAMEX'), 1, 1, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'NOMBRE', 1, 0, 'C');
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->Cell(50, 6, utf8_decode('RENAN ARMANDO MAGAÑA DIAZ'), 1, 1, 'C');
  $pdf->SetFont('Arial', 'B', 10);
  $pdf->Cell(30, 6, 'CLAVE', 1, 0, 'C');
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->Cell(50, 6, utf8_decode('002691702182686154'), 1, 1, 'C');
  // ------------------------------------------- FOOTER ------------------------------------------ //

  $pdf->Output('I', "Cotizacion " . dataM['cotM_folio'] . ".pdf");
}
