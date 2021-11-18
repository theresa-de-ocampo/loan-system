<?php
class Penalty extends Transaction {
	public function getPenalties($id) {
		$this->db->query("
			SELECT
				`penalty_id`,
				`penalty_date`,
				`interest_date`,
				`penalty`.`amount`,
				`penalty`.`status`
			FROM
				`penalty`
			INNER JOIN `interest`
				USING (`interest_id`)
			WHERE
				`penalty`.`loan_id` = ?
		");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getPenaltyBalance($penalty_id) {
		$this->db->query("CALL get_penalty_balance(?, @balance)");
		$this->db->bind(1, $penalty_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getPenaltyPayments($id) {
		$this->db->query("
			SELECT
				`penalty_date`,
				`penalty_payment`.`amount`,
				`date_time_paid`
			FROM
				`penalty_payment`
			INNER JOIN `penalty`
				USING (`penalty_id`)
			WHERE
				`loan_id` = ?
		");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function insertPenaltyPayment($data) {
		try {
			$this->db->startTransaction();
			$loan_id = $data["loan-id"];
			$penalty_id = $data["penalty-id"];
			$balance = $data["balance"];
			$amount = $data["amount"];

			$this->db->query("INSERT INTO `penalty_payment` (`amount`, `penalty_id`) VALUES (?, ?)");
			$this->db->bind(1, $amount);
			$this->db->bind(2, $penalty_id);
			$this->db->executeWithoutCatch();
			$penalty_payment_id = $this->db->lastInsertId();

			if ($amount >= $balance) {
				$this->db->query("UPDATE `penalty` SET `status` = 'Paid' WHERE `penalty_id` = ?");
				$this->db->bind(1, $penalty_id);
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
			$this->db->confirmQueryWithReceipt("../receipts/penalty-payment.php?penalty-id=$penalty_id&balance=$balance&payment-id=$penalty_payment_id");
		}
	}

	public function getPenaltyReceiptData($penalty_id, $penalty_payment_id) {
		$this->db->query("SELECT * FROM `penalty` WHERE `penalty_id` = ?");
		$this->db->bind(1, $penalty_id);
		$penalty = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `loan` WHERE `loan_id` = ?");
		$this->db->bind(1, $penalty->loan_id);
		$loan = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `penalty_payment` WHERE `penalty_payment_id` = ?");
		$this->db->bind(1, $penalty_payment_id);
		$penalty_payment = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `interest` WHERE `interest_id` = ?");
		$this->db->bind(1, $penalty->interest_id);
		$interest = $this->db->resultRecord();

		$entities = $this->getEntities($loan->borrower_id, $loan->guarantor_id);

		return array(
			"borrower" => $entities["borrower"],
			"guarantor" => $entities["guarantor"],
			"interest_date" => $interest->interest_date,
			"interest_balance" => $this->getInterestBalanceByDate($interest->interest_id, $penalty->penalty_date),
			"penalty_date" => $penalty->penalty_date,
			"penalty_amount" => $penalty->amount,
			"amount_paid" => $penalty_payment->amount,
			"date_time_paid" => $penalty_payment->date_time_paid,
			"interest_id" => $interest->interest_id
		);
	}

	public function getTotalPenaltiesCollected() {
		$this->db->query("
			SELECT
				COALESCE(SUM(`penalty_payment`.`amount`), 0)
			FROM
				`penalty_payment`
			INNER JOIN `penalty`
				USING (`penalty_id`)
			INNER JOIN `loan`
				USING (`loan_id`)
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}
}