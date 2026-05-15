<?php
$contenido = getContenido($_GET['vista']);

if ($contenido == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Entrada') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Salida') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Entradas') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else  if ($contenido == 'Salidas') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
