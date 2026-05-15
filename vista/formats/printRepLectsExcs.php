<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}

class PDF extends FPDF
{

    // Propiedades para controlar el pie de página o títulos
    protected $CurrentClienteRs = '';

    function Header()
    {
        // Logo y título (Ajusta la ruta y el texto según sea necesario)
        // $this->Image('logo.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('REPORTE DE LECTURAS Y EXCEDENTES'), 0, 0, 'C');
        $this->Ln(10);

        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 5, utf8_decode('Fecha de Generación: ') . date('d/m/Y'), 0, 1, 'R');
        $this->Ln(5);

        // Si tienes una cabecera específica para el cliente, puedes añadirla aquí
        if ($this->CurrentClienteRs) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 7, utf8_decode('CLIENTE ACTUAL: ') . $this->CurrentClienteRs, 0, 1, 'L');
            $this->Ln(2);
        }
    }

    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Fuente Arial itálica 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Método para imprimir la fila de la lectura
    function PrintLecturaRow($data, $isMulticolor)
    {
        // [FECHA, ESC, B&N, COL, EXC ESC, EXC B&N, EXC COL, NOTA / CAMBIO] - Anchos originales: 30, 25, 25, 25, 25, 25, 25 (170)
        // Ajustamos anchos para incluir EXC ESC: 25, 20, 20, 20, 20, 20, 20, 25 (170)
        $w = [25, 20, 20, 20, 20, 20, 20, 25]; // Anchos de las columnas

        // Fecha de Lectura
        $this->Cell($w[0], 6, $data['lectura_fecha'], 'LR', 0, 'L');

        // Contadores
        $this->Cell($w[1], 6, number_format($data['lectura_esc']), 'R', 0, 'R');
        $this->Cell($w[2], 6, number_format($data['lectura_bn']), 'R', 0, 'R');
        if ($isMulticolor) {
            $this->Cell($w[3], 6, number_format($data['lectura_col'] ?? 0), 'R', 0, 'R');
        } else {
            // Celda vacía si no es multicolor
            $this->Cell($w[3], 6, '', 'R', 0, 'R');
        }

        // Excedentes (ESC, B&N, COL)
        $exEsc = $data['excedente_esc'] ?? 0;
        $exBN = $data['excedente_bn'] ?? 0;
        $exCol = $data['excedente_col'] ?? 0;
        $advertencia = $data['advertencia'] ?? null;

        if ($advertencia) {
            $this->SetFont('Arial', 'I', 6);
            $this->SetTextColor(255, 0, 0); // Rojo para advertencia
            $nota = utf8_decode("Falta LIM: {$advertencia}");
            $this->Cell($w[4] + $w[5] + $w[6], 6, $nota, 'R', 0, 'C'); // Celda combinada para advertencia
            $this->SetFont('Arial', '', 8);
            $this->SetTextColor(0); // Volver a negro
        } else {
            $this->SetTextColor(255, 0, 0); // Rojo para excedentes

            $this->Cell($w[4], 6, $exEsc > 0 ? number_format($exEsc) : '0', 'R', 0, 'R');
            $this->Cell($w[5], 6, $exBN > 0 ? number_format($exBN) : '0', 'R', 0, 'R');
            $this->Cell($w[6], 6, $exCol > 0 ? number_format($exCol) : '0', 'R', 0, 'R');

            $this->SetTextColor(0); // Volver al negro
        }

        // Notas (Cambio de equipo)
        $cambio = $data['cambio_equipo'] ?? null;
        $w_nota = ($advertencia) ? $w[7] : $w[7]; // Mismo ancho
        if ($cambio) {
            $nota = utf8_decode("CAMBIO: Ret:{$cambio['retirado']} Ing:{$cambio['ingresado']}");
            $this->SetFont('Arial', 'I', 6);
            $this->Cell($w_nota, 6, $nota, 'R', 1, 'L');
            $this->SetFont('Arial', '', 8);
        } else {
            $this->Cell($w_nota, 6, '', 'R', 1, 'L');
        }
    }
}
// ===================================================================
// 1. OBTENCIÓN DE DATOS (3 Consultas)
// ===================================================================

