ALTER TABLE `Contratos`
ADD COLUMN `contrato_firma_estatus` ENUM ('1', '0') NOT NULL DEFAULT '0' AFTER `contrato_telefono`,
ADD COLUMN `contrato_fecha_firma` DATETIME DEFAULT NULL AFTER `contrato_firma_estatus`;