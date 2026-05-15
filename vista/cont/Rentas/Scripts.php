<script>
  var pagina = '<?= $pagina[1] ?>';
  if (pagina == 'Lista' || pagina == 'Otros') {
    tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);
  } else {
    tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", <?= $DT_orderCol; ?>);
  }

  if (document.querySelector(".btn-lect")) {
    document.querySelectorAll('.btn-lect').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var tipoBTN = elem.dataset.tipo;

        if (tipoBTN == "cambio") {
          titulo = "VISUALIZAR EVIDENCIA DE CAMBIO";
          urlFinal = elem.dataset.url;
          Swal.fire({
            title: titulo,
            text: "Visualizar cambio de equipo??.",
            icon: "question",
            confirmButtonText: 'Continuar',
            confirmButtonColor: "#008341",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            cancelButtonColor: "#440d00",
          }).then(async function(res) {
            if (res.isConfirmed) {
              window.open(urlFinal, titulo)
            }
          });
        } else {
          var renta_id = elem.dataset.rentaid;
          var custom_mes = elem.dataset.mes;
          var custom_anio = elem.dataset.anio;
          var lecturaID = elem.dataset.lectura;

          const res = await fetch(SERVERURL + "ajax/queryFetchLect.php", {
            method: "POST",
            body: JSON.stringify({
              renta_id,
              custom_mes,
              custom_anio,
            }),
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
            },
          });
          const result = await res.json();
          var TypeLect = result.Type;

          /*
              Type    Descripcion
              0       Sin datos de lectura actual y anterior.
              1       Unicamente con lectura actual.
              2       Unicamente con lectura anterior.
              3       Lectura completa.
              4       Sin lectura actual con ajuste por cambio.
              5       Lectura completa con ajuste por cambio.
              6       Imprimir para toma de lectura.
          */

          var mapForm = document.createElement("form");
          mapForm.target = "_blank";
          mapForm.method = "POST";
          mapForm.action = SERVERURL + "vista/formats/printLect_tipo" + TypeLect + ".php";

          function valAppendChild(nam, val) {
            var variable = document.createElement("input");
            variable.type = "hidden";
            variable.name = nam;
            variable.value = val;
            mapForm.appendChild(variable);
          }

          valAppendChild("current_year", custom_anio);
          valAppendChild("current_month", custom_mes);
          valAppendChild("cliente_rs", result.rentaData.cliente_rs);
          valAppendChild("cliente_emiFact", result.rentaData.cliente_emiFact);
          valAppendChild("cliente_rfc", result.rentaData.cliente_rfc);
          valAppendChild("cliente_cp", result.rentaData.cliente_cp);
          valAppendChild("contrato_folio", result.rentaData.contrato_folio);

          valAppendChild("CFDI_codigo", result.rentaData.CFDI_codigo);
          valAppendChild("CFDI_descripcion", result.rentaData.CFDI_descripcion);
          valAppendChild("regFis_codigo", result.rentaData.regFis_codigo);
          valAppendChild("regFis_descripcion", result.rentaData.regFis_descripcion);

          valAppendChild("renta_id", elem.id);
          valAppendChild("renta_folio", result.rentaData.renta_folio);
          valAppendChild("renta_depto", result.rentaData.renta_depto);
          valAppendChild("renta_contacto", result.rentaData.renta_contacto);
          valAppendChild("renta_telefono", result.rentaData.renta_telefono);
          valAppendChild("zona_nombre", result.rentaData.zona_nombre);

          valAppendChild("renta_costo", result.rentaData.renta_costo);
          valAppendChild("renta_inc_esc", result.rentaData.renta_inc_esc);
          valAppendChild("renta_inc_bn", result.rentaData.renta_inc_bn);
          valAppendChild("renta_inc_col", result.rentaData.renta_inc_col);
          valAppendChild("renta_exc_esc", result.rentaData.renta_exc_esc);
          valAppendChild("renta_exc_bn", result.rentaData.renta_exc_bn);
          valAppendChild("renta_exc_col", result.rentaData.renta_exc_col);

          valAppendChild("equipo_codigo", result.rentaData.equipo_codigo);
          valAppendChild("equipo_serie", result.rentaData.equipo_serie);
          valAppendChild("equipo_serie", result.rentaData.equipo_serie);
          valAppendChild("modelo_tipo", result.rentaData.modelo_tipo);
          valAppendChild("modelo_linea", result.rentaData.modelo_linea);
          valAppendChild("modelo_modelo", result.rentaData.modelo_modelo);
          valAppendChild("modelo_toner", result.rentaData.modelo_toner);

          valAppendChild("equipo_nivel_K", result.rentaData.equipo_nivel_K);
          valAppendChild("renta_stock_K", result.rentaData.renta_stock_K);
          valAppendChild("equipo_nivel_M", result.rentaData.equipo_nivel_M);
          valAppendChild("renta_stock_M", result.rentaData.renta_stock_M);
          valAppendChild("equipo_nivel_C", result.rentaData.equipo_nivel_C);
          valAppendChild("renta_stock_C", result.rentaData.renta_stock_C);
          valAppendChild("equipo_nivel_Y", result.rentaData.equipo_nivel_Y);
          valAppendChild("renta_stock_Y", result.rentaData.renta_stock_Y);
          valAppendChild("equipo_nivel_R", result.rentaData.equipo_nivel_R);
          valAppendChild("renta_stock_R", result.rentaData.renta_stock_R);

          if (TypeLect == 3) {
            Swal.fire({
              title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
              text: "Lectura Completa.",
              icon: "info",
              confirmButtonText: 'Continuar',
              confirmButtonColor: "#008341",
              showCancelButton: true,
              cancelButtonText: 'Cancelar',
              cancelButtonColor: "#440d00",
              showDenyButton: true,
              denyButtonText: 'Editar',
              denyButtonColor: "#aba300ff"
            }).then(async function(res) {
              if (res.isConfirmed) {
                valAppendChild("curr_reporte_id", result.currLectData.lectura_reporte_id);
                valAppendChild("curr_lectura_fecha", result.currLectData.lectura_fecha);
                valAppendChild("curr_lectura_pdf", result.currLectData.lectura_pdf);
                valAppendChild("curr_lectura_esc", result.currLectData.lectura_esc);
                valAppendChild("curr_lectura_bn", result.currLectData.lectura_bn);
                valAppendChild("curr_lectura_col", result.currLectData.lectura_col);

                valAppendChild("prev_reporte_id", result.prevLectData.lectura_reporte_id);
                valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
                valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
                valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
                valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
                valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);
                document.body.appendChild(mapForm);
                mapForm.submit();
              } else if (res.isDenied) {
                location.href = SERVERURL + "Lecturas/ID/" + lecturaID;
              }
            });
          }

          if (TypeLect == 5) {
            Swal.fire({
              title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
              text: "Lectura completa con ajuste por cambio.",
              icon: "info",
              confirmButtonText: 'Continuar',
              confirmButtonColor: "#008341",
              showCancelButton: true,
              cancelButtonText: 'Cancelar',
              cancelButtonColor: "#440d00",
              showDenyButton: true,
              denyButtonText: 'Editar',
              denyButtonColor: "#aba300ff"
            }).then(async function(res) {
              if (res.isConfirmed) {
                valAppendChild("cambio_fecha", result.adjuLectData.cambio_fecha);

                valAppendChild("cambio_equipoIng_id", result.adjuLectData.cambio_equipoIng_id);
                valAppendChild("cambio_Ing_esc", result.adjuLectData.cambio_Ing_esc);
                valAppendChild("cambio_Ing_bn", result.adjuLectData.cambio_Ing_bn);
                valAppendChild("cambio_Ing_col", result.adjuLectData.cambio_Ing_col);

                valAppendChild("cambio_equipoRet_id", result.adjuLectData.cambio_equipoRet_id);
                valAppendChild("cambio_Ret_esc", result.adjuLectData.cambio_Ret_esc);
                valAppendChild("cambio_Ret_bn", result.adjuLectData.cambio_Ret_bn);
                valAppendChild("cambio_Ret_col", result.adjuLectData.cambio_Ret_col);

                valAppendChild("prev_reporte_id", result.prevLectData.lectura_reporte_id);
                valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
                valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
                valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
                valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
                valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);

                valAppendChild("curr_reporte_id", result.currLectData.lectura_reporte_id);
                valAppendChild("curr_lectura_fecha", result.currLectData.lectura_fecha);
                valAppendChild("curr_lectura_pdf", result.currLectData.lectura_pdf);
                valAppendChild("curr_lectura_esc", result.currLectData.lectura_esc);
                valAppendChild("curr_lectura_bn", result.currLectData.lectura_bn);
                valAppendChild("curr_lectura_col", result.currLectData.lectura_col);
                document.body.appendChild(mapForm);
                mapForm.submit();
              } else if (res.isDenied) {
                location.href = SERVERURL + "Lecturas/ID/" + lecturaID;
              }
            });
          }

        }
      });
    });
  }


  // document.addEventListener("DOMContentLoaded", function() {
  //   // Usamos delegación de eventos por si la tabla se recarga dinámicamente
  //   document.body.addEventListener("click", function(event) {
  //     const btn = event.target.closest('.btn-visualizar');

  //     if (btn) {
  //       let urlFinal = "";
  //       let titulo = "";

  //       if (btn.dataset.tipo === "lectura") {
  //         // Es una lectura: apunta al generador PHP
  //         const lecturaID = btn.dataset.id;
  //         titulo = "VISUALIZAR FORMATO DE LECTURA";
  //         urlFinal = SERVERURL + 'vista/cont/Rentas/printLect.php?lecturaid=' + lecturaID;
  //       } else {
  //         // Es un cambio: usa la ruta directa al PDF estático
  //         titulo = "VISUALIZAR EVIDENCIA DE CAMBIO";
  //         urlFinal = btn.dataset.url;
  //       }

  //       // Actualizar Modal
  //       const modalTitle = document.getElementById("modalTitleFull");
  //       const modalBody = document.getElementById("modalBodyFull");

  //       if (modalTitle && modalBody) {
  //         modalTitle.innerText = titulo;
  //         // Usamos <embed> o <iframe> para mostrar el PDF
  //         modalBody.innerHTML = `
  //                   <div style="height: 80vh; width: 100%;">
  //                       <embed src="${urlFinal}" type="application/pdf" width="100%" height="100%">
  //                   </div>`;

  //         // Mostrar el modal (usando jQuery como en tu ejemplo)
  //         $("#modalFull").modal("show");
  //       }
  //     }
  //   });
  // });

  if (document.getElementById('btnCliente')) {
    document.getElementById('btnCliente').addEventListener("click", async function(e) {
      var valor = document.getElementById('btnCliente').dataset.valor;
      var Data = await QRYajax(0, 'cliente_id_enc', valor);
      if (Data['Status']) {
        Data = Data['Data'][0];
        var Title = "EDITAR CLIENTE";
        var html = `
                            <fieldset>
                                <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" id="cliente_id_edit" name="cliente_id_edit" value="` + valor + `">
                                                <label for="cliente_rs_edit" class="bmd-label-floating">RAZON SOCIAL</label>
                                                <input type="text" class="form-control" list="clientes_rs" id="cliente_rs_edit" name="cliente_rs_edit" maxlength="150" value="` + Data['cliente_rs'] + `">
                                                <datalist id="clientes_rs">
                                                    <?php
                                                    $sql = "SELECT * FROM Clientes ORDER BY cliente_rs ASC";
                                                    $query = consultaData($sql);
                                                    $dataTon = $query['dataFetch'];
                                                    foreach ($dataTon as $dato) { ?>
                                                        <option value="<?php echo $dato['cliente_rs']; ?>"><?php echo $dato['cliente_rs']; ?></option>
                                                    <?php } ?>
                                                </datalist>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="cliente_rfc_edit" class="bmd-label-floating">RFC</label>
                                                <input type="text" class="form-control" list="clientes_rfc" id="cliente_rfc_edit" name="cliente_rfc_edit" maxlength="50" pattern="[a-zA-Z0-9]{10,15}" value="` + Data['cliente_rfc'] + `">
                                                <datalist id="clientes_rfc">
                                                    <?php
                                                    $sql = "SELECT * FROM Clientes ORDER BY cliente_rfc ASC";
                                                    $query = consultaData($sql);
                                                    $dataTon = $query['dataFetch'];
                                                    foreach ($dataTon as $dato) { ?>
                                                        <option value="<?php echo $dato['cliente_rfc']; ?>"><?php echo $dato['cliente_rfc']; ?></option>
                                                    <?php } ?>
                                                </datalist>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="cliente_contacto_edit" class="bmd-label-floating">CONTACTO</label>
                                                <input type="text" class="form-control" list="clientes_contactos" id="cliente_contacto_edit" name="cliente_contacto_edit" maxlength="100" value="` + Data['cliente_contacto'] + `">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="cliente_telefono_edit" class="bmd-label-floating">TELEFONO</label>
                                                <input type="text" class="form-control" list="clientes_rfc" id="cliente_telefono_edit" name="cliente_telefono_edit" maxlength="25" value="` + Data['cliente_telefono'] + `">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            `;
      } else {
        var Title = "ERROR";
        var html = "(O _ o) No existe el cliente solicitado (o _ O)";
      }
      document.getElementById("modalTitleForm").innerText = Title;
      document.getElementById("modalBodyForm").innerHTML = html;
      $('#modalForm').modal('show');
    });
  }

  if (document.querySelector('.check-renta')) {
    document.querySelectorAll('.check-renta').forEach(checkbox => {
      checkbox.addEventListener('change', async function() {
        var renta_id = this.id;
        var tipo = this.dataset.tipo; // 'facturado' o 'pagado'
        var valor = this.checked ? 1 : 0;
        var anio = this.dataset.anio;
        var mes = this.dataset.mes;

        var ajax = await QRYajax("Rentas_Checks", renta_id, tipo, valor, anio, mes);

        // Swal.fire({
        //   title: ajax['Status'] ? "Exito" : "Error",
        //   text: ajax['Status'] ? "Accion Completada" : "Ocurrio un error",
        //   icon: ajax['Data'],
        // });
        console.log(ajax['Data']);
      });
    });
  }

  if (document.getElementById("tablaRentasCheck")) {
    const buscador = document.getElementById('inputBuscador');
    const tabla = document.getElementById('tablaRentasCheck').getElementsByTagName('tbody')[0];
    const filas = tabla.getElementsByTagName('tr');
    const totalDisplay = document.getElementById('montoTotalHTML');

    buscador.addEventListener('keyup', function() {
      const termino = buscador.value.toLowerCase();
      let sumaTotal = 0;

      for (let i = 0; i < filas.length; i++) {
        // Buscamos en Folio(3), Cliente(4), Renta(5) y Costo(6)
        const textoFila = filas[i].innerText.toLowerCase();

        if (textoFila.indexOf(termino) > -1) {
          filas[i].style.display = "";
          // Sumar el valor del atributo data-valor de la celda de costo
          const costo = parseFloat(filas[i].querySelector('.monto-fila').getAttribute('data-valor'));
          sumaTotal += costo;
        } else {
          filas[i].style.display = "none";
        }
      }

      // Actualizar el texto del total formateado
      totalDisplay.innerHTML = '$' + sumaTotal.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    });

  }

  // Modal de ventana para imprimir Stock De Toners
  if (document.getElementById("btnCostos")) {
    document.getElementById("btnCostos").addEventListener("click", async function(e) {
      e.preventDefault();

      var titleModal = "COSTOS POR RENTAS";
      var url = SERVERURL + "vista/formats/rentasCostos.php";
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = '<embed src="' + url + '" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">';
      $("#modalFull").modal("show");
    })
  }

  if (document.getElementById("btnCostos2")) {
    document.getElementById("btnCostos2").addEventListener("click", async function(e) {
      e.preventDefault();

      var titleModal = "FORMATO DE RENTAS";
      var url = SERVERURL + "vista/formats/rentasCostos2.php";
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = '<embed src="' + url + '" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">';
      $("#modalFull").modal("show");
    })
  }

  if (document.querySelector('.btnDetails')) {
    document.querySelectorAll('.btnDetails').forEach((elem) => {
      elem.addEventListener("click", async function() {
        var url = SERVERURL + "Rentas/Detalles/" + elem.value;
        window.location.href = url;
      });
    });
  }

  if (document.getElementById('btnEdit')) {
    var btnEdit = document.getElementById('btnEdit');
    btnEdit.addEventListener("click", async function(e) {
      e.preventDefault();
      var url = SERVERURL + "Rentas/Editar/" + btnEdit.value;
      window.location.href = url;
    });
  }

  if (document.getElementById('btnDetails')) {
    var btnDetails = document.getElementById('btnDetails');
    btnDetails.addEventListener("click", async function(e) {
      e.preventDefault();
      var url = SERVERURL + "Rentas/Detalles/" + btnDetails.value;
      window.location.href = url;
    });
  }

  if (document.getElementById("map_addRentas")) {

    if (document.getElementById("renta_coor").dataset.tipo == "add") {
      // ------====== Acciones al Agregar Renta
      var coorsLat = 21.160867273665243;
      var coorsLon = -86.85246894571384;

      var map = L.map('map_addRentas').setView([coorsLat, coorsLon], 13);
      L.tileLayer(mapTheme1, {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map);

      var popup = L.popup();

      function onMapClick(e) {

        var rentaCoords = e.latlng;

        popup
          .setLatLng(e.latlng)
          .setContent("La nueva Renta se encuentra Aqui<br>" + rentaCoords.lat + ", " + rentaCoords.lng)
          .openOn(map);


        document.getElementById("renta_coor").value = rentaCoords.lat + ", " + rentaCoords.lng;
      }

    } else if (document.getElementById("renta_coor").dataset.tipo == "edit") {
      // ------====== Acciones al Editar Renta
      var coors = document.getElementById("renta_coor").value;
      var coorsLat = coors.split(",")[0];
      var coorsLon = coors.split(",")[1];
      var etiqueta = "<a href='https://www.google.com/maps/search/" + coorsLat + "," + coorsLon + "' target='_blanck'>Aqui Esta La Renta<a>";

      var map = L.map('map_addRentas').setView([coorsLat, coorsLon], 13);
      L.tileLayer(mapTheme1, {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map);

      var popup = L.popup();

      // Codigo con pin personalizado
      var marker = L.marker([coorsLat, coorsLon])
        .bindPopup(etiqueta)
        .addTo(map);

      function onMapClick(e) {

        var rentaCoords = e.latlng;

        popup
          .setLatLng(e.latlng)
          .setContent("La Renta ahora se encuentra aqui<br>" + rentaCoords.lat + ", " + rentaCoords.lng)
          .openOn(map);


        document.getElementById("renta_coor").value = rentaCoords.lat + ", " + rentaCoords.lng;
      }
    }

    map.on('click', onMapClick);
  }

  if (document.querySelector('input[name="renta_stock"]')) {
    document.querySelectorAll('input[name="renta_stock"]').forEach((elem) => {
      elem.addEventListener("change", async function(event) {
        var item = event.target.value;
        const equipo_id = document.getElementById("equipo_id").value;
        var dataEquQRY = await QRYajax(0, "equipo_id_enc", equipo_id);

        // Validacion de equipo existente.
        if (dataEquQRY.Status) {
          dataEquQRY = dataEquQRY.Data[0];
        } else {
          return;
        }

        if (item == "true") {
          if (dataEquQRY.modelo_tipo == "Monocromatico") {
            var html = `
                          <input type="hidden" id="renta_stock_M" name="renta_stock_M" value="0">
                          <input type="hidden" id="renta_stock_Y" name="renta_stock_Y" value="0">
                          <input type="hidden" id="renta_stock_C" name="renta_stock_C" value="0">

                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_K">NEGRO</label>
                          <input type="number" class="form-control" id="renta_stock_K" name="renta_stock_K" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_R">RESIDUAL</label>
                          <input type="number" class="form-control" id="renta_stock_R" name="renta_stock_R" maxlength="1" value="0" pattern="[0-9]{1}">
                        </div>
                      </div>
                    </div>
                    `;
          } else {
            var html = `
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_K">NEGRO</label>
                          <input type="number" class="form-control" id="renta_stock_K" name="renta_stock_K" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_M">MAGENTA</label>
                          <input type="number" class="form-control" id="renta_stock_M" name="renta_stock_M" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_Y">AMARILLO</label>
                          <input type="number" class="form-control" id="renta_stock_Y" name="renta_stock_Y" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_C">AZUL</label>
                          <input type="number" class="form-control" id="renta_stock_C" name="renta_stock_C" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="renta_stock_R">RESIDUAL</label>
                          <input type="number" class="form-control" id="renta_stock_R" name="renta_stock_R" maxlength="1" value="1" pattern="[0-9]{1}">
                        </div>
                      </div>
                    </div>
                    `;
          }
        }
        if (item == "false") {
          var html = ``;
        }
        document.getElementById("renta_stock").innerHTML = html;
      });
    });
  }

  if (document.getElementById("equipo_id")) {
    $('#equipo_id').on('change', async function() {
      var equipoIDenc = $(this).val();
      var dataEquQRY = await QRYajax(0, "equipo_id_enc", equipoIDenc);

      // Validacion de equipo existente.
      if (dataEquQRY.Status) {
        dataEquQRY = dataEquQRY.Data[0];
      } else {
        Swal.fire({
          title: "Ocurrio un error.",
          text: "No existe el equipo.",
          icon: "error",
        });
        return;
      }

      if (dataEquQRY['modelo_tipo'] == "Monocromatico") {
        document.getElementById("DIV_renta_inc_col").style.display = "none";
        document.getElementById("DIV_renta_exc_col").style.display = "none";
        document.getElementById("col_lectura_col").style.display = "none";
        document.getElementById("col_equipo_nivel_K").style.display = "block";
        document.getElementById("col_equipo_nivel_M").style.display = "none";
        document.getElementById("col_equipo_nivel_C").style.display = "none";
        document.getElementById("col_equipo_nivel_Y").style.display = "none";
        document.getElementById("col_equipo_nivel_R").style.display = "block";
      } else {
        document.getElementById("DIV_renta_inc_col").style.display = "block";
        document.getElementById("DIV_renta_exc_col").style.display = "block";
        document.getElementById("col_lectura_col").style.display = "block";
        document.getElementById("col_equipo_nivel_K").style.display = "block";
        document.getElementById("col_equipo_nivel_M").style.display = "block";
        document.getElementById("col_equipo_nivel_C").style.display = "block";
        document.getElementById("col_equipo_nivel_Y").style.display = "block";
        document.getElementById("col_equipo_nivel_R").style.display = "block";
      }

      document.getElementById("dataStock").style.display = "block";
      document.getElementById("renta_stock").innerHTML = "";
      document.getElementById("stock1").checked = true;
      document.getElementById("stock2").checked = false;
    });
  }

  if (document.getElementById('lectura_formato')) {
    document.getElementById("lectura_formato").addEventListener("change", function() {
      if (document.getElementById("lectura_formato").checked) {
        document.getElementById('div_lectura_formato').innerHTML = `<input type="file" class="form-control" name="lectura_formato" accept="image/jpeg">`;
      } else {
        document.getElementById('div_lectura_formato').innerHTML = ``;
      }
    })
  }

  if (document.getElementById('lectura_estado')) {
    document.getElementById("lectura_estado").addEventListener("change", function() {
      if (document.getElementById("lectura_estado").checked) {
        document.getElementById('div_lectura_estado').innerHTML = `<input type="file" class="form-control" name="lectura_estado" accept="image/jpeg">`;
      } else {
        document.getElementById('div_lectura_estado').innerHTML = ``;
      }
    })
  }
</script>