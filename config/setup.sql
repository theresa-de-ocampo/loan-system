USE mysql;
DROP DATABASE IF EXISTS `ciudad_nuevo`;
CREATE DATABASE `ciudad_nuevo`;
USE `ciudad_nuevo`;

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

	CONSTRAINT fk_loan_borrower_id FOREIGN KEY (`borrower_id`)
		REFERENCES `data_subject` (`data_subject_id`)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_loan_guarantor_id FOREIGN KEY (`guarantor_id`)
		REFERENCES `guarantor` (`guarantor_id`)
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

-- [STORED PROCEDURE] calculate_age
CREATE PROCEDURE calculate_age (
	IN p_bday DATE,
	OUT p_age INT UNSIGNED
)
	SELECT
		TIMESTAMPDIFF(YEAR, p_bday, CURDATE())
	INTO
		p_age;

-- [STORED PROCEDURE] precise_timestampdiff_month
CREATE PROCEDURE precise_timestampdiff_month (
	IN p_start_date DATE,
	IN p_end_date DATE,
	OUT p_date_difference DECIMAL(6, 4)
)
	SET p_date_difference = 
		TIMESTAMPDIFF(MONTH, p_start_date, p_end_date) +
		DATEDIFF(
			p_end_date,
			p_start_date + INTERVAL TIMESTAMPDIFF(MONTH, p_start_date, p_end_date) MONTH
		) /
		DATEDIFF(
			p_start_date + INTERVAL TIMESTAMPDIFF(MONTH, p_start_date, p_end_date) + 1 MONTH,
			p_start_date + INTERVAL TIMESTAMPDIFF(MONTH, p_start_date, p_end_date) MONTH
		);

-- [STORED PROCEDURE] get_interest_rate
CREATE PROCEDURE get_interest_rate(OUT p_interest_rate DECIMAL(3, 2))
	SELECT
		`interest_rate`
	INTO
		p_interest_rate
	FROM
		`cycle`
	WHERE
		`cycle_id` = YEAR(CURDATE());

-- [STORED PROCEDURE] get_min_processing_fee
CREATE PROCEDURE get_min_processing_fee(OUT p_min_processing_fee SMALLINT)
	SELECT
		`min_processing_fee`
	INTO
		p_min_processing_fee
	FROM
		`cycle`
	WHERE
		`cycle_id` = YEAR(CURDATE());

-- [STORED PROCEDURE] get_processing_fee()
DELIMITER $$
CREATE PROCEDURE get_processing_fee(
	IN p_principal_balance DECIMAL(10, 2),
	OUT p_processing_fee DECIMAL(8, 2)
)
BEGIN
	DECLARE lv_interest_rate DECIMAL(3, 2);
	DECLARE lv_min_processing_fee SMALLINT;

	CALL get_interest_rate(lv_interest_rate);
	CALL get_min_processing_fee(lv_min_processing_fee);

	IF p_principal_balance > 1000 THEN
		SET p_processing_fee = lv_min_processing_fee + (((p_principal_balance - 1000) / 1000) * 10);
	ELSE
		SET p_processing_fee = lv_min_processing_fee;
	END IF;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_accrued_interest
CREATE PROCEDURE get_accrued_interest (
	IN p_loan_id INT UNSIGNED,
	OUT p_accrued_interest DECIMAL(10, 2)
)
	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		p_accrued_interest
	FROM
		`interest`
	WHERE
		`loan_id` = p_loan_id;

