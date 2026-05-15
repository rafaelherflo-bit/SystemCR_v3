<div class="container-fluid py-3">
  <?php
  require_once $_SERVER["DOCUMENT_ROOT"] . '/vista/assets/pdfparser/alt_autoload.php';
  $parser = new \Smalot\PdfParser\Parser();

  if (isset($_FILES['constancia_pdf'])) {
    try {
      if ($_FILES['constancia_pdf']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir el PDF. Código de error: ' . $_FILES['constancia_pdf']['error']);
      }

      // Estructura inicial de datos
      $datos = [
        'cliente_existe' => FALSE,
        'cliente_id' => 0,
        'cliente_tipo' => '',
        'cliente_rs' => '',
        'cliente_rfc' => '',
        'cliente_curp' => '',
        'cliente_nombreComercial' => '',
        'cliente_regCap' => '',
        'cliente_nombre' => '',
        'cliente_apellido1' => '',
        'cliente_apellido2' => '',
        'cliente_cp' => '',
        'cliente_tipoVialidad' => '',
        'cliente_noVialidad' => '',
        'cliente_nuExterior' => '',
        'cliente_nuInterior' => '',
        'cliente_noColonia' => '',
        'cliente_noLocalidad' => '',
        'cliente_noMunicipio' => '',
        'cliente_entidadFederativa' => '',
        'cliente_calle1' => '',
        'cliente_calle2' => '',
        'cliente_regFis_id' => 0,
        'cliente_cfdi_id' => 0,
        'cliente_contacto' => '',
        'cliente_correo' => '',
        'cliente_telefono' => '',
        'cliente_emiFact' => 1
      ];

      $pdf = $parser->parseFile($_FILES['constancia_pdf']['tmp_name']);
      $text = $pdf->getText();
      $text = preg_replace('/Página\[\d+\]de\[\d+\]/i', '', $text);

      // Extracción de RFC y Tipo con patrones de respaldo
      $datos['cliente_rfc'] = '';
      $rfcPatterns = [
        '/RFC\s*[:\-]?\s*([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/i',
        '/Registro\s*Federal\s*de\s*Contribuyentes\s*[:\-]?\s*([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/i',
        '/CÉDULA\s*DE\s*IDENTIFICACIÓN\s*FISCAL.*?([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/is',
        '/CÉDULA\s*DE\s*IDENTIFICACION\s*FISCAL.*?([A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3})/is'
      ];
      foreach ($rfcPatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
          $datos['cliente_rfc'] = trim($matches[1]);
          break;
        }
      }

      if ($datos['cliente_rfc'] === '') {
        throw new Exception('No se pudo encontrar el RFC dentro del PDF. Verifica que el documento sea una constancia fiscal válida.');
      }

      $datos['cliente_tipo'] = (strlen($datos['cliente_rfc']) === 13) ? "Fisica" : "Moral";

      if (preg_match('/Nombre\s*Comercial:\s*(.*?)\s*(?:Fecha\s*inicio|Datos\s*del\s*domicilio)/is', $text, $m)) $datos['cliente_nombreComercial'] = trim($m[1]);

      if ($datos['cliente_tipo'] == "Fisica") {
        if (preg_match('/CURP:\s*([A-Z]{4}\d{6}[A-Z]{6}[A-Z0-9]{2})/i', $text, $m)) $datos['cliente_curp'] = trim($m[1]);
        // if (preg_match('/CURP:\s*([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNSL]|N[NYL]|P[OT]|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9]\d)/i', $text, $m)) {
        //   $datos['cliente_curp'] = trim($m[1]);
        // }
        if (preg_match('/Nombre\s*\(s\):\s*(.*?)\s*Primer\s*Apellido/is', $text, $m)) $datos['cliente_nombre'] = trim($m[1]);
        if (preg_match('/Primer\s*Apellido:\s*(.*?)\s*Segundo\s*Apellido/is', $text, $m)) $datos['cliente_apellido1'] = trim($m[1]);
        if (preg_match('/Segundo\s*Apellido:\s*(.*?)\s*Fecha\s*inicio/is', $text, $m)) $datos['cliente_apellido2'] = trim($m[1]);
        $datos['cliente_rs'] = trim($datos['cliente_nombre'] . " " . $datos['cliente_apellido1'] . " " . $datos['cliente_apellido2']);
      } else {
        if (preg_match('/Denominación\s*\/\s*Razón\s*Social:\s*(.*?)\s*Régimen\s*Capital/is', $text, $m)) $datos['cliente_rs'] = trim($m[1]);
        if (preg_match('/Régimen\s*Capital:\s*(.*?)\s*(?:Nombre\s*Comercial|Fecha\s*inicio)/is', $text, $m)) $datos['cliente_regCap'] = trim($m[1]);
      }

      // Domicilio
      if (preg_match('/Código\s*Postal:\s*(\d{5})/i', $text, $m)) $datos['cliente_cp'] = $m[1];
      if (preg_match('/Tipo\s*de\s*Vialidad:\s*(.*?)\s*Nombre\s*de\s*Vialidad/i', $text, $m)) $datos['cliente_tipoVialidad'] = trim($m[1]);
      if (preg_match('/Nombre\s*de\s*Vialidad:\s*(.*?)\s*Número\s*Exterior/i', $text, $m)) $datos['cliente_noVialidad'] = trim($m[1]);
      if (preg_match('/Número\s*Exterior:\s*(.*?)\s*Número\s*Interior/i', $text, $m)) $datos['cliente_nuExterior'] = trim($m[1]);
      if (preg_match('/Número\s*Interior:\s*(.*?)\s*Nombre\s*de\s*la\s*Colonia/i', $text, $m)) $datos['cliente_nuInterior'] = trim($m[1]);
      if (preg_match('/Nombre\s*de\s*la\s*Colonia:\s*(.*?)\s*Nombre\s*de\s*la\s*Localidad/i', $text, $m)) $datos['cliente_noColonia'] = trim($m[1]);
      if (preg_match('/Nombre\s*de\s*la\s*Localidad:\s*(.*?)\s*Nombre\s*del\s*Municipio/i', $text, $m)) $datos['cliente_noLocalidad'] = trim($m[1]);
      if (preg_match('/Municipio\s*o\s*Demarcación\s*Territorial:\s*(.*?)\s*Nombre\s*de\s*la\s*Entidad/i', $text, $m)) $datos['cliente_noMunicipio'] = trim($m[1]);
      if (preg_match('/Entidad\s*Federativa:\s*(.*?)\s*Entre\s*Calle/i', $text, $m)) $datos['cliente_entidadFederativa'] = trim($m[1]);
      if (preg_match('/Entre\s*Calle:\s*(.*?)\s*(?:YCalle|Y\s*Calle)/i', $text, $m)) $datos['cliente_calle1'] = trim($m[1]);
      if (preg_match('/YCalle:\s*(.*?)\s*(?:Actividades|Página|Regímenes)/i', $text, $m)) $datos['cliente_calle2'] = trim($m[1]);

      // Régimen Fiscal
      $patternRegimen = '/Regímenes:\s*.*?Fin\s*(.*?)\s*\d{2}\/\d{2}\/\d{4}/is';
      if (preg_match($patternRegimen, $text, $m)) {
        $regNombre = preg_replace('/(?<=[a-z])([A-Z])/', ' $1', trim($m[1]));
        $busReg = consultaData("SELECT regFis_id FROM catRegimenFiscal WHERE regFis_descripcion LIKE '%" . $regNombre . "%' LIMIT 1");
        if ($busReg['numRows'] > 0) $datos['cliente_regFis_id'] = $busReg['dataFetch'][0]['regFis_id'];
      }

      // Búsqueda en DB
      $QRY = consultaData("SELECT * FROM Clientes WHERE cliente_rfc = '" . $datos['cliente_rfc'] . "'");
      if ($QRY['numRows'] > 0) {
        $row = $QRY['dataFetch'][0];
        $datos['cliente_existe'] = true;
        $datos['cliente_id'] = encryption($row['cliente_id']);
        $datos['cliente_contacto'] = $row['cliente_contacto'];
        $datos['cliente_correo'] = $row['cliente_correo'];
        $datos['cliente_telefono'] = $row['cliente_telefono'];
        $datos['cliente_cfdi_id'] = $row['cliente_cfdi_id'];
        if ($datos['cliente_regFis_id'] == 0) $datos['cliente_regFis_id'] = $row['cliente_regFis_id'];
      }
  ?>

      <form class="FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="<?= $datos['cliente_existe'] ? "update" : "save" ?>">
        <input type="hidden" id="cliente_id_input" value="<?= $datos['cliente_id']; ?>" name="<?= $datos['cliente_existe'] ? "actualizarCliente" : "agregarCliente" ?>">
        <input type="hidden" name="cliente_tipo" id="cliente_tipo" value="<?= $datos['cliente_tipo'] ?>">

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="row align-items-center">
              <div class="col-md-7">
                <h5 class="fw-bold mb-0 text-dark">
                  <i class="fas fa-id-card me-2"></i>
                  <?= $datos['cliente_existe'] ? "Actualizando Datos Fiscales" : "Nuevo Registro Detectado" ?>
                </h5>
              </div>
              <div class="col-md-5">
                <div class="input-group input-group-sm">
                  <div class="input-group-text"><input id="cbxUpdate" class="form-check-input mt-0" type="checkbox"></div>
                  <select id="cliente_id_select" class="form-select form-select-sm" disabled>
                    <option value="">ASOCIAR A OTRO CLIENTE...</option>
                    <?php
                    $qryC = consultaData("SELECT * FROM Clientes ORDER BY cliente_rs ASC");
                    foreach ($qryC['dataFetch'] as $dc) {
                      echo '<option value="' . encryption($dc['cliente_id']) . '">(' . $dc['cliente_rfc'] . ') ' . $dc['cliente_rs'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <hr>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white pt-4 px-4">
            <h5 class="fw-bold"><span class="text-primary"><i class="far fa-id-card me-2"></i></span>Actualizar Cliente de Tipo: <span id="titleTipo"><?= $datos['cliente_tipo'] ?></span></h5>
            <hr>
          </div>
          <div class="card-body p-4 pt-0">
            <div class="row">
              <div class="col">
                <label class="small fw-bold">RFC <span class="text-danger">*</span></label>
                <input type="text" class="form-control fw-bold" name="cliente_rfc" id="input_rfc" value="<?= $datos['cliente_rfc'] ?>" maxlength="13" placeholder="Ingresa RFC para detectar tipo" style="text-transform: uppercase;" required>
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
                    <option value="<?= encryption($regFis['regFis_id']) ?>" <?= $datos['cliente_regFis_id'] == $regFis['regFis_id'] ? "selected" : "" ?>><?= $regFis['regFis_codigo'] ?> | <?= $regFis['regFis_descripcion'] ?></option>
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
                    <option value="<?= encryption($CFDI['CFDI_id']) ?>" <?= $datos['cliente_cfdi_id'] == $CFDI['CFDI_id'] ? "selected" : "" ?>><?= $CFDI['CFDI_codigo'] ?> | <?= $CFDI['CFDI_descripcion'] ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-3">
                <label class="small fw-bold">RAZÓN SOCIAL</label>
                <input type="text" class="form-control fw-bold" name="cliente_rs" id="cliente_rs" value="<?= $datos['cliente_rs'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col-md-3 seccion_fisica <?= $datos['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
                <label class="small fw-bold">CURP</label>
                <input type="text" class="form-control" name="cliente_curp" id="cliente_curp" value="<?= $datos['cliente_curp'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col-md-3 seccion_moral <?= $datos['cliente_tipo'] === "Fisica" ? "d-none" : "" ?>">
                <label class="small fw-bold">REGIMEN CAPITAL</label>
                <input type="text" class="form-control" name="cliente_regCap" id="cliente_regCap" value="<?= $datos['cliente_regCap'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col-md-3">
                <label class="small fw-bold">NOMBRE COMERCIAL</label>
                <input type="text" class="form-control" name="cliente_nombreComercial" id="cliente_nombreComercial" value="<?= $datos['cliente_nombreComercial'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col seccion_fisica <?= $datos['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
                <label class="small fw-bold">NOMBRE(s)</label>
                <input type="text" class="form-control" name="cliente_nombre" id="cliente_nombre" value="<?= $datos['cliente_nombre'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col seccion_fisica <?= $datos['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
                <label class="small fw-bold">1er APELLIDO</label>
                <input type="text" class="form-control" name="cliente_apellido1" id="cliente_apellido1" value="<?= $datos['cliente_apellido1'] ?>" style="text-transform: uppercase;">
              </div>

              <div class="col seccion_fisica <?= $datos['cliente_tipo'] === "Moral" ? "d-none" : "" ?>">
                <label class="small fw-bold">2do APELLIDO</label>
                <input type="text" class="form-control" name="cliente_apellido2" id="cliente_apellido2" value="<?= $datos['cliente_apellido2'] ?>" style="text-transform: uppercase;">
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
              <div class="col-md-2"><label class="small fw-bold">C.P. <span class="text-danger">*</span></label><input type="text" class="form-control" name="cliente_cp" value="<?= $datos['cliente_cp'] ?>" required></div>
              <div class="col-md-3"><label class="small fw-bold">Tipo Vialidad</label><input type="text" class="form-control" name="cliente_tipoVialidad" value="<?= $datos['cliente_tipoVialidad'] ?>"></div>
              <div class="col-md-7"><label class="small fw-bold">Calle / Vialidad</label><input type="text" class="form-control" name="cliente_noVialidad" value="<?= $datos['cliente_noVialidad'] ?>"></div>
              <div class="col-md-2"><label class="small fw-bold">Núm. Ext.</label><input type="text" class="form-control" name="cliente_nuExterior" value="<?= $datos['cliente_nuExterior'] ?>"></div>
              <div class="col-md-2"><label class="small fw-bold">Núm. Int.</label><input type="text" class="form-control" name="cliente_nuInterior" value="<?= $datos['cliente_nuInterior'] ?>"></div>
              <div class="col-md-4"><label class="small fw-bold">Colonia</label><input type="text" class="form-control" name="cliente_noColonia" value="<?= $datos['cliente_noColonia'] ?>"></div>
              <div class="col-md-4"><label class="small fw-bold">Municipio</label><input type="text" class="form-control" name="cliente_noMunicipio" value="<?= $datos['cliente_noMunicipio'] ?>"></div>
              <div class="col-md-6"><label class="small fw-bold">Entre Calle</label><input type="text" class="form-control" name="cliente_calle1" value="<?= $datos['cliente_calle1'] ?>"></div>
              <div class="col-md-6"><label class="small fw-bold">Y Calle</label><input type="text" class="form-control" name="cliente_calle2" value="<?= $datos['cliente_calle2'] ?>"></div>
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
              <div class="col-md-4"><label class="small fw-bold">Persona de Contacto</label><input type="text" class="form-control" name="cliente_contacto" value="<?= $datos['cliente_contacto'] ?>" required></div>
              <div class="col-md-4"><label class="small fw-bold">Correo Electrónico</label><input type="email" class="form-control" name="cliente_correo" value="<?= $datos['cliente_correo'] ?>" required></div>
              <div class="col-md-4"><label class="small fw-bold">Teléfono</label><input type="text" class="form-control" name="cliente_telefono" value="<?= $datos['cliente_telefono'] ?>" required></div>
            </div>
            <div class="bg-light p-3 rounded mb-4">
              <label class="fw-bold text-danger d-block mb-2">ADJUNTAR CONSTANCIA PDF</label>
              <input type="file" name="cliente_pdf" class="form-control w-50 mx-auto" accept=".pdf">
            </div>
            <button type="submit" class="btn btn-<?= $datos['cliente_existe'] ? 'warning' : 'primary' ?> btn-lg px-5 shadow"><?= $datos['cliente_existe'] ? '<i class="fas fa-sync-alt me-2"></i> ACTUALIZAR' : '<i class="fas fa-save me-2"></i> GUARDAR' ?> CLIENTE</button>
          </div>
        </div>
      </form>

      <script>
        // Lógica de Asociación Manual
        if (document.getElementById('cbxUpdate')) {
          const cbx = document.getElementById('cbxUpdate');
          const sel = document.getElementById('cliente_id_select');
          const inp = document.getElementById('cliente_id_input');

          cbx.addEventListener('change', function() {
            if (this.checked) {
              sel.disabled = false;
              inp.disabled = true;
              sel.name = "actualizarCliente";
              inp.removeAttribute('name');
            } else {
              sel.disabled = true;
              inp.disabled = false;
              sel.removeAttribute('name');
              inp.name = "agregarCliente";
            }
          });
        }
      </script>

    <?php
    } catch (Exception $e) {
      echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
  } else {
    // Vista de Carga Inicial
    ?>
    <div class="col-12 col-md-5 mx-auto mt-5">
      <div class="card shadow-lg border-0">
        <div class="card-body p-5 text-center">
          <i class="fas fa-file-pdf fa-4x text-danger mb-4"></i>
          <h4 class="fw-bold">Lector Fiscal SAT</h4>
          <p class="text-muted">Carga el PDF para autocompletar la ficha del cliente.</p>
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="constancia_pdf" class="form-control mb-3" accept=".pdf" required>
            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">ANALIZAR DOCUMENTO</button>
          </form>
        </div>
      </div>
    </div>
  <?php } ?>
</div>