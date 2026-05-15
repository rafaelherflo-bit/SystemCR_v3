<?php
$contenido = getContenido($_GET['vista']);

if ($contenido[0] == 'Custom') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido[0] . ".php";
} else if ($contenido == 'Lista') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else if ($contenido == 'Agregar') {
  require_once SERVERDIR . "vista/cont/" . $pagina[0] . "/" . $contenido . ".php";
} else {
  require_once SERVERDIR . "vista/cont/404.php";
}
