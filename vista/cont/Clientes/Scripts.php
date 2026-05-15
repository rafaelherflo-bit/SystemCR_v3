<script>
  tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", <?php echo $DT_orderCol; ?>);

  if (document.getElementById('input_rfc')) {
    document.getElementById('input_rfc').addEventListener('input', function(e) {
      const rfc = e.target.value.trim();

      const cliente_tipo = document.getElementById('cliente_tipo');
      const titleTipo = document.getElementById('titleTipo');

      // Estos necesitan estar si es Fisica
      const seccion_fisica = document.querySelectorAll('.seccion_fisica');
      const seccion_moral = document.querySelectorAll('.seccion_moral');

      const cliente_curp = document.getElementById('cliente_curp');
      const cliente_nombre = document.getElementById('cliente_nombre');
      const cliente_apellido1 = document.getElementById('cliente_apellido1');
      const cliente_apellido2 = document.getElementById('cliente_apellido2');
      const cliente_nombreComercial = document.getElementById('cliente_nombreComercial');

      const cliente_regCap = document.getElementById('cliente_regCap');

      if (rfc.length >= 12) {
        if (rfc.length === 13) {
          seccion_fisica.forEach(element => {
            element.classList.remove('d-none');
          });
          seccion_moral.forEach(element => {
            element.classList.add('d-none');
          });
          cliente_tipo.value = "Fisica";
          titleTipo.innerText = "de Tipo: Fisica";

          cliente_regCap.value = "";
        } else {
          seccion_fisica.forEach(element => {
            element.classList.add('d-none');
          });
          seccion_moral.forEach(element => {
            element.classList.remove('d-none');
          });
          cliente_tipo.value = "Moral";
          titleTipo.innerText = "de Tipo: Moral";

          cliente_curp.value = "";
          cliente_nombre.value = "";
          cliente_apellido1.value = "";
          cliente_apellido2.value = "";
          cliente_nombreComercial.value = "";
        }
      }
    });
  }
</script>