<script>
  tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);

  if (document.getElementById('cotM_IVA')) {
    document.getElementById('cotM_IVA').addEventListener('input', (e) => {
      var IVAs = e.srcElement.value;
      document.getElementById('cotM_IVA_label').innerText = "IVA AL " + IVAs + "%";
    })
  }

  // Funcion para adjuntar el placeholder del moto sugerido para cada producto. //
  if (document.getElementById('cotD_prod_id')) {
    $('#cotD_prod_id').on('select2:select', async function(e) {
      var data = e.params.data;
      var QRYdataProd = await QRYajax(0, "cotD_prod_id_enc", data.id);
      if (QRYdataProd.Status) {
        var dataProd = QRYdataProd.Data[0];
        document.getElementById('cotD_monto').placeholder = dataProd['AlmP_precio'];
      } else {
        Swal.fire({
          title: "El producto elegido no existe en la base de datos.",
          text: "",
          icon: "info",
        });
      }
    });
  }

  // Funcion para visualizar PDF en una ventana externa, desde Ventana Lista. //
  if (document.querySelector('.btnPDF')) {
    document.querySelectorAll('.btnPDF').forEach((elem) => {
      elem.addEventListener("click", async function() {
        var encIDcotM = this.dataset.id;
        const QRYencIDcotM = await QRYajax(0, "encIDcotM", encIDcotM);

        Swal.fire({
          title: "PDF",
          text: "Deseas visualizar el PDF",
          icon: "info",
          confirmButtonText: 'Confirmar',
        }).then(async function(result) {
          if (result.isConfirmed) {

            if (QRYencIDcotM.Status) {

              var mapForm = document.createElement("form");
              mapForm.target = "_blank";
              mapForm.method = "POST";
              mapForm.action = SERVERURL + "vista/formats/printCotizacion.php";

              var inputIDcotM = document.createElement("input");
              inputIDcotM.type = "hidden";
              inputIDcotM.name = "encIDcotM";
              inputIDcotM.value = encIDcotM;
              mapForm.appendChild(inputIDcotM);

              document.body.appendChild(mapForm);
              mapForm.submit();

            } else {
              Swal.fire({
                title: "ERROR CRITICO",
                text: "El registro de Cotizacion solicitado no existe, recarga la pagina.",
                icon: "error",
              })
            }
          }
        })
      });
    });
  }

  // Funcion para visualizar PDF en una ventana externa, desde Ventana Detalles. //
  if (document.getElementById('btnPDFcotM')) {
    document.getElementById('btnPDFcotM').addEventListener('click', async function() {
      var encIDcotM = this.dataset.id;
      const QRYencIDcotM = await QRYajax(0, "encIDcotM", encIDcotM);

      Swal.fire({
        title: "PDF",
        text: "Deseas visualizar el PDF",
        icon: "info",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {

          if (QRYencIDcotM.Status) {

            var mapForm = document.createElement("form");
            mapForm.target = "_blank";
            mapForm.method = "POST";
            mapForm.action = SERVERURL + "vista/formats/printCotizacion.php";

            var inputIDcotM = document.createElement("input");
            inputIDcotM.type = "hidden";
            inputIDcotM.name = "encIDcotM";
            inputIDcotM.value = encIDcotM;
            mapForm.appendChild(inputIDcotM);

            document.body.appendChild(mapForm);
            mapForm.submit();

          } else {
            Swal.fire({
              title: "ERROR CRITICO",
              text: "El registro de Cotizacion solicitado no existe, recarga la pagina.",
              icon: "error",
            })
          }
        }
      })
    });
  }

  // Ventana modal para editar un producto existente. //
  if (document.querySelector('.editarProducto')) {
    document.querySelectorAll('.editarProducto').forEach((elem) => {
      elem.addEventListener("click", async function() {
        var prodIDenc = elem.id;
        var prod_desc = this.dataset.desc;
        var prod_precio = this.dataset.precio;
        var unList_id = this.dataset.unidad;
        let Activo;
        let Inactivo;
        if (this.dataset.estado == 1) {
          Activo = "selected";
        } else {
          Inactivo = "selected";
        }
        var unidades = await QRYajax(2, "unidadesList_edit", unList_id);
        if (unidades.Status) {
          unidades = unidades.Data;
          var unidad = unidades.Unidad;
          var unidadesList = unidades.unidadesList;

          var html = `
                      <input type="hidden" name="actualizarProducto" value="` + prodIDenc + `">
                      <fieldset class="form-neon">
                        <div class="container-fluid">
                          <div class="row justify-content-md-center">
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_estado" class="bmd-label-floating">ESTADO</label>
                                <select class="form-select" id="prod_estado" name="prod_estado">
                                  <option value="1" ` + Activo + `>Activo</option>
                                  <option value="0" ` + Inactivo + `>Inactivo</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_desc" class="bmd-label-floating">Descripcion</label>
                                <input class="form-control" type="text" id="prod_desc" name="prod_desc" value="` + prod_desc + `">
                              </div>
                            </div>
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_precio" class="bmd-label-floating">Precio</label>
                                <input class="form-control" type="number" id="prod_precio" name="prod_precio" value="` + prod_precio + `" pattern="^[0-9]+$">
                              </div>
                            </div>
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_unList_id" class="bmd-label-floating">Unidad</label>
                                <select class="form-select" id="prod_unList_id" name="prod_unList_id">
                                  <option value="` + unidad['id'] + `">` + unidad['unidad'] + `</option>`;
          unidades = unidades['unidadesList'];
          for (let i = 0; i < unidades.length; i++) {
            html += `<option value="` + unidades[i]['id'] + `">` + unidades[i]['unidad'] + `</option>`;
          }
          html += `
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                            `;
          document.getElementById("modalBodyForm").innerHTML = html;
          document.getElementById("modalTitleForm").innerText = "EDITAR PRODUCTO EXISTENTE";
          $('#modalForm').modal('show');
        } else {
          console.log("Error: ".unidades.Data);
        }
      })
    })
  }

  // Ventana modal para Agregar un producto. //
  if (document.getElementById('addProdCot')) {
    document.getElementById('addProdCot').addEventListener('click', async function(e) {
      e.preventDefault();

      var unidades = await QRYajax(2, "unidadesList_add");
      console.log(unidades);
      if (unidades.Status) {
        unidades = unidades.Data;
        console.log(unidades);
        var html = `
                      <input type="hidden" name="nuevoProducto" value="1">
                      <fieldset class="form-neon">
                        <div class="container-fluid">
                          <div class="row justify-content-md-center">
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_desc" class="bmd-label-floating">Descripcion</label>
                                <input class="form-control" type="text" id="prod_desc" name="prod_desc">
                              </div>
                            </div>
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_precio" class="bmd-label-floating">Precio</label>
                                <input class="form-control" type="number" id="prod_precio" name="prod_precio" pattern="^[0-9]+$">
                              </div>
                            </div>
                            <div class="col-md-auto">
                              <div class="form-group">
                                <label for="prod_unidad" class="bmd-label-floating">Unidad</label>
                                <select class="form-select" id="prod_unList_id" name="prod_unList_id">
                                  <option disabled selected>Selecciona una unidad de medida</option>`;
        for (let i = 0; i < unidades.length; i++) {
          html += `<option value="` + unidades[i]['id'] + `">` + unidades[i]['unidad'] + `</option>`;
        }
        html += `
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                      `;
        document.getElementById("modalBodyForm").innerHTML = html;
        document.getElementById("modalTitleForm").innerText = "AGREGAR PRODUCTO NUEVO";
        $('#modalForm').modal('show');
      } else {
        console.log("Error: ".unidades.Data);
      }
    })
  }

  // Redireccion para entrar a detalles de Cotizacion. //
  if (document.querySelector('.btnDetalles')) {
    document.querySelectorAll('.btnDetalles').forEach((elem) => {
      elem.addEventListener("click", async function() {
        var encIDcotM = this.dataset.id;
        location.href = SERVERURL + "Cotizador/idD/" + encIDcotM;
      })
    })
  }

  // Redireccion para entrar a Editor de Cotizacion. //
  if (document.getElementById('btnEditcotM')) {
    document.getElementById('btnEditcotM').addEventListener("click", async function() {
      location.href = SERVERURL + "Cotizador/idE/" + document.getElementById('btnEditcotM').dataset.id;
    })
  }

  // Redireccion para Eliminar Cotizacion. //
  if (document.getElementById('btnDELcotM')) {
    document.getElementById('btnDELcotM').addEventListener("click", async function() {
      Swal.fire({
        title: "Deseas Eliminar el Registro?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var IDenc = document.getElementById('btnDELcotM').dataset.id;
          const deleteReg = await QRYajax(1, "deleteCotM", IDenc);
          if (deleteReg.Status) {
            location.reload();
          } else {
            console.log(deleteReg.Data);
          }
        }
      })
    })
  }

  if (document.querySelector('.btnDelCotD')) {
    document.querySelectorAll('.btnDelCotD').forEach((elem) => {
      elem.addEventListener("click", async function() {
        Swal.fire({
          title: "Deseas Eliminar el Registro?",
          text: "",
          icon: "warning",
          confirmButtonText: 'Confirmar',
        }).then(async function(result) {
          if (result.isConfirmed) {
            const deleteReg = await QRYajax(1, "cotDdel_enc", elem.value);
            if (deleteReg.Status) {
              location.reload();
            } else {
              console.log(deleteReg.Data);
            }
          }
        })
      })
    })
  }
</script>