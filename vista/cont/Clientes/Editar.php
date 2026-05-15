<?php
$QRY = consultaData("SELECT * FROM Clientes 
        INNER JOIN catRegimenFiscal ON Clientes.cliente_regFis_id = catRegimenFiscal.regFis_id 
        INNER JOIN catCFDI ON Clientes.cliente_cfdi_id = catCFDI.CFDI_id 
        WHERE cliente_id = " . decryption($GLOBALS['pagina2']));

if ($QRY['numRows'] > 0) {
  $row = $QRY['dataFetch'][0];
} else {
  redirect($GLOBALS['redirect']);
  exit();
}
?>
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0 text-uppercase text-primary">
        <i class="fas fa-users me-2"></i> Directorio de Clientes
      </h4>
      <p class="text-muted small mb-0">Gestión de cartera, datos fiscales y estados de cuenta.</p>
    </div>

    <ul class="nav nav-pills small">
      <li class="nav-item">
        <a class="nav-link bg-light text-dark shadow-sm border mx-1" href="/Clientes/Fiscal">
          <i class="fas fa-file-invoice me-2 text-danger"></i> CONSTANCIA
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-primary bg-light text-dark shadow-sm mx-1 fw-bold" href="/Clientes/Agregar">
          <i class="fas fa-plus me-1 text-primary"></i> AGREGAR CLIENTE
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-primary bg-light text-dark shadow-sm mx-1 fw-bold" href="/Clientes/Lista">
          <i class="fas fa-list me-1 text-primary"></i> LISTA DE CLIENTES
        </a>
      </li>
    </ul>
  </div>


  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="update">
    <input type="hidden" name="actualizarCliente" value="<?= encryption($row['cliente_id']) ?>">
    <input type="hidden" name="cliente_tipo" id="cliente_tipo" value="<?= $row['cliente_tipo'] ?>">

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-primary"><i class="far fa-id-card me-2"></i></span>Actualizar Cliente de Tipo: <span id="titleTipo"><?= $row['cliente_tipo'] ?></span></h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="row">
          <div class="col">
            <label class="small fw-bold">RFC <span class="text-danger">*</span></label>
            <input type="text" class="form-control fw-bold" name="cliente_rfc" id="input_rfc" value="<?= $row['cliente_rfc'] ?>" maxlength="13" placeholder="Ingresa RFC para detectar tipo" style="text-transform: uppercase;" required>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-dark"><i class="fas fa-stream me-2"></i></span>Datos Fiscales</h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="row">
          <div class="col-md-6">
            <label class="small fw-bold">RÉGIMEN FISCAL</label>
            <select class="form-select" name="cliente_regFis_id" data-placeholder="Selecciona un régimen..." required>
              <option></option>
              <?php
              $regFis_QRY = consultaData("SELECT * FROM catRegimenFiscal ORDER BY regFis_codigo ASC");
              foreach ($regFis_QRY['dataFetch'] as $regFis) {
              ?>
                <option value="<?= encryption($regFis['regFis_id']) ?>" <?= $row['cliente_regFis_id'] == $regFis['regFis_id'] ? "selected" : "" ?>><?= $regFis['regFis_codigo'] ?> | <?= $regFis['regFis_descripcion'] ?></option>
              <?php
              }
              ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="small fw-bold">USO DE CFDI</label>
            <select class="form-select" name="cliente_cfdi_id" data-placeholder="Selecciona un uso..." required>
              <option></option>
              <?php
              $CFDI_QRY = consultaData("SELECT * FROM catCFDI ORDER BY CFDI_codigo ASC");
              foreach ($CFDI_QRY['dataFetch'] as $CFDI) {
              ?>
                <option value="<?= encryption($CFDI['CFDI_id']) ?>" <?= $row['cliente_cfdi_id'] == $CFDI['CFDI_id'] ? "selected" : "" ?>><?= $CFDI['CFDI_codigo'] ?> | <?= $CFDI['CFDI_descripcion'] ?></option>
              <?php
              }
              ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="small fw-bold">RAZÓN SOCIAL</label>
            <input type="text" class="form-control fw-bold" name="cliente_rs" id="cliente_rs" value="<?= $row['cliente_rs'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3 seccion_fisica <?= $row['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
            <label class="small fw-bold">CURP</label>
            <input type="text" class="form-control" name="cliente_curp" id="cliente_curp" value="<?= $row['cliente_curp'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3 seccion_moral <?= $row['cliente_tipo'] === "Fisica" ? "d-none" : "" ?>">
            <label class="small fw-bold">REGIMEN CAPITAL</label>
            <input type="text" class="form-control" name="cliente_regCap" id="cliente_regCap" value="<?= $row['cliente_regCap'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3">
            <label class="small fw-bold">NOMBRE COMERCIAL</label>
            <input type="text" class="form-control" name="cliente_nombreComercial" id="cliente_nombreComercial" value="<?= $row['cliente_nombreComercial'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica <?= $row['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
            <label class="small fw-bold">NOMBRE(s)</label>
            <input type="text" class="form-control" name="cliente_nombre" id="cliente_nombre" value="<?= $row['cliente_nombre'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica <?= $row['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
            <label class="small fw-bold">1er APELLIDO</label>
            <input type="text" class="form-control" name="cliente_apellido1" id="cliente_apellido1" value="<?= $row['cliente_apellido1'] ?>" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica <?= $row['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
            <label class="small fw-bold">2do APELLIDO</label>
            <input type="text" class="form-control" name="cliente_apellido2" id="cliente_apellido2" value="<?= $row['cliente_apellido2'] ?>" style="text-transform: uppercase;">
          </div>
        </div>
      </div>
    </div>

    <?php // Apartado de direccion 
    ?>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-danger"><i class="fas fa-map-marker-alt me-2"></i></span>Domicilio Fiscal</h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="row g-3">
          <div class="col-md-2"><label class="small fw-bold">C.P. <span class="text-danger">*</span></label><input type="text" class="form-control" name="cliente_cp" value="<?= $row['cliente_cp'] ?>" required></div>
          <div class="col-md-3"><label class="small fw-bold">Tipo Vialidad</label><input type="text" class="form-control" name="cliente_tipoVialidad" value="<?= $row['cliente_tipoVialidad'] ?>"></div>
          <div class="col-md-7"><label class="small fw-bold">Calle / Vialidad</label><input type="text" class="form-control" name="cliente_noVialidad" value="<?= $row['cliente_noVialidad'] ?>"></div>
          <div class="col-md-2"><label class="small fw-bold">Núm. Ext.</label><input type="text" class="form-control" name="cliente_nuExterior" value="<?= $row['cliente_nuExterior'] ?>"></div>
          <div class="col-md-2"><label class="small fw-bold">Núm. Int.</label><input type="text" class="form-control" name="cliente_nuInterior" value="<?= $row['cliente_nuInterior'] ?>"></div>
          <div class="col-md-4"><label class="small fw-bold">Colonia</label><input type="text" class="form-control" name="cliente_noColonia" value="<?= $row['cliente_noColonia'] ?>"></div>
          <div class="col-md-4"><label class="small fw-bold">Municipio</label><input type="text" class="form-control" name="cliente_noMunicipio" value="<?= $row['cliente_noMunicipio'] ?>"></div>
          <div class="col-md-6"><label class="small fw-bold">Entre Calle</label><input type="text" class="form-control" name="cliente_calle1" value="<?= $row['cliente_calle1'] ?>"></div>
          <div class="col-md-6"><label class="small fw-bold">Y Calle</label><input type="text" class="form-control" name="cliente_calle2" value="<?= $row['cliente_calle2'] ?>"></div>
        </div>
      </div>
    </div>

    <?php // Apartado adicional guardado de constancia y datos utiles para el PDF de contrato 
    ?>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-success"><i class="fas fa-envelope me-2"></i></span> Contacto y Archivo</h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0 text-center">
        <div class="row g-3 mb-4 text-start">
          <div class="col-md-4"><label class="small fw-bold">Persona de Contacto</label><input type="text" class="form-control" name="cliente_contacto" value="<?= $row['cliente_contacto'] ?>" required></div>
          <div class="col-md-4"><label class="small fw-bold">Correo Electrónico</label><input type="email" class="form-control" name="cliente_correo" value="<?= $row['cliente_correo'] ?>" required></div>
          <div class="col-md-4"><label class="small fw-bold">Teléfono</label><input type="text" class="form-control" name="cliente_telefono" value="<?= $row['cliente_telefono'] ?>" required></div>
        </div>
        <div class="bg-light p-3 rounded mb-4">
          <label class="fw-bold text-danger d-block mb-2">ADJUNTAR CONSTANCIA PDF</label>
          <input type="file" name="cliente_pdf" class="form-control w-50 mx-auto" accept=".pdf">
        </div>
        <button type="submit" class="btn btn-warning btn-lg px-5 shadow"><i class="fas fa-sync-alt me-2"></i> ACTUALIZAR CLIENTE</button>
      </div>
    </div>

  </form>
</div>