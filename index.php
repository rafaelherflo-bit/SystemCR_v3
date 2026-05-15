<?php require_once './config/SERVER.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo SERVERNAME; ?></title>
    <?php include SERVERDIR . 'vista/inc/Links.php'; ?>
</head>

<body>
    <?php
    $peticionAjax = false;
    $pagina = explode("/", $_GET['vista']);
    isset($pagina)    ? $GLOBALS['pagina']  = $pagina    : '';
    isset($pagina[0]) ? $GLOBALS['pagina0'] = $pagina[0] : '';
    isset($pagina[1]) ? $GLOBALS['pagina1'] = $pagina[1] : '';
    isset($pagina[2]) ? $GLOBALS['pagina2'] = $pagina[2] : '';
    isset($pagina[3]) ? $GLOBALS['pagina3'] = $pagina[3] : '';
    isset($pagina[4]) ? $GLOBALS['pagina4'] = $pagina[4] : '';
    isset($pagina[5]) ? $GLOBALS['pagina5'] = $pagina[5] : '';
    $GLOBALS['redirect'] = SERVERURL. $GLOBALS['pagina0'];


    $vista = getVista($pagina);
    if ($vista == 'Login' || $vista == '404') {
        require_once SERVERDIR . 'vista/cont/' . strtolower($vista) . '.php';
    } else {
        session_start();

        if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
            echo forceoutSession();
            exit();
        }

        require_once SERVERDIR . 'vista/inc/offcanvas.php';

        $DT_pageLength = 350;
        $DT_orderType = "asc";
        $DT_orderCol = 0;

        $tituloContenido = strtoupper($pagina[0]);

        if ($pagina[0] == "Dash") {
            $tituloContenido = 'DASHBOARD';
        } else if ($pagina[0] == "Retiros") {
            $tituloContenido = 'CANCELACION';
        } else if ($pagina[0] == "ReportesR") {
            $tituloContenido = 'REPORTES DE RENTAS';
        } else if ($pagina[0] == "ReportesF") {
            $tituloContenido = 'REPORTES FORANEOS';
        } else if ($pagina[0] == "Almacen") {
            $tituloContenido = strtoupper($pagina[0]) . ' | ' . strtoupper($pagina[1]) . ' &nbsp; <i class="fas fa-spray-can"></i>';
            // if ($pagina[1] == "Toners" || $pagina[1] == "Refacciones" || $pagina[1] == "Chips") {
            //     $tituloContenido = strtoupper($pagina[0]) . ' | <span class="btn btn-secondary btn-PDF" data-PDF="' . strtoupper($pagina[1]) . '"><i class="fas fa-file-pdf fa-fw"></i> &nbsp; ' . strtoupper($pagina[1]) . ' &nbsp; <i class="fas fa-spray-can"></i></span>';
            // }
        }

        echo '
        <center>
            <h3><i class="fas fa-copyright"></i><i class="fas fa-registered"></i> &nbsp; ' . $tituloContenido . '</h3>
        </center>
        ';

        /* -------- Llamamos la vista ------------------ */
        require_once $vista;
        /* -------- Llamamos tambien los modals -------- */
        require_once SERVERDIR . 'vista/inc/modals.php';

        /* -------- Llamamos tambien los Scripts Personalizados -------- */
        require_once SERVERDIR . 'vista/inc/Scripts.php';
        if (file_exists(SERVERDIR . "vista/cont/" . ucfirst($pagina[0]) . "/Scripts.php")) {
            require_once SERVERDIR . "vista/cont/" . ucfirst($pagina[0]) . "/Scripts.php";
        }
        if (file_exists(SERVERDIR . "vista/cont/" . ucfirst($pagina[0]) . "/" . ucfirst($pagina[1]) . "Scripts.php")) {
            require_once SERVERDIR . "vista/cont/" . ucfirst($pagina[0]) . "/" . ucfirst($pagina[1]) . "Scripts.php";
        }
    }
    ?>
</body>

</html>