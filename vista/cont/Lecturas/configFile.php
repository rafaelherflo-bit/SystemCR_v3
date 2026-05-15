<?php
$redirect = SERVERURL . "Dash/";
$modulo = $GLOBALS['pagina0'];
$vista = $GLOBALS['pagina1'];

// 1. Configuración de Listas de Validación
$whitelist = ['Agregar', 'Custom', 'Mapa', 'ID'];
$listaAños = range(2020, 2030);
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

// 2. Validación de Seguridad inicial
if (!in_array($vista, $whitelist)) {
  redirect($redirect);
}

$contenido = $vista; // Valor por defecto

// 3. Lógica Específica por Vista
switch ($vista) {
  case 'Custom':
    // Validamos que al menos exista el año
    if (isset($GLOBALS['pagina2']) && in_array($GLOBALS['pagina2'], $listaAños)) {
      $ano = $GLOBALS['pagina2'];
      $mes = (isset($GLOBALS['pagina3']) && array_key_exists($GLOBALS['pagina3'], $listaMeses)) ? $listaMeses[$GLOBALS['pagina3']] : null;

      // Si hay mes, el contenido lleva 3 elementos, si no, solo 2
      $contenido = ($mes) ? [$vista, $ano, $mes] : [$vista, $ano];
    } else {
      redirect($redirect);
    }
    break;

  case 'ID':
    if (isset($GLOBALS['pagina2']) && !empty($GLOBALS['pagina2'])) {
      $contenido = [$vista, $GLOBALS['pagina2']];
    } else {
      redirect($redirect);
    }
    break;

  case 'Agregar':
  case 'Mapa':
    if (isset($GLOBALS['pagina2'])) redirect($redirect); // No deben llevar parámetros extra
    break;
}

// 4. Carga de Archivo Dinámica (Unificada)
// Obtenemos el nombre del archivo del primer elemento si es array, o del string directo
$nombreArchivo = (is_array($contenido)) ? $contenido[0] : $contenido;
$rutaFinal = SERVERDIR . "vista/cont/{$modulo}/{$nombreArchivo}.php";

if (file_exists($rutaFinal)) {
  isset($contenido[0]) ? $GLOBALS['contenido0'] = $contenido[0] : '';
  isset($contenido[1]) ? $GLOBALS['contenido1'] = $contenido[1] : '';
  isset($contenido[2]) ? $GLOBALS['contenido2'] = $contenido[2] : '';
  isset($contenido[3]) ? $GLOBALS['contenido3'] = $contenido[3] : '';
  isset($contenido[4]) ? $GLOBALS['contenido4'] = $contenido[4] : '';
  isset($contenido[5]) ? $GLOBALS['contenido5'] = $contenido[5] : '';

  require_once $rutaFinal;
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
