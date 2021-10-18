-- [FUNCTION] get_total_principal_paid
DELIMITER $$
CREATE FUNCTION total_principal_paid (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL(10, 2)
NOT DETERMINISTIC
BEGIN
	DECLARE lv_total_payment DECIMAL(10, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		lv_total_payment
	FROM
		`principal_payment`
	WHERE
		`loan_id` = p_loan_id;

	RETURN (lv_total_payment);
END $$
DELIMITER ;

-- [FUNCTION] total_interest_paid
DELIMITER $$
CREATE FUNCTION total_interest_paid (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL(9, 2)
NOT DETERMINISTIC
BEGIN
	DECLARE lv_total_payment DECIMAL (9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		lv_total_payment
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

	RETURN (lv_total_payment);
END $$
DELIMITER ;

-- [FUNCTION] total_penalty_paid
DELIMITER $$
CREATE FUNCTION total_penalty_paid (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL(9, 2)
NOT DETERMINISTIC
BEGIN
	DECLARE lv_total_payment DECIMAL(9, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		lv_total_payment
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

	RETURN (lv_total_payment);
END $$
DELIMITER ;

-- [FUNCTION] total_processing_fee_paid
DELIMITER $$
CREATE FUNCTION total_processing_fee_paid (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL (8, 2)
NOT DETERMINISTIC
BEGIN
	DECLARE lv_total_payment DECIMAL(8, 2);

	SELECT
		COALESCE(SUM(`amount`), 0)
	INTO
		lv_total_payment
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

	RETURN (lv_total_payment);
END $$
DELIMITER ;

-- [FUNCTION] total_amount_lent
CREATE FUNCTION total_amount_lent (
	p_guarantor_id INT UNSIGNED,
	p_cycle_id YEAR
)
RETURNS DECIMAL (30, 2)
NOT DETERMINISTIC
	RETURN (
		SELECT
			COALESCE(SUM(`principal`), 0)
		FROM
			`loan`
		WHERE
			`guarantor_id` = p_guarantor_id AND
			`cycle_id` = p_cycle_id
	);

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
CREATE PROCEDURE get_processing_fee (
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
	DECLARE amount_to_be_paid DECIMAL(10, 2);

	SELECT
		`principal`
	INTO
		amount_to_be_paid
	FROM
		`loan`
	WHERE 
		`loan_id` = p_loan_id;

	SET p_balance = amount_to_be_paid - total_principal_paid(p_loan_id);
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

	SET p_total_receivables = total_interest - total_interest_paid(p_loan_id);
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

	SET p_total_receivables = total_penalties - total_penalty_paid(p_loan_id);
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

	SET p_total_receivables = total_processing_fees - total_processing_fee_paid(p_loan_id);
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

-- [FUNCTION] total_receivables_by_loan
DELIMITER $$
CREATE FUNCTION total_receivables_by_loan (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL(50, 2)
NOT DETERMINISTIC
BEGIN
	CALL get_principal_balance(p_loan_id, @principal_balance);
	CALL get_interest_receivables(p_loan_id, @interest_receivables);
	CALL get_penalty_receivables(p_loan_id, @penalty_receivables);
	CALL get_processing_fee_receivables(p_loan_id, @processing_fee_receivables);

	RETURN (@principal_balance + @interest_receivables + @penalty_receivables + @processing_fee_receivables);
END $$
DELIMITER ;

-- [FUNCTION] total_payments_by_loan
CREATE FUNCTION total_payments_by_loan (
	p_loan_id INT UNSIGNED
)
RETURNS DECIMAL(50, 2)
NOT DETERMINISTIC
	RETURN (
		total_principal_paid(p_loan_id) + total_interest_paid(p_loan_id) + 
		total_penalty_paid (p_loan_id) + total_processing_fee_paid(p_loan_id)
	);

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