-- [STORED PROCEDURE] get_principal_balance
DELIMITER $$
CREATE PROCEDURE get_principal_balance (
	IN p_loan_id INT UNSIGNED,
	OUT p_balance DECIMAL(10, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(10, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`principal_payment`
	WHERE
		`loan_id` = p_loan_id;

	SELECT
		`principal`
	INTO
		amount_to_be_paid
	FROM
		`loan`
	WHERE 
		`loan_id` = p_loan_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_principal_balance_by_date_time
DELIMITER $$
CREATE PROCEDURE get_principal_balance_by_date_time (
	IN p_loan_id INT UNSIGNED,
	IN p_date_time DATETIME,
	OUT p_balance DECIMAL(9, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`principal_payment`
	WHERE
		`loan_id` = p_loan_id AND
		`date_time_paid` < p_date_time;

	SELECT
		`principal`
	INTO
		amount_to_be_paid
	FROM
		`loan`
	WHERE 
		`loan_id` = p_loan_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_interest_balance
DELIMITER $$
CREATE PROCEDURE get_interest_balance (
	IN p_interest_id INT UNSIGNED,
	OUT p_balance DECIMAL(9, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`interest_payment`
	WHERE
		`interest_id` = p_interest_id;

	SELECT
		`amount`
	INTO
		amount_to_be_paid
	FROM
		`interest`
	WHERE 
		`interest_id` = p_interest_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_interest_balance_by_date
DELIMITER $$
CREATE PROCEDURE get_interest_balance_by_date (
	IN p_interest_id INT UNSIGNED,
	IN p_penalty_date DATE,
	OUT p_balance DECIMAL(9, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`interest_payment`
	WHERE
		`interest_id` = p_interest_id AND
		DATE(`date_time_paid`) <= p_penalty_date;

	SELECT
		`amount`
	INTO
		amount_to_be_paid
	FROM
		`interest`
	WHERE 
		`interest_id` = p_interest_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_penalty_balance
DELIMITER $$
CREATE PROCEDURE get_penalty_balance (
	IN p_penalty_id INT UNSIGNED,
	OUT p_balance DECIMAL(9, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`penalty_payment`
	WHERE
		`penalty_id` = p_penalty_id;

	SELECT
		`amount`
	INTO
		amount_to_be_paid
	FROM
		`penalty`
	WHERE
		`penalty_id` = p_penalty_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_processing_fee_balance
DELIMITER $$
CREATE PROCEDURE get_processing_fee_balance (
	IN p_processing_fee_id INT UNSIGNED,
	OUT p_balance DECIMAL(8, 2)
)
BEGIN
	DECLARE amount_to_be_paid, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`processing_fee_payment`
	WHERE
		`processing_fee_id` = p_processing_fee_id;

	SELECT
		`amount`
	INTO
		amount_to_be_paid
	FROM
		`processing_fee`
	WHERE
		`processing_fee_id` = p_processing_fee_id;

	SET p_balance = amount_to_be_paid - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_interest_receivables
DELIMITER $$
CREATE PROCEDURE get_interest_receivables (
	IN p_loan_id INT UNSIGNED,
	OUT p_total_receivables DECIMAL(9, 2)
)
BEGIN
	DECLARE total_interest, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_interest
	FROM
		`interest`
	WHERE
		`loan_id` = p_loan_id;

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`interest_payment`
	WHERE
		`interest_id` IN (
				SELECT
					`interest_id`
				FROM
					`interest`
				WHERE
					`loan_id` = p_loan_id
			);

	SET p_total_receivables = total_interest - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_penalty_receivables
DELIMITER $$
CREATE PROCEDURE get_penalty_receivables (
	IN p_loan_id INT UNSIGNED,
	OUT p_total_receivables DECIMAL(9, 2)
)
BEGIN
	DECLARE total_penalties, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_penalties
	FROM
		`penalty`
	WHERE
		`loan_id` = p_loan_id;

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`penalty_payment`
	WHERE
		`penalty_id` IN (
				SELECT
					`penalty_id`
				FROM
					`penalty`
				WHERE
					`loan_id` = p_loan_id
			);

	SET p_total_receivables = total_penalties - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_processing_fee_receivables
DELIMITER $$
CREATE PROCEDURE get_processing_fee_receivables (
	IN p_loan_id INT UNSIGNED,
	OUT p_total_receivables DECIMAL(8, 2)
)
BEGIN
	DECLARE total_processing_fees, total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_processing_fees
	FROM
		`processing_fee`
	WHERE
		`loan_id` = p_loan_id;

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		total_payment
	FROM
		`processing_fee_payment`
	WHERE
		`processing_fee_id` IN (
				SELECT
					`processing_fee_id`
				FROM
					`processing_fee`
				WHERE
					`loan_id` = p_loan_id
			);

	SET p_total_receivables = total_processing_fees - total_payment;
END $$
DELIMITER ;

-- [STORED PROCEDURE] check_loan_status
DELIMITER $$
CREATE PROCEDURE check_loan_status (
	IN p_loan_id INT UNSIGNED
)
BEGIN
	DECLARE principal_flag, interest_flag, penalty_flag, processing_fee_flag TINYINT;

	CALL get_principal_balance(p_loan_id, @balance);
	SELECT @balance INTO principal_flag;

	CALL get_interest_receivables(p_loan_id, @total_receivables);
	SELECT @total_receivables INTO interest_flag;

	CALL get_penalty_receivables(p_loan_id, @total_receivables);
	SELECT @total_receivables INTO penalty_flag;

	CALL get_processing_fee_receivables(p_loan_id, @total_receivables);
	SELECT @total_receivables INTO processing_fee_flag;

	IF principal_flag = 0 AND interest_flag = 0 AND penalty_flag = 0 AND processing_fee_flag = 0 THEN
		UPDATE `loan` SET `status` = 'Closed' WHERE `loan_id` = p_loan_id;
	END IF;
END $$
DELIMITER ;

-- [STORED PROCEDURE] get_total_receivables_by_loan
DELIMITER $$
CREATE PROCEDURE get_total_receivables_by_loan (
	IN p_loan_id INT UNSIGNED,
	OUT p_total_receivables DECIMAL(50, 2)
)
BEGIN
	CALL get_principal_balance(p_loan_id, @principal_balance);
	CALL get_interest_receivables(p_loan_id, @interest_receivables);
	CALL get_penalty_receivables(p_loan_id, @penalty_receivables);
	CALL get_processing_fee_receivables(p_loan_id, @processing_fee_receivables);

	SET p_total_receivables = @principal_balance + @interest_receivables + @penalty_receivables + @processing_fee_receivables;
END $$
DELIMITER ;

-- [STORED PROCEDURE] check_for_interest
DELIMITER $$
CREATE PROCEDURE check_for_interest()
BEGIN
	DECLARE lv_end_of_table TINYINT DEFAULT 0;
	DECLARE lv_today DATE DEFAULT CURDATE();
	DECLARE lv_flag DECIMAL(6, 4);
	DECLARE lv_loan_id INT UNSIGNED;
	DECLARE lv_loan_date DATE;
	DECLARE lv_interest_rate DECIMAL(3, 2);
	DECLARE lv_interest_amount DECIMAL(9, 2);

	DECLARE loan_cursor
		CURSOR FOR
			SELECT `loan_id`, `loan_date_time` FROM `loan` WHERE `status` = 'Active';

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET lv_end_of_table = 1;

	OPEN loan_cursor;
	loan_loop: LOOP
		FETCH loan_cursor INTO lv_loan_id, lv_loan_date;

		IF lv_end_of_table = 1 THEN
			LEAVE loan_loop;
		END IF;

		CALL precise_timestampdiff_month(lv_loan_date, lv_today, lv_flag);

		-- IF lv_flag is a number without a fractional part, i.e., 1.0, 2.0, 3.0, and so on.
		IF CEIL(lv_flag) = lv_flag THEN
			CALL get_interest_rate(lv_interest_rate);
			CALL get_principal_balance(lv_loan_id, @principal_balance);
			SET lv_interest_amount = @principal_balance * lv_interest_rate;

			INSERT INTO
				`interest`
			VALUES
				(DEFAULT, lv_today, lv_interest_amount, DEFAULT, lv_loan_id);
		END IF;
	END LOOP loan_loop;
	CLOSE loan_cursor;
END $$
DELIMITER ;

-- [STORED PROCEDURE] check_for_penalty
DELIMITER $$
CREATE PROCEDURE check_for_penalty()
BEGIN
	DECLARE lv_end_of_table TINYINT DEFAULT 0;
	DECLARE lv_today DATE DEFAULT CURDATE();
	DECLARE lv_loan_id INT UNSIGNED;
	DECLARE lv_interest_id INT UNSIGNED;
	DECLARE lv_interest_date DATE;
	DECLARE lv_interest_status CHAR(10);
	DECLARE lv_penalty_amount DECIMAL(9, 2);

	DECLARE interest_cursor
		CURSOR FOR
			SELECT
				`loan`.`loan_id`,
				`interest_id`,
				`interest_date`,
				`interest`.`status`
			FROM
				`loan`
			INNER JOIN `interest`
				USING (`loan_id`)
			WHERE
				`loan`.`status` = 'Active' AND
				`interest`.`status` IN ('Pending', 'Overdue');

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET lv_end_of_table = 1;

	OPEN interest_cursor;
	interest_loop: LOOP
		FETCH interest_cursor INTO lv_loan_id, lv_interest_id, lv_interest_date, lv_interest_status;

		IF lv_end_of_table = 1 THEN
			LEAVE interest_loop;
		END IF;

		CALL get_interest_balance(lv_interest_id, @interest_balance);
		IF lv_today BETWEEN DATE_ADD(lv_interest_date, INTERVAL 1 DAY) AND DATE_ADD(lv_interest_date, INTERVAL 7 DAY) THEN
			IF lv_today <= DATE_ADD(lv_interest_date, INTERVAL 6 DAY) THEN
				IF lv_interest_status = 'Pending' THEN
					UPDATE `interest` SET `status` = 'Overdue' WHERE `interest_id` = lv_interest_id;
				END IF;
				SET lv_penalty_amount = ROUND(@interest_balance / DAY(LAST_DAY(lv_today)));
			ELSE
				SET lv_penalty_amount = @interest_balance;
			END IF;

			INSERT INTO
				`penalty`
			VALUES
				(DEFAULT, lv_today, lv_penalty_amount, DEFAULT, lv_interest_id, lv_loan_id);
		END IF;
	END LOOP interest_loop;
	CLOSE interest_cursor;
END $$
DELIMITER ;

-- [STORED PROCEDURE] check_for_processing_fee
DELIMITER $$
CREATE PROCEDURE check_for_processing_fee()
BEGIN
	DECLARE lv_end_of_table TINYINT DEFAULT 0;
	DECLARE lv_today DATE DEFAULT CURDATE();
	DECLARE lv_date_difference DECIMAL(6, 4);
	DECLARE lv_loan_id INT UNSIGNED;
	DECLARE lv_loan_date DATE;
	DECLARE lv_principal DECIMAL(10, 2);
	DECLARE lv_paid DECIMAL(10, 2);
	DECLARE lv_processing_fee_amount DECIMAL(8, 2);

	DECLARE loan_cursor
		CURSOR FOR
			SELECT
				`loan`.`loan_id`,
				`loan_date_time`,
				`loan`.`principal`,
				COALESCE(SUM(`principal_payment`.`amount`), 0) AS paid
			FROM
				`loan`
			LEFT JOIN `principal_payment`
				USING (`loan_id`)
			WHERE
				`status` = 'Active'
			GROUP BY
				`loan`.`loan_id`
			HAVING
				paid < `loan`.`principal`;


	DECLARE CONTINUE HANDLER FOR NOT FOUND SET lv_end_of_table = 1;

	OPEN loan_cursor;
	loan_loop: LOOP
		FETCH loan_cursor INTO lv_loan_id, lv_loan_date, lv_principal, lv_paid;

		IF lv_end_of_table = 1 THEN
			LEAVE loan_loop;
		END IF;

		CALL precise_timestampdiff_month(lv_loan_date, lv_today, lv_date_difference);
		
		IF (lv_date_difference != 0 AND ((lv_date_difference % 3) = 0)) = 1 THEN
			CALL get_processing_fee(lv_principal - lv_paid, lv_processing_fee_amount);

			INSERT INTO `processing_fee`
			VALUES (DEFAULT, lv_today, lv_processing_fee_amount, DEFAULT, lv_loan_id);
		END IF;
	END LOOP loan_loop;
	CLOSE loan_cursor;
END $$
DELIMITER ;

-- [EVENT] check_accruals (Run everday at 12:10 A.M.)
/*SET GLOBAL event_scheduler = ON;
DELIMITER $$
CREATE EVENT check_accruals
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 1 DAY + INTERVAL 10 MINUTE)
DO
BEGIN
	CALL check_for_interest();
	CALL check_for_penalty();
	CALL check_for_processing_fee();
END $$
DELIMITER ;*/

-- [TRIGGER] after_principal_payment
CREATE TRIGGER after_principal_payment
AFTER INSERT ON `principal_payment`
FOR EACH ROW
	CALL check_loan_status(NEW.loan_id);

-- [TRIGGER] after_interest_payment
DELIMITER $$
CREATE TRIGGER after_interest_payment
AFTER INSERT ON `interest_payment`
FOR EACH ROW
BEGIN
	DECLARE acquired_loan_id INT UNSIGNED;

	SELECT
		`loan_id`
	INTO
		acquired_loan_id
	FROM
		`loan`
	INNER JOIN `interest`
		USING (`loan_id`)
	INNER JOIN `interest_payment`
		USING (`interest_id`)
	WHERE
		`interest_payment_id` = NEW.`interest_payment_id`;

	CALL check_loan_status(acquired_loan_id);
END $$
DELIMITER ;

-- [TRIGGER] after_penalty_payment
DELIMITER $$
CREATE TRIGGER after_penalty_payment
AFTER INSERT ON `penalty_payment`
FOR EACH ROW
BEGIN
	DECLARE acquired_loan_id INT UNSIGNED;

	SELECT
		`loan_id`
	INTO
		acquired_loan_id
	FROM
		`loan`
	INNER JOIN `penalty`
		USING (`loan_id`)
	INNER JOIN `penalty_payment`
		USING (`penalty_id`)
	WHERE
		`penalty_payment_id` = NEW.`penalty_payment_id`;

	CALL check_loan_status(acquired_loan_id);
END $$
DELIMITER ;

-- [TRIGGER] after_processing_fee_payment
DELIMITER $$
CREATE TRIGGER after_processing_fee_payment
AFTER INSERT ON `processing_fee_payment`
FOR EACH ROW
BEGIN
	DECLARE acquired_loan_id INT UNSIGNED;

	SELECT
		`loan_id`
	INTO
		acquired_loan_id
	FROM
		`loan`
	INNER JOIN `processing_fee`
		USING (`loan_id`)
	INNER JOIN `processing_fee_payment`
		USING (`processing_fee_id`)
	WHERE
		`processing_fee_payment_id` = NEW.`processing_fee_payment_id`;

	CALL check_loan_status(acquired_loan_id);
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
	(DEFAULT, 23, 9, '2021-02-10 10:00:00', 5000, 'Closed', '1.jpg', NULL),
	(DEFAULT, 24, 10, '2021-06-21 11:00:00', 25000, 'Closed', '2.jpg', '2.pdf');

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