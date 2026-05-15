<?php  ?>
<div class="container-fluid">
  <ul class="full-box list-unstyled page-nav-tabs">
    <li>
      <a class="active" class=""><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR RENTA</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Rentas/Lista"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE RENTAS</a>
    </li>
    <li>
      <a href="<?= SERVERURL; ?>Rentas/Otros"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; NO ACTIVOS</a>
    </li>
    <li>
      <a id="btnCostos"><i class="fas fa-file-pdf fa-fw"></i> &nbsp; COSTOS POR RENTAS</a>
    </li>
  </ul>
</div>

<div class="container-fluid">
  <form class="form-neon FormularioAjax" action="<?= SERVERURL; ?>ajax/controllerAjax.php" method="post" data-form="save" autocomplete="off">
    <input type="hidden" name="nuevaRenta" value="1">
    <fieldset>
      <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-1">
            <div class="form-group">
              <label for="renta_finicio" class="bmd-label-floating">FECHA DE INICIO</label>
              <input type="date" class="form-control" id="renta_finicio" name="renta_finicio" value="<?= date("Y-m-d"); ?>">
            </div>
          </div>
          <div class="col-12 col-md-3">
            <div class="form-group">
              <label for="renta_contrato_id" class="bmd-label-floating">CONTRATO</label>
              <select class="form-select" id="renta_contrato_id" name="renta_contrato_id" data-placeholder="Selecciona un Contrato">
                <option></option>
                <?php
                $sql = "SELECT * FROM Contratos
                                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                    ORDER BY contrato_folio ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['contrato_id']); ?>"><?= "(" . $dato['contrato_folio'] . ") " . $dato['contrato_folio'] . " | (" . $dato['cliente_rfc'] . ") " . $dato['cliente_rs']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="contacto" class="bmd-label-floating">CONTACTO</label>
              <input type="text" class="form-control" list="contacto_list" id="contacto" name="renta_contacto" maxlength="50" pattern="[a-zA-Z ?.?]{5,50}">
              <datalist id="contacto_list">
                <?php
                $sqlContactos = "SELECT * FROM Rentas ORDER BY renta_contacto ASC";
                $queryContactos = consultaData($sqlContactos);
                $dataContactos = $queryContactos['dataFetch'];
                $contacto = "";
                foreach ($dataContactos as $datoContacto) {
                  if ($datoContacto['renta_contacto'] != $contacto) {
                    $contacto = $datoContacto['renta_contacto']; ?>
                    <option value="<?= $datoContacto['renta_contacto']; ?>"><?= $datoContacto['renta_contacto']; ?></option>
                  <?php } ?>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="telefono" class="bmd-label-floating">TELEFONO</label>
              <input type="text" class="form-control" list="telefono_list" id="telefono" name="renta_telefono" maxlength="50" pattern="[0-9]{0,15}">
              <datalist id="telefono_list">
                <?php
                $telefono = "";
                foreach ($dataContactos as $datoTelefono) {
                  if ($datoTelefono['renta_telefono'] != $telefono) {
                    $telefono = $datoTelefono['renta_telefono']; ?>
                    <option value="<?= $datoTelefono['renta_telefono']; ?>"><?= $datoTelefono['renta_contacto']; ?></option>
                  <?php } ?>
                <?php } ?>
              </datalist>
            </div>
          </div>
          <div class="col-12 col-md-1">
            <div class="form-group">
              <label for="renta_tipo" class="bmd-label-floating">TIPO DE RENTA</label>
              <select class="form-select" id="renta_tipo" name="renta_tipo" data-placeholder="Tipo de Renta">
                <option></option>
                <option value="fija">Fija</option>
                <option value="temporal">Temporal</option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-2">
            <div class="form-group">
              <label for="zona_id" class="bmd-label-floating">ZONA</label>
              <select class="form-select" id="zona_id" name="zona_id" data-placeholder="Zona">
                <option></option>
                <?php
                $sql = "SELECT * FROM Zonas ORDER BY zona_codigo ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['zona_id']); ?>"><?= "(" . $dato['zona_codigo'] . ") " . $dato['zona_nombre']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-1">
            <div class="form-group">
              <label for="renta_depto" class="bmd-label-floating">DEPARTAMENTO</label>
              <input type="text" class="form-control" list="deptos_list" id="renta_depto" name="renta_depto" maxlength="50" pattern="[a-zA-Z ?.?]{4,50}" placeholder="DEPARTAMENTO">
              <datalist id="deptos_list">
                <?php
                $deptos_list = [
                  "Recursos Humanos",
                  "Finanzas",
                  "Marketing",
                  "Ventas",
                  "Produccion",
                  "Operaciones",
                  "Logística",
                  "Compras",
                  "Sistemas",
                  "Recepcion",
                  "Nominas",
                  "Administracion",
                  "Dirección General",
                  "Oficina de Obra",
                ];

                foreach ($deptos_list as $datoDepto) { ?>
                  <option value="<?= $datoDepto; ?>"><?= $datoDepto; ?></option>
                <?php } ?>
              </datalist>
            </div>
          </div>
        </div>

        <hr>

        <!-- INICIO ------ DATOS DEL EQUIPO ------ INICIO -->
        <div class="row">
          <div class="col-12 col-md-5">
            <div class="form-group">
              <select class="form-select" id="equipo_id" name="equipo_id" data-placeholder="Selecciona un Equipo">
                <option></option>
                <?php
                $sql = "SELECT * FROM Equipos
                                    INNER JOIN Modelos ON Equipos.equipo_modelo_id = Modelos.modelo_id
                                    WHERE equipo_estado = 'Espera'
                                    ORDER BY equipo_estado ASC";
                $query = consultaData($sql);
                $dataTon = $query['dataFetch'];
                foreach ($dataTon as $dato) { ?>
                  <option value="<?= encryption($dato['equipo_id']); ?>"><?= $dato['modelo_linea'] . " " . $dato['modelo_modelo'] . " (" . $dato['equipo_codigo'] . " | " . $dato['equipo_serie'] . ") - " . $dato['equipo_estado']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <br>
          <div class="col-12 col-md-2" id="dataStock" style="display: none;">
            <div class="form-group">
              <div class="custom-control custom-radio">
                <input type="radio" id="stock1" name="renta_stock" value="false" class="custom-control-input" checked>
                <label class="custom-control-label" for="stock1">Sin Stock</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="stock2" name="renta_stock" value="true" class="custom-control-input">
                <label class="custom-control-label" for="stock2">Con Stock</label>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-5" id="renta_stock">
          </div>
        </div>
        <div class="row">
          <div class="col" id="col_equipo_nivel_K" style="display: none;">
            <div class="form-group">
              <label for="equipo_nivel_K" class="bmd-label-floating">NIVEL NEGRO</label>
              <input type="number" class="form-control" id="equipo_nivel_K" name="equipo_nivel_K" maxlength="50" value="25">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_M" style="display: none;">
            <div class="form-group">
              <label for="equipo_nivel_M" class="bmd-label-floating">NIVEL MAGENTA</label>
              <input type="number" class="form-control" id="equipo_nivel_M" name="equipo_nivel_M" maxlength="50" value="0">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_C" style="display: none;">
            <div class="form-group">
              <label for="equipo_nivel_C" class="bmd-label-floating">NIVEL CYAN</label>
              <input type="number" class="form-control" id="equipo_nivel_C" name="equipo_nivel_C" maxlength="50" value="0">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_Y" style="display: none;">
            <div class="form-group">
              <label for="equipo_nivel_Y" class="bmd-label-floating">NIVEL AMARILLO</label>
              <input type="number" class="form-control" id="equipo_nivel_Y" name="equipo_nivel_Y" maxlength="50" value="0">
            </div>
          </div>
          <div class="col" id="col_equipo_nivel_R" style="display: none;">
            <div class="form-group">
              <label for="equipo_nivel_R" class="bmd-label-floating">NIVEL RESIDUAL</label>
              <input type="number" class="form-control" id="equipo_nivel_R" name="equipo_nivel_R" maxlength="50" value="0">
            </div>
          </div>
        </div>
        <!-- FIN ------ DATOS DEL EQUIPO ------ FIN -->

        <hr>

        <legend><i class="fas fa-map-pin"></i> &nbsp; Ubicacion Geografica</legend>
        <input type="hidden" id="renta_coor" name="renta_coor" data-tipo="add">
        <div id="map_addRentas" class="row">
        </div>

        <br><br>

        <legend><i class="fas fa-info-circle"></i> &nbsp; Detalles de la Renta</legend>
        <p>Estos datos apareceran en los formatos de Lecturas y reportes.</p>
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="form-group">
              <label for="renta_costo" class="bmd-label-floating">COSTO MENSUAL DE LA RENTA</label>
              <input type="number" class="form-control" id="renta_costo" name="renta_costo" pattern="^[0-9]+?" value="0">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="renta_inc_esc" class="bmd-label-floating">CANTIDAD INCLUIDA DE ESCANEOS</label>
              <input type="number" class="form-control" id="renta_inc_esc" name="renta_inc_esc" pattern="^[0-9]+?" value="0">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="renta_inc_bn" class="bmd-label-floating">CANTIDAD INCLUIDA DE B&N</label>
              <input type="number" class="form-control" id="renta_inc_bn" name="renta_inc_bn" pattern="^[0-9]+?" value="0">
            </div>
          </div>
          <div class="col-12 col-md-4" id="DIV_renta_inc_col">
            <div class="form-group">
              <label for="renta_inc_col" class="bmd-label-floating">CANTIDAD INCLUIDA DE COLOR</label>
              <input type="number" class="form-control" id="renta_inc_col" name="renta_inc_col" pattern="^[0-9]+?" value="0">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="renta_exc_esc" class="bmd-label-floating">COBRO POR EXECEDENTE DE ESCANEOS</label>
              <input type="float" class="form-control" id="renta_exc_esc" name="renta_exc_esc" pattern="^([0-9]+\.?[0-9]{0,3})$" value="0.0">
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="form-group">
              <label for="renta_exc_bn" class="bmd-label-floating">COBRO POR EXECEDENTE DE B&N</label>
              <input type="float" class="form-control" id="renta_exc_bn" name="renta_exc_bn" pattern="^([0-9]+\.?[0-9]{0,3})$" value="0.0">
            </div>
          </div>
          <div class="col-12 col-md-4" id="DIV_renta_exc_col">
            <div class="form-group">
              <label for="renta_exc_col" class="bmd-label-floating">COBRO POR EXECEDENTE DE COLOR</label>
              <input type="float" class="form-control" id="renta_exc_col" name="renta_exc_col" pattern="^([0-9]+\.?[0-9]{0,3})$" value="0.0">
            </div>
          </div>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col">
          <legend><i class="fas fa-info-circle"></i> &nbsp; Contadores de Lectura</legend>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label for="lectura_esc" class="bmd-label-floating">ESCANEO</label>
                <input type="number" class="form-control" id="lectura_esc" name="lectura_esc" maxlength="50">
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label for="lectura_bn" class="bmd-label-floating">B&N</label>
                <input type="number" class="form-control" id="lectura_bn" name="lectura_bn" maxlength="50">
              </div>
            </div>
            <div class="col-12" id="col_lectura_col">
              <div class="form-group">
                <label for="lectura_col" class="bmd-label-floating">COLOR</label>
                <input type="number" class="form-control" id="lectura_col" name="lectura_col" maxlength="50" value="0">
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <legend><i class="fas fa-info-circle"></i> &nbsp; Evidencias de Entrega</legend>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <input type="checkbox" class="btn-check" id="lectura_formato" autocomplete="off">
                <label class="btn btn-outline-primary" for="lectura_formato">FORMATO DE LECTURA</label>
                <div id="div_lectura_formato">
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col">
              <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                <input type="checkbox" class="btn-check" id="lectura_estado" autocomplete="off">
                <label class="btn btn-outline-primary" for="lectura_estado">PAGINA DE ESTADO</label>
              </div>
              <div class="form-group">
                <div id="div_lectura_estado">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </fieldset>
    <?php include SERVERDIR . "vista/inc/endForm.php"; ?>
  </form>
</div>