<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/config/SERVER.php';
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['privilegio']) || !isset($_SESSION['id'])) {
    echo forceoutSession();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin="" />
    <style>
        body {
            padding: 0;
            margin: 0;
            background-color: #333;
        }

        #map-template {
            height: 100%;
            width: 100%;
            top: 0;
            position: absolute;
        }
    </style>
    <title>Mapa Lecturas</title>
</head>

<body>
    <input type="text" id="custom_mes" value="<?= $_GET['custom_mes']; ?>">
    <input type="text" id="custom_anio" value="<?= $_GET['custom_anio']; ?>">
    <div id="map-template"></div>
</body>
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
<script>
    const SERVERURL = window.location.protocol + "//" + window.location.host;

    async function mapaLecturas() {
        const map = L.map("map-template").setView(
            [21.1674382097248, -86.86569297527858],
            12
        );

        map.locate({
            enableHighAccuracy: true
        });

        map.on("locationfound", (e) => {
            var myIcon = L.icon({
                iconUrl: "/vista/assets/icons/leaf-orange.png",
                shadowUrl: '/vista/assets/icons/leaf-shadow.png',
                iconSize: [38, 95], // size of the icon
                shadowSize: [50, 64], // size of the shadow
                iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                shadowAnchor: [4, 62], // the same for the shadow
                popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
            });
            var marker = L.marker([e.latlng.lat, e.latlng.lng], {
                    icon: myIcon
                })
                .bindPopup("Estoy aqui")
                .addTo(map);
        });

        const mapTheme1 = "https://tile.openstreetmap.org/{z}/{x}/{y}.png";
        const mapTheme2 = "https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png";
        const mapTheme3 = "https://{s}.tile.thunderforest.com/neighbourhood/{z}/{x}/{y}{r}.png?apikey={apikey}";

        L.tileLayer(mapTheme1).addTo(map);

        const sqlLecturasRentas = `SELECT renta_id, cliente_rs, renta_estado, renta_depto, renta_coor, ( SELECT lectura_fecha FROM Lecturas WHERE lectura_renta_id = renta_id AND MONTH (lectura_fecha) = ` +
            document.getElementById("custom_mes").value +
            ` AND YEAR (lectura_fecha) = ` +
            document.getElementById("custom_anio").value +
            `) AS lectura_fecha FROM Rentas
                INNER JOIN Contratos ON Rentas.renta_contrato_id = Contratos.contrato_id
                INNER JOIN Clientes ON Contratos.contrato_cliente_id = Clientes.cliente_id
                WHERE renta_id IN ( SELECT lectura_renta_id FROM Lecturas ) AND renta_estado = 'Activo'`;

        const res = await fetch(SERVERURL + "/ajax/mapaLecturasFetchAjax.php", {
            method: "POST",
            body: JSON.stringify({
                sqlLecturasRentas,
            }),
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });
        const rentaData = await res.json();

        for (var i = 0; i < rentaData.length; i++) {
            const ele = rentaData[i];
            var rentaCoords = ele.renta_coor;
            if (rentaCoords != 0) {

                // Codigo con pin personalizado
                if (ele.lectura_fecha === null) {
                    var pin_status = "/vista/assets/icons/leaf-red.png";
                } else {
                    var pin_status = "/vista/assets/icons/leaf-green.png";
                }
                var myIcon = L.icon({
                    iconUrl: pin_status,
                    shadowUrl: '/vista/assets/icons/leaf-shadow.png',
                    iconSize: [38, 95], // size of the icon
                    shadowSize: [50, 64], // size of the shadow
                    iconAnchor: [22, 94], // point of the icon which will correspond to marker's location
                    shadowAnchor: [4, 62], // the same for the shadow
                    popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
                });

                var rentaCoords = rentaCoords.replace(" ", "");
                rentaCoords = rentaCoords.split(",");

                // Codigo con pin personalizado
                var marker = L.marker([rentaCoords[0], rentaCoords[1]], {
                        icon: myIcon
                    })
                    .bindPopup("<a href='https://www.google.com/maps/search/" + ele.renta_coor + "' target='_blanck'>" + ele.cliente_rs + " - " + ele.renta_depto + "<a>")
                    .addTo(map);
            }
        }
    };
    mapaLecturas();
</script>

</html>