// --- 1.1 Consulta de Rentas Activas y sus límites ---
$SQLrentas = "SELECT 
R.renta_id, R.renta_folio, R.renta_depto, R.renta_contrato_id, 
R.renta_inc_esc, R.renta_inc_bn, R.renta_inc_col,
Cl.cliente_id, Cl.cliente_rs, 
Co.contrato_id, Co.contrato_folio, 
M.modelo_tipo,
E.equipo_serie
FROM Rentas R
INNER JOIN Equipos E ON R.renta_equipo_id = E.equipo_id
INNER JOIN Modelos M ON E.equipo_modelo_id = M.modelo_id
INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id
INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id
INNER JOIN Zonas Z ON R.renta_zona_id = Z.zona_id
WHERE R.renta_estado = 'Activo'
ORDER BY Cl.cliente_rs ASC, Co.contrato_folio ASC, R.renta_folio ASC";

$QRYrentas = consultaData($SQLrentas);

$rentasData = [];
$rentasIds = [];
foreach ($QRYrentas['dataFetch'] as $renta) {
    // Asegurar que los límites numéricos sean tratados como enteros
    $renta['renta_inc_esc'] = (int)$renta['renta_inc_esc'];
    $renta['renta_inc_bn'] = (int)$renta['renta_inc_bn'];
    $renta['renta_inc_col'] = (int)$renta['renta_inc_col'];

    $rentasData[$renta['renta_id']] = $renta;
    $rentasIds[] = $renta['renta_id'];
}
$idsString = implode("','", $rentasIds);
$lecturasAgrupadas = [];
$cambiosAgrupados = [];

if (!empty($rentasIds)) {

    // --- 1.2 Consulta de TODAS las Lecturas Históricas ---
    $SQLlecturas = "SELECT lectura_renta_id, lectura_fecha, lectura_esc, lectura_bn, lectura_col 
FROM Lecturas
WHERE lectura_renta_id IN ('" . $idsString . "')
ORDER BY lectura_renta_id, lectura_fecha DESC";
    $QRYlecturas = consultaData($SQLlecturas);

    foreach ($QRYlecturas['dataFetch'] as $lectura) {
        $lecturasAgrupadas[$lectura['lectura_renta_id']][] = $lectura;
    }

    // --- 1.3 Consulta de TODOS los Cambios de Equipo Relevantes ---
    $SQLcambios = "SELECT 
C.cambio_renta_id, C.cambio_fecha, 
C.cambio_Ret_esc, C.cambio_Ret_bn, C.cambio_Ret_col, 
C.cambio_Ing_esc, C.cambio_Ing_bn, C.cambio_Ing_col,
ER.equipo_serie AS serie_retirado,
EI.equipo_serie AS serie_ingresado
FROM Cambios C
INNER JOIN Equipos ER ON C.cambio_equipoRet_id = ER.equipo_id
INNER JOIN Equipos EI ON C.cambio_equipoIng_id = EI.equipo_id
WHERE C.cambio_renta_id IN ('" . $idsString . "')
ORDER BY C.cambio_renta_id, C.cambio_fecha ASC";
    $QRYcambios = consultaData($SQLcambios);

    foreach ($QRYcambios['dataFetch'] as $cambio) {
        $cambiosAgrupados[$cambio['cambio_renta_id']][] = $cambio;
    }
}


// ===================================================================
// 2. CÁLCULO DE EXCEDENTE HISTÓRICO (MODIFICADA)
// ===================================================================

$rentasExcedentes = [];
$totalMesesConLectura = [];

