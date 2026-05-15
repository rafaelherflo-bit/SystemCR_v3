<script>
  tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", 2);

  // Modal de ventana para imprimir
  if (document.getElementById("btnContrAct")) {
    document.getElementById("btnContrAct").addEventListener("click", async function(e) {
      e.preventDefault();

      var titleModal = "CONTRATOS ACTIVOS";
      var url = SERVERURL + "vista/formats/printRentsSto.php";
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = '<embed src="' + url + '" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">';
      $("#modalFull").modal("show");
    })
  }

  if (document.getElementById("btnPrintDetalleContrato")) {
    document.getElementById("btnPrintDetalleContrato").addEventListener("click", async function(e) {
      e.preventDefault();
      const firmaEstado = document.getElementById("btnPrintDetalleContrato").dataset.estatus;
      if (firmaEstado == 0) {
        const contratoId = document.getElementById("btnPrintDetalleContrato").dataset.contrato;
        var titleModal = "DETALLES DE CONTRATO";
        var url = SERVERURL + "vista/formats/printDetalleContrato.php?id=" + contratoId;
      } else {
        const folio = document.getElementById("btnPrintDetalleContrato").dataset.folio;
        var titleModal = "CONTRATO FIRMADO";
        var url = SERVERURL + `DocsCR/Contratos/${folio}.pdf`;
      }
      document.getElementById("modalTitleFull").innerText = titleModal;
      document.getElementById("modalBodyFull").innerHTML = `<embed src="${url}" height="100%" width="100%" X-Frame-Options="SAMEORIGIN">`;
      $("#modalFull").modal("show");
    })
  }

  if (document.getElementById("map_addContrato")) {

    var map = L.map('map_addContrato').setView([21.160867273665243, -86.85246894571384], 13);
    L.tileLayer(mapTheme1, {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var popup = L.popup();

    function onMapClick(e) {

      var rentaCoords = e.latlng;

      popup
        .setLatLng(e.latlng)
        .setContent("La nueva renta se encuentra Aqui " + rentaCoords.lat + ", " + rentaCoords.lng)
        .openOn(map);


      document.getElementById("renta_coor").value = rentaCoords.lat + ", " + rentaCoords.lng;
    }

    map.on('click', onMapClick);
  }

  if (document.getElementById("equipo_id")) {
    $('#equipo_id').on('change', async function() {
      var equipoIDenc = $(this).val();
      var dataEquQRY = await QRYajax(0, "equipo_id_enc", equipoIDenc);

      // Validacion de equipo existente.
      if (dataEquQRY.Status) {
        dataEquQRY = dataEquQRY.Data[0];
      } else {
        return;
      }

      if (dataEquQRY['modelo_tipo'] == "Monocromatico") {
        document.getElementById("DIV_renta_inc_col").style.display = "none";
        document.getElementById("DIV_renta_exc_col").style.display = "none";
        document.getElementById("col_lectura_col").style.display = "none";
        document.getElementById("col_equipo_nivel_K").style.display = "block";
        document.getElementById("col_equipo_nivel_M").style.display = "none";
        document.getElementById("col_equipo_nivel_C").style.display = "none";
        document.getElementById("col_equipo_nivel_Y").style.display = "none";
        document.getElementById("col_equipo_nivel_R").style.display = "block";
      } else {
        document.getElementById("DIV_renta_inc_col").style.display = "block";
        document.getElementById("DIV_renta_exc_col").style.display = "block";
        document.getElementById("col_lectura_col").style.display = "block";
        document.getElementById("col_equipo_nivel_K").style.display = "block";
        document.getElementById("col_equipo_nivel_M").style.display = "block";
        document.getElementById("col_equipo_nivel_C").style.display = "block";
        document.getElementById("col_equipo_nivel_Y").style.display = "block";
        document.getElementById("col_equipo_nivel_R").style.display = "block";
      }

      document.getElementById("dataStock").style.display = "block";
      document.getElementById("renta_stock").innerHTML = "";
      document.getElementById("stock1").checked = true;
      document.getElementById("stock2").checked = false;
    });
  }

  if (document.getElementById('lectura_formato')) {
    document.getElementById("lectura_formato").addEventListener("change", function() {
      if (document.getElementById("lectura_formato").checked) {
        document.getElementById('div_lectura_formato').innerHTML = `<input type="file" class="form-control" name="lectura_formato" accept="image/jpeg">`;
      } else {
        document.getElementById('div_lectura_formato').innerHTML = ``;
      }
    })
  }

  if (document.getElementById('lectura_estado')) {
    document.getElementById("lectura_estado").addEventListener("change", function() {
      if (document.getElementById("lectura_estado").checked) {
        document.getElementById('div_lectura_estado').innerHTML = `<input type="file" class="form-control" name="lectura_estado" accept="image/jpeg">`;
      } else {
        document.getElementById('div_lectura_estado').innerHTML = ``;
      }
    })
  }
</script>