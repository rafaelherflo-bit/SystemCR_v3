<?php
$peticionAjax = true;
require_once '../config/SERVER.php';

// ------------------------------------ Incluir Funciones ------------------------------------ //
require_once '../controllers/almacenController.php';
// Estos dos al final se sustituiran por el de almacen //
require_once '../controllers/tonersController.php';
require_once '../controllers/refaccionesController.php';
// --------------------------------------------------- //
require_once '../controllers/equiposController.php';
require_once '../controllers/lecturasController.php';
require_once '../controllers/facturasController.php';
require_once '../controllers/cobranzasController.php';
require_once '../controllers/cotizadorController.php';
require_once '../controllers/reportesRController.php';
require_once '../controllers/reportesFController.php';
require_once '../controllers/clientesController.php';
require_once '../controllers/contratosController.php';
require_once '../controllers/rentasController.php';
require_once '../controllers/retirosController.php';
require_once '../controllers/cambiosController.php';
require_once '../controllers/proveedoresController.php';

// ======================================= CAMBIOS =========================================== //
// ------------------------------------ Agregar Cambio --------------------------------------- //
if (isset($_POST['cambioAdd']) && $_POST['cambioAdd'] == 1) {
  echo cambioAdd();
}
// ------------------------------------ Editar Cambio --------------------------------------- //
if (isset($_POST['cambioEdit'])) {
  echo cambioEdit();
}

// ======================================= RETIROS =========================================== //
// ------------------------------------ Agregar Retiro --------------------------------------- //
if (isset($_POST['retiro_fecha_add']) && isset($_POST['retiro_renta_id_add'])) {
  echo retiroAdd();
}

// ========================================= RENTAS ============================================ //
// -------------------------------------- Agregar Renta ---------------------------------------- //
if (isset($_POST['nuevaRenta']) && $_POST['nuevaRenta'] == 1) {
  echo rentaAdd();
}

// -------------------------------------- Editar Renta ----------------------------------------- //
if (isset($_POST['renta_id_edit'])) {
  echo rentaEdit();
}

// ========================================= CONTRATOS ========================================= //
// -------------------------------------- Agregar Contrato ------------------------------------- //
if (isset($_POST['nuevoContrato']) && $_POST['nuevoContrato'] == 1) {
  echo contratoAdd();
}
// --------------------------------- Subir Archivo de Contrato --------------------------------- //
if (isset($_POST['contrato_id_upload']) && isset($_FILES['contrato_file_upload'])) {
  echo contratoFileUpload();
}

// ========================================= CLIENTES ========================================= //
// -------------------------------------- Agregar Cliente ------------------------------------- //
if (isset($_POST['agregarCliente']) || isset($_POST['actualizarCliente'])) {
  echo clienteControlador();
}



// ======================================= PROVEEDORES ======================================= //
//                                           TONERS                                           //
// -------------------------- Agregar nuevo Proveedor de Toners -------------------------- //
if (isset($_POST['provT_nombre_add'])) {
  echo agregarProvT();
}
// -------------------------- Actualizar info de Proveedor de Toners -------------------------- //
if (isset($_POST['provT_id_edit'])) {
  echo actualizarProvT();
}
// ======================================= PROVEEDORES ======================================= //
//                                         REFACCIONES                                         //
// -------------------------- Agregar nuevo Proveedor de Refacciones -------------------------- //
if (isset($_POST['provR_nombre_add'])) {
  echo agregarProvR();
}
// ----------------------- Actualizar info de Proveedor de Refacciones ----------------------- //
if (isset($_POST['provR_id_edit'])) {
  echo actualizarProvR();
}
// ======================================= PROVEEDORES ======================================= //
//                                          EQUIPOS                                          //
// -------------------------- Agregar nuevo Proveedor de Equipos -------------------------- //
if (isset($_POST['provE_nombre_add'])) {
  echo agregarProvE();
}
// ------------------------- Actualizar info de Proveedor de Equipos ------------------------- //
if (isset($_POST['provE_id_edit'])) {
  echo actualizarProvE();
}




