<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
$id_contrato = (isset($_GET['id'])) ? decryption($_GET['id']) : 0;

if ($id_contrato == 0) {
  die("Error: No se proporcionó un ID de contrato válido.");
}

// 1. OBTENCIÓN DE DATOS CENTRALIZADA
$sqlC = "SELECT * FROM Contratos 
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id 
        WHERE contrato_id = '$id_contrato'";
$datosC = consultaData($sqlC);
$c = $datosC['dataFetch'][0];

// AV INSTITUTO POLITECNICO MZ 1 LTE 3 37 SM. 504. CANCUN Q ROO CP.77533
$direccionFiscal = $c['cliente_tipoVialidad'] . " " . $c['cliente_noVialidad'] . ", CP." . $c['cliente_cp'];

$rsArrendatario = $c['cliente_rs'];

// Lógica del Arrendador
if ($c['cliente_emiFact'] == 2) {
  $rsArrendadora = "MIMI FLORES OLAN";
  $rfcArrendadora = "FOOM730326JL4";
} else {
  $rsArrendadora = "RENAN ARMANDO MAGAÑA DIAZ";
  $rfcArrendadora = "MADR8504096K8";
}

// Obtención de equipos para Declaración I inciso b)
$sqlR = "SELECT R.*, E.equipo_serie, E.equipo_estado, E.equipo_codigo, M.modelo_modelo, M.modelo_linea , M.modelo_tipo 
        FROM Rentas R 
        LEFT JOIN Equipos E ON R.renta_equipo_id = E.equipo_id 
        LEFT JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
        WHERE R.renta_contrato_id = '$id_contrato' AND R.renta_estado = 'Activo'";
$rentas = consultaData($sqlR);

foreach ($rentas['dataFetch'] as $rentaCheck) {
  if ($rentaCheck['renta_costo'] == 0) {
    die("Una renta no tiene costo mensual asignado");
  }

  if ($rentaCheck['renta_inc_esc'] == 0) {
    die("Cantidad Incluida de ESCANEO, no asignado a " . $rentaCheck['renta_depto']);
  } else if ($rentaCheck['renta_exc_esc'] == 0) {
    die("Costo de Excedente ESCANEO, no asignado a " . $rentaCheck['renta_depto']);
  }

  if ($rentaCheck['renta_inc_bn'] == 0) {
    die("Cantidad Incluida de B&N, no asignado a " . $rentaCheck['renta_depto']);
  } else if ($rentaCheck['renta_exc_bn'] == 0) {
    die("Costo de Excedente B&N, no asignado a " . $rentaCheck['renta_depto']);
  }

  if ($rentaCheck['modelo_tipo'] == "Multicolor") {
    if ($rentaCheck['renta_inc_col'] == 0) {
      die("Cantidad Incluida de COLOR, no asignado a " . $rentaCheck['renta_depto']);
    } else if ($rentaCheck['renta_exc_col'] == 0) {
      die("Costo de Excedente COLOR, no asignado a " . $rentaCheck['renta_depto']);
    }
  }
}


class PDF extends FPDF
{
  function Footer()
  {
    $this->SetY(-60);
    // Número de página
    $this->SetY(-15);
    $this->SetFont('Arial', 'I', 8);
    $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }

  function SectionTitle($title)
  {
    $this->SetFont('Arial', 'B', 11);
    $this->Cell(0, 10, utf8_decode($title), 0, 1, 'C');
  }