foreach ($rentasData as $rentaId => $renta) {

    $lecturas = $lecturasAgrupadas[$rentaId] ?? [];
    $cambios = $cambiosAgrupados[$rentaId] ?? [];

    $mesesConExcedente = 0;
    $mesesAnalizados = 0;
    $lecturasConCalculo = []; // Nuevo array para guardar los resultados

    // Invertir para cálculo: Antigua a Reciente
    $lecturasOrdenadas = array_reverse($lecturas);
    $lecturaAnterior = null;

    $limiteEsc = $renta['renta_inc_esc']; // Límite de Escaneo
    $limiteBN = $renta['renta_inc_bn'];
    $limiteColor = $renta['renta_inc_col'];

    foreach ($lecturasOrdenadas as $i => $lecturaActual) {

        $lecturaCalculada = $lecturaActual; // Usamos el mismo array para añadir resultados
        $lecturaCalculada['advertencia'] = null; // Inicializar advertencia

        // Comprobación de límites en cero
        $limitesCero = [];
        if ($limiteEsc === 0) $limitesCero[] = 'ESC';
        if ($limiteBN === 0) $limitesCero[] = 'B&N';
        if ($renta['modelo_tipo'] == "Multicolor" && $limiteColor === 0) $limitesCero[] = 'COL';

        if ($lecturaAnterior) {

            $mesesAnalizados++;

            // Si hay límites en cero, NO calculamos excedentes y ponemos una advertencia
            if (!empty($limitesCero)) {
                $lecturaCalculada['excedente_esc'] = 0;
                $lecturaCalculada['excedente_bn'] = 0;
                $lecturaCalculada['excedente_col'] = 0;
                $lecturaCalculada['cambio_equipo'] = null; // Si hubo cambio se debería mostrar, pero para simplificar, se omite el cálculo.
                $lecturaCalculada['advertencia'] = implode(', ', $limitesCero);
            } else {

                // 1. Consumo Bruto
                $consumoEscBruto = $lecturaActual['lectura_esc'] - $lecturaAnterior['lectura_esc'];
                $consumoBNBruto = $lecturaActual['lectura_bn'] - $lecturaAnterior['lectura_bn'];
                $consumoColorBruto = ($lecturaActual['lectura_col'] ?? 0) - ($lecturaAnterior['lectura_col'] ?? 0);

                // 2. Aplicar Ajuste por Cambio de Equipo
                $ajusteEsc = 0;
                $ajusteBN = 0;
                $ajusteColor = 0;
                $cambioRegistrado = false;
                $detalleCambio = null;

                $fechaActual = strtotime($lecturaActual['lectura_fecha']);
                $fechaAnterior = strtotime($lecturaAnterior['lectura_fecha']);

                foreach ($cambios as $cambio) {
                    $fechaCambio = strtotime($cambio['cambio_fecha']);

                    if ($fechaCambio > $fechaAnterior && $fechaCambio <= $fechaActual) {

                        // Sumar ajustes (si hubiera múltiples cambios en el periodo, se suman)
                        $ajusteEsc += $cambio['cambio_Ret_esc'] - $cambio['cambio_Ing_esc'];
                        $ajusteBN += $cambio['cambio_Ret_bn'] - $cambio['cambio_Ing_bn'];
                        $ajusteColor += ($cambio['cambio_Ret_col'] ?? 0) - ($cambio['cambio_Ing_col'] ?? 0);

                        // Guardar detalles del primer cambio para impresión (si quieres mostrar todos, usa un array)
                        if (!$cambioRegistrado) {
                            $detalleCambio = [
                                'fecha' => $cambio['cambio_fecha'],
                                'retirado' => $cambio['serie_retirado'],
                                'ingresado' => $cambio['serie_ingresado'],
                            ];
                            $cambioRegistrado = true;
                        }
                    }
                }

                // 3. Consumo Mensual Ajustado
                $consumoMensualEsc = $consumoEscBruto + $ajusteEsc;
                $consumoMensualBN = $consumoBNBruto + $ajusteBN;
                $consumoMensualColor = $consumoColorBruto + $ajusteColor;

                // 4. Cálculo de Excedente
                $excedenteEsc = max(0, $consumoMensualEsc - $limiteEsc);
                $excedenteBN = max(0, $consumoMensualBN - $limiteBN);
                $excedenteColor = max(0, $consumoMensualColor - $limiteColor);

                if ($excedenteEsc > 0 || $excedenteBN > 0 || $excedenteColor > 0) {
                    $mesesConExcedente++;
                }

                // 5. Almacenar resultados para la presentación
                $lecturaCalculada['excedente_esc'] = $excedenteEsc;
                $lecturaCalculada['excedente_bn'] = $excedenteBN;
                $lecturaCalculada['excedente_col'] = $excedenteColor;
                $lecturaCalculada['cambio_equipo'] = $detalleCambio;
            }
        } else {
            // La primera lectura de la historia no tiene cálculo de consumo mensual previo.
            $lecturaCalculada['excedente_esc'] = 0;
            $lecturaCalculada['excedente_bn'] = 0;
            $lecturaCalculada['excedente_col'] = 0;
            $lecturaCalculada['cambio_equipo'] = null;
        }

        $lecturasConCalculo[] = $lecturaCalculada;
        $lecturaAnterior = $lecturaActual;
    }

    $rentasExcedentes[$rentaId] = $mesesConExcedente;
    $totalMesesConLectura[$rentaId] = $mesesAnalizados;

    // Sobrescribir las lecturas agrupadas con los resultados del cálculo
    $lecturasAgrupadas[$rentaId] = array_reverse($lecturasConCalculo); // Volver a ordenar DESC (Reciente a Antigua)
}
// ===================================================================


// Inicializar FPDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 20); // Margen inferior de 20mm
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$cliID = 0;
$contrID = 0;
$isFirstPage = true;

