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
        <a class="nav-link active shadow-sm border mx-1">
          <i class="fas fa-plus me-1"></i> AGREGAR CLIENTE
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link bg-primary bg-light text-dark shadow-sm mx-1 fw-bold" href="/Clientes/Lista">
          <i class="fas fa-list me-1 text-primary"></i> LISTA DE CLIENTES
        </a>
      </li>
    </ul>
  </div>

  <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save">

    <input type="hidden" name="agregarCliente">
    <input type="hidden" name="cliente_tipo" id="cliente_tipo">

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-primary"><i class="far fa-id-card"></i></span> Registro de Nuevo Cliente <span id="titleTipo"></span></h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="row">
          <div class="col">
            <label class="small fw-bold">RFC <span class="text-danger">*</span></label>
            <input type="text" class="form-control fw-bold" name="cliente_rfc" id="input_rfc" maxlength="13" placeholder="Ingresa RFC para detectar tipo" style="text-transform: uppercase;" required>
          </div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-dark"><i class="fas fa-stream"></i></span> Datos Fiscales</h5>
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
                <option value="<?= encryption($regFis['regFis_id']) ?>"><?= $regFis['regFis_codigo'] ?> | <?= $regFis['regFis_descripcion'] ?></option>
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
                <option value="<?= encryption($CFDI['CFDI_id']) ?>"><?= $CFDI['CFDI_codigo'] ?> | <?= $CFDI['CFDI_descripcion'] ?></option>
              <?php
              }
              ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="small fw-bold">RAZÓN SOCIAL</label>
            <input type="text" class="form-control fw-bold" name="cliente_rs" id="cliente_rs" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3 seccion_fisica">
            <label class="small fw-bold">CURP</label>
            <input type="text" class="form-control" name="cliente_curp" id="cliente_curp" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3 seccion_moral">
            <label class="small fw-bold">REGIMEN CAPITAL</label>
            <input type="text" class="form-control" name="cliente_regCap" id="cliente_regCap" style="text-transform: uppercase;">
          </div>

          <div class="col-md-3">
            <label class="small fw-bold">NOMBRE COMERCIAL</label>
            <input type="text" class="form-control" name="cliente_nombreComercial" id="cliente_nombreComercial" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica">
            <label class="small fw-bold">NOMBRE(s)</label>
            <input type="text" class="form-control" name="cliente_nombre" id="cliente_nombre" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica">
            <label class="small fw-bold">1er APELLIDO</label>
            <input type="text" class="form-control" name="cliente_apellido1" id="cliente_apellido1" style="text-transform: uppercase;">
          </div>

          <div class="col seccion_fisica">
            <label class="small fw-bold">2do APELLIDO</label>
            <input type="text" class="form-control" name="cliente_apellido2" id="cliente_apellido2" style="text-transform: uppercase;">
          </div>
        </div>
      </div>
    </div>

    <?php // Apartado de direccion 
    ?>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white pt-4 px-4">
        <h5 class="fw-bold"><span class="text-danger"><i class="fas fa-map-marker-alt me-2"></i></span> Domicilio Fiscal</h5>
        <hr>
      </div>
      <div class="card-body p-4 pt-0">
        <div class="row g-3">
          <div class="col-md-2"><label class="small fw-bold">C.P. <span class="text-danger">*</span></label><input type="text" class="form-control" name="cliente_cp" required></div>
          <div class="col-md-3"><label class="small fw-bold">Tipo Vialidad</label><input type="text" class="form-control" name="cliente_tipoVialidad"></div>
          <div class="col-md-7"><label class="small fw-bold">Calle / Vialidad</label><input type="text" class="form-control" name="cliente_noVialidad"></div>
          <div class="col-md-2"><label class="small fw-bold">Núm. Ext.</label><input type="text" class="form-control" name="cliente_nuExterior"></div>
          <div class="col-md-2"><label class="small fw-bold">Núm. Int.</label><input type="text" class="form-control" name="cliente_nuInterior"></div>
          <div class="col-md-4"><label class="small fw-bold">Colonia</label><input type="text" class="form-control" name="cliente_noColonia"></div>
          <div class="col-md-4"><label class="small fw-bold">Municipio</label><input type="text" class="form-control" name="cliente_noMunicipio"></div>
          <div class="col-md-6"><label class="small fw-bold">Entre Calle</label><input type="text" class="form-control" name="cliente_calle1"></div>
          <div class="col-md-6"><label class="small fw-bold">Y Calle</label><input type="text" class="form-control" name="cliente_calle2"></div>
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
          <div class="col-md-4"><label class="small fw-bold">Persona de Contacto</label><input type="text" class="form-control" name="cliente_contacto" required></div>
          <div class="col-md-4"><label class="small fw-bold">Correo Electrónico</label><input type="email" class="form-control" name="cliente_correo" required></div>
          <div class="col-md-4"><label class="small fw-bold">Teléfono</label><input type="text" class="form-control" name="cliente_telefono" required></div>
        </div>
        <div class="bg-light p-3 rounded mb-4">
          <label class="fw-bold text-danger d-block mb-2">ADJUNTAR CONSTANCIA PDF</label>
          <input type="file" name="cliente_pdf" class="form-control w-50 mx-auto" accept=".pdf">
        </div>
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow"><i class="fas fa-save me-2"></i> GUARDAR CLIENTE</button>
      </div>
    </div>

  </form>
</div>