  function Clause($title, $text)
  {
    $this->SetFont('Arial', 'B', 10);
    $this->Write(5, utf8_decode($title . " "));
    $this->SetFont('Arial', '', 10);
    $this->MultiCell(0, 5, utf8_decode($text), 0, 'J');
    $this->Ln(3);
  }
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->SetMargins(20, 20, 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$bullet = chr(149);

// --- TÍTULO ---
$pdf->AddLink();
$pdf->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);
$pdf->Ln(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->MultiCell(0, 5, utf8_decode("CONTRATO DE ARRENDAMIENTO DE IMPRESORAS"), 0, 'C');
$pdf->MultiCell(0, 5, utf8_decode("CONTRATO: " . $c['contrato_folio']), 0, 'C');
$pdf->Ln(5);

// --- PROEMIO ---
$proemio = "CONTRATO DE ARRENDAMIENTO DE EQUIPO DE FOTOCOPIADO QUE CELEBRAN POR UNA PARTE EL	C. " . $rsArrendadora . " RFC: " . $rfcArrendadora . " EN LO SUCESIVO \"EL ARRENDADOR\" Y POR LA OTRA PARTE " . strtoupper($c['cliente_rs']) . ". RFC: " . strtoupper($c['cliente_rfc']) . " REPRESENTADA EN ESTE ACTO POR EL SR./SRA. " . strtoupper($c['cliente_contacto']) . ". EN SU CARÁCTER DE CLIENTE, A QUIEN EN LO SUCESIVO SE LE DENOMINARA COMO \"EL ARRENDATARIO\", AL TENOR DE LOS SIGUIENTES DECLARACIONES Y CLÁUSULAS:";
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($proemio), 0, 'J');
$pdf->Ln(5);

$pdf->SectionTitle("D E C L A R A C I O N E S");

// --- DECLARACIONES ARRENDADORA ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode("I.- DECLARA \"EL ARRENDADOR\", QUE:"), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode("a). Manifiesta bajo protesta de decir verdad llamarse como ha quedado escrito en el proemio del presente instrumento, ser mayor de edad, de nacionalidad mexicana y que tiene todas las atribuciones, facultades y capacidad suficiente para celebrar toda clase de actos, contratos y convenio civiles o mercantiles y que es su voluntad celebrar el presente contrato."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("b). Que bajo protesta de decir verdad que \"EL ARRENDADOR\" es propietario de el/los equipos de fotocopiado de la Marca KYOCERA y es su deseo otorgar en arrendamiento a \"EL ARRENDATARIO\" los equipos que seran descritos a continuacion, de acuerdo con los términos y condiciones del presente Contrato, en estados de servir para el uso convenido y es su decisión darlo en arrendamiento al Sr./Sra. " . strtoupper($c['cliente_contacto']) . "."), 0, 'J');

// Tabla de Equipos
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(240);
$pdf->Cell(15);
$pdf->Cell(50, 7, "RENTA FOLIO", 1, 0, 'C', true);
$pdf->Cell(50, 7, "MODELO", 1, 0, 'C', true);
$pdf->Cell(50, 7, "SERIE", 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);
foreach ($rentas['dataFetch'] as $r) {
  $pdf->Cell(15);
  $pdf->Cell(50, 6, utf8_decode($c['contrato_folio'] . "-" . $r['renta_folio']), 1, 0, 'C');
  $pdf->Cell(50, 6, utf8_decode($r['modelo_linea'] . " " . $r['modelo_modelo']), 1, 0, 'C');
  $pdf->Cell(50, 6, utf8_decode($r['equipo_serie']), 1, 1, 'C');
}

$pdf->Ln(5);

// --- DECLARACIONES ARRENDATARIO ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode("II.- DECLARA \"EL ARRENDATARIO\", QUE:"), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode("a). Declara \"EL ARRENDATARIO\" que tiene la capacidad legal para celebrar contratos y desea que \"EL ARRENDADOR\" le proporcione en calidad de arrendamiento el equipo y modelo que requiere para su servicio."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("b) Es una sociedad legalmente constituida bajo el nombre de \"" . $c['cliente_rs'] . "\".\n"), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("c) El Sr./Sra. " . strtoupper($c['contrato_contacto']) . " cuenta con facultades suficientes para obligarla en los términos de este Contrato."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("d) RFC: " . $c['cliente_rfc'] . " y domicilio fiscal en: " .  $direccionFiscal . "."), 0, 'J');

// --- RECUADRO DE SEGURIDAD (RELLENO TRANSVERSAL) ---
$pdf->Ln(5);
$yActual = $pdf->GetY();
$margenInferior = 250; // Límite antes del número de página
$altoRecuadro = $margenInferior - $yActual;

if ($altoRecuadro > 10) { // Solo dibujar si hay espacio suficiente
  $xInicio = 20;
  $anchoRecuadro = 190; // Ancho total considerando márgenes de 20mm

  // Dibujamos el contorno
  $pdf->Rect($xInicio, $yActual, $anchoRecuadro, $altoRecuadro);

  // Dibujamos líneas transversales (diagonales)
  $pdf->SetDrawColor(200, 200, 200); // Color gris claro para no saturar
  $espaciado = 5; // Espacio entre líneas

  for ($i = 0; $i < ($anchoRecuadro + $altoRecuadro); $i += $espaciado) {
    // Lógica para mantener las líneas dentro de los límites del Rect
    $x1 = max($xInicio, $xInicio + $i - $altoRecuadro);
    $y1 = min($yActual + $altoRecuadro, $yActual + $i);
    $x2 = min($xInicio + $anchoRecuadro, $xInicio + $i);
    $y2 = max($yActual, $yActual + $i - $anchoRecuadro);

    $pdf->Line($x1, $y1, $x2, $y2);
  }

  $pdf->SetDrawColor(0, 0, 0); // Reset color a negro
  $pdf->SetXY($xInicio, $yActual + ($altoRecuadro / 2) - 3);
  $pdf->SetFont('Arial', 'I', 8);
  $pdf->Cell($anchoRecuadro, 6, utf8_decode("--- ESPACIO CERRADO DELIBERADAMENTE ---"), 0, 0, 'C');
}

// --- PÁGINA 2: CLÁUSULAS INICIALES Y COSTOS ---
$pdf->AddPage();
$pdf->SectionTitle("C L Á U S U L A S");

// --- TODAS LAS CLÁUSULAS SOLICITADAS ---

$pdf->Clause("PRIMERA. OBJETO DEL CONTRATO.", "\"LA ARRENDADORA\" se obliga a entregar en arrendamiento los equipos descritos en la declaración I inciso b), de la marca KYOCERA, en buen estado y listos para el uso convenido.");

$pdf->Clause("SEGUNDA. COSTO.", "El costo por concepto de servicios se cubrirá de la siguiente manera:");
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(240);
$pdf->Cell(30, 7, "MODELO", 1, 0, 'C', true);
$pdf->Cell(30, 7, "SERIE", 1, 0, 'C', true);
$pdf->Cell(30, 7, "ESCANEO", 1, 0, 'C', true);
$pdf->Cell(30, 7, "B&N", 1, 0, 'C', true);
$pdf->Cell(30, 7, "COLOR", 1, 0, 'C', true);
$pdf->Cell(25, 7, "MENSUALIDAD", 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 8);
$totalRentas = 0;
foreach ($rentas['dataFetch'] as $r) {
  $pdf->Cell(30, 5, utf8_decode($r['modelo_linea'] . " " . $r['modelo_modelo']), 1, 0, 'C');
  $pdf->Cell(30, 5, utf8_decode($r['equipo_serie']), 1, 0, 'C');
  $pdf->Cell(30, 5, utf8_decode($r['renta_inc_esc'] . " / $" . number_format($r['renta_exc_esc'], 2) . " + IVA."), 1, 0, 'C');
  $pdf->Cell(30, 5, utf8_decode($r['renta_inc_bn'] . " / $" . number_format($r['renta_exc_bn'], 2) . " + IVA."), 1, 0, 'C');
  $pdf->Cell(30, 5, utf8_decode($r['modelo_tipo'] === "Multicolor" ? $r['renta_inc_col'] . " / $" . number_format($r['renta_exc_col'], 2) . " + IVA." : "N/A"), 1, 0, 'C');
  $pdf->Cell(25, 5, utf8_decode("$" . number_format($r['renta_costo'], 2) . " + IVA."), 1, 1, 'C');
  $totalRentas += $r['renta_costo'];
}
$pdf->Cell(120);
$pdf->Cell(30, 5, utf8_decode("TOTAL:"), 1, 0, 'R', true);
$pdf->Cell(25, 5, utf8_decode("$" . number_format($totalRentas, 2) . " + IVA."), 1, 1, 'C', true);

$pdf->Ln(2);

$pdf->Clause("TERCERA. FORMA DE PAGO.", "\"EL ARRENDATARIO\" pagará a \"LA ARRENDADORA\" el importe del precio pactado mensualmente bajo las siguientes condiciones:");
$pdf->SetFont('Arial', '', 10);

$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Determinación de Consumo:"), 0, 'J');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("El número de fotocopias se determinará mediante el reporte electrónico generado automáticamente por el sistema de monitoreo remoto integrado en el equipo. Para efectos de facturación, se tomará como base el contador registrado el día primero de cada mes."), 0, 'J');

$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Validación Tecnológica:"), 0, 'J');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("Ambas partes aceptan la validez de los reportes digitales enviados por el equipo vía internet como prueba plena del consumo. En caso de que \"EL ARRENDATARIO\" lo solicite, \"LA ARRENDADORA\" enviará el historial detallado de reportes diarios del mes correspondiente."), 0, 'J');

