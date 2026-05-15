CREATE TABLE
    `Clientes` (
        `cliente_id` int NOT NULL,
        `cliente_emiFact` int NOT NULL DEFAULT '1',
        `cliente_estado` varchar(25) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Espera',
            `cliente_rs` varchar(150) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `cliente_rfc` varchar(45) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `cliente_cp` int NOT NULL DEFAULT '0',
            `cliente_regFis_id` int NOT NULL DEFAULT '0',
            `cliente_cfdi_id` int NOT NULL DEFAULT '0',
            `cliente_contacto` varchar(100) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
            `cliente_correo` varchar(150) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
            `cliente_telefono` varchar(25) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `Clientes` (
        `cliente_id` int (11) NOT NULL,
        `cliente_emiFact` int (11) NOT NULL DEFAULT 1,
        `cliente_estado` varchar(25) NOT NULL DEFAULT 'Espera',
        `cliente_tipo` varchar(25) NOT NULL DEFAULT 'Fisica',
        `cliente_regCap` varchar(255) DEFAULT NULL,
        `cliente_rs` varchar(100) NOT NULL,
        `cliente_rfc` varchar(15) NOT NULL,
        `cliente_curp` varchar(25) DEFAULT NULL,
        `cliente_nombreComercial` varchar(100) DEFAULT NULL,
        `cliente_cp` int (5) NOT NULL DEFAULT 0,
        `cliente_noVialidad` varchar(150) NOT NULL DEFAULT '',
        `cliente_nuInterior` varchar(150) NOT NULL DEFAULT '',
        `cliente_noLocalidad` varchar(150) NOT NULL DEFAULT '',
        `cliente_entidadFederativa` varchar(150) NOT NULL DEFAULT '',
        `cliente_tipoVialidad` varchar(150) NOT NULL DEFAULT '',
        `cliente_nuExterior` varchar(150) NOT NULL DEFAULT '',
        `cliente_noColonia` varchar(150) NOT NULL DEFAULT '',
        `cliente_noMunicipio` varchar(150) NOT NULL DEFAULT '',
        `cliente_calle1` varchar(150) NOT NULL DEFAULT '',
        `cliente_calle2` varchar(150) NOT NULL DEFAULT '',
        `cliente_regFis_id` int (11) NOT NULL DEFAULT 0,
        `cliente_cfdi_id` int (11) NOT NULL DEFAULT 0,
        `cliente_nombre` varchar(100) DEFAULT NULL,
        `cliente_apellido1` varchar(100) DEFAULT NULL,
        `cliente_apellido2` varchar(100) DEFAULT NULL,
        `cliente_contacto` varchar(50) NOT NULL DEFAULT 'nombre',
        `cliente_correo` varchar(100) NOT NULL DEFAULT 'correo',
        `cliente_telefono` varchar(15) NOT NULL DEFAULT 'telefono'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `Clientes`
-- 1. Modificar columnas existentes para ajustar tamaños y valores por defecto
MODIFY COLUMN `cliente_id` int (11) NOT NULL,
MODIFY COLUMN `cliente_emiFact` int (11) NOT NULL DEFAULT 1,
MODIFY COLUMN `cliente_rs` varchar(100) NOT NULL,
MODIFY COLUMN `cliente_rfc` varchar(15) NOT NULL,
MODIFY COLUMN `cliente_cp` int (5) NOT NULL DEFAULT 0,
MODIFY COLUMN `cliente_regFis_id` int (11) NOT NULL DEFAULT 0,
MODIFY COLUMN `cliente_cfdi_id` int (11) NOT NULL DEFAULT 0,
MODIFY COLUMN `cliente_contacto` varchar(50) NOT NULL DEFAULT 'nombre',
MODIFY COLUMN `cliente_correo` varchar(100) NOT NULL DEFAULT 'correo',
MODIFY COLUMN `cliente_telefono` varchar(15) NOT NULL DEFAULT 'telefono',
-- 2. Agregar nuevas columnas en el orden específico solicitado
ADD COLUMN `cliente_tipo` varchar(25) NOT NULL DEFAULT 'Fisica' AFTER `cliente_estado`,
ADD COLUMN `cliente_regCap` varchar(255) DEFAULT NULL AFTER `cliente_tipo`,
ADD COLUMN `cliente_curp` varchar(25) DEFAULT NULL AFTER `cliente_rfc`,
ADD COLUMN `cliente_nombreComercial` varchar(100) DEFAULT NULL AFTER `cliente_curp`,
-- Bloque de dirección detallada
ADD COLUMN `cliente_noVialidad` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_cp`,
ADD COLUMN `cliente_nuInterior` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_noVialidad`,
ADD COLUMN `cliente_noLocalidad` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_nuInterior`,
ADD COLUMN `cliente_entidadFederativa` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_noLocalidad`,
ADD COLUMN `cliente_tipoVialidad` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_entidadFederativa`,
ADD COLUMN `cliente_nuExterior` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_tipoVialidad`,
ADD COLUMN `cliente_noColonia` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_nuExterior`,
ADD COLUMN `cliente_noMunicipio` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_noColonia`,
ADD COLUMN `cliente_calle1` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_noMunicipio`,
ADD COLUMN `cliente_calle2` varchar(150) NOT NULL DEFAULT '' AFTER `cliente_calle1`,
-- Bloque de nombres personales (para personas físicas)
ADD COLUMN `cliente_nombre` varchar(100) DEFAULT NULL AFTER `cliente_cfdi_id`,
ADD COLUMN `cliente_apellido1` varchar(100) DEFAULT NULL AFTER `cliente_nombre`,
ADD COLUMN `cliente_apellido2` varchar(100) DEFAULT NULL AFTER `cliente_apellido1`;