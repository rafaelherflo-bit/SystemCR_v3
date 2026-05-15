<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['arguments'])) {
  $response = [
    $Status = FALSE,
    $Data = "error"
  ];
  $params = $_POST['arguments'];

  if ($params[0] == 0) {
    $query = "";
    // Tipo 0 es de Consulta de Datos

    // Consulta para datos de Cliente por ID Encriptado //
    if ($params[1] == 'cliente_id_enc') {
      $query = "SELECT * FROM Clientes WHERE cliente_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de CobranzaM por ID Encriptado //
    if ($params[1] == 'cobM_id_enc') {
      $query = "SELECT * FROM cobranzasM WHERE cobM_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Reportes Foraneos por ID Encriptado //
    if ($params[1] == 'reporteF_id_enc') {
      $query = "SELECT * FROM ReportesF
                INNER JOIN Clientes ON ReportesF.reporteF_cliente_id = Clientes.cliente_id
                INNER JOIN Modelos ON ReportesF.reporteF_equ_modelo_id = Modelos.modelo_id
                WHERE reporteF_id = '" . decryption($params[2]) . "'";
    }

    if ($params[1] == "cambio_id_enc") {
      $query = "SELECT
        Cambios.cambio_id,
        Cambios.cambio_fecha,
        Cambios.cambio_folio,
        Cambios.cambio_motivo,
        Cambios.cambio_comm,
        Clientes.cliente_rs,
        Clientes.cliente_rfc,
        Contratos.contrato_folio,
        Rentas.renta_folio,
        Rentas.renta_depto,
        Zonas.zona_nombre,
        EquiposIng.equipo_serie AS equipoIng_serie,
        EquiposIng.equipo_codigo AS equipoIng_codigo,
        EquiposIng.equipo_modelo_id AS equipoIng_modelo_id,
        ModelosIng.modelo_modelo AS modeloIng_modelo,
        ModelosIng.modelo_linea AS modeloIng_linea,
        EquiposRet.equipo_serie AS equipoRet_serie,
        EquiposRet.equipo_codigo AS equipoRet_codigo,
        EquiposRet.equipo_modelo_id AS equipoRet_modelo_id,
        ModelosRet.modelo_modelo AS modeloRet_modelo,
        ModelosRet.modelo_linea AS modeloRet_linea
        FROM Cambios
        INNER JOIN Rentas ON Cambios.cambio_renta_id = Rentas.renta_id
        INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
        INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
        INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
        INNER JOIN Equipos AS EquiposIng ON Cambios.cambio_equipoIng_id = EquiposIng.equipo_id
        INNER JOIN Modelos AS ModelosIng ON EquiposIng.equipo_modelo_id = ModelosIng.modelo_id
        INNER JOIN Equipos AS EquiposRet ON Cambios.cambio_equipoRet_id = EquiposRet.equipo_id
        INNER JOIN Modelos AS ModelosRet ON EquiposRet.equipo_modelo_id = ModelosRet.modelo_id
        WHERE cambio_id = '" . decryption($params[2]) . "'";
    }


    // ======================================= COTIZADOR ======================================= //
    // Consulta para datos de Productos para cotizador por ID Encriptado //
    if ($params[1] == 'cotD_prod_id_enc') {
      $query = "SELECT * FROM AlmacenP WHERE AlmP_id = '" . decryption($params[2]) . "'";
    }
    // Consulta para Registro de cotizador por ID Encriptado //
    if ($params[1] == 'encIDcotM') {
      $query = "SELECT * FROM cotizadorM WHERE cotM_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Reportes de Rentas por ID Encriptado //
    if ($params[1] == 'reporte_id_enc') {
      $query = "SELECT * FROM Reportes
                INNER JOIN Rentas ON Reportes.reporte_renta_id = Rentas.renta_id
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                INNER JOIN Equipos ON Reportes.reporte_equipo_id = Equipos.equipo_id
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE reporte_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Registro de Salida de Toner por ID Encriptado //
    if ($params[1] == 'tonerRO_id_enc') {
      $query = "SELECT * FROM TonersRegistrosS
                INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
                WHERE tonerRO_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Registro de Salida de Toner por ID Encriptado //
    if ($params[1] == 'dataRST') {
      if ($params[2] == 'Venta') {
        $query = "SELECT * FROM TonersRegistrosS
                  INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
                  INNER JOIN Clientes ON TonersRegistrosS.tonerRO_identificador = Clientes.cliente_id
                  WHERE tonerRO_id = '" . decryption($params[3]) . "'";
      } else if ($params[2] == 'Renta') {
        $query = "SELECT * FROM TonersRegistrosS
                  INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
                  INNER JOIN Rentas ON TonersRegistrosS.tonerRO_identificador = Rentas.renta_id
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                  WHERE tonerRO_id = '" . decryption($params[3]) . "'";
      } else if ($params[2] == 'Interno') {
        $query = "SELECT * FROM TonersRegistrosS
                  INNER JOIN Toners ON TonersRegistrosS.tonerRO_toner_id = Toners.toner_id
                  WHERE tonerRO_id = '" . decryption($params[3]) . "'";
      }
    }

    // Consulta para datos de Equipo por ID Encriptado //
    if ($params[1] == 'equipo_id_enc') {
      $query = "SELECT * FROM Equipos
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE equipo_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Equipo por ID Encriptado //
    if ($params[1] == 'modelo_id_enc') {
      $query = "SELECT * FROM Modelos WHERE modelo_id = '" . decryption($params[2]) . "'";
    }

    // Consulta para datos de Pagos Lecturas //
    if ($params[1] == 'checkLectChP') {
      $query = "SELECT * FROM LectChP
                WHERE LChP_renta_id = '" . decryption($params[2]) . "'
                AND LChP_month = '" . $params[3] . "'
                AND LChP_year = '" . $params[4] . "'";
    }

    // Consulta para Consultar Reportes en Zonas Por Dia //
    if ($params[1] == 'zonaRepotesDia') {
      $query = "SELECT * FROM Reportes
                WHERE reporte_renta_id = " . $params[2] . "
                AND YEAR(reporte_fecha) = " . $params[3] . "
                AND MONTH(reporte_fecha) = " . $params[4] . "
                AND DAY(reporte_fecha) = " . $params[5];
    }

    // Consulta para Consultar Reportes en Zonas Por Mes //
    if ($params[1] == 'zonaRepotesMes') {
      $query = "SELECT * FROM Reportes
                WHERE reporte_renta_id = " . $params[2] . "
                AND YEAR(reporte_fecha) = " . $params[3] . "
                AND MONTH(reporte_fecha) = " . $params[4];
    }

    // Consulta para Consultar Reportes en Zonas Por Anio //
    if ($params[1] == 'zonaRepotesAnio') {
      $query = "SELECT * FROM Reportes
                WHERE reporte_renta_id = " . $params[2] . "
                AND YEAR(reporte_fecha) = " . $params[3];
    }

    if ($params[1] == "lecturaRentasZona") {
      $query = "SELECT renta_id, cliente_rs, cliente_rfc, renta_estado, renta_depto, renta_coor, renta_finicio, renta_folio, contrato_folio, zona_id,
                ( SELECT lectura_fecha FROM Lecturas WHERE lectura_renta_id = renta_id AND MONTH (lectura_fecha) = " . $params[3] . " AND YEAR (lectura_fecha) = " . $params[4] . ") AS lectura_fecha
                FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                WHERE renta_id IN ( SELECT lectura_renta_id FROM Lecturas )
                AND zona_id = " . $params[2] . "
                AND MONTH(renta_finicio) <= " . $params[3] . "
                AND YEAR(renta_finicio) <= " . $params[4] . "
                AND DAY(renta_finicio) > 1
                AND renta_estado = 'Activo'";
    }

    // Consulta para Consultar Reportes en Zonas Por Anio //
    if ($params[1] == 'rentasZona') {
      $query = "SELECT * FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
                INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
                INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                WHERE renta_estado = 'Activo'
                AND renta_zona_id = " . $params[2] . "
                ORDER BY contrato_folio ASC,
                renta_folio ASC";
    }



    /* ========================================= TONERS ======================================== */

    // Consulta para Consultar Datos del toner por ID Encriptado //
    if ($params[1] == 'toner_id_enc') {
      $query = "SELECT * FROM Toners
                INNER JOIN ProveedoresT ON Toners.toner_provT_id = ProveedoresT.provT_id
                WHERE toner_id = " . decryption($params[2]);
    }

    /* ====================================== REFACCIONES ====================================== */

    // Consulta para Consultar Datos de la refaccion por ID Encriptado //
    if ($params[1] == 'ref_id_enc') {
      $query = "SELECT * FROM Refacciones
                INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id
                WHERE ref_id = " . decryption($params[2]);
    }

    /* ======================== ZONAS ======================== */

    // Consulta para Consultar Datos por ID Encriptado //
    if ($params[1] == 'zona_id') {
      $query = "SELECT * FROM Zonas
                WHERE zona_id = " . $params[2];
    }



    // Busqueda, validacion y recopilacion de datos //
    $datos = consultaData($query);
    if ($datos['numRows'] == 0) {
      $response = [
        'Status' => false,
        'Data' => $datos['dataFetch']
      ];
    }
    if ($datos['numRows'] > 0) {
      $response = [
        'Status' => true,
        'Data' => $datos['dataFetch']
      ];
    }
  } else if ($params[0] == 1) {

    // - - - - - - ALMACEN - - - - - - //
    // Borrar Registro
    if ($params[1] == 'delTonerIdEnc' || $params[1] == 'delChipIdEnc' || $params[1] == 'delRefaccionIdEnc' || $params[1] == 'delServicioIdEnc') {
      session_start();
      // if ($_SESSION['id'] != 1 && $_SESSION['id'] != 2) {
      //   $Status = FALSE;
      //   $Data = "No se tiene el permiso para borrar el registro.";
      // } else {
      $QRY = consultaData("SELECT * FROM AlmacenD WHERE AlmDP_id = '" . decryption($params[2]) . "'");
      if ($QRY['numRows'] >= 1) {
        $Status = FALSE;
        $Data = "Existen movimientos relacionados con este producto, no es posble eliminarlo.";
      } else {
        if (sentenciaData("DELETE FROM AlmacenP WHERE AlmP_id = '" . decryption($params[2]) . "'")) {
          $Status = TRUE;
          $Data = "Registro Borrado.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar el registro.";
        }
      }
      // }
    }

    // Borrar Registro de Detalle
    if ($params[1] == 'delAlmD') {
      if (sentenciaData("DELETE FROM AlmacenD WHERE AlmD_id = '" . decryption($params[2]) . "'")) {
        $Status = TRUE;
        $Data = "Registro Borrado.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar el registro.";
      }
    }

    // Borrar Registro Principal
    if ($params[1] == 'delAlmM') {
      $AlmM_id = decryption($params[2]);
      $QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = '$AlmM_id'");
      $AlmM_DATA = $QRY['dataFetch'][0];

      if ($AlmM_DATA['AlmM_estado'] == 0 || $AlmM_DATA['AlmM_tipo'] > 0) {
        if (is_file(SERVERDIR . "DocsCR/ALMACEN/Evidencias/" . $AlmM_DATA['AlmM_folio'] . ".pdf")) {
          $fileStatus = (unlink(SERVERDIR . "DocsCR/ALMACEN/Evidencias/" . $AlmM_DATA['AlmM_folio'] . ".pdf")) ? TRUE : FALSE;
        } else {
          $fileStatus = TRUE;
        }

        if ($fileStatus) {
          if (sentenciaData("DELETE FROM AlmacenD WHERE AlmDM_id = '$AlmM_id'")) {
            if (sentenciaData("DELETE FROM AlmacenM WHERE AlmM_id = '$AlmM_id'")) {
              $Status = TRUE;
              $Data = "Registro Borrado.";
            } else {
              $Status = FALSE;
              $Data = "No se pudo borrar el registro principal.";
            }
          } else {
            $Status = FALSE;
            $Data = "No se pudieron borrar los registros de detalles.";
          }
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar la evidencia.";
        }
      } else {
        $Status = TRUE;
        $Data = "Exito, la accion no baja a negativo el stock de ningun producto.";
        // Variable con funcion que realiza un array recopilando las entradas y salidas de los productos en Almacen basado en los movimientos (Funcion anidada en SERVER.php)
        $SQL_AlmP_total = "SELECT AlmDP_id, SUM(AlmD_cantidad) AS AlmP_stock, AlmP_codigo FROM AlmacenD
                          INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
                          INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                          WHERE AlmM_estado = 1
                          GROUP BY AlmDP_id
                          ORDER BY AlmDP_id ASC";
        $QRY_AlmP_total = consultaAlmacenP()['dataFetch'];

        $SQL_AlmP_AlmMid = "SELECT AlmDP_id, SUM(AlmD_cantidad) AS AlmP_stock, AlmP_codigo FROM AlmacenD
                          INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
                          INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                          WHERE AlmDM_id = '$AlmM_id'
                          AND AlmM_estado = 1
                          GROUP BY AlmDP_id
                          ORDER BY AlmDP_id ASC";
        $QRY_AlmDP_AlmMid = consultaData($SQL_AlmP_AlmMid);


        foreach ($QRY_AlmDP_AlmMid['dataFetch'] as $AlmP_AlmMid) {
          $indice = array_search($AlmP_AlmMid['AlmDP_id'], array_column($QRY_AlmP_total, 'AlmP_id'));

          if ($indice !== false) {
            if ($QRY_AlmP_total[$indice]['AlmP_stock'] < $AlmP_AlmMid['AlmP_stock']) {
              $menos = $QRY_AlmP_total[$indice]['AlmP_stock'] - $AlmP_AlmMid['AlmP_stock'];
              $Status = FALSE;
              $Data = "No es posible desactivar el registro, el stock se reduce en $menos, para el producto " . $QRY_AlmP_total[$indice]['AlmP_codigo'];
              break;
            }
          }
        }
        if ($Status) {
          if (is_file(SERVERDIR . "DocsCR/ALMACEN/Evidencias/" . $AlmM_DATA['AlmM_folio'] . ".pdf")) {
            $fileStatus = (unlink(SERVERDIR . "DocsCR/ALMACEN/Evidencias/" . $AlmM_DATA['AlmM_folio'] . ".pdf")) ? TRUE : FALSE;
          } else {
            $fileStatus = TRUE;
          }

          if ($fileStatus) {
            if (sentenciaData("DELETE FROM AlmacenD WHERE AlmDM_id = '$AlmM_id'")) {
              if (sentenciaData("DELETE FROM AlmacenM WHERE AlmM_id = '$AlmM_id'")) {
                $Status = TRUE;
                $Data = "Registro Borrado.";
              } else {
                $Status = FALSE;
                $Data = "No se pudo borrar el registro principal.";
              }
            } else {
              $Status = FALSE;
              $Data = "No se pudieron borrar los registros de detalles.";
            }
          } else {
            $Status = FALSE;
            $Data = "No se pudo borrar la evidencia.";
          }
        }
      }
    }

    // - - - - - - ACTIVAR O DESACTIVAR REGISTRO MAIN DE ALMACEN - - - - - - //
    // Desactivar Registro Main de Almacen
    if ($params[1] == 'desactivarAlmM') {
      $AlmM_id = decryption($params[2]);
      $QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = '$AlmM_id'");
      $AlmM_DATA = $QRY['dataFetch'][0];

      if ($AlmM_DATA['AlmM_tipo'] > 0) {
        if (sentenciaData("UPDATE AlmacenM SET AlmM_estado = 0 WHERE AlmM_id = '$AlmM_id'")) {
          $Status = TRUE;
          $Data = "Registro Desactivado.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo desactivar el registro Principal.";
        }
      } else {
        $Status = TRUE;
        $Data = "Exito, la accion no baja a negativo el stock de ningun producto.";
        // Variable con funcion que realiza un array recopilando las entradas y salidas de los productos en Almacen basado en los movimientos (Funcion anidada en SERVER.php)
        $QRY_AlmP_total = consultaAlmacenP()['dataFetch'];

        $SQL_AlmP_AlmMid = "SELECT AlmDP_id, SUM(AlmD_cantidad) AS AlmP_stock, AlmP_codigo FROM AlmacenD
                          INNER JOIN AlmacenP ON AlmacenD.AlmDP_id = AlmacenP.AlmP_id
                          INNER JOIN AlmacenM ON AlmacenD.AlmDM_id = AlmacenM.AlmM_id
                          WHERE AlmDM_id = '$AlmM_id'
                          AND AlmM_estado = 1
                          GROUP BY AlmDP_id
                          ORDER BY AlmDP_id ASC";
        $QRY_AlmDP_AlmMid = consultaData($SQL_AlmP_AlmMid);


        foreach ($QRY_AlmDP_AlmMid['dataFetch'] as $AlmP_AlmMid) {
          $indice = array_search($AlmP_AlmMid['AlmDP_id'], array_column($QRY_AlmP_total, 'AlmP_id'));

          if ($indice !== false) {
            if ($QRY_AlmP_total[$indice]['AlmP_stock'] < $AlmP_AlmMid['AlmP_stock']) {
              $menos = $QRY_AlmP_total[$indice]['AlmP_stock'] - $AlmP_AlmMid['AlmP_stock'];
              $Status = FALSE;
              $Data = "No es posible desactivar el registro, el stock se reduce en $menos, para el producto " . $QRY_AlmP_total[$indice]['AlmP_codigo'];
              break;
            }
          }
        }
        if ($Status) {
          sentenciaData("UPDATE AlmacenM SET AlmM_estado = 0 WHERE AlmM_id = '$AlmM_id'");
        }
      }
    }

    // - - - - - - PROVEEDORES - - - - - - //
    // Borrar Registro
    if ($params[1] == 'delProveedorIdEnc') {
      session_start();
      if ($_SESSION['id'] != 1 && $_SESSION['id'] != 2) {
        $Status = FALSE;
        $Data = "No se tiene el permiso para borrar el registro.";
      } else if (sentenciaData("DELETE FROM AlmacenProvs WHERE AlmProv_id = '" . decryption($params[2]) . "'")) {
        $Status = TRUE;
        $Data = "Registro Borrado.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar el registro.";
      }
    }
    // - - - - - - PROVEEDORES - - - - - - //






    // Tipo 1 es de Insertcion, borrado o Actualizacion de Datos
    if ($params[1] == 'insertLectChP') {
      // if (consultaData("SELECT * FROM LectChP WHERE LChP_folio = '" . $params[5] . "'")['numRows'] >= 1) {
      //     $Status = false;
      // } else {
      $sentenciaData = "INSERT INTO LectChP (LChP_renta_id, LChP_year, LChP_month, LChP_folio) VALUES (" . decryption($params[2]) . ", '" . $params[3] . "', '" . $params[4] . "', '" . $params[5] . "')";
      $Status = sentenciaData($sentenciaData);
      // }

      if ($Status) {
        $Data = "Registro agregado correctamente.";
      } else {
        $Data = "No se pudo agregar el registro.";
      }
    }

    // Eliminar registro de Cotizacion, Si tiene cargos los eliminara igual.
    if ($params[1] == "deleteCotM") {
      $cotM_SQL = "SELECT * FROM cotizadorM WHERE cotM_id = " . decryption($params[2]);
      $cotM_QRY = consultaData($cotM_SQL);

      $cotD_SQL = "SELECT * FROM cotizadorD WHERE cotD_cotM_id = " . decryption($params[2]);
      $cotD_QRY = consultaData($cotD_SQL);

      if ($cotM_QRY['numRows'] <= 0) {
        $Status = FALSE;
        $Data = "No existe el registro que deseas eliminar verificalo.";
      } else if ($cotM_QRY['numRows'] > 1) {
        $Status = FALSE;
        $Data = "No se puede eliminar el registro principal si existen Cargos agregados.";
      } else {

        $statusDetalles = TRUE;
        if ($cotD_QRY['numRows'] >= 1) {
          $statusDetalles = sentenciaData("DELETE FROM cotizadorD WHERE cotD_cotM_id = '" . decryption($params[2]) . "'");
        }

        if ($statusDetalles) {
          $Status = sentenciaData("DELETE FROM cotizadorM WHERE cotM_id = '" . decryption($params[2]) . "'");
          $Data = "Registro eliminado correctamente";
        }
      }
    }

    // Eliminar registro de Cobranza y de existir eliminar tambien el archivo de factura; Pero si existen cobros o pagos no se podra eliminar el registro.
    if ($params[1] == "cobM_Eliminar") {
      $cobM_SQL = "SELECT * FROM cobranzasM WHERE cobM_id = " . decryption($params[2]);
      $cobM_QRY = consultaData($cobM_SQL);

      $cobP_SQL = "SELECT * FROM cobranzasP WHERE cobP_cobM_id = " . decryption($params[2]);
      $cobP_QRY = consultaData($cobP_SQL);

      $cobC_SQL = "SELECT * FROM cobranzasC WHERE cobC_cobM_id = " . decryption($params[2]);
      $cobC_QRY = consultaData($cobC_SQL);

      if ($cobM_QRY['numRows'] <= 0) {
        $Status = FALSE;
        $Data = "No existe el registro que deseas eliminar verificalo.";
      } else if ($cobP_QRY['numRows'] >= 1) {
        $Status = FALSE;
        $Data = "No se puede eliminar el registro principal si existen Pagos efectuados.";
      } else if ($cobC_QRY['numRows'] >= 1) {
        $Status = FALSE;
        $Data = "No se puede eliminar el registro principal si existen Cargos agregados.";
      } else if ($cobM_QRY['numRows'] > 1) {
        $Status = FALSE;
        $Data = "Error grave registro duplicado, verifica duplicidad de ID primario.";
      } else {
        $cobM_Data = $cobM_QRY['dataFetch'][0];
        $sentenciaData = "DELETE FROM cobranzasM WHERE cobM_id = '" . decryption($params[2]) . "'";
        if ($cobM_Data['cobM_archivo'] != "0") {
          if (unlink(SERVERDIR . "DocsCR/Facturas/" . explode("-", $cobM_Data['cobM_fecha'])[0] . "/" . explode("-", $cobM_Data['cobM_fecha'])[1] . "/" . $cobM_Data['cobM_archivo'])) {
            $Status = sentenciaData($sentenciaData);
            $Data = "Registro eliminado correctamente";
          } else {
            $Status = FALSE;
            $Data = "No se pudo borrar el archivo de factura.";
          }
        } else {
          $Status = sentenciaData($sentenciaData);
          $Data = "Registro eliminado correctamente";
        }
      }
    }

    // Borrar PDF de Reporte Foraneo
    if ($params[1] == 'tonerDisableIDenc') {
      $sentenciaData = "UPDATE Toners SET toner_estado = 'Inactivo' WHERE toner_id = '" . decryption($params[2]) . "'";
      $Status = sentenciaData($sentenciaData);
      if ($Status) {
        $Data = "Toner Deshabilitado.";
      } else {
        $Data = "No se pudo actualizar el estatus del toner.";
      }
    }

    // Borrar PDF de Reporte Foraneo
    if ($params[1] == 'delRepFPDF') {
      if (unlink(SERVERDIR . "DocsCR/ReportesF/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . $params[4] . ".pdf")) {
        $Data = "PDF Borrado.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar el archivo.";
      }
    }

    // Borrar PDF de RST
    if ($params[1] == 'delEviPDF') {
      $tipo = $params[2];
      $folio = $params[3];
      list($anio, $mes, $dia) = explode("-", $params[4]);
      if (unlink(SERVERDIR . "DocsCR/ALMACEN/" . $tipo . "/" . $anio . "/" . $mes . "/" . $folio . ".pdf")) {
        $Status = TRUE;
        $Data = "Evidencia Borrada.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar el archivo.";
      }
    }

    // BORRAR IMAGEN DE REFACCION
    if ($params[1] == 'delRefImg') {
      $SQL = "SELECT * FROM Refacciones WHERE ref_id = " . decryption($params[2]);
      $dataRef = consultaData($SQL)['dataFetch'][0];

      $codigo = $params[2];
      if (unlink(SERVERDIR . "DocsCR/ALMACEN/REFACCIONES/" . $dataRef['ref_codigo'] . ".jpg")) {
        $Status = TRUE;
        $Data = "Imagen Borrada.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar la Imagen.";
      }
    }

    // Borrar Detalle de Cotizacion Detalles
    if ($params[1] == 'cotDdel_enc') {
      if (sentenciaData("DELETE FROM cotizadorD WHERE cotD_id = '" . decryption($params[2]) . "'")) {
        $Status = TRUE;
        $Data = "Registro Borrado.";
      } else {
        $Status = FALSE;
        $Data = "No se pudo borrar el registro.";
      }
    }

    // Borrar PDF de Reporte de Renta
    if ($params[1] == 'delRepPDF') {
      $sentenciaData = "UPDATE Reportes SET reporte_archivo = '' WHERE reporte_id = '" . decryption($params[2]) . "'";
      $Status = sentenciaData($sentenciaData);
      if ($Status) {
        if (unlink(SERVERDIR . "DocsCR/ReportesCR/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . $params[4])) {
          $Data = "PDF Borrado.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar el archivo.";
        }
      } else {
        $Data = "No se pudo actualizar el dato en el registro.";
      }
    }

    // Borrar PDF de Cambio
    if ($params[1] == 'delCamPDF') {
      $sentenciaData = "UPDATE Cambios SET cambio_file = '' WHERE cambio_id = '" . decryption($params[2]) . "'";
      $Status = sentenciaData($sentenciaData);
      if ($Status) {
        if (unlink(SERVERDIR . "DocsCR/CambiosDeEquipos/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . $params[4] . ".pdf")) {
          $Data = "Evidencia Borrada.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar el archivo.";
        }
      } else {
        $Data = "No se pudo actualizar el dato en el registro.";
      }
    }

    // Borrar Pagina de Estado de Lectura
    if ($params[1] == 'delPEidEnc') {
      if (file_exists(SERVERDIR . "DocsCR/Lecturas/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . "Formatos/" . $params[4])) {
        $Status = TRUE;
      } else {
        $sentenciaData = "UPDATE Lecturas SET lectura_pdf = '' WHERE lectura_id = '" . decryption($params[2]) . "'";
        $Status = sentenciaData($sentenciaData);
      }

      if ($Status) {
        if (unlink(SERVERDIR . "DocsCR/Lecturas/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . "PE/" . $params[4])) {
          $Data = "Pagina de Estado Borrada.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar la pagina de estado.";
        }
      } else {
        $Data = "No se pudo actualizar el dato en el registro.";
      }
    }

    // Borrar Formato de Lectura
    if ($params[1] == 'delFLidEnc') {
      if (file_exists(SERVERDIR . "DocsCR/Lecturas/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . "PE/" . $params[4])) {
        $Status = TRUE;
      } else {
        $sentenciaData = "UPDATE Lecturas SET lectura_pdf = '' WHERE lectura_id = '" . decryption($params[2]) . "'";
        $Status = sentenciaData($sentenciaData);
      }

      if ($Status) {
        if (unlink(SERVERDIR . "DocsCR/Lecturas/" . explode("-", $params[3])[0] . "/" . explode("-", $params[3])[1] . "/" . "Formatos/" . $params[4])) {
          $Data = "Formato de Lectura Borrado.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar el formato de lectura.";
        }
      } else {
        $Data = "No se pudo actualizar el dato en el registro.";
      }
    }

    if ($params[1] == 'editLectChP') {
      // if (consultaData("SELECT * FROM LectChP WHERE LChP_folio = '" . $params[5] . "'")['numRows'] >= 1) {
      //     $Status = false;
      // } else {
      $sentenciaData = "UPDATE LectChP SET LChP_folio = '" . $params[2] . "' WHERE LChP_id = '" . decryption($params[3]) . "'";
      $Status = sentenciaData($sentenciaData);
      // }


      if ($Status) {
        $Data = "Registro Completado.";
      } else {
        $Data = "Error.";
      }
    }

    if ($params[1] == 'delLectChP') {
      // if (consultaData("SELECT * FROM LectChP WHERE LChP_folio = '" . $params[5] . "'")['numRows'] >= 1) {
      //     $Status = false;
      // } else {
      $sentenciaData = "DELETE FROM LectChP WHERE LChP_id = '" . decryption($params[2]) . "'";
      $Status = sentenciaData($sentenciaData);
      // }


      if ($Status) {
        $Data = "Registro Completado.";
      } else {
        $Data = "Error.";
      }
    }

    if ($params[1] == 'delRepAct') {
      // if (consultaData("SELECT * FROM LectChP WHERE LChP_folio = '" . $params[5] . "'")['numRows'] >= 1) {
      //     $Status = false;
      // } else {
      $sentenciaData = "DELETE FROM Reportes WHERE reporte_id = '" . decryption($params[2]) . "'";
      $Status = sentenciaData($sentenciaData);
      // }


      if ($Status) {
        $Data = "Registro Completado.";
      } else {
        $Data = "Error.";
      }
    }

    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == 2) {
    $Image = "";


    /* ====================================== LECTURAS ====================================== */

    // Consulta para Consultar Todas las rentas tomando en cuenta la existencia de lecturas del mes y anio //
    if ($params[1] == 'rentas_lecturas_exist') {
      list($anio, $mes, $dia) = explode("-", $params[2]);

      $SQL = "SELECT cliente_rs, cliente_rfc, renta_id, renta_estado, renta_depto, renta_coor, renta_finicio, renta_folio, zona_id , zona_nombre, contrato_folio, modelo_linea, modelo_modelo, modelo_tipo, equipo_serie, equipo_codigo,
              ( SELECT lectura_id FROM Lecturas WHERE lectura_renta_id = renta_id AND MONTH (lectura_fecha) = " . $mes . " AND YEAR (lectura_fecha) = " . $anio . ") AS lectura_id_act, ( SELECT lectura_fecha FROM Lecturas WHERE lectura_renta_id = renta_id AND MONTH (lectura_fecha) = " . $mes . " AND YEAR (lectura_fecha) = " . $anio . ") AS lectura_fecha_act
              FROM Rentas
              INNER JOIN Equipos ON Rentas.renta_equipo_id = Equipos.equipo_id
              INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
              INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
              INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
              INNER JOIN Zonas ON Rentas.renta_zona_id = Zonas.zona_id
              WHERE renta_id IN ( SELECT lectura_renta_id FROM Lecturas )
              AND renta_estado = 'Activo'";

      $QRY = consultaData($SQL)['dataFetch'];
      $Data = [];
      foreach ($QRY as $dato) {
        if ($dato['lectura_fecha_act'] == NULL) {
          $lectura_status = FALSE;
        } else {
          $lectura_status = TRUE;
        }

        $array = [
          "lectura_status" => $lectura_status,
          "renta_id" => encryption($dato['renta_id']),
          "renta_data" => $dato['contrato_folio'] . "-" . $dato['renta_folio'] . " | " . $dato['cliente_rs'] . " | " . $dato['renta_depto'] . " | " . $dato['equipo_serie']
        ];
        array_push($Data, $array);
      }

      $response = [
        'Status' => TRUE,
        'Data' => $Data
      ];
    }

    /* ====================================== REFACCIONES ====================================== */

    // Consulta para Consultar Datos de la refaccion por ID Encriptado //
    if ($params[1] == 'ref_id_enc') {
      $SQL = "SELECT * FROM Refacciones
                INNER JOIN ProveedoresR ON Refacciones.ref_provR_id = ProveedoresR.provR_id
                WHERE ref_id = " . decryption($params[2]);
      $QRY = consultaData($SQL);

      $Status = "";
      $Imgage = "";
      $Data = "";

      if ($QRY['numRows'] >= 2) {
        $Status = FALSE;
        $Data = "Existe mas de un registro con ese ID";
      } else if ($QRY['numRows'] <= 0) {
        $Status = FALSE;
        $Data = "No existe registro con ese ID";
      } else {
        $Status = TRUE;

        $dataRef = $QRY['dataFetch'][0];

        $Proveedores = [];
        foreach (consultaData("SELECT * FROM ProveedoresR WHERE provR_id != " . $dataRef['ref_provR_id'] . " AND provR_estado = 'Activo'")['dataFetch'] as $row) {
          $array = [
            "provR_id" => encryption($row['provR_id']),
            "provR_nombre" => $row['provR_nombre']
          ];
          array_push($Proveedores, $array);
        }

        $dataRef['ref_provR_id'] = encryption($dataRef['ref_provR_id']);

        $Data = [
          "Proveedores" => $Proveedores,
          "Refaccion" => $dataRef
        ];


        if (file_exists(SERVERDIR . "DocsCR/ALMACEN/REFACCIONES/" . $dataRef['ref_codigo'] . ".jpg")) {
          $Image = [
            'Status' => TRUE,
            'URL' => SERVERURL . "DocsCR/ALMACEN/REFACCIONES/" . $dataRef['ref_codigo'] . ".jpg"
          ];
        } else {
          $Image = [
            'Status' => FALSE,
            'URL' => ""
          ];
        }
      }

      $response = [
        'Status' => $Status,
        'Image' => $Image,
        'Data' => $Data
      ];
    }

    // Obteniendo Stock de toner con ID de Toner encriptado
    if ($params[1] == 'tonerStockCheckEnc') {
      $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . decryption($params[2]))['dataFetch'][0]['tonerET'];
      $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . decryption($params[2]))['dataFetch'][0]['tonerST'];
      $tonersStock = $tonerET - $tonerST;
      $response = [
        'Status' => TRUE,
        'Data' => $tonersStock
      ];
    }

    // Obteniendo Datos para Editar Registro de Salida de Toner
    if ($params[1] == 'dataRST') {
      $tonerRO_id = decryption($params[3]);
      $QRY0 = consultaData("SELECT * FROM TonersRegistrosS WHERE tonerRO_id = '$tonerRO_id'");
      if ($QRY0['numRows'] == 0) {
        $Status = FALSE;
        $Data = "No existe RST.";
      } else {
        $QRY0 = $QRY0['dataFetch'][0];

        if ($params[2] == 'Venta') {
          $SQL = "SELECT * FROM Clientes";
          if ($QRY0['tonerRO_tipo'] == $params[2]) {
            $SQL1 = $SQL . " WHERE cliente_id = " . $QRY0['tonerRO_identificador'];
            $QRY1 = consultaData($SQL1);

            $SQL2 = $SQL . " WHERE cliente_id != " . $QRY0['tonerRO_identificador'];
            $QRY2 = consultaData($SQL2);

            if ($QRY1['numRows'] == 0) {
              $Status = FALSE;
              $Data = "No hay datos de la tabla clientes para mostrar.";
            } else if ($QRY2['numRows'] == 0) {
              $Status = FALSE;
              $Data = "No exite el cliente identificado.";
            } else {
              $Status = TRUE;
              $Clientes = [];
              foreach ($QRY2['dataFetch'] as $row) {
                $array = [
                  "cliente_id" => encryption($row['cliente_id']),
                  "cliente_rs" => $row['cliente_rs'],
                  "cliente_rfc" => $row['cliente_rfc']
                ];
                array_push($Clientes, $array);
              }
              $QRY1 = $QRY1['dataFetch'][0];
              $Data = [
                "Cliente" => [
                  "cliente_id" => encryption($QRY1['cliente_id']),
                  "cliente_rs" => $QRY1['cliente_rs'],
                  "cliente_rfc" => $QRY1['cliente_rfc']
                ],
                "Clientes" => $Clientes
              ];
            }
          } else {
            $Status = TRUE;
            $Clientes = [];
            foreach (consultaData("SELECT * FROM Clientes")['dataFetch'] as $row) {
              $array = [
                "cliente_id" => encryption($row['cliente_id']),
                "cliente_rs" => $row['cliente_rs'],
                "cliente_rfc" => $row['cliente_rfc']
              ];
              array_push($Clientes, $array);
            }
            $Data = [
              "Cliente" => [],
              "Clientes" => $Clientes
            ];
          }
        } else if ($params[2] == 'Renta') {
          $SQL = "SELECT * FROM Rentas
                  INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                  INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id";

          if ($QRY0['tonerRO_tipo'] == $params[2]) {
            $SQL1 = $SQL . " WHERE renta_id = " . $QRY0['tonerRO_identificador'];
            $QRY1 = consultaData($SQL1);

            $SQL2 = $SQL . " WHERE renta_id != " . $QRY0['tonerRO_identificador'];
            $QRY2 = consultaData($SQL2);

            if ($QRY1['numRows'] == 0) {
              $Status = FALSE;
              $Data = "No hay datos de la tabla Rentas para mostrar.";
            } else if ($QRY2['numRows'] == 0) {
              $Status = FALSE;
              $Data = "No exite la renta identificada.";
            } else {
              $Status = TRUE;

              $Rentas = [];
              foreach ($QRY2['dataFetch'] as $row) {
                $array = [
                  "renta_id" => encryption($row['renta_id']),
                  "contrato_folio" => $row['contrato_folio'],
                  "renta_folio" => $row['renta_folio'],
                  "cliente_rs" => $row['cliente_rs'],
                  "renta_depto" => $row['renta_depto']
                ];
                array_push($Rentas, $array);
              }

              $QRY1 = $QRY1['dataFetch'][0];
              $Data = [
                "Renta" => [
                  "renta_id" => encryption($QRY1['renta_id']),
                  "contrato_folio" => $QRY1['contrato_folio'],
                  "renta_folio" => $QRY1['renta_folio'],
                  "cliente_rs" => $QRY1['cliente_rs'],
                  "renta_depto" => $QRY1['renta_depto']
                ],
                "Rentas" => $Rentas
              ];
            }
          } else {
            $Status = TRUE;

            $Rentas = [];
            foreach (consultaData($SQL)['dataFetch'] as $row) {
              $array = [
                "renta_id" => encryption($row['renta_id']),
                "contrato_folio" => $row['contrato_folio'],
                "renta_folio" => $row['renta_folio'],
                "cliente_rs" => $row['cliente_rs'],
                "renta_depto" => $row['renta_depto']
              ];
              array_push($Rentas, $array);
            }

            $Data = [
              "Renta" => [],
              "Rentas" => $Rentas
            ];
          }
        } else if ($params[2] == 'Interno') {
          $Status = TRUE;
          $Data = ["Interno" => encryption("0")];
        }
      }

      $response = [
        'Status' => $Status,
        'Data' => $Data
      ];
    }

    // Obteniendo Datos para Editar Registro de unidadesList
    if ($params[1] == 'unidadesList_edit') {
      $SQL1 = "SELECT * FROM unidadesList WHERE unList_id = " . $params[2];
      $QRY1 = consultaData($SQL1);

      $SQL2 = "SELECT * FROM unidadesList WHERE unList_id != " . $params[2];
      $QRY2 = consultaData($SQL2);

      if ($QRY1['numRows'] == 0) {
        $Status = FALSE;
        $Data = "No existe el registro de unidad.";
      } else if ($QRY2['numRows'] == 0) {
        $Status = FALSE;
        $Data = "No existen registros de unidades.";
      } else {
        $Status = TRUE;

        $unidadesList = [];
        foreach ($QRY2['dataFetch'] as $row) {
          $array = [
            "id" => encryption($row['unList_id']),
            "unidad" => $row['unList_unidad'] . " | " . $row['unList_uni']
          ];
          array_push($unidadesList, $array);
        }

        $QRY1 = $QRY1['dataFetch'][0];
        $Data = [
          "Unidad" => [
            "id" => encryption($QRY1['unList_id']),
            "unidad" => $QRY1['unList_unidad'] . " | " . $QRY1['unList_uni']
          ],
          "unidadesList" => $unidadesList
        ];
      }

      $response = [
        'Status' => $Status,
        'Data' => $Data
      ];
    }

    // Obteniendo Datos para Editar Registro de unidadesList
    if ($params[1] == 'AlmMdelPDF') {
      $QRY = consultaData("SELECT * FROM AlmacenM WHERE AlmM_id = " . decryption($params[2]));
      if ($QRY['numRows'] == 0) {
        $Status = FALSE;
        $Data = "No existe el registro principal.";
      } else if ($QRY['numRows'] > 1) {
        $Status = FALSE;
        $Data = "Error ID duplicado en la base de datos.";
      } else {
        $DATA = $QRY['dataFetch'][0];
        if (unlink(SERVERDIR . "DocsCR/ALMACEN/Evidencias/" . $DATA['AlmM_folio'] . ".pdf")) {
          $Status = TRUE;
          $Data = "Evidencia eliminada correctamente.";
        } else {
          $Status = FALSE;
          $Data = "No se pudo borrar la Evidencia.";
        }
      }
      $response = [
        'Status' => $Status,
        'Data' => $Data
      ];
    }

    // Obteniendo Datos para Agregar Registro de unidadesList
    if ($params[1] == 'unidadesList_add') {
      $SQL = "SELECT * FROM unidadesList";
      $QRY = consultaData($SQL);

      if ($QRY['numRows'] == 0) {
        $Status = FALSE;
        $Data = "No existe el registro de unidad.";
      } else {
        $Status = TRUE;

        $Data = [];
        foreach ($QRY['dataFetch'] as $row) {
          $array = [
            "id" => encryption($row['unList_id']),
            "unidad" => $row['unList_unidad'] . " | " . $row['unList_uni']
          ];
          array_push($Data, $array);
        }
      }

      $response = [
        'Status' => $Status,
        'Data' => $Data
      ];
    }
  } else if ($params[0] == "delRegWithID") {
    $whiteList = array(
      "equipo_contabilidad",
      "equipos_contactos",
      "equipos_ether",
      "equipos_wifi",
    );

    session_start();
    // Definimos quiénes son los administradores permitidos
    $adminAllowed = [1, 2];
    // Condición: Si la tabla NO está en la whitelist Y el usuario NO es un admin permitido
    if (!in_array($params[1], $whiteList) && !in_array($_SESSION['id'], $adminAllowed)) {
      $response = [
        'Status' => FALSE,
        'Data' => "No tiene permisos para realizar esta acción"
      ];
      echo json_encode($response);
      exit();
    }

    $SQL = "DELETE FROM $params[1] WHERE $params[2] = '" . decryption($params[3]) . "'";
    $Status = sentenciaData($SQL);
    if ($Status) {
      $Data = "Listo";
    } else {
      $Data = "No se pudo borrar";
    }
    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == "QRYresByIDenc") {

    $table = $params[1];
    $value = decryption($params[2]);

    $PK = getPKfromTable($table);

    if ($PK['numRows'] == 0) {
      $response = [
        'Status' => FALSE,
        'Data' => []
      ];
    } else {
      $PK = $PK['dataFetch'][0]['COLUMN_NAME'];
      $SQL = "SELECT * FROM $table WHERE $PK = '$value'";
      $QRY = consultaData($SQL);
      if ($QRY['numRows'] > 0) {
        $Status = TRUE;
        $Data = $QRY['dataFetch'];
      } else {
        $Status = FALSE;
        $Data = [];
      }
    }
    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == "Rentas_Checks") {
    $tabla = $params[0];
    $renta_id = $params[1];
    $check_tipo = "check_" . $params[2];
    $valor = $params[3];
    $check_anio = $params[4];
    $check_mes = $params[5];

    $SQL = "SELECT * FROM $tabla WHERE renta_id = $renta_id AND check_anio = $check_anio AND check_mes = $check_mes";
    $res = consultaData($SQL);

    if ($res['numRows'] == 0) {
      $SQL = "INSERT INTO $tabla (renta_id, $check_tipo, check_anio, check_mes) VALUES ($renta_id, $valor, $check_anio, $check_mes)";
    } else {
      $SQL = "UPDATE $tabla SET
              renta_id = $renta_id,
              $check_tipo = $valor,
              check_anio = $check_anio,
              check_mes = $check_mes
              WHERE check_id = " . $res['dataFetch'][0]['check_id'];
    }

    $Status = sentenciaData($SQL);
    $Data = $Status ? "success" : "error";

    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == "lecturaRentasZona") {
    $query = "SELECT 
    R.renta_id, 
    Cl.cliente_rs, 
    Cl.cliente_rfc, 
    R.renta_estado, 
    R.renta_depto, 
    R.renta_coor, 
    R.renta_finicio, 
    R.renta_folio, 
    Co.contrato_folio, 
    Z.zona_id,
    L.lectura_fecha -- Obtenida directamente del JOIN
    FROM Rentas R
    INNER JOIN Contratos Co ON R.renta_contrato_id = Co.contrato_id
    INNER JOIN Clientes Cl ON Co.contrato_cliente_id = Cl.cliente_id
    INNER JOIN Zonas Z ON R.renta_zona_id = Z.zona_id
    -- Cambiamos la subconsulta por un LEFT JOIN con condiciones específicas
    LEFT JOIN Lecturas L ON L.lectura_renta_id = R.renta_id 
    AND MONTH(L.lectura_fecha) = $params[2] 
    AND YEAR(L.lectura_fecha) = $params[3]
    WHERE R.renta_estado = 'Activo'
    AND R.renta_zona_id = $params[1]
    AND R.renta_finicio <= LAST_DAY(STR_TO_DATE('$params[3]-$params[2]-01', '%Y-%m-%d'))
    AND DAY(R.renta_finicio) > 1
    -- Aseguramos que solo traiga rentas que tengan al menos una lectura registrada históricamente
    AND EXISTS (SELECT 1 FROM Lecturas L2 WHERE L2.lectura_renta_id = R.renta_id)
    ORDER BY contrato_folio ASC, renta_folio ASC";
    $datos = consultaData($query);
    $response = [
      'Status' => false,
      'Data' => $datos['dataFetch']
    ];
  } else if ($params[0] == "Equipos") {
    if ($params[1] == "equipo_serie") {
      $sqlSeries = "SELECT * FROM Equipos e
                    INNER JOIN Modelos m ON e.equipo_modelo_id = m.modelo_id
                    WHERE equipo_serie = '" . strtoupper($params[2]) . "'";
      $qrySeries = consultaData($sqlSeries);
      if ($qrySeries['numRows'] == 0) {
        $Status = FALSE;
        $qryModelos = consultaData("SELECT * FROM Modelos ORDER BY modelo_modelo DESC, modelo_tipo ASC");
        $qryProvs   = consultaData("SELECT * FROM ProveedoresE WHERE provE_estado = 'Activo'");

        foreach ($qryModelos['dataFetch'] as &$m) {
          $m['modelo_id'] = encryption($m['modelo_id']);
        }
        foreach ($qryProvs['dataFetch'] as &$p) {
          $p['provE_id'] = encryption($p['provE_id']);
        }

        $Data = [
          "Modelos"     => $qryModelos['dataFetch'],
          "Proveedores" => $qryProvs['dataFetch'],
        ];
      } else {
        $Status = TRUE;
        $Data = [
          "Equipo" => $qrySeries['dataFetch'][0]
        ];
      }
    } else if ($params[1] == "consultar_contadores") {
      $equipo_id = limpiarCadena(decryption($params[2]));
      $inicio = limpiarCadena($params[3]);
      $fin = limpiarCadena($params[4]);
      $Status = FALSE;

      if ($equipo_id == "" || $inicio == "" || $fin == "") {
        $Data = 'Faltan parámetros';
      } else {
        $SQL = "SELECT date_of_receipt, scan_total, bw_total, color_total 
            FROM historial_reportes 
            WHERE equipo_id = '$equipo_id' 
            AND date_of_receipt BETWEEN '$inicio 00:00:00' AND '$fin 23:59:59'
            ORDER BY date_of_receipt ASC";
        $QRY = consultaData($SQL);
        if ($QRY['numRows'] == 0) {
          $Data = 'Sin Datos';
        } else {
          $Status = TRUE;
          $Data = $QRY['dataFetch'];
        }
      }
    } else if ($params[1] == "consultar_contador") {
      $equipo_id = limpiarCadena(decryption($params[2]));
      $fin = limpiarCadena($params[3]); // Formato esperado: YYYY-MM-DD
      $Status = FALSE;

      if ($equipo_id == "" || $fin == "") {
        $Data = 'Faltan parámetros';
      } else {
        // 1. Obtener la última lectura oficial
        $SQL_Lect = "SELECT * FROM Lecturas WHERE lectura_equipo_id = '$equipo_id' ORDER BY lectura_fecha DESC LIMIT 1";
        $QRY_Lect = consultaData($SQL_Lect);

        if ($QRY_Lect['numRows'] == 0) {
          $Data = "No hay lecturas previas para este equipo";
        } else {
          // 2. Obtener el reporte específico de la fecha $fin
          $DL = $QRY_Lect['dataFetch'][0];    // Datos Lectura
          // Usamos LIKE para que coincida con la fecha sin importar la hora exacta
          $SQL_lastReg = "SELECT * FROM historial_reportes 
                            WHERE equipo_id = '$equipo_id' 
                            AND date_of_receipt BETWEEN '" . $DL['lectura_fecha'] . " 00:00:00' AND '$fin 23:59:59'
                            ORDER BY date_of_receipt DESC LIMIT 1";
          $QRY_lastReg = consultaData($SQL_lastReg);

          if ($QRY_lastReg['numRows'] == 0) {
            $Data = "No hay reportes registrados entre la ultima lectura del " . $DL['lectura_fecha'] . " hasta fecha: $fin seleccionada";
          } else {
            $DLR = $QRY_lastReg['dataFetch'][0]; // Datos Reporte

            // 3. Cálculos de diferencia (Reporte actual - Lectura anterior)
            $resEsc = $DLR['scan_total'] - $DL['lectura_esc'];
            $resBN  = $DLR['bw_total']   - $DL['lectura_bn'];
            $resCol = $DLR['color_total'] - $DL['lectura_col'];

            $Status = TRUE;
            $Data = [
              "lastLect" => [
                "fecha" => $DL['lectura_fecha'],
                "esc"   => $DL['lectura_esc'],
                "bn"    => $DL['lectura_bn'],
                "col"   => $DL['lectura_col']
              ],
              "lastReg" => [
                "fecha" => $DLR['date_of_receipt'],
                "esc"   => $DLR['scan_total'],
                "bn"    => $DLR['bw_total'],
                "col"   => $DLR['color_total']
              ],
              "Calculo" => [
                "diffEsc" => $resEsc,
                "diffBN"  => $resBN,
                "diffCol" => $resCol
              ]
            ];
          }
        }
      }
    }
    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == "Rentas") {
    if ($params[1] == "consultar_reportes_lecturas") {
      $fechaInicio = $params[2];
      $fechaInicio = $params[2];
      $fechaInicio = $params[2];
      $sqlSeries = "SELECT * FROM historial_reportes WHERE ";
      $qrySeries = consultaData($sqlSeries);
      $Status = TRUE;
    }
    $response = [
      'Status' => $Status,
      'Data' => $Data
    ];
  } else if ($params[0] == "rentas_facturas") {
    // if ($params[1] == "subir_zip") {
    //   // 1. Recibir y decodificar parámetros
    //   $archivoBase64 = $params[2];
    //   $rentaID = decryption($params[3]);
    //   $mes = $params[4];
    //   $anio = $params[5];
    //   $identificador = $params[6]; // Nuevo parámetro recibido

    //   // 2. Validación de registros previos (Antes de subir el archivo)
    //   $sqlCheck = "SELECT * FROM rentas_facturas WHERE identificador = '$identificador' OR (renta_id = '$rentaID' AND mes = '$mes' AND anio = '$anio')";
    //   $check = consultaData($sqlCheck);

    //   if ($check['numRows'] > 0) {
    //     $Status = false;
    //     $Data = "Error: Ya existe un registro con este identificador, o ya existe un registro para esta renta en este mes y año.";
    //   } else {
    //     // 3. Generar la ruta y el nombre del archivo (Folio) automáticamente
    //     $directorioBase = SERVERDIR . "DocsCR/rentas_facturas/{$anio}/{$mes}/";

    //     if (!file_exists($directorioBase)) {
    //       mkdir($directorioBase, 0755, true);
    //     }

    //     $totalArchivos = count(glob($directorioBase . "FOL-*.zip"));
    //     $siguienteNumero = $totalArchivos + 1;
    //     $folioGenerado = 'FOL-' . str_pad($siguienteNumero, 5, '0', STR_PAD_LEFT);

    //     $rutaFinal = $directorioBase . $folioGenerado . ".zip";
    //     $archivoBinario = base64_decode($archivoBase64);

    //     // 4. Intentar guardar el archivo
    //     if (file_put_contents($rutaFinal, $archivoBinario) !== false) {

    //       // 5. Insertar en la tabla 'rentas_facturas'
    //       $sqlInsert = "INSERT INTO rentas_facturas (renta_id, mes, anio, folio, identificador) 
    //                     VALUES ('$rentaID', '$mes', '$anio', '$folioGenerado', '$identificador')";

    //       // *Nota: Reemplaza 'sentenciaData' por la función estándar de tu framework para INSERT
    //       if (sentenciaData($sqlInsert)) {
    //         $Status = true;
    //         $Data = [
    //           "archivo" => $folioGenerado . ".zip",
    //           "rentaID" => $rentaID,
    //           "mes" => $mes,
    //           "anio" => $anio,
    //           "folioGenerado" => $folioGenerado
    //         ];
    //       } else {
    //         // Si falla la base de datos, eliminamos el archivo subido para no dejar archivos huérfanos
    //         unlink($rutaFinal);
    //         $Status = false;
    //         $Data = "Error al guardar el registro en la base de datos.";
    //       }
    //     } else {
    //       $Status = false;
    //       $Data = "Error al guardar el archivo en la ruta especificada.";
    //     }
    //   }

    //   $response = [
    //     'Status' => $Status,
    //     'Data' => $Data
    //   ];
    // }
    if ($params[1] == "subir_zip") {
      // 1. Recibir y decodificar parámetros
      $archivoBase64 = $params[2];
      $rentaID = decryption($params[3]);
      $mes = $params[4];
      $anio = $params[5];
      $identificador = $params[6];

      // Validación de registros previos
      $sqlCheck = "SELECT * FROM rentas_facturas WHERE identificador = '$identificador' OR (renta_id = '$rentaID' AND mes = '$mes' AND anio = '$anio')";
      $check = consultaData($sqlCheck);

      if ($check['numRows'] > 0) {
        $Status = false;
        $Data = "Error: Ya existe un registro con este identificador, o ya existe un registro para esta renta en este mes y año.";
      } else {
        $directorioBase = SERVERDIR . "DocsCR/rentas_facturas/{$mes}/{$anio}/";
        if (!file_exists($directorioBase)) {
          mkdir($directorioBase, 0755, true);
        }

        $totalArchivos = count(glob($directorioBase . "FOL-*.zip"));
        $siguienteNumero = $totalArchivos + 1;
        $folioGenerado = 'FOL-' . str_pad($siguienteNumero, 5, '0', STR_PAD_LEFT);

        $rutaFinal = $directorioBase . $folioGenerado . ".zip";
        $archivoBinario = base64_decode($archivoBase64);

        if (file_put_contents($rutaFinal, $archivoBinario) !== false) {
          // Reemplaza 'sentenciaData' por el nombre de tu función en tu base de datos
          $sqlInsert = "INSERT INTO rentas_facturas (renta_id, mes, anio, folio, identificador) 
                      VALUES ('$rentaID', '$mes', '$anio', '$folioGenerado', '$identificador')";

          if (sentenciaData($sqlInsert)) {
            $Status = true;
            $Data = ["folioGenerado" => $folioGenerado];
          } else {
            unlink($rutaFinal);
            $Status = false;
            $Data = "Error al guardar el registro en la base de datos.";
          }
        } else {
          $Status = false;
          $Data = "Error al guardar el archivo en el servidor.";
        }
      }

      $response = ['Status' => $Status, 'Data' => $Data];
    } else if ($params[1] == "actualizar_identificador") {
      $rentaID = decryption($params[2]);
      $mes = $params[3];
      $anio = $params[4];
      $nuevoIdentificador = $params[5];
      $folio = $params[6];

      // Validación de duplicados al actualizar
      $sqlCheck = "SELECT * FROM rentas_facturas WHERE identificador = '$nuevoIdentificador' AND folio != '$folio'";
      $check = consultaData($sqlCheck);

      if ($check['numRows'] > 0) {
        $Status = false;
        $Data = "Error: Este identificador ya está asignado a otro registro.";
      } else {
        $sqlUpdate = "UPDATE rentas_facturas SET identificador = '$nuevoIdentificador' WHERE folio = '$folio'";

        if (sentenciaData($sqlUpdate)) {
          $Status = true;
          $Data = "Identificador actualizado correctamente.";
        } else {
          $Status = false;
          $Data = "Error al actualizar la base de datos.";
        }
      }

      $response = ['Status' => $Status, 'Data' => $Data];
    } else if ($params[1] == "eliminar_zip") {
      $rentaID = decryption($params[2]);
      $mes = $params[3];
      $anio = $params[4];
      $folio = $params[5];

      // Ruta del archivo a eliminar
      $rutaArchivo = SERVERDIR . "DocsCR/rentas_facturas/{$mes}/{$anio}/{$folio}.zip";

      // Eliminar archivo físico
      if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
      }

      $sqlDelete = "DELETE FROM rentas_facturas WHERE folio = '$folio'";

      if (sentenciaData($sqlDelete)) {
        $Status = true;
        $Data = "Registro eliminado.";
      } else {
        $Status = false;
        $Data = "Error al eliminar el registro de la base de datos.";
      }

      $response = ['Status' => $Status, 'Data' => $Data];
    }
  }

  // Lanzando array de datos en JSON a JacaScript //
  echo json_encode($response);
} else {
  session_start();
  session_unset();
  session_destroy();
  header('Location: ' . SERVERURL . 'Login/');
  exit();
}
