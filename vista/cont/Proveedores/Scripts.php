<script>
  tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", <?php echo $DT_orderCol; ?>);

  // Modal de ventana Editar Proveedor
  if (document.querySelector(".btn-edit-prov")) {
    document.querySelectorAll('.btn-edit-prov').forEach((elem) => {
      elem.addEventListener("click", async function(e) {
        e.preventDefault();
        var tab = elem.value;
        var prov_id = elem.id;
        var tabla = tab == "provR" ?
          "ProveedoresR" :
          tab == "provE" ?
          "ProveedoresE" :
          tab == "provT" ?
          "ProveedoresT" :
          "noTabla";
        if (tabla != "noTabla") {
          let provData = await consultaFetch('enc_' + tab + '_id', prov_id);
          if (provData.Estado) {
            provData = provData.Data[0];
            var Title = "";
            var html = `
                        <fieldset>
                            <input type="hidden" class="form-control" id="` + tab + `_id_edit" name="` + tab + `_id_edit" value="` + prov_id + `">
                            <legend><i class="fas fa-user"></i> &nbsp; Editar Proveedor</legend>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="` + tab + `_nombre_edit" class="bmd-label-floating">NOMBRE</label>
                                            <input class="form-control" id="` + tab + `_nombre_edit" name="` + tab + `_nombre_edit" value="` + provData[tab + '_nombre'] + `" pattern="^([A-Za-z0-9\\s.,&-ñÑ\\/]+)*$">
                                        </div>
                                        <div class="form-group">
                                            <select class="form-select" id="` + tab + `_estado_edit" name="` + tab + `_estado_edit">`;
            if (provData[tab + '_estado'] == "Activo") {
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
            html += `
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    `;
            document.getElementById("modalTitleForm").innerText = Title;
            document.getElementById("modalBodyForm").innerHTML = html;
            $('#modalForm').modal('show');
          } else {
            Swal.fire({
              title: "Espera",
              text: "No existe el proveedor solicitado.",
              icon: "info",
              confirmButtonText: 'OK'
            });
          }
        } else {
          Swal.fire({
            title: "Ocurrio un error.",
            text: "No existe la tabla solicitada.",
            icon: "error",
          });
        }
      });
    });
  }

  // Modal de ventana Agregar Proveedor
  if (document.getElementById("btn-addProv")) {
    document.getElementById("btn-addProv").addEventListener("click", async function(e) {
      e.preventDefault();
      var tab = document.getElementById("btn-addProv").value;
      if (tab == "provR") {
        var titulo = "Refacciones";
        var tabla = "ProveedoresR";
      } else if (tab == "provE") {
        var titulo = "Equipos";
        var tabla = "ProveedoresE";
      } else if (tab == "provT") {
        var titulo = "Toners";
        var tabla = "ProveedoresT";
      } else {
        var titulo = "";
        var tabla = "noTabla";
      }

      if (tabla != "noTabla") {
        var Data = await consultaFetch(tabla, 1);
        Data = Data["Data"];
        var html = `
                  <fieldset>
                    <legend><i class="fas fa-plus"></i> &nbsp; Agregar nuevo Proveedor de ` + titulo + `</legend>
                    <datalist id="_nombres">`;
        for (let i = 0; i < Data.length; i++) {
          html +=
            `
                      <option value="` + Data[i][tab + '_nombre'] + `">` + Data[i][tab + '_nombre'] + `</option>
          `;

        }
        html += `
                    </datalist>
                    `;
        html += `
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                  <label for="` + tab + `_nombre_add" class="bmd-label-floating">NOMBRE</label>
                                  <input class="form-control" id="` + tab + `_nombre_add" name="` + tab + `_nombre_add" pattern="^([A-Za-z0-9\\s.,&-ñÑ\\/]+)*$">
                                </div>
                            </div>
                        </div>
                    </div>
                  </fieldset>
                    `;
        document.getElementById("modalTitleForm").innerText = "";
        document.getElementById("modalBodyForm").innerHTML = html;
        $('#modalForm').modal('show');
      } else {
        Swal.fire({
          title: "Espera",
          text: "No existe el proveedor solicitado.",
          icon: "info",
          confirmButtonText: 'OK'
        });
      }
    });
  }
</script>