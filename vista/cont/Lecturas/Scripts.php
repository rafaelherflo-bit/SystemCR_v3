<script>
  if (document.getElementById("btnFacturas")) {
    document.getElementById("btnFacturas").addEventListener("click", async function(e) {
      e.preventDefault();
      const anio = document.getElementById('custom_anio').value;
      const mes = document.getElementById('custom_mes').value;

      document.getElementById("modalTitleFull").innerText = "IMPRIMIR TODAS LAS LECTURAS";
      document.getElementById("modalBodyFull").innerHTML = `<embed src="${SERVERURL}vista/formats/printFacturasLecturas.php?anio=${anio}&mes=${mes}" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
      $("#modalFull").modal("show");

    })
  };

  if (document.getElementById("repExcRents")) {
    document.getElementById("repExcRents").addEventListener("click", async function(e) {
      e.preventDefault();
      document.getElementById("modalTitleFull").innerText = "REPORTE DE LECTURAS Y EXCEDENTES";
      document.getElementById("modalBodyFull").innerHTML =
        `<embed src="` + SERVERURL + `vista/formats/printRepLectsExcs.php" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
      $("#modalFull").modal("show");
    })
  }

  if (document.getElementById("btnPrintLectMonth")) {
    document.getElementById("btnPrintLectMonth").addEventListener("click", async function(e) {
      e.preventDefault();
      document.getElementById("modalTitleFull").innerText = "IMPRIMIR TODAS LAS LECTURAS";
      document.getElementById("modalBodyFull").innerHTML =
        `<embed src="` + SERVERURL + 'vista/formats/printLect_tipo6.php?year=' + document.getElementById("custom_anio").value + '&month=' + document.getElementById("custom_mes").value + '&zone=' + document.getElementById("printLectZona").value + `" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
      $("#modalFull").modal("show");
    })
  }

  if (document.getElementById("delLectPE")) {
    document.getElementById("delLectPE").addEventListener("click", async function(e) {
      e.preventDefault();
      const lectID = this.dataset.id;
      const lectArchivo = this.dataset.archivo;
      const lectFecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar Pagina de Estado?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delPEidEnc', lectID, lectFecha, lectArchivo);
          if (data.Status) {
            location.reload();
          } else {
            Swal.fire({
              title: "Ocurrio un error.",
              text: data.Data,
              icon: "error",
            });
          }
        }
      })
    })
  }

  if (document.getElementById("delLectFL")) {
    document.getElementById("delLectFL").addEventListener("click", async function(e) {
      e.preventDefault();
      const lectID = this.dataset.id;
      const lectArchivo = this.dataset.archivo;
      const lectFecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar Fromato de Lectura?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delFLidEnc', lectID, lectFecha, lectArchivo);
          if (data.Status) {
            location.reload();
          } else {
            Swal.fire({
              title: "Ocurrio un error.",
              text: data.Data,
              icon: "error",
            });
          }
        }
      })
    })
  }
  tablaDatos(<?= $DT_pageLength; ?>, "desc", 0);

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

  if (document.getElementById("btnMapaLecturas")) {
    document.getElementById("btnMapaLecturas").addEventListener("click", async function(e) {
      e.preventDefault();
      Swal.fire({
        title: "Mapa de Lecturas",
        text: document.getElementById("periodoCustom").value,
        icon: "info",
        confirmButtonText: 'Confirmar',
        confirmButtonColor: "#3085d6",
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: "#440d00"
      }).then(async function(result) {
        if (result.isConfirmed) {
          // window.location.href = SERVERURL + 'vista/formats/mapaLecturas.php?custom_mes=' + document.getElementById("custom_mes").value + '&custom_anio=' + document.getElementById("custom_anio").value;
          // window.location.href = SERVERURL + 'Lecturas/Mapa/;
          document.forms['formRedirect'].action = SERVERURL + "Lecturas/Mapa";
          var input = `
                      <input type='hidden' name='custom_mes' value='` + document.getElementById("custom_mes").value + `'>
                      <input type='hidden' name='custom_anio' value='` + document.getElementById("custom_anio").value + `'>
                      `;
          document.getElementById("formRedirect").innerHTML = input;
          document.forms["formRedirect"].submit();
        }
      });
    })
  };

  if (document.getElementById("map-template")) {
    async function lectMap() {

      const map = L.map("map-template").setView(
        [21.1674382097248, -86.86569297527858],
        12
      );

      map.locate({
        enableHighAccuracy: true
      });

      map.on("locationfound", (e) => {
        var myIcon = L.icon({
          iconUrl: "/vista/assets/icons/leaf-orange.png",
          shadowUrl: '/vista/assets/icons/leaf-shadow.png',
          iconSize: [38, 95], // size of the icon
          shadowSize: [50, 64], // size of the shadow
          iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
          shadowAnchor: [4, 62], // the same for the shadow
          popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
        });
        var marker = L.marker([e.latlng.lat, e.latlng.lng], {
            icon: myIcon
          })
          .bindPopup("Estoy aqui")
          .addTo(map);
      });

      L.tileLayer(mapTheme1).addTo(map);

      const sqlLecturasRentas = `SELECT renta_id, cliente_rs, renta_estado, renta_depto, renta_coor, ( SELECT lectura_fecha FROM Lecturas WHERE lectura_renta_id = renta_id AND MONTH (lectura_fecha) = ` +
        document.getElementById("custom_mes").value +
        ` AND YEAR (lectura_fecha) = ` +
        document.getElementById("custom_anio").value +
        `) AS lectura_fecha FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE renta_id IN ( SELECT lectura_renta_id FROM Lecturas ) AND renta_estado = 'Activo'`;

      const res = await fetch(SERVERURL + "/ajax/mapaLecturasFetchAjax.php", {
        method: "POST",
        body: JSON.stringify({
          sqlLecturasRentas,
        }),
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      });
      const rentaData = await res.json();

      for (var i = 0; i < rentaData.length; i++) {
        const ele = rentaData[i];
        var rentaCoords = ele.renta_coor;
        if (rentaCoords != 0) {

          // Codigo con pin personalizado
          if (ele.lectura_fecha === null) {
            var pin_status = "/vista/assets/icons/leaf-red.png";
          } else {
            var pin_status = "/vista/assets/icons/leaf-green.png";
          }
          var myIcon = L.icon({
            iconUrl: pin_status,
            shadowUrl: '/vista/assets/icons/leaf-shadow.png',
            iconSize: [38, 95], // size of the icon
            shadowSize: [50, 64], // size of the shadow
            iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
            shadowAnchor: [4, 62], // the same for the shadow
            popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
          });

          var rentaCoords = rentaCoords.replace(" ", "");
          rentaCoords = rentaCoords.split(",");

          // Codigo con pin personalizado
          var marker = L.marker([rentaCoords[0], rentaCoords[1]], {
              icon: myIcon
            })
            .bindPopup("<a href='https://www.google.com/maps/search/" + ele.renta_coor + "' target='_blanck'>" + ele.cliente_rs + " - " + ele.renta_depto + "<a>")
            .addTo(map);
        }
      }
    }
    lectMap();
  }



  if (document.getElementById("lectura_fecha_add")) {

    document.getElementById("lectura_fecha_add").addEventListener("change", async function(e) {
      var val = e.target.value;
      selectStatusRentLect(val);
    });

    selectStatusRentLect(document.getElementById("lectura_fecha_add").value);

    async function selectStatusRentLect(value) {
      var Result = await QRYajax(2, 'rentas_lecturas_exist', value);
      if (Result.Status) {
        var Data = Result.Data;
        var html =
          `<div class="form-group">
              <label for="lectura_renta_id" class="bmd-label-floating">RENTA</label>
              <select name="lectura_renta_id" id="lectura_renta_id" class="form-select" data-placeholder="Selecciona una Renta">
                <option></option>`;
        for (let i = 0; i < Data.length; i++) {
          if (Data[i]['lectura_status'] == true) {
            html += "<option value=" + Data[i]['renta_id'] + " disabled> ✓ " + Data[i]['renta_data'] + "</option>";
          } else {
            html += "<option value=" + Data[i]['renta_id'] + ">" + Data[i]['renta_data'] + "</option>";
          }
        }
        html += `
              </select>
            </div>`;

        document.getElementById("renta_lectura").innerHTML = html;
        select2();

        $("#lectura_renta_id").on("select2:select", async function(e) {
          var dataRenta = await consultaFetch('renta_id_enc', e.currentTarget.value);
          if (dataRenta.Estado) {
            dataRenta = dataRenta.Data[0];
            if (dataRenta['modelo_tipo'] == 'Monocromatico') {
              $("#col_renta_stock_M").css('display', 'none');
              $("#col_renta_stock_C").css('display', 'none');
              $("#col_renta_stock_Y").css('display', 'none');
              $("#col_equipo_nivel_M").css('display', 'none');
              $("#col_equipo_nivel_C").css('display', 'none');
              $("#col_equipo_nivel_Y").css('display', 'none');
              $("#col_lectura_col").css('display', 'none');
            } else {
              $("#col_renta_stock_M").css('display', 'block');
              $("#col_renta_stock_C").css('display', 'block');
              $("#col_renta_stock_Y").css('display', 'block');
              $("#col_equipo_nivel_M").css('display', 'block');
              $("#col_equipo_nivel_C").css('display', 'block');
              $("#col_equipo_nivel_Y").css('display', 'block');
              $("#col_lectura_col").css('display', 'block');
            }
            if (dataRenta['modelo_modelo'] == 'M2040dn/L' || dataRenta['modelo_modelo'] == 'M2035dn/L' || dataRenta['modelo_modelo'] == 'M5521cdn' || dataRenta['modelo_modelo'] == 'M5526cdw' || dataRenta['modelo_modelo'] == 'M5526cdn') {
              $("#col_renta_stock_R").css('display', 'none');
              $("#col_equipo_nivel_R").css('display', 'none');
            } else {
              $("#col_renta_stock_R").css('display', 'block');
              $("#col_equipo_nivel_R").css('display', 'block');
            }
            $("#renta_equipo").html("&nbsp; CONSUMIBLES PARA: " + dataRenta['modelo_linea'] + " " + dataRenta['modelo_modelo'] + " (" + dataRenta['equipo_serie'] + ")");
            $("#renta_stock_K").val(dataRenta['renta_stock_K']);
            $("#renta_stock_M").val(dataRenta['renta_stock_M']);
            $("#renta_stock_C").val(dataRenta['renta_stock_C']);
            $("#renta_stock_Y").val(dataRenta['renta_stock_Y']);
            $("#renta_stock_R").val(dataRenta['renta_stock_R']);
            $("#equipo_nivel_K").val(dataRenta['equipo_nivel_K']);
            $("#equipo_nivel_M").val(dataRenta['equipo_nivel_M']);
            $("#equipo_nivel_C").val(dataRenta['equipo_nivel_C']);
            $("#equipo_nivel_Y").val(dataRenta['equipo_nivel_Y']);
            $("#equipo_nivel_R").val(dataRenta['equipo_nivel_R']);
          } else {
            Swal.fire({
              title: "Ocurrio un error.",
              text: "No existe la Renta.",
              icon: "error",
            });
          }
        });

      } else {
        window.location.reload()
      }
    }

  }
</script>