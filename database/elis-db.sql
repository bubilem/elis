-- MySQL Script generated by MySQL Workbench
-- Fri Jan 22 11:59:09 2021
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema elis
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema elis
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `elis` DEFAULT CHARACTER SET utf8 ;
USE `elis` ;

-- -----------------------------------------------------
-- Table `elis`.`place`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`place` (
  `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `code` VARCHAR(10) NULL,
  `street` VARCHAR(45) NULL,
  `city_name` VARCHAR(45) NULL,
  `city_code` VARCHAR(20) NULL,
  `country_code` CHAR(2) NULL COMMENT 'ISO 3166-1 alpha-2',
  `gps` VARCHAR(30) NULL COMMENT 'ISO 6709',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`vehicle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`vehicle` (
  `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `uid` VARCHAR(10) NOT NULL,
  `mileage` MEDIUMINT UNSIGNED NOT NULL,
  `avg_consuption` DECIMAL(5,1) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`route`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`route` (
  `id` INT NOT NULL,
  `vehicle` MEDIUMINT UNSIGNED NOT NULL,
  `begin` DATETIME NOT NULL,
  `end` DATETIME NULL,
  `mileage` MEDIUMINT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_route_vehicle1_idx` (`vehicle` ASC),
  CONSTRAINT `fk_route_vehicle1`
    FOREIGN KEY (`vehicle`)
    REFERENCES `elis`.`vehicle` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`user` (
  `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(40) NULL,
  `name` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(65) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`event` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` CHAR(3) NOT NULL COMMENT 'code list event-types.json',
  `date` DATETIME NOT NULL,
  `recorded` MEDIUMINT UNSIGNED NOT NULL,
  `route` INT NOT NULL,
  `mileage` MEDIUMINT UNSIGNED NULL,
  `place` MEDIUMINT UNSIGNED NULL,
  `place_manual` VARCHAR(100) NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_place1_idx` (`place` ASC),
  INDEX `fk_event_route1_idx` (`route` ASC),
  INDEX `fk_event_user1_idx` (`recorded` ASC),
  CONSTRAINT `fk_event_place1`
    FOREIGN KEY (`place`)
    REFERENCES `elis`.`place` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_route1`
    FOREIGN KEY (`route`)
    REFERENCES `elis`.`route` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_user1`
    FOREIGN KEY (`recorded`)
    REFERENCES `elis`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`user_has_role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`user_has_role` (
  `user` MEDIUMINT UNSIGNED NOT NULL,
  `role` CHAR(3) NOT NULL,
  `assigned` DATETIME NOT NULL,
  PRIMARY KEY (`user`, `role`),
  INDEX `fk_user_has_role_user_idx` (`user` ASC),
  CONSTRAINT `fk_user_has_role_user`
    FOREIGN KEY (`user`)
    REFERENCES `elis`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`package`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`package` (
  `id` INT NOT NULL,
  `code` VARCHAR(20) NOT NULL,
  `type` CHAR(3) NULL,
  `width` DECIMAL(6,3) NOT NULL COMMENT 'm',
  `height` DECIMAL(6,3) NOT NULL COMMENT 'm',
  `lenght` DECIMAL(6,3) NOT NULL COMMENT 'm',
  `weight` DECIMAL(10,2) NOT NULL COMMENT 'kg',
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`route_has_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`route_has_user` (
  `route` INT NOT NULL,
  `user` MEDIUMINT UNSIGNED NOT NULL,
  `role` CHAR(3) NOT NULL,
  `assigned` DATETIME NOT NULL,
  PRIMARY KEY (`route`, `user`),
  INDEX `fk_route_has_user_user1_idx` (`user` ASC),
  INDEX `fk_route_has_user_route1_idx` (`route` ASC),
  CONSTRAINT `fk_route_has_user_route1`
    FOREIGN KEY (`route`)
    REFERENCES `elis`.`route` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_route_has_user_user1`
    FOREIGN KEY (`user`)
    REFERENCES `elis`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `elis`.`package_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `elis`.`package_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` DATETIME NOT NULL,
  `package` INT NOT NULL,
  `state` CHAR(3) NULL COMMENT 'code list package-states.json',
  `event` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_package_log_package1_idx` (`package` ASC),
  INDEX `fk_package_log_event1_idx` (`event` ASC),
  CONSTRAINT `fk_package_log_package1`
    FOREIGN KEY (`package`)
    REFERENCES `elis`.`package` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_package_log_event1`
    FOREIGN KEY (`event`)
    REFERENCES `elis`.`event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
