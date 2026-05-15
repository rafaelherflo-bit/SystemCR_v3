<?php

// ============================ Controladores de Proveedores ============================ //
//                     Toners                     //
function agregarProvT()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provT_nombre = limpiarCadena(str_replace("ñ", "Ñ", strtoupper($_POST['provT_nombre_add'])));

    if ($provT_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provT_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresT WHERE provT_nombre = '$provT_nombre'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("INSERT INTO ProveedoresT (provT_nombre, provT_estado) VALUES ('$provT_nombre', 'Activo')")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Registro Completo. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
function actualizarProvT()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provT_id = limpiarCadena(decryption($_POST['provT_id_edit']));
    $provT_nombre = limpiarCadena(strtoupper($_POST['provT_nombre_edit']));
    $provT_estado = limpiarCadena($_POST['provT_estado_edit']);

    if (consultaData("SELECT * FROM ProveedoresT WHERE provT_id = '$provT_id'")['numRows'] <= 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor solicitado no exite. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($provT_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provT_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresT WHERE provT_nombre = '$provT_nombre' AND provT_id != '$provT_id'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $provT_nombre = str_replace("ñ", "Ñ", strtoupper($provT_nombre));
    }

    if ($provT_estado == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Ninguna opcion de estado seleccionada. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($provT_estado == "Activo" && $provT_estado == "Inactivo") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Opcion de estado NO valida. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("UPDATE ProveedoresT SET provT_nombre = '$provT_nombre', provT_estado = '$provT_estado' WHERE provT_id = '$provT_id'")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Actualizacion Completada. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
//                   Refacciones                   //
function agregarProvR()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provR_nombre = limpiarCadena(str_replace("ñ", "Ñ", strtoupper($_POST['provR_nombre_add'])));

    if ($provR_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provR_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresR WHERE provR_nombre = '$provR_nombre'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $provR_nombre = str_replace("ñ", "Ñ", strtoupper($provR_nombre));
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("INSERT INTO ProveedoresR (provR_nombre, provR_estado) VALUES ('$provR_nombre', 'Activo')")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Registro Completo. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
function actualizarProvR()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provR_id = limpiarCadena(decryption($_POST['provR_id_edit']));
    $provR_nombre = limpiarCadena(strtoupper($_POST['provR_nombre_edit']));
    $provR_estado = limpiarCadena($_POST['provR_estado_edit']);

    if (consultaData("SELECT * FROM ProveedoresR WHERE provR_id = '$provR_id'")['numRows'] <= 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor solicitado no exite. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($provR_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provR_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresR WHERE provR_nombre = '$provR_nombre' AND provR_id != '$provR_id'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $provR_nombre = str_replace("ñ", "Ñ", strtoupper($provR_nombre));
    }

    if ($provR_estado == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Ninguna opcion de estado seleccionada. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($provR_estado == "Activo" && $provR_estado == "Inactivo") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Opcion de estado NO valida. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("UPDATE ProveedoresR SET provR_nombre = '$provR_nombre', provR_estado = '$provR_estado' WHERE provR_id = '$provR_id'")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Actualizacion Completada. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
//                     Equipos                     //
function agregarProvE()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provE_nombre = limpiarCadena(str_replace("ñ", "Ñ", strtoupper($_POST['provE_nombre_add'])));

    if ($provE_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provE_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresE WHERE provE_nombre = '$provE_nombre'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $provE_nombre = str_replace("ñ", "Ñ", strtoupper($provE_nombre));
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("INSERT INTO ProveedoresE (provE_nombre, provE_estado) VALUES ('$provE_nombre', 'Activo')")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Registro Completo. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo realizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
function actualizarProvE()
{
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Comprobacion de Formulario ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
    $provE_id = limpiarCadena(decryption($_POST['provE_id_edit']));
    $provE_nombre = limpiarCadena(strtoupper($_POST['provE_nombre_edit']));
    $provE_estado = limpiarCadena($_POST['provE_estado_edit']);

    if (consultaData("SELECT * FROM ProveedoresE WHERE provE_id = '$provE_id'")['numRows'] <= 0) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor solicitado no exite. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    if ($provE_nombre == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Es necesario ingresar un nombre. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (verificarDatos("^([A-Za-z0-9\s.,&-ñÑ\/]+)*$", $provE_nombre)) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El nombre NO cuenta con el Formato Solicitado. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if (consultaData("SELECT * FROM ProveedoresE WHERE provE_nombre = '$provE_nombre' AND provE_id != '$provE_id'")['numRows'] >= 1) {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) El proveedor ya existe. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else {
        $provE_nombre = str_replace("ñ", "Ñ", strtoupper($provE_nombre));
    }

    if ($provE_estado == "") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Ninguna opcion de estado seleccionada. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    } else if ($provE_estado == "Activo" && $provE_estado == "Inactivo") {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(TT _ TT) Opcion de estado NO valida. (TT _ TT)',
            'Tipo' => 'error'
        ];
        echo json_encode($alerta);
        exit();
    }

    // --------------------------------------------- Agregamos el Formulario a la DB --------------------------------------------- //
    if (sentenciaData("UPDATE ProveedoresE SET provE_nombre = '$provE_nombre', provE_estado = '$provE_estado' WHERE provE_id = '$provE_id'")) {
        $alerta = [
            'Alerta' => 'recargar',
            'Titulo' => 'EXITO',
            'Texto' => '\(- _ -)/ Actualizacion Completada. \(- _ -)/',
            'Tipo' => 'success'
        ];
    } else {
        $alerta = [
            'Alerta' => 'simple',
            'Titulo' => 'Ocurrio un Error inesperado',
            'Texto' => '(o _ O) No se pudo actualizar el registro. (o _ O)',
            'Tipo' => 'error'
        ];
    }
    // --------------------------------- ^^^ FIN ^^^ Agregamos el Formulario a la DB ^^^ FIN ^^^ --------------------------------- //

    // Lanzamos La respuesta Final //
    echo json_encode($alerta);
    exit();
}
