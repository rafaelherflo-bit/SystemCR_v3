<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
require_once SERVERDIR . "vista/assets/fpdf/fpdf.php";

session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
  echo forceoutSession();
  exit();
}

$pdf = new FPDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Output('I', "Almacen Chips Stock - " . date("d-m-Y") . ".pdf");
