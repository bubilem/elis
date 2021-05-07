-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Úte 04. kvě 2021, 13:15
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
  `recorded` mediumint(8) UNSIGNED NOT NULL,
  `route` int(10) UNSIGNED NOT NULL,
  `mileage` mediumint(8) UNSIGNED DEFAULT NULL,
  `place` mediumint(8) UNSIGNED DEFAULT NULL,
  `place_manual` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `fk_event_place1_idx` (`place`),
  KEY `fk_event_route1_idx` (`route`),
  KEY `fk_event_user1_idx` (`recorded`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `package`
--

INSERT INTO `package` (`id`, `code`, `type`, `width`, `height`, `lenght`, `weight`, `description`) VALUES
(1, '457654', 'EP1', '0.800', '5.000', '1.200', '1500.00', 'fdfgdfgdf');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `vehicle` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_route_vehicle1_idx` (`vehicle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `route`
--

INSERT INTO `route` (`id`, `name`, `begin`, `end`, `mileage`, `description`, `vehicle`) VALUES
(1, 'R01', '2021-01-29 00:00:00', NULL, NULL, 'test', 1),
(2, 'R02', '2021-01-29 00:00:00', NULL, NULL, 'test', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `surname`) VALUES
(1, 'michal.bubilek@skolavdf.cz', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Michal', 'Bubílek');

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
(1, 'ADM', '2021-05-02 10:37:28');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `vehicle`
--

INSERT INTO `vehicle` (`id`, `name`, `uid`, `mileage`, `avg_consuption`) VALUES
(1, 'V01', '4U1 123456', 50, '6.2'),
(2, 'V02', '4U1 123457', 12, '12.2');

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_place1` FOREIGN KEY (`place`) REFERENCES `place` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_route1` FOREIGN KEY (`route`) REFERENCES `route` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_event_user1` FOREIGN KEY (`recorded`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `package_log`
--
ALTER TABLE `package_log`
  ADD CONSTRAINT `fk_package_log_event1` FOREIGN KEY (`event`) REFERENCES `event` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_package_log_package1` FOREIGN KEY (`package`) REFERENCES `package` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `route`
--
ALTER TABLE `route`
  ADD CONSTRAINT `fk_route_vehicle1` FOREIGN KEY (`vehicle`) REFERENCES `vehicle` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `route_has_user`
--
ALTER TABLE `route_has_user`
  ADD CONSTRAINT `fk_route_has_user_route1` FOREIGN KEY (`route`) REFERENCES `route` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_route_has_user_user1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `user_has_role`
--
ALTER TABLE `user_has_role`
  ADD CONSTRAINT `fk_user_has_role_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