$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Contingencia:"), 0, 'J');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("Si por falta de conexión a internet o fallas técnicas del equipo no se pudiera obtener la lectura remota, las partes podrán acordar excepcionalmente una lectura física o el envío de una fotografía del contador del equipo."), 0, 'J');

$pdf->SetFont('Arial', 'B', 9);
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Facturación:"), 0, 'J');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("Una vez obtenido el contador, \"LA ARRENDADORA\" presentará la factura correspondiente para su pago."), 0, 'J');

$pdf->Ln(2);

$pdf->Clause("CUARTA. INTERESES MORATORIOS.", "En caso de que \"EL ARRENDATARIO\" no liquide el pago de la renta y sus excedentes dentro del plazo pactado, se generará a favor de \"LA ARRENDADORA\" un cargo por concepto de interés moratorio equivalente al 15% (quince por ciento) por cada mes de atraso o fracción del mismo.");
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Base del Cálculo: Dicho porcentaje se aplicará sobre el monto total acumulado de las rentas y excedentes no pagados de los meses anteriores."), 0, 'J');
$pdf->MultiCell(0, 5, $bullet . utf8_decode(" Acumulación: Este cargo es independiente al pago de la renta del mes corriente, la cual deberá cubrirse en su totalidad junto con los cargos moratorios generados hasta la fecha de pago."), 0, 'J');

