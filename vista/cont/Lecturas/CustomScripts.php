<script>
  // if (document.querySelector('.btn-renta-factura')) {
  //   document.querySelectorAll('.btn-renta-factura').forEach((elem) => {

  //     elem.addEventListener("click", async function() {

  //       const rentaIDenc = elem.dataset.renta;
  //       const mes = elem.dataset.mes;
  //       const anio = elem.dataset.anio;
  //       const estado = parseInt(elem.dataset.estado);

  //       console.log(estado, typeof(estado));

  //       if (estado === 0) {
  //         // Alerta para solicitar el archivo .zip
  //         Swal.fire({
  //           title: "Cargar archivo de facturación",
  //           html: `
  //                 <div style="text-align: left;">
  //                   <label style="font-weight: bold; margin-bottom: 5px; display: block;">Selecciona el archivo .zip:</label>
  //                   <input type="file" id="swal-file" accept=".zip" class="swal2-file" style="margin-top: 0;">

  //                   <label style="font-weight: bold; margin-top: 15px; margin-bottom: 5px; display: block;">Identificador de la factura:</label>
  //                   <input type="text" id="swal-identificador" class="swal2-input" placeholder="Ingresa el identificador" style="margin: 0; width: 90%;">
  //                 </div>
  //               `,
  //           showCancelButton: true,
  //           confirmButtonText: "Procesar y guardar",
  //           cancelButtonText: "Cancelar",
  //           preConfirm: () => {
  //             const fileInput = document.getElementById('swal-file').files[0];
  //             const identificador = document.getElementById('swal-identificador').value;

  //             if (!fileInput) {
  //               Swal.showValidationMessage("¡Es necesario seleccionar al menos un archivo!");
  //               return false;
  //             }
  //             if (!fileInput.name.endsWith('.zip')) {
  //               Swal.showValidationMessage("¡Solo se permiten archivos con extensión .zip!");
  //               return false;
  //             }
  //             if (!identificador || identificador.trim() === "") {
  //               Swal.showValidationMessage("¡El identificador es obligatorio!");
  //               return false;
  //             }

  //             return {
  //               file: fileInput,
  //               identificador: identificador
  //             };
  //           }
  //         }).then(async (result) => {
  //           if (result.isConfirmed) {
  //             const archivoFile = result.value.file;
  //             const identificador = result.value.identificador;

  //             try {
  //               const reader = new FileReader();
  //               reader.readAsDataURL(archivoFile);

  //               reader.onload = async function() {
  //                 const base64Data = reader.result.split(',')[1];

  //                 try {
  //                   // Nota: Agregamos el identificador al final de los parámetros ($params[6])
  //                   const respuesta = await QRYajax(
  //                     "rentas_facturas", // $params[0]
  //                     "subir_zip", // $params[1]
  //                     base64Data, // $params[2]
  //                     rentaIDenc, // $params[3]
  //                     mes, // $params[4]
  //                     anio, // $params[5]
  //                     identificador // $params[6]
  //                   );

  //                   if (respuesta.Status) {
  //                     Swal.fire({
  //                       title: "¡Éxito!",
  //                       text: `Archivo procesado. Folio generado: ${respuesta.Data.folioGenerado}`,
  //                       icon: "success"
  //                     }).then(function() {
  //                       location.reload();
  //                     });
  //                   } else {
  //                     Swal.fire("Error", respuesta.Data, "error");
  //                   }
  //                 } catch (error) {
  //                   console.error("Error en la petición:", error);
  //                   Swal.fire("Error", "Ocurrió un problema al procesar el archivo.", "error");
  //                 }
  //               };

  //               reader.onerror = function(error) {
  //                 console.error("Error al leer el archivo: ", error);
  //                 Swal.fire("Error", "No se pudo leer el archivo.", "error");
  //               };

  //             } catch (error) {
  //               console.error("Error al procesar el archivo:", error);
  //             }
  //           }
  //         });
  //       } else {}
  //     });
  //   });
  // }

  if (document.querySelector('.btn-renta-factura')) {
    document.querySelectorAll('.btn-renta-factura').forEach((elem) => {

      elem.addEventListener("click", async function() {
        const rentaIDenc = elem.dataset.renta;
        const mes = elem.dataset.mes;
        const anio = elem.dataset.anio;
        const folio = elem.dataset.folio; // Obtenemos el folio
        const identificador = elem.dataset.identificador; // Obtenemos el identificador
        const estado = parseInt(elem.dataset.estado);

        console.log(estado, typeof(estado));

        if (estado === 0) {
          // === ESTADO 0: SUBIR ARCHIVO Y REGISTRO ===
          Swal.fire({
            title: "Cargar archivo de facturación",
            html: `
            <div style="text-align: left;">
              <label style="font-weight: bold; margin-bottom: 5px; display: block;">Selecciona el archivo .zip:</label>
              <input type="file" id="swal-file" accept=".zip" class="swal2-file" style="margin-top: 0;">
              
              <label style="font-weight: bold; margin-top: 15px; margin-bottom: 5px; display: block;">Identificador de la factura:</label>
              <input type="text" id="swal-identificador" class="swal2-input" placeholder="Ingresa el identificador" style="margin: 0; width: 90%;">
            </div>
          `,
            showCancelButton: true,
            confirmButtonText: "Procesar y guardar",
            cancelButtonText: "Cancelar",
            preConfirm: () => {
              const fileInput = document.getElementById('swal-file').files[0];
              const identificadorInput = document.getElementById('swal-identificador').value;

              if (!fileInput) {
                Swal.showValidationMessage("¡Es necesario seleccionar al menos un archivo!");
                return false;
              }
              if (!fileInput.name.endsWith('.zip')) {
                Swal.showValidationMessage("¡Solo se permiten archivos con extensión .zip!");
                return false;
              }
              if (!identificadorInput || identificadorInput.trim() === "") {
                Swal.showValidationMessage("¡El identificador es obligatorio!");
                return false;
              }

              return {
                file: fileInput,
                identificador: identificadorInput
              };
            }
          }).then(async (result) => {
            if (result.isConfirmed) {
              const archivoFile = result.value.file;
              const identificadorValue = result.value.identificador;

              try {
                const reader = new FileReader();
                reader.readAsDataURL(archivoFile);

                reader.onload = async function() {
                  const base64Data = reader.result.split(',')[1];

                  try {
                    const respuesta = await QRYajax(
                      "rentas_facturas", // $params[0]
                      "subir_zip", // $params[1]
                      base64Data, // $params[2]
                      rentaIDenc, // $params[3]
                      mes, // $params[4]
                      anio, // $params[5]
                      identificadorValue // $params[6]
                    );

                    if (respuesta.Status) {
                      Swal.fire({
                        title: "¡Éxito!",
                        text: `Archivo procesado. Folio generado: ${respuesta.Data.folioGenerado}`,
                        icon: "success"
                      }).then(function() {
                        location.reload();
                      });
                    } else {
                      Swal.fire("Error", respuesta.Data, "error");
                    }
                  } catch (error) {
                    console.error("Error en la petición:", error);
                    Swal.fire("Error", "Ocurrió un problema al procesar el archivo.", "error");
                  }
                };

                reader.onerror = function(error) {
                  console.error("Error al leer el archivo: ", error);
                  Swal.fire("Error", "No se pudo leer el archivo.", "error");
                };

              } catch (error) {
                console.error("Error al procesar el archivo:", error);
              }
            }
          });

        } else if (estado === 1) {

          Swal.fire({
            title: "¿Qué deseas hacer?",
            html: `
            <div>Folio actual: <strong>${folio}</strong></div>
            <div class="d-flex flex-column gap-2 mt-3">
              <button type="button" class="swal2-confirm swal2-styled" id="btn-swal-edit" style="background-color: #3085d6; width: 100%; margin: 3px 0;">
                Editar Identificador
              </button>
              <button type="button" class="swal2-confirm swal2-styled" id="btn-swal-download" style="background-color: #28a745; width: 100%; margin: 3px 0;">
                Descargar Archivo
              </button>
              <button type="button" class="swal2-deny swal2-styled" id="btn-swal-delete" style="background-color: #d33; width: 100%; margin: 3px 0;">
                Eliminar Archivo
              </button>
            </div>
          `,
            showCancelButton: true,
            showConfirmButton: false,
            showDenyButton: false,
            cancelButtonText: "Cancelar"
          }).then(async (result) => {
            // El modal se maneja a través de los eventos de los botones personalizados
          });

          // --- MANEJO DE EVENTOS DE LOS BOTONES ---
          setTimeout(() => {
            // Editar Identificador
            document.getElementById('btn-swal-edit')?.addEventListener('click', () => {
              Swal.close();

              Swal.fire({
                title: "Editar Identificador",
                input: "text",
                inputValue: identificador,
                inputPlaceholder: "Ingresa el nuevo identificador",
                showCancelButton: true,
                inputValidator: (value) => {
                  if (!value || value.trim() === "") {
                    return "¡El identificador no puede estar vacío!";
                  }
                }
              }).then(async (editResult) => {
                if (editResult.isConfirmed) {
                  try {
                    const respuesta = await QRYajax(
                      "rentas_facturas",
                      "actualizar_identificador",
                      rentaIDenc,
                      mes,
                      anio,
                      editResult.value, // Nuevo Identificador
                      folio
                    );

                    if (respuesta.Status) {
                      Swal.fire({
                        title: "¡Éxito!",
                        text: "Identificador actualizado correctamente.",
                        icon: "success"
                      }).then(() => location.reload());
                    } else {
                      Swal.fire("Error", respuesta.Data, "error");
                    }
                  } catch (error) {
                    console.error("Error en la petición:", error);
                    Swal.fire("Error", "Ocurrió un problema al actualizar el identificador.", "error");
                  }
                }
              });
            });

            // Descargar Archivo
            document.getElementById('btn-swal-download')?.addEventListener('click', () => {
              Swal.close();

              // Lógica para descargar el archivo .zip
              // Ajusta la ruta base según el alias público de tu servidor web (Ej: /DocsCR/...)
              const link = document.createElement('a');
              link.href = `/DocsCR/rentas_facturas/${anio}/${mes}/${folio}.zip`;
              link.download = `${folio}.zip`;

              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
            });

            // Eliminar Archivo y Registro
            document.getElementById('btn-swal-delete')?.addEventListener('click', () => {
              Swal.close();

              Swal.fire({
                title: "¿Estás seguro?",
                text: "El archivo y el registro de la base de datos serán eliminados.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
              }).then(async (delResult) => {
                if (delResult.isConfirmed) {
                  try {
                    const respuesta = await QRYajax(
                      "rentas_facturas",
                      "eliminar_zip",
                      rentaIDenc,
                      mes,
                      anio,
                      folio
                    );

                    if (respuesta.Status) {
                      Swal.fire({
                        title: "¡Éxito!",
                        text: "Archivo y registro eliminados con éxito.",
                        icon: "success"
                      }).then(() => location.reload());
                    } else {
                      Swal.fire("Error", respuesta.Data, "error");
                    }
                  } catch (error) {
                    console.error("Error en la petición:", error);
                    Swal.fire("Error", "Ocurrió un problema al eliminar el archivo.", "error");
                  }
                }
              });
            });
          }, 100); // Retraso mínimo para asegurar que los elementos estén en el DOM.

          // === ESTADO 1: EDITAR IDENTIFICADOR O ELIMINAR ARCHIVO ===
          // Swal.fire({
          //   title: "¿Qué deseas hacer?",
          //   text: `Folio actual: ${folio}`,
          //   icon: "question",
          //   showCancelButton: true,
          //   showDenyButton: true,
          //   confirmButtonText: "Editar Identificador",
          //   denyButtonText: "Eliminar Archivo",
          //   cancelButtonText: "Cancelar"
          // }).then(async (result) => {
          //   if (result.isConfirmed) {

          //     // --- EDITAR IDENTIFICADOR ---
          //     Swal.fire({
          //       title: "Editar Identificador",
          //       input: "text",
          //       inputValue: identificador,
          //       inputPlaceholder: "Ingresa el nuevo identificador",
          //       showCancelButton: true,
          //       inputValidator: (value) => {
          //         if (!value || value.trim() === "") {
          //           return "¡El identificador no puede estar vacío!";
          //         }
          //       }
          //     }).then(async (editResult) => {
          //       if (editResult.isConfirmed) {
          //         try {
          //           const respuesta = await QRYajax(
          //             "rentas_facturas",
          //             "actualizar_identificador",
          //             rentaIDenc,
          //             mes,
          //             anio,
          //             editResult.value, // Nuevo Identificador
          //             folio
          //           );

          //           if (respuesta.Status) {
          //             Swal.fire({
          //               title: "¡Éxito!",
          //               text: "Identificador actualizado correctamente.",
          //               icon: "success"
          //             }).then(() => location.reload());
          //           } else {
          //             Swal.fire("Error", respuesta.Data, "error");
          //           }
          //         } catch (error) {
          //           console.error("Error en la petición:", error);
          //           Swal.fire("Error", "Ocurrió un problema al actualizar el identificador.", "error");
          //         }
          //       }
          //     });

          //   } else if (result.isDenied) {

          //     // --- ELIMINAR ARCHIVO Y REGISTRO ---
          //     Swal.fire({
          //       title: "¿Estás seguro?",
          //       text: "El archivo y el registro de la base de datos serán eliminados.",
          //       icon: "warning",
          //       showCancelButton: true,
          //       confirmButtonText: "Sí, eliminar",
          //       cancelButtonText: "Cancelar"
          //     }).then(async (delResult) => {
          //       if (delResult.isConfirmed) {
          //         try {
          //           const respuesta = await QRYajax(
          //             "rentas_facturas",
          //             "eliminar_zip",
          //             rentaIDenc,
          //             mes,
          //             anio,
          //             folio
          //           );

          //           if (respuesta.Status) {
          //             Swal.fire({
          //               title: "¡Éxito!",
          //               text: "Archivo y registro eliminados con éxito.",
          //               icon: "success"
          //             }).then(() => location.reload());
          //           } else {
          //             Swal.fire("Error", respuesta.Data, "error");
          //           }
          //         } catch (error) {
          //           console.error("Error en la petición:", error);
          //           Swal.fire("Error", "Ocurrió un problema al eliminar el archivo.", "error");
          //         }
          //       }
          //     });
          //   }
          // });
        }
      });
    });
  }

  if (document.querySelector('.btn-LectChP_edit')) {
    document.querySelectorAll('.btn-LectChP_edit').forEach((elem) => {
      elem.addEventListener("click", async function() {
        var LCPid = elem.id;
        var LCPfolio = elem.textContent
        Swal.fire({
          title: "El registro no tiene cobranza",
          text: "Que deseas realizar?",
          icon: "info",
          confirmButtonText: 'Editar',
          confirmButtonColor: "#ffe100eb",
          showCancelButton: true,
          cancelButtonText: 'Eliminar',
          cancelButtonColor: "#cd0000a5",
        }).then(async function(res) {
          if (res.isConfirmed) {
            Swal.fire({
              title: "Editar Folio",
              html: `
            <div class="row">
              <div class="col">
                <input id="LChP_folio_edit" type="text" class="swal2-input" value="` + LCPfolio + `" pattern="[a-zA-Z0-9]+">
              </div>
            </div>`,
              focusConfirm: false,
              preConfirm: () => {
                return [
                  document.getElementById('LChP_folio_edit').value
                ]
              }
            }).then(async function(result) {
              if (result.isConfirmed) {
                const LChPfolio = result.value[0];
                const editChPAjax = await QRYajax(1, "editLectChP", LChPfolio, LCPid);
                if (editChPAjax.Status) {
                  location.reload();
                } else {
                  Swal.fire({
                    title: editChPAjax.Data,
                    icon: "error"
                  })
                }
              }
            })
          } else if (res.isDismissed && res.dismiss == 'cancel') {
            const delChPAjax = await QRYajax(1, "delLectChP", LCPid);
            if (delChPAjax.Status) {
              location.reload();
            } else {
              Swal.fire({
                title: delChPAjax.Data,
                icon: "error"
              })
            }
          }
        });
      })
    })
  }

  if (document.querySelector('.btn-LectChP')) {
    document.querySelectorAll('.btn-LectChP').forEach((elem) => {
      elem.addEventListener("click", async function() {
        const elemVal = elem.dataset.value;
        const elemIDenc = elem.dataset.id;
        var btnText = elem.textContent;
        var btnClass = elem.className;

        if (elemVal == "No Checado") {
          const elemIDenc = elem.dataset.id;
          const elemMes = elem.dataset.mes;
          const elemAnio = elem.dataset.anio;
          Swal.fire({
            title: "CONTROL DE FACTURAS",
            text: "Que deseas realizar?",
            icon: "question",
            confirmButtonText: "AGREGAR FOLIO",
            confirmButtonColor: "#00d9ceff",
            showCancelButton: true,
            cancelButtonText: "INICIAR COBRANZA",
            cancelButtonColor: "#0004d9ff"
          }).then(async function(result) {
            if (result.isConfirmed) {
              Swal.fire({
                title: "Agregar Folio de Cobranza",
                html: `
                        <div class="row">
                          <div class="col">
                            <input id="LChP_folio_add" type="text" class="swal2-input" placeholder="Folio Pago o Factura" pattern="[a-zA-Z0-9]+">
                          </div>
                        </div>`,
                focusConfirm: false,
                preConfirm: () => {
                  return [
                    document.getElementById('LChP_folio_add').value
                  ]
                }
              }).then(async function(result) {
                if (result.isConfirmed) {
                  const LChPfolio = result.value[0];
                  const insertChPAjax = await QRYajax(1, "insertLectChP", elemIDenc, elemAnio, elemMes, LChPfolio);
                  if (insertChPAjax.Status) {
                    location.reload();
                  } else {
                    console.log(insertChPAjax.Data);
                  }
                }
              })
            } else if (result.isDismissed && result.dismiss == 'cancel') {
              document.getElementById("modalTitleFull").innerText = "INICIAR COBRANZA";
              document.getElementById("modalBodyFull").innerHTML =
                `<embed src="` +
                SERVERURL + "Cobranzas/Agregar" +
                `" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
              $("#modalFull").modal("show");
            }
          });
        } else if (elemVal == 0) {
          Swal.fire({
            title: "El registro no tiene cobranza",
            text: "Que deseas realizar?",
            icon: "info",
            confirmButtonText: 'Editar',
            confirmButtonColor: "#ffe100eb",
            showCancelButton: true,
            cancelButtonText: 'Eliminar',
            cancelButtonColor: "#cd0000a5",
          }).then(async function(res) {
            if (res.isConfirmed) {
              Swal.fire({
                title: "Editar Folio",
                html: `
                      <div class="row">
                        <div class="col">
                          <input id="LChP_folio_edit" type="text" class="swal2-input" value="` + btnText + `" pattern="[a-zA-Z0-9]+">
                        </div>
                      </div>`,
                focusConfirm: false,
                preConfirm: () => {
                  return [
                    document.getElementById('LChP_folio_edit').value
                  ]
                }
              }).then(async function(result) {
                if (result.isConfirmed) {
                  const LChPfolio = result.value[0];
                  const editChPAjax = await QRYajax(1, "editLectChP", LChPfolio, elemIDenc);
                  if (editChPAjax.Status) {
                    location.reload();
                  } else {
                    Swal.fire({
                      title: editChPAjax.Data,
                      icon: "error"
                    })
                  }
                }
              })
            } else if (res.isDismissed && res.dismiss == 'cancel') {
              const delChPAjax = await QRYajax(1, "delLectChP", elemIDenc);
              if (delChPAjax.Status) {
                location.reload();
              } else {
                Swal.fire({
                  title: delChPAjax.Data,
                  icon: "error"
                })
              }
            }
          });
        } else {
          Swal.fire({
            title: "Que deseas realizar?",
            icon: "info",
            confirmButtonText: 'Editar',
            confirmButtonColor: "#ffe100eb",
            showCancelButton: true,
            cancelButtonText: 'Eliminar',
            cancelButtonColor: "#cd0000a5",
            showDenyButton: true,
            denyButtonText: 'Cobranza',
            denyButtonColor: "#009acda5"
          }).then(async function(res) {
            if (res.isConfirmed) {
              Swal.fire({
                title: "Editar Folio",
                html: `
                      <div class="row">
                        <div class="col">
                          <input id="LChP_folio_edit" type="text" class="swal2-input" value="` + btnText + `" pattern="[a-zA-Z0-9]+">
                        </div>
                      </div>`,
                focusConfirm: false,
                preConfirm: () => {
                  return [
                    document.getElementById('LChP_folio_edit').value
                  ]
                }
              }).then(async function(result) {
                if (result.isConfirmed) {
                  const LChPfolio = result.value[0];
                  const editChPAjax = await QRYajax(1, "editLectChP", LChPfolio, elemIDenc);
                  if (editChPAjax.Status) {
                    location.reload();
                  } else {
                    Swal.fire({
                      title: editChPAjax.Data,
                      icon: "error"
                    })
                  }
                }
              })
            } else if (res.isDismissed && res.dismiss == 'cancel') {
              const delChPAjax = await QRYajax(1, "delLectChP", elemIDenc);
              if (delChPAjax.Status) {
                location.reload();
              } else {
                Swal.fire({
                  title: delChPAjax.Data,
                  icon: "error"
                })
              }
            } else if (res.isDenied) {
              window.location.href = "<?= SERVERURL ?>Cobranzas/idD/" + elemVal;
            }
          });
        }
      })
    })
  }

  if (document.querySelector('.btn-pCh')) {
    document.querySelectorAll('.btn-pCh').forEach((elem) => {
      elem.addEventListener("click", async function() {
        const renta_id = elem.id;
        const btn_texto = elem.textContent;
        const custMes = document.getElementById("custom_mes").value;
        const custAnio = document.getElementById("custom_anio").value;
        const facturaQuery = await consultaFetch2("existFact", renta_id, custMes, custAnio);

        if (facturaQuery.Estado == false || btn_texto == "Pagado") {
          document.forms['formRedirect'].action = SERVERURL + "Facturas";
          var input = `
                      <input type='hidden' name='renta_id' value='` + renta_id + `'>
                      <input type='hidden' name='custom_mes' value='` + custMes + `'>
                      <input type='hidden' name='custom_anio' value='` + custAnio + `'>
                      `;
          document.getElementById("formRedirect").innerHTML = input;
          document.forms["formRedirect"].submit();
        } else {
          Swal.fire({
            title: "CONTROL DE FACTURAS",
            text: "Que deseas revisar?",
            icon: "question",
            confirmButtonText: "FACTURA",
            confirmButtonColor: "#00a1d9",
            showCancelButton: true,
            cancelButtonText: "PAGOS",
            cancelButtonColor: "#03d900"
          }).then(async function(result) {
            if (result.isConfirmed) {
              document.forms['formRedirect'].action = SERVERURL + "Facturas";
              var input = `
                                    <input type='hidden' name='renta_id' value='` + renta_id + `'>
                                    <input type='hidden' name='custom_mes' value='` + custMes + `'>
                                    <input type='hidden' name='custom_anio' value='` + custAnio + `'>
                                    `;
              document.getElementById("formRedirect").innerHTML = input;
              document.forms["formRedirect"].submit();
            } else if (result.isDismissed) {
              if (result.dismiss == 'cancel') {

                const facturaData = facturaQuery.Data[0];
                var pCh_id = facturaData['pCh_id'];
                var pCh_noFact = facturaData['pCh_noFact'];
                var pCh_fechaFact = facturaData['pCh_fechaFact'];
                var pCh_subTFact = facturaData['pCh_subTFact'];
                var IVAs = facturaData['pCh_ivaFact'] / 100;
                IVAs = IVAs * pCh_subTFact;
                var pCh_totalFact = parseFloat(pCh_subTFact) + IVAs;
                var pCh_PAGO = pCh_totalFact;
                var pCh_PAGADO = 0;

                var html = `
                  <input type="hidden" class="form-control" id="pCh_id" name="pCh_id" value="` + pCh_id + `">
                  <fieldset class="form-neon"><legend><i class="fas fa-money-bill-wave"></i></i> &nbsp; PAGOS DE FACTURA | ` + pCh_noFact + `</legend>
                    <div class="container-fluid">
                  `;

                const paysQuery = await consultaFetch("existPays", pCh_id);
                if (paysQuery.Estado) {
                  pCh_PAGO = 0;
                  var paysData = paysQuery.Data;
                  html += `
                          <div class="row">
                            <div class="col">
                              <fieldset class="form-neon"><legend><i class="fas fa-handshake"></i>
                                </i> &nbsp; REALIZADOS</legend>
                                <div class="table-responsive">
                                  <table class="table table-dark table-sm">
                                    <thead>
                                      <tr>
                                        <th>
                                          <center>FECHA</center>
                                        </th>
                                        <th>
                                          <center>CANTIDAD</center>
                                        </th>
                                        <th>
                                          <center>COMENTARIO</center>
                                        </th>
                                        <th>
                                          <center>ACCIONES</center>
                                        </th>
                                      </tr>
                                    </thead>
                                    <tbody>
                              `;
                  for (let i = 0; i < paysData.length; i++) {
                    html += `
                                      <tr>
                                        <td>
                                        ` + paysData[i]['pCH_fechaPago'] + `
                                        </td>
                                        <td>
                                        ` + paysData[i]['pCH_cantPago'] + `
                                        </td>
                                        <td>
                                        ` + paysData[i]['pCH_comm'] + `
                                        </td>
                                        <td>
                                          <span class="btn btn-warning btn-del-pCH" id="` + paysData[i]['pCH_id'] + `">Eliminar</span>
                                        </td>
                                      </tr>
                            `;
                    pCh_PAGO = parseFloat(paysData[i]['pCH_cantPago']) + pCh_PAGO;
                  }
                  html += `
                                    </tbody>
                                  </table>
                                </div>
                              </fieldset>
                            </div>
                          </div>
                          <br>
                  `;
                  pCh_PAGADO = pCh_PAGO;
                  pCh_PAGO = pCh_totalFact - pCh_PAGO;
                }

                html += `
                        <div class="row">
                          <div class="col">
                            <div class="form-group">
                              <label for="pCH_fechaPago" class="bmd-label-floating">FECHA DE PAGO</label>
                              <input class="form-control" type="date" required id="pCH_fechaPago" name="pCH_fechaPago" value="` + pCh_fechaFact + `" pattern="\\d{4}-\\d{2}-\\d{2}" title="Ingrese la fecha en formato AAAA-MM-DD">
                            </div>
                          </div>
                          <div class="col">
                            <div class="form-group">
                              <label for="pCH_cantPago" class="bmd-label-floating">PAGO + IVA</label>
                              <input class="form-control" type="text" placeholder="0.00" required id="pCH_cantPago" name="pCH_cantPago" min="0" value="` + pCh_PAGO + `" step="0.00" title="Pago con IVA" pattern="^-?\\d+(\\.\\d+)?$">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col">
                            <div class="form-group">
                              <label for="pCH_comm" class="bmd-label-floating">COMENTARIO DE PAGO</label>
                              <textarea class="form-control" placeholder="Descripcion de forma de pago" required id="pCH_comm" name="pCH_comm" title="Descripcion de forma de pago"></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-md-center">
                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label for="pCH_cantPago" class="bmd-label-floating">TOTAL DE FACTURA CON IVA</label>
                              <input class="form-control" type="text" disabled value="` + pCh_totalFact + `">
                            </div>
                          </div>
                          <div class="col-lg-auto">
                            <div class="form-group">
                              <label for="pCH_cantPago" class="bmd-label-floating">TOTAL PAGADO</label>
                              <input class="form-control" type="text" disabled value="` + pCh_PAGADO + `">
                            </div>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  `;


                document.getElementById("modalBodyForm").innerHTML = html;
                $('#modalForm').modal('show');

                if (document.querySelector(".btn-del-pCH")) {
                  document.querySelectorAll('.btn-del-pCH').forEach((elem) => {
                    elem.addEventListener("click", async function(event) {
                      Swal.fire({
                        title: "Eliminar Pago...",
                        text: "Deseas Continuar?",
                        icon: "question",
                        confirmButtonText: "Confirmar",
                        confirmButtonColor: "#03d900",
                        showCancelButton: true,
                        cancelButtonText: "Cancelar",
                        cancelButtonColor: "#00a1d9"
                      }).then(async function(result) {
                        if (result.isConfirmed) {
                          var delPCH = await sentenciaAjax("delPCHid", elem.id);
                          if (delPCH.Estado) {
                            location.reload();
                          } else {
                            Swal.fire({
                              title: "ERROR",
                              text: delPCH.Data,
                              icon: "error"
                            });
                          }
                        }
                      })
                    })
                  })
                }


              }
            }
          });
        }
      });
    });
  }

  if (document.querySelector(".btn-lect")) {
    document.querySelectorAll('.btn-lect').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        var renta_id = elem.id;
        if (document.getElementById("custom_mes")) {
          var custom_mes = document.getElementById("custom_mes").value;
        } else {
          var custom_mes = elem.value;
        }
        var custom_anio = document.getElementById("custom_anio").value;

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

        if (TypeLect == 0) {
          Swal.fire({
            title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
            text: "Sin datos de lectura actual y anterior.",
            icon: "info",
            confirmButtonText: 'Continuar',
            confirmButtonColor: "#008341",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            cancelButtonColor: "#440d00"
          }).then(async function(res) {
            if (res.isConfirmed) {
              document.body.appendChild(mapForm);
              mapForm.submit();
            }
          });
        }

        if (TypeLect == 1) {
          Swal.fire({
            title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
            text: "Unicamente Con Lectura Actual.",
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
              valAppendChild("lectura_fecha", result.currLectData.lectura_fecha);
              valAppendChild("lectura_pdf", result.currLectData.lectura_pdf);
              document.body.appendChild(mapForm);
              mapForm.submit();
            } else if (res.isDenied) {
              var lecturaID = elem.dataset.lectura;
              location.href = SERVERURL + "Lecturas/ID/" + lecturaID;
            }
          });
        }

        if (TypeLect == 2) {
          Swal.fire({
            title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
            text: "NO tiene lectura en este periodo.",
            icon: "info",
            confirmButtonText: 'Ver Lectura',
            confirmButtonColor: "#008341",
            showCancelButton: true,
            cancelButtonText: 'Tomar Lectura',
            cancelButtonColor: "#0085cd"
          }).then(async function(res) {
            if (res.isConfirmed) {
              valAppendChild("prev_lectura_id", result.prevLectData.lectura_id);
              valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
              valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
              document.body.appendChild(mapForm);
              mapForm.submit();
            } else if (res.isDismissed && res.dismiss == 'cancel') {
              valAppendChild("print", true);
              valAppendChild("prev_lectura_id", result.prevLectData.lectura_id);
              valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
              valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
              valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
              valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
              valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);
              document.body.appendChild(mapForm);
              mapForm.submit();
            }
          });
        }

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
              valAppendChild("curr_lectura_id", result.currLectData.lectura_id);
              valAppendChild("curr_lectura_fecha", result.currLectData.lectura_fecha);
              valAppendChild("curr_lectura_pdf", result.currLectData.lectura_pdf);
              valAppendChild("curr_lectura_esc", result.currLectData.lectura_esc);
              valAppendChild("curr_lectura_bn", result.currLectData.lectura_bn);
              valAppendChild("curr_lectura_col", result.currLectData.lectura_col);

              valAppendChild("prev_reporte_id", result.prevLectData.lectura_reporte_id);
              valAppendChild("prev_lectura_id", result.prevLectData.lectura_id);
              valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
              valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
              valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
              valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
              valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);
              document.body.appendChild(mapForm);
              mapForm.submit();
            } else if (res.isDenied) {
              var lecturaID = elem.dataset.lectura;
              location.href = SERVERURL + "Lecturas/ID/" + lecturaID;
            }
          });
        }

        if (TypeLect == 4) {
          Swal.fire({
            title: result.rentaData.contrato_folio + "-" + result.rentaData.renta_folio + " | " + result.rentaData.renta_depto,
            text: "Sin lectura actual con ajuste por cambio de equipo.",
            icon: "info",
            confirmButtonText: 'Continuar',
            confirmButtonColor: "#008341",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            cancelButtonColor: "#440d00"
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

              valAppendChild("prev_lectura_id", result.prevLectData.lectura_id);
              valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
              valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
              valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
              valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
              valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);
              document.body.appendChild(mapForm);
              mapForm.submit();
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
              valAppendChild("prev_lectura_id", result.prevLectData.lectura_id);
              valAppendChild("prev_lectura_fecha", result.prevLectData.lectura_fecha);
              valAppendChild("prev_lectura_pdf", result.prevLectData.lectura_pdf);
              valAppendChild("prev_lectura_esc", result.prevLectData.lectura_esc);
              valAppendChild("prev_lectura_bn", result.prevLectData.lectura_bn);
              valAppendChild("prev_lectura_col", result.prevLectData.lectura_col);

              valAppendChild("curr_reporte_id", result.currLectData.lectura_reporte_id);
              valAppendChild("curr_lectura_id", result.currLectData.lectura_id);
              valAppendChild("curr_lectura_fecha", result.currLectData.lectura_fecha);
              valAppendChild("curr_lectura_pdf", result.currLectData.lectura_pdf);
              valAppendChild("curr_lectura_esc", result.currLectData.lectura_esc);
              valAppendChild("curr_lectura_bn", result.currLectData.lectura_bn);
              valAppendChild("curr_lectura_col", result.currLectData.lectura_col);
              document.body.appendChild(mapForm);
              mapForm.submit();
            } else if (res.isDenied) {
              var lecturaID = elem.dataset.lectura;
              location.href = SERVERURL + "Lecturas/ID/" + lecturaID;
            }
          });
        }
      });
    });
  }

  if (document.querySelector(".zona_id")) {
    document.querySelectorAll('.zona_id').forEach((elem) => {
      elem.addEventListener("click", function(event) {
        Swal.fire({
          title: "Visualizar Lecturas Restantes",
          text: "",
          icon: "info",
          confirmButtonText: 'Imprimir',
          confirmButtonColor: "#3085d6",
          showCancelButton: true,
          cancelButtonText: 'Solo Ver',
          cancelButtonColor: "#88BB89"
        }).then(async function(result) {
          const zona_id = elem.id;
          const custom_mes = document.getElementById("custom_mes").value;
          const custom_anio = document.getElementById("custom_anio").value;

          var Zona = await QRYajax(0, 'zona_id', zona_id);
          Zona = Zona.Data[0];
          var titleModal = "Lecturas Restantes de " + Zona['zona_nombre'] + " en " + document.getElementById('periodoCustom').value;
          if (result.isConfirmed) {
            var url = SERVERURL + 'vista/formats/printRestLects.php?zona_id=' + zona_id + '&custom_mes=' + custom_mes + '&custom_anio=' + custom_anio;
            var content = `<embed src="` + url + `" height="100%" width="100%" >`;
            showModal(content, titleModal);
          } else if (result.isDismissed && result.dismiss == 'cancel') {
            var content = `
                        <div class="table-responsive">
                          <table class="table table-dark table-sm">
                            <thead>
                              <tr>
                                <th></th>
                                <th>FOLIO</th>
                                <th>DEPARTAMENTO</th>
                                <th>RAZON SOCIAL</th>
                                <th>RFC</th>
                              </tr>
                            </thead>
                            <tbody>
                        `;


            var lecturaRentasZona = await QRYajax('lecturaRentasZona', zona_id, custom_mes, custom_anio);
            lecturaRentasZona = lecturaRentasZona.Data;
            for (var i = 0; i < lecturaRentasZona.length; i++) {
              var rentaZona = lecturaRentasZona[i];

              content += rentaZona.lectura_fecha != null ? '<tr class="table-success">' : '<tr>';

              content += `
                                  <td>` + (i + 1) + `</td>
                                  <td>` + rentaZona.contrato_folio + `-` + rentaZona.renta_folio + `</td>
                                  <td>` + rentaZona.renta_depto + `</td>
                                  <td>` + rentaZona.cliente_rs + `</td>
                                  <td>` + rentaZona.cliente_rfc + `</td>
                              </tr>
                              `;
            }

            content += `
                            </tbody>
                          </table>
                        </div>
                        `;
            showModal(content, titleModal);
          }

          function showModal(content, titleModal) {
            document.getElementById("modalTitleFull").innerText = titleModal;
            document.getElementById("modalBodyFull").innerHTML = content;
            $('#modalFull').modal('show');
          }
        });
      });
    });
  }
</script>