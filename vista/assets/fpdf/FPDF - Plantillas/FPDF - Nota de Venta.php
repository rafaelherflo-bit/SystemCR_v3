<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

// --- Clase para personalizar la nota de venta (opcional pero recomendado) ---
class PDF extends FPDF
{
  // Cabecera de página
  function Header()
  {
    // Logo (Asegúrate de que la ruta de la imagen sea correcta)
    $this->Image(LOGOCR, 10, 10, 50, 0, '', WEBSITE);

    // Configuración de fuente para el título
    $this->SetFont('Arial', 'B', 15);

    // Movernos a la derecha
    $this->Cell(80);

    // Título
    $this->Cell(30, 10, 'NOTA DE VENTA', 0, 0, 'C');

    // Información de la Nota (Número y Fecha)
    $this->SetFont('Arial', '', 10);
    $this->SetY(10);
    $this->SetX(150);
    $this->Cell(50, 5, utf8_decode('No. Venta: 000123'), 0, 1, 'R');
    $this->SetX(150);
    $this->Cell(50, 5, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');

    // Salto de línea
    $this->Ln(10);
  }

  // Pie de página
  function Footer()
  {
    // Posición a 1.5 cm del final
    $this->SetY(-15);

    // Configuración de fuente
    $this->SetFont('Arial', 'I', 8);

    // Número de página
    $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }

  // Encabezados de la tabla de productos
  function cabeceraTabla()
  {
    $this->SetFillColor(230, 230, 230); // Color de fondo para encabezados
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(10, 7, utf8_decode('Cód.'), 1, 0, 'C', 1);
    $this->Cell(80, 7, utf8_decode('Descripción'), 1, 0, 'L', 1);
    $this->Cell(25, 7, utf8_decode('Cant.'), 1, 0, 'C', 1);
    $this->Cell(35, 7, utf8_decode('Precio Unit.'), 1, 0, 'R', 1);
    $this->Cell(40, 7, utf8_decode('Total'), 1, 1, 'R', 1); // El '1' indica un salto de línea
  }

  // Datos de la tabla
  function datosTabla($data)
  {
    $this->SetFont('Arial', '', 10);
    $totalGeneral = 0;
    foreach ($data as $row) {
      $totalFila = $row['cantidad'] * $row['precio'];
      $totalGeneral += $totalFila;

      $this->Cell(10, 6, $row['codigo'], 1, 0, 'C');
      // Usamos MultiCell para descripciones largas
      $x = $this->GetX();
      $y = $this->GetY();
      $this->MultiCell(80, 6, utf8_decode($row['descripcion']), 1, 'L');
      $this->SetXY($x + 80, $y); // Vuelve a la posición para las siguientes celdas

      $this->Cell(25, 6, $row['cantidad'], 1, 0, 'C');
      $this->Cell(35, 6, number_format($row['precio'], 2), 1, 0, 'R');
      $this->Cell(40, 6, number_format($totalFila, 2), 1, 1, 'R'); // Salto de línea
    }

    // Línea de Total
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(150, 7, 'TOTAL:', 1, 0, 'R', 1); // Celda unificada para Subtotal y Etiqueta de Total
    $this->Cell(40, 7, number_format($totalGeneral, 2) . ' MXN', 1, 1, 'R', 1);
  }
}

// --- Creación del PDF ---

// 1. Inicializar la clase con el formato de página
$pdf = new PDF('P', 'mm', 'A4'); // P=Portrait, mm=unidades, A4=formato
$pdf->AliasNbPages(); // Para el conteo de páginas en el Footer
$pdf->AddPage();

// 2. Información del cliente (Ejemplo de celdas simples)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'CLIENTE:', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, utf8_decode('Nombre: Juan Pérez'), 0, 1);
$pdf->Cell(0, 5, utf8_decode('Dirección: Calle Ficticia No. 10'), 0, 1);
$pdf->Cell(0, 5, 'RFC: PERE900101XYZ', 0, 1);
$pdf->Ln(5);

// 3. Definir los datos de los productos (simulación de datos de una base de datos)
$data = array(
  array('codigo' => 'P001', 'descripcion' => 'Smartphone Modelo X última generación con 128GB de almacenamiento', 'cantidad' => 1, 'precio' => 8500.50),
  array('codigo' => 'P002', 'descripcion' => 'Funda protectora de silicona color negro', 'cantidad' => 2, 'precio' => 150.00),
  array('codigo' => 'S010', 'descripcion' => 'Servicio de configuración inicial y migración de datos', 'cantidad' => 1, 'precio' => 500.00),
);

// 4. Generar la tabla de productos
$pdf->cabeceraTabla();
$pdf->datosTabla($data);

// 5. Notas Adicionales
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCell(0, 5, utf8_decode('* Esta nota de venta no es un comprobante fiscal. Los precios incluyen IVA.'), 0, 'L');

// 6. Salida del PDF
// 'I' para mostrar en el navegador, 'D' para descargar, 'F' para guardar en el servidor
$pdf->Output('I', 'nota_venta_000123.pdf');
