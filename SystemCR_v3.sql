-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 15-05-2026 a las 21:11:55
-- Versión del servidor: 10.11.14-MariaDB-0+deb12u2
-- Versión de PHP: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `SystemCR_v3`
--
CREATE DATABASE IF NOT EXISTS `SystemCR_v3` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `SystemCR_v3`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AlmacenD`
--

CREATE TABLE `AlmacenD` (
  `AlmD_id` int(11) NOT NULL,
  `AlmDM_id` int(11) NOT NULL,
  `AlmDP_id` int(11) NOT NULL,
  `AlmD_comentario` varchar(999) NOT NULL,
  `AlmD_cantidad` int(11) NOT NULL,
  `AlmD_precio` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AlmacenM`
--

CREATE TABLE `AlmacenM` (
  `AlmM_id` int(11) NOT NULL,
  `AlmM_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `AlmM_uS_id` int(11) NOT NULL,
  `AlmM_estado` int(11) NOT NULL DEFAULT 0,
  `AlmM_folio` varchar(25) NOT NULL,
  `AlmM_fecha` date NOT NULL,
  `AlmM_tipo` int(11) NOT NULL,
  `AlmM_IVA` int(11) NOT NULL,
  `AlmM_identificador` int(11) NOT NULL,
  `AlmM_empleado` int(11) NOT NULL,
  `AlmM_comentario` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `AlmacenM`
--
DELIMITER $$
CREATE TRIGGER `tr_conciliar_despues_almacen` AFTER UPDATE ON `AlmacenM` FOR EACH ROW BEGIN

IF OLD.AlmM_estado = 0 AND NEW.AlmM_estado = 1 THEN

        
        UPDATE conciliacion_stock
        SET
            estado = 'conciliado',
            observaciones = CONCAT('Auto-conciliado por Trigger al cerrar Folio: ', NEW.AlmM_id, ' el ', NOW())
        WHERE
            almacen_m_id = NEW.AlmM_id
            AND estado = 'espera';

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AlmacenP`
--

CREATE TABLE `AlmacenP` (
  `AlmP_id` int(11) NOT NULL,
  `AlmP_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `AlmP_estado` int(11) NOT NULL DEFAULT 1,
  `AlmP_stock_min` int(11) NOT NULL DEFAULT 5,
  `AlmP_codigo` varchar(25) NOT NULL,
  `AlmP_descripcion` varchar(999) NOT NULL,
  `AlmP_precio` varchar(25) NOT NULL,
  `AlmP_unidadM` varchar(15) NOT NULL,
  `AlmP_cat_id` varchar(1) NOT NULL,
  `AlmP_subcat_id` int(11) NOT NULL,
  `AlmP_prov_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `AlmacenP`
--

INSERT INTO `AlmacenP` (`AlmP_id`, `AlmP_timestamp`, `AlmP_estado`, `AlmP_stock_min`, `AlmP_codigo`, `AlmP_descripcion`, `AlmP_precio`, `AlmP_unidadM`, `AlmP_cat_id`, `AlmP_subcat_id`, `AlmP_prov_id`) VALUES
(1, '2025-10-25 06:13:09', 1, 20, '62363', 'TK-1175 | 59310 | 12000 | ECOSYS M2040dn/L', '1680.00', '3', '1', 0, 1),
(2, '2025-11-01 15:39:18', 1, 5, '92236', 'Mantenimiento de Equipo', '750.00', '2', '4', 1, 10),
(3, '2025-11-03 15:40:35', 1, 10, '91458', 'TK-3122 | 58840 | 30000 | ECOSYS M3550, FS4200', '0.00', '3', '1', 0, 1),
(4, '2025-11-03 15:46:10', 1, 0, '87774', 'TKR-1175 | 0 | 0 | ECOSYS M2040dn/L', '0.00', '3', '1', 0, 10),
(5, '2025-11-03 15:48:58', 1, 5, '18577', 'TK-3102 | 58839 | 12500 | ECOSYS M3040/M340/FS2100', '0.00', '3', '1', 0, 1),
(6, '2025-11-03 15:51:59', 1, 0, '47223', 'TK-3112 | 44870 | 0 | ECOSYS FS4100', '0.00', '3', '1', 0, 1),
(7, '2025-11-03 15:57:28', 1, 0, '20387', 'TK-3132 | 44872 | 25000 | ECOSYS FS4300', '0.00', '3', '1', 0, 1),
(8, '2025-11-03 16:02:27', 1, 4, '56111', 'TK-3162 | 61859 | 12500 | ECOSYS M3145', '0.00', '3', '1', 0, 1),
(9, '2025-11-03 16:04:17', 1, 5, '87423', 'TK-6307 | 48614 | 35000 | TASKalfa 3501i', '0.00', '3', '1', 0, 1),
(10, '2025-11-03 16:05:50', 1, 7, '22122', 'TK-6327 | 51382 | 35000 | TASKalfa 4002i', '0.00', '3', '1', 0, 1),
(11, '2025-11-03 16:18:31', 1, 5, '58565', 'TKR-5232K | 51325 | 2600 | ECOSYS M5521cdn/cdw', '0.00', '3', '1', 1, 3),
(12, '2025-11-03 16:18:50', 1, 5, '23042', 'TKR-5232M | 51327 | 2200 | ECOSYS M5521cdn/cdw', '0.00', '3', '1', 2, 3),
(13, '2025-11-03 16:18:59', 1, 5, '63951', 'TKR-5232Y | 51328 | 2200 | ECOSYS M5521cdn/cdw', '0.00', '3', '1', 4, 3),
(14, '2025-11-03 16:35:49', 1, 3, '92268', 'TK-5197C | 49327 | 7000 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 3, 1),
(15, '2025-11-03 16:36:16', 1, 5, '65837', 'TK-5197K | 49326 | 15000 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 1, 1),
(16, '2025-11-03 16:36:44', 1, 3, '34713', 'TK-5197M | 49328 | 7000 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 2, 1),
(17, '2025-11-03 16:36:56', 1, 3, '41996', 'TK-5197Y | 49329 | 7000 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 4, 1),
(18, '2025-11-03 16:42:00', 1, 5, '52813', 'TKR-5232C | 51326 | 2200 | ECOSYS M5521 cdn/cdw', '0.00', '3', '1', 3, 3),
(19, '2025-11-03 16:42:46', 1, 5, '68077', 'TKR-5242C | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 3, 3),
(20, '2025-11-03 16:42:55', 1, 5, '94434', 'TKR-5242K | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 1, 3),
(21, '2025-11-03 16:42:59', 1, 3, '68876', 'TK-5207C | 49339 | 12000 | TASKalfa 356ci', '0.00', '3', '1', 3, 1),
(22, '2025-11-03 16:43:00', 1, 5, '35893', 'TKR-5242M | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 2, 3),
(23, '2025-11-03 16:43:04', 1, 5, '72566', 'TKR-5242Y | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 4, 3),
(24, '2025-11-03 16:43:27', 1, 5, '82016', 'TK-5207K | 49338 | 18000 | TASKalfa 356ci', '0.00', '3', '1', 1, 1),
(25, '2025-11-03 16:44:47', 1, 3, '77871', 'TK-5207M | 49340 | 12000 | TASKalfa 356ci', '0.00', '3', '1', 2, 1),
(26, '2025-11-03 16:45:05', 1, 3, '35897', 'TK-5207Y | 49341 | 12000 | TASKalfa 356ci', '0.00', '3', '1', 4, 1),
(27, '2025-11-03 16:45:14', 1, 7, '97486', 'TK-8307K | 50663 | 25000 | TASKalfa 3051ci', '0.00', '3', '1', 1, 1),
(28, '2025-11-03 16:45:36', 1, 5, '14853', 'TK-8307C | 50664 | 15000 | TASKalfa 3051ci', '0.00', '3', '1', 3, 1),
(29, '2025-11-03 16:45:50', 1, 5, '61011', 'TK-8307M | 50665 | 15000 | TASKalfa 3051ci', '0.00', '3', '1', 2, 1),
(30, '2025-11-03 16:45:59', 1, 5, '89653', 'TK-8307Y | 50666 | 15000 | TASKalfa 3051ci', '0.00', '3', '1', 4, 1),
(31, '2025-11-03 16:47:36', 1, 4, '16588', 'TK-5217C | 49351 | 15000 | TASKalfa 406ci', '0.00', '3', '1', 3, 1),
(32, '2025-11-03 16:47:52', 1, 5, '39307', 'TK-5217K | 49350 | 20000 | TASKalfa 406ci', '0.00', '3', '1', 1, 1),
(33, '2025-11-03 16:48:05', 1, 7, '59830', 'TK-8337K | 50622 | 25000 | TASKalfa 3252ci, 3253ci', '0.00', '3', '1', 1, 1),
(34, '2025-11-03 16:48:10', 1, 4, '68478', 'TK-5217M | 49352 | 15000 | TASKalfa 406ci', '0.00', '3', '1', 2, 1),
(35, '2025-11-03 16:48:17', 1, 5, '94476', 'TK-8337C | 50623 | 12000 | TASKalfa 3252ci, 3253ci', '0.00', '3', '1', 3, 1),
(36, '2025-11-03 16:48:22', 1, 4, '90092', 'TK-5217Y | 49353 | 15000 | TASKalfa 406ci', '0.00', '3', '1', 4, 1),
(37, '2025-11-03 16:48:31', 1, 5, '26834', 'TK-8337M | 50624 | 12000 | TASKalfa 3252ci, 3253ci', '0.00', '3', '1', 2, 1),
(38, '2025-11-03 16:48:40', 1, 5, '76804', 'TK-8337Y | 50625 | 12000 | TASKalfa 3252ci, 3253ci', '0.00', '3', '1', 4, 1),
(40, '2025-11-03 16:49:43', 1, 4, '18931', 'TK-8347K | 50638 | 20000 | TASKalfa 2552ci', '0.00', '3', '1', 1, 1),
(42, '2025-11-03 16:55:02', 1, 3, '22400', 'TK-8347C | 50639 | 12000 | TASKalfa 2552ci', '0.00', '3', '1', 3, 1),
(43, '2025-11-03 16:55:36', 1, 3, '89923', 'TK-8347M | 50640 | 12000 | TASKalfa 2552ci', '0.00', '3', '1', 2, 1),
(44, '2025-11-03 16:55:45', 1, 3, '63239', 'TK-8347Y | 50641 | 12000 | TASKalfa 2552ci', '0.00', '3', '1', 4, 1),
(45, '2025-11-03 16:56:44', 1, 6, '33924', 'TK-8527K | 52824 | 30000 | TASKalfa 3552ci', '0.00', '3', '1', 1, 1),
(46, '2025-11-03 16:56:59', 1, 4, '85955', 'TK-8527C | 52825 | 20000 | TASKalfa 3552ci', '0.00', '3', '1', 3, 1),
(47, '2025-11-03 16:57:06', 1, 4, '36930', 'TK-8527M | 52826 | 20000 | TASKalfa 3552ci', '0.00', '3', '1', 2, 1),
(48, '2025-11-03 16:57:12', 1, 4, '38661', 'TK-8527Y | 52827 | 20000 | TASKalfa 3552ci', '0.00', '3', '1', 4, 1),
(49, '2025-11-03 16:57:49', 1, 5, '58976', 'TK-8557K | 58723 | 40000 | TASKalfa 6054ci', '0.00', '3', '1', 1, 1),
(50, '2025-11-03 16:58:02', 1, 5, '28314', 'TK-8557C | 58724 | 25000 | TASKalfa 6054ci', '0.00', '3', '1', 3, 1),
(51, '2025-11-03 16:58:09', 1, 5, '73374', 'TK-8557M | 58725 | 25000 | TASKalfa 6054ci', '0.00', '3', '1', 2, 1),
(52, '2025-11-03 16:58:17', 1, 5, '56084', 'TK-8557Y | 58726 | 25000 | TASKalfa 6054ci', '0.00', '3', '1', 4, 1),
(53, '2025-11-03 17:24:47', 1, 0, '29971', 'TK-1147 | 58838 | 12000 | ECOSYS M2035', '0.00', '3', '1', 0, 1),
(54, '2025-11-05 08:03:40', 1, 5, '44952', 'Monocromatico | ECOSYS M2040dn/L | TK-1175', '0.00', '2', '5', 1, 10),
(55, '2025-11-05 08:03:40', 1, 5, '64376', 'Monocromatico | ECOSYS M2035dn/L | TK-1147', '0.00', '2', '5', 1, 10),
(56, '2025-11-05 08:03:40', 1, 5, '52196', 'Multicolor | ECOSYS M5521cdn | TK-5232', '0.00', '2', '5', 2, 10),
(57, '2025-11-05 08:03:40', 1, 5, '79945', 'Multicolor | ECOSYS M5526cdw | TK-5242', '0.00', '2', '5', 2, 10),
(58, '2025-11-05 08:03:40', 1, 5, '54155', 'Multicolor | ECOSYS M5521cdw | TK-5232', '0.00', '2', '5', 2, 10),
(59, '2025-11-05 08:03:40', 1, 5, '31756', 'Monocromatico | ECOSYS M3645idn | TK-3162', '0.00', '2', '5', 1, 10),
(60, '2025-11-05 08:03:40', 1, 5, '64368', 'Monocromatico | ECOSYS M3655idn | TK-3182', '0.00', '2', '5', 1, 10),
(61, '2025-11-05 08:03:40', 1, 5, '59499', 'Monocromatico | ECOSYS M3040idn | TK-3102', '0.00', '2', '5', 1, 10),
(62, '2025-11-05 08:03:40', 1, 5, '23582', 'Monocromatico | ECOSYS M2045dn/L | TK-1165', '0.00', '2', '5', 1, 10),
(63, '2025-11-05 08:03:40', 1, 5, '68966', 'Monocromatico | ECOSYS M3145dn/L | TK-3162', '0.00', '2', '5', 1, 10),
(64, '2025-11-05 08:03:40', 1, 5, '57546', 'Monocromatico | TASKalfa CS 4002i | TK-6327', '0.00', '2', '5', 1, 10),
(65, '2025-11-05 08:03:40', 1, 5, '90355', 'Monocromatico | TASKalfa 4002i | TK-6327', '0.00', '2', '5', 1, 10),
(66, '2025-11-05 08:03:40', 1, 5, '46615', 'Multicolor | TASKalfa 3051ci | TK-8307', '0.00', '2', '5', 2, 10),
(67, '2025-11-05 08:03:40', 1, 5, '16960', 'Multicolor | TASKalfa CS 3051ci | TK-8307', '0.00', '2', '5', 2, 10),
(68, '2025-11-05 08:03:40', 1, 5, '67669', 'Multicolor | TASKalfa CS 3252ci | TK-8337', '0.00', '2', '5', 2, 10),
(69, '2025-11-05 08:03:40', 1, 5, '20668', 'Multicolor | TASKalfa 3252ci | TK-8337', '0.00', '2', '5', 2, 10),
(70, '2025-11-05 08:03:40', 1, 5, '14554', 'Multicolor | TASKalfa CS 2552ci | Tk-8347', '0.00', '2', '5', 2, 10),
(71, '2025-11-05 08:03:40', 1, 5, '70448', 'Multicolor | TASKalfa 2552ci | Tk-8347', '0.00', '2', '5', 2, 10),
(72, '2025-11-05 08:03:40', 1, 5, '80133', 'Multicolor | TASKalfa 356ci | TK-5207', '0.00', '2', '5', 2, 10),
(73, '2025-11-05 08:03:40', 1, 5, '61938', 'Multicolor | TASKalfa CS 356ci | TK-5207', '0.00', '2', '5', 2, 10),
(74, '2025-11-05 08:03:40', 1, 5, '15939', 'Multicolor | TASKalfa 406ci | TK-5217', '0.00', '2', '5', 2, 10),
(75, '2025-11-05 08:03:40', 1, 5, '34761', 'Multicolor | TASKalfa CS 406ci | TK-5217', '0.00', '2', '5', 2, 10),
(76, '2025-11-05 08:03:40', 1, 5, '12835', 'Monocromatico | TASKalfa CS 3501i | TK-6307', '0.00', '2', '5', 1, 10),
(77, '2025-11-05 08:03:40', 1, 5, '96160', 'Multicolor | TASKalfa 3552ci | TK-8527', '0.00', '2', '5', 2, 10),
(78, '2025-11-05 08:03:40', 1, 5, '24068', 'Multicolor | TASKalfa CS 3552ci | TK-8527', '0.00', '2', '5', 2, 10),
(79, '2025-11-05 08:03:40', 1, 5, '29342', 'Multicolor | TASKalfa 306ci | TK-5197', '0.00', '2', '5', 2, 10),
(80, '2025-11-05 08:03:40', 1, 5, '84863', 'Multicolor | TASKalfa CS 306ci | TK-5197', '0.00', '2', '5', 2, 10),
(81, '2025-11-05 08:03:40', 1, 5, '87947', 'Monocromatico | TASKalfa 3501i | TK-6307', '0.00', '2', '5', 1, 10),
(82, '2025-11-05 08:03:40', 1, 5, '13032', 'Multicolor | TASKalfa CS 3253ci | TK-8337', '0.00', '2', '5', 2, 10),
(83, '2025-11-05 08:03:40', 1, 5, '52598', 'Multicolor | ECOSYS MA2100cfx | TK-5262', '0.00', '2', '5', 2, 10),
(84, '2025-11-05 08:03:40', 1, 5, '10125', 'Monocromatico | ECOSYS MA4000x | TK-1247', '0.00', '2', '5', 1, 10),
(85, '2025-11-05 17:12:39', 1, 0, '21060', 'TK-3182 | 56731 | 0 | ECOSYS P3055dn', '0.00', '3', '1', 0, 1),
(86, '2025-11-05 17:18:46', 1, 2, '14225', 'TK-5217C | 0 | 0 | TASKalfa 406ci', '0.00', '3', '1', 3, 11),
(87, '2025-11-05 17:19:25', 1, 2, '64869', 'TK-5217M | 0 | 0 | TASKalfa 406ci', '0.00', '3', '1', 2, 11),
(88, '2025-11-05 17:19:31', 1, 2, '99825', 'TK-5217Y | 0 | 0 | TASKalfa 406ci', '0.00', '3', '1', 4, 11),
(89, '2025-11-05 17:19:54', 1, 5, '49294', 'TK-5217K | 0 | 0 | TASKalfa 406ci', '0.00', '3', '1', 1, 11),
(90, '2025-11-05 17:21:21', 1, 5, '64895', 'TK-5197K | 0 | 0 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 1, 11),
(91, '2025-11-05 17:21:27', 1, 2, '67872', 'TK-5197M | 0 | 0 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 2, 11),
(92, '2025-11-05 17:21:32', 1, 2, '73969', 'TK-5197C | 0 | 0 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 3, 11),
(93, '2025-11-05 17:21:36', 1, 2, '18043', 'TK-5197Y | 0 | 0 | TASKalfa 306ci 307ci 308ci', '0.00', '3', '1', 4, 11),
(94, '2025-11-05 17:28:20', 1, 5, '55261', 'TK-5207K | 0 | 0 | TASKalfa 356ci', '0.00', '3', '1', 1, 11),
(95, '2025-11-05 17:28:23', 1, 2, '37809', 'TK-5207M | 0 | 0 | TASKalfa 356ci', '0.00', '3', '1', 2, 11),
(96, '2025-11-05 17:28:27', 1, 2, '95933', 'TK-5207C | 0 | 0 | TASKalfa 356ci', '0.00', '3', '1', 3, 11),
(97, '2025-11-05 17:28:31', 1, 2, '40158', 'TK-5207Y | 0 | 0 | TASKalfa 356ci', '0.00', '3', '1', 4, 11),
(98, '2025-11-05 17:29:33', 1, 3, '64699', 'TK-5242K | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 1, 11),
(99, '2025-11-05 17:29:38', 1, 2, '31323', 'TK-5242M | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 2, 11),
(100, '2025-11-05 17:29:41', 1, 2, '41915', 'TK-5242C | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 3, 11),
(101, '2025-11-05 17:29:45', 1, 2, '45820', 'TK-5242Y | 0 | 0 | ECOSYS M5526cdn/cdw', '0.00', '3', '1', 4, 11),
(102, '2025-11-05 18:13:28', 1, 5, '76420', 'RENTA DE EQUIPO MULTIFUNCIONAL  BLANCO Y NEGRO', '1200.00', '2', '4', 1, 10),
(103, '2025-11-05 19:03:00', 1, 5, '50446', 'RENTA DE EQUIPO COLOR', '1800.00', '2', '4', 1, 10),
(104, '2025-11-05 19:07:38', 1, 5, '57697', 'DISPISITIVO AP TPLINK', '750.00', '2', '4', 1, 10),
(105, '2025-11-06 20:16:57', 1, 5, '41646', 'KG-0001 | KIT DE GOMAS BASICAS PARA EQUIPOS 3pz', '0.00', '5', '3', 2, 10),
(106, '2025-11-06 20:18:10', 1, 5, '47670', 'KG-0002 | KIT DE GOMAS PARA TASKALFA', '0.00', '5', '3', 2, 10),
(107, '2025-11-06 20:24:13', 1, 5, '18177', 'BS-0001 | 2040, 306, 308, 356, 406', '0.00', '1', '3', 34, 10),
(108, '2025-11-06 20:24:49', 1, 5, '24709', 'CI-2035 | ECOSYS M2035DN', '0.00', '1', '3', 10, 10),
(109, '2025-11-06 20:30:51', 1, 5, '49462', 'CI-2040 | ECOSYS M2040DN', '0.00', '1', '3', 10, 10),
(110, '2025-11-06 20:31:54', 1, 5, '57176', 'CI-5018 | ECOSYS M5521/M5526 CDN/CDW', '0.00', '1', '3', 10, 10),
(111, '2025-11-06 20:32:21', 1, 5, '58736', 'CI-5520 | ECOSYS M5521/M5526 cdn/cdw', '0.00', '1', '3', 10, 10),
(112, '2025-11-06 20:36:32', 1, 5, '85779', 'CL-2040 | ECOSYS M2040DN/M2135DN', '0.00', '1', '3', 6, 10),
(113, '2025-11-06 20:36:54', 1, 5, '16790', 'CL-4100 | ECOSYS M3040 M3540', '0.00', '1', '3', 6, 10),
(114, '2025-11-06 20:37:24', 1, 5, '71153', 'CL-6305 | TASKALFA 2552 3253 3051 4002 3552', '0.00', '1', '3', 6, 10),
(115, '2025-11-06 20:39:10', 1, 5, '42716', 'FL-2040 | ECOSYS M2040dn/M2135dn', '0.00', '1', '3', 4, 10),
(116, '2025-11-06 20:40:05', 1, 5, '34800', 'MC-2000 | TASKALFA 2552 2553', '0.00', '1', '3', 3, 10),
(117, '2025-11-06 20:40:47', 1, 5, '34732', 'MC-3100 | ECOSYS 5521/5526/3040/3145', '0.00', '1', '3', 3, 10),
(118, '2025-11-06 20:42:02', 1, 5, '32064', 'RC-1620 | ECOSYS 1620', '0.00', '1', '3', 1, 10),
(119, '2025-11-06 20:42:31', 1, 5, '27121', 'RC-2100 | ECOSYS M3040/M3145', '0.00', '1', '3', 1, 10),
(120, '2025-11-06 20:42:52', 1, 5, '37312', 'RC-2810 | ECOSYS M2810/M2820', '0.00', '1', '3', 1, 10),
(121, '2025-11-06 20:43:16', 1, 5, '47772', 'RC-4500 | TASKALFA 3500/4500', '0.00', '1', '3', 1, 10),
(122, '2025-11-06 20:44:03', 1, 5, '65482', 'RC-5521 | ECOSYS M5521/M5526 CDN/CDW', '0.00', '1', '3', 1, 10),
(123, '2025-11-06 20:44:49', 1, 5, '10434', 'SF-5501 | ECOSYS M5521 M5526 TR', '0.00', '1', '3', 8, 10),
(124, '2025-11-06 20:45:15', 1, 5, '15478', 'SF-5502 | ECOSYS M5521 M5526 DK', '0.00', '1', '3', 8, 10),
(125, '2025-11-06 20:46:16', 1, 5, '13406', 'TLF-2040 | TEFLON - ECOSYS M2040dn/2640dn/M2135dn', '0.00', '1', '3', 7, 10),
(126, '2025-11-06 20:46:33', 1, 5, '12457', 'TLT-2040 | TELA - ECOSYS M2040 M2640 M2135', '0.00', '1', '3', 35, 10),
(127, '2025-11-06 20:46:55', 1, 5, '19775', 'TM-2040 | ECOSYS M2040', '0.00', '1', '3', 32, 10),
(128, '2025-11-07 03:47:43', 1, 5, '92635', 'Instalación de equipo yuc', '950.00', '2', '4', 1, 10),
(129, '2025-11-07 03:48:22', 1, 5, '12652', 'instalación de equipo cozu', '950.00', '2', '4', 1, 10),
(130, '2025-11-07 03:49:03', 1, 5, '17983', 'instalación de equipo playa del carmen', '450.00', '2', '4', 1, 10),
(131, '2025-11-07 03:51:22', 1, 5, '76119', 'Cable de red de 1.8 mt', '100.00', '2', '4', 1, 10),
(132, '2025-11-07 19:51:45', 1, 5, '16084', 'TK-1147 | 0 | ECOSYS M3145', '0.00', '1', '2', 0, 10),
(133, '2025-11-07 20:16:57', 1, 5, '58364', 'TK-1152 | 0 | ECOSYS M2135dn', '0.00', '1', '2', 0, 1),
(134, '2025-11-07 20:18:06', 1, 5, '21649', 'TK-1175 | 0 | ECOSYS M2040dn/L', '0.00', '1', '2', 0, 1),
(135, '2025-11-07 20:18:57', 1, 5, '89120', 'TK-3102 | 0 | ECOSYS M3040', '0.00', '1', '2', 0, 1),
(136, '2025-11-07 20:19:25', 1, 5, '42441', 'TK-3162 | 0 | ECOSYS M3145', '0.00', '1', '2', 0, 10),
(137, '2025-11-07 20:20:32', 1, 0, '30718', 'ES-4172 | 0 | OKIDATA ES4172LP', '0.00', '1', '2', 0, 10),
(138, '2025-11-07 20:21:26', 1, 0, '11563', 'ES-4132 | 0 | OKIDATA ES4172LP', '0.00', '1', '2', 0, 10),
(139, '2025-11-07 20:23:28', 1, 5, '75816', 'TK-5197C | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 3, 10),
(140, '2025-11-07 20:23:50', 1, 5, '61899', 'TK-5197K | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 1, 10),
(141, '2025-11-07 20:24:05', 1, 5, '22542', 'TK-5197M | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 2, 10),
(142, '2025-11-07 20:24:40', 1, 5, '37227', 'TK-5197Y | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 4, 10),
(143, '2025-11-07 20:25:34', 1, 5, '55074', 'TK-5207C | 0 | TASKalfa 356ci', '0.00', '1', '2', 3, 10),
(144, '2025-11-07 20:25:48', 1, 5, '46945', 'TK-5207K | 0 | TASKalfa 356ciTASKalfa 356ci', '0.00', '1', '2', 1, 10),
(145, '2025-11-07 20:26:06', 1, 5, '43474', 'TK-5207M | 0 | TASKalfa 356ci', '0.00', '1', '2', 2, 10),
(146, '2025-11-07 20:26:25', 1, 5, '29383', 'TK-5207Y | 0 | TASKalfa 356ci', '0.00', '1', '2', 4, 10),
(147, '2025-11-07 20:27:26', 1, 5, '18047', 'TK-5232C | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 3, 10),
(148, '2025-11-07 20:27:42', 1, 5, '80754', 'TK-5232K | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 1, 10),
(149, '2025-11-07 20:27:56', 1, 5, '55807', 'TK-5232M | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 2, 10),
(151, '2025-11-07 20:28:45', 1, 5, '80807', 'TK-5232Y | 0 | ECOSYS M5521cdn', '0.00', '1', '2', 4, 10),
(152, '2025-11-07 20:30:23', 1, 5, '65106', 'TK-5242C | 0 | ECOSYS M5526cdn/cdw', '0.00', '1', '2', 3, 10),
(153, '2025-11-07 20:30:35', 1, 5, '53985', 'TK-5242K | 0 | ECOSYS M5526cdn/cdw', '0.00', '1', '2', 1, 10),
(154, '2025-11-07 20:30:46', 1, 5, '69433', 'TK-5242M | 0 | ECOSYS M5526cdn/cdw', '0.00', '1', '2', 2, 10),
(155, '2025-11-07 20:30:58', 1, 5, '78174', 'TK-5242Y | 0 | ECOSYS M5526cdn/cdw', '0.00', '1', '2', 4, 10),
(156, '2025-11-25 21:51:12', 1, 5, '10730', 'equipo monocromático ma 4000xl', '12500.00', '2', '4', 1, 10),
(157, '2025-11-29 21:26:44', 1, 5, '67143', 'CF-3027 | Ventilador trifásico de alto flujo.', '740.00', '1', '3', 37, 10),
(158, '2025-11-29 21:51:08', 1, 5, '74082', 'AR-3252 | Arnes de Cable Principal No. 3 TASKalfa 3252ci', '1245.00', '1', '3', 36, 10),
(159, '2025-11-29 21:52:29', 1, 5, '69344', 'PCB-3252 | PW Finer TASKalfa 3252ci', '980.00', '1', '3', 38, 10),
(160, '2026-01-20 16:37:18', 1, 0, '60499', 'TK-1278 | 0 | 12000 | ECOSYS MA4000x/L', '0.00', '1', '1', 0, 11),
(161, '2026-01-27 21:37:37', 1, 5, '83595', 'Servicio de matto', '750.00', '2', '4', 1, 10),
(162, '2026-01-29 22:01:33', 1, 0, '18172', 'EG-1515 | fusor reacondicionado m2040 (a cambio)', '2500.00', '1', '3', 9, 10),
(163, '2026-02-06 19:54:49', 1, 5, '57381', 'CI-2500 | Main Charger 2500', '0.00', '1', '3', 10, 10),
(164, '2026-02-09 17:32:49', 1, 5, '99396', 'TM-5521 | FUSOR 5230 ORIGINAL', '5320.00', '1', '3', 32, 10),
(165, '2026-02-12 17:14:30', 1, 0, '38484', 'EG-2040 | FUSOR PARA m2040', '0.00', '1', '3', 9, 10),
(166, '2026-02-12 17:19:41', 1, 5, '51052', 'SERVICIO GENERAL', '750.00', '2', '4', 1, 10),
(167, '2026-02-12 18:17:09', 1, 0, '38985', 'TKR-6327 | 0 | 0 | TASKalfa 4002i', '0.00', '3', '1', 0, 10),
(168, '2026-02-18 13:29:25', 1, 0, '87561', 'EG-012 | Cuerpo de engranes de fijado', '750.00', '1', '3', 9, 11),
(169, '2026-02-18 21:43:42', 1, 5, '21394', 'CI-347 | Dk 1150 compatible con m2040', '2500.00', '1', '3', 10, 11),
(170, '2026-03-14 15:47:43', 1, 5, '13904', 'MC-3000 | 3500i/3501i/4501i/6500i', '0.00', '1', '3', 3, 10),
(171, '2026-04-13 16:07:32', 1, 2, '40523', 'DK-1150 | DRUM KIT COMPATIBLE M2040-M2045', '0.00', '2', '3', 39, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AlmacenProvs`
--

CREATE TABLE `AlmacenProvs` (
  `AlmProv_id` int(11) NOT NULL,
  `AlmProv_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `AlmProv_estado` int(11) NOT NULL DEFAULT 1,
  `AlmProv_nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `AlmacenProvs`
--

INSERT INTO `AlmacenProvs` (`AlmProv_id`, `AlmProv_timestamp`, `AlmProv_estado`, `AlmProv_nombre`) VALUES
(1, '2025-10-28 23:20:20', 1, 'KATUN'),
(2, '2025-10-28 23:20:20', 1, 'LEON AMARILLO'),
(3, '2025-10-28 23:21:54', 1, 'RELLENADO'),
(10, '2025-11-01 15:51:02', 1, 'SIN DEFINIR'),
(11, '2025-11-05 17:18:15', 1, 'KYOCERA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cambios`
--

CREATE TABLE `Cambios` (
  `cambio_id` int(11) NOT NULL,
  `cambio_fecha` date NOT NULL,
  `cambio_folio` varchar(25) NOT NULL DEFAULT '0',
  `cambio_renta_id` int(11) NOT NULL,
  `cambio_equipoRet_id` int(11) NOT NULL,
  `cambio_Ret_esc` int(11) NOT NULL,
  `cambio_Ret_bn` int(11) NOT NULL,
  `cambio_Ret_col` int(11) NOT NULL,
  `cambio_equipoIng_id` int(11) NOT NULL,
  `cambio_Ing_esc` int(11) NOT NULL,
  `cambio_Ing_bn` int(11) NOT NULL,
  `cambio_Ing_col` int(11) NOT NULL,
  `cambio_motivo` varchar(255) NOT NULL,
  `cambio_comm` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catCFDI`
--

CREATE TABLE `catCFDI` (
  `CFDI_id` int(11) NOT NULL,
  `CFDI_codigo` varchar(5) NOT NULL,
  `CFDI_descripcion` varchar(255) NOT NULL,
  `CFDI_fisica` tinyint(1) NOT NULL COMMENT 'Aplica para Persona Física (1=Sí, 0=No)',
  `CFDI_moral` tinyint(1) NOT NULL COMMENT 'Aplica para Persona Moral (1=Sí, 0=No)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catCFDI`
--

INSERT INTO `catCFDI` (`CFDI_id`, `CFDI_codigo`, `CFDI_descripcion`, `CFDI_fisica`, `CFDI_moral`) VALUES
(1, 'G01', 'Adquisición de mercancías.', 1, 1),
(2, 'G02', 'Devoluciones, descuentos o bonificaciones.', 1, 1),
(3, 'G03', 'Gastos en general.', 1, 1),
(4, 'I01', 'Construcciones.', 1, 1),
(5, 'I02', 'Mobiliario y equipo de oficina por inversiones.', 1, 1),
(6, 'I03', 'Equipo de transporte.', 1, 1),
(7, 'I04', 'Equipo de computo y accesorios.', 1, 1),
(8, 'I05', 'Dados, troqueles, moldes, matrices y herramental.', 1, 1),
(9, 'I06', 'Comunicaciones telefónicas.', 1, 1),
(10, 'I07', 'Comunicaciones satelitales.', 1, 1),
(11, 'I08', 'Otra maquinaria y equipo.', 1, 1),
(12, 'D01', 'Honorarios médicos, dentales y gastos hospitalarios.', 1, 0),
(13, 'D02', 'Gastos médicos por incapacidad o discapacidad.', 1, 0),
(14, 'D03', 'Gastos funerales.', 1, 0),
(15, 'D04', 'Donativos.', 1, 0),
(16, 'D05', 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).', 1, 0),
(17, 'D06', 'Aportaciones voluntarias al SAR.', 1, 0),
(18, 'D07', 'Primas por seguros de gastos médicos.', 1, 0),
(19, 'D08', 'Gastos de transportación escolar obligatoria.', 1, 0),
(20, 'D09', 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.', 1, 0),
(21, 'D10', 'Pagos por servicios educativos (colegiaturas).', 1, 0),
(22, 'S01', 'Sin efectos fiscales.', 1, 1),
(23, 'CP01', 'Pagos.', 1, 1),
(24, 'CN01', 'Nómina.', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CategoriasR`
--

CREATE TABLE `CategoriasR` (
  `catR_id` int(11) NOT NULL,
  `catR_nombre` varchar(75) NOT NULL,
  `catR_codigo` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `CategoriasR`
--

INSERT INTO `CategoriasR` (`catR_id`, `catR_nombre`, `catR_codigo`) VALUES
(1, 'Rodillo De Calor', 'RC'),
(2, 'Kit De Gomas', 'KG'),
(3, 'Rodillo De Carga', 'MC'),
(4, 'Filmina', 'FL'),
(5, 'Rodillo de Precion', 'RP'),
(6, 'Cuchilla De Limpieza', 'CL'),
(7, 'Telilla De Teflon', 'TLF'),
(8, 'Sin Fin', 'SF'),
(9, 'Engranaje', 'EG'),
(10, 'Cilindro De Imagen', 'CI'),
(11, 'Deposito de Desechos', 'WT'),
(32, 'Termistores', 'TM'),
(33, 'Tapones Residuales', 'TPR'),
(34, 'Bisagra', 'BS'),
(35, 'Telilla De Tela', 'TLT'),
(36, 'Arnes', 'AR'),
(37, 'Cooler Fan', 'CF'),
(38, 'Tarjeta', 'PCB'),
(39, 'Drum Kit', 'DK');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catRegimenFiscal`
--

CREATE TABLE `catRegimenFiscal` (
  `regFis_id` int(11) NOT NULL,
  `regFis_codigo` char(5) NOT NULL COMMENT 'Clave oficial del Régimen Fiscal del SAT',
  `regFis_descripcion` varchar(300) NOT NULL,
  `regFis_fisica` tinyint(1) NOT NULL COMMENT '1 si aplica para Persona Física, 0 si no aplica',
  `regFis_moral` tinyint(1) NOT NULL COMMENT '1 si aplica para Persona Moral, 0 si no aplica'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catRegimenFiscal`
--

INSERT INTO `catRegimenFiscal` (`regFis_id`, `regFis_codigo`, `regFis_descripcion`, `regFis_fisica`, `regFis_moral`) VALUES
(1, '601', 'General de Ley Personas Morales', 0, 1),
(2, '603', 'Personas Morales con Fines no Lucrativos', 0, 1),
(3, '605', 'Sueldos y Salarios e Ingresos Asimilados a Salarios', 1, 0),
(4, '606', 'Arrendamiento', 1, 0),
(5, '607', 'Régimen de Enajenación o Adquisición de Bienes', 1, 0),
(6, '608', 'Demás ingresos', 1, 0),
(7, '610', 'Residentes en el Extranjero sin Establecimiento Permanente en México', 1, 1),
(8, '611', 'Ingresos por Dividendos (socios y accionistas)', 1, 0),
(9, '612', 'Personas Físicas con Actividades Empresariales y Profesionales', 1, 0),
(10, '614', 'Ingresos por intereses', 1, 0),
(11, '615', 'Régimen de los ingresos por obtención de premios', 1, 0),
(12, '616', 'Sin obligaciones fiscales', 1, 0),
(13, '620', 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos', 0, 1),
(14, '621', 'Incorporación Fiscal', 1, 0),
(15, '622', 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras', 1, 1),
(16, '623', 'Opcional para Grupos de Sociedades', 0, 1),
(17, '624', 'Coordinados', 0, 1),
(18, '625', 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas', 1, 0),
(19, '626', 'Régimen Simplificado de Confianza', 1, 1),
(20, '628', 'Hidrocarburos', 0, 1),
(21, '629', 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales', 0, 1),
(22, '630', 'Enajenación de acciones en bolsa de valores', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Clientes`
--

CREATE TABLE `Clientes` (
  `cliente_id` int(11) NOT NULL,
  `cliente_emiFact` int(11) NOT NULL DEFAULT 1,
  `cliente_estado` varchar(25) NOT NULL DEFAULT 'Espera',
  `cliente_tipo` varchar(25) NOT NULL DEFAULT 'Fisica',
  `cliente_regCap` varchar(255) DEFAULT NULL,
  `cliente_rs` varchar(100) NOT NULL,
  `cliente_rfc` varchar(15) NOT NULL,
  `cliente_curp` varchar(25) DEFAULT NULL,
  `cliente_nombreComercial` varchar(100) DEFAULT NULL,
  `cliente_cp` int(11) NOT NULL DEFAULT 0,
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
  `cliente_regFis_id` int(11) NOT NULL DEFAULT 0,
  `cliente_cfdi_id` int(11) NOT NULL DEFAULT 0,
  `cliente_nombre` varchar(100) DEFAULT NULL,
  `cliente_apellido1` varchar(100) DEFAULT NULL,
  `cliente_apellido2` varchar(100) DEFAULT NULL,
  `cliente_contacto` varchar(50) NOT NULL DEFAULT 'nombre',
  `cliente_correo` varchar(100) NOT NULL DEFAULT 'correo',
  `cliente_telefono` varchar(15) NOT NULL DEFAULT 'telefono'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conciliacion_stock`
--

CREATE TABLE `conciliacion_stock` (
  `id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `almacen_m_id` int(11) DEFAULT NULL,
  `subio_k` tinyint(1) DEFAULT 0,
  `subio_c` tinyint(1) DEFAULT 0,
  `subio_m` tinyint(1) DEFAULT 0,
  `subio_y` tinyint(1) DEFAULT 0,
  `detalle_niveles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `fecha_deteccion` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','espera','conciliado','anomalia') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `conciliacion_stock`
--

INSERT INTO `conciliacion_stock` (`id`, `renta_id`, `equipo_id`, `almacen_m_id`, `subio_k`, `subio_c`, `subio_m`, `subio_y`, `detalle_niveles`, `fecha_deteccion`, `estado`, `observaciones`) VALUES
(1, 10, 14, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":19,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":96}}', '2026-01-19 12:18:51', 'pendiente', NULL),
(2, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":11,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":6},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":6,\"Black\":99}}', '2026-01-19 14:19:11', 'pendiente', NULL),
(3, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":6},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":6,\"Black\":95}}', '2026-01-20 09:51:28', 'pendiente', NULL),
(4, 58, 58, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":52,\"toner_magenta\":68,\"toner_yellow\":57},\"despues\":{\"Cyan\":52,\"Magenta\":68,\"Yellow\":57,\"Black\":25}}', '2026-01-26 07:58:26', 'pendiente', NULL),
(5, 58, 58, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":0,\"toner_cyan\":51,\"toner_magenta\":67,\"toner_yellow\":56},\"despues\":{\"Cyan\":50,\"Magenta\":67,\"Yellow\":56,\"Black\":23}}', '2026-01-27 12:15:31', 'pendiente', NULL),
(6, 58, 58, 46, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":23,\"toner_cyan\":50,\"toner_magenta\":67,\"toner_yellow\":56},\"despues\":{\"Cyan\":49,\"Magenta\":67,\"Yellow\":56,\"Black\":100}}', '2026-01-28 11:58:14', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 46 el 2026-01-28 12:01:37'),
(7, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":6},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":6,\"Black\":73}}', '2026-02-03 14:19:41', 'pendiente', NULL),
(8, 68, 70, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":48,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":95}}', '2026-02-16 12:33:07', 'pendiente', NULL),
(9, 80, 93, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":93}}', '2026-02-20 12:11:43', 'pendiente', NULL),
(10, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":30,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":91},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":91,\"Black\":99}}', '2026-02-25 14:25:07', 'pendiente', NULL),
(11, 26, 65, 74, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":100}}', '2026-02-26 13:58:04', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 74 el 2026-02-26 16:14:58'),
(12, 13, 13, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":0,\"Magenta\":0,\"Yellow\":0,\"Black\":48}}', '2026-02-28 17:57:53', 'pendiente', NULL),
(13, 88, 97, NULL, 0, 1, 1, 0, '{\"antes\":{\"toner_black\":43,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":86},\"despues\":{\"Cyan\":41,\"Magenta\":42,\"Yellow\":85,\"Black\":43}}', '2026-03-01 09:59:07', 'pendiente', NULL),
(14, 42, 56, NULL, 1, 1, 1, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":5},\"despues\":{\"Cyan\":30,\"Magenta\":27,\"Yellow\":5,\"Black\":23}}', '2026-03-01 10:59:18', 'pendiente', NULL),
(15, 75, 80, NULL, 1, 1, 1, 1, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":-1},\"despues\":{\"Cyan\":43,\"Magenta\":37,\"Yellow\":43,\"Black\":41}}', '2026-03-01 10:59:18', 'pendiente', NULL),
(16, 55, 55, NULL, 0, 0, 1, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":5,\"toner_magenta\":-1,\"toner_yellow\":5},\"despues\":{\"Cyan\":5,\"Magenta\":44,\"Yellow\":5,\"Black\":5}}', '2026-03-01 10:59:18', 'pendiente', NULL),
(17, 8, 8, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":0,\"Magenta\":0,\"Yellow\":0,\"Black\":26}}', '2026-03-01 11:29:22', 'pendiente', NULL),
(18, 43, 43, NULL, 0, 0, 0, 1, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":5,\"toner_magenta\":5,\"toner_yellow\":-1},\"despues\":{\"Cyan\":5,\"Magenta\":5,\"Yellow\":83,\"Black\":5}}', '2026-03-01 11:59:33', 'pendiente', NULL),
(19, 40, 87, NULL, 1, 1, 0, 1, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":35,\"toner_yellow\":-1},\"despues\":{\"Cyan\":71,\"Magenta\":34,\"Yellow\":75,\"Black\":66}}', '2026-03-01 11:59:33', 'pendiente', NULL),
(20, 45, 96, NULL, 0, 1, 1, 1, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":-1},\"despues\":{\"Cyan\":29,\"Magenta\":29,\"Yellow\":29,\"Black\":5}}', '2026-03-01 12:29:42', 'pendiente', NULL),
(21, 49, 47, NULL, 1, 0, 1, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":6,\"toner_magenta\":-1,\"toner_yellow\":6},\"despues\":{\"Cyan\":6,\"Magenta\":26,\"Yellow\":6,\"Black\":25}}', '2026-03-01 12:29:42', 'pendiente', NULL),
(22, 71, 72, NULL, 1, 1, 1, 1, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":-1},\"despues\":{\"Cyan\":50,\"Magenta\":50,\"Yellow\":52,\"Black\":46}}', '2026-03-01 15:30:00', 'pendiente', NULL),
(23, 77, 82, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":0,\"Magenta\":0,\"Yellow\":0,\"Black\":36}}', '2026-03-02 08:31:20', 'pendiente', NULL),
(24, 46, 46, 71, 0, 1, 0, 0, '{\"antes\":{\"toner_black\":6,\"toner_cyan\":0,\"toner_magenta\":16,\"toner_yellow\":6},\"despues\":{\"Cyan\":11,\"Magenta\":16,\"Yellow\":6,\"Black\":6}}', '2026-03-02 09:31:27', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 71 el 2026-03-03 11:00:09'),
(25, 53, 88, NULL, 1, 0, 1, 0, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":35,\"toner_magenta\":-1,\"toner_yellow\":38},\"despues\":{\"Cyan\":35,\"Magenta\":83,\"Yellow\":38,\"Black\":84}}', '2026-03-02 11:31:52', 'pendiente', NULL),
(26, 48, 84, NULL, 1, 1, 1, 1, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":-1,\"toner_yellow\":-1},\"despues\":{\"Cyan\":31,\"Magenta\":31,\"Yellow\":39,\"Black\":25}}', '2026-03-02 12:32:22', 'pendiente', NULL),
(27, 47, 42, NULL, 0, 0, 0, 1, '{\"antes\":{\"toner_black\":6,\"toner_cyan\":5,\"toner_magenta\":35,\"toner_yellow\":-1},\"despues\":{\"Cyan\":5,\"Magenta\":35,\"Yellow\":13,\"Black\":6}}', '2026-03-03 12:05:00', 'pendiente', NULL),
(28, 49, 47, NULL, 1, 0, 1, 0, '{\"antes\":{\"toner_black\":25,\"toner_cyan\":6,\"toner_magenta\":26,\"toner_yellow\":6},\"despues\":{\"Cyan\":6,\"Magenta\":80,\"Yellow\":6,\"Black\":80}}', '2026-03-04 16:38:21', 'pendiente', NULL),
(29, 92, 98, NULL, 1, 0, 1, 0, '{\"antes\":{\"toner_black\":0,\"toner_cyan\":7,\"toner_magenta\":0,\"toner_yellow\":12},\"despues\":{\"Cyan\":7,\"Magenta\":50,\"Yellow\":12,\"Black\":50}}', '2026-03-10 11:25:36', 'pendiente', NULL),
(30, 93, 41, NULL, 1, 1, 1, 1, '{\"antes\":{\"toner_black\":6,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":-1},\"despues\":{\"Cyan\":50,\"Magenta\":50,\"Yellow\":50,\"Black\":50}}', '2026-03-10 11:55:49', 'pendiente', NULL),
(31, 52, 52, NULL, 1, 1, 0, 1, '{\"antes\":{\"toner_black\":-1,\"toner_cyan\":-1,\"toner_magenta\":19,\"toner_yellow\":-1},\"despues\":{\"Cyan\":32,\"Magenta\":19,\"Yellow\":33,\"Black\":33}}', '2026-03-11 09:58:16', 'pendiente', NULL),
(32, 78, 77, 90, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":87},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":87,\"Black\":80}}', '2026-03-14 11:07:47', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 90 el 2026-03-19 11:41:57'),
(33, 78, 77, 90, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":80,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":86},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":86,\"Black\":100}}', '2026-03-17 12:16:25', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 90 el 2026-03-19 11:41:57'),
(34, 8, 8, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":26,\"toner_cyan\":0,\"toner_magenta\":0,\"toner_yellow\":0},\"despues\":{\"Cyan\":0,\"Magenta\":0,\"Yellow\":0,\"Black\":100}}', '2026-03-18 09:48:43', 'pendiente', NULL),
(35, 10, 14, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":11,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":97}}', '2026-03-23 12:03:04', 'pendiente', NULL),
(36, 12, 79, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":14,\"toner_cyan\":null,\"toner_magenta\":null,\"toner_yellow\":null},\"despues\":{\"Cyan\":null,\"Magenta\":null,\"Yellow\":null,\"Black\":100}}', '2026-03-26 13:53:19', 'pendiente', NULL),
(37, 94, 96, NULL, 1, 1, 1, 1, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":29,\"toner_magenta\":29,\"toner_yellow\":29},\"despues\":{\"Cyan\":100,\"Magenta\":100,\"Yellow\":100,\"Black\":100}}', '2026-04-07 10:27:10', 'pendiente', NULL),
(38, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":82},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":82,\"Black\":100}}', '2026-04-09 13:04:37', 'pendiente', NULL),
(39, 78, 77, NULL, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":78},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":78,\"Black\":81}}', '2026-04-20 09:47:49', 'pendiente', NULL),
(40, 78, 77, 107, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":77},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":77,\"Black\":99}}', '2026-04-20 19:50:06', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 107 el 2026-04-22 12:44:32'),
(41, 78, 77, 107, 1, 0, 0, 0, '{\"antes\":{\"toner_black\":5,\"toner_cyan\":6,\"toner_magenta\":6,\"toner_yellow\":77},\"despues\":{\"Cyan\":6,\"Magenta\":6,\"Yellow\":77,\"Black\":98}}', '2026-04-20 21:50:16', 'conciliado', 'Auto-conciliado por Trigger al cerrar Folio: 107 el 2026-04-22 12:44:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Contratos`
--

CREATE TABLE `Contratos` (
  `contrato_id` int(11) NOT NULL,
  `contrato_folio` varchar(25) NOT NULL,
  `contrato_cliente_id` int(11) NOT NULL,
  `contrato_finicio` date NOT NULL,
  `contrato_ffin` date DEFAULT NULL,
  `contrato_estado` enum('Espera','Activo','Cancelado') NOT NULL DEFAULT 'Espera',
  `contrato_contacto` varchar(50) NOT NULL,
  `contrato_telefono` varchar(50) NOT NULL,
  `contrato_firma_estatus` enum('1','0') NOT NULL DEFAULT '0',
  `contrato_fecha_firma` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizadorD`
--

CREATE TABLE `cotizadorD` (
  `cotD_id` int(11) NOT NULL,
  `cotD_cotM_id` int(11) NOT NULL,
  `cotD_prod_id` int(11) NOT NULL,
  `cotD_cantidad` int(11) NOT NULL,
  `cotD_monto` int(11) NOT NULL,
  `cotD_descuento` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizadorM`
--

CREATE TABLE `cotizadorM` (
  `cotM_id` int(11) NOT NULL,
  `cotM_estatus` int(11) NOT NULL DEFAULT 1,
  `cotM_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `cotM_folio` varchar(50) NOT NULL,
  `cotM_IVA` int(11) NOT NULL,
  `cotM_cliRS` varchar(120) NOT NULL,
  `cotM_cliRFC` varchar(25) NOT NULL,
  `cotM_comm` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Equipos`
--

CREATE TABLE `Equipos` (
  `equipo_id` int(11) NOT NULL,
  `equipo_modelo_id` int(11) NOT NULL,
  `equipo_provE_id` int(11) NOT NULL,
  `equipo_estado` enum('Espera','Rentado','Reparacion','Vendido','Inhabilitado') NOT NULL,
  `equipo_codigo` varchar(15) NOT NULL,
  `equipo_serie` varchar(25) NOT NULL,
  `equipo_fingreso` date NOT NULL,
  `chip_k` int(11) NOT NULL DEFAULT 0,
  `chip_m` int(11) NOT NULL DEFAULT 0,
  `chip_c` int(11) NOT NULL DEFAULT 0,
  `chip_y` int(11) NOT NULL DEFAULT 0,
  `equipo_nivel_K` int(11) DEFAULT 0,
  `equipo_nivel_M` int(11) DEFAULT 0,
  `equipo_nivel_C` int(11) DEFAULT 0,
  `equipo_nivel_Y` int(11) DEFAULT 0,
  `equipo_nivel_R` int(11) DEFAULT 0,
  `equipo_wifi` int(11) NOT NULL DEFAULT 0,
  `equipo_ethe` int(11) NOT NULL DEFAULT 0,
  `equipo_usb` int(11) NOT NULL DEFAULT 0,
  `equipo_contabilidad` int(11) NOT NULL DEFAULT 0,
  `equipo_contactos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Equipos`
--

INSERT INTO `Equipos` (`equipo_id`, `equipo_modelo_id`, `equipo_provE_id`, `equipo_estado`, `equipo_codigo`, `equipo_serie`, `equipo_fingreso`, `chip_k`, `chip_m`, `chip_c`, `chip_y`, `equipo_nivel_K`, `equipo_nivel_M`, `equipo_nivel_C`, `equipo_nivel_Y`, `equipo_nivel_R`, `equipo_wifi`, `equipo_ethe`, `equipo_usb`, `equipo_contabilidad`, `equipo_contactos`) VALUES
(7, 1, 2, 'Rentado', 'ECO-001', 'VR91585129', '2025-02-05', 1, 0, 0, 0, 31, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(8, 11, 1, 'Rentado', 'TAS-001', 'W378Y15455', '2025-02-05', 0, 0, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 8, 2, 'Reparacion', 'ECO-002', 'LSD5307321', '2025-02-06', 0, 0, 0, 0, 95, 0, 0, 0, 20, 0, 0, 0, 0, 0),
(10, 1, 2, 'Rentado', 'ECO-003', 'VR96X01111', '2025-02-06', 1, 0, 0, 0, 32, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(11, 1, 2, 'Rentado', 'ECO-004', 'VR91180872', '2025-02-06', 1, 0, 0, 0, 81, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 1, 2, 'Rentado', 'ECO-005', 'VR93717686', '2025-02-06', 1, 0, 0, 0, 98, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(13, 1, 2, 'Rentado', 'ECO-006', 'VR99Y70748', '2025-02-06', 1, 0, 0, 0, 96, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(14, 1, 2, 'Rentado', 'ECO-007', 'VR98839189', '2025-02-06', 1, 0, 0, 0, 40, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(15, 1, 2, 'Rentado', 'ECO-008', 'VR98Z53036', '2025-02-06', 1, 0, 0, 0, 63, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(16, 1, 2, 'Rentado', 'ECO-009', 'VR99763483', '2025-02-06', 1, 0, 0, 0, 83, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(17, 1, 2, 'Rentado', 'ECO-010', 'VR98229276', '2025-02-06', 1, 0, 0, 0, 33, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 11, 1, 'Rentado', 'TAS-002', 'VFC8303724', '2025-02-06', 0, 0, 0, 0, 95, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 11, 1, 'Rentado', 'TAS-003', 'W378411906', '2025-02-06', 0, 0, 0, 0, 54, 0, 0, 0, 35, 0, 0, 0, 0, 0),
(20, 25, 1, 'Reparacion', 'TAS-004', 'LAB5X04082', '2025-02-05', 0, 0, 0, 0, 71, NULL, NULL, NULL, 20, 0, 0, 0, 0, 0),
(21, 31, 1, 'Rentado', 'TAS-005', 'LAB5Z04416', '2025-02-06', 0, 0, 0, 0, 98, 0, 0, 0, 30, 0, 0, 0, 0, 0),
(22, 7, 2, 'Reparacion', 'ECO-011', 'R4P0458552', '2025-02-06', 0, 0, 0, 0, 99, 0, 0, 0, 15, 0, 0, 0, 0, 0),
(23, 6, 2, 'Rentado', 'ECO-012', 'R4S9407791', '2025-02-06', 0, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(24, 1, 2, 'Reparacion', 'ECO-013', 'VR97309297', '2025-02-06', 0, 0, 0, 0, 99, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(25, 10, 2, 'Rentado', 'ECO-014', 'R4V8Y04657', '2025-02-06', 1, 0, 0, 0, 6, 0, 0, 0, 20, 0, 0, 0, 0, 0),
(26, 1, 2, 'Rentado', 'ECO-015', 'VR98838474', '2025-02-06', 1, 0, 0, 0, 77, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(27, 1, 2, 'Rentado', 'ECO-016', 'VR96Z02993', '2025-02-06', 0, 0, 0, 0, 59, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(28, 23, 1, 'Rentado', 'TAS-006', 'V788Z07728', '2025-02-06', 0, 0, 0, 1, 30, 50, 70, 76, 50, 0, 0, 0, 0, 0),
(29, 29, 1, 'Reparacion', 'TAS-007', 'V7E8105226', '2025-02-06', 0, 0, 0, 0, 50, 50, 50, 50, 0, 0, 0, 0, 0, 0),
(30, 4, 2, 'Reparacion', 'ECO-017', 'VE83926916', '2025-02-06', 0, 0, 0, 0, 95, 45, 55, 56, 0, 0, 0, 0, 0, 0),
(31, 3, 2, 'Reparacion', 'ECO-018', 'VDW7700553', '2025-02-06', 0, 0, 0, 0, 50, 50, 50, 50, 0, 0, 0, 0, 0, 0),
(32, 1, 2, 'Reparacion', 'ECO-019', 'VR96X00729', '2025-02-06', 0, 0, 0, 0, 99, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(33, 3, 1, 'Reparacion', 'ECO-020', 'VDW7700562', '2025-02-06', 0, 0, 0, 0, 48, 75, 80, 81, 0, 0, 0, 0, 0, 0),
(34, 1, 2, 'Reparacion', 'ECO-021', 'VR98431512', '2025-02-06', 0, 0, 0, 0, 99, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(35, 1, 2, 'Rentado', 'ECO-022', 'VR96X00791', '2025-02-06', 1, 0, 0, 0, 42, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(36, 1, 2, 'Rentado', 'ECO-023', 'VR98943230', '2025-02-06', 1, 0, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(37, 1, 2, 'Rentado', 'ECO-024', 'VR94325446', '2025-02-06', 0, 0, 0, 0, 49, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(38, 1, 2, 'Rentado', 'ECO-025', 'VR98Z53039', '2025-02-06', 1, 0, 0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(39, 1, 2, 'Rentado', 'ECO-026', 'VR96X00863', '2025-02-06', 0, 0, 0, 0, 90, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(40, 27, 1, 'Reparacion', 'TAS-008', 'VLQ8107007', '2025-02-06', 0, 0, 0, 0, 52, 80, 80, 80, 15, 0, 0, 0, 0, 0),
(41, 27, 1, 'Rentado', 'TAS-009', 'VLQ7604706', '2025-02-06', 0, 0, 0, 0, 13, 50, 50, 0, 0, 0, 0, 0, 0, 0),
(42, 27, 1, 'Reparacion', 'TAS-010', 'VLQ8508858', '2025-02-06', 1, 1, 1, 0, 5, 33, 5, 13, 10, 0, 0, 0, 0, 0),
(43, 27, 1, 'Rentado', 'TAS-011', 'VLQ8508804', '2025-02-06', 1, 1, 1, 0, 5, 5, 5, 83, 6, 0, 0, 0, 0, 0),
(44, 19, 1, 'Espera', 'TAS-012', 'VFJ6800172', '2025-02-06', 0, 1, 1, 1, 6, 5, 5, 5, 15, 0, 0, 0, 0, 0),
(45, 16, 1, 'Reparacion', 'TAS-013', 'W2R8910508', '2025-02-06', 0, 0, 0, 0, 71, 25, 25, 75, 15, 0, 0, 0, 0, 0),
(46, 14, 1, 'Rentado', 'TAS-014', 'LA76405012', '2025-02-06', 1, 1, 0, 1, 74, 17, 39, 78, 5, 0, 0, 0, 0, 0),
(47, 16, 1, 'Rentado', 'TAS-015', 'W2R7Y07305', '2025-02-06', 0, 0, 1, 1, 80, 80, 6, 6, 0, 0, 0, 0, 0, 0),
(48, 4, 2, 'Reparacion', 'ECO-027', 'VE80Y15123', '2025-02-06', 0, 0, 0, 0, 98, 98, 98, 98, 0, 0, 0, 0, 0, 0),
(49, 21, 1, 'Reparacion', 'TAS-016', 'V7B5Z00063', '2025-02-06', 0, 1, 0, 0, -1, 97, -1, -1, 25, 0, 0, 0, 0, 0),
(50, 21, 1, 'Rentado', 'TAS-017', 'V7B6301215', '2025-02-06', 1, 1, 1, 1, 48, 13, 6, 9, 50, 0, 0, 0, 0, 0),
(51, 21, 1, 'Espera', 'TAS-018', 'V7B5Z00043', '2025-02-06', 0, 0, 0, 0, 53, 72, 58, -1, 15, 0, 0, 0, 0, 0),
(52, 23, 1, 'Rentado', 'TAS-019', 'V786302584', '2025-02-06', 0, 1, 0, 0, 33, 18, 32, 33, 75, 0, 0, 0, 0, 0),
(53, 22, 1, 'Reparacion', 'TAS-020', 'V7B6300820', '2025-02-06', 0, 0, 0, 0, 85, 70, 78, 29, 15, 0, 0, 0, 0, 0),
(54, 29, 1, 'Rentado', 'TAS-021', 'V7E7903956', '2025-02-06', 1, 1, 1, 1, 68, 27, 6, 85, 10, 0, 0, 0, 0, 0),
(55, 27, 1, 'Rentado', 'TAS-022', 'W2R7203154', '2025-02-06', 1, 0, 1, 1, 5, 44, 5, 5, 11, 0, 0, 0, 0, 0),
(56, 27, 1, 'Rentado', 'TAS-023', 'VLQ8810138', '2025-02-06', 0, 0, 0, 0, 23, 27, 30, 5, 8, 0, 0, 0, 0, 0),
(57, 27, 1, 'Rentado', 'TAS-024', 'VLR8902507', '2025-02-06', 1, 1, 1, 1, 36, 5, 67, 76, 2, 0, 0, 0, 0, 0),
(58, 3, 2, 'Rentado', 'ECO-028', 'VDW2Z03455', '2025-02-06', 1, 1, 1, 1, 60, 46, 20, 33, 0, 0, 0, 0, 0, 0),
(59, 22, 1, 'Reparacion', 'TAS-025', 'V9X6300298', '2025-02-06', 0, 0, 0, 0, 50, 50, 50, 50, 0, 0, 0, 0, 0, 0),
(60, 23, 1, 'Reparacion', 'TAS-026', 'V786403014', '2025-02-06', 0, 0, 0, 0, 75, 74, 32, 29, 15, 0, 0, 0, 0, 0),
(61, 1, 2, 'Rentado', 'ECO-029', 'VR91989834', '2025-02-06', 1, 0, 0, 0, 90, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(62, 14, 1, 'Espera', 'TAS-027', 'LA75Y04278', '2025-02-08', 1, 1, 1, 1, 25, 83, 25, 78, 40, 0, 0, 0, 0, 0),
(63, 20, 1, 'Rentado', 'TAS-028', 'W2V7803509', '2025-02-11', 1, 1, 1, 1, 6, 38, 22, 24, 0, 0, 0, 0, 0, 0),
(64, 1, 2, 'Reparacion', 'ECO-030', 'VR97106036', '2025-02-28', 0, 0, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(65, 1, 2, 'Rentado', 'ECO-031', 'VR90273667', '2025-03-01', 1, 0, 0, 0, 93, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(66, 1, 2, 'Inhabilitado', 'ECO-032', 'VR99Z72463', '2025-03-06', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(67, 27, 1, 'Reparacion', 'TAS-029', 'VLQ8408102', '2025-03-14', 0, 0, 0, 0, 85, 50, 50, 50, 15, 0, 0, 0, 0, 0),
(68, 3, 2, 'Rentado', 'ECO-033', 'VDW7600491', '2025-04-04', 0, 1, 1, 0, 6, 6, 40, -1, 0, 0, 0, 0, 0, 0),
(69, 1, 2, 'Espera', 'ECO-034', 'VR90Z78479', '2025-03-06', 1, 0, 0, 0, 87, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(70, 1, 2, 'Rentado', 'ECO-035', 'VR98229257', '2025-03-06', 1, 0, 0, 0, 62, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(71, 1, 2, 'Rentado', 'ECO-036', 'VR98127382', '2025-04-11', 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(72, 27, 1, 'Rentado', 'TAS-030', 'VLQ8408013', '2025-04-30', 1, 0, 0, 0, 7, 50, 50, 52, 22, 0, 0, 0, 0, 0),
(73, 23, 1, 'Reparacion', 'TAS-031', 'V789608774', '2025-04-30', 0, 0, 0, 0, 80, 50, 20, 10, 38, 0, 0, 0, 0, 0),
(74, 14, 1, 'Rentado', 'TAS-032', 'LA76405064', '2025-05-09', 1, 1, 1, 1, 46, 20, 32, 46, 20, 0, 0, 0, 0, 0),
(75, 12, 1, 'Rentado', 'TAS-033', 'W378311343', '2025-05-06', 0, 0, 0, 0, 5, NULL, NULL, NULL, 15, 0, 0, 0, 0, 0),
(76, 1, 2, 'Rentado', 'ECO-037', 'VR96X01079', '2025-05-07', 1, 0, 0, 0, 8, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(77, 4, 2, 'Rentado', 'ECO-038', 'VE83122981', '2025-05-21', 1, 1, 1, 1, 99, 97, 96, 95, 99, 0, 0, 0, 0, 0),
(78, 5, 2, 'Reparacion', 'ECO-039', 'VDZ2X06967', '2025-05-26', 0, 0, 0, 0, 95, 40, 29, 25, 0, 0, 0, 0, 0, 0),
(79, 1, 2, 'Rentado', 'ECO-040', 'VR98229259', '2025-05-26', 1, 0, 0, 0, 62, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(80, 32, 1, 'Rentado', 'TAS-034', 'RH49100104', '2025-05-27', 0, 0, 0, 0, 41, 37, 43, 43, 49, 0, 0, 0, 0, 0),
(81, 1, 2, 'Rentado', 'ECO-041', 'VR99Z72290', '2025-06-30', 1, 0, 0, 0, 31, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(82, 11, 1, 'Espera', 'TAS-035', 'VFC8103413', '2025-07-10', 0, 0, 0, 0, 36, 0, 0, 0, 39, 0, 0, 0, 0, 0),
(83, 11, 1, 'Espera', 'TAS-036', 'VFC8X04496', '2025-07-10', 1, 0, 0, 0, 5, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(84, 24, 1, 'Rentado', 'TAS-037', 'V9Y6300845', '2025-07-11', 0, 0, 0, 0, 25, 31, 31, 39, 41, 0, 0, 0, 0, 0),
(85, 1, 2, 'Reparacion', 'ECO-042', 'VR96Z03654', '2025-07-11', 0, 0, 0, 0, 95, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(86, 1, 2, 'Rentado', 'ECO-043', 'VR97309661', '2025-07-11', 1, 0, 0, 0, 85, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(87, 32, 1, 'Rentado', 'TAS-038', 'RFG9603922', '2025-07-21', 0, 1, 0, 0, 66, 15, 71, 75, 4, 0, 0, 0, 0, 0),
(88, 32, 1, 'Rentado', 'TAS-039', 'RH42103282', '2025-08-07', 0, 0, 1, 1, 84, 83, 31, 32, 20, 0, 0, 0, 0, 0),
(90, 1, 2, 'Rentado', 'ECO-044', 'VR95134782', '2025-08-05', 1, 0, 0, 0, 36, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(91, 33, 3, 'Rentado', 'ECO-045', 'W6E3800449', '2025-09-12', 0, 0, 0, 0, 50, 5, 5, 0, 0, 0, 0, 0, 0, 0),
(92, 32, 1, 'Rentado', 'TAS-040', 'RFG9401411', '2025-09-12', 1, 1, 1, 1, 6, 6, 6, 6, 35, 0, 0, 0, 0, 0),
(93, 1, 1, 'Rentado', 'ECO-046', 'VR94Y33202', '2025-08-01', 1, 0, 0, 0, 38, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0),
(94, 12, 1, 'Rentado', 'TAS-041', 'W377907431', '2025-09-09', 0, 0, 0, 0, 35, 0, 0, 0, 14, 0, 0, 0, 0, 0),
(95, 33, 3, 'Espera', 'ECO-047', 'W6E4300978', '2025-10-14', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(96, 15, 3, 'Rentado', 'TAS-042', 'VFG7701876', '2025-12-09', 0, 0, 0, 0, 100, 100, 100, 100, 0, 0, 0, 0, 0, 0),
(97, 16, 1, 'Rentado', 'TAS-043', 'W2R8710023', '2026-02-02', 1, 0, 0, 1, 35, 42, 41, 75, 24, 0, 0, 0, 0, 0),
(98, 32, 1, 'Rentado', 'TAS-044', 'RFG0305429', '2026-02-09', 0, 0, 1, 1, 50, 50, 5, 7, 0, 0, 0, 0, 0, 0),
(99, 32, 1, 'Rentado', 'TAS-045', 'RFG0305526', '2026-02-12', 0, 0, 0, 0, 67, 62, 68, 68, 10, 0, 0, 0, 0, 0),
(100, 33, 2, 'Rentado', 'ECO-048', 'W6E4300975', '2026-04-25', 1, 1, 1, 1, 88, 95, 94, 95, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `EquiposUbi`
--

CREATE TABLE `EquiposUbi` (
  `equUbi_id` int(11) NOT NULL,
  `equUbi_equipo_id` int(11) NOT NULL,
  `equUbi_fecha_inicio` date NOT NULL,
  `equUbi_fecha_final` date DEFAULT NULL,
  `equUbi_ubi` int(11) NOT NULL,
  `equUbi_ubicacion` varchar(350) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `EquiposUbi`
--

INSERT INTO `EquiposUbi` (`equUbi_id`, `equUbi_equipo_id`, `equUbi_fecha_inicio`, `equUbi_fecha_final`, `equUbi_ubi`, `equUbi_ubicacion`) VALUES
(63, 7, '2025-02-05', NULL, 1, '7'),
(64, 8, '2025-02-06', NULL, 1, '8'),
(65, 10, '2025-02-06', NULL, 1, '14'),
(66, 11, '2025-02-06', NULL, 1, '11'),
(67, 12, '2025-02-06', NULL, 1, '12'),
(68, 13, '2025-02-06', NULL, 1, '13'),
(69, 14, '2025-02-06', NULL, 1, '10'),
(70, 15, '2025-02-06', NULL, 1, '15'),
(71, 16, '2025-02-06', NULL, 1, '16'),
(72, 17, '2025-02-06', NULL, 1, '17'),
(73, 18, '2025-02-06', NULL, 1, '18'),
(74, 19, '2025-02-06', NULL, 1, '19'),
(75, 21, '2025-02-06', NULL, 1, '21'),
(76, 22, '2025-02-06', NULL, 1, '22'),
(77, 23, '2025-02-06', NULL, 1, '23'),
(78, 24, '2025-02-06', '2025-08-09', 1, '24'),
(79, 25, '2025-02-06', NULL, 1, '25'),
(80, 28, '2025-02-06', NULL, 1, '60'),
(81, 32, '2025-07-23', '2025-08-05', 1, '79'),
(82, 35, '2025-02-06', NULL, 1, '35'),
(83, 36, '2025-02-06', NULL, 1, '36'),
(84, 37, '2025-02-06', NULL, 1, '37'),
(85, 38, '2025-02-06', NULL, 1, '38'),
(86, 39, '2025-02-06', NULL, 1, '39'),
(87, 40, '2025-04-30', NULL, 1, '71'),
(88, 42, '2025-02-06', NULL, 1, '47'),
(89, 43, '2025-02-06', NULL, 1, '43'),
(90, 44, '2025-02-14', NULL, 1, '64'),
(91, 45, '2025-02-06', NULL, 1, '45'),
(92, 46, '2025-02-06', NULL, 1, '46'),
(93, 47, '2025-04-11', NULL, 1, '70'),
(94, 49, '2025-02-06', NULL, 1, '49'),
(95, 50, '2025-02-06', NULL, 1, '59'),
(96, 51, '2025-02-06', NULL, 1, '51'),
(97, 52, '2025-02-06', NULL, 1, '52'),
(98, 53, '2025-02-06', NULL, 1, '53'),
(99, 54, '2025-02-06', NULL, 1, '54'),
(100, 55, '2025-02-06', NULL, 1, '55'),
(101, 56, '2025-02-06', NULL, 1, '42'),
(102, 57, '2025-02-06', NULL, 1, '57'),
(103, 58, '2025-02-06', NULL, 1, '58'),
(104, 61, '2025-07-25', NULL, 1, '80'),
(105, 62, '2025-02-08', NULL, 1, '62'),
(106, 63, '2025-02-12', NULL, 1, '63'),
(107, 65, '2025-02-06', NULL, 1, '26'),
(108, 67, '2025-03-14', '2025-08-06', 1, '65'),
(109, 68, '2025-04-04', NULL, 1, '66'),
(110, 69, '2025-03-06', NULL, 1, '67'),
(111, 70, '2025-03-06', NULL, 1, '68'),
(112, 71, '2025-04-11', NULL, 1, '69'),
(113, 72, '2025-02-06', NULL, 1, '41'),
(114, 74, '2025-05-09', NULL, 1, '72'),
(115, 75, '2025-05-06', NULL, 1, '73'),
(116, 76, '2025-05-07', NULL, 1, '74'),
(117, 77, '2025-07-15', NULL, 1, '78'),
(118, 80, '2025-05-28', NULL, 1, '75'),
(119, 82, '2025-07-10', NULL, 1, '77'),
(120, 83, '2025-02-06', NULL, 1, '20'),
(121, 84, '2025-02-06', NULL, 1, '48'),
(122, 85, '2025-02-06', NULL, 1, '9'),
(123, 86, '2025-06-11', NULL, 1, '76'),
(124, 87, '2025-02-06', NULL, 1, '40'),
(125, 67, '2025-07-24', '2025-08-06', 2, '1'),
(126, 67, '2025-08-05', '2025-08-06', 1, '81'),
(127, 88, '2025-08-06', NULL, 1, '81'),
(128, 76, '2024-08-27', NULL, 1, '82'),
(129, 76, '2024-08-27', NULL, 1, '83'),
(130, 17, '2025-05-07', NULL, 1, '84'),
(131, 17, '2025-08-08', NULL, 1, '85'),
(132, 17, '2025-08-08', NULL, 1, '86'),
(133, 81, '2025-08-09', NULL, 1, '24'),
(134, 90, '2025-08-05', NULL, 1, '79');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos_contactos`
--

CREATE TABLE `equipos_contactos` (
  `equCon_id` int(11) NOT NULL,
  `equCon_equipo_id` int(11) NOT NULL,
  `equCon_nombre` varchar(150) NOT NULL,
  `equCon_host` varchar(150) NOT NULL,
  `equCon_ruta` varchar(155) NOT NULL,
  `equCon_usuario` varchar(150) NOT NULL,
  `equCon_clave` varchar(150) NOT NULL,
  `equCon_correo` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos_contactos`
--

INSERT INTO `equipos_contactos` (`equCon_id`, `equCon_equipo_id`, `equCon_nombre`, `equCon_host`, `equCon_ruta`, `equCon_usuario`, `equCon_clave`, `equCon_correo`) VALUES
(1, 72, 'fleon', '', '', '', '', 'fleon@cirlce-ap.com'),
(2, 72, 'hmarin', '', '', '', '', 'hmarin@circle-ap.com'),
(3, 72, 'mnavarro', '', '', '', '', 'mnavarro@circle-ap.com'),
(4, 72, 'ajimenez', '', '', '', '', 'ajimenez@circle-ap.com'),
(5, 10, 'CAJA1', 'CAJA', 'scan', 'user', 'user', ''),
(6, 10, 'RR HH', 'DESKTOP-JRLIVLF', 'scan', 'user', 'user', ''),
(7, 10, 'compras', 'DESKTOP-AOAIVJT', 'scan', 'user', 'user', ''),
(8, 10, 'Administrador', 'DESKTOP-GG7OTSK', 'scan', 'user', 'user', 'admincancun@grupoentrefuegos.com'),
(9, 10, 'Compras2', 'DESKTOP-8TNOMRP', 'scan', 'user', 'user', ''),
(10, 47, 'Majoo', '', '', '', '', 'mhernandez@abcollectionresidences.com'),
(11, 47, 'Arturo Gonzalez', '', '', '', '', 'agonzalez@abliving.com'),
(12, 47, 'Karla Nataren', '', '', '', '', 'knataren@abcollectionresidences.com'),
(13, 47, 'Angelica Villarreal', '', '', '', '', 'avillarreal@abliving.com'),
(14, 63, 'juridico', '', '', '', '', 'juridico@aventirealestate.com'),
(15, 63, 'arealegal', '', '', '', '', 'arealegal@aventirealestate.com'),
(16, 63, 'ashly', '', '', '', '', 'ashly@aventirealestate.com'),
(17, 63, 'Aventi Mac', '10.20.0.3', 'escaner', 'AventiImac', '123456789', ''),
(18, 55, 'Nicolas Sierra', '', '', '', '', 'nsierra@circle.mx'),
(19, 55, 'Jessica Rogel', '', '', '', '', 'jrogel@circle.mx'),
(20, 55, 'Laura Fragoso', '', '', '', '', 'lfragoso@circle.mx'),
(21, 55, 'Fernando Perez', '', '', '', '', 'feperez@circle.mx'),
(22, 55, 'Saul Cruz', '', '', '', '', 'scruz@circle.mx'),
(23, 55, 'Oscar Maza', '', '', '', '', 'omaza@circle.mx'),
(24, 55, 'David Canche', '', '', '', '', 'dcanche@circle.mx'),
(25, 55, 'Alfonzo Nava', '', '', '', '', 'costos@elevacap.com'),
(26, 51, 'ADG11', '192.168.1.171', 'scan', 'user', 'user', 'direcciongral@elt3mplocancun.com'),
(27, 51, 'DAO11', '', '', '', '', 'arizaolveradaniela@gmail.com'),
(28, 51, 'Abril', 'LAP-T3MPLO-JY', 'escaneos', 'user', 'user', 'admin.ventas@elt3mplocancun.com'),
(29, 51, 'ARZ11', '', '', '', '', 'abrilrzaldo21@gmail.com'),
(30, 92, 'Zurii', '192.168.1.200', 'Escaner', 'user', 'user', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos_ether`
--

CREATE TABLE `equipos_ether` (
  `equEther_equipo_id` int(11) NOT NULL,
  `equEther_IP` varchar(15) NOT NULL,
  `equEther_MASK` varchar(15) NOT NULL,
  `equEther_PE` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos_ether`
--

INSERT INTO `equipos_ether` (`equEther_equipo_id`, `equEther_IP`, `equEther_MASK`, `equEther_PE`) VALUES
(8, '192.168.100.83', '255.255.255.0', '192.168.100.1'),
(10, '192.168.1.105', '255.255.255.0', '192.168.1.254'),
(41, '192.168.100.238', '255.255.255.0', '192.168.100.1'),
(46, '192.168.100.66', '255.255.255.0', '192.168.100.1'),
(47, '192.168.88.85', '255.255.255.0', '192.168.88.1'),
(51, '192.168.1.100', '255.255.255.0', '192.168.1.254'),
(55, '192.168.1.214', '255.255.255.0', '192.168.1.1'),
(61, '192.168.103.171', '255.255.255.0', '192.168.103.171'),
(72, '192.168.0.100', '255.255.255.0', '192.168.0.1'),
(79, '192.168.100.76', '255.255.255.0', '192.168.100.254'),
(81, '192.168.0.66', '255.255.255.0', '192.168.0.1'),
(92, '192.168.1.136', '255.255.255.0', '192.168.1.1'),
(96, '192.168.1.164', '255.255.255.0', '192.168.1.1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos_wifi`
--

CREATE TABLE `equipos_wifi` (
  `equWifi_equipo_id` int(11) NOT NULL,
  `equWifi_SSID` varchar(255) NOT NULL,
  `equWifi_WPA` varchar(255) NOT NULL,
  `equWifi_IP` varchar(15) NOT NULL,
  `equWifi_MASK` varchar(15) NOT NULL,
  `equWifi_PE` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos_wifi`
--

INSERT INTO `equipos_wifi` (`equWifi_equipo_id`, `equWifi_SSID`, `equWifi_WPA`, `equWifi_IP`, `equWifi_MASK`, `equWifi_PE`) VALUES
(63, 'Aventi - GigNet', 'Av3nt123', '10.20.1.140', '255.255.252.0', '10.20.0.1'),
(77, 'CR-Admin', 'rosotto_bello', '10.10.1.64', '255.255.255.0', '10.10.1.254'),
(92, 'TLDI', 'TLD1#2025', '10.10.11.168', '255.255.254.0', '10.10.10.1'),
(96, 'STARLINK ADMINISTRATIVO', 'Circle.2024', '192.168.1.164', '255.255.255.0', '192.168.1.1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_contabilidad`
--

CREATE TABLE `equipo_contabilidad` (
  `equConta_id` int(11) NOT NULL,
  `equConta_equipo_id` int(11) NOT NULL,
  `equConta_ident` int(11) NOT NULL,
  `equConta_nombre` int(11) NOT NULL,
  `equConta_restCT` int(11) NOT NULL,
  `equConta_restCU` int(11) NOT NULL,
  `equConta_restCF` int(11) NOT NULL,
  `equConta_restIT` int(11) NOT NULL,
  `equConta_restIF` int(11) NOT NULL,
  `equConta_restEO` int(11) NOT NULL,
  `equConta_restFAX` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `EsP`
--

CREATE TABLE `EsP` (
  `EsP_id` int(11) NOT NULL,
  `EsP_date` varchar(25) NOT NULL,
  `EsP_estado` varchar(25) NOT NULL DEFAULT 'Activo',
  `EsP_prod_id` int(11) NOT NULL,
  `EsP_cantidad` int(11) NOT NULL,
  `EsP_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_reportes`
--

CREATE TABLE `historial_reportes` (
  `id` int(11) NOT NULL,
  `meter_date_raw` varchar(100) DEFAULT NULL,
  `date_of_receipt` datetime DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `uid_correo` int(11) NOT NULL,
  `body_correo` text DEFAULT NULL,
  `renta_id` int(11) DEFAULT NULL,
  `equipo_id` int(11) DEFAULT NULL,
  `serial_number` varchar(50) NOT NULL,
  `scan_total` int(11) DEFAULT 0,
  `bw_total` int(11) DEFAULT 0,
  `color_total` int(11) DEFAULT 0,
  `toner_cyan` int(11) DEFAULT -1,
  `toner_magenta` int(11) DEFAULT -1,
  `toner_yellow` int(11) DEFAULT -1,
  `toner_black` int(11) DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LectChP`
--

CREATE TABLE `LectChP` (
  `LChP_id` int(11) NOT NULL,
  `LChP_renta_id` int(11) NOT NULL,
  `LChP_month` int(11) NOT NULL,
  `LChP_year` int(11) NOT NULL,
  `LChP_folio` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lecturas`
--

CREATE TABLE `Lecturas` (
  `lectura_id` int(11) NOT NULL,
  `lectura_tipo` enum('Automatica','Manual') NOT NULL DEFAULT 'Automatica',
  `lectura_reporte_id` int(11) DEFAULT NULL,
  `lectura_fecha` date DEFAULT NULL,
  `lectura_renta_id` int(11) NOT NULL DEFAULT 0,
  `lectura_equipo_id` int(11) NOT NULL,
  `lectura_pdf` varchar(855) DEFAULT NULL,
  `lectura_esc` int(11) NOT NULL,
  `lectura_bn` int(11) NOT NULL,
  `lectura_col` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `LogReg`
--

CREATE TABLE `LogReg` (
  `logReg_id` int(11) NOT NULL,
  `logReg_usuario_id` int(11) NOT NULL,
  `logReg_fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Modelos`
--

CREATE TABLE `Modelos` (
  `modelo_id` int(11) NOT NULL,
  `modelo_tipo` varchar(25) NOT NULL,
  `modelo_linea` varchar(25) NOT NULL,
  `modelo_lin` varchar(5) NOT NULL,
  `modelo_modelo` varchar(25) NOT NULL,
  `modelo_toner` varchar(25) NOT NULL,
  `modelo_DK` varchar(50) DEFAULT NULL,
  `modelo_DV` varchar(50) DEFAULT NULL,
  `modelo_TR` varchar(50) DEFAULT NULL,
  `modelo_FK` varchar(50) DEFAULT NULL,
  `modelo_DP` varchar(50) DEFAULT NULL,
  `modelo_DR` varchar(50) DEFAULT NULL,
  `modelo_resi` int(11) NOT NULL DEFAULT 0,
  `modelo_wifi` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Modelos`
--

INSERT INTO `Modelos` (`modelo_id`, `modelo_tipo`, `modelo_linea`, `modelo_lin`, `modelo_modelo`, `modelo_toner`, `modelo_DK`, `modelo_DV`, `modelo_TR`, `modelo_FK`, `modelo_DP`, `modelo_DR`, `modelo_resi`, `modelo_wifi`) VALUES
(1, 'Monocromatico', 'ECOSYS', 'ECO', 'M2040dn/L', 'TK-1175', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(2, 'Monocromatico', 'ECOSYS', 'ECO', 'M2035DN/L', 'TK-1147', NULL, NULL, NULL, '1150', NULL, NULL, 0, 0),
(3, 'Multicolor', 'ECOSYS', 'ECO', 'M5521cdn', 'TK-5232', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(4, 'Multicolor', 'ECOSYS', 'ECO', 'M5526cdw', 'TK-5242', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(5, 'Multicolor', 'ECOSYS', 'ECO', 'M5521cdw', 'TK-5232', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(6, 'Monocromatico', 'ECOSYS', 'ECO', 'M3645idn', 'TK-3162', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(7, 'Monocromatico', 'ECOSYS', 'ECO', 'M3655idn', 'TK-3182', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(8, 'Monocromatico', 'ECOSYS', 'ECO', 'M3040idn', 'TK-3102', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(9, 'Monocromatico', 'ECOSYS', 'ECO', 'M2045dn/L', 'TK-1165', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(10, 'Monocromatico', 'ECOSYS', 'ECO', 'M3145dn/L', 'TK-3162', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(11, 'Monocromatico', 'TASKalfa', 'TAS', 'CS 4002i', 'TK-6327', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(12, 'Monocromatico', 'TASKalfa', 'TAS', '4002i', 'TK-6327', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(13, 'Multicolor', 'TASKalfa', 'TAS', '3051ci', 'TK-8307', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(14, 'Multicolor', 'TASKalfa', 'TAS', 'CS 3051ci', 'TK-8307', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(15, 'Multicolor', 'TASKalfa', 'TAS', 'CS 3252ci', 'TK-8337', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(16, 'Multicolor', 'TASKalfa', 'TAS', '3252ci', 'TK-8337', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(19, 'Multicolor', 'TASKalfa', 'TAS', 'CS 2552CI', 'TK-8347', '8350', '8350', '8550', '8350', '7110', NULL, 1, 1),
(20, 'Multicolor', 'TASKalfa', 'TAS', '2552ci', 'Tk-8347', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(21, 'Multicolor', 'TASKalfa', 'TAS', '356ci', 'TK-5207', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(22, 'Multicolor', 'TASKalfa', 'TAS', 'CS 356ci', 'TK-5207', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(23, 'Multicolor', 'TASKalfa', 'TAS', '406CI', 'TK-5217', NULL, NULL, NULL, NULL, '5110', NULL, 1, 0),
(24, 'Multicolor', 'TASKalfa', 'TAS', 'CS 406ci', 'TK-5217', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(25, 'Monocromatico', 'TASKalfa', 'TAS', 'CS 3501i', 'TK-6307', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(27, 'Multicolor', 'TASKalfa', 'TAS', '3552CI', 'TK-8527', '8550', '8550', '8550', '8550', '7110', NULL, 1, 1),
(28, 'Multicolor', 'TASKalfa', 'TAS', 'CS 3552ci', 'TK-8527', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(29, 'Multicolor', 'TASKalfa', 'TAS', '306ci', 'TK-5197', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(30, 'Multicolor', 'TASKalfa', 'TAS', 'CS 306ci', 'TK-5197', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(31, 'Monocromatico', 'TASKalfa', 'TAS', '3501i', 'TK-6307', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(32, 'Multicolor', 'TASKalfa CS', 'TAS', '3253ci', 'TK-8337', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(33, 'Multicolor', 'ECOSYS', 'ECO', 'MA2100cfx', 'TK-5262', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(34, 'Monocromatico', 'ECOSYS', 'ECO', 'MA4000x', 'TK-1247', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Rentas`
--

CREATE TABLE `Rentas` (
  `renta_id` int(11) NOT NULL,
  `renta_contrato_id` int(11) NOT NULL,
  `renta_equipo_id` int(11) DEFAULT NULL,
  `renta_zona_id` int(11) NOT NULL,
  `renta_coor` varchar(255) NOT NULL DEFAULT '0',
  `renta_estado` enum('Cancelado','Activo','Espera') NOT NULL DEFAULT 'Activo',
  `renta_folio` varchar(25) NOT NULL,
  `renta_depto` varchar(255) NOT NULL,
  `renta_finicio` date NOT NULL,
  `renta_ffin` date DEFAULT NULL,
  `renta_tipo` varchar(25) NOT NULL DEFAULT 'fija',
  `renta_costo` int(11) DEFAULT 0,
  `renta_inc_esc` int(11) DEFAULT 0,
  `renta_inc_bn` int(11) DEFAULT 0,
  `renta_inc_col` int(11) DEFAULT 0,
  `renta_exc_esc` float DEFAULT 0,
  `renta_exc_bn` float DEFAULT 0,
  `renta_exc_col` float DEFAULT 0,
  `renta_contacto` varchar(50) NOT NULL,
  `renta_telefono` varchar(50) DEFAULT NULL,
  `renta_stock_K` int(11) NOT NULL DEFAULT 0,
  `renta_stock_M` int(11) NOT NULL DEFAULT 0,
  `renta_stock_C` int(11) NOT NULL DEFAULT 0,
  `renta_stock_Y` int(11) NOT NULL DEFAULT 0,
  `renta_stock_R` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Rentas_Checks`
--

CREATE TABLE `Rentas_Checks` (
  `check_id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `check_anio` int(11) NOT NULL,
  `check_mes` varchar(20) NOT NULL,
  `check_facturado` tinyint(1) DEFAULT 0,
  `check_pagado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rentas_facturas`
--

CREATE TABLE `rentas_facturas` (
  `id` int(11) NOT NULL,
  `renta_id` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `identificador` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Reportes`
--

CREATE TABLE `Reportes` (
  `reporte_id` int(11) NOT NULL,
  `reporte_fecha` datetime NOT NULL,
  `reporte_fecha_fin` datetime DEFAULT NULL,
  `reporte_fecha_inicio` datetime DEFAULT NULL,
  `reporte_estado` int(11) NOT NULL DEFAULT 1,
  `reporte_wmakes` varchar(100) NOT NULL DEFAULT '0',
  `reporte_renta_id` int(11) NOT NULL,
  `reporte_equipo_id` int(11) NOT NULL,
  `reporte_archivo` varchar(500) DEFAULT NULL,
  `reporte_reporte` varchar(999) NOT NULL,
  `reporte_resolucion` varchar(999) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ReportesF`
--

CREATE TABLE `ReportesF` (
  `reporteF_id` int(11) NOT NULL,
  `reporteF_folio` varchar(25) DEFAULT NULL,
  `reporteF_fecha` datetime NOT NULL,
  `reporteF_fecha_fin` datetime DEFAULT NULL,
  `reporteF_fecha_inicio` datetime DEFAULT NULL,
  `reporteF_estado` int(11) NOT NULL DEFAULT 1,
  `reporteF_wmakes` varchar(100) NOT NULL DEFAULT '0',
  `reporteF_cliente_id` int(11) NOT NULL,
  `reporteF_equ_serie` varchar(25) NOT NULL,
  `reporteF_equ_estado` int(11) NOT NULL,
  `reporteF_equ_modelo_id` int(11) NOT NULL,
  `reporteF_esc_ini` int(11) NOT NULL,
  `reporteF_bn_ini` int(11) NOT NULL,
  `reporteF_col_ini` int(11) NOT NULL,
  `reporteF_esc_fin` int(11) NOT NULL,
  `reporteF_bn_fin` int(11) NOT NULL,
  `reporteF_col_fin` int(11) NOT NULL,
  `reporteF_nivelK_ini` int(11) NOT NULL,
  `reporteF_nivelM_ini` int(11) NOT NULL,
  `reporteF_nivelC_ini` int(11) NOT NULL,
  `reporteF_nivelY_ini` int(11) NOT NULL,
  `reporteF_nivelR_ini` int(11) NOT NULL,
  `reporteF_nivelK_fin` int(11) NOT NULL,
  `reporteF_nivelM_fin` int(11) NOT NULL,
  `reporteF_nivelC_fin` int(11) NOT NULL,
  `reporteF_nivelY_fin` int(11) NOT NULL,
  `reporteF_nivelR_fin` int(11) NOT NULL,
  `reporteF_reporte` varchar(999) NOT NULL,
  `reporteF_resolucion` varchar(999) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Retiros`
--

CREATE TABLE `Retiros` (
  `retiro_id` int(11) NOT NULL,
  `retiro_fecha` date NOT NULL,
  `retiro_motivo` varchar(25) NOT NULL,
  `retiro_renta_id` int(11) NOT NULL,
  `retiro_equipo_id` int(11) NOT NULL,
  `retiro_esc` int(11) NOT NULL,
  `retiro_bn` int(11) NOT NULL,
  `retiro_col` int(11) NOT NULL DEFAULT 0,
  `retiro_comm` varchar(999) NOT NULL,
  `retiro_file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidadesList`
--

CREATE TABLE `unidadesList` (
  `unList_id` int(11) NOT NULL,
  `unList_unidad` varchar(25) NOT NULL,
  `unList_uni` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidadesList`
--

INSERT INTO `unidadesList` (`unList_id`, `unList_unidad`, `unList_uni`) VALUES
(1, 'PIEZA', 'PZA'),
(2, 'UNIDAD', 'UN'),
(3, 'CAJA', 'CAJ'),
(4, 'DOCENA', 'DZ'),
(5, 'PAQUETE', 'PAQ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `usuario_id` int(11) NOT NULL,
  `usuario_nombre` varchar(50) NOT NULL,
  `usuario_apellido` varchar(50) NOT NULL,
  `usuario_telefono` varchar(20) NOT NULL,
  `usuario_direccion` varchar(200) NOT NULL,
  `usuario_email` varchar(150) NOT NULL,
  `usuario_usuario` varchar(20) NOT NULL,
  `usuario_clave` varchar(250) NOT NULL,
  `usuario_estado` varchar(25) NOT NULL,
  `usuario_privilegio` int(11) NOT NULL,
  `usuario_navbarStatus` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Zonas`
--

CREATE TABLE `Zonas` (
  `zona_id` int(11) NOT NULL,
  `zona_nombre` varchar(25) NOT NULL,
  `zona_codigo` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `Zonas`
--

INSERT INTO `Zonas` (`zona_id`, `zona_nombre`, `zona_codigo`) VALUES
(1, 'Cancun', 'CUN'),
(2, 'Playa del Carmen', 'PCR'),
(3, 'Puerto Morelos', 'MRL'),
(4, 'Tulum', 'TQO'),
(5, 'Chetumal', 'CHT'),
(6, 'Isla Mujeres', 'IMJ');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `AlmacenD`
--
ALTER TABLE `AlmacenD`
  ADD PRIMARY KEY (`AlmD_id`),
  ADD KEY `AlmDP_id` (`AlmDP_id`);

--
-- Indices de la tabla `AlmacenM`
--
ALTER TABLE `AlmacenM`
  ADD PRIMARY KEY (`AlmM_id`);

--
-- Indices de la tabla `AlmacenP`
--
ALTER TABLE `AlmacenP`
  ADD PRIMARY KEY (`AlmP_id`);

--
-- Indices de la tabla `AlmacenProvs`
--
ALTER TABLE `AlmacenProvs`
  ADD PRIMARY KEY (`AlmProv_id`);

--
-- Indices de la tabla `Cambios`
--
ALTER TABLE `Cambios`
  ADD PRIMARY KEY (`cambio_id`),
  ADD KEY `cambio_renta_id` (`cambio_renta_id`),
  ADD KEY `cambio_equipoRet_id` (`cambio_equipoRet_id`),
  ADD KEY `cambio_equipoIng_id` (`cambio_equipoIng_id`);

--
-- Indices de la tabla `catCFDI`
--
ALTER TABLE `catCFDI`
  ADD PRIMARY KEY (`CFDI_id`),
  ADD UNIQUE KEY `CFDI_codigo` (`CFDI_codigo`);

--
-- Indices de la tabla `CategoriasR`
--
ALTER TABLE `CategoriasR`
  ADD PRIMARY KEY (`catR_id`);

--
-- Indices de la tabla `catRegimenFiscal`
--
ALTER TABLE `catRegimenFiscal`
  ADD PRIMARY KEY (`regFis_id`),
  ADD UNIQUE KEY `regFis_codigo` (`regFis_codigo`);

--
-- Indices de la tabla `Clientes`
--
ALTER TABLE `Clientes`
  ADD PRIMARY KEY (`cliente_id`);

--
-- Indices de la tabla `conciliacion_stock`
--
ALTER TABLE `conciliacion_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado` (`estado`),
  ADD KEY `renta_id` (`renta_id`);

--
-- Indices de la tabla `Contratos`
--
ALTER TABLE `Contratos`
  ADD PRIMARY KEY (`contrato_id`);

--
-- Indices de la tabla `cotizadorD`
--
ALTER TABLE `cotizadorD`
  ADD PRIMARY KEY (`cotD_id`);

--
-- Indices de la tabla `cotizadorM`
--
ALTER TABLE `cotizadorM`
  ADD PRIMARY KEY (`cotM_id`);

--
-- Indices de la tabla `Equipos`
--
ALTER TABLE `Equipos`
  ADD PRIMARY KEY (`equipo_id`);

--
-- Indices de la tabla `EquiposUbi`
--
ALTER TABLE `EquiposUbi`
  ADD PRIMARY KEY (`equUbi_id`);

--
-- Indices de la tabla `equipos_contactos`
--
ALTER TABLE `equipos_contactos`
  ADD PRIMARY KEY (`equCon_id`);

--
-- Indices de la tabla `equipos_ether`
--
ALTER TABLE `equipos_ether`
  ADD UNIQUE KEY `equEther_equipo_id` (`equEther_equipo_id`) USING BTREE;

--
-- Indices de la tabla `equipos_wifi`
--
ALTER TABLE `equipos_wifi`
  ADD UNIQUE KEY `equWifi_equipo_id` (`equWifi_equipo_id`) USING BTREE;

--
-- Indices de la tabla `equipo_contabilidad`
--
ALTER TABLE `equipo_contabilidad`
  ADD PRIMARY KEY (`equConta_id`);

--
-- Indices de la tabla `EsP`
--
ALTER TABLE `EsP`
  ADD PRIMARY KEY (`EsP_id`);

--
-- Indices de la tabla `historial_reportes`
--
ALTER TABLE `historial_reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipoID` (`equipo_id`),
  ADD KEY `serial_number` (`serial_number`),
  ADD KEY `date_of_receipt` (`date_of_receipt`);

--
-- Indices de la tabla `LectChP`
--
ALTER TABLE `LectChP`
  ADD PRIMARY KEY (`LChP_id`);

--
-- Indices de la tabla `Lecturas`
--
ALTER TABLE `Lecturas`
  ADD PRIMARY KEY (`lectura_id`);

--
-- Indices de la tabla `LogReg`
--
ALTER TABLE `LogReg`
  ADD PRIMARY KEY (`logReg_id`);

--
-- Indices de la tabla `Modelos`
--
ALTER TABLE `Modelos`
  ADD PRIMARY KEY (`modelo_id`);

--
-- Indices de la tabla `Rentas`
--
ALTER TABLE `Rentas`
  ADD PRIMARY KEY (`renta_id`);

--
-- Indices de la tabla `Rentas_Checks`
--
ALTER TABLE `Rentas_Checks`
  ADD PRIMARY KEY (`check_id`),
  ADD UNIQUE KEY `renta_periodo` (`renta_id`,`check_anio`,`check_mes`);

--
-- Indices de la tabla `rentas_facturas`
--
ALTER TABLE `rentas_facturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Reportes`
--
ALTER TABLE `Reportes`
  ADD PRIMARY KEY (`reporte_id`);

--
-- Indices de la tabla `ReportesF`
--
ALTER TABLE `ReportesF`
  ADD PRIMARY KEY (`reporteF_id`);

--
-- Indices de la tabla `Retiros`
--
ALTER TABLE `Retiros`
  ADD PRIMARY KEY (`retiro_id`);

--
-- Indices de la tabla `unidadesList`
--
ALTER TABLE `unidadesList`
  ADD PRIMARY KEY (`unList_id`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`usuario_id`);

--
-- Indices de la tabla `Zonas`
--
ALTER TABLE `Zonas`
  ADD PRIMARY KEY (`zona_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `AlmacenD`
--
ALTER TABLE `AlmacenD`
  MODIFY `AlmD_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `AlmacenM`
--
ALTER TABLE `AlmacenM`
  MODIFY `AlmM_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `AlmacenP`
--
ALTER TABLE `AlmacenP`
  MODIFY `AlmP_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT de la tabla `AlmacenProvs`
--
ALTER TABLE `AlmacenProvs`
  MODIFY `AlmProv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `Cambios`
--
ALTER TABLE `Cambios`
  MODIFY `cambio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `catCFDI`
--
ALTER TABLE `catCFDI`
  MODIFY `CFDI_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `CategoriasR`
--
ALTER TABLE `CategoriasR`
  MODIFY `catR_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `conciliacion_stock`
--
ALTER TABLE `conciliacion_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `Contratos`
--
ALTER TABLE `Contratos`
  MODIFY `contrato_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizadorD`
--
ALTER TABLE `cotizadorD`
  MODIFY `cotD_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizadorM`
--
ALTER TABLE `cotizadorM`
  MODIFY `cotM_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Equipos`
--
ALTER TABLE `Equipos`
  MODIFY `equipo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `EquiposUbi`
--
ALTER TABLE `EquiposUbi`
  MODIFY `equUbi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de la tabla `equipos_contactos`
--
ALTER TABLE `equipos_contactos`
  MODIFY `equCon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `equipo_contabilidad`
--
ALTER TABLE `equipo_contabilidad`
  MODIFY `equConta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `EsP`
--
ALTER TABLE `EsP`
  MODIFY `EsP_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `historial_reportes`
--
ALTER TABLE `historial_reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `LectChP`
--
ALTER TABLE `LectChP`
  MODIFY `LChP_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Lecturas`
--
ALTER TABLE `Lecturas`
  MODIFY `lectura_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `LogReg`
--
ALTER TABLE `LogReg`
  MODIFY `logReg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1667;

--
-- AUTO_INCREMENT de la tabla `Modelos`
--
ALTER TABLE `Modelos`
  MODIFY `modelo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `Rentas`
--
ALTER TABLE `Rentas`
  MODIFY `renta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Rentas_Checks`
--
ALTER TABLE `Rentas_Checks`
  MODIFY `check_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rentas_facturas`
--
ALTER TABLE `rentas_facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Reportes`
--
ALTER TABLE `Reportes`
  MODIFY `reporte_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ReportesF`
--
ALTER TABLE `ReportesF`
  MODIFY `reporteF_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Retiros`
--
ALTER TABLE `Retiros`
  MODIFY `retiro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidadesList`
--
ALTER TABLE `unidadesList`
  MODIFY `unList_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Zonas`
--
ALTER TABLE `Zonas`
  MODIFY `zona_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Cambios`
--
ALTER TABLE `Cambios`
  ADD CONSTRAINT `Cambios_ibfk_1` FOREIGN KEY (`cambio_renta_id`) REFERENCES `Rentas` (`renta_id`),
  ADD CONSTRAINT `Cambios_ibfk_2` FOREIGN KEY (`cambio_equipoRet_id`) REFERENCES `Equipos` (`equipo_id`),
  ADD CONSTRAINT `Cambios_ibfk_3` FOREIGN KEY (`cambio_equipoIng_id`) REFERENCES `Equipos` (`equipo_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
