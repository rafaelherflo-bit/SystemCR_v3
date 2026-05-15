<?php
$_POST = json_decode(file_get_contents('php://input'), true);
$peticionAjax = true;
require_once '../config/SERVER.php';

$id = $_POST['id'];
$action = $_POST['accion'];
$Data = "Hecho.";

// ========================= COBRANZAS ========================= //
// ------------------------ Eliminar PAGO por ID encriptado ------------------------ //
if ($action == 'DelcobPencID') {
  $check1 = consultaData("SELECT * FROM cobranzasP WHERE cobP_id = '" . decryption($id) . "'");
  if ($check1['numRows'] == 0) {
    $result = false;
    $Data = "No exite el ID de pago solicitado.";
  } else {
    $cobM_id = $check1['dataFetch'][0]['cobP_cobM_id'];
    $check2 = consultaData("SELECT * FROM cobranzasM WHERE cobM_id = '" . $cobM_id . "'");
    if ($check2['numRows'] == 0) {
      $result = false;
      $Data = "No exite el ID de registro principal solicitado.";
    } else {
      $result = sentenciaData("DELETE FROM cobranzasP WHERE cobP_id = '" . decryption($id) . "'");
      if ($result) {
        if ($result) {
          $result = sentenciaData("UPDATE cobranzasM SET cobM_status = 1 WHERE cobM_id = '$cobM_id'");
        } else {
          $Data = "No se pudo actualizar el estaus del registro.";
        }
      } else {
        $Data = "No se pudo eliminar el pago.";
      }
    }
  }
}
// ------------------------ Eliminar CARGO por ID encriptado ------------------------ //
if ($action == 'DelcobCencID') {
  $check1 = consultaData("SELECT * FROM cobranzasC WHERE cobC_id = '" . decryption($id) . "'");
  if ($check1['numRows'] == 0) {
    $result = false;
    $Data = "No exite el ID de cargo solicitado.";
  } else {
    $cobM_id = $check1['dataFetch'][0]['cobC_cobM_id'];
    $check2 = consultaData("SELECT * FROM cobranzasM WHERE cobM_id = '" . $cobM_id . "'");
    if ($check2['numRows'] == 0) {
      $result = false;
      $Data = "No exite el ID de registro principal solicitado.";
    } else {
      $check3 = consultaData("SELECT * FROM cobranzasP WHERE cobP_cobM_id = '" . $cobM_id . "'");
      if ($check3['numRows'] >= 1) {
        $result = false;
        $Data = "No se pueden eliminar los cargos, elimina primero los pagos efectuados.";
      } else {
        $result = sentenciaData("DELETE FROM cobranzasC WHERE cobC_id = '" . decryption($id) . "'");
        if ($result == false) {
          $Data = "No se pudo actualizar el estaus del registro.";
        }
      }
    }
  }
}
// --------------------------------------------------------------------------------- //

if ($action == 'delPCHid') {
  $sentenciaData = "DELETE FROM payCheck_pagos WHERE pCH_id = $id";
  $result = sentenciaData($sentenciaData);

  if ($result == false) {
    $Data = "No se pudo eliminar el pago.";
  }
}

if ($action == 'delPCHid') {
  $sentenciaData = "DELETE FROM payCheck_pagos WHERE pCH_id = $id";
  $result = sentenciaData($sentenciaData);

  if ($result == false) {
    $Data = "No se pudo eliminar el pago.";
  }
}

if ($action == 'delPChid') {
  $id = decryption($id);
  $pChQuery = consultaData("SELECT * FROM payCheck WHERE pCh_id = $id");
  if ($pChQuery['numRows'] == 0) {
    $result = false;
    $Data = "No existe el registro solicitado.";
  } else {
    if (consultaData("SELECT * FROM payCheck_pagos WHERE pCH_pCh_id = $id")['numRows'] >= 1) {
      $result = false;
      $Data = "El registro contiene pagos realizados, elimina primero los pagos realizados.";
    } else {
      $pChData = $pChQuery['dataFetch'][0];
      $pCh_fechaFact = $pChData['pCh_fechaFact'];
      list($pChAnio, $pChMes, $pChDia) = explode("-", $pCh_fechaFact);
      $pCh_archivo = $pChData['pCh_archivo'];

      if ($pCh_archivo == 0) {
        $sentenciaData = "DELETE FROM payCheck WHERE pCH_id = $id";
        $result = sentenciaData($sentenciaData);
        if ($result == false) {
          $Data = "No se pudo eliminar el registro de factura.";
        }
      } else if (unlink(SERVERDIR . 'DocsCR/Facturas/' . $pChAnio . '/' . $pChMes . '/' . $pCh_archivo)) {
        $sentenciaData = "DELETE FROM payCheck WHERE pCH_id = $id";
        $result = sentenciaData($sentenciaData);
        if ($result == false) {
          $Data = "No se pudo eliminar el registro de factura.";
        }
      } else {
        $result = false;
        $Data = "No se pudo eliminar el archivo ligado.";
      }
    }
  }
}

if ($action == 'delPDFpChid') {
  $id = decryption($id);
  $pChQuery = consultaData("SELECT * FROM payCheck WHERE pCh_id = $id");
  if ($pChQuery['numRows'] == 0) {
    $result = false;
    $Data = "No existe el registro solicitado.";
  } else {
    $pChData = $pChQuery['dataFetch'][0];
    $pCh_fechaFact = $pChData['pCh_fechaFact'];
    list($pChAnio, $pChMes, $pChDia) = explode("-", $pCh_fechaFact);
    $pCh_archivo = $pChData['pCh_archivo'];

    if (unlink(SERVERDIR . 'DocsCR/Facturas/' . $pChAnio . '/' . $pChMes . '/' . $pCh_archivo)) {
      $sentenciaData = "UPDATE payCheck SET pCh_archivo = 0 WHERE pCh_id = $id";
      $result = sentenciaData($sentenciaData);
      if ($result == false) {
        $Data = "No se pudo eliminar el archivo ligado.";
      }
    } else {
      $result = false;
      $Data = "No se pudo eliminar el archivo ligado.";
    }
  }
}

if ($action == 'delPDFcobMid') {
  $id = decryption($id);
  $cobMQuery = consultaData("SELECT * FROM cobranzasM WHERE cobM_id = $id");
  if ($cobMQuery['numRows'] == 0) {
    $result = false;
    $Data = "No existe el registro solicitado.";
  } else {
    $cobMData = $cobMQuery['dataFetch'][0];
    $cobM_fecha = $cobMData['cobM_fecha'];
    list($cobMAnio, $cobMMes, $cobMDia) = explode("-", $cobM_fecha);
    $cobM_archivo = $cobMData['cobM_archivo'];

    if (unlink(SERVERDIR . 'DocsCR/Facturas/' . $cobMAnio . '/' . $cobMMes . '/' . $cobM_archivo)) {
      $sentenciaData = "UPDATE cobranzasM SET cobM_archivo = 0 WHERE cobM_id = $id";
      $result = sentenciaData($sentenciaData);
      if ($result == false) {
        $Data = "No se pudo eliminar el archivo ligado.";
      }
    } else {
      $result = false;
      $Data = "No se pudo eliminar el archivo ligado.";
    }
  }
}

$result = [
  'Estado' => $result,
  'Data' => $Data
];

echo json_encode($result);
