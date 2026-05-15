    <!--=============================================
	=            Include JavaScript files           =
	==============================================-->
    <script>
      let btn_salir = document.querySelector('.btn-exit-system');
      btn_salir.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Cerrar Session',
          text: "Quieres cerrar la sesion?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'Si',
          cancelButtonColor: '#d33',
          cancelButtonText: 'No'
        }).then(async function(result) {
          if (result.isConfirmed) {
            let id = "<?= encryption($_SESSION['id']); ?>";
            let usuario = "<?= encryption($_SESSION['usuario']); ?>";

            let datos = new FormData();
            datos.append("id", id);
            datos.append("usuario", usuario);


            var res = await fetch(SERVERURL + "/ajax/sessionsAjax.php", {
              method: "POST",
              body: JSON.stringify({
                id,
                usuario,
              }),
              headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
              },
            });
            const resJson = await res.json();
            return alertas_ajax(resJson);
          }
        });
      });
    </script>
    <!-- jQuery V3.4.1 -->
    <script src=<?= SERVERURL . "vista/js/jquery-3.4.1.min.js"; ?>></script>

    <!-- popper -->
    <script src=<?= SERVERURL . "vista/js/popper.min.js"; ?>></script>

    <!-- Bootstrap V5 -->
    <script src=<?= SERVERURL . "vista/js/bootstrap.bundle.min.js"; ?>></script>
    <script src=<?= SERVERURL . "vista/js/dataTables.min.js"; ?>></script>
    <script src=<?= SERVERURL . "vista/js/dataTables.bootstrap5.min.js"; ?>></script>

    <!-- Select2 V1.1 -->
    <script src=<?= SERVERURL . "vista/js/select2.js"; ?>></script>

    <!-- jQuery Custom Content Scroller V3.1.5 -->
    <!-- <script src=<?= SERVERURL . "vista/js/jquery.mCustomScrollbar.concat.min.js"; ?>></script> -->

    <!-- Mapa Leaflet 1.9.4 -->
    <script
      src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
      crossorigin=""></script>

    <script src=<?= SERVERURL . "vista/js/alertas.js"; ?>></script>
    <script>
      const SERVERDIR = "<?= SERVERDIR ?>";
      const SERVERURL = "<?= SERVERURL ?>";
    </script>
    <script src=<?= SERVERURL . "vista/js/main.js"; ?>></script>

    <script>
      if (document.querySelector(".btn-PDF")) {
        document.querySelectorAll(".btn-PDF").forEach((elem) => {
          elem.addEventListener("click", async function(e) {
            const btnPDF = elem.dataset.pdf;
            const Pagina = btnPDF.toLowerCase().charAt(0).toUpperCase() + btnPDF.toLowerCase().slice(1);
            document.getElementById("modalTitleFull").innerText = btnPDF;
            document.getElementById("modalBodyFull").innerHTML = `<embed src="` + SERVERURL + `vista/formats/Almacen` + Pagina + `.php" height="100%" width = "100%" X-Frame-Options="SAMEORIGIN">`;
            $("#modalFull").modal("show");
          });
        });
      }
    </script>