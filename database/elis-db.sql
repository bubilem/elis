-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Pon 14. čen 2021, 21:43
-- Verze serveru: 5.7.26
-- Verze PHP: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `elis`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` char(3) NOT NULL COMMENT 'code list event-types.json',
  `date` datetime NOT NULL,
  `recorded` mediumint(8) UNSIGNED DEFAULT NULL,
  `route` int(10) UNSIGNED NOT NULL,
  `mileage` mediumint(8) UNSIGNED DEFAULT NULL,
  `place` mediumint(8) UNSIGNED DEFAULT NULL,
  `place_manual` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `fk_event_place1_idx` (`place`),
  KEY `fk_event_route1_idx` (`route`),
  KEY `fk_event_user1_idx` (`recorded`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `event`
--

INSERT INTO `event` (`id`, `type`, `date`, `recorded`, `route`, `mileage`, `place`, `place_manual`, `description`) VALUES
(4, 'LOD', '2021-06-12 17:15:54', 1, 1, NULL, 2, 'Mariána', ''),
(5, 'LOD', '2021-06-12 17:16:20', 1, 1, NULL, 2, 'Mariána', ''),
(7, 'UNL', '2021-06-12 18:56:54', 1, 1, NULL, 2, '', ''),
(8, 'WTG', '2021-06-12 19:29:21', 1, 1, NULL, 2, '', ''),
(9, 'ONW', '2021-06-12 19:47:28', 1, 1, NULL, 2, '', 'Jedu'),
(12, 'RFL', '2021-06-13 12:32:10', 2, 1, NULL, 1, '', ''),
(13, 'UNL', '2021-06-13 12:33:35', 2, 1, NULL, 1, '', ''),
(14, 'LOD', '2021-06-13 12:46:30', 3, 1, NULL, 1, '', ''),
(15, 'ONW', '2021-06-13 12:48:29', 2, 1, NULL, 1, '', ''),
(16, 'OTH', '2021-06-13 12:52:18', 2, 1, NULL, NULL, 'D10, Brandýs n. L.', 'Police control'),
(17, 'ONW', '2021-06-13 13:32:29', 2, 1, NULL, NULL, '', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `package`
--

DROP TABLE IF EXISTS `package`;
CREATE TABLE IF NOT EXISTS `package` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `type` char(3) DEFAULT NULL,
  `width` decimal(6,3) NOT NULL COMMENT 'm',
  `height` decimal(6,3) NOT NULL COMMENT 'm',
  `lenght` decimal(6,3) NOT NULL COMMENT 'm',
  `weight` decimal(10,2) NOT NULL COMMENT 'kg',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `package`
--

INSERT INTO `package` (`id`, `code`, `type`, `width`, `height`, `lenght`, `weight`, `description`) VALUES
(2, '0000001-C20', 'C20', '2.438', '2.591', '6.058', '27800.00', ''),
(3, '0000002-C20', 'C20', '2.438', '2.591', '6.058', '27800.00', ''),
(4, '0000003-C40', 'C40', '2.438', '2.591', '12.192', '26199.00', ''),
(5, '0000004-C40', 'C40', '2.438', '2.591', '12.192', '26199.00', 'Electronics, gems, fragile');

-- --------------------------------------------------------

--
-- Struktura tabulky `package_log`
--

DROP TABLE IF EXISTS `package_log`;
CREATE TABLE IF NOT EXISTS `package_log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `package` int(10) UNSIGNED NOT NULL,
  `state` char(3) DEFAULT NULL COMMENT 'code list package-states.json',
  `event` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_package_log_package1_idx` (`package`),
  KEY `fk_package_log_event1_idx` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `package_log`
--

INSERT INTO `package_log` (`id`, `date`, `package`, `state`, `event`) VALUES
(1, '2021-06-12 10:54:55', 2, 'ACP', NULL),
(2, '2021-06-12 11:00:10', 3, 'ACP', NULL),
(3, '2021-06-12 11:27:55', 2, 'WTG', NULL),
(4, '2021-06-12 11:31:25', 2, 'WTG', NULL),
(5, '2021-06-12 14:37:58', 4, 'ACP', NULL),
(6, '2021-06-12 14:38:02', 3, 'WTG', NULL),
(8, '2021-06-12 17:15:54', 2, 'TRN', 4),
(9, '2021-06-12 17:16:20', 3, 'TRN', 5),
(11, '2021-06-12 18:56:54', 2, 'WTG', 7),
(12, '2021-06-13 12:33:35', 3, 'WTG', 13),
(13, '2021-06-13 12:38:21', 5, 'ACP', NULL),
(14, '2021-06-13 12:38:31', 5, 'WTG', NULL),
(15, '2021-06-13 12:46:30', 5, 'TRN', 14);

-- --------------------------------------------------------

--
-- Struktura tabulky `place`
--

DROP TABLE IF EXISTS `place`;
CREATE TABLE IF NOT EXISTS `place` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `street` varchar(45) DEFAULT NULL,
  `city_name` varchar(45) DEFAULT NULL,
  `city_code` varchar(20) DEFAULT NULL,
  `country_code` char(2) DEFAULT NULL COMMENT 'ISO 3166-1 alpha-2',
  `gps` varchar(30) DEFAULT NULL COMMENT 'ISO 6709',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `place`
--

INSERT INTO `place` (`id`, `name`, `code`, `street`, `city_name`, `city_code`, `country_code`, `gps`) VALUES
(1, 'Depot Prague', 'DP-PRG', '', 'Prague', '', 'CZ', ''),
(2, 'Depot Varnsdorf', 'DP-VDF', '', 'Varnsdorf', '', 'CZ', ''),
(3, 'Depot Gdańsk', 'DP-GDA', '', 'Gdańsk', '', 'PL', ''),
(4, 'Gas station Varnsdorf', 'GS-VDF', '', 'Varnsdorf', '', 'CZ', ''),
(5, 'Gas station Poznań', 'GS-POZ', '', 'Poznań', '', 'PL', ''),
(6, 'Gas station Harachov', 'GS-HAR', '', 'Harachov', '', 'CZ', '50.7802, 15.4130');

-- --------------------------------------------------------

--
-- Struktura tabulky `route`
--

DROP TABLE IF EXISTS `route`;
CREATE TABLE IF NOT EXISTS `route` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `mileage` mediumint(9) DEFAULT NULL,
  `description` text,
  `vehicle` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_route_vehicle1_idx` (`vehicle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `route`
--

INSERT INTO `route` (`id`, `name`, `begin`, `end`, `mileage`, `description`, `vehicle`) VALUES
(1, 'R01', '2021-05-28 00:00:00', NULL, NULL, 'test', 1),
(2, 'R02', '2021-01-29 00:00:00', NULL, NULL, 'test', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `route_has_user`
--

DROP TABLE IF EXISTS `route_has_user`;
CREATE TABLE IF NOT EXISTS `route_has_user` (
  `route` int(10) UNSIGNED NOT NULL,
  `user` mediumint(8) UNSIGNED NOT NULL,
  `role` char(3) NOT NULL,
  `assigned` datetime NOT NULL,
  PRIMARY KEY (`route`,`user`),
  KEY `fk_route_has_user_user1_idx` (`user`),
  KEY `fk_route_has_user_route1_idx` (`route`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `route_has_user`
--

INSERT INTO `route_has_user` (`route`, `user`, `role`, `assigned`) VALUES
(1, 2, 'DRV', '2021-06-13 11:55:17'),
(1, 3, 'DSP', '2021-06-13 11:54:48'),
(2, 1, 'DSP', '2021-06-13 11:30:04'),
(2, 2, 'DRV', '2021-06-13 11:55:43'),
(2, 3, 'DSP', '2021-06-13 11:55:34'),
(2, 4, 'CDR', '2021-06-13 11:55:53');

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(40) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `surname` varchar(65) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `surname`) VALUES
(1, 'michal.bubilek@skolavdf.cz', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Michal', 'Bubílek'),
(2, 'driver@jid-project.eu', 'fdda0c46f953c1a45bdc520849be1e4edf4e228c', 'John', 'Driver'),
(3, 'dispatcher@jid-project.eu', 'bdf70eff0e4d79093bd5f318014dd13348b89cdb', 'Jack', 'Dispatcher'),
(4, 'admin@jid-project.eu', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Alan', 'Admin');

-- --------------------------------------------------------

--
-- Struktura tabulky `user_has_role`
--

DROP TABLE IF EXISTS `user_has_role`;
CREATE TABLE IF NOT EXISTS `user_has_role` (
  `user` mediumint(8) UNSIGNED NOT NULL,
  `role` char(3) NOT NULL,
  `assigned` datetime NOT NULL,
  PRIMARY KEY (`user`,`role`),
  KEY `fk_user_has_role_user_idx` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `user_has_role`
--

INSERT INTO `user_has_role` (`user`, `role`, `assigned`) VALUES
(1, 'ADM', '2021-06-13 11:48:39'),
(1, 'DRV', '2021-06-13 11:48:39'),
(1, 'DSP', '2021-06-13 11:48:39'),
(2, 'DRV', '2021-06-13 11:47:54'),
(3, 'DSP', '2021-06-13 11:49:40'),
(4, 'ADM', '2021-06-13 11:50:59');

-- --------------------------------------------------------

--
-- Struktura tabulky `vehicle`
--

DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE IF NOT EXISTS `vehicle` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `uid` varchar(10) NOT NULL,
  `mileage` mediumint(8) UNSIGNED NOT NULL,
  `avg_consuption` decimal(5,1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `vehicle`
--

INSERT INTO `vehicle` (`id`, `name`, `uid`, `mileage`, `avg_consuption`) VALUES
(1, 'V01', '4U1 123456', 5024, '9.0'),
(2, 'V02', '4U1 123457', 12, '12.2'),
(3, 'V03', '4U1 123458', 200, '12.0');

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_place1` FOREIGN KEY (`place`) REFERENCES `place` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_route1` FOREIGN KEY (`route`) REFERENCES `route` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_user1` FOREIGN KEY (`recorded`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Omezení pro tabulku `package_log`
--
ALTER TABLE `package_log`
  ADD CONSTRAINT `fk_package_log_event1` FOREIGN KEY (`event`) REFERENCES `event` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_package_log_package1` FOREIGN KEY (`package`) REFERENCES `package` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `route`
--
ALTER TABLE `route`
  ADD CONSTRAINT `fk_route_vehicle1` FOREIGN KEY (`vehicle`) REFERENCES `vehicle` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Omezení pro tabulku `route_has_user`
--
ALTER TABLE `route_has_user`
  ADD CONSTRAINT `fk_route_has_user_route1` FOREIGN KEY (`route`) REFERENCES `route` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_route_has_user_user1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `user_has_role`
--
ALTER TABLE `user_has_role`
  ADD CONSTRAINT `fk_user_has_role_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