$pdf->Ln(2);

$pdf->Clause("QUINTA. PROPIEDAD.", "\"EL ARRENDADOR\" conservará en todo tiempo y lugar la propiedad exclusiva de la máquina fotocopiadora objeto del presente contrato.");

$pdf->Clause("SEXTA. OBLIGACIONES.", "\"EL ARRENDADOR\" se obliga a:");
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("1.- Mantener la máquina fotocopiadora en buen estado, siendo el único autorizado para realizar los ajustes y reparaciones que sean necesarias, para el buen uso de la fotocopiadora."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("2.- \"EL ARRENDADOR\" no se responsabiliza de cualquier daño o perdida originados por el mal uso del equipo de fotocopiado por parte de \"EL ARRENDATARIO\"."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("3.- \"EL ARRENDADOR\", entregará a \"EL ARRENDATARIO\", en el domicilio de éste los insumos (tóner) con previa solicitud de parte del arrendatario."), 0, 'J');

$pdf->Clause("SÉPTIMA. OBLIGACIONES.", "\"EL ARRENDATARIO\" se compromete a:");
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("1.- \"EL ARRENDATARIO\" notificará las anomalías que presente el equipo de fotocopiado, para su reparación, estableciéndose un plazo máximo de 48 horas hábiles a partir de dicha notificación, para atender la solicitud de ajuste, reparación o cambio de máquina."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("2.- Pagar a \"EL ARRENDADOR\" el servicio y refacciones causadas por accidente, negligencia o cualquier otra causa imputable a \"EL ARRENDATARIO\"."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("3.- Usar el bien arrendado conforme a lo convenido."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("4.- Restituir el equipo y sus complementos arrendados al terminar el presente contrato."), 0, 'J');

$pdf->Clause("OCTAVA. RESPONSABILIDAD.", "\"EL ARRENDATARIO\" será responsable por:");
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, utf8_decode("1.- \"EL ARRENDATARIO\" notificará las anomalías que presente el equipo de fotocopiado, para su reparación, estableciéndose un plazo máximo de 48 horas hábiles a partir de dicha notificación, para atender la solicitud de ajuste, reparación o cambio de máquina."), 0, 'J');
$pdf->MultiCell(0, 5, utf8_decode("2.- Usar accesorios y aditamentos que dañen al equipo o le hubiere hecho alteraciones al mismo pagara a \"EL ARRENDADOR\" por separado el servicio y refacciones necesarios debiendo presentar la factura correspondiente."), 0, 'J');

// Definimos la fecha de inicio (Hoy)
$fechaInicio = date("Y-m-d");
// Calculamos la fecha fin (Un año después)
$fechaFin = date("Y-m-d", strtotime($fechaInicio . ' + 1 year'));
$pdf->Clause("NOVENA. PLAZO.", "El presente contrato de arrendamiento de equipo de fotocopiado se celebra por un plazo de un año improrrogable, que se computará del " . date("d/m/Y", strtotime($fechaInicio)) . " al " . date("d/m/Y", strtotime($fechaFin)) . ".");

$pdf->Clause("DÉCIMA. RESCISIÓN.", "Las partes convienen en que la falta de cumplimiento o la violación a lo estipulado en cualquiera de las cláusulas del presente contrato, dará lugar a la rescisión del mismo.");

$pdf->Clause("DÉCIMA PRIMERA. JURISDICCIÓN.", "En caso de controversia suscitada en torno a la interpretación y aplicación de las cláusulas del presente contrato, las partes se someten expresamente a la jurisdicción de los tribunales competentes del Primer Distrito Judicial en esta Entidad, renunciando a cualquier otro fuero o jurisdicción que pudiera corresponderles.");

// // --- 1. DETERMINAR POSICIÓN PARA FIRMAS (Fijamos dónde deben empezar) ---
// // Queremos que las firmas estén siempre a unos 60mm del final de la hoja
// $yFirmas = 210;
// $yActual = $pdf->GetY();
// $xInicio = 20;
// $anchoRecuadro = 190;

// // --- 2. RECUADRO DE SEGURIDAD ENTRE ÚLTIMA CLÁUSULA Y FIRMAS ---
// $altoRecuadro = $yFirmas - $yActual - 5; // Dejamos un pequeño margen de 5mm

// if ($altoRecuadro > 10) {
//   // Dibujamos el contorno
//   $pdf->SetDrawColor(0, 0, 0);
//   $pdf->Rect($xInicio, $yActual, $anchoRecuadro, $altoRecuadro);

//   // Dibujamos líneas transversales (gris claro)
//   $pdf->SetDrawColor(200, 200, 200);
//   $espaciado = 5;
//   for ($i = 0; $i < ($anchoRecuadro + $altoRecuadro); $i += $espaciado) {
//     $x1 = max($xInicio, $xInicio + $i - $altoRecuadro);
//     $y1 = min($yActual + $altoRecuadro, $yActual + $i);
//     $x2 = min($xInicio + $anchoRecuadro, $xInicio + $i);
//     $y2 = max($yActual, $yActual + $i - $anchoRecuadro);
//     $pdf->Line($x1, $y1, $x2, $y2);
//   }

//   // Texto de cierre
//   $pdf->SetXY($xInicio, $yActual + ($altoRecuadro / 2) - 3);
//   $pdf->SetFont('Arial', 'I', 8);
//   $pdf->SetTextColor(100, 100, 100);
//   $pdf->Cell($anchoRecuadro, 6, utf8_decode("--- ÚLTIMA LÍNEA DEL CONTRATO / ESPACIO CERRADO ---"), 0, 0, 'C');
//   $pdf->SetTextColor(0, 0, 0); // Reset color texto
// }

// --- 3. BLOQUE DE FECHA Y FIRMAS (Posición Fija) ---
// $pdf->SetY($yFirmas);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode("Firmado en ______________, _____________________, el día _______ de __________________ de __________"), 0, 1, 'C');
$pdf->Ln(25);

$yF = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0); // Asegurar que la línea de firma sea negra
$pdf->Line(20, $yF, 90, $yF);
$pdf->Line(120, $yF, 190, $yF);

$pdf->SetXY(20, $yF + 2);
$pdf->MultiCell(70, 5, utf8_decode($rsArrendadora . "\n\"LA ARRENDADORA\""), 0, 'C');

$pdf->SetXY(120, $yF + 2);
$pdf->MultiCell(70, 5, utf8_decode($rsArrendatario . "\n\"EL ARRENDATARIO\""), 0, 'C');

$pdf->Output('I', "Contrato_" . $c['contrato_folio'] . ".pdf");
