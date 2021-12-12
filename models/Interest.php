<?php
class Interest extends Transaction {
	public function getInterests($id) {
		$this->db->query("SELECT * FROM `interest` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getInterestBalance($interest_id) {
		$this->db->query("CALL get_interest_balance(?, @balance)");
		$this->db->bind(1, $interest_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getAccruedInterest($id) {
		$this->db->query("CALL get_accrued_interest(?, @accrued_interest)");
		$this->db->bind(1, $id);
		$this->db->execute();

		$this->db->query("SELECT @accrued_interest");
		return $this->db->resultColumn();
	}

	public function getInterestPayments($id) {
		$this->db->query("
			SELECT
				`interest_date`,
				`interest_payment`.`amount`,
				`date_time_paid`
			FROM
				`interest_payment`
			INNER JOIN `interest`
				USING (`interest_id`)
			WHERE
				`loan_id` = ?
		");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function insertInterestPayment($data) {
		try {
			$this->db->startTransaction();
			$loan_id = $data["loan-id"];
			$interest_id = $data["interest-id"];
			$balance = $data["balance"];
			$amount = $data["amount"];
			
			$this->db->query("INSERT INTO `interest_payment` (`amount`, `interest_id`) VALUES (?, ?)");
			$this->db->bind(1, $amount);
			$this->db->bind(2, $interest_id);
			$this->db->executeWithoutCatch();
			$interest_payment_id = $this->db->lastInsertId();

			$this->db->query("SELECT `interest_date` FROM `interest` WHERE `interest_id` = ?");
			$this->db->bind(1, $interest_id);
			$interest_date = $this->db->resultColumn();

			if ($amount >= $balance) {
				if (date("Y-m-d") > $interest_date)
					$status = "Late";
				else
					$status = "Paid";
				$this->db->query("UPDATE `interest` SET `status` = '$status' WHERE `interest_id` = ?");
				$this->db->bind(1, $interest_id);
				$this->db->executeWithoutCatch();
			}
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		finally {
			$this->db->confirmQueryWithReceipt("../receipts/interest-payment.php?interest-id=$interest_id&balance=$balance&payment-id=$interest_payment_id");
		}
	}

	public function getInterestReceiptData($interest_id, $interest_payment_id) {
		$this->db->query("SELECT * FROM `interest` WHERE `interest_id` = ?");
		$this->db->bind(1, $interest_id);
		$interest = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `loan` WHERE `loan_id` = ?");
		$this->db->bind(1, $interest->loan_id);
		$loan = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `interest_payment` WHERE `interest_payment_id` = ?");
		$this->db->bind(1, $interest_payment_id);
		$interest_payment = $this->db->resultRecord();

		$entities = $this->getEntities($loan->borrower_id, $loan->guarantor_id);

		return array(
			"borrower" => $entities["borrower"],
			"guarantor" => $entities["guarantor"],
			"loan_date_time" => $loan->loan_date_time,
			"principal" => $loan->principal,
			"principal_balance" => $this->getPrincipalBalanceByDateTime($loan->loan_id, $interest->interest_date),
			"interest_date" => $interest->interest_date,
			"interest_amount" => $interest->amount,
			"interest_balance" => $this->getInterestBalance($loan->loan_id, $interest->interest_id),
			"amount_paid" => $interest_payment->amount,
			"date_time_paid" => $interest_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
	}

	public function getTotalInterestCollected() {
		$this->db->query("
			SELECT
				COALESCE(SUM(`interest_payment`.`amount`), 0)
			FROM
				`interest_payment`
			INNER JOIN `interest`
				USING (`interest_id`)
			INNER JOIN `loan`
				USING (`loan_id`)
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}
}