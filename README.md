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
--------------------------------------------------------------------------------------------
<?php
/**
 * Archivo: keys.php
 * Contiene valores sensibles y privados.
 * No debe subirse al control de versiones.
 */

define('SERVERNAME', 'SystemCR');
define('COMPANYNAME', utf8_decode('CR - Imprime Tus Ideas'));
define('WEBSITE', utf8_decode('www.cr-imprimetusideas.com.mx'));
define('dataRFC1', utf8_decode('RENAN ARMANDO MAGAÑA DIAZ (MADR8504096K8)'));
define('dataRFC2', utf8_decode('CALLE ISLA MAGDALENA, MANZANA 536 LOTE 1,'));
define('dataRFC3', utf8_decode('EDIFICIO C DEPARTAMENTO 203, CANCÚN,'));
define('dataRFC4', utf8_decode('BENITO JUARÉZ, QUINTANA ROO, MÉXICO, CP 77517'));

define('USER', 'admin1');
define('DB', 'SystemCR_v3');
define('HOST', 'localhost');
define('PASS', 'MySQL_admin1_');

define('SECRET_KEY', '$SYSTEMA@2024');
define('SECRET_IV', '010203');
--------------------------------------------------------------------------------------------

> No subas `config/keys.php` al repositorio.

## Uso

1. Copia `config/keys.php` si no existe
2. Ajusta credenciales de base de datos y datos fiscales
3. Abre el proyecto en el servidor web
