<script>
  tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", <?php echo $DT_orderCol; ?>);

  const formulario = document.getElementById("formulario");
  const codigoR = document.getElementById("ref_codigo_add");
  if (codigoR) {
    codigoR.addEventListener("blur", async function() {
      if (codigoR.value != "") {
        let refCodigo = await consultaFetch('ref_codigo', codigoR.value);
        if (refCodigo['Estado']) {
          var refData = refCodigo['Data'][0];
          var html = `
                        <input type="hidden" value="` + refData['ref_id'] + `" name="ref_id">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" value="` + refData['provR_nombre'] + ` | ` + refData['ref_comp'] + `" disabled>
                                </div>
                            </div>
                        </div>
                        `;
          formulario.innerHTML = html;
        } else {
          var html = `
                        <input type="hidden" name="ref_nuevo">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <select class="form-select" id="ref_catR_id" name="ref_catR_id" data-placeholder="Selecciona una Categoria">
                                        <option></option>
                                        <?php
                                        $sql = 'SELECT * FROM CategoriasR ORDER BY catR_id ASC';
                                        $query = consultaData($sql);
                                        $catRdata = $query['dataFetch'];
                                        foreach ($catRdata as $dato) { ?>
                                            <option value="<?php echo $dato['catR_id']; ?>"><?php echo $dato['catR_codigo'] . " | " . $dato['catR_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <select class="form-select" id="ref_provR_id" name="ref_provR_id" data-placeholder="Selecciona un proveedor">
                                        <option></option>
                                        <?php
                                        $sql = 'SELECT * FROM ProveedoresR ORDER BY provR_nombre ASC';
                                        $query = consultaData($sql);
                                        $dataProvT = $query['dataFetch'];
                                        foreach ($dataProvT as $dato) { ?>
                                            <option value="<?php echo $dato['provR_id']; ?>"><?php echo $dato['provR_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="ref_comp" class="bmd-label-floating">COMPATIBILIDAD</label>
                                    <input type="text" class="form-control" id="ref_comp" name="ref_comp" maxlength="50">
                                </div>
                            </div>
                        </div>
                        `;
          formulario.innerHTML = html;
          select2();
        }
      }
    });
  }


  // Modal de ventana Categorias
  if (document.getElementById("btn-Cats")) {
    document.getElementById("btn-Cats").addEventListener("click", function(event) {
      event.preventDefault();
      $("#modalCatsR").modal("show");
    });
  }

  // Modal de ventana Editar Categoria
  if (document.querySelector(".btn-editCatR")) {
    document.querySelectorAll('.btn-editCatR').forEach((elem) => {
      elem.addEventListener("click", async function(e) {
        e.preventDefault();
        let catRData = await consultaFetch('enc_catR_id', elem.id);
        if (catRData.Estado) {
          catRData = catRData.Data[0];
          // {
          //   catR_id: '3',
          //   catR_nombre: 'Rodillo de Carga',
          //   catR_codigo: 'MC'
          // }
          var Title = "";
          var html = `
                      <fieldset>
                          <input type="hidden" class="form-control" id="catR_id_edit" name="catR_id_edit" value="` + elem.id + `">
                          <legend><i class="fas fa-user"></i> &nbsp; Editar Categoria de Refacciones</legend>
                          <div class="container-fluid">
                              <div class="row">
                                  <div class="col-12 col-md-6">
                                      <div class="form-group">
                                          <label for="catR_codigo_edit" class="bmd-label-floating">CODIGO</label>
                                          <input class="form-control" id="catR_codigo_edit" name="catR_codigo_edit" value="` + catRData['catR_codigo'] + `">
                                      </div>
                                      <div class="form-group">
                                          <label for="catR_nombre_edit" class="bmd-label-floating">DESCRIPCION</label>
                                          <input class="form-control" id="catR_nombre_edit" name="catR_nombre_edit" max="30" value="` + catRData['catR_nombre'] + `">
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </fieldset>
                    `;
          document.getElementById("modalTitleForm").innerText = Title;
          document.getElementById("modalBodyForm").innerHTML = html;
          $('#modalForm').modal('show');
          $("#modalCatsR").modal("hide");
        } else {
          Swal.fire({
            title: "Espera",
            text: "No existe la categoria solicitada.",
            icon: "info",
            confirmButtonText: 'OK'
          });
        }
      });
    });
  }

  // Modal de ventana Agregar Categoria
  if (document.getElementById("btn-addCatR")) {
    document.getElementById("btn-addCatR").addEventListener("click", async function(e) {
      e.preventDefault();
      var dataCatsR = await consultaFetch("CategoriasR", 1);
      dataCatsR = dataCatsR["Data"];
      var html = `
                  <fieldset>
                    <legend><i class="fas fa-plus"></i> &nbsp; Agregar Categoria de Refacciones</legend>
                    <datalist id="catR_codigos">`;
      for (let i = 0; i < dataCatsR.length; i++) {
        html +=
          `<option value="` + dataCatsR[i].catR_codigo + `">` + dataCatsR[i].catR_codigo + `</option>`;

      }
      html += `
                    </datalist>
                    <datalist id="catR_nombres">`;
      for (let i = 0; i < dataCatsR.length; i++) {
        html +=
          `<option value="` + dataCatsR[i].catR_nombre + `">` + dataCatsR[i].catR_nombre + `</option>`;

      }
      html += `
                    </datalist>
                    `;
      html += `
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="catR_codigo_add" class="bmd-label-floating">CODIGO</label>
                                    <input class="form-control" id="catR_codigo_add" name="catR_codigo_add" pattern="[A-Z]{2,3}" list="catR_codigos" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="catR_nombre_add" class="bmd-label-floating">DESCRIPCION</label>
                                    <input class="form-control" id="catR_nombre_add" name="catR_nombre_add" list="catR_nombres" autocomplete="off" max="30">
                                </div>
                            </div>
                        </div>
                    </div>
                  </fieldset>
                    `;
      document.getElementById("modalTitleForm").innerText = "";
      document.getElementById("modalBodyForm").innerHTML = html;
      $('#modalForm').modal('show');
      $("#modalCatsR").modal("hide");
    });
  }

  // Modal de ventana para imprimir Stock De Toners
  if (document.getElementById("btn-Stock")) {
    document.getElementById("btn-Stock").addEventListener("click", async function(e) {
      e.preventDefault();
      console.log("click")

      var titleModal = "EXISTENCIA DE REFACCIONES";
      var url = SERVERURL + "vista/formats/refaccionesStock.php";
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = '<embed src="' + url + '" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">';
      $("#modalFull").modal("show");
    })
  }

  // Editar Refaccion
  if (document.querySelector('.btnEdit')) {
    document.querySelectorAll('.btnEdit').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var Result = await QRYajax(2, 'ref_id_enc', elem.value);

        var Status = Result.Status;
        var Data = Result.Data;
        var Image = Result.Image;
        console.log(Image);

        if (Status) {

          var RefData = Data.Refaccion;
          var ProvRef = Data.Proveedores;

          var title = "REFACCION | " + RefData['ref_codigo'];
          var html = `
                      <input type="hidden" class="form-control" id="ref_id_edit" name="ref_id_edit" value="` + elem.value + `">
                      <fieldset>
                          <legend><i class="fas fa-user"></i> &nbsp; Información básica</legend>
                          <div class="container-fluid">
                              <div class="row">
                                  <div class="col-12 col-md">
                                      <div class="form-group">
                                          <label for="ref_comp_edit" class="bmd-label-floating">COMPATIBILIDAD</label>
                                          <textarea class="form-control" id="ref_comp_edit" name="ref_comp_edit">` + RefData['ref_comp'] + `</textarea>
                                      </div>
                                  </div>
                                  <div class="col-12 col-md-12">
                                      <div class="form-group">
                                          <label>PROVEEDOR</label>
                                          <select class="form-select" id="ref_provR_id_edit" name="ref_provR_id_edit">
                                              <option selected value="` + RefData['ref_provR_id'] + `">` + RefData['provR_nombre'] + `</option>`
          for (let iProv = 0; iProv < ProvRef.length; iProv++) {
            html += `
                                              <option value="` + ProvRef[iProv]['provR_id'] + `">` + ProvRef[iProv]['provR_nombre'] + `</option>`;
          }
          html += `
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-12 col-md-12">
                                      <div class="form-group">
                                          <label>ESTADO</label>
                                          <select class="form-select" id="ref_estado_edit" name="ref_estado_edit">
                                          `;

          if (RefData['ref_estado'] == "Activo") {
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
                      <fieldset>
                        <legend><i class="fas fa-user"></i> &nbsp; Imagen</legend>
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-12 col-md">
                  `;

          if (Image.Status) {
            html += `
            <button class="btn btn-danger" id="btnDelImg" value="` + elem.value + `"><i class="fas fa-trash"></i></button>
            <embed src="` + Image.URL + `" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">
            `;
          } else {
            html += `
                              <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                <input type="checkbox" class="btn-check" id="ref_image" autocomplete="off">
                                <label class="btn btn-outline-primary" for="ref_image">IMAGEN</label>
                              </div>
                              <div class="form-group">
                                <div id="div_ref_image">
                                </div>
                              </div>`;
          }
          html += `
                            </div>
                          </div>
                        </div>
                      </fieldset>`;
        } else {
          var title = "ERROR";
          var html = Data;
        }

        document.getElementById("modalTitleForm").innerText = title;
        document.getElementById("modalBodyForm").innerHTML = html;
        select2();
        $('#modalForm').modal('show');

        if (document.getElementById('ref_image')) {
          document.getElementById("ref_image").addEventListener("change", function() {
            if (document.getElementById("ref_image").checked) {
              document.getElementById('div_ref_image').innerHTML = `<input type="file" class="form-control" name="ref_image" accept="image/jpeg">`;
            } else {
              document.getElementById('div_ref_image').innerHTML = ``;
            }
          })
        }

        if (document.getElementById('btnDelImg')) {
          document.getElementById("btnDelImg").addEventListener("click", async function(e) {
            e.preventDefault();
            Swal.fire({
              title: "Eliminar Imagen",
              text: "Deseas continuar?",
              icon: "warning",
            }).then(async function(res) {
              if (res.isConfirmed) {
                var Result = await QRYajax(1, 'delRefImg', elem.value);
                if (Result.Status) {
                  location.reload();
                } else {
                  Swal.fire({
                    title: "Ocurrio un error.",
                    text: Result.Data,
                    icon: "error",
                  });
                }
              }
            });
          })
        }
      });
    });
  }

  // Ver Imagen
  if (document.querySelector('.btnPhoto')) {
    document.querySelectorAll('.btnPhoto').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var Result = await QRYajax(2, 'ref_id_enc', elem.value);

        var Status = Result.Status;
        var Data = Result.Data;
        var Image = Result.Image;
        console.log(Image);

        if (Status) {

          var RefData = Data.Refaccion;
          var ProvRef = Data.Proveedores;

          var title = "REFACCION | " + RefData['ref_codigo'];
          var html = `
                      <input type="hidden" class="form-control" id="ref_id_edit" name="ref_id_edit" value="` + elem.value + `">
                      <input type="hidden" name="ref_comp_edit" value="` + RefData['ref_comp'] + `">
                      <input type="hidden" name="ref_provR_id_edit" value="` + RefData['ref_provR_id'] + `">
                      <input type="hidden" name="ref_estado_edit" value="` + RefData['ref_estado'] + `">
                      <fieldset>
                        <legend><i class="fas fa-user"></i> &nbsp; Imagen</legend>
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-12 col-md">
                  `;

          if (Image.Status) {
            html += `
            <button class="btn btn-danger" id="btnDelImg" value="` + elem.value + `"><i class="fas fa-trash"></i></button>
            <embed src="` + Image.URL + `" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">
            `;
          } else {
            html += `
                              <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                <input type="checkbox" class="btn-check" id="ref_image" autocomplete="off">
                                <label class="btn btn-outline-primary" for="ref_image">IMAGEN</label>
                              </div>
                              <div class="form-group">
                                <div id="div_ref_image">
                                </div>
                              </div>`;
          }
          html += `
                            </div>
                          </div>
                        </div>
                      </fieldset>`;
        } else {
          var title = "ERROR";
          var html = Data;
        }

        document.getElementById("modalTitleForm").innerText = title;
        document.getElementById("modalBodyForm").innerHTML = html;
        $('#modalForm').modal('show');

        if (document.getElementById('ref_image')) {
          document.getElementById("ref_image").addEventListener("change", function() {
            if (document.getElementById("ref_image").checked) {
              document.getElementById('div_ref_image').innerHTML = `<input type="file" class="form-control" name="ref_image" accept="image/jpeg">`;
            } else {
              document.getElementById('div_ref_image').innerHTML = ``;
            }
          })
        }

        if (document.getElementById('btnDelImg')) {
          document.getElementById("btnDelImg").addEventListener("click", async function(e) {
            e.preventDefault();
            Swal.fire({
              title: "Eliminar Imagen",
              text: "Deseas continuar?",
              icon: "warning",
            }).then(async function(res) {
              if (res.isConfirmed) {
                var Result = await QRYajax(1, 'delRefImg', elem.value);
                if (Result.Status) {
                  location.reload();
                } else {
                  Swal.fire({
                    title: "Ocurrio un error.",
                    text: Result.Data,
                    icon: "error",
                  });
                }
              }
            });
          })
        }
      });
    });
  }
</script>