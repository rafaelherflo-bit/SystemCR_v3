<script>
  tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);

  // ----------------------------------  SECCION REGISTROS DE SALIDA
  // °°°°°°°°°°°°°°°°°° Editar Registro de Salida 
  // Boton editar registro de Salida //
  if (document.querySelector('.btn-edit-RST')) {
    document.querySelectorAll('.btn-edit-RST').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        location.href = SERVERURL + "Toners/RST/" + elem.id;
      })
    })
  }
  // °°°°°°°°°°°°°°°°°° Opciones de retiro en modulo de retiro de toner 
  // 
  if (document.querySelector('input[name="tipo-outTalm"]')) {
    document.querySelectorAll('input[name="tipo-outTalm"]').forEach((elem) => {
      elem.addEventListener("change", async function(event) {
        var item = event.target.value;
        if (item == "Venta") {
          var html = `
                    <div class="row">
                      <div class="col-12 col-md-12">
                        <div class="form-group">
                            <input type="hidden" id="tipo-outTalm" value="` + item + `">
                            <select class="form-select" id="identificador-outTalm" data-placeholder="Selecciona un Cliente">
                                <option></option>
                                <?php
                                $sql = 'SELECT * FROM Clientes';
                                $query = consultaData($sql);
                                $dataTon = $query['dataFetch'];
                                foreach ($dataTon as $dato) { ?>
                                    <option value="<?= encryption($dato['cliente_id']); ?>"><?= "(" . $dato['cliente_rfc'] . ") " . $dato['cliente_rs']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>
                    </div>
                    `;
        }
        if (item == "Renta") {
          // var codigoID = document.getElementById("tonerRO_toner_id");
          // tonerCodigo = await consultaFetch('toner_id', codigoID.value);
          // console.log(tonerCodigo['Data'][0]);
          var html = `
                    <div class="row">
                      <div class="col-12 col-md-12">
                        <div class="form-group">
                          <input type="hidden" id="tipo-outTalm" value="` + item + `">
                          <select class="form-select" id="identificador-outTalm" data-placeholder="Selecciona una Renta">
                              <option></option>
                              <?php
                              $sql = 'SELECT * FROM Rentas
                              INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                              INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                              WHERE renta_estado = "Activo"';
                              $query = consultaData($sql);
                              $dataTon = $query['dataFetch'];
                              foreach ($dataTon as $dato) { ?>
                                  <option value="<?= encryption($dato['renta_id']); ?>"><?= "(" . $dato['contrato_folio'] . "-" . $dato['renta_folio'] . ") - " . $dato['cliente_rs'] . " | " . $dato['renta_depto']; ?></option>
                              <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    `;
        }
        if (item == "Interno") {
          var html = `
                      <input type="hidden" id="tipo-outTalm" value="` + item + `">
                      <input type="hidden" id="identificador-outTalm" value="0">
                    `;
        }
        document.getElementById("divContTipo").innerHTML = html;
        select2();
      });
    });
  }
  // ------ FIN ------

  // °°°°°°°°°°°°°°°°°° Agregar Card y datos al JSON para agregar nuevo registro de Salida.
  if (document.getElementById("btn-outTalm")) {
    var arrayOutTalm = [];
    var idOutTalm = 0;

    document.getElementById("btn-outTalm").addEventListener("click", async function(e) {
      e.preventDefault();

      // INICIO ----------------------- INICIO //
      var fechaOutTalm = document.getElementById("fecha-outTalm").value;
      var tonerIDOutTalm = $('#toner_id-outTalm').val();
      var cantOutTalm = document.getElementById("cant-outTalm").value;
      var commOutTalm = document.getElementById("comm-outTalm").value;
      var tipoOutTalm = document.getElementById("tipo-outTalm").value;
      var empOutTalm = document.getElementById("emp-outTalm").value;
      var identOutTalm = $('#identificador-outTalm').val();
      // var RST_file = document.getElementById('RST_file');


      // if (RST_file.files && RST_file.files[0]) {
      //   RST_file = RST_file.files[0];
      //   console.log(RST_file);
      // } else {
      //   Swal.fire({
      //     title: "Error",
      //     text: "No hay una evidencia seleccionada.",
      //     icon: "error",
      //     confirmButtonText: 'Aceptar'
      //   });
      //   return;
      // }


      if (fechaOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar fecha",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        console.log("Fecha: " + fechaOutTalm);
      }

      if (tonerIDOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Selecciona un codigo",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        var tonerStockCheck = await QRYajax(2, 'tonerStockCheckEnc', tonerIDOutTalm);
        var tonerData = await QRYajax(0, 'toner_id_enc', tonerIDOutTalm);
        if (tonerStockCheck.Estado) {
          Swal.fire({
            title: "Error",
            text: "No hay Stock en Almacen para este Toner",
            icon: "error",
            confirmButtonText: 'Aceptar'
          });
          return;
        } else {
          console.log("Toner ID: " + tonerIDOutTalm);
          console.log("Toner Stock: " + tonerStockCheck.Data);
        }
      }

      if (arrayOutTalm.length > 0) {
        for (let i = 0; i < arrayOutTalm.length; i++) {
          var array = arrayOutTalm[i];
          if (array.tonerRO_toner_id == tonerIDOutTalm) {
            Swal.fire({
              title: "Error",
              text: "Ya existe una salida agregada para este toner",
              icon: "error",
              confirmButtonText: 'Aceptar'
            });
            return;
          }
        }
      } else {
        idOutTalm = 0;
      }

      if (cantOutTalm == 0) {
        Swal.fire({
          title: "Error",
          text: "Falta agregar cantidad",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (cantOutTalm < 0) {
        Swal.fire({
          title: "Error",
          text: "Cantidad incorrecta",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (cantOutTalm > tonerStockCheck.Data) {
        Swal.fire({
          title: "Error",
          text: "La cantidad solicitada supera el Stock en Almacen",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (tonerData.Estado) {
        Swal.fire({
          title: "Error",
          text: "No exite el toner solicitado",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (tonerData.Data[0]['toner_estado'] == "Inactivo") {
        Swal.fire({
          title: "Error",
          text: "El toner solicitado se encuentra inactivo",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        idOutTalm++;
        tonerData = tonerData.Data[0];
        console.log("Cantidad: " + cantOutTalm);
      }

      if (tipoOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar Tipo de Salida",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        console.log(tipoOutTalm);
      }

      if (identOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar Identificador",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        console.log("Identificador: " + identOutTalm);
      }


      if (empOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar Empleado",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        console.log("Empleado: " + empOutTalm);
      }

      if (commOutTalm == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar Comentario",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        console.log("Comentarios: " + commOutTalm);
      }

      var innerOutTalm = {
        idOutTalm,
        // RST_file,
        tonerRO_toner_id: tonerIDOutTalm,
        tonerRO_fecha: fechaOutTalm,
        tonerRO_cantidad: cantOutTalm,
        tonerRO_comm: commOutTalm,
        tonerRO_tipo: tipoOutTalm,
        tonerRO_empleado: empOutTalm,
        tonerRO_identificador: identOutTalm,
      };

      arrayOutTalm.push(innerOutTalm);

      // Crea un nuevo elemento div Tipo Col
      const divCol = document.createElement("div");
      divCol.classList.add("col");
      divCol.setAttribute("id", "divCol" + idOutTalm);
      // Agregando HTML con DivCARD al DivCol
      var divCardHTML = `
                        <div class="card" style="width: 18rem;">
                          <div class="card-header">
                            <button class="btnDel-OutTalm btn btn-danger" value="` + idOutTalm + `" onclick="trDelOutTalm(event)">Eliminar</button>
                          </div>
                          <ul class="list-group list-group-flush">
                            <p><b>Fecha</b>: ` + fechaOutTalm + `</p>
                            <p><b>` + tonerData.toner_codigo + ` | ` + tonerData.toner_comp + `</b></p>
                            <p><b>Cantidad</b>: ` + cantOutTalm + `</p>
                            <p><b>Tipo</b>: ` + tipoOutTalm + `</p>
                            <p><b>Empleado</b>: ` + empOutTalm + `</p>
                            <p><b>Comentario</b>: ` + commOutTalm + `</p>
                          </ul>
                        </div>
                        `;
      divCol.innerHTML = divCardHTML;

      // Agregando Div Class CARD
      document.getElementById('divRowOutTalm').appendChild(divCol);

      // Reseteando formulario
      document.getElementById("fecha-outTalm").value = fechaOutTalm;
      $('.form-select').val(null).trigger('change');
      document.getElementById("cant-outTalm").value = "";
      document.getElementById("comm-outTalm").value = "";

      console.table(arrayOutTalm);



      //  FIN -------------------------- FIN  //
      document.getElementById("fin-outTalm").style.display = "block";

    });

    function trDelOutTalm(event) {
      event.preventDefault();
      const id = event.target.value;
      document.getElementById("divCol" + id).remove();

      arrayOutTalm = arrayOutTalm.filter(item => item.idOutTalm != id)
      console.log(arrayOutTalm);
    }



    // Inicio ---- Boton Cancelar Todo
    document.getElementById("btnCancel-outTalm").addEventListener("click", function() {
      Swal.fire({
        title: "Espera.",
        text: "Deseas cancelar todo??.",
        icon: "info",
        confirmButtonText: 'Si',
        confirmButtonColor: "#008341",
        showCancelButton: true,
        cancelButtonText: 'No',
        cancelButtonColor: "#440d00"
      }).then(async function(res) {
        if (res.isConfirmed) {
          window.location.reload();
        }
      });
    });
    // FIN ---- Boton Cancelar Todo

    // Inicio ---- Boton Finalizar Todo
    document.getElementById("btnSuccess-outTalm").addEventListener("click", function() {
      Swal.fire({
        title: "Confirmacion.",
        text: "Agregar los registros??.",
        icon: "info",
        confirmButtonText: 'Agregar',
        confirmButtonColor: "#008341",
        showCancelButton: true,
        cancelButtonText: 'Aun no',
        cancelButtonColor: "#440d00"
      }).then(async function(res) {
        if (res.isConfirmed) {

          var envio = 1;

          if (arrayOutTalm.length > 0) {

            // function valAppendChild(nam, val) {
            //   var inputAppCh = document.createElement("input");
            //   inputAppCh.type = "hidden";
            //   inputAppCh.enctype = "multipart/form-data";
            //   inputAppCh.name = nam;
            //   inputAppCh.value = val;
            //   mapForm.appendChild(inputAppCh);
            // }



            // for (let i = 0; i < arrayOutTalm.length; i++) {

            //   const RST_file = arrayOutTalm[i].RST_file;
            //   const tonerRO_fecha = arrayOutTalm[i].tonerRO_fecha;
            //   const tonerRO_toner_id = arrayOutTalm[i].tonerRO_toner_id;
            //   const tonerRO_cantidad = arrayOutTalm[i].tonerRO_cantidad;
            //   const tonerRO_comm = arrayOutTalm[i].tonerRO_comm;
            //   const tonerRO_tipo = arrayOutTalm[i].tonerRO_tipo;
            //   const tonerRO_empleado = arrayOutTalm[i].tonerRO_empleado;
            //   const tonerRO_identificador = arrayOutTalm[i].tonerRO_identificador;

            //   var mapForm = document.createElement("form");
            //   mapForm.method = "POST";
            //   mapForm.action = SERVERURL + "ajax/outTalmFetchAjax.php";

            //   valAppendChild("RST_file", RST_file);
            //   valAppendChild("tonerRO_fecha", tonerRO_fecha);
            //   valAppendChild("tonerRO_toner_id", tonerRO_toner_id);
            //   valAppendChild("tonerRO_cantidad", tonerRO_cantidad);
            //   valAppendChild("tonerRO_comm", tonerRO_comm);
            //   valAppendChild("tonerRO_tipo", tonerRO_tipo);
            //   valAppendChild("tonerRO_empleado", tonerRO_empleado);
            //   valAppendChild("tonerRO_identificador", tonerRO_identificador);

            //   document.body.appendChild(mapForm);
            //   mapForm.submit();
            //   envio++
            // }



            // for (let i = 0; i < arrayOutTalm.length; i++) {
            //   const formData = new FormData();

            //   const RST_file = arrayOutTalm[i].RST_file;
            //   const tonerRO_fecha = arrayOutTalm[i].tonerRO_fecha;
            //   const tonerRO_toner_id = arrayOutTalm[i].tonerRO_toner_id;
            //   const tonerRO_cantidad = arrayOutTalm[i].tonerRO_cantidad;
            //   const tonerRO_comm = arrayOutTalm[i].tonerRO_comm;
            //   const tonerRO_tipo = arrayOutTalm[i].tonerRO_tipo;
            //   const tonerRO_empleado = arrayOutTalm[i].tonerRO_empleado;
            //   const tonerRO_identificador = arrayOutTalm[i].tonerRO_identificador;

            //   // Usamos un nombre único para cada archivo y dato si envías múltiples elementos
            //   formData.append(`RST_file`, RST_file);
            //   formData.append(`tonerRO_fecha`, tonerRO_fecha);
            //   formData.append(`tonerRO_toner_id`, tonerRO_fecha);
            //   formData.append(`tonerRO_cantidad`, tonerRO_cantidad);
            //   formData.append(`tonerRO_comm`, tonerRO_comm);
            //   formData.append(`tonerRO_tipo`, tonerRO_tipo);
            //   formData.append(`tonerRO_empleado`, tonerRO_empleado);
            //   formData.append(`tonerRO_identificador`, tonerRO_identificador);


            //   const res = await fetch(SERVERURL + "ajax/test.php", {
            //     method: 'POST',
            //     body: formData
            //   });
            //   const data = await res.json();
            //   if (data.Status) {
            //     console.log(data.Data)
            //     // console.log("Envio No.:" + envio + ", " + data.Result);
            //   } else {
            //     console.log("Envio No.:" + envio + " FALLIDO, " + data.Result);
            //     Swal.fire({
            //       title: "Error",
            //       text: "Envio No.:" + envio + " FALLIDO, " + data.Result,
            //       icon: "error",
            //       confirmButtonText: 'Aceptar'
            //     });
            //     break;
            //     return;
            //   }
            //   envio++
            // }

            let i = 0;
            do {
              var tonerRO_fecha = arrayOutTalm[i].tonerRO_fecha;
              var tonerRO_toner_id = arrayOutTalm[i].tonerRO_toner_id;
              var tonerRO_cantidad = arrayOutTalm[i].tonerRO_cantidad;
              var tonerRO_comm = arrayOutTalm[i].tonerRO_comm;
              var tonerRO_tipo = arrayOutTalm[i].tonerRO_tipo;
              var tonerRO_empleado = arrayOutTalm[i].tonerRO_empleado;
              var tonerRO_identificador = arrayOutTalm[i].tonerRO_identificador;

              const res = await fetch(SERVERURL + "ajax/outTalmFetchAjax.php", {
                method: "POST",
                body: JSON.stringify({
                  tonerRO_fecha,
                  tonerRO_toner_id,
                  tonerRO_cantidad,
                  tonerRO_comm,
                  tonerRO_tipo,
                  tonerRO_empleado,
                  tonerRO_identificador,
                }),
                headers: {
                  Accept: "application/json",
                  "Content-Type": "application/json",
                },
              });
              const data = await res.json();
              if (data.Status) {
                console.log("Envio No.:" + envio + ", " + data.Result);
              } else {
                console.log("Envio No.:" + envio + " FALLIDO, " + data.Result);
                Swal.fire({
                  title: "Error",
                  text: "Envio No.:" + envio + " FALLIDO, " + data.Result,
                  icon: "error",
                  confirmButtonText: 'Aceptar'
                });
                break;
                return;
              }
              envio++

              i++
            } while (i < arrayOutTalm.length);

            console.log(i);

            if (i == arrayOutTalm.length) {
              Swal.fire({
                title: "Exito",
                text: "Salidas Agregadas Correctamente!",
                icon: "success",
                confirmButtonText: 'OK !!',
                confirmButtonColor: "#3085d6"
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            }


            // for (let i = 0; i < arrayOutTalm.length; i++) {
            //   var tonerRO_fecha = arrayOutTalm[i].tonerRO_fecha;
            //   var tonerRO_toner_id = arrayOutTalm[i].tonerRO_toner_id;
            //   var tonerRO_cantidad = arrayOutTalm[i].tonerRO_cantidad;
            //   var tonerRO_comm = arrayOutTalm[i].tonerRO_comm;
            //   var tonerRO_tipo = arrayOutTalm[i].tonerRO_tipo;
            //   var tonerRO_empleado = arrayOutTalm[i].tonerRO_empleado;
            //   var tonerRO_identificador = arrayOutTalm[i].tonerRO_identificador;

            //   const res = await fetch(SERVERURL + "ajax/outTalmFetchAjax.php", {
            //     method: "POST",
            //     body: JSON.stringify({
            //       tonerRO_fecha,
            //       tonerRO_toner_id,
            //       tonerRO_cantidad,
            //       tonerRO_comm,
            //       tonerRO_tipo,
            //       tonerRO_empleado,
            //       tonerRO_identificador,
            //     }),
            //     headers: {
            //       Accept: "application/json",
            //       "Content-Type": "application/json",
            //     },
            //   });
            //   const data = await res.json();
            //   if (data.Status) {
            //     console.log("Envio No.:" + envio + ", " + data.Result);
            //   } else {
            //     console.log("Envio No.:" + envio + " FALLIDO, " + data.Result);
            //     Swal.fire({
            //       title: "Error",
            //       text: "Envio No.:" + envio + " FALLIDO, " + data.Result,
            //       icon: "error",
            //       confirmButtonText: 'Aceptar'
            //     });
            //     break;
            //     return;
            //   }
            //   envio++
            // }


          } else {
            Swal.fire({
              title: "Error",
              text: "No hay datos que agregar",
              icon: "error",
              confirmButtonText: 'Aceptar'
            });
            return;
          }
        }
      });
    });
    // FIN ---- Boton Finalizar Todo
  }
  // ------ FIN ------

  // ----------------------------------  SECCION REGISTRO DE SALIDA (RST)
  // °°°°°°°°°°°°°°°°°° Modificador de DIV dependiendo del tipo de SALIDA /Renta/Venta/Interno.

  if (document.getElementById('evidencia_PDF')) {
    document.getElementById("evidencia_PDF").addEventListener("change", function() {
      if (document.getElementById("evidencia_PDF").checked) {
        document.getElementById('div_evidencia_PDF').innerHTML = `<input type="file" class="form-control" name="evidencia_PDF" accept="application/pdf">`;
      } else {
        document.getElementById('div_evidencia_PDF').innerHTML = ``;
      }
    })
  }

  // °°°°°°°°°°°°°°°°°° Boton en seccion de listado de Registros de Salida para visualizar el PDF de evidencia.
  if (document.querySelector(".pdf-RST")) {
    document.querySelectorAll('.pdf-RST').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        const tipo = this.dataset.tipo;
        const folio = this.dataset.folio;
        const fecha = this.dataset.fecha.split('-');
        const titleModal = "REGISTRO DE SALIDA " + tipo.substring(0, tipo.length - 1) + " | " + folio;
        const url = SERVERURL + 'DocsCR/ALMACEN/' + tipo + '/' + fecha[0] + '/' + fecha[1] + '/' + folio + '.pdf';
        const embed = `<embed src="` + url + `" height="100%" width="100%" >`;
        document.getElementById("modalTitleFull").innerText = titleModal;
        document.getElementById("modalBodyFull").innerHTML = embed;
        $('#modalFull').modal('show');
      })
    })
  }

  if (document.getElementById("delEviPDF")) {
    document.getElementById("delEviPDF").addEventListener("click", async function(e) {
      e.preventDefault();
      const tipo = this.dataset.tipo;
      const folio = this.dataset.folio;
      const fecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar evidencia?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delEviPDF', tipo, folio, fecha);
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

  if (document.getElementById("requestTipo")) {

    let Candy = "";
    let Renan = "";
    let Rafa = "";

    if (document.getElementById("requestEmp").value == "Candy") {
      Candy = "selected";
    } else if (document.getElementById("requestEmp").value == "Renan") {
      Renan = "selected";
    } else if (document.getElementById("requestEmp").value == "Rafa") {
      Rafa = "selected";
    }


    let html;

    window.onload = async function() {
      var rstQRY = await QRYajax(0, "tonerRO_id_enc", document.getElementById("tonerRO_id").value);
      if (rstQRY.Status) {
        const selectTipo = document.getElementById("requestTipo").value;
        var dataRSTresult = await QRYajax(2, "dataRST", selectTipo, document.getElementById("tonerRO_id").value);
        if (dataRSTresult.Status) {
          dataRSTresult = dataRSTresult.Data;

          html = `
                    <div class="row">
                      <div class="col-12 col-md-12">
                      <input type="hidden" name="tonerRO_tipo" value="` + selectTipo + `">`;

          if (selectTipo == "Venta") {


            var Cliente = dataRSTresult.Cliente;
            if (Cliente.length == 0) {
              html += `<select class="form-select" name="tonerRO_identificador" data-placeholder="Selecciona un cliente">
                      <option></option>`;
            } else {
              html += `<select class="form-select" name="tonerRO_identificador">
                      <option value="` + Cliente.cliente_id + `">( ` + Cliente.cliente_rs + ` ) ` + Cliente.cliente_rfc + `</option>`;
            }

            const Clientes = dataRSTresult.Clientes;
            for (let i = 0; i < Clientes.length; i++) {
              html += `<option value="` + Clientes[i].cliente_id + `">( ` + Clientes[i].cliente_rs + ` ) ` + Clientes[i].cliente_rfc + `</option>`;
            }

            html += `</select>`;


          } else if (selectTipo == "Renta") {

            var Renta = dataRSTresult.Renta;

            if (Renta.length == 0) {
              html += `<select class="form-select" name="tonerRO_identificador" data-placeholder="Selecciona una renta">
                      <option></option>`;
            } else {
              html += `<select class="form-select" name="tonerRO_identificador">
                      <option value="` + Renta.renta_id + `">( ` + Renta.contrato_folio + `-` + Renta.renta_folio + ` ) ` + Renta.cliente_rs + ` | ` + Renta.renta_depto + `</option>`;
            }

            const Rentas = dataRSTresult.Rentas;
            for (let i = 0; i < Rentas.length; i++) {
              html += `<option value="` + Rentas[i].renta_id + `">( ` + Rentas[i].contrato_folio + `-` + Rentas[i].renta_folio + ` ) ` + Rentas[i].cliente_rs + ` | ` + Rentas[i].renta_depto + `</option>`;
            }

            html += `</select>`;


          } else if (selectTipo == "Interno") {
            html += `
                <input type="hidden" name="tonerRO_identificador" value="` + dataRSTresult.Interno + `">
                `;
          }

          html += `
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-12">
                      <div class="form-group">
                          <select class="form-select" name="tonerRO_empleado">
                            <option value="Candy" ` + Candy + `>Candy</option>
                            <option value="Renan" ` + Renan + `>Renan</option>
                            <option value="Rafa" ` + Rafa + `>Rafa</option>
                          </select>
                      </div>
                    </div>
                  </div>
                  `;
          document.getElementById("divContTipoEdit").innerHTML = html;
          select2();

          // console.log(selectTipo);
          // console.log(dataRSTresult);
        } else {
          Swal.fire({
            title: "ERROR",
            text: dataRSTresult.Data,
            icon: "error",
          })
        }
      } else {
        Swal.fire({
          title: "ERROR",
          text: "El registro solicitado no exite, seras redireccionado.",
          icon: "error",
          confirmButtonText: 'Confirmar',
        }).then(async function(result) {
          console.log(result)
          if (result.isConfirmed || (result.isDismissed && result.dismiss == 'backdrop')) {
            window.location.href = SERVERURL + "Toners/Lista";
          }
        })
      }
    };



    // Acciones al cambiar el input tipo radio del tipo de Registro de Retiro /Renta/Venta/Interno/
    document.querySelectorAll('input[name="tonerRO_tipo"]').forEach((elem) => {
      elem.addEventListener("change", async function(event) {
        const selectTipo = event.target.value;
        var dataRSTresult = await QRYajax(2, "dataRST", selectTipo, document.getElementById("tonerRO_id").value);
        if (dataRSTresult.Status) {
          dataRSTresult = dataRSTresult.Data;

          html = `
                    <div class="row">
                      <div class="col-12 col-md-12">
                      <input type="hidden" name="tonerRO_tipo" value="` + selectTipo + `">`;

          if (selectTipo == "Venta") {


            var Cliente = dataRSTresult.Cliente;
            if (Cliente.length == 0) {
              html += `<select class="form-select" name="tonerRO_identificador" data-placeholder="Selecciona un cliente">
                      <option></option>`;
            } else {
              html += `<select class="form-select" name="tonerRO_identificador">
                      <option value="` + Cliente.cliente_id + `">( ` + Cliente.cliente_rs + ` ) ` + Cliente.cliente_rfc + `</option>`;
            }

            const Clientes = dataRSTresult.Clientes;
            for (let i = 0; i < Clientes.length; i++) {
              html += `<option value="` + Clientes[i].cliente_id + `">( ` + Clientes[i].cliente_rs + ` ) ` + Clientes[i].cliente_rfc + `</option>`;
            }

            html += `</select>`;


          } else if (selectTipo == "Renta") {

            var Renta = dataRSTresult.Renta;

            if (Renta.length == 0) {
              html += `<select class="form-select" name="tonerRO_identificador" data-placeholder="Selecciona una renta">
                      <option></option>`;
            } else {
              html += `<select class="form-select" name="tonerRO_identificador">
                      <option value="` + Renta.renta_id + `">( ` + Renta.contrato_folio + `-` + Renta.renta_folio + ` ) ` + Renta.cliente_rs + ` | ` + Renta.renta_depto + `</option>`;
            }

            const Rentas = dataRSTresult.Rentas;
            for (let i = 0; i < Rentas.length; i++) {
              html += `<option value="` + Rentas[i].renta_id + `">( ` + Rentas[i].contrato_folio + `-` + Rentas[i].renta_folio + ` ) ` + Rentas[i].cliente_rs + ` | ` + Rentas[i].renta_depto + `</option>`;
            }

            html += `</select>`;


          } else if (selectTipo == "Interno") {
            html += `
                <input type="hidden" name="tonerRO_identificador" value="` + dataRSTresult.Interno + `">
                `;
          }

          html += `
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-md-12">
                      <div class="form-group">
                          <select class="form-select" name="tonerRO_empleado">
                            <option value="Candy" ` + Candy + `>Candy</option>
                            <option value="Renan" ` + Renan + `>Renan</option>
                            <option value="Rafa" ` + Rafa + `>Rafa</option>
                          </select>
                      </div>
                    </div>
                  </div>
                  `;
          document.getElementById("divContTipoEdit").innerHTML = html;
          select2();

          // console.log(selectTipo);
          // console.log(dataRSTresult);
        } else {
          Swal.fire({
            title: "ERROR",
            text: dataRSTresult.Data,
            icon: "error",
          })
        }
      });
    });


  }





  // ----------------------------------  SECCION REGISTROS DE ENTRADA
  // °°°°°°°°°°°°°°°°°° Ocultar Seccion de Agregar Toner existente al perder el foco en input de agregar Nuevo producto al almacen.
  if (document.getElementById("toner_codigo_add")) {
    document.getElementById("toner_codigo_add").addEventListener("blur", async function() {
      if (document.getElementById("toner_codigo_add").value != "") {
        document.getElementById("AddExsT").style.display = "none";
      }
    });
  }
  // ------ FIN ------

  // °°°°°°°°°°°°°°°°°° Agregar Card y datos al JSON para agregar nuevo registro de Entrada de toner existente.
  if (document.getElementById("btn-AddExsT")) {
    var arrayAddExsT = [];
    var idAddExsT = 0;

    document.getElementById("btn-AddExsT").addEventListener("click", async function(e) {
      e.preventDefault();
      // INICIO ----------------------- INICIO //
      var fechaAddExsT = document.getElementById("fecha-AddExsT").value;
      var codigoAddExsT = document.getElementById("codigo-AddExsT").value;
      var cantAddExsT = document.getElementById("cant-AddExsT").value;
      var commAddExsT = document.getElementById("comm-AddExsT").value;

      if (fechaAddExsT == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar fecha",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (codigoAddExsT == "") {
        Swal.fire({
          title: "Error",
          text: "Selecciona un codigo",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else if (cantAddExsT <= 0) {
        if (cantAddExsT == 0) {
          Swal.fire({
            title: "Error",
            text: "Falta agregar cantidad",
            icon: "error",
            confirmButtonText: 'Aceptar'
          });
          return;
        } else if (cantAddExsT < 0) {
          Swal.fire({
            title: "Error",
            text: "Cantidad incorrecta",
            icon: "error",
            confirmButtonText: 'Aceptar'
          });
          return;
        }
      } else if (commAddExsT == "") {
        Swal.fire({
          title: "Error",
          text: "Falta agregar Comentario",
          icon: "error",
          confirmButtonText: 'Aceptar'
        });
        return;
      } else {
        var tonerData = await consultaFetch('toner_id_enc', codigoAddExsT);
        if (tonerData.Estado) {
          tonerData = tonerData.Data[0];
          if (tonerData.toner_estado == "Activo") {
            idAddExsT++;

            var innerAddExsT = {
              idAddExsT,
              tonerR_toner_id: tonerData.toner_id,
              tonerR_fecha: fechaAddExsT,
              tonerR_cant: cantAddExsT,
              tonerR_comm: commAddExsT,
            };

            arrayAddExsT.push(innerAddExsT);

            // Crea un nuevo elemento div Tipo Col
            const divCol = document.createElement("div");
            divCol.classList.add("col");
            divCol.setAttribute("id", "divColAddExsT" + idAddExsT);
            // Agregando HTML con DivCARD al DivCol
            var divCardHTML = `
                              <div class="card" style="width: 18rem;">
                                <div class="card-header">
                                  <button class="btnDel-AddExsT btn btn-danger" value="` + idAddExsT + `" onclick="trDelAddExsT(event)">Eliminar</button>
                                </div>
                                <ul class="list-group list-group-flush">
                                  <input value="` + fechaAddExsT + `" disabled>
                                  <input value="` + tonerData.toner_codigo + ` | ` + tonerData.toner_comp + `" disabled>
                                  <input value="` + cantAddExsT + `" disabled>
                                  <input value="` + commAddExsT + `" disabled>
                                </ul>
                              </div>
                              `;
            divCol.innerHTML = divCardHTML;
            document.getElementById('divRowAddExsT').appendChild(divCol);


            $('.form-select').val(null).trigger('change');
            document.getElementById("fecha-AddExsT").value = fechaAddExsT;
            document.getElementById("cant-AddExsT").value = "";
            document.getElementById("comm-AddExsT").value = "";

            console.table(arrayAddExsT);
          } else {
            Swal.fire({
              title: "Error",
              text: "Toner marcado como inactivo.",
              icon: "error",
              confirmButtonText: 'Aceptar'
            });
            return;
          }
        } else {
          Swal.fire({
            title: "Error",
            text: "Toner no encontrado.",
            icon: "error",
            confirmButtonText: 'Aceptar'
          });
          return;
        }
      }

      //  FIN -------------------------- FIN  //
      document.getElementById("fin-AddExsT").style.display = "block";
      document.getElementById("AddNewT").style.display = "none";

    });

    function trDelAddExsT(event) {
      event.preventDefault();
      const id = event.target.value;
      document.getElementById("divColAddExsT" + id).remove();

      arrayAddExsT = arrayAddExsT.filter(item => item.idAddExsT != id)
      console.table(arrayAddExsT)
      if (arrayAddExsT.length == 0) {
        document.getElementById("fin-AddExsT").style.display = "none";
        document.getElementById("AddNewT").style.display = "block";
      }
    }



    // Inicio ---- Boton Cancelar Todo
    document.getElementById("btnCancel-AddExsT").addEventListener("click", function() {
      Swal.fire({
        title: "Espera.",
        text: "Deseas cancelar todo??.",
        icon: "info",
        confirmButtonText: 'Si',
        confirmButtonColor: "#008341",
        showCancelButton: true,
        cancelButtonText: 'No',
        cancelButtonColor: "#440d00"
      }).then(async function(res) {
        if (res.isConfirmed) {
          window.location.reload();
        }
      });
    });
    // FIN ---- Boton Cancelar Todo

    // Inicio ---- Boton Finalizar Todo
    document.getElementById("btnSuccess-AddExsT").addEventListener("click", function() {
      Swal.fire({
        title: "Confirmacion.",
        text: "Agregar los registros??.",
        icon: "info",
        confirmButtonText: 'Agregar',
        confirmButtonColor: "#008341",
        showCancelButton: true,
        cancelButtonText: 'Aun no',
        cancelButtonColor: "#440d00"
      }).then(async function(res) {
        if (res.isConfirmed) {
          var envio = 1;
          let i = 0
          do {
            var tonerR_fecha = arrayAddExsT[i].tonerR_fecha;
            var tonerR_toner_id = arrayAddExsT[i].tonerR_toner_id;
            var tonerR_cant = arrayAddExsT[i].tonerR_cant;
            var tonerR_comm = arrayAddExsT[i].tonerR_comm;

            const res = await fetch(SERVERURL + "ajax/addExsTFetchAjax.php", {
              method: "POST",
              body: JSON.stringify({
                tonerR_fecha,
                tonerR_toner_id,
                tonerR_cant,
                tonerR_comm,
              }),
              headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
              },
            });
            const data = await res.json();
            if (data.Status) {
              console.log("Envio No.:" + envio + ", " + data.Result);
            } else {
              console.log("Envio No.:" + envio + " FALLIDO, " + data.Result);
            }
            envio++

            i++
          } while (i < arrayAddExsT.length);

          console.log(i);
          if (i == arrayAddExsT.length) {
            Swal.fire({
              title: "Exito",
              text: "Entradas Agregadas Correctamente!",
              icon: "success",
              confirmButtonText: 'OK !!',
              confirmButtonColor: "#3085d6"
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              }
            });
          }




          // for (let i = 0; i < arrayAddExsT.length; i++) {
          //   var tonerR_fecha = arrayAddExsT[i].tonerR_fecha;
          //   var tonerR_toner_id = arrayAddExsT[i].tonerR_toner_id;
          //   var tonerR_cant = arrayAddExsT[i].tonerR_cant;
          //   var tonerR_comm = arrayAddExsT[i].tonerR_comm;

          //   const res = await fetch(SERVERURL + "ajax/addExsTFetchAjax.php", {
          //     method: "POST",
          //     body: JSON.stringify({
          //       tonerR_fecha,
          //       tonerR_toner_id,
          //       tonerR_cant,
          //       tonerR_comm,
          //     }),
          //     headers: {
          //       Accept: "application/json",
          //       "Content-Type": "application/json",
          //     },
          //   });
          //   const data = await res.json();
          //   if (data.Status) {
          //     console.log("Envio No.:" + envio + ", " + data.Result);
          //   } else {
          //     console.log("Envio No.:" + envio + " FALLIDO, " + data.Result);
          //   }
          //   envio++
          // }
          // console.log(i);
          // Swal.fire({
          //   title: "Exito",
          //   text: "Registros Completados!",
          //   icon: "success",
          //   confirmButtonText: 'OK !!',
          //   confirmButtonColor: "#3085d6"
          // }).then((result) => {
          //   if (result.isConfirmed) {
          //     location.reload();
          //   }
          // });
        }
      });
    });
    // FIN ---- Boton Finalizar Todo
  }
  // ------ FIN ------






  // ----------------------------------  SECCION PRODUCTOS TONERS
  // °°°°°°°°°°°°°°°°°° Modal de ventana para Editar datos de Producto.
  if (document.querySelector('.btnEdit')) {
    document.querySelectorAll('.btnEdit').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var Data = await consultaFetch('toner_id_enc', elem.value);
        if (Data['Estado']) {
          Data = Data['Data'][0];
          var Title = "";
          var html = `
                      <input type="hidden" class="form-control" id="toner_id_edit" name="toner_id_edit" value="` + elem.value + `">
                      <fieldset>
                          <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
                          <div class="container-fluid">
                              <div class="row">
                                  <div class="col-12 col-md">
                                      <div class="form-group">
                                          <label for="toner_parte_edit" class="bmd-label-floating">NO. DE PARTE</label>
                                          <input type="text" class="form-control" id="toner_parte_edit" name="toner_parte_edit" value="` + Data['toner_parte'] + `">
                                      </div>
                                  </div>
                                  <div class="col-12 col-md">
                                      <div class="form-group">
                                          <label for="toner_comp_edit" class="bmd-label-floating">COMPATIBILIDAD</label>
                                          <textarea class="form-control" id="toner_comp_edit" name="toner_comp_edit">` + Data['toner_comp'] + `</textarea>
                                      </div>
                                  </div>
                                  <div class="col-12 col-md">
                                      <div class="form-group">
                                          <label for="toner_rendi_edit" class="bmd-label-floating">RENDIMIENTO</label>
                                          <input type="text" class="form-control" id="toner_rendi_edit" name="toner_rendi_edit" value="` + Data['toner_rendi'] + `">
                                      </div>
                                  </div>
                                  <div class="col-12 col-md-12">
                                      <div class="form-group">
                                          <label>PROVEEDOR</label>
                                          <select class="form-select" id="provT_id_edit" name="provT_id">
                                              <option selected value="` + Data['provT_id'] + `">` + Data['provT_nombre'] + `</option>
                                              <?php
                                              $sql = 'SELECT * FROM ProveedoresT ORDER BY provT_nombre ASC';
                                              $query = consultaData($sql);
                                              $dataProvT = $query['dataFetch'];
                                              foreach ($dataProvT as $dato) { ?>
                                                  <option value="<?= $dato['provT_id']; ?>"><?= $dato['provT_nombre']; ?></option>
                                              <?php } ?>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-12 col-md-12">
                                      <div class="form-group">
                                          <label>ESTADO</label>
                                          <select class="form-select" id="toner_estado_edit" name="toner_estado_edit">
                                          `;
          if (Data['toner_estado'] == "Activo") {
            html += `
                                            <option selected value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                    `;
          } else {
            html += `
                                            <option selected value="Inactivo">Inactivo</option>
                                            <option value="Activo">Activo</option>
                    `;
          }
          html += `</select>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </fieldset>
                    `;
        } else {
          var Title = "";
          var html = "(O _ o) No existe el cliente solicitado (o _ O)";
        }
        document.getElementById("modalTitleForm").innerText = Title;
        document.getElementById("modalBodyForm").innerHTML = html;
        select2();
        $('#modalForm').modal('show');
      });
    });
  }

  // °°°°°°°°°°°°°°°°°° Modal de ventana para imprimir Stock De Toners.
  if (document.getElementById("btn-Stock")) {
    document.getElementById("btn-Stock").addEventListener("click", async function(e) {
      e.preventDefault();
      console.log("click")

      var titleModal = "EXISTENCIA DE TONER";
      var url = SERVERURL + "vista/formats/tonerStock.php";
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = '<embed src="' + url + '" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">';
      $("#modalFull").modal("show");
    })
  }

  // °°°°°°°°°°°°°°°°°° Boton para desabilitar un producto en lista de existencia.
  if (document.querySelector('.btnDisable')) {
    document.querySelectorAll('.btnDisable').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var Data = await QRYajax(0, 'toner_id_enc', elem.value);
        if (Data.Status) {
          Swal.fire({
            title: "Espera.",
            text: "Deseas Inablitar este toner??.",
            icon: "warning",
            confirmButtonText: 'Si',
            confirmButtonColor: "#008341",
            showCancelButton: true,
            cancelButtonText: 'No',
            cancelButtonColor: "#440d00"
          }).then(async function(res) {
            if (res.isConfirmed) {
              Swal.fire({
                title: "Doble confirmacion.",
                text: "De verdad lo vas a inabilitar??.",
                icon: "error",
                confirmButtonText: 'Si',
                confirmButtonColor: "#008341",
                showCancelButton: true,
                cancelButtonText: 'No',
                cancelButtonColor: "#440d00"
              }).then(async function(res) {
                if (res.isConfirmed) {
                  var disbaleToner = await QRYajax(1, 'tonerDisableIDenc', elem.value);
                  if (disbaleToner.Status) {
                    Swal.fire({
                      title: "Actualizacion Completada",
                      text: "Toner Desabilitado correctamente.",
                      icon: "info",
                      confirmButtonText: "OK !!",
                      confirmButtonColor: "#3085d6",
                    }).then((result) => {
                      if (result.isConfirmed) {
                        location.reload();
                      }
                    });
                  } else {
                    Swal.fire({
                      title: "ERROR",
                      text: disbaleToner.Data,
                      icon: "error",
                    });
                    return;
                  }
                }
              });
            }
          });
        } else {
          Swal.fire({
            title: "ERROR",
            text: "No existe el toner seleccionado.",
            icon: "error",
          });
          return;
        }
      })
    })
  }
</script>