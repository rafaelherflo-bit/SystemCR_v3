# SystemCR

## Descripción breve del proyecto
Es una aplicación web en PHP para la gestión administrativa de una empresa.

- Usa index.php como enrutador principal y SERVER.php para la configuración global.
- Tiene módulos para:
Clientes
Contratos
Cotizador
Rentas
Lecturas
Cobranzas
Equipos
Toners
Refacciones
Proveedores
Almacén
Cambios
Reportes
Retiros
Usuarios
- Incluye endpoints AJAX en ajax y formatos de impresión/PDF en formats.
- Está orientado a controlar inventarios, clientes, facturación, contratos y reportes operativos.

## Características

- Enrutamiento central en `index.php`
- Módulos para clientes, contratos, cotizaciones, rentas, lecturas, cobranzas, equipos, toners, refacciones, proveedores, almacén, cambios, reportes, retiros y usuarios
- Endpoints AJAX en la carpeta `ajax/`
- Vistas y formatos de impresión en `vista/`
- Configuración principal en `config/SERVER.php`

## Configuración

Este proyecto separa las claves sensibles en `config/keys.php`.

- `SERVERNAME`
- `COMPANYNAME`
- `WEBSITE`
- `dataRFC1`..`dataRFC4`
- `USER`
- `DB`
- `HOST`
- `PASS`
- `SECRET_KEY`
- `SECRET_IV`

## Ejemplo de archivo en config/keys.php

define('SERVERNAME', 'SystemCR');
define('COMPANYNAME', utf8_decode('NOMBRE DE LA EMPRESA'));
define('WEBSITE', utf8_decode('sitioweb.com'));
define('dataRFC1', utf8_decode('RAZON SOCIAL DE EMPRESA (RFCDEEMPRESA123)'));
define('dataRFC2', utf8_decode('PARTE DE LA DORECCION 1,'));
define('dataRFC3', utf8_decode('PARTE DE LA DIRECCION 2,'));
define('dataRFC4', utf8_decode('MUNICIPIO, ESTADO, PAIS, CODIGO POSTAL'));

define('USER', 'userDB');
define('DB', 'nameDB');
define('HOST', 'localhost');
define('PASS', 'passDB');

define('SECRET_KEY', '$PALABRA@CLAVE$123');
define('SECRET_IV', '010203');


> No subas `config/keys.php` al repositorio.

## Uso

1. Copia `config/keys.php` si no existe
2. Ajusta credenciales de base de datos y datos fiscales
3. Abre el proyecto en el servidor web
