<?php
$redirect = SERVERURL . "Dash/";
$modulo = $GLOBALS['pagina0']; // Ej: ReportesF
$vista = $GLOBALS['pagina1'];  // Ej: Agregar

$whitelist = ['Activos', 'Iniciar', 'Agregar', 'Custom', 'idRAedit', 'idRFCedit', 'idRC'];
$listaAnios = range(2020, 2030);
$listaMeses = [
  'Enero' => 1,
  'Febrero' => 2,
  'Marzo' => 3,
  'Abril' => 4,
  'Mayo' => 5,
  'Junio' => 6,
  'Julio' => 7,
  'Agosto' => 8,
  'Septiembre' => 9,
  'Octubre' => 10,
  'Noviembre' => 11,
  'Diciembre' => 12
];

// 1. Validación de Seguridad inicial
if (!in_array($vista, $whitelist)) {
  header("Location: " . $redirect);
  exit();
}

$contenido = $vista; // Valor por defecto string

// 2. Lógica Específica por Vista
switch ($vista) {
  case 'Custom':
    if (isset($GLOBALS['pagina2']) && in_array($GLOBALS['pagina2'], $listaAnios)) {
      $anio = $GLOBALS['pagina2'];
      $mes = (isset($GLOBALS['pagina3']) && array_key_exists($GLOBALS['pagina3'], $listaMeses)) ? $listaMeses[$GLOBALS['pagina3']] : null;
      $contenido = ($mes) ? [$vista, $anio, $mes] : [$vista, $anio];
    } else {
      header("Location: " . $redirect);
      exit();
    }
    break;

  // FORMA CORRECTA de agrupar cases en PHP
  case 'idRAedit':
  case 'idRC':
  case 'idRFCedit':
    if (isset($GLOBALS['pagina2']) && !empty($GLOBALS['pagina2'])) {
      $contenido = [$vista, $GLOBALS['pagina2']];
    } else {
      header("Location: " . $redirect);
      exit();
    }
    break;

  case 'Agregar':
  case 'Iniciar':
  case 'Activos':
    $contenido = $vista; // Aseguramos que sea string
    break;
}

// 3. Carga de Archivo Dinámica
$nombreArchivo = (is_array($contenido)) ? $contenido[0] : $contenido;
$rutaFinal = SERVERDIR . "vista/cont/{$modulo}/{$nombreArchivo}.php";

if (file_exists($rutaFinal)) {
  require_once $rutaFinal;
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
