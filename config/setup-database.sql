USE mysql;
DROP DATABASE IF EXISTS `cooperative`;
CREATE DATABASE `cooperative`
	CHARACTER SET  utf8mb4
	COLLATE utf8mb4_0900_ai_ci;
USE `cooperative`;

CREATE TABLE `cycle` (
	`cycle_id` YEAR PRIMARY KEY DEFAULT (YEAR(CURDATE())),
	`membership_fee` SMALLINT NOT NULL DEFAULT 12000,
	`interest_rate` DECIMAL(3, 2) NOT NULL DEFAULT 0.10, -- in decimal
	`min_processing_fee` SMALLINT NOT NULL DEFAULT 20
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
	`loan_date_time` DATETIME NOT NULL DEFAULT (NOW()),
	`principal` DECIMAL(10, 2) NOT NULL,
	`status` ENUM('Active', 'Closed') DEFAULT 'Active' NOT NULL,
	`proof` CHAR(10) NOT NULL,
	`collateral` CHAR(10),
	`cycle_id` YEAR DEFAULT (YEAR(CURDATE())),

	CONSTRAINT fk_loan_borrower_id FOREIGN KEY (`borrower_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_loan_guarantor_id FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_loan_cycle_id FOREIGN KEY (`cycle_id`)
		REFERENCES `cycle` (`cycle_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `principal_payment` (
	`principal_payment_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`amount` DECIMAL(10, 2) NOT NULL,
	`date_time_paid` DATETIME NOT NULL DEFAULT (NOW()),
	`loan_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_principal_payment_loan_id FOREIGN KEY (`loan_id`)
		REFERENCES `loan` (`loan_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `interest` (
	`interest_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`interest_date` DATE NOT NULL DEFAULT (CURDATE()),
	`amount` DECIMAL(9, 2) NOT NULL,
	`status` ENUM('Paid', 'Pending', 'Overdue', 'Late') DEFAULT 'Pending' NOT NULL,
	`loan_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_interest_loan_id FOREIGN KEY (`loan_id`)
		REFERENCES `loan` (`loan_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `interest_payment` (
	`interest_payment_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`amount` DECIMAL(9, 2) NOT NULL,
	`date_time_paid` DATETIME NOT NULL DEFAULT (NOW()),
	`interest_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_interest_payment_interest_id FOREIGN KEY (`interest_id`)
		REFERENCES `interest` (`interest_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `penalty` (
	`penalty_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`penalty_date` DATE NOT NULL,
	`amount` DECIMAL(9, 2) NOT NULL,
	`status` ENUM('Paid', 'Pending') DEFAULT 'Pending' NOT NULL,
	`interest_id` INT UNSIGNED NOT NULL,
	`loan_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_penalty_interest_id FOREIGN KEY (`interest_id`)
		REFERENCES `interest` (`interest_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_penalty_loan_id FOREIGN KEY (`loan_id`)
		REFERENCES `loan` (`loan_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `penalty_payment` (
	`penalty_payment_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`amount` DECIMAL(9, 2) NOT NULL,
	`date_time_paid` DATETIME NOT NULL DEFAULT (NOW()),
	`penalty_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_penalty_payment_penalty_id FOREIGN KEY (`penalty_id`)
		REFERENCES `penalty` (`penalty_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `processing_fee` (
	`processing_fee_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`processing_fee_date` DATE NOT NULL DEFAULT (CURDATE()),
	`amount` DECIMAL(8, 2) NOT NULL,
	`status` ENUM('Paid', 'Pending') DEFAULT 'Pending' NOT NULL,
	`loan_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_processing_fee_loan_id FOREIGN KEY (`loan_id`)
		REFERENCES `loan` (`loan_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `processing_fee_payment` (
	`processing_fee_payment_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`amount` DECIMAL(8, 2) NOT NULL,
	`date_time_paid` DATETIME NOT NULL DEFAULT (NOW()),
	`processing_fee_id` INT UNSIGNED NOT NULL,

	CONSTRAINT fk_processing_fee_payment_processing_fee_id FOREIGN KEY (`processing_fee_id`)
		REFERENCES `processing_fee` (`processing_fee_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `administrator` (
	`admin_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`data_subject_id` INT UNSIGNED,

	CONSTRAINT fk_admin_data_subject_id FOREIGN KEY (`data_subject_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

/* Population of Tables */
INSERT INTO
	`cycle` (`cycle_id`)
VALUES
	('2020'),
	('2021');

INSERT INTO 
	`data_subject`
VALUES
	-- Guarantors for 2020
	(DEFAULT, 'Jovyln', 'Caturay', 'Busque', '09490394823', '1981-04-28', 'P1, B1, L1'),
	(DEFAULT, 'Allan', 'Angue', 'Busque', '09491128936', '1981-10-13', 'P1, B1, L1'),
	(DEFAULT, 'Amy', 'Angue', 'Cahu', '09079038153', '1995-02-06', 'P1, B1, L2'),
	(DEFAULT, 'Editha', 'Magpali', 'Hallare', '09089792979', '1956-01-15', 'P1, B1, L3'),
	(DEFAULT, 'Alvin', 'Jeciel', 'Hallare', '09186829559', '1961-06-06', 'P1, B1, L4'),
	(DEFAULT, 'Raquel', 'Pagtakhan', 'Loyola', '09184720087', '1980-05-25', 'P1, B1, L5'),
	(DEFAULT, 'Iconarclo', 'Cabrera', 'Api', '09497266639', '1969-10-09', 'P1, B1, L6'),
	(DEFAULT, 'Felicita', 'Magtibay', 'Imbis', '09183719442', '1956-06-01', 'P1, B1, L7'),
	-- Guarantors for 2021
	(DEFAULT, 'Aurora', 'Imbis', 'Liberato', '09358314949', '1990-01-01', 'P1, B3, L1'),
	(DEFAULT, 'Beth', 'Bigalbal', 'Navalta', '09189218835', '1965-07-19', 'P1, B3, L2'),
	(DEFAULT, 'Theresa', 'Gumapas', 'De Ocampo', '09078466964', '1999-11-07', 'P2, B1, L1'),
	(DEFAULT, 'Felicita', 'Pabiton', 'Nable', '09186475411', '1995-06-01', 'P1, B3, L3'),
	(DEFAULT, 'Gina', 'Medina', 'Robiso', '09452579778', '1999-11-13', 'P1, B3, L4'),
	(DEFAULT, 'Helen', 'Balatico', 'Tailon', '09072914753', '1980-11-05', 'P1, B3, L5'),
	(DEFAULT, 'Jane', 'Hera', 'Histo', '09229013858', '1983-03-20', 'P1, B3, L6'),
	(DEFAULT, 'Adrian', 'Ilag', 'Dela Torre', '09239174896', '1980-06-09', 'P1, B3, L7'),
	(DEFAULT, 'Lyn', 'Lopez', 'Ledesma', '09348918364', '1994-08-23', 'P1, B3, L8'),
	(DEFAULT, 'Mary', 'Rodriguez', 'Pingol', '09251388491', '1973-10-09', 'P1, B3, L9'),
	(DEFAULT, 'Mavic', 'Ferrer', 'Gariando', '09071213894', '1995-01-23', 'P1, B3, L10'),
	(DEFAULT, 'Mona', 'Nacalaban', 'Aguinaldo', '09749712038', '1956-08-23', 'P1, B3, L11'),
	(DEFAULT, 'Nelly', 'Años', 'Zamora', '09449293018', '1991-05-18', 'P1, B3, L12'),
	(DEFAULT, 'Nelsie', 'Bigalbal', 'Nasol', '09458129443', '1965-07-19', 'P1, B3, L13'),
	-- Borrowers for 2021
	(DEFAULT, 'Cherryluz', 'Casaul', 'Javier', '09749481225', '1981-03-21', 'P1, B4, L1'),
	(DEFAULT, 'Arkin', 'Diaz', 'Hicban', '09269866738', '1984-05-27', 'P1, B4, L2');

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
	`administrator`
VALUES
	(DEFAULT, 'ma_theresa7@yahoo.com', '$2y$10$uyNGNk8Ccj35tfSpFOWVte4rOjE02VDMYTBMYJAqULRysMSDYWjuO', 11);

INSERT INTO
	`loan`
VALUES
	(DEFAULT, 23, 9, '2021-02-10 10:00:00', 5000, 'Closed', '1.jpg', NULL, '2021'),
	(DEFAULT, 24, 10, '2021-06-21 11:00:00', 25000, 'Closed', '2.jpg', '2.pdf', '2021');

INSERT INTO
	`principal_payment`
VALUES
	(DEFAULT, 3000, '2021-06-23 08:00:00', 1),
	(DEFAULT, 2000, '2021-08-10 08:00:00', 1),
	(DEFAULT, 10000, '2021-07-18 09:00:00', 2),
	(DEFAULT, 15000, '2021-08-18 09:00:00', 2);

INSERT INTO
	`interest`
VALUES
	(DEFAULT, '2021-02-10', 500, 'Paid', 1),  -- 1
	(DEFAULT, '2021-03-10', 500, 'Paid', 1),  -- 2
	(DEFAULT, '2021-03-16', 2500, 'Paid', 2), -- 3
	(DEFAULT, '2021-04-10', 500, 'Paid', 1),  -- 4
	(DEFAULT, '2021-04-16', 2500, 'Paid', 2), -- 5
	(DEFAULT, '2021-05-10', 500, 'Paid', 1),  -- 6
	(DEFAULT, '2021-05-16', 2500, 'Paid', 2), -- 7
	(DEFAULT, '2021-06-10', 500, 'Late', 1),  -- 8
	(DEFAULT, '2021-06-16', 2500, 'Late', 2), -- 9
	(DEFAULT, '2021-07-10', 200, 'Paid', 1),  -- 10
	(DEFAULT, '2021-07-16', 2500, 'Paid', 2), -- 11
	(DEFAULT, '2021-08-10', 200, 'Paid', 1),  -- 12
	(DEFAULT, '2021-08-16', 1500, 'Paid', 2); -- 13

INSERT INTO
	`interest_payment`
VALUES
	(DEFAULT, 500, '2021-02-10 08:00:00', 1),
	(DEFAULT, 500, '2021-03-10 08:00:00', 2),
	(DEFAULT, 2500, '2021-03-16 09:01:00', 3),
	(DEFAULT, 500, '2021-04-10 08:00:00', 4),
	(DEFAULT, 2500, '2021-04-15 09:01:00', 5),
	(DEFAULT, 500, '2021-05-10 08:00:00', 6),
	(DEFAULT, 2500, '2021-05-16 09:01:00', 7),
	(DEFAULT, 1000, '2021-06-16 09:00:00', 9),
	(DEFAULT, 1500, '2021-06-18 09:00:00', 9),
	(DEFAULT, 200, '2021-07-10 08:00:00', 10),
	(DEFAULT, 2500, '2021-07-16 09:01:00', 11),
	(DEFAULT, 200, '2021-08-10 08:00:00', 12),
	(DEFAULT, 500, '2021-08-16 08:00:00', 8),
	(DEFAULT, 1500, '2021-08-18 09:01:00', 13);

INSERT INTO
	`penalty`
VALUES
	(DEFAULT, '2021-06-11', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-12', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-13', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-14', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-15', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-16', 17, 'Paid', 8, 1),
	(DEFAULT, '2021-06-17', 500, 'Paid', 8, 1),
	(DEFAULT, '2021-06-17', 50, 'Paid', 9, 2),
	(DEFAULT, '2021-06-18', 50, 'Paid', 9, 2);

INSERT INTO
	`penalty_payment`
VALUES
	(DEFAULT, 17, '2021-08-16 08:01:00', 1),
	(DEFAULT, 17, '2021-08-16 08:02:00', 2),
	(DEFAULT, 17, '2021-08-16 08:03:00', 3),
	(DEFAULT, 17, '2021-08-16 08:04:00', 4),
	(DEFAULT, 17, '2021-08-16 08:05:00', 5),
	(DEFAULT, 17, '2021-08-16 08:06:00', 6),
	(DEFAULT, 500, '2021-08-16 08:07:00', 7),
	(DEFAULT, 50, '2021-06-18 09:00:00', 8),
	(DEFAULT, 50, '2021-06-18 09:01:00', 9);

INSERT INTO
	`processing_fee`
VALUES
	(DEFAULT, '2021-02-10', 60, 'Paid', 1),  -- 1
	(DEFAULT, '2021-03-16', 260, 'Paid', 2), -- 2
	(DEFAULT, '2021-05-10', 60, 'Paid', 1),  -- 3
	(DEFAULT, '2021-06-16', 260, 'Paid', 2), -- 4
	(DEFAULT, '2021-08-10', 30, 'Paid', 1);  -- 5

INSERT INTO
	`processing_fee_payment`
VALUES
	(DEFAULT, 60, '2021-02-10 08:02:00', 1),
	(DEFAULT, 260, '2021-03-16 09:02:00', 2),
	(DEFAULT, 60, '2021-05-10 08:02:00', 3),
	(DEFAULT, 260, '2021-06-16 09:02:00', 4),
	(DEFAULT, 30, '2021-08-10 08:02:00', 5);