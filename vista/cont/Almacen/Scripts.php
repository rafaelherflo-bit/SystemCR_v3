<script>
  if (document.getElementById("SysVista")) {
    const SysVista = document.getElementById("SysVista").dataset;
    const Vista = SysVista.vista;
    const Contenido = SysVista.cont;
    const Subcontenido = SysVista.subcont;
    // Esta seccion es para la vista de Almacen
    if (Vista == "Almacen") {

      console.log(Vista + " | " + Contenido + " | " + Subcontenido);

      // Esta seccion es para el contenido Toners
      if (Contenido == "Toners") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Lista") {
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 2);
        }
        // -------------------------------------

        // Esta seccion es para el subcontenido Agregar
        if (Subcontenido == "Agregar") {}
        // -------------------------------------

        // Esta seccion es para el subcontenido Editar
        if (Subcontenido == "Editar") {}
        // -------------------------------------
      }
      // ------------------------------

      // Esta seccion es para el contenido Refacciones
      if (Contenido == "Refacciones") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Lista") {
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", <?= $DT_orderCol; ?>);
        }
        // -------------------------------------

        // Esta seccion es para el subcontenido Agregar
        if (Subcontenido == "Agregar") {}
        // -------------------------------------

        // Esta seccion es para el subcontenido Editar
        if (Subcontenido == "Editar") {}
        // -------------------------------------
      }
      // ------------------------------

      // Esta seccion es para el contenido Proveedores
      if (Contenido == "Proveedores") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Lista") {
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", <?= $DT_orderCol; ?>);
        }
      }
      // ------------------------------

      // Esta seccion es para el contenido Equipos
      if (Contenido == "Equipos") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Lista") {
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);
        }
        // -------------------------------------

        // Esta seccion es para el subcontenido Editar
        if (Subcontenido == "Editar") {}
        // -------------------------------------
      }
      // ------------------------------

      // Esta seccion es para el contenido Otros
      if (Contenido == "Otros") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Lista") {
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);
        }
        // -------------------------------------

        // Esta seccion es para el subcontenido Editar
        if (Subcontenido == "Editar") {}
        // -------------------------------------
      }
      // ------------------------------

      // Esta seccion es para el contenido Movimientos
      if (Contenido == "Movimientos") {
        // Esta seccion es para el subcontenido Lista
        if (Subcontenido == "Agregar" || Subcontenido == "Editar") {

          if (document.getElementById('AlmM_IVA')) {
            document.getElementById('AlmM_IVA').addEventListener('input', (e) => {
              var IVAs = e.srcElement.value;
              document.getElementById('AlmM_IVA_label').innerText = "IVA AL " + IVAs + "%";
            })
          }

          if (document.getElementById("AlmM_tipo")) {
            $('#AlmM_tipo').on('select2:select', function(e) {
              const data = e.params.data;
              const valor = data.id;
              if (valor == 0) {
                var AlmM_identificador_DIV = `<div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                                                  <select class="form-select" id="AlmM_empleado" name="AlmM_empleado" data-placeholder="Selecciona un Empleado">
                                                    <option></option>
                                                    <?php
                                                    $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                                                WHERE usuario_id != 1
                                                                AND usuario_estado = 'Activo'
                                                                ORDER BY usuario_id ASC";
                                                    $uS_QRY = consultaData($uS_SQL);
                                                    foreach ($uS_QRY['dataFetch'] as $uS) {
                                                    ?>
                                                      <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " " . $uS['usuario_apellido']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>`;
              } else if (valor == 1) {
                var AlmM_identificador_DIV = `<div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                                                  <select class="form-select" id="AlmM_empleado" name="AlmM_empleado" data-placeholder="Selecciona un Empleado">
                                                    <option></option>
                                                    <?php
                                                    $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                                                WHERE usuario_id != 1
                                                                AND usuario_estado = 'Activo'
                                                                ORDER BY usuario_id ASC";
                                                    $uS_QRY = consultaData($uS_SQL);
                                                    foreach ($uS_QRY['dataFetch'] as $uS) {
                                                    ?>
                                                      <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " " . $uS['usuario_apellido']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>`;
              } else if (valor == 2) {
                var AlmM_identificador_DIV = `<div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_identificador" class="bmd-label-floating">RENTA</label>
                                                  <select class="form-select" id="AlmM_identificador" name="AlmM_identificador" data-placeholder="Selecciona una Renta">
                                                    <option></option>
                                                    <?php
                                                    $rentas_SQL = "SELECT * FROM Rentas
                                                    INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                                                    INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                                                    WHERE renta_estado = 'Activo'
                                                    ORDER BY contrato_folio ASC";
                                                    $rentas_QRY = consultaData($rentas_SQL);
                                                    foreach ($rentas_QRY['dataFetch'] as $renta) {
                                                    ?>
                                                      <option value="<?= encryption($renta['renta_id']); ?>"><?= $renta['contrato_folio'] . "-" . $renta['renta_folio'] . " | " . $renta['renta_depto']  . " | " . $renta['cliente_rfc']  . " | " . $renta['cliente_rs']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                                                  <select class="form-select" id="AlmM_empleado" name="AlmM_empleado" data-placeholder="Selecciona un Empleado">
                                                    <option></option>
                                                    <?php
                                                    $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                                                WHERE usuario_id != 1
                                                                AND usuario_estado = 'Activo'
                                                                ORDER BY usuario_id ASC";
                                                    $uS_QRY = consultaData($uS_SQL);
                                                    foreach ($uS_QRY['dataFetch'] as $uS) {
                                                    ?>
                                                      <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " " . $uS['usuario_apellido']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>`;
              } else if (valor == 3) {
                var AlmM_identificador_DIV = `<div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_identificador" class="bmd-label-floating">CLIENTE</label>
                                                  <select class="form-select" id="AlmM_identificador" name="AlmM_identificador" data-placeholder="Selecciona un Cliente">
                                                    <option></option>
                                                    <?php
                                                    $clientes_QRY = consultaData("SELECT * FROM Clientes ORDER BY cliente_rfc ASC");
                                                    foreach ($clientes_QRY['dataFetch'] as $cliente) {
                                                    ?>
                                                      <option value="<?= encryption($cliente['cliente_id']); ?>"><?= $cliente['cliente_rfc']  . " | " . $cliente['cliente_rs']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_empleado" class="bmd-label-floating">EMPLEADO</label>
                                                  <select class="form-select" id="AlmM_empleado" name="AlmM_empleado" data-placeholder="Selecciona un Empleado">
                                                    <option></option>
                                                    <?php
                                                    $uS_SQL = "SELECT usuario_id, usuario_nombre, usuario_apellido FROM Usuarios
                                                                WHERE usuario_id != 1
                                                                AND usuario_estado = 'Activo'
                                                                ORDER BY usuario_id ASC";
                                                    $uS_QRY = consultaData($uS_SQL);
                                                    foreach ($uS_QRY['dataFetch'] as $uS) {
                                                    ?>
                                                      <option value="<?= encryption($uS['usuario_id']); ?>"><?= $uS['usuario_nombre']  . " " . $uS['usuario_apellido']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                  </select>
                                                </div>
                                              </div>
                                              <div class="col-12 col-md">
                                                <div class="form-group">
                                                  <label for="AlmM_IVA" id="AlmM_IVA_label" class="bmd-label-floating">IVA AL 16%</label>
                                                  <input class="form-control" id="AlmM_IVA" name="AlmM_IVA" type="range" value="16" min="0" max="100" pattern="[0-9]{0,100}" title="Ingrese un número entre 0 y 100">
                                                </div>
                                              </div>`;
              }
              document.getElementById("AlmM_identificador_DIV").innerHTML = AlmM_identificador_DIV;
              select2();
              document.getElementById('AlmM_IVA').addEventListener('input', (e) => {
                var IVAs = e.srcElement.value;
                document.getElementById('AlmM_IVA_label').innerText = "IVA AL " + IVAs + "%";
              })
            });
          }
        }
        if (Subcontenido == "Detalles") {
          if (document.getElementById("btn-active_AlmM")) {
            document.getElementById("btn-active_AlmM").addEventListener('click', function() {
              Swal.fire({
                title: '<i class="fas fa-check"></i> &nbsp; Activando Movimiento &nbsp; <i class="fas fa-dolly"></i>',
                html: `
                      <i class="fas fa-folder-plus"></i> &nbsp; Agrega la evidencia para activar &nbsp; <i class="fas fa-file-pdf"></i>
                      <br>
                      <input type="hidden" name="active_AlmM" id="active_AlmM" value="<?= $pagina[3]; ?>">
                      <input type="hidden" class="form-control" name="usuario_admin" id="usuario_admin" value="<?= $_SESSION['usuario']; ?>">
                      <input type="hidden" class="form-control" name="clave_admin" id="clave_admin" value="<?= $_SESSION['passclave']; ?>">
                      <input type="file" class="form-control" name="AlmM_file" id="AlmM_file" accept="application/pdf">
                      `,
                focusConfirm: false,
                showCancelButton: true,
                preConfirm: () => {
                  const active_AlmM = document.getElementById('active_AlmM').value;
                  const usuario_admin = document.getElementById('usuario_admin').value;
                  const clave_admin = document.getElementById('clave_admin').value;
                  const AlmM_file = document.getElementById('AlmM_file').files[0]; // Obtiene el primer archivo

                  // Opcional: Validación
                  if (!active_AlmM || !usuario_admin || !clave_admin) {
                    Swal.showValidationMessage('Por favor, complete ambos campos de texto.');
                    return false; // Evita que se cierre el modal
                  }

                  // Devuelve un objeto con todos los valores
                  return {
                    active_AlmM,
                    usuario_admin,
                    clave_admin,
                    AlmM_file // El objeto File o undefined si no se seleccionó
                  };
                }
              }).then((result) => {
                if (result.isConfirmed) {
                  const data = result.value;

                  if (data.AlmM_file) {
                    // Aquí puedes usar FormData para enviar los datos al servidor,
                    // incluyendo el archivo. Por ejemplo, con fetch o axios.

                    const formData = new FormData();
                    formData.append('active_AlmM', data.active_AlmM);
                    formData.append('usuario_admin', data.usuario_admin);
                    formData.append('clave_admin', data.clave_admin);
                    formData.append('AlmM_file', data.AlmM_file);

                    // Ejemplo de envío (deberías implementar la lógica de AJAX real aquí)
                    fetch("<?= SERVERURL; ?>ajax/controllerAjax.php", {
                        method: 'POST',
                        body: formData
                      })
                      .then(response => response.json())
                      .then(data => {
                        return alertas_ajax(data);
                      })
                      .catch(error => {
                        return alertas_ajax(error);
                      });
                  } else {
                    Swal.fire({
                      title: 'Error al ingresar archivo',
                      text: 'Debes agregar una evidencia',
                      icon: 'warning'
                    });
                  }

                }
              });
            });
          }

          if (document.getElementById('btnRequi')) {
            document.getElementById('btnRequi').addEventListener("click", function() {
              const iD = this.dataset.id;
              document.getElementById("modalTitleFull").innerText = 'IMPRIMIR REQUISICION';
              document.getElementById("modalBodyFull").innerHTML = `<embed src="` + SERVERURL + `vista/formats/AlmacenRequi.php?iD=` + iD + `" height="100%" width = "100%" X-Frame-Options="SAMEORIGIN">`;
              $("#modalFull").modal("show");
            });
          }
          tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 4);
        }
      }
      // ------------------------------

      // Esta seccion es para el contenido Custom Day
      if (Subcontenido == "CustomDay" || Subcontenido == "CustomMonth" || Subcontenido == "CustomYear") {
        tablaDatos(<?= $DT_pageLength; ?>, "ASC", 0);
      }
      // ------------------------------
    }

    if (document.querySelector('.btnAction')) {
      document.querySelectorAll('.btnAction').forEach((elem) => {
        elem.addEventListener("click", async function() {
          var elemID = elem.id;
          var elemTipo = elem.dataset.tipo;

          if (elemTipo == "edit") {
            location.href = SERVERURL + "Almacen/" + Contenido + "/Editar/" + elemID;
          } else if (elemTipo == "details") {
            location.href = SERVERURL + "Almacen/" + Contenido + "/Detalles/" + elemID;
          } else if (elemTipo == "delete") {
            var titulo = "Eliminar Registro...";
            var texto = "Deseas Continuar?";
            var icono = "question";
            if (Contenido == "Toners") {
              var deleteAction = "delTonerIdEnc";
            } else if (Contenido == "Chips") {
              var deleteAction = "delChipIdEnc";
            } else if (Contenido == "Refacciones") {
              var deleteAction = "delRefaccionIdEnc";
            } else if (Contenido == "Servicios") {
              var deleteAction = "delServicioIdEnc";
            } else if (Contenido == "Proveedores") {
              var deleteAction = "delProveedorIdEnc";
            } else if (Contenido == "Movimientos") {
              if (Subcontenido == "Detalles") {
                var elemTipo = elem.dataset.delete;
                var deleteAction = elemTipo;
                if (elemTipo == "delAlmM") {
                  var titulo = '<i class="fas fa-exclamation-triangle"></i> Elminiando Registro Principal <i class="fas fa-exclamation-triangle"></i>';
                  var texto = 'Se eliminara todo rastro, guarda los archivos y toma nota del registro.';
                  var icono = "warning";
                }
              }
            }
            Swal.fire({
              title: titulo,
              text: texto,
              icon: icono,
              confirmButtonText: '<i class="fas fa-trash"></i>',
              confirmButtonColor: "#d9190094",
            }).then(async function(result) {
              if (result.isConfirmed) {
                deleteAction = await QRYajax(1, deleteAction, elemID);
                if (deleteAction.Status) {
                  Swal.fire({
                    title: "LISTO",
                    text: deleteAction.Data,
                    icon: "success"
                  }).then(async function(result) {
                    if (result.isConfirmed) {
                      location.reload();
                    }
                  })
                } else {
                  Swal.fire({
                    title: "ERROR",
                    text: deleteAction.Data,
                    icon: "error"
                  });
                }
              }
            })
          } else if (elemTipo == "filePDF") {
            if (elem.dataset.action == "watch") {
              document.getElementById("modalTitleFull").innerText = "EVIDENCIA";
              document.getElementById("modalBodyFull").innerHTML = `<embed src="` + SERVERURL + `DocsCR/ALMACEN/Evidencias/` + elem.dataset.folio + `.pdf" height="100%" width = "100%" X-Frame-Options="SAMEORIGIN">`;
              $("#modalFull").modal("show");
            } else if (elem.dataset.action == "delete") {
              Swal.fire({
                title: "Eliminar Evidencia PDF...",
                text: "Deseas Continuar?",
                icon: "question",
                confirmButtonText: '<i class="fas fa-trash"></i>',
                confirmButtonColor: "#d9190094",
              }).then(async function(result) {
                if (result.isConfirmed) {
                  deletePDF = await QRYajax(2, 'AlmMdelPDF', elemID);
                  if (deletePDF.Status) {
                    Swal.fire({
                      title: "LISTO",
                      text: deletePDF.Data,
                      icon: "success"
                    }).then(async function(result) {
                      if (result.isConfirmed) {
                        location.reload();
                      }
                    })
                  } else {
                    Swal.fire({
                      title: "ERROR",
                      text: deletePDF.Data,
                      icon: "error"
                    });
                  }
                }
              })
            }
          } else if (elemTipo == "init") {
            Swal.fire({
              title: "Activar Registro...",
              text: "Deseas Continuar?",
              icon: "question",
              confirmButtonText: '<i class="fas fa-check"></i>',
              confirmButtonColor: "#00d91d94",
            }).then(async function(result) {
              if (result.isConfirmed) {
                initAction = await QRYajax(1, "activarAlmM", elemID);
                if (initAction.Status) {
                  Swal.fire({
                    title: "LISTO",
                    text: initAction.Data,
                    icon: "success"
                  }).then(async function(result) {
                    if (result.isConfirmed) {
                      location.reload();
                    }
                  })
                } else {
                  Swal.fire({
                    title: "ERROR",
                    text: initAction.Data,
                    icon: "error"
                  });
                }
              }
            })
          } else if (elemTipo == "noinit") {
            Swal.fire({
              title: "Desactivar Registro...",
              text: "Deseas Continuar?",
              icon: "question",
              confirmButtonText: '<i class="fas fa-check"></i>',
              confirmButtonColor: "#d9000094",
            }).then(async function(result) {
              if (result.isConfirmed) {
                noinitAction = await QRYajax(1, "desactivarAlmM", elemID);
                if (noinitAction.Status) {
                  Swal.fire({
                    title: "LISTO",
                    text: noinitAction.Data,
                    icon: "success"
                  }).then(async function(result) {
                    if (result.isConfirmed) {
                      location.reload();
                    }
                  })
                } else {
                  Swal.fire({
                    title: "ERROR",
                    text: noinitAction.Data,
                    icon: "error"
                  });
                }
              }
            })
          }
        })
      })
    }
    // -------------------------
  }
</script>