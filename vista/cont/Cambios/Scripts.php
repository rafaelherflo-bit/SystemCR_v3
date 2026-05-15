<script>
  tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", <?php echo $DT_orderCol; ?>);

  if (document.getElementById("buscarCustom")) {
    document.getElementById("buscarCustom").addEventListener("click", function() {
      window.location.href = SERVERURL + "Cambios/Custom/" + document.getElementById("anioCustom").value + "/" + document.getElementById("mesCustom").value;
    });
  }

  $("#cambio_motivo").change(function() {
    if (this.value == "Por Reparacion") {
      Swal.fire({
        title: "Espera",
        text: "Es obligatorio tener los contadores para realizar esta operacion, asegurate de extraerlos del equipo retirado.",
        icon: "info",
        confirmButtonText: 'Aceptar'
      });
    }
  });

  if (document.querySelector(".cambioAction")) {
    document.querySelectorAll('.cambioAction').forEach((elem) => {
      elem.addEventListener("click", async function(event) {
        const cambioIDenc = elem.id;
        const typeAction = elem.dataset.type;
        if (typeAction == "PDF") {
          Swal.fire({
            title: "Visualizar Evidencia?",
            text: "",
            icon: "info",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              var data = await QRYajax(0, 'cambio_id_enc', cambioIDenc);
              if (data.Status) {
                data = data.Data[0];
                // console.log(data)
                const cambio_fecha = data['cambio_fecha'].split('-');
                const titleModal = data['cambio_folio'] + " | " + data['cliente_rs'] + " (" + data['contrato_folio'] + "-" + data['renta_folio'] + " - " + data['renta_depto'] + ") | Fecha de Cambio: " + data['cambio_fecha'];
                const url = SERVERURL + 'DocsCR/CambiosDeEquipos/' + cambio_fecha[0] + '/' + cambio_fecha[1] + '/' + data['cambio_folio'] + '.pdf';
                const embed = `<embed src="` + url + `" height="100%" width="100%" >`;
                document.getElementById("modalTitleFull").innerText = titleModal;
                document.getElementById("modalBodyFull").innerHTML = embed;
                $('#modalFull').modal('show');
              } else {
                Swal.fire({
                  title: "Error",
                  text: "No se encontro el registro solicitado.",
                  icon: "error",
                })
              }
            }
          })
        } else if (typeAction == "EDIT") {
          Swal.fire({
            title: "Editar Registro?",
            text: "",
            icon: "info",
            confirmButtonText: 'Confirmar',
          }).then(async function(result) {
            if (result.isConfirmed) {
              var data = await QRYajax(0, 'cambio_id_enc', cambioIDenc);
              if (data.Status) {
                window.location.href = SERVERURL + "Cambios/Edit/" + cambioIDenc;
              } else {
                Swal.fire({
                  title: "Error",
                  text: "No se encontro el registro solicitado.",
                  icon: "error",
                })
              }
            }
          })

        }
      });
    });
  };

  if (document.getElementById('cambio_file_box')) {
    document.getElementById("cambio_file_box").addEventListener("change", function() {
      if (document.getElementById("cambio_file_box").checked) {
        document.getElementById('div_cambio_file').innerHTML = `<input type="file" class="form-control" name="cambio_file" accept="application/pdf">`;
      } else {
        document.getElementById('div_cambio_file').innerHTML = ``;
      }
    })
  }

  if (document.getElementById("delCamPDF")) {
    document.getElementById("delCamPDF").addEventListener("click", async function(e) {
      e.preventDefault();
      const camID = this.dataset.id;
      const camFolio = this.dataset.folio;
      const camFecha = this.dataset.fecha;
      Swal.fire({
        title: "Borrar Evidencia de Cambio?",
        text: "",
        icon: "warning",
        confirmButtonText: 'Confirmar',
      }).then(async function(result) {
        if (result.isConfirmed) {
          var data = await QRYajax(1, 'delCamPDF', camID, camFecha, camFolio);
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
</script>