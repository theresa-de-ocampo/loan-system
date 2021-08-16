USE mysql;
DROP DATABASE IF EXISTS `loan`;
CREATE DATABASE `loan`;
USE `loan`;

CREATE TABLE `cycle` (
	`cycle_id` YEAR PRIMARY KEY,
	`membership_fee` SMALLINT NOT NULL DEFAULT 12000,
	`interest_rate` DECIMAL(3, 2) DEFAULT 0.10 -- in decimal
) Engine=InnoDB;

CREATE TABLE `data_subject` (
	`data_subject_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`fname` VARCHAR(50) NOT NULL,
	`mname` VARCHAR(50) NOT NULL,
	`lname` VARCHAR(50) NOT NULL,
	`contact_no` CHAR(11) NOT NULL,
	`bday` DATE NOT NULL,
	`phase_block_lot` VARCHAR(20) NOT NULL
) Engine=InnoDB;

CREATE TABLE `guarantor` (
	`guarantor_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`number_of_share` TINYINT NOT NULL,
	`data_subject_id` INT UNSIGNED,

	CONSTRAINT fk_guarantor_data_subject FOREIGN KEY (`data_subject_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `guarantor_cycle_map` (
	`cycle_id` YEAR,
	`guarantor_id` INT UNSIGNED,

	CONSTRAINT pk_guarantor_cycle_map PRIMARY KEY (`guarantor_id`, `cycle_id`),
	CONSTRAINT fk_guarantor_cycle_map_cycle FOREIGN KEY (`cycle_id`)
		REFERENCES `cycle` (`cycle_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_guarantor_cycle_map_guarantor FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

INSERT INTO
	`cycle` (`cycle_id`)
VALUES
	('2021');

INSERT INTO 
	`data_subject`
VALUES
	(DEFAULT, 'Theresa', 'Gumapas', 'De Ocampo', '09078466964', '1999-11-07', 'P1, B5, L27'),
	(DEFAULT, 'Aurora', 'Imbis', 'Liberato', '09358314949', '1984-01-01', 'P2, B1, L46'),
	(DEFAULT, 'Beth', 'Bigalbal', 'Navalta', '09189218835', '1965-07-19', 'P2, B1, L8');

INSERT INTO
	`guarantor`
VALUES
	(DEFAULT, 5, 2);

INSERT INTO
	`guarantor_cycle_map`
VALUES
	('2021', 1);