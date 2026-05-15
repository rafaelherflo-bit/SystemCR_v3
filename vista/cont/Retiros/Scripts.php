<script>
    tablaDatos(<?php echo $DT_pageLength; ?>, "<?php echo $DT_orderType; ?>", <?php echo $DT_orderCol; ?>);

if (document.getElementById("buscarCustom")) {
    document.getElementById("buscarCustom").addEventListener("click", function() {
        window.location.href = SERVERURL + "Retiros/Custom/" + document.getElementById("anioCustom").value + "/" + document.getElementById("mesCustom").value;
    });
}
</script>