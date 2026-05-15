<script>
  tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", <?= $DT_orderCol; ?>);
  // Borrar PDF de reporte FORANEO en Editor de Registro
  if (document.getElementById("delRepFPDF")) {
    document.getElementById("delRepFPDF").addEventListener("click", async function(e) {
      e.preventDefault();
      const repID = this.dataset.id;
      const repArchivo = this.dataset.archivo;
      const repFecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar PDF de Reporte Foraneo?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delRepFPDF', repID, repFecha, repArchivo);
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

  // Borrar PDF de reporte FORANEO en Editor de Registro
  if (document.querySelector(".btnRepFComp")) {
    document.querySelectorAll('.btnRepFComp').forEach((elem) => {
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
              location.href = SERVERURL + "ReportesF/idRFCedit/" + eleID;
            }
          })

        } else if (eleTipo == 1) {
          var data = await QRYajax(0, 'reporteF_id_enc', eleID);
          if (data.Status) {
            data = data.Data[0];
            // if (data['reporte_archivo'] == "") {
            //   Swal.fire({
            //     title: "Ocurrio un error.",
            //     text: "No existe el PDF.",
            //     icon: "error",
            //   });
            // } else {
            const reporteF_fecha = data['reporteF_fecha'].split('-');
            const titleModal = data['cliente_rs'] + " (" + data['cliente_rfc'] + ") | Fecha de Reporte: " + data['reporteF_fecha'];
            const url = SERVERURL + 'DocsCR/ReportesF/' + reporteF_fecha[0] + '/' + reporteF_fecha[1] + '/' + data['reporteF_folio'] + ".pdf";
            const embed = `<embed src="` + url + `" height="100%" width="100%" >`;
            document.getElementById("modalTitleFull").innerText = titleModal;
            document.getElementById("modalBodyFull").innerHTML = embed;
            $('#modalFull').modal('show');
            // }
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

  // $("#reporteF_equ_modelo_id").on("select2:select", async function(e) {
  //   var dataModelo = await QRYajax(0, 'modelo_id_enc', e.currentTarget.value);
  //   if (dataModelo.Status) {
  //     dataModelo = dataModelo.Data[0];
  //     if (dataModelo['modelo_tipo'] == 'Monocromatico') {
  //       // Contadores
  //       //------ INICIAL
  //       $("#DIV_reporteF_col_ini").css('display', 'none');
  //       //------ FINAL
  //       $("#DIV_reporteF_col_fin").css('display', 'none');


  //       // Niveles de TONER
  //       //------ INICIAL
  //       $("#DIV_reporteF_nivelM_ini").css('display', 'none');
  //       $("#DIV_reporteF_nivelC_ini").css('display', 'none');
  //       $("#DIV_reporteF_nivelY_ini").css('display', 'none');
  //       //------ FINAL
  //       $("#DIV_reporteF_nivelM_fin").css('display', 'none');
  //       $("#DIV_reporteF_nivelC_fin").css('display', 'none');
  //       $("#DIV_reporteF_nivelY_fin").css('display', 'none');
  //     } else {
  //       // Contadores
  //       //------ INICIAL
  //       $("#DIV_reporteF_col_ini").css('display', 'block');
  //       //------ FINAL
  //       $("#DIV_reporteF_col_fin").css('display', 'block');


  //       // Niveles de TONER
  //       //------ INICIAL
  //       $("#DIV_reporteF_nivelM_ini").css('display', 'block');
  //       $("#DIV_reporteF_nivelC_ini").css('display', 'block');
  //       $("#DIV_reporteF_nivelY_ini").css('display', 'block');
  //       //------ FINAL
  //       $("#DIV_reporteF_nivelM_fin").css('display', 'block');
  //       $("#DIV_reporteF_nivelC_fin").css('display', 'block');
  //       $("#DIV_reporteF_nivelY_fin").css('display', 'block');
  //     }

  //     // RESIDUAL
  //     if (dataModelo['modelo_modelo'] == 'M2040dn/L' || dataModelo['modelo_modelo'] == 'M2035dn/L' || dataModelo['modelo_modelo'] == 'M5521cdn' || dataModelo['modelo_modelo'] == 'M5526cdw' || dataModelo['modelo_modelo'] == 'M5526cdn' || dataModelo['modelo_modelo'] == 'M5526cdw') {
  //       $("#DIV_reporteF_nivelR_ini").css('display', 'none');
  //       $("#DIV_reporteF_nivelR_fin").css('display', 'none');
  //     } else {
  //       $("#DIV_reporteF_nivelR_ini").css('display', 'block');
  //       $("#DIV_reporteF_nivelR_fin").css('display', 'block');
  //     }

  //   } else {
  //     Swal.fire({
  //       title: "Ocurrio un error.",
  //       text: "No existe el modelo seleccionado.",
  //       icon: "error",
  //     });
  //   }
  // });

  $("#reporteF_equ_modelo_id").on("select2:select", async function(e) {
    var dataModelo = await QRYajax(0, 'modelo_id_enc', e.currentTarget.value);
    if (dataModelo.Status) {
      dataModelo = dataModelo.Data[0];
      if (dataModelo['modelo_tipo'] == 'Monocromatico') {
        // Contadores
        //------ INICIAL
        document.getElementById("DIV_reporteF_col_ini").style.display = "none";
        document.getElementById("reporteF_col_ini").value = 0;
        //------ FINAL
        document.getElementById("DIV_reporteF_col_fin").style.display = "none";
        document.getElementById("reporteF_col_fin").value = 0;


        // Niveles de TONER
        //------ INICIAL
        document.getElementById("DIV_reporteF_nivelM_ini").style.display = "none";
        document.getElementById("reporteF_nivelM_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelC_ini").style.display = "none";
        document.getElementById("reporteF_nivelC_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelY_ini").style.display = "none";
        document.getElementById("reporteF_nivelY_ini").value = 0;
        //------ FINAL
        document.getElementById("DIV_reporteF_nivelM_fin").style.display = "none";
        document.getElementById("reporteF_nivelM_fin").value = 0;
        document.getElementById("DIV_reporteF_nivelC_fin").style.display = "none";
        document.getElementById("reporteF_nivelC_fin").value = 0;
        document.getElementById("DIV_reporteF_nivelY_fin").style.display = "none";
        document.getElementById("reporteF_nivelY_fin").value = 0;
      } else {
        // Contadores
        //------ INICIAL
        document.getElementById("DIV_reporteF_col_ini").style.display = "block";
        document.getElementById("reporteF_col_ini").value = 0;
        //------ FINAL
        document.getElementById("DIV_reporteF_col_fin").style.display = "block";
        document.getElementById("reporteF_col_fin").value = 0;


        // Niveles de TONER
        //------ INICIAL
        document.getElementById("DIV_reporteF_nivelM_ini").style.display = "block";
        document.getElementById("reporteF_nivelM_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelC_ini").style.display = "block";
        document.getElementById("reporteF_nivelC_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelY_ini").style.display = "block";
        document.getElementById("reporteF_nivelY_ini").value = 0;
        //------ FINAL
        document.getElementById("DIV_reporteF_nivelM_fin").style.display = "block";
        document.getElementById("reporteF_nivelM_fin").value = 0;
        document.getElementById("DIV_reporteF_nivelC_fin").style.display = "block";
        document.getElementById("reporteF_nivelC_fin").value = 0;
        document.getElementById("DIV_reporteF_nivelY_fin").style.display = "block";
        document.getElementById("reporteF_nivelY_fin").value = 0;
      }

      // RESIDUAL
      if (dataModelo['modelo_modelo'] == 'M2040dn/L' || dataModelo['modelo_modelo'] == 'M2035dn/L' || dataModelo['modelo_modelo'] == 'M5521cdn' || dataModelo['modelo_modelo'] == 'M5526cdw' || dataModelo['modelo_modelo'] == 'M5526cdn' || dataModelo['modelo_modelo'] == 'M5526cdw') {
        document.getElementById("DIV_reporteF_nivelR_ini").style.display = "none";
        document.getElementById("reporteF_nivelR_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelR_fin").style.display = "none";
        document.getElementById("reporteF_nivelR_fin").value = 0;
      } else {
        document.getElementById("DIV_reporteF_nivelR_ini").style.display = "block";
        document.getElementById("reporteF_nivelR_ini").value = 0;
        document.getElementById("DIV_reporteF_nivelR_fin").style.display = "block";
        document.getElementById("reporteF_nivelR_fin").value = 0;
      }

    } else {
      Swal.fire({
        title: "Ocurrio un error.",
        text: "No existe el modelo seleccionado.",
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

          console.log(tipo);
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

  if (document.getElementById("reporteF_fecha_inicio")) {
    document.getElementById("reporteF_fecha_inicio").addEventListener("change", function() {
      document.getElementById("reporteF_fecha_fin").value = document.getElementById("reporteF_fecha_inicio").value;
    })
  }
</script>