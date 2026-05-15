<?php

// --- 1. DATOS DE AUTENTICACIÓN Y URL DE CONECTIA
// * Reemplaza estos valores con tus credenciales reales de Conectia.
$api_key = "TU_API_KEY_DE_CONECTIA"; 
$user_id = "TU_USUARIO_DE_CONECTIA";
$url_timbrado = "https://api.conectia.mx/vX/timbrar/json"; // URL de ejemplo, verifica la actual en la documentación de Conectia

// --- 2. PREPARACIÓN DEL CFDI EN FORMATO JSON
// * La estructura JSON varía según el PAC y la versión del CFDI (4.0). 
// * Debes construir el JSON completo del CFDI según la documentación de Conectia.

$cfdi_data = [
    "Version" => "4.0",
    "Serie" => "A",
    "Folio" => "101",
    "Fecha" => date("Y-m-d\TH:i:s"),
    "LugarExpedicion" => "45079",
    "TipoDeComprobante" => "I", // Ingreso
    "Exportacion" => "01", // No aplica
    "MetodoPago" => "PUE",
    "FormaPago" => "03", // Transferencia
    "Moneda" => "MXN",
    "Total" => 116.00,
    "SubTotal" => 100.00,
    
    "Emisor" => [
        "Rfc" => "EKU9003173C9", // RFC de prueba
        "Nombre" => "ESCUELA KEMPER URGATE SA DE CV",
        "RegimenFiscal" => "601",
        "FacAtrAdquirente" => "0",
        "DomicilioFiscal" => "45079"
    ],
    
    "Receptor" => [
        "Rfc" => "XAXX010101000", // RFC genérico
        "Nombre" => "PUBLICO EN GENERAL",
        "RegimenFiscalReceptor" => "616",
        "UsoCFDI" => "S01",
        "DomicilioFiscalReceptor" => "45079"
    ],
    
    "Conceptos" => [
        [
            "ClaveProdServ" => "84111506",
            "Cantidad" => 1,
            "ClaveUnidad" => "E48",
            "Descripcion" => "SERVICIO DE PRUEBA",
            "ValorUnitario" => 100.00,
            "Importe" => 100.00,
            "ObjetoImp" => "02", // Sí objeto de impuesto
            "Impuestos" => [
                "Traslados" => [
                    [
                        "Base" => 100.00,
                        "Impuesto" => "002", // IVA
                        "TipoFactor" => "Tasa",
                        "TasaOCuota" => 0.160000,
                        "Importe" => 16.00
                    ]
                ]
            ]
        ]
    ]
];

$json_cfdi = json_encode($cfdi_data, JSON_UNESCAPED_UNICODE);

// --- 3. CONFIGURACIÓN E INICIO DE LA SESIÓN cURL
$ch = curl_init($url_timbrado);

// Configuración de la solicitud POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devuelve la respuesta como cadena
curl_setopt($ch, CURLOPT_POST, true);           // Configura como POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_cfdi); // Envía los datos JSON

// Configuración de los encabezados (Headers)
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    // Usualmente se requiere un header de Autorización (Token, API Key, o Usuario/Contraseña)
    // Este header debe ser confirmado con la documentación de Conectia
    'X-Api-Key: ' . $api_key,
    'X-User-Id: ' . $user_id,
    'Content-Length: ' . strlen($json_cfdi)
]);

// --- 4. EJECUCIÓN Y RECEPCIÓN DE LA RESPUESTA
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// --- 5. PROCESAMIENTO DE LA RESPUESTA
echo "<h2>Resultado del Timbrado (HTTP Code: $http_code)</h2>";

if ($response === FALSE) {
    echo "<h3>Error de Conexión cURL:</h3>" . curl_error($ch);
} else {
    $respuesta_json = json_decode($response, true);

    if ($http_code == 200 && isset($respuesta_json['Comprobante'])) {
        echo "<h3>¡Timbrado Exitoso!</h3>";
        // Aquí puedes guardar el XML timbrado y el PDF/UUID
        echo "<p>UUID: <strong>" . $respuesta_json['Comprobante']['UUID'] . "</strong></p>";
        
        // El XML timbrado y el PDF suelen venir codificados en Base64
        $xml_timbrado = base64_decode($respuesta_json['Comprobante']['XML']);
        // file_put_contents('CFDI_' . $respuesta_json['Comprobante']['UUID'] . '.xml', $xml_timbrado);
        
        // Mostrar respuesta completa (opcional)
        echo "<pre>" . print_r($respuesta_json, true) . "</pre>";

    } else {
        echo "<h3>Error en el Timbrado:</h3>";
        echo "<p>Código de Respuesta HTTP: $http_code</p>";
        echo "<pre>" . print_r($respuesta_json, true) . "</pre>";
    }
}

?>