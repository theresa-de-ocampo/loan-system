USE mysql;
DROP DATABASE IF EXISTS `loan`;
CREATE DATABASE `loan`;
USE `loan`;

CREATE TABLE `cycle` (
	`cycle_id` YEAR PRIMARY KEY DEFAULT (YEAR(CURDATE())),
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
	`guarantor_id` INT UNSIGNED PRIMARY KEY,

	CONSTRAINT fk_guarantor_data_subject FOREIGN KEY (`guarantor_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `guarantor_cycle_map` (
	`guarantor_id` INT UNSIGNED,
	`cycle_id` YEAR DEFAULT (YEAR(CURDATE())),
	`number_of_share` TINYINT NOT NULL,

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

-- [STORED PROCEDURE] calculate_age
DELIMITER $$
CREATE PROCEDURE calculate_age (
	IN p_bday DATE,
	OUT p_age INT UNSIGNED
)
BEGIN
	SELECT
		TIMESTAMPDIFF(YEAR, p_bday, CURDATE())
	INTO
		p_age;
END $$
DELIMITER ;

INSERT INTO
	`cycle`
VALUES
	(DEFAULT, DEFAULT, DEFAULT);

INSERT INTO 
	`data_subject`
VALUES
	(DEFAULT, 'Aurora', 'Imbis', 'Liberato', '09358314949', '1984-01-01', 'P1, B1, L1'),
	(DEFAULT, 'Beth', 'Bigalbal', 'Navalta', '09189218835', '1965-07-19', 'P1, B1, L2'),
	(DEFAULT, 'Felicita', 'Pabiton', 'Nable', '09186475411', '1995-06-01', 'P1, B1, L3'),
	(DEFAULT, 'Gina', 'Medina', 'Robiso', '09452579778', '1999-11-13', 'P1, B1, L4'),
	(DEFAULT, 'Helen', 'Balatico', 'Tailon', '09072914753', '1980-11-05', 'P1, B1, L5'),
	(DEFAULT, 'Jane', 'Hera', 'Histo', '09229013858', '1983-03-20', 'P1, B1, L6'),
	(DEFAULT, 'Adrian', 'Ilag', 'Dela Torre', '09239174896', '1980-06-09', 'P1, B1, L7'),
	(DEFAULT, 'Lyn', 'Lopez', 'Ledesma', '09348918364', '1994-08-23', 'P1, B1, L8'),
	(DEFAULT, 'Mary', 'Rodriguez', 'Pingol', '09251388491', '1973-10-09', 'P1, B1, L9'),
	(DEFAULT, 'Mavic', 'Ferrer', 'Gariando', '09071213894', '1995-01-23', 'P1, B1, L10'),
	(DEFAULT, 'Mona', 'Nacalaban', 'Aguinaldo', '09749712038', '1956-08-23', 'P1, B1, L11'),
	(DEFAULT, 'Nelly', 'Años', 'Zamora', '09449293018', '1991-05-18', 'P1, B1, L12'),
	(DEFAULT, 'Theresa', 'Gumapas', 'De Ocampo', '09078466964', '1999-11-07', 'P2, B1, L1');

INSERT INTO
	`guarantor`
VALUES
	(1),
	(2),
	(3),
	(4),
	(5),
	(6),
	(7),
	(8),
	(9),
	(10),
	(11),
	(12);

INSERT INTO
	`guarantor_cycle_map` (`guarantor_id`, `number_of_share`)
VALUES
	(1, 5),
	(2, 4),
	(3, 5),
	(4, 5),
	(5, 5),
	(6, 4),
	(7, 4),
	(8, 3),
	(9, 5),
	(10, 5),
	(11, 2),
	(12, 5);