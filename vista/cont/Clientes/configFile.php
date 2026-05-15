<?php
// Configuración inicial
$redirect = SERVERURL . "Dash/";
$modulo = $GLOBALS['pagina0']; // Ej: "Clientes"
$vista  = $GLOBALS['pagina1']; // Ej: "Lista", "Agregar", "Editar"

// 1. Definimos las vistas que permiten un ID (parámetro extra)
$vistasConParametro = ['Editar'];
// 2. Definimos las vistas que son directas (sin parámetro)
$vistasSimples = ['Lista', 'Agregar', 'Fiscal'];

$whitelist = array_merge($vistasConParametro, $vistasSimples);

// 3. Validación de estructura y seguridad
if (in_array($vista, $whitelist)) {

  // Caso A: La vista requiere ID (como Editar)
  if (in_array($vista, $vistasConParametro)) {
    if (isset($GLOBALS['pagina2']) && !empty($GLOBALS['pagina2'])) {
      $contenido = [$vista, $GLOBALS['pagina2']];
    } else {
      redirect($redirect);
    }
  }
  // Caso B: La vista es simple (como Lista o Agregar)
  else {
    $contenido = $vista;
  }

  // 4. Carga Dinámica Automática
  $archivoRuta = SERVERDIR . "vista/cont/{$modulo}/{$vista}.php";

  if (file_exists($archivoRuta)) {
    require_once $archivoRuta;
  } else {
    require_once SERVERDIR . "vista/cont/404.php";
  }
} else {
  // Si no está en la whitelist, al Dashboard
  redirect($redirect);
}