// ========================================= ALMACEN ========================================= //
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ------------------------------------ REGISTRO PRINCIPAL ----------------------------------- //
if (isset($_POST['iniciar_AlmM']) && decryption($_POST['iniciar_AlmM']) == "iniciar_AlmM") {
  echo iniciar_AlmM();
}
if (isset($_POST['editar_AlmM']) && $_POST['editar_AlmM'] != "") {
  echo editar_AlmM();
}
if (isset($_POST['agregar_AlmDM']) && $_POST['agregar_AlmDM'] != "") {
  echo agregar_AlmDM();
}
if (isset($_POST['active_AlmM']) && $_POST['active_AlmM'] != "") {
  echo active_AlmM();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ------------------------------------- NUEVOS REGISTROS ------------------------------------ //
if (isset($_POST['nuevoRegistro_AlmP'])) {
  if (decryption($_POST['nuevoRegistro_AlmP']) == "Toners") {
    echo nuevoRegistro_AlmP_Toners();
  } else if (decryption($_POST['nuevoRegistro_AlmP']) == "Chips") {
    echo nuevoRegistro_AlmP_Chips();
  } else if (decryption($_POST['nuevoRegistro_AlmP']) == "Refacciones") {
    echo nuevoRegistro_AlmP_Refacciones();
  } else if (decryption($_POST['nuevoRegistro_AlmP']) == "Servicios") {
    echo nuevoRegistro_AlmP_Servicios();
  } else if (decryption($_POST['nuevoRegistro_AlmP']) == "Otros") {
    echo nuevoRegistro_AlmP_Otros();
  }
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //
// ------------------------------------- EDITAR REGISTROS ------------------------------------ //
//                               ----------- TONERS -------------                              //
if (isset($_POST['editarRegistro_AlmP_Toners']) && $_POST['editarRegistro_AlmP_Toners'] != "") {
  echo editarRegistro_AlmP_Toners();
}
//                                ----------- CHIPS -------------                              //
if (isset($_POST['editarRegistro_AlmP_Chips']) && $_POST['editarRegistro_AlmP_Chips'] != "") {
  echo editarRegistro_AlmP_Chips();
}
//                             ----------- REFACCIONES -------------                           //
if (isset($_POST['editarRegistro_AlmP_Refacciones']) && $_POST['editarRegistro_AlmP_Refacciones'] != "") {
  echo editarRegistro_AlmP_Refacciones();
}
//                              ----------- SERVICIOS -------------                            //
if (isset($_POST['editarRegistro_AlmP_Servicios']) && $_POST['editarRegistro_AlmP_Servicios'] != "") {
  echo editarRegistro_AlmP_Servicios();
}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ //


//                             ----------- PROVEEDORES -------------                           //
// ------------------------------- Agregar Proveedor de Almacen ------------------------------ //
if (isset($_POST['nuevoRegistro_AlmProv']) && decryption($_POST['nuevoRegistro_AlmProv']) == "nuevoRegistro_AlmProv") {
  echo nuevoRegistro_AlmProv();
}
// ------------------------------- Agregar Proveedor de Almacen ------------------------------ //
if (isset($_POST['editarRegistro_AlmProv']) && decryption($_POST['editarRegistro_AlmProv']) != "") {
  echo editarRegistro_AlmProv();
}


// ------------------------------------- Entrada de Toner ------------------------------------- //
if (isset($_POST['toner_codigo_add'])) {
  echo entradaNewToner();
}
// ------------------------------------- Salida de Toner ------------------------------------- //
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~  Agregar
// if (isset($_POST['tonerRO_fecha']) && isset($_POST['tonerRO_toner_id'])) {
//   echo salidaToner();
// }
// // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~  Actualizar
// if (isset($_POST['actualizarRST'])) {
//   echo actualizarRST();
// }
// ------------------------------------- Actualizar info de Toner ----------------------------- //
if (isset($_POST['toner_id_edit']) && isset($_POST['toner_comp_edit'])) {
  echo actualizarToner();
}

// ========================================== REFACCIONES ===================================== //
// ------------------------------------- Actualizar info de Refaccion ----------------------------- //
if (isset($_POST['ref_id_edit']) && isset($_POST['ref_comp_edit'])) {
  echo actualizarRefaccion();
}
// ----------------------------- Agregar una Entrada de Refaccion ----------------------------- //
if (isset($_POST['ref_codigo_add'])) {
  echo entradaRefaccion();
}
// ----------------------------- Agregar una Salida de Refaccion ----------------------------- //
if (isset($_POST['ref_id_out'])) {
  echo salidaRefaccion();
}

// ----------------------------- Ingreso de nueva categoria de Refaccion ----------------------------- //
if (isset($_POST['catR_codigo_add'])) {
  echo addCatR();
}

// ----------------------------- Actualizacion de categoria de Refaccion ----------------------------- //
if (isset($_POST['catR_id_edit'])) {
  echo editCatR();
}

// =========================================== EQUIPOS ====================================== //
// -------------------------------------- Agregar un Equipo --------------------------------- //
if (isset($_POST['agregarEquipo'])) {
  echo agregarEquipo();
}
// -------------------------------------- Actualizar un Equipo --------------------------------- //
if (isset($_POST['actualizarEquipo'])) {
  echo actualizarEquipo();
}
// -------------------------------------- Actualizar un Modelo --------------------------------- //
if (isset($_POST['update_modelo_id'])) {
  echo updateModelo();
}
// -------------------------------------- Configuracion de WIFI --------------------------------- //
if (isset($_POST['configuracionWIFI'])) {
  echo configuracionWIFI();
}
// -------------------------------------- Configuracion de Ethernet --------------------------------- //
if (isset($_POST['configuracionEthernet'])) {
  echo configuracionEthernet();
}
// -------------------------------------- Configuracion de Ethernet --------------------------------- //
if (isset($_POST['equipoContactoAdd'])) {
  echo equipoContactoAdd();
}
// -------------------------------------- Configuracion de Ethernet --------------------------------- //
if (isset($_POST['equipoContactoEdit'])) {
  echo equipoContactoEdit();
}

// =========================================== LECTURAS ===================================== //
// ------------------------------------- Agregar una Lectura -------------------------------- //
if ((isset($_POST['agregarLectura']) && $_POST['agregarLectura'] == 0)) {
  echo agregarLectura();
}
// ------------------------------------- Actualizar Lectura -------------------------------- //
if (isset($_POST['actualizarLectura']) && $_POST['actualizarLectura'] != 0) {
  echo actualizarLectura();
}

// =========================================== REPORTES ===================================== //
// ------------------------------------- Agregar un Reporte --------------------------------- //
if ((isset($_POST['reporte_fecha']) && isset($_POST['reporte_renta_id']) && isset($_POST['reporte_estado'])) && $_POST['reporte_estado'] == 1) {
  echo agregarReporte();
}
// ------------------------------------- Iniciar un Reporte --------------------------------- //
if ((isset($_POST['reporte_fecha']) && isset($_POST['reporte_renta_id']) && isset($_POST['reporte_estado'])) && $_POST['reporte_estado'] == 0) {
  echo iniciarReporte();
}
// ------------------------------------- Actualizar Reporte Inicial --------------------------------- //
if (isset($_POST['reporte_fecha']) && isset($_POST['reporte_renta_id']) && isset($_POST['reporte_activo_update'])) {
  echo actualizarIniciarReporte();
}
// ------------------------------------- Actualizar Reporte Completo --------------------------------- //
if (isset($_POST['reporte_fecha']) && isset($_POST['reporte_renta_id']) && isset($_POST['reporte_completo_update'])) {
  echo actualizarCompletoReporte();
}
// ------------------------------------- Finalizar Reporte Iniciado --------------------------------- //
if (isset($_POST['reporte_fecha']) && isset($_POST['reporte_renta_id']) && isset($_POST['reporte_activo_completar'])) {
  echo completarInicioReporte();
}

// ======================================== REPORTES FORANEOS ================================== //
// ------------------------------------- Agregar un Reporte Foraneo --------------------------------- //
if (isset($_POST['reporteF_completo_nuevo']) && isset($_POST['reporteF_fecha']) && isset($_POST['reporteF_cliente_id']) && isset($_POST['reporteF_estado'])) {
  echo agregarReporteForaneo();
}
// ------------------------------------- Actualizar un Reporte Foraneo --------------------------------- //
if (isset($_POST['reporteF_completo_update']) && isset($_POST['reporteF_fecha']) && isset($_POST['reporteF_cliente_id']) && isset($_POST['reporteF_estado'])) {
  echo actualizarReporteForaneo();
}

// =========================================== FACTURAS ===================================== //
// ------------------------------------- Agregar un pago de Factura --------------------------------- //
if (isset($_POST['pCh_id']) && isset($_POST['pCH_fechaPago']) && isset($_POST['pCH_cantPago']) && isset($_POST['pCH_comm'])) {
  echo agregarPagoFactura();
}
// ------------------------------------- Agregar un registro de Factura --------------------------------- //
if (isset($_POST['pCh_modo']) && $_POST['pCh_modo'] == 0) {
  echo agregarFactura();
}
// ------------------------------------- Editar un registro de Factura --------------------------------- //
if (isset($_POST['pCh_modo']) && $_POST['pCh_modo'] != 0) {
  echo editarFactura();
}

// =========================================== COBRANZAS ===================================== //
// ------------------------------------- Iniciar un Registro --------------------------------- //
if (isset($_POST['cobM_fecha_add']) && isset($_POST['cobM_cliente_id_add']) && isset($_POST['cobM_comm_add'])) {
  echo iniciarCobranza();
}
// ------------------------------------- Agregar Cobro --------------------------------- //
if (isset($_POST['cobC_cobM_id']) && isset($_POST['cobC_fecha']) && isset($_POST['cobC_conc']) && isset($_POST['cobC_monto'])) {
  echo agregarCobro();
}
// ------------------------------------- Agregar Cobro --------------------------------- //
if (isset($_POST['cobP_cobM_id']) && isset($_POST['cobP_fecha']) && isset($_POST['cobP_conc']) && isset($_POST['cobP_monto'])) {
  echo agregarPago();
}

// =========================================== COTIZADOR =================================== //
// --------------------------------------- Nuevo Registro de Cotizacion -------------------- //
if (isset($_POST['nuevaCotizacion'])) {
  echo nuevaCotizacion();
}
// --------------------------------------- Actualizar Registro de Cotizacion -------------------- //
if (isset($_POST['editarCotizacion'])) {
  echo editarCotizacion();
}
// --------------------------------------- Agregando productos a la cotizacion ------------- //
if (isset($_POST['agregarProdCotD'])) {
  echo agregarProdCotD();
}
// --------------------------------------- Nuevo Registro de Producto ---------------------- //
// if (isset($_POST['nuevoProducto'])) {
//   echo nuevoProducto();
// }
// // --------------------------------------- Nuevo Registro de Producto ---------------------- //
// if (isset($_POST['actualizarProducto'])) {
//   echo actualizarProducto();
// }

echo json_encode([
  'Alerta' => 'simple',
  'Titulo' => 'ERROR',
  'Texto' => '(TT _ TT) No se encontro ninguna funcion para este formulario. (TT _ TT)',
  'Tipo' => 'error'
]);
