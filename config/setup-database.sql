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

CREATE TABLE `closing` (
	`closing_id` YEAR PRIMARY KEY DEFAULT (YEAR(CURDATE())),
	`closing_date` DATE NOT NULL DEFAULT (CURDATE()),
	`interest` DECIMAL(50, 2) NOT NULL,
	`processing_fee` DECIMAL(30, 2) NOT NULL,

	CONSTRAINT fk_closing_cycle_id FOREIGN KEY (`closing_id`)
		REFERENCES `cycle` (`cycle_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `roi` (
	`amount` DECIMAL(50, 2) NOT NULL,
	`status` ENUM('Pending', 'Claimed') DEFAULT 'Pending',
	`date_time_claimed` DATETIME,
	`proof` CHAR(10),
	`guarantor_id` INT UNSIGNED NOT NULL,
	`closing_id` YEAR NOT NULL,

	CONSTRAINT pk_roi PRIMARY KEY (`guarantor_id`, `closing_id`),
	CONSTRAINT fk_roi_guarantor_id FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor_cycle_map` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_roi_closing_id FOREIGN KEY (`closing_id`)
		REFERENCES `closing` (`closing_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `salary` (
	`amount`DECIMAL(50, 2) NOT NULL,
	`status` ENUM('Pending', 'Claimed') DEFAULT 'Pending',
	`date_time_claimed` DATETIME,
	`proof` CHAR(10),
	`guarantor_id` INT UNSIGNED NOT NULL,
	`closing_id` YEAR NOT NULL,

	CONSTRAINT pk_salary PRIMARY KEY (`guarantor_id`, `closing_id`),
	CONSTRAINT fk_salary_guarantor_id FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor_cycle_map` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_salary_closing_id FOREIGN KEY (`closing_id`)
		REFERENCES `closing` (`closing_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `fund` (
	`closing_id` YEAR PRIMARY KEY,
	`amount` DECIMAL(50, 2) NOT NULL,
	`claimed_by` INT UNSIGNED,
	`date_time_claimed` DATETIME,
	`proof` CHAR(15),
	`purpose` VARCHAR(2000),

	CONSTRAINT fk_fund_received_by FOREIGN KEY (`claimed_by`)
		REFERENCES `guarantor_cycle_map` (`guarantor_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_fund_closing_id FOREIGN KEY (`closing_id`)
		REFERENCES `closing` (`closing_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `user` (
	`user_id` INT UNSIGNED PRIMARY KEY,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`username` CHAR(8) NOT NULL,
	`profile_picture` CHAR(12) NOT NULL DEFAULT 'default.jpg',

	CONSTRAINT fk_user_id FOREIGN KEY (`user_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
) Engine=InnoDB;

CREATE TABLE `administrator` (
	`position` ENUM('Auditor', 'Treasurer', 'Asst. Treasurer') NOT NULL,
	`cycle_id` YEAR DEFAULT (YEAR(CURDATE())) NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,

	CONSTRAINT pk_administrator PRIMARY KEY (`user_id`, `cycle_id`),
	CONSTRAINT uc_administrator UNIQUE (`position`, `cycle_id`),
	CONSTRAINT fk_administrator_cycle_id FOREIGN KEY (`cycle_id`)
		REFERENCES `cycle` (`cycle_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_administrator_user_id FOREIGN KEY (`user_id`)
		REFERENCES `user` (`user_id`)
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
	-- Guarantors for 2020 and 2021
	(DEFAULT, 'Aurora', 'Imbis', 'Liberato', '09358314949', '1990-01-01', 'P1, B2, L8'),
	(DEFAULT, 'Beth', 'Bigalbal', 'Navalta', '09189218835', '1965-07-19', 'P1, B2, L9'),
	(DEFAULT, 'Theresa', 'Gumapas', 'De Ocampo', '09078466964', '1999-11-07', 'P1, B2, L10'),
	-- Borrowers for 2020
	(DEFAULT, 'Illuminada', 'Gumapas', 'Tampis', '09074893894', '1985-03-25', 'P1 B2 L1'),
	(DEFAULT, 'Mirella', 'Cordova', 'Lezada', '09183784784', '1994-07-06', 'P1, B2, L2'),
	(DEFAULT, 'Rafael', 'Mangalindan', 'Quito', '09349841026', '1990-05-21', 'P1, B2, L3'),
	(DEFAULT, 'Kiev', 'Santos', 'Albarico', '09238947812', '1989-09-11', 'P1, B2, L4'),
	(DEFAULT, 'Vicky', 'Lingo', 'Custodio', '09229847810', '1985-03-18', 'P1, B2, L5'),
	(DEFAULT, 'Jonas', 'Javier', 'Nazareno', '09074893091', '1987-07-01', 'P1, B2, L6'),
	(DEFAULT, 'Trisha', 'Landico', 'Anastacio', '09459038947', '1991-04-24', 'P1, B2, L6'),
	(DEFAULT, 'Aiko', 'Sy', 'Salorsano', '09180958413', '1992-07-12', 'P1, B2, L7'),
	(DEFAULT, 'Bryan', 'Garcia', 'Bueno', '09072838835', '1986-01-21', 'P1, B2, L8'),
	(DEFAULT, 'Bianca', 'Sobrevega', 'Bersabe', '09453317833', '1988-08-08', 'P1, B2, L9'),
	(DEFAULT, 'Cristina', 'Reyes', 'Pabillo', '09227184473', '1982-04-15', 'P1, B2, L10'),
	-- Guarantors for 2021
	(DEFAULT, 'Ryan', 'Pabiton', 'Nable', '09186475411', '1995-06-01', 'P1, B3, L3'),
	(DEFAULT, 'Carlo', 'Medina', 'Robiso', '09452579778', '1993-11-13', 'P1, B3, L4'),
	(DEFAULT, 'Helen', 'Balatico', 'Tailon', '09072914753', '1980-11-05', 'P1, B3, L5'),
	(DEFAULT, 'Jane', 'Hera', 'Histo', '09229013858', '1983-03-20', 'P1, B3, L6'),
	(DEFAULT, 'Adrian', 'Ilag', 'Dela Torre', '09239174896', '1980-06-09', 'P1, B3, L7'),
	(DEFAULT, 'Lyn', 'Lopez', 'Ledesma', '09348918364', '1994-08-23', 'P1, B3, L8'),
	(DEFAULT, 'Mary', 'Rodriguez', 'Pingol', '09251388491', '1973-10-09', 'P1, B3, L9'),
	(DEFAULT, 'Mavic', 'Ferrer', 'Gariando', '09071213894', '1995-01-23', 'P1, B3, L10'),
	(DEFAULT, 'Mona', 'Nacalaban', 'Aguinaldo', '09749712038', '1956-08-23', 'P1, B3, L11'),
	(DEFAULT, 'Nelly', 'Años', 'Zamora', '09449293018', '1991-05-18', 'P1, B3, L12'),
	(DEFAULT, 'Sofia', 'Bigalbal', 'Indico', '09458129443', '1988-07-19', 'P1, B3, L13'),
	-- Borrowers for 2021
	(DEFAULT, 'Cherryluz', 'Casaul', 'Javier', '09749481225', '1981-03-21', 'P1, B4, L1'),
	(DEFAULT, 'Arkin', 'Diaz', 'Hicban', '09269866738', '1984-05-27', 'P1, B4, L2'),
	(DEFAULT, 'Norlyn', 'Mariano', 'Marahay', '09103847832', '1976-09-22', 'P1, B4, L3'),
	(DEFAULT, 'Judie Ann', 'De Leon', 'Ancayan', '09349847821', '1980-05-21', 'P1, B4, L4');

INSERT INTO
	`guarantor`
VALUES
	(1), (2), (3), (4), (5), (6), (7), (8), (9), (10), (11), (23), (24), (25), (26), (27), (28), (29), (30), (31), (32), (33);

INSERT INTO
	`guarantor_cycle_map`
VALUES
	(1, '2020', 2),
	(2, '2020', 1),
	(3, '2020', 3),
	(4, '2020', 1),
	(5, '2020', 1),
	(6, '2020', 1),
	(7, '2020', 1),
	(8, '2020', 1),
	(9, '2020', 1),
	(10, '2020', 2),
	(11, '2020', 1),
	(9, '2021', 5),
	(10, '2021', 4),
	(11, '2021', 1),
	(23, '2021', 5),
	(24, '2021', 5),
	(25, '2021', 5),
	(26, '2021', 4),
	(27, '2021', 4),
	(28, '2021', 3),
	(29, '2021', 5),
	(30, '2021', 1),
	(31, '2021', 2),
	(32, '2021', 5),
	(33, '2021', 1);

INSERT INTO
	`user`
VALUES
	(9, 'aurora.liberato@gmail.com', '$2y$10$CtnzbINrxyH1wLupEyJ2U.ta2WbMK5PdJi8AXNPEK.dQffNpNr5.2', 'Aurora', '9.jpg'),
	(10, 'beth.nevalta@gmail.com', '$2y$10$MTlKxMqwYNONcXUEi.wFMuWZyOCejJxIqtt5oKs3r7ez9BFl4b9uW', 'Beth', '10.jpg'),
	(11, 'ma_theresa7@yahoo.com', '$2y$10$uyNGNk8Ccj35tfSpFOWVte4rOjE02VDMYTBMYJAqULRysMSDYWjuO', 'Theresa', '11.jpg'),
	(23, 'ryan.nable@gmail.com', '$2y$10$nOpsIELnJbe5kQVWFiMKqOPX8MGprsty2Mto.1Uj7Cb0eNS6uPK7a', 'Ryan', '23.jpg'),
	(24, 'carlo.robiso@gmail.com', '$2y$10$jxR6Yl2Pzb422tKFtJBz2.SVXU3XLcAeNRQ4wzLz6TzF7Lh4w3OpW', 'Carlo', '24.jpg'),
	(34, 'cherry.javier@gmail.com', '$2y$10$XXUH1F2sBoSNQ2HsXfS5aekKncilh1aj27ijIuiCV4JpWvDnhjC5.', 'Cherry', DEFAULT);

INSERT INTO
	`administrator`
VALUES
	('Auditor', '2020', 9),
	('Treasurer', '2020', 10),
	('Asst. Treasurer', '2020', 11),
	('Auditor', '2021', 23),
	('Treasurer', '2021', 24),
	('Asst. Treasurer', '2021', 11);

INSERT INTO
	`loan`
VALUES
	(DEFAULT, 34, 9, '2021-02-10 07:59:00', 5000, 'Closed', '1.jpg', NULL, '2021'),
	(DEFAULT, 35, 10, '2021-06-21 08:59:00', 25000, 'Closed', '2.jpg', '2.pdf', '2021'),
	(DEFAULT, 36, 9, '2021-03-12 09:59:00', 10000, 'Closed', '3.jpg', '3.jpg', '2021'),
	(DEFAULT, 37, 9, '2021-04-05 10:59:00', 15000, 'Closed', '4.jpg', '4.jpg', '2021'),
	(DEFAULT, 34, 1, '2020-03-13 07:59:00', 8000, 'Closed', '5.jpg',  NULL, '2020');

INSERT INTO
	`principal_payment`
VALUES
	(DEFAULT, 3000, '2021-06-23 08:00:00', 1),
	(DEFAULT, 2000, '2021-08-10 08:00:00', 1),
	(DEFAULT, 10000, '2021-07-18 09:00:00', 2),
	(DEFAULT, 15000, '2021-08-18 09:00:00', 2),
	(DEFAULT, 5000, '2021-05-12 10:00:00', 3),
	(DEFAULT, 5000, '2021-06-03 10:00:00', 3),
	(DEFAULT, 5000, '2021-06-05 11:00:00', 4),
	(DEFAULT, 5000, '2021-07-23 11:00:00', 4),
	(DEFAULT, 2000, '2021-09-05 11:00:00', 4),
	(DEFAULT, 3000, '2021-10-05 11:00:00', 4),
	(DEFAULT, 8000, '2021-05-26 08:00:00', 5);

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
	(DEFAULT, '2021-08-16', 1500, 'Paid', 2), -- 13
	(DEFAULT, '2021-03-12', 1000, 'Paid', 3), -- 14
	(DEFAULT, '2021-04-12', 1000, 'Paid', 3), -- 15
	(DEFAULT, '2021-05-12', 1000, 'Paid', 3), -- 16
	(DEFAULT, '2021-04-05', 1500, 'Paid', 4), -- 17
	(DEFAULT, '2021-05-05', 1500, 'Paid', 4), -- 18
	(DEFAULT, '2021-06-05', 1500, 'Paid', 4), -- 19
	(DEFAULT, '2021-07-05', 1000, 'Paid', 4), -- 20
	(DEFAULT, '2021-08-05', 500, 'Paid', 4),  -- 21
	(DEFAULT, '2021-09-05', 500, 'Paid', 4),  -- 22
	(DEFAULT, '2021-10-05', 300, 'Paid', 4),  -- 23
	(DEFAULT, '2020-03-13', 800, 'Paid', 5), -- 24
	(DEFAULT, '2020-04-13', 800, 'Paid', 5), -- 25
	(DEFAULT, '2020-05-13', 800, 'Paid', 5); -- 26

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
	(DEFAULT, 1500, '2021-08-18 09:01:00', 13),
	(DEFAULT, 1000, '2021-03-12 10:01:00', 14),
	(DEFAULT, 1000, '2021-04-12 10:01:00', 15),
	(DEFAULT, 1000, '2021-05-12 10:01:00', 16),
	(DEFAULT, 1500, '2021-04-05 11:01:00', 17),
	(DEFAULT, 1500, '2021-05-05 11:01:00', 18),
	(DEFAULT, 1500, '2021-06-05 11:01:00', 19),
	(DEFAULT, 1000, '2021-07-05 11:01:00', 20),
	(DEFAULT, 500, '2021-08-05 11:01:00', 21),
	(DEFAULT, 500, '2021-09-05 11:01:00', 22),
	(DEFAULT, 300, '2021-10-05 11:01:00', 23),
	(DEFAULT, 800, '2020-03-13 08:00:00', 24),
	(DEFAULT, 800, '2020-04-13 08:00:00', 25),
	(DEFAULT, 800, '2020-05-13 08:00:00', 26);

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
	(DEFAULT, '2021-08-10', 30, 'Paid', 1),  -- 5
	(DEFAULT, '2021-03-12', 110, 'Paid', 3), -- 6
	(DEFAULT, '2021-04-05', 160, 'Paid', 4), -- 7
	(DEFAULT, '2021-07-05', 110, 'Paid', 4), -- 8
	(DEFAULT, '2021-10-05', 40, 'Paid', 4),  -- 9
	(DEFAULT, '2020-03-13', 90, 'Paid', 5); -- 10

INSERT INTO
	`processing_fee_payment`
VALUES
	(DEFAULT, 60, '2021-02-10 08:02:00', 1),
	(DEFAULT, 260, '2021-03-16 09:02:00', 2),
	(DEFAULT, 60, '2021-05-10 08:02:00', 3),
	(DEFAULT, 260, '2021-06-16 09:02:00', 4),
	(DEFAULT, 30, '2021-08-10 08:02:00', 5),
	(DEFAULT, 110, '2021-03-12 10:02:00', 6),
	(DEFAULT, 160, '2021-04-05 11:02:00', 7),
	(DEFAULT, 110, '2021-07-05 11:02:00', 8),
	(DEFAULT, 40, '2021-10-05 11:02:00', 9),
	(DEFAULT, 90, '2020-03-13 08:02:00', 10);