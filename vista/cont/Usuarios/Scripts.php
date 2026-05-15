<script>
    tablaDatos(<?= $DT_pageLength; ?>, "<?= $DT_orderType; ?>", 1);

    if (document.querySelector(".filaTBody")) {
        document.querySelectorAll('.filaTBody').forEach((elem) => {
            elem.addEventListener("click", async function(event) {
                const usuario_id = elem.id;
                fetch(SERVERURL + "ajax/queryUserAjax.php", {
                        method: "POST",
                        body: JSON.stringify({
                            usuario_id
                        }),
                        headers: {
                            Accept: "application/json",
                            "Content-Type": "application/json",
                        },
                    }).then((res) => {
                        if (res.ok) {
                            return res.json();
                        }
                        throw new Error('Something went wrong');
                    })
                    .then((resJson) => {
                        document.getElementById("usuario_id_Upd").value = usuario_id;
                        document.getElementById("usuario_usuario_Upd").value = resJson.usuario_usuario;
                        document.getElementById("usuario_nombre_Upd").value = resJson.usuario_nombre;
                        document.getElementById("usuario_apellido_Upd").value = resJson.usuario_apellido;
                        document.getElementById("usuario_email_Upd").value = resJson.usuario_email;
                        document.getElementById("usuario_telefono_Upd").value = resJson.usuario_telefono;
                        document.getElementById("usuario_direccion_Upd").value = resJson.usuario_direccion;
                        $('#modalFormUpd').modal('show');
                    })
                    .catch((err) => {
                        Swal.fire({
                            title: "Ocurrio un error.",
                            text: "No existe el usuario solicitado.",
                            icon: "error",
                        });
                    });
            });
        });
    }
</script>