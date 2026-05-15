<?php
$redirect = SERVERURL . "Dash/";

// 1. Definimos qué pestañas requieren un ID (parámetro $GLOBALS['pagina2'])
$requiereID = ['Detalles'];
// 2. Definimos qué pestañas son simples
$vistasSimples = ['Agregar', 'Lista', 'Otros'];

$whitelist = array_merge($requiereID, $vistasSimples);
$archivo = $GLOBALS['pagina1']; // Nombre de la vista

// 3. Verificación de Seguridad y Parámetros
if (in_array($archivo, $whitelist)) {

  // Si requiere ID, validamos que exista y no esté vacío
  if (in_array($archivo, $requiereID)) {
    if (!isset($GLOBALS['pagina2']) || $GLOBALS['pagina2'] == "") {
      redirect($redirect);
    }
    $contenido = [$archivo, $GLOBALS['pagina2']];
  } else {
    // Si es simple, validamos que NO traiga parámetros extra (opcional, por limpieza)
    $contenido = $archivo;
  }
} else {
  redirect($redirect);
}

// 4. Inclusión Automática del Archivo
// Construimos la ruta: vista/cont/Carpeta/Vista.php
$nombreVista = is_array($contenido) ? $contenido[0] : $contenido;
$rutaFinal = SERVERDIR . "vista/cont/" . $GLOBALS['pagina0'] . "/" . $nombreVista . ".php";

if (file_exists($rutaFinal)) {
  require_once $rutaFinal;
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
