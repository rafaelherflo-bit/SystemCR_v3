<script>
  tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", <?= $DT_orderCol; ?>);

  if (document.getElementById("delRepPDF")) {
    document.getElementById("delRepPDF").addEventListener("click", async function(e) {
      e.preventDefault();
      const repID = this.dataset.id;
      const repArchivo = this.dataset.archivo;
      const repFecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar PDF?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delRepPDF', repID, repFecha, repArchivo);
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

  if (document.querySelector(".btnRepAction")) {
    document.querySelectorAll('.btnRepAction').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        const eleTipo = elem.dataset.tipo;
        const eleId = elem.dataset.id;

        // ~-~-~-~    TIPOS    ~-~-~-~ //
        // 0    ----    ELIMINAR
        // 1    ----    EDITAR
        // 2    ----    COMPLETAR
        // ~-~-~-~~-~-~-~~-~-~-~~-~-~ //

        if (eleTipo == 0) {
          Swal.fire({
            title: "Deseas Eliminar el Registro?",
            text: "",
            icon: "warning",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              const deleteReg = await QRYajax(1, "delRepAct", eleId);
              if (deleteReg.Status) {
                location.reload();
              } else {
                console.log(deleteReg.Data);
              }
            }
          })
        } else if (eleTipo == 1) {
          Swal.fire({
            title: "Deseas Editar el Registro?",
            text: "",
            icon: "info",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              location.href = SERVERURL + "ReportesR/idRAedit/" + eleId;
            }
          })
        } else if (eleTipo == 2) {
          Swal.fire({
            title: "Deseas Completar el Registro?",
            text: "",
            icon: "info",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              location.href = SERVERURL + "ReportesR/idRC/" + eleId;
            }
          })
        } else if (eleTipo == 3) {
          Swal.fire({
            title: "Imprimir Reporte",
            text: "",
            icon: "info",
            confirmButtonText: 'Continuar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              var mapForm = document.createElement("form");
              mapForm.target = "_blank";
              mapForm.method = "POST";
              mapForm.action = SERVERURL + "vista/formats/printRepActivo.php";
              var input = document.createElement("input");
              input.type = "hidden";
              input.name = "reporte_id";
              input.value = eleId;
              mapForm.appendChild(input);
              document.body.appendChild(mapForm);
              mapForm.submit();
            }
          })
        }
      })
    })
  }

  if (document.querySelector(".btnRepComp")) {
    document.querySelectorAll('.btnRepComp').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        const eleTipo = elem.dataset.tipo;
        const eleID = elem.dataset.id;
        if (eleTipo == 0) {

          // ~-~-~-~    TIPOS    ~-~-~-~ //
          // 0    ----    EDITAR
          // 1    ----    PDF
          // ~-~-~-~~-~-~-~~-~-~-~~-~-~ //
          Swal.fire({
            title: "Deseas Editar el Registro?",
            text: "",
            icon: "info",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              location.href = SERVERURL + "ReportesR/idRCedit/" + eleID;
            }
          })

        } else if (eleTipo == 1) {
          var data = await QRYajax(0, 'reporte_id_enc', eleID);
          if (data.Status) {
            data = data.Data[0];
            if (data['reporte_archivo'] == "") {
              Swal.fire({
                title: "Ocurrio un error.",
                text: "No existe el PDF.",
                icon: "error",
              });
            } else {
              const reporte_fecha = data['reporte_fecha'].split('-');
              const titleModal = data['cliente_rs'] + " (" + data['contrato_folio'] + "-" + data['renta_folio'] + " - " + data['renta_depto'] + ") | Fecha de Reporte: " + data['reporte_fecha'];
              const url = SERVERURL + 'DocsCR/ReportesCR/' + reporte_fecha[0] + '/' + reporte_fecha[1] + '/' + data['reporte_archivo'];
              const embed = `<embed src="` + url + `" height="100%" width="100%" >`;
              document.getElementById("modalTitleFull").innerText = titleModal;
              document.getElementById("modalBodyFull").innerHTML = embed;
              $('#modalFull').modal('show');
            }
          } else {
            Swal.fire({
              title: "Ocurrio un error.",
              text: "No existe el reporte.",
              icon: "error",
            });
          }
        }
      });
    });
  }

  $("#reporte_renta_id").on("select2:select", async function(e) {
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
      } else {
        $("#col_renta_stock_M").css('display', 'block');
        $("#col_renta_stock_C").css('display', 'block');
        $("#col_renta_stock_Y").css('display', 'block');
        $("#col_equipo_nivel_M").css('display', 'block');
        $("#col_equipo_nivel_C").css('display', 'block');
        $("#col_equipo_nivel_Y").css('display', 'block');
      }
      if (dataRenta['modelo_modelo'] == 'M2040dn/L' || dataRenta['modelo_modelo'] == 'M2035dn/L' || dataRenta['modelo_modelo'] == 'M5521cdn' || dataRenta['modelo_modelo'] == 'M5526cdw' || dataRenta['modelo_modelo'] == 'M5526cdn' || dataRenta['modelo_modelo'] == 'M5526cdw') {
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

  if (document.querySelector(".zona_id")) {
    document.querySelectorAll('.zona_id').forEach((elem) => {
      elem.addEventListener("click", async function(event) {

        const zona_id = elem.id;
        const tipo = elem.dataset.tipo;

        var dataRentasZonas = await consultaFetch('zona_id', zona_id);
        zona_nombre = dataRentasZonas.Data[0].zona_nombre;
        var titleModal = "Cantidad de Reportes de " + zona_nombre + " | " + document.getElementById('periodoCustom').value;

        var content = `
                    <div class="table-responsive">
                        <table class="table table-dark table-sm">
                            <thead>
                            <tr>
                                <th>FOLIO</th>
                                <th>DEPARTAMENTO</th>
                                <th>RAZON SOCIAL</th>
                                <th>RFC</th>
                                <th>CANTIDAD</th>
                            </tr>
                            </thead>
                            <tbody>
                    `;


        var dataRentasZonas = await consultaFetch('rentasZona', zona_id);
        dataRentasZonas = dataRentasZonas.Data;
        for (var i = 0; i < dataRentasZonas.length; i++) {
          var rentaZona = dataRentasZonas[i];
          
          if (tipo == 1) {
            var custom_anio = elem.dataset.anio;
            var custom_mes = elem.dataset.mes;
            var custom_dia = elem.dataset.dia;
            var dataRepRentaZona = await QRYajax(0, "zonaRepotesDia", rentaZona.renta_id, custom_anio, custom_mes, custom_dia);
          } else if (tipo == 2) {
            var custom_anio = elem.dataset.anio;
            var custom_mes = elem.dataset.mes;
            var dataRepRentaZona = await QRYajax(0, "zonaRepotesMes", rentaZona.renta_id, custom_anio, custom_mes);
          } else if (tipo == 3) {
            var custom_anio = elem.dataset.anio;
            var dataRepRentaZona = await QRYajax(0, "zonaRepotesAnio", rentaZona.renta_id, custom_anio);
          }
          // var dataRepRentaZona = await consultaFetch2('rentasRepZona', "1", rentaZona.renta_id, custom_mes, custom_anio);
          content += (dataRepRentaZona['Data'].length > 0) ? `<tr class="table-info">` : `<tr>`;

          content += `
                                    <td>` + rentaZona.contrato_folio + `-` + rentaZona.renta_folio + `</td>
                                    <td>` + rentaZona.renta_depto + `</td>
                                    <td>` + rentaZona.cliente_rs + `</td>
                                    <td>` + rentaZona.cliente_rfc + `</td>
                                    <td>` + dataRepRentaZona['Data'].length + `</td>
                                </tr>
                                `;
        }

        content += `
                            </tbody>
                        </table>
                    </div>
                    `;

        document.getElementById("modalTitleFull").innerText = titleModal;
        document.getElementById("modalBodyFull").innerHTML = content;
        $('#modalFull').modal('show');

      });
    });
  }

  if (document.getElementById("reporte_fecha_inicio")) {
    document.getElementById("reporte_fecha_inicio").addEventListener("change", function() {
      document.getElementById("reporte_fecha_fin").value = document.getElementById("reporte_fecha_inicio").value;
    })
  }

  if (document.getElementById("reporte_fecha")) {
    document.getElementById("reporte_fecha").addEventListener("change", function() {
      document.getElementById("reporte_fecha_inicio").value = document.getElementById("reporte_fecha").value;
      document.getElementById("reporte_fecha_fin").value = document.getElementById("reporte_fecha").value;
    })
  }
</script>