foreach ($rentasData as $rentaId => $renta) {

    // --- 2.1 CÁLCULO DE RESALTE (Frecuencia de Excedente) ---
    $mesesExcedente = $rentasExcedentes[$rentaId] ?? 0;
    $totalMeses = $totalMesesConLectura[$rentaId] ?? 1;
    $promedioExcedente = ($totalMeses > 0) ? $mesesExcedente / $totalMeses : 0;
    $resaltar = ($promedioExcedente > 0.5 && $totalMeses >= 3);

    // --- 2.2 MANEJO DE ENCABEZADOS (Cliente y Contrato) ---

    // 1. CLIENTE
    if ($cliID == 0 || $cliID != $renta['cliente_id']) {
        if (!$isFirstPage) {
            // Salto de página para un cliente nuevo (opcional, mejora la legibilidad)
            $pdf->AddPage();
        }
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 128); // Azul oscuro
        $pdf->Cell(0, 7, utf8_decode('CLIENTE: ') . $renta['cliente_rs'], 0, 1, 'L');
        $pdf->Ln(2);
        $cliID = $renta['cliente_id'];
        $contrID = 0; // Reiniciar Contrato
        $isFirstPage = false;
    }

    // 2. CONTRATO
    if ($contrID == 0 || $contrID != $renta['contrato_id']) {
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->SetTextColor(0, 0, 0); // Negro
        $pdf->Cell(0, 6, utf8_decode('CONTRATO: ') . $renta['contrato_folio'], 0, 1, 'L');
        $pdf->Ln(1);
        $contrID = $renta['contrato_id'];
    }

    // 3. RENTA (Encabezado de la Sección de Lecturas)
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(230, 230, 230); // Fondo gris claro

    if ($resaltar) {
        $pdf->SetTextColor(255, 0, 0); // Texto rojo para resaltar
    } else {
        $pdf->SetTextColor(0); // Texto negro normal
    }

    // Preparar el string de límites
    $limitesStr = "LÍMITES: ESC {$renta['renta_inc_esc']} / B&N {$renta['renta_inc_bn']}";
    if ($renta['modelo_tipo'] == "Multicolor") {
        $limitesStr .= " / COL {$renta['renta_inc_col']}";
    }

    $rentaTitulo = utf8_decode("RENTA: {$renta['renta_folio']} | DEPTO: {$renta['renta_depto']} | SERIE ACTUAL: {$renta['equipo_serie']}");
    $pdf->Cell(150, 6, $rentaTitulo, 1, 0, 'L', true);

    if ($resaltar) {
        $porcentaje = round($promedioExcedente * 100);
        $pdf->Cell(40, 6, "ALTO EXCEDENTE: {$porcentaje}%", 1, 1, 'C', true);
    } else {
        $pdf->Cell(40, 6, utf8_decode($limitesStr), 1, 1, 'C', true);
    }

    $pdf->SetTextColor(0); // Volver a negro para las lecturas

    // --- 2.3 CABECERAS DE LA TABLA DE LECTURAS (Ajustada) ---
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(240, 240, 240);
    // [25, 20, 20, 20, 20, 20, 20, 25]
    $w = [25, 20, 20, 20, 20, 20, 20, 25]; // Anchos de las columnas
    $header = ['FECHA', 'ESC', 'B&N', 'COL', 'EXC ESC', 'EXC B&N', 'EXC COL', 'NOTA / CAMBIO'];

    foreach ($header as $i => $col) {
        // El ancho de EXC ESC, EXC B&N y EXC COL es 20 cada uno
        $pdf->Cell($w[$i], 6, utf8_decode($col), 1, 0, 'C', true);
    }
    $pdf->Ln();

    // --- 2.4 FILAS DE LECTURAS (Últimas 10) ---
    $pdf->SetFont('Arial', '', 8);
    $lecturasImpresion = array_slice($lecturasAgrupadas[$rentaId] ?? [], 0, 10);

    foreach ($lecturasImpresion as $lecturaRow) {
        // La función PrintLecturaRow maneja el formato y los límites
        $pdf->PrintLecturaRow($lecturaRow, $renta['modelo_tipo'] == "Multicolor");
    }

    // Cerrar la tabla de lecturas
    $pdf->Cell(array_sum($w), 0, '', 'T', 1, 'L');
    $pdf->Ln(5);
}

// Salida del PDF
$pdf->Output('I', 'Reporte_Lecturas_Excedentes.pdf');
