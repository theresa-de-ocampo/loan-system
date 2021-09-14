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

	public function getPrincipalBalanceByDateTime($id, $date_time) {
		$this->db->query("CALL get_principal_balance_by_date_time(?, ?, @balance)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $date_time);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

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

	public function getPenaltyBalance($penalty_id) {
		$this->db->query("CALL get_penalty_balance(?, @balance)");
		$this->db->bind(1, $penalty_id);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getPenaltyPayments($id) {
		$this->db->query("SELECT `penalty_date`, `penalty_payment`.`amount`, `date_time_paid` FROM `penalty_payment` INNER JOIN `penalty` USING (`penalty_id`) WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

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
		$this->db->query("SELECT `processing_fee_date`, `processing_fee_payment`.`amount`, `date_time_paid` FROM `processing_fee_payment` INNER JOIN `processing_fee` USING (`processing_fee_id`) WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function insertPrincipalPayment($data) {
		try {
			$this->db->startTransaction();
			$loan_id = $data["loan-id"];
			$balance = $data["balance"];
			$amount = $data["amount"];

			$this->db->query("INSERT INTO `principal_payment` (`amount`, `loan_id`) VALUES (?, ?)");
			$this->db->bind(1, $amount);
			$this->db->bind(2, $loan_id);
			$this->db->executeWithoutCatch();
			$principal_payment_id = $this->db->lastInsertId();

			if ($amount >= $balance) {
				$this->db->query("UPDATE `loan` SET `status` = 'Closed' WHERE `loan_id` = ?");
				$this->db->bind(1, $loan_id);
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
			$this->db->confirmQueryWithReceipt("../receipts/principal-payment.php?loan-id=$loan_id&balance=$balance&payment-id=$principal_payment_id");
		}
	}

	public function getPrincipalReceiptData($loan_id, $principal_payment_id) {
		$this->db->query("SELECT * FROM `loan` WHERE `loan_id` = ?");
		$this->db->bind(1, $loan_id);
		$loan = $this->db->resultRecord();

		$this->db->query("SELECT * FROM `principal_payment` WHERE `principal_payment_id` = ?");
		$this->db->bind(1, $principal_payment_id);
		$principal_payment = $this->db->resultRecord();

		$entities = $this->getEntities($loan->borrower_id, $loan->guarantor_id);
		
		return array(
			"borrower" => $entities["borrower"],
			"guarantor" => $entities["guarantor"],
			"loan_date_time" => $loan->loan_date_time,
			"principal" => $loan->principal,
			"principal_balance" => $this->getPrincipalBalanceByDateTime($loan->loan_id, $principal_payment->date_time_paid),
			"amount_paid" => $principal_payment->amount,
			"date_time_paid" => $principal_payment->date_time_paid
		);
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
			"principal_balance" => $this->getPrincipalBalance($loan->loan_id),
			"interest_date" => $interest->interest_date,
			"interest_amount" => $interest->amount,
			"interest_balance" => $this->getInterestBalance($loan->loan_id, $interest->interest_id),
			"amount_paid" => $interest_payment->amount,
			"date_time_paid" => $interest_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
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

	private function getInterestBalanceByDate($id, $penalty_date) {
		$this->db->query("CALL get_interest_balance_by_date(?, ?, @balance)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $penalty_date);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
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
			"principal_balance" => $this->getPrincipalBalance($loan->loan_id),
			"processing_fee_date" => $processing_fee->processing_fee_date,
			"processing_fee_amount" => $processing_fee->amount,
			"amount_paid" => $processing_fee_payment->amount,
			"date_time_paid" => $processing_fee_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
	}

	private function getEntities($borrower_id, $guarantor_id) {
		$data_subject = new DataSubject();
		$borrower = $data_subject->getName($borrower_id);
		$guarantor = $data_subject->getName($guarantor_id);
		return array("borrower" => $borrower, "guarantor" => $guarantor);
	}
}