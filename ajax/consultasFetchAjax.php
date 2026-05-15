<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['tipo']) && isset($_POST['valor'])) {
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ ZONAS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para Datos del Cliente por ID //
    if ($tipo == 'zonas') {
        $consulta = "SELECT * FROM Zonas";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ CLIENTES ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para Datos del Cliente por ID Encriptado //
    if ($tipo == 'cliente_id_enc') {
        $consulta = "SELECT * FROM Clientes
        WHERE cliente_id = '" . decryption($valor) . "'";
    }

    // Consulta para Datos del Cliente por ID //
    if ($tipo == 'cliente_id') {
        $consulta = "SELECT * FROM Clientes
        WHERE cliente_id = '" . $valor . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ CONTRATOS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //
    // Consulta Contratos asignados a clientes por ID cliente Enc //
    if ($tipo == 'ConCli_id_enc') {
        $consulta = "SELECT * FROM Contratos
        WHERE contrato_cliente_id = '" . decryption($valor) . "'";
    }
    // Consulta Contratos asignados a clientes por ID cliente //
    if ($tipo == 'ConCli_id') {
        $consulta = "SELECT * FROM Contratos
        WHERE contrato_cliente_id = '" . $valor . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ RENTAS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //
    // Consulta Rentas asignadas a Contratos por ID de Contrato Enc //
    if ($tipo == 'RenCon_id_enc') {
        $consulta = "SELECT * FROM Rentas
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_contrato_id = '" . decryption($valor) . "'";
    }
    // Consulta Rentas asignadas a Contratos por ID de Contrato //
    if ($tipo == 'RenCon_id') {
        $consulta = "SELECT * FROM Rentas
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_contrato_id = '" . $valor . "'";
    }

    // Consulta para Datos de la Renta por ID //
    if ($tipo == 'renta_id') {
        $consulta = "SELECT * FROM Rentas
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_id = '" . $valor . "'";
    }

    // Consulta para Datos de la Renta por ID Encriptado //
    if ($tipo == 'renta_id_enc') {
        $consulta = "SELECT * FROM Rentas
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_id = '" . decryption($valor) . "'";
    }

    // Consulta para Datos de las Rentas por Zona //
    if ($tipo == 'rentasZona') {
        $consulta = "SELECT * FROM Rentas
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE renta_estado = 'Activo'
        AND renta_zona_id = '$valor'
        ORDER BY contrato_folio ASC,
        renta_folio ASC";
    }

    // Consulta de existencias de Lecturas de rentas por zona //
    if ($tipo == 'rentasLectZona') {
        $consulta = "SELECT * FROM Lecturas
        WHERE lectura_renta_id = " . $_POST['valor1'] . "
        AND MONTH(lectura_fecha) = " . $_POST['valor2'] . "
        AND YEAR(lectura_fecha) = " . $_POST['valor3'];
    }

    // Consulta de existencias de Lecturas de rentas por zona //
    if ($tipo == 'rentasRepZona') {
        $consulta = "SELECT * FROM Reportes
        WHERE reporte_renta_id = " . $_POST['valor1'] . "
        AND MONTH(reporte_fecha) = " . $_POST['valor2'] . "
        AND YEAR(reporte_fecha) = " . $_POST['valor3'];
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ TONERS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para Datos del Toner //
    if ($tipo == 'toner_codigo') {
        $consulta = "SELECT * FROM Toners
        INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id
        WHERE toner_codigo = '" . $valor . "'";
    }
    // Consulta para Datos del Toner por ID //
    if ($tipo == 'toner_id') {
        $consulta = "SELECT * FROM Toners
        INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id
        WHERE toner_id = '" . $valor . "'";
    }
    // Consulta para Datos del Toner por ID Encriptado //
    if ($tipo == 'toner_id_enc') {
        $consulta = "SELECT * FROM Toners
        INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id
        WHERE toner_id = '" . decryption($valor) . "'";
    }


    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ Refacciones ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para refacciones por codigo //
    if ($tipo == 'ref_codigo') {
        $consulta = "SELECT * FROM Refacciones
        INNER JOIN CategoriasR ON Refacciones.ref_catR_id = CategoriasR.catR_id
        INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id
        WHERE ref_codigo = '" . $valor . "'";
    }
    // Consulta para traer todas las categorias de refacciones //
    if ($tipo == 'CategoriasR') {
        $consulta = "SELECT * FROM CategoriasR";
    }
    // Consulta para refacciones por id Encriptado //
    if ($tipo == 'enc_catR_id') {
        $consulta = "SELECT * FROM CategoriasR
        WHERE catR_id = '" . decryption($valor) . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ Equipos ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para Todos los Equipos por No. de Serie //
    if ($tipo == 'equSerie') {
        $consulta = "SELECT * FROM Equipos
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        INNER JOIN ProveedoresE ON Equipos.equipo_provE_id = ProveedoresE.provE_id
        WHERE equipo_serie = '" . $valor . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ Proveedores ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //
    //              Toners              //
    // Consulta para datos de Proveedores de Toners //
    if ($tipo == 'ProveedoresT') {
        $consulta = "SELECT * FROM ProveedoresT";
    }
    // Consulta para datos de Proveedor de Toners por ID //
    if ($tipo == 'provT_id') {
        $consulta = "SELECT * FROM ProveedoresT WHERE provT_id =  '" . $valor . "'";
    }
    // Consulta para datos de Proveedor de Toners por ID encriptado //
    if ($tipo == 'enc_provT_id') {
        $consulta = "SELECT * FROM ProveedoresT WHERE provT_id =  '" . decryption($valor) . "'";
    }
    // Consulta para datos de Proveedor de Toners por Nombre //
    if ($tipo == 'nameProvT') {
        $consulta = "SELECT * FROM ProveedoresT WHERE provT_nombre = '" . $valor . "'";
    }

    //              Refacciones              //
    // Consulta para datos de Proveedores de Toners //
    if ($tipo == 'ProveedoresR') {
        $consulta = "SELECT * FROM ProveedoresR";
    }
    // Consulta para datos de  Proveedor de Refaccones por ID //
    if ($tipo == 'provR_id') {
        $consulta = "SELECT * FROM ProveedoresR WHERE provR_id =  '" . $valor . "'";
    }
    // Consulta para datos de Proveedor de Refaccones por ID encriptado //
    if ($tipo == 'enc_provR_id') {
        $consulta = "SELECT * FROM ProveedoresR WHERE provR_id =  '" . decryption($valor) . "'";
    }
    // Consulta para datos de  Proveedor de Refaccones por Nombre //
    if ($tipo == 'nameProvR') {
        $consulta = "SELECT * FROM ProveedoresR WHERE provR_nombre = '" . $valor . "'";
    }

    //              Equipos              //
    // Consulta para datos de Proveedores de Toners //
    if ($tipo == 'ProveedoresE') {
        $consulta = "SELECT * FROM ProveedoresE";
    }
    // Consulta para datos de Proveedor de Equipos por ID //
    if ($tipo == 'provE_id') {
        $consulta = "SELECT * FROM ProveedoresE WHERE provE_id =  '" . $valor . "'";
    }
    // Consulta para datos de Proveedor de Equipos por ID encriptado //
    if ($tipo == 'enc_provE_id') {
        $consulta = "SELECT * FROM ProveedoresE WHERE provE_id =  '" . decryption($valor) . "'";
    }
    // Consulta para datos de Proveedor de Equipos por Nombre //
    if ($tipo == 'nameProvE') {
        $consulta = "SELECT * FROM ProveedoresE WHERE provE_nombre = '" . $valor . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ Lecturas ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para Datos del Mapa //
    if ($tipo == 'mapaConsulta') {
        $consulta = $valor;
    }

    // Consulta para datos de lectura por ID encriptado //
    if ($tipo == 'lectura_id_enc') {
        $consulta = "SELECT * FROM Lecturas WHERE lectura_id = '" . decryption($valor) . "'";
    }

    // Consulta para datos de lectura para imprimir //
    if ($tipo == 'LecRen') {
        $renta_id = $_POST['valor1'];
        $custom_mes = $_POST['valor2'];
        $custom_anio = $_POST['valor3'];
        $consulta = "SELECT * FROM Lecturas
        INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE lectura_renta_id = $renta_id
        AND  YEAR(lectura_fecha) = $custom_anio
        AND  MONTH(lectura_fecha) = $custom_mes";
    }
    if ($tipo == 'consultaLecturas') {
        $renta_id = decryption($_POST['valor1']);
        $custom_mes = $_POST['valor2'];
        $custom_anio = $_POST['valor3'];
        $consulta = "SELECT * FROM Lecturas
        INNER JOIN Rentas ON Lecturas.lectura_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Lecturas.lectura_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE lectura_renta_id = $renta_id
        AND  YEAR(lectura_fecha) = $custom_anio
        AND  MONTH(lectura_fecha) = $custom_mes";
    }

    // Consulta para datos de lectura para imprimir //
    if ($tipo == 'consultaReportesIDenc') {
        $consulta = "SELECT * FROM Reportes
        INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Equipos ON Reportes.reporte_equipo_id = Equipos.equipo_id
        INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
        WHERE reporte_id = " . decryption($_POST['valor']);
    }

    // Consulta para datos de Facturacion //
    if ($tipo == 'existFact') {
        $consulta = "SELECT * FROM payCheck
        WHERE pCh_renta_id = " . decryption($_POST['valor']) . "
        AND MONTH(pCh_fechaFact) = " . $_POST['valor1'] . "
        AND YEAR(pCh_fechaFact) = " . $_POST['valor2'];
    }

    // Consulta para datos de Pagos Lecturas //
    if ($tipo == 'exstPayCh') {
        $consulta = "SELECT * FROM payCheck
        WHERE pCh_renta_id = " . decryption($_POST['valor']) . "
        AND MONTH(pCh_fechaFact) = " . $_POST['valor1'] . "
        AND YEAR(pCh_fechaFact) = " . $_POST['valor2'];
    }

    // Consulta para datos de Pagos //
    if ($tipo == 'existPays') {
        $consulta = "SELECT * FROM payCheck_pagos WHERE pCH_pCh_id = $valor";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ ZONAS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para datos de lectura por ID encriptado //
    if ($tipo == 'zona_id') {
        $consulta = "SELECT * FROM Zonas WHERE zona_id = $valor";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //


    // ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ COBRANZAS ~=~=~=~=~=~=~=~=~~=~=~=~=~=~=~=~=~ //

    // Consulta para datos por ID encriptado //
    if ($tipo == 'cobMpagosIDenc') {
        $consulta = "SELECT * FROM cobranzasP WHERE cobP_cobM_id = '" . decryption($valor) . "'";
    }
    // xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx //

    // Busqueda, validacion y recopilacion de datos //
    $datos = consultaData($consulta);
    if ($datos['numRows'] == 0) {
        $data = [
            'Estado' => false,
            'Data' => $datos['dataFetch']
        ];
    }
    if ($datos['numRows'] > 0) {
        $data = [
            'Estado' => true,
            'Data' => $datos['dataFetch']
        ];
    }
    echo json_encode($data);
} else {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . SERVERURL . 'login/');
    exit();
}
