<?php
class ProcessingFee extends Transaction {
	public function getProcessingFees($id) {
		$this->db->query("SELECT * FROM `processing_fee` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getProcessingFeeBalance($processing_fee_id) {
		$this->db->query("CALL get_processing_fee_balance(?, @balance)");
		$this->db->bind(1, $processing_fee_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getProcessingFeePayments($id) {
		$this->db->query("
			SELECT
				`processing_fee_date`,
				`processing_fee_payment`.`amount`,
				`date_time_paid`
			FROM
				`processing_fee_payment`
			INNER JOIN `processing_fee`
				USING (`processing_fee_id`)
			WHERE
				`loan_id` = ?
		");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function insertProcessingFeePayment($data) {
		try {
			$this->db->startTransaction();
			$loan_id = $data["loan-id"];
			$processing_fee_id = $data["processing-fee-id"];
			$balance = $data["balance"];
			$amount = $data["amount"];

			$this->db->query("INSERT INTO `processing_fee_payment` (`amount`, `processing_fee_id`) VALUES (?, ?)");
			$this->db->bind(1, $amount);
			$this->db->bind(2, $processing_fee_id);
			$this->db->executeWithoutCatch();
			$processing_fee_payment_id = $this->db->lastInsertId();

			if ($amount >= $balance) {
				$this->db->query("UPDATE `processing_fee` SET `status` = 'Paid' WHERE `processing_fee_id` = ?");
				$this->db->bind(1, $processing_fee_id);
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
			$this->db->confirmQueryWithReceipt("../receipts/processing-fee-payment.php?processing-fee-id=$processing_fee_id&balance=$balance&payment-id=$processing_fee_payment_id");
		}
	}

	public function getProcessingFeeReceiptData($processing_fee_id, $processing_fee_payment_id) {
		$this->db->query("SELECT * FROM `processing_fee` WHERE `processing_fee_id` = ?");
		$this->db->bind(1, $processing_fee_id);
		$processing_fee = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `loan` WHERE `loan_id` = ?");
		$this->db->bind(1, $processing_fee->loan_id);
		$loan = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `processing_fee_payment` WHERE `processing_fee_payment_id` = ?");
		$this->db->bind(1, $processing_fee_payment_id);
		$processing_fee_payment = $this->db->resultRecord();

		$entities = $this->getEntities($loan->borrower_id, $loan->guarantor_id);

		return array(
			"borrower" => $entities["borrower"],
			"guarantor" => $entities["guarantor"],
			"loan_date_time" => $loan->loan_date_time,
			"principal" => $loan->principal,
			"principal_balance" => $this->getPrincipalBalanceByDateTime($loan->loan_id, $processing_fee->processing_fee_date),
			"processing_fee_date" => $processing_fee->processing_fee_date,
			"processing_fee_amount" => $processing_fee->amount,
			"amount_paid" => $processing_fee_payment->amount,
			"date_time_paid" => $processing_fee_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
	}

	public function getTotalProcessingFeeCollected() {
		$this->db->query("
			SELECT
				COALESCE(SUM(`processing_fee_payment`.`amount`), 0)
			FROM
				`processing_fee_payment`
			INNER JOIN `processing_fee`
				USING (`processing_fee_id`)
			INNER JOIN `loan`
				USING (`loan_id`)
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}
}