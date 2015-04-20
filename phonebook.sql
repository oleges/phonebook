CREATE DATABASE IF NOT EXISTS `phonebook` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

USE `phonebook` ;

CREATE  TABLE IF NOT EXISTS `phonebook`.`city` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `phonebook`.`street` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `city_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_street_city1_idx` (`city_id` ASC) ,
  CONSTRAINT `fk_street_city1`
    FOREIGN KEY (`city_id` )
    REFERENCES `phonebook`.`city` (`id` ))
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `phonebook`.`person` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `last_name` VARCHAR(45) NULL ,
  `first_name` VARCHAR(45) NULL ,
  `patronymic` VARCHAR(45) NULL ,
  `birthdate` DATE NULL ,
  `city_id` INT NOT NULL ,
  `street_id` INT NOT NULL ,
  `tel` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_person_city1_idx` (`city_id` ASC) ,
  INDEX `fk_person_street1_idx` (`street_id` ASC) ,
  CONSTRAINT `fk_person_city1`
    FOREIGN KEY (`city_id` )
    REFERENCES `phonebook`.`city` (`id` ),
  CONSTRAINT `fk_person_street1`
    FOREIGN KEY (`street_id` )
    REFERENCES `phonebook`.`street` (`id` ))
ENGINE = InnoDB;

USE `phonebook` ;

INSERT INTO `city` (`name`) VALUES
('Москва'),
('Владивосток'),
('Лондон');

INSERT INTO `street` (`name`, `city_id`) VALUES
('Новый арбат', 1),
('Тверская', 1),
('Вавилова', 1),
('Светланская', 2),
('Школьная', 2),
('Невельского', 2),
('Кингс Роуд', 3),
('Эбби Роуд', 3),
('Карнаби Стрит', 3);

CREATE USER 'phonebook_user'@'localhost' IDENTIFIED BY 'password';
GRANT USAGE ON *.* TO 'phonebook_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON `phonebook`.* TO 'phonebook_user'@'localhost';
