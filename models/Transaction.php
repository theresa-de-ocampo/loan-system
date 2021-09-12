<?php
class Transaction {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getLoans() {
		$this->db->query("SELECT * FROM `loan`");
		return $this->db->resultSet();
	}

	public function getLoan($id) {
		$this->db->query("SELECT * FROM `loan` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function getPrincipalPayments($id) {
		$this->db->query("SELECT * FROM `principal_payment` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getPrincipalBalance($id) {
		$this->db->query("CALL get_principal_balance(?, @balance)");
		$this->db->bind(1, $id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getInterests($id) {
		$this->db->query("SELECT * FROM `interest` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getInterestBalance($loan_id, $interest_id) {
		$this->db->query("CALL get_interest_balance(?, ?, @balance)");
		$this->db->bind(1, $loan_id);
		$this->db->bind(2, $interest_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getInterestPayments($id) {
		$this->db->query("SELECT `interest_date`, `interest_payment`.`amount`, `date_time_paid` FROM `interest_payment` INNER JOIN `interest` USING (`interest_id`) WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getPenalties($id) {
		$this->db->query("SELECT `penalty_id`, `penalty_date`, `interest_date`, `penalty`.`amount`, `penalty`.`status` FROM `penalty` INNER JOIN `interest` USING (`interest_id`) WHERE `penalty`.`loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getPenaltyBalance($loan_id, $penalty_id) {
		$this->db->query("CALL get_penalty_balance(?, ?, @balance)");
		$this->db->bind(1, $loan_id);
		$this->db->bind(2, $penalty_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getProcessingFees($id) {
		$this->db->query("SELECT * FROM `processing_fee` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getProcessingFeePayments($id) {
		$this->db->query("SELECT `processing_fee_date`, `processing_fee_payment`.`amount`, `date_time_paid` FROM `processing_fee_payment` INNER JOIN `processing_fee` USING (`processing_fee_id`) WHERE `loan_id` = ?");
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
			
			$this->db->query("INSERT INTO `interest_payment` (`amount`, `interest_id`) VALUES(?, ?)");
			$this->db->bind(1, $amount);
			$this->db->bind(2, $interest_id);
			$this->db->executeWithoutCatch();
			$interest_payment_id = $this->db->lastInsertId();

			$this->db->query("SELECT `interest_date` FROM `interest` WHERE `interest_id` = ?");
			$this->db->bind(1, $interest_id);
			$interest_date = strtotime($this->db->resultColumn());

			if ($amount >= $balance) {
				$now = time();
				if ($now > $interest_date)
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
			$this->db->confirmQueryWithReceipt("../receipts/interest-payment.php?interest-id=$interest_id&payment-id=$interest_payment_id");
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

		$data_subject = new DataSubject();
		$borrower = $data_subject->getName($loan->borrower_id);
		$guarantor = $data_subject->getName($loan->guarantor_id);

		return array(
			"borrower" => $borrower,
			"guarantor" => $guarantor,
			"loan_date_time" => $loan->loan_date_time,
			"principal" => $loan->principal,
			"principal_balance" => $this->getPrincipalBalance($loan->loan_id),
			"interest_date" => $interest->interest_date,
			"interest_amount" => $interest->amount,
			"interest_balance" => $this->getInterestBalance($loan->loan_id, $interest->interest_id),
			"amount_paid" => $interest_payment->amount,
			"date_time_paid" => $interest_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
	}
}