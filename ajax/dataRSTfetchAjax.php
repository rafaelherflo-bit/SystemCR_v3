<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

// Verificacion de existencia de algun post reconocido //
if (isset($_POST['arguments'])) {
  $params = $_POST['arguments'];

  if ($params[0] == 0) {
    // Tipo 0 es de Consulta de Datos

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
        Cambios.cambio_motivo,
        Cambios.cambio_comm,
        Cambios.cambio_file,
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
    if ($params[1] == 'tonerStockCheckEnc') {
      $tonerET = consultaData("SELECT SUM(tonerR_cant) AS tonerET FROM TonersRegistrosE WHERE tonerR_toner_id = " . decryption($params[2]))['dataFetch'][0]['tonerET'];
      $tonerST = consultaData("SELECT SUM(tonerRO_cantidad) AS tonerST FROM TonersRegistrosS WHERE tonerRO_toner_id = " . decryption($params[2]))['dataFetch'][0]['tonerST'];
      $tonersStock = $tonerET - $tonerST;
      $response = [
        'Status' => TRUE,
        'Data' => $tonersStock
      ];
    }
  } else {
    // Si no hay tipo de dato reconocido, salta directo a error.
    $response = [
      'Status' => false,
      'Data' => "error"
    ];
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
