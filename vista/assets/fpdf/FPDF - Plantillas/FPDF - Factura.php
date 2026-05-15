<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

// --- CLASE FPDF PERSONALIZADA (Opcional, pero útil para encabezado/pie de página)
class PDF extends FPDF
{
  // Cabecera de página
  function Header()
  {
    // Logo
    // $this->Image('ruta/a/tu/logo.png', 10, 8, 33);

    // Título de la factura
    $this->SetFont('Arial', 'B', 15);
    $this->Cell(80); // Mueve el cursor a la derecha
    $this->Cell(30, 10, 'FACTURA DE VENTA', 0, 1, 'C');

    // Información de la Empresa
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 5, 'Tu Nombre de Empresa S.A.', 0, 1, 'L');
    $this->Cell(0, 5, 'Direccion: Calle Falsa 123', 0, 1, 'L');
    $this->Cell(0, 5, 'NIF/CIF: XXXXXXXXX', 0, 1, 'L');
    $this->Cell(0, 5, 'Teléfono: 555-1234', 0, 1, 'L');

    // Salto de línea
    $this->Ln(5);
  }

  // Pie de página
  function Footer()
  {
    // Posición a 1.5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Número de página
    $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }
}

// --- CREACIÓN DEL PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages(); // Necesario para mostrar el total de páginas en el pie de página
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10); // Márgenes

// --- DATOS DE LA FACTURA
$num_factura = "2025-001";
$fecha = "08/11/2025";
$cliente_nombre = "Cliente Ejemplo S.L.";
$cliente_direccion = "Av. Siempre Viva 742";
$cliente_id = "Y-9876543";

// --- DATOS DE LOS PRODUCTOS
// Array: [código, descripción, cantidad, precio_unitario]
$productos = [
  ['P001', 'Servicio de desarrollo web', 1, 500.00],
  ['P002', 'Licencia de software anual', 2, 150.00],
  ['P003', 'Soporte técnico (horas)', 5, 50.00]
];

// --- INFORMACIÓN DE LA FACTURA Y CLIENTE
$pdf->SetY(40); // Posición inicial después de la cabecera
$pdf->SetFont('Arial', 'B', 10);

// Número de factura y fecha
$pdf->Cell(60, 5, 'Factura No: ' . $num_factura, 0, 0, 'L');
$pdf->Cell(0, 5, 'Fecha: ' . $fecha, 0, 1, 'R');
$pdf->Ln(5);

// Datos del Cliente
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(0, 7, 'DATOS DEL CLIENTE', 1, 1, 'L', 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Nombre: ' . $cliente_nombre, 0, 1, 'L');
$pdf->Cell(0, 5, 'Dirección: ' . $cliente_direccion, 0, 1, 'L');
$pdf->Cell(0, 5, 'ID Fiscal: ' . $cliente_id, 0, 1, 'L');
$pdf->Ln(5);

// --- TABLA DE DETALLES DE LA FACTURA

// Títulos de la tabla
$pdf->SetFillColor(180, 180, 180);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 7, 'COD.', 1, 0, 'C', 1);
$pdf->Cell(95, 7, 'DESCRIPCIÓN', 1, 0, 'L', 1);
$pdf->Cell(20, 7, 'CANT.', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'P. UNIT.', 1, 0, 'R', 1);
$pdf->Cell(25, 7, 'TOTAL', 1, 1, 'R', 1); // El 1 final significa nueva línea

// Contenido de la tabla
$pdf->SetFont('Arial', '', 10);
$subtotal = 0;
foreach ($productos as $producto) {
  $codigo = $producto[0];
  $descripcion = $producto[1];
  $cantidad = $producto[2];
  $precio_unitario = $producto[3];
  $total_producto = $cantidad * $precio_unitario;
  $subtotal += $total_producto;

  $pdf->Cell(20, 6, $codigo, 1, 0, 'C');
  $pdf->Cell(95, 6, $descripcion, 1, 0, 'L');
  $pdf->Cell(20, 6, $cantidad, 1, 0, 'C');
  $pdf->Cell(30, 6, number_format($precio_unitario, 2, '.', ','), 1, 0, 'R');
  $pdf->Cell(25, 6, number_format($total_producto, 2, '.', ','), 1, 1, 'R'); // Nueva línea
}

// --- RESUMEN DE TOTALES
$iva_porcentaje = 0.16; // 16% de IVA
$iva_monto = $subtotal * $iva_porcentaje;
$total_final = $subtotal + $iva_monto;

// Subtotal
$pdf->Cell(135, 6, '', 0, 0); // Celda vacía para alinear
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'Subtotal:', 1, 0, 'L', 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 6, number_format($subtotal, 2, '.', ','), 1, 1, 'R');

// IVA
$pdf->Cell(135, 6, '', 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'IVA (16%):', 1, 0, 'L', 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(25, 6, number_format($iva_monto, 2, '.', ','), 1, 1, 'R');

// Total Final
$pdf->Cell(135, 6, '', 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(255, 200, 200); // Color diferente para el total
$pdf->Cell(30, 6, 'TOTAL:', 1, 0, 'L', 1);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(25, 6, '$' . number_format($total_final, 2, '.', ','), 1, 1, 'R', 1);

// --- NOTAS
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 5, 'Condiciones de pago: Pago a 30 días.', 0, 1);

// --- SALIDA DEL PDF
$pdf->Output('I', 'factura_' . $num_factura . '.pdf'); // 'I' para mostrar en el navegador, 'D' para descarga
