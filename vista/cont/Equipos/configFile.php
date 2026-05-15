<?php
// 1. Definimos los grupos de páginas según sus requisitos
$paginasSimples = ['Agregar', 'Lista', 'Modelos'];
$paginasConID = ['ID', 'Wifi', 'Contactos', 'Ethernet', 'Contabilidad', 'Niveles', 'Registros', 'Contadores', 'Modeloid'];

// Combinamos ambos para el chequeo inicial de existencia en la lista blanca
$whiteList = array_merge($paginasSimples, $paginasConID);

// 2. Variables de control
$totalElementos = count($GLOBALS['pagina']);
$seccion = $GLOBALS['pagina1'] ?? ''; // Evitamos errores de índice no definido
$valido = false;

// 3. Verificación lógica
if (in_array($seccion, $whiteList)) {

  // Caso A: Es una página simple (Ej: Equipos/Agregar)
  // Debe estar en $paginasSimples y tener exactamente 2 elementos (Equipos y Agregar)
  if (in_array($seccion, $paginasSimples) && $totalElementos == 2) {
    $valido = true;
  }

  // Caso B: Es una página con ID (Ej: Equipos/ID/asdasd123)
  // Debe estar en $paginasConID, tener exactamente 3 elementos y que el ID no esté vacío
  elseif (in_array($seccion, $paginasConID) && $totalElementos == 3 && !empty($GLOBALS['pagina2'])) {
    $valido = true;
  }
}

// 4. Carga de archivos
if ($valido) {
  require_once SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $GLOBALS['pagina1'] . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
