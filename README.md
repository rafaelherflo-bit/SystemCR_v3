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

> No subas `config/keys.php` al repositorio.

## Uso

1. Copia `config/keys.php` si no existe
2. Ajusta credenciales de base de datos y datos fiscales
3. Abre el proyecto en el servidor web
