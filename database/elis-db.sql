-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Ned 20. čen 2021, 18:04
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `event`
--

INSERT INTO `event` (`id`, `type`, `date`, `recorded`, `route`, `mileage`, `place`, `place_manual`, `description`) VALUES
(3, 'INS', '2021-06-12 17:00:00', 1, 1, NULL, 2, 'Mariána', 'Go to DP-VDF'),
(4, 'LOD', '2021-06-12 17:15:54', 1, 1, NULL, 2, 'Mariána', ''),
(5, 'LOD', '2021-06-12 17:16:20', 1, 1, NULL, 2, 'Mariána', ''),
(7, 'UNL', '2021-06-12 18:56:54', 1, 1, NULL, 2, '', ''),
(8, 'WTG', '2021-06-12 19:29:21', 1, 1, NULL, 2, '', ''),
(10, 'INS', '2021-06-12 19:47:28', 1, 1, NULL, NULL, '', 'After UNL wait to new LOAD.'),
(12, 'RFL', '2021-06-13 12:32:10', 2, 1, NULL, 1, '', ''),
(13, 'UNL', '2021-06-13 12:33:35', 2, 1, NULL, 1, '', ''),
(14, 'LOD', '2021-06-13 12:46:30', 3, 1, NULL, 1, '', ''),
(15, 'ONW', '2021-06-13 12:48:29', 2, 1, NULL, 1, '', ''),
(16, 'OTH', '2021-06-13 12:52:18', 2, 1, NULL, NULL, 'D10, Brandýs n. L.', 'Police control'),
(17, 'ONW', '2021-06-13 13:32:29', 2, 1, NULL, NULL, '', ''),
(18, 'UNL', '2021-06-19 10:19:35', 1, 1, NULL, 3, '', ''),
(19, 'LOD', '2021-06-19 10:22:27', 1, 1, NULL, 3, '', ''),
(20, 'INS', '2021-06-19 15:00:00', 1, 2, NULL, 1, '', 'LOAD in DP-PRG'),
(21, 'LOD', '2021-06-19 15:24:02', 2, 2, NULL, 1, '', ''),
(22, 'ONW', '2021-06-19 15:24:38', 2, 2, NULL, 1, '', ''),
(23, 'UNL', '2021-06-20 11:01:38', 2, 2, NULL, 3, '', ''),
(24, 'ONW', '2021-06-20 14:31:01', 2, 1, NULL, NULL, '', ''),
(25, 'RST', '2021-06-20 16:10:57', 2, 1, NULL, NULL, 'D10 Highway', '');

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
