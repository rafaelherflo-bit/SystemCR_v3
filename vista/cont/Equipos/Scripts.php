<script>
  if ('<?= $pagina[1] ?>' == 'Modelos') {
    tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 0);
  } else {
    tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 3);
  }

  if (document.getElementById("btnPrintModelos")) {
    document.getElementById("btnPrintModelos").addEventListener("click", async function(e) {
      e.preventDefault();

      document.getElementById("modalTitleFull").innerText = "IMPRIMIR MODELOS DE EQUIPOS";
      document.getElementById("modalBodyFull").innerHTML = `<embed src="${SERVERURL}vista/formats/printModelos.php" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
      $("#modalFull").modal("show");

    })
  };

  if (document.getElementById("equipo_serie")) {
    document.getElementById("equipo_serie").addEventListener("input", async function() {
      const serieInput = this.value.trim();
      const btnEnviar = document.querySelector('button[type="submit"]'); // Seleccionamos el botón
      const blockNiveles = document.getElementById("contenedor-niveles");
      const seccColores = document.querySelectorAll(".seccion-color");
      const seccResidual = document.getElementById("seccion-residual");
      const inputC = document.getElementsByName('equipo_nivel_C')[0];
      const inputM = document.getElementsByName('equipo_nivel_M')[0];
      const inputY = document.getElementsByName('equipo_nivel_Y')[0];
      const inputR = document.getElementsByName('equipo_nivel_R')[0];

      if (serieInput === "") return;

      const container = document.getElementById("formulario");
      container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

      const equipoData = await QRYajax('Equipos', 'equipo_serie', serieInput);
      if (equipoData['Status']) {
        blockNiveles.style.display = 'none';
        inputC.value = 0;
        inputM.value = 0;
        inputY.value = 0;
        inputR.value = 0;
        // --- EL EQUIPO YA EXISTE ---
        const equData = equipoData['Data']['Equipo'];

        // 1. DESHABILITAR BOTÓN
        btnEnviar.disabled = true;
        btnEnviar.classList.add("opacity-50", "btn-secondary");
        btnEnviar.classList.remove("btn-primary", "shadow");

        html = `
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">Acceso Denegado: Serie Duplicada</h5>
                            <p class="mb-0 small text-uppercase">
                                El equipo <strong>${equData.modelo_linea} ${equData.modelo_modelo}</strong>, 
                                ya se encuentra en la base de datos con estatus: <strong>${equData.equipo_estado}</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row g-3 opacity-75">
                    <div class="col">
                        <label class="small fw-bold text-muted">Toner K</label>
                        <input type="text" class="form-control bg-light" value="${equData.equipo_nivel_K} %" disabled>
                    </div>
                `;
        if (equData.modelo_tipo == "Multicolor") {
          html += `
                    <div class="col">
                        <label class="small fw-bold text-muted">Toner C</label>
                        <input type="text" class="form-control bg-light" value="${equData.equipo_nivel_C} %" disabled>
                    </div>
                    <div class="col">
                        <label class="small fw-bold text-muted">Toner M</label>
                        <input type="text" class="form-control bg-light" value="${equData.equipo_nivel_M} %" disabled>
                    </div>
                    <div class="col">
                        <label class="small fw-bold text-muted">Toner Y</label>
                        <input type="text" class="form-control bg-light" value="${equData.equipo_nivel_Y} %" disabled>
                    </div>
                `;
        }
        if (equData.modelo_resi == 1) {
          html += `
                    <div class="col">
                        <label class="small fw-bold text-muted">Bote Residual</label>
                        <input type="text" class="form-control bg-light" value="${equData.equipo_nivel_R} %" disabled>
                    </div>
            `;
        }
        html += `
                </div>
            `;

      } else {
        // --- EL EQUIPO ES NUEVO ---
        const Modelos = equipoData['Data']['Modelos'];
        const Proveedores = equipoData['Data']['Proveedores'];

        // 2. REHABILITAR BOTÓN
        btnEnviar.disabled = false;
        btnEnviar.classList.remove("opacity-50", "btn-secondary");
        btnEnviar.classList.add("btn-primary", "shadow");

        html = `
                <div class="text-center mb-4">
                  <span class="badge bg-success px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> SERIE DISPONIBLE PARA REGISTRO
                  </span>
                </div>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase">Modelo</label>
                    <select class="form-select" id="equipo_modelo_id" name="equipo_modelo_id" required>
                      <option selected disabled>Selecciona...</option>
                `;
        Modelos.forEach(mod => {
          html += `<option data-tipo="${mod.modelo_tipo}" data-resi="${mod.modelo_resi}" value="${mod.modelo_id}">${mod.modelo_linea} ${mod.modelo_modelo}</option>`;
        });
        html += `
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase">Proveedor</label>
                    <select class="form-select" name="equipo_provE_id" required>
                      <option selected disabled>Selecciona...</option>
                `;
        Proveedores.forEach(provE => {
          html += `<option value="${provE.provE_id}">${provE.provE_nombre}</option>`;
        });
        html += `
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="small fw-bold text-muted text-uppercase">Estado Inicial</label>
                    <select class="form-select" name="equipo_estado">
                      <option value="Espera" selected>Espera</option>
                      <option value="Reparacion">Reparacion</option>
                    </select>
                  </div>
                </div>
                `;
        blockNiveles.style.display = 'block';
      }
      container.innerHTML = html;
      if (typeof select2 === "function") select2();

      $('#equipo_modelo_id').on('select2:select', function(e) {
        // Obtenemos el elemento seleccionado a través de los datos del evento
        const data = e.params.data.element;

        // Accedemos a los atributos data-
        const tipo = data.dataset.tipo;
        const resi = data.dataset.resi;

        // Aquí puedes disparar la lógica según el tipo (ej. mostrar/ocultar campos de color)
        if (tipo === "Multicolor") {
          seccColores.forEach(color => {
            color.style.display = 'block';
          });
        } else {
          seccColores.forEach(color => {
            color.style.display = 'none';
          });
          inputC.value = 0;
          inputM.value = 0;
          inputY.value = 0;
        }
        if (resi == 1) {
          seccResidual.style.display = 'block';
        } else {
          seccResidual.style.display = 'none';
          inputR.value = 0;
        }
      });

    });
  }

  if (document.getElementById("equipo_serie_edit")) {
    document.getElementById("equipo_serie_edit").addEventListener("blur", async function() {
      const serieNueva = this.value.trim();
      const idActual = document.getElementById("equipo_actual_id").value;
      const btnSubmit = document.getElementById("btn-actualizar");
      const msgContainer = document.getElementById("msg-error-serie");

      if (serieNueva === "") return;

      // Consultamos la serie
      const resp = await consultaFetch('equSerie', serieNueva);

      if (resp['Estado']) {
        const dataEncontrada = resp['Data'][0];

        // VALIDACIÓN CRUCIAL: ¿El equipo encontrado es diferente al que edito?
        if (dataEncontrada.equipo_id !== idActual) {
          // BLOQUEAR: Es un duplicado real
          btnSubmit.disabled = true;
          btnSubmit.classList.add("opacity-50");
          msgContainer.innerHTML = `
                    <div class="text-danger small mt-1 fw-bold">
                        <i class="fas fa-times-circle"></i> Error: Esta serie ya pertenece al equipo ${dataEncontrada.modelo_linea} ${dataEncontrada.modelo_modelo}.
                    </div>`;
          this.classList.add("is-invalid");
        } else {
          // LIBERAR: Es el mismo equipo, no hay problema
          resetValidation(this, btnSubmit, msgContainer);
        }
      } else {
        // LIBERAR: La serie es nueva o no existe en otros registros
        resetValidation(this, btnSubmit, msgContainer);
      }
    });
  }

  function resetValidation(input, btn, msg) {
    btn.disabled = false;
    btn.classList.remove("opacity-50");
    input.classList.remove("is-invalid");
    input.classList.add("is-valid");
    msg.innerHTML = "";
  }

  function updateRangeLabel(input, labelId) {
    const label = document.getElementById(labelId);
    if (label) {
      label.innerText = input.value + "%";
      // Cambio de color visual dinámico según el nivel
      if (input.value < 20) {
        label.classList.add("text-danger", "fw-bold");
      } else {
        label.classList.remove("text-danger", "fw-bold");
      }
    }
  }

  // Modal de ventana Editar Categoria
  if (document.querySelector(".btn-editEquContacto")) {
    document.querySelectorAll('.btn-editEquContacto').forEach((elem) => {
      elem.addEventListener("click", async function(e) {
        document.getElementById("frmEquipoContacto").name = "equipoContactoEdit";
        document.getElementById("frmEquipoContacto").value = elem.dataset.value;

        Swal.fire({
          title: "Editar Contacto",
          text: "Deseas continuar?",
          icon: "warning",
        }).then(async function(res) {
          if (res.isConfirmed) {
            var Result = await QRYajax('QRYresByIDenc', 'equipos_contactos', elem.dataset.value);
            if (Result.Status) {
              var Data = Result.Data[0];
              document.getElementById("equCon_nombre").value = Data['equCon_nombre'];
              document.getElementById("equCon_correo").value = Data['equCon_correo'];
              document.getElementById("equCon_host").value = Data['equCon_host'];
              document.getElementById("equCon_ruta").value = Data['equCon_ruta'];
              document.getElementById("equCon_usuario").value = Data['equCon_usuario'];
              document.getElementById("equCon_clave").value = Data['equCon_clave'];

              document.getElementById("btn-submit").classList.replace('btn-primary', 'btn-warning');
              document.getElementById("btn-submit").innerHTML = '<i class="fas fa-edit"></i> ACTUALIZAR CONTACTO';

            } else {
              Swal.fire({
                title: "Ocurrio un error.",
                text: "No hay resultados",
                icon: "error",
              });
            }
          }
        });
      })
    })
  }
</script>