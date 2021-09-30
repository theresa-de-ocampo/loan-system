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
		$loan_id = $data["loan-id"];
		$balance = $data["balance"];
		$amount = $data["amount"];

		$this->db->query("INSERT INTO `principal_payment` (`amount`, `loan_id`) VALUES (?, ?)");
		$this->db->bind(1, $amount);
		$this->db->bind(2, $loan_id);
		$this->db->execute();
		$principal_payment_id = $this->db->lastInsertId();

		$this->db->confirmQueryWithReceipt("../receipts/principal-payment.php?loan-id=$loan_id&balance=$balance&payment-id=$principal_payment_id");
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
			"principal_balance" => $this->getPrincipalBalanceByDateTime($loan->loan_id, $processing_fee->processing_fee_date),
			"processing_fee_date" => $processing_fee->processing_fee_date,
			"processing_fee_amount" => $processing_fee->amount,
			"amount_paid" => $processing_fee_payment->amount,
			"date_time_paid" => $processing_fee_payment->date_time_paid,
			"loan_id" => $loan->loan_id
		);
	}

	public function getAccruedInterest($id) {
		$this->db->query("CALL get_accrued_interest(?, @accrued_interest)");
		$this->db->bind(1, $id);
		$this->db->execute();

		$this->db->query("SELECT @accrued_interest");
		return $this->db->resultColumn();
	}

	public function getTotalReceivablesByLoan($id) {
		$this->db->query("CALL get_total_receivables_by_loan(?, @total_receivables)");
		$this->db->bind(1, $id);
		$this->db->execute();

		$this->db->query("SELECT @total_receivables");
		return $this->db->resultColumn();
	}

	public function addNewLoan($data, $files) {
		try {
			$this->db->startTransaction();
			if (empty($data["data-subject-id"])) {
				require_once "../models/DataSubject.php";
				$dataSubject = new DataSubject();
				$dataSubject->addDataSubject([$data["fname"], $data["mname"], $data["lname"], $data["contact-no"], $data["bday"], $data["address"]]);
				$borrower_id = $this->db->lastInsertId();
			}
			else
				$borrower_id = $data["data-subject-id"];
			$this->db->query("INSERT INTO `loan` (borrower_id, guarantor_id, principal) VALUES (?, ?, ?)");
			$this->db->bind(1, $borrower_id);
			$this->db->bind(2, $data["guarantor-id"]);
			$this->db->bind(3, $data["principal"]);
			$this->db->executeWithoutCatch();
			$loan_id = $this->db->lastInsertId();

			/*
			Add files to project repository. 
			UPDATE newly inserted loan record to include the documents.
			Add advance interest, and processing fee.
			[NOTE] Sequencing of commands is important.
				1. UPDATE can't be done with the INSERT command earlier because the filename is based on the record ID.
				2. UPDATE is done before the file(s) was/were copied to the project repository. If it was the other way around, you'd have to delete the files from the respository in case the UPDATE command fails.
			*/
			require_once "../lib/upload-file.php";
			$principal = $_POST["principal"];
			$upload_file = new UploadFile();
			$proof_file_error = $files["proof"]["error"];
			$proof_file_tmp_name = $files["proof"]["tmp_name"];
			if ($proof_file_error == UPLOAD_ERR_OK)
				if ($upload_file->isImage($proof_file_tmp_name)) {
					$path = $files["proof"]["name"];
					$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
					$target_dir = "../img/transactions/loan/";
					$file_dest = $target_dir.$loan_id.".".$extension;
					$file_name = $loan_id.".".$extension;

					$this->db->query("UPDATE `loan` SET `proof` = '$file_name' WHERE `loan_id` = $loan_id");
					$this->db->executeWithoutCatch();

					$this->db->query("CALL get_interest_rate(@rate)");
					$this->db->execute();
					$this->db->query("SELECT @rate");
					$amount = $this->db->resultColumn() * $principal;
					$this->db->query("INSERT INTO `interest` (`amount`, `loan_id`) VALUES ($amount, $loan_id)");
					$this->db->executeWithoutCatch();

					$this->db->query("CALL get_processing_fee($principal, @fee)");
					$this->db->execute();
					$this->db->query("SELECT @fee");
					$amount = $this->db->resultColumn();
					$this->db->query("INSERT INTO `processing_fee` (`amount`, `loan_id`) VALUES ($amount, $loan_id)");
					$this->db->executeWithoutCatch();
					move_uploaded_file($proof_file_tmp_name, $file_dest);
				}
				else
					throw new Exception("Please upload image files only for proof of transaction.");
			else
				throw new Exception("[PROOF] ".$upload_file->codeToMessage($proof_file_error));

			if ($principal >= 10000) {
				$collateral_file_error = $files["collateral"]["error"];
				$collateral_file_tmp_name = $files["collateral"]["tmp_name"];
				if ($collateral_file_error == UPLOAD_ERR_OK)
					if ($upload_file->isImage($collateral_file_tmp_name) || $upload_file->isPDF($collateral_file_tmp_name)) {
						$path = $files["collateral"]["name"];
						$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
						$target_dir = "../img/transactions/collateral/";
						$file_dest = $target_dir.$loan_id.".".$extension;
						$file_name = $loan_id.".".$extension;
						$this->db->query("UPDATE `loan` SET `collateral` = '$file_name' WHERE `loan_id` = $loan_id");
						$this->db->executeWithoutCatch();
						move_uploaded_file($collateral_file_tmp_name, $file_dest);
					}
					else
						throw new Exception("Please upload image or PDF files only for the collateral.");
				else
					throw new Exception("[COLLATERAL] ".$upload_file->codeToMessage($proof_file_error));
			}
			
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		catch (Exception $e) {
			$this->db->rollBack();
			$message = $e->getMessage();
			echo "<script>alert('$message');</script>";
			echo "<script>window.location.replace('../transactions.php');</script>";
		}
		finally {
			$this->db->confirmQuery("Loan disbursement was successfully recorded!", "../transactions.php");
		}
	}

	private function getEntities($borrower_id, $guarantor_id) {
		$data_subject = new DataSubject();
		$borrower = $data_subject->getName($borrower_id);
		$guarantor = $data_subject->getName($guarantor_id);
		return array("borrower" => $borrower, "guarantor" => $guarantor);
	}
}