USE mysql;
DROP DATABASE IF EXISTS `ciudad_nuevo`;
CREATE DATABASE `ciudad_nuevo`;
USE `ciudad_nuevo`;

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

CREATE TABLE `loan` (
	`loan_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`borrower_id` INT UNSIGNED NOT NULL,
	`guarantor_id` INT UNSIGNED NOT NULL,
	`loan_date_time` DATETIME NOT NULL,
	`principal` DECIMAL(10, 2) NOT NULL,
	`status` ENUM('Active', 'Closed') DEFAULT 'Active' NOT NULL,

	CONSTRAINT fk_loan_borrower_id FOREIGN KEY (`borrower_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_loan_guarantor_id FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `loan_detail` (
	`loan_detail_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`transaction_date` DATE NOT NULL,
	`principal_balance` DECIMAL(10, 2) NOT NULL,
	`principal_payment` DECIMAL(10, 2),
	`interest_amount` DECIMAL(8, 2),
	`interest_status` ENUM('Paid', 'Pending', 'Overdue', 'Late') DEFAULT 'Pending',
	`interest_date_time_paid` DATETIME,
	`interest_balance` DECIMAL(8, 2),
	`penalty_amount` DECIMAL(8, 2),
	`penalty_from_interest_date` DATE,
	`penalty_status` ENUM('Paid', 'Pending') DEFAULT 'Pending',
	`penalty_date_time_paid` DATETIME,
	`penalty_balance` DECIMAL(8, 2),
	`processing_fee_amount` DECIMAL(7, 2),
	`processing_fee_status` ENUM('Paid', 'Pending') DEFAULT 'Pending',
	`processing_fee_date_time_paid` DATETIME,
	`processing_fee_balance` DECIMAL(7, 2),
	`loan_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_loan_detail_loan_id FOREIGN KEY (`loan_id`)
		REFERENCES `loan` (`loan_id`)
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

-- [VIEW] savings
CREATE VIEW savings AS
SELECT
	`guarantor_id`,
	CONCAT(fname, ' ', lname) AS `member`,
	`number_of_share`,
	`number_of_share` * `membership_fee` AS `principal`
FROM
	`data_subject`
INNER JOIN
	`guarantor_cycle_map` gcm
ON 
	`data_subject_id` = `guarantor_id` AND
	`cycle_id` = YEAR(CURDATE())
INNER JOIN
	`cycle` c
ON
	c.`cycle_id` = gcm.`cycle_id`;

-- [VIEW] current_guarantors
CREATE VIEW current_guarantors AS
SELECT *
FROM `data_subject`
INNER JOIN
	`guarantor_cycle_map` ON `data_subject_id` = `guarantor_id`
WHERE
	`cycle_id` = YEAR(CURDATE());

-- [VIEW] not_current_guarantors
CREATE VIEW not_current_guarantors AS
SELECT *
FROM `data_subject`
WHERE 
	`data_subject_id` NOT IN (
		SELECT `data_subject_id` FROM current_guarantors
	);

/* Population of Tables */
INSERT INTO
	`cycle`
VALUES
	('2020', DEFAULT, DEFAULT),
	(DEFAULT, DEFAULT, DEFAULT);

INSERT INTO 
	`data_subject`
VALUES
	(DEFAULT, 'Jovyln', 'Caturay', 'Busque', '09490394823', '1981-04-28', 'P1, B1, L1'),
	(DEFAULT, 'Allan', 'Angue', 'Busque', '09491128936', '1981-10-13', 'P1, B1, L1'),
	(DEFAULT, 'Amy', 'Angue', 'Cahu', '09079038153', '1995-02-06', 'P1, B1, L2'),
	(DEFAULT, 'Editha', 'Magpali', 'Hallare', '09089792979', '1956-01-15', 'P1, B1, L3'),
	(DEFAULT, 'Alvin', 'Jeciel', 'Hallare', '09186829559', '1961-06-06', 'P1, B1, L4'),
	(DEFAULT, 'Raquel', 'Pagtakhan', 'Loyola', '09184720087', '1980-05-25', 'P1, B1, L5'),
	(DEFAULT, 'Iconarclo', 'Cabrera', 'Api', '09497266639', '1969-10-09', 'P1, B1, L6'),
	(DEFAULT, 'Felicita', 'Magtibay', 'Imbis', '09183719442', '1956-06-01', 'P1, B1, L7'),
	(DEFAULT, 'Aurora', 'Imbis', 'Liberato', '09358314949', '1984-01-01', 'P1, B2, L1'),
	(DEFAULT, 'Beth', 'Bigalbal', 'Navalta', '09189218835', '1965-07-19', 'P1, B2, L2'),
	(DEFAULT, 'Theresa', 'Gumapas', 'De Ocampo', '09078466964', '1999-11-07', 'P2, B1, L1'),
	(DEFAULT, 'Felicita', 'Pabiton', 'Nable', '09186475411', '1995-06-01', 'P1, B2, L3'),
	(DEFAULT, 'Gina', 'Medina', 'Robiso', '09452579778', '1999-11-13', 'P1, B2, L4'),
	(DEFAULT, 'Helen', 'Balatico', 'Tailon', '09072914753', '1980-11-05', 'P1, B2, L5'),
	(DEFAULT, 'Jane', 'Hera', 'Histo', '09229013858', '1983-03-20', 'P1, B2, L6'),
	(DEFAULT, 'Adrian', 'Ilag', 'Dela Torre', '09239174896', '1980-06-09', 'P1, B2, L7'),
	(DEFAULT, 'Lyn', 'Lopez', 'Ledesma', '09348918364', '1994-08-23', 'P1, B2, L8'),
	(DEFAULT, 'Mary', 'Rodriguez', 'Pingol', '09251388491', '1973-10-09', 'P1, B2, L9'),
	(DEFAULT, 'Mavic', 'Ferrer', 'Gariando', '09071213894', '1995-01-23', 'P1, B2, L10'),
	(DEFAULT, 'Mona', 'Nacalaban', 'Aguinaldo', '09749712038', '1956-08-23', 'P1, B2, L11'),
	(DEFAULT, 'Nelly', 'Años', 'Zamora', '09449293018', '1991-05-18', 'P1, B2, L12'),
	(DEFAULT, 'Nelsie', 'Bigalbal', 'Nasol', '09458129443', '1965-07-19', 'P1, B2, L13'),
	(DEFAULT, 'Cherryluz', 'Casaul', 'Javier', '09749481225', '1981-03-21', 'P2, B1, L1');

INSERT INTO
	`guarantor`
VALUES
	(1), (2), (3), (4), (5), (6), (7), (8), (9), (10), (11), (12), (13), (14), (15), (16), (17), (18), (19), (20), (21), (22);

INSERT INTO
	`guarantor_cycle_map`
VALUES
	(1, '2020', 5),
	(2, '2020', 5),
	(3, '2020', 5),
	(4, '2020', 5),
	(5, '2020', 5),
	(6, '2020', 5),
	(7, '2020', 5),
	(8, '2020', 5),
	(9, '2020', 5),
	(10, '2020', 5),
	(11, '2020', 5),
	(9, '2021', 5),
	(10, '2021', 4),
	(11, '2021', 5),
	(12, '2021', 5),
	(13, '2021', 5),
	(14, '2021', 5),
	(15, '2021', 4),
	(16, '2021', 4),
	(17, '2021', 3),
	(18, '2021', 5),
	(19, '2021', 5),
	(20, '2021', 2),
	(21, '2021', 5),
	(22, '2021', 5);

INSERT INTO
	`loan`
VALUES
	(DEFAULT, 23, 9, '2021-02-10 10:00:00', 5000, DEFAULT);

INSERT INTO
	`loan_detail`
VALUES
	(DEFAULT, '2021-02-10', 5000, NULL, 500, 'Paid', '2021-02-10 08:00:00', 0, NULL, NULL, NULL, NULL, NULL, 60, 'Paid', '2021-02-10 08:01:00', 0, 1),
	(DEFAULT, '2021-03-10', 5000, NULL, 500, 'Paid', '2021-03-10 08:15:00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(DEFAULT, '2021-04-10', 5000, NULL, 500, 'Paid', '2021-04-10 08:14:00', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);