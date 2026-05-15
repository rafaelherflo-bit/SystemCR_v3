<?php
$peticionAjax = true;
require_once '../config/SERVER.php';

// ------------------------------------ Incluir Funcion ------------------------------------ //
require_once '../controllers/cobranzasController.php';

// ------------------------------------ Agregar Cobranza --------------------------------------- //
if (isset($_POST['cobM_fecha_add']) && isset($_POST['cobM_cliente_id_add'])) {
    echo iniciarCobranza();
}

// ------------------------------------ Editar Cobranza --------------------------------------- //
if (isset($_POST['cobM_fecha_edit']) && isset($_POST['cobM_cliente_id_edit'])) {
    echo editarCobranza();
}
