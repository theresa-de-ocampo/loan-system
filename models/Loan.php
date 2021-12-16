<?php
class Loan extends Transaction {
	public function getLoans() {
		$this->db->query("SELECT * FROM `loan`  WHERE `cycle_id` = $this->cycle");
		return $this->db->resultSet();
	}

	public function getTotalReceivablesByLoan($id) {
		$this->db->query("SELECT total_receivables_by_loan($id)");
		return $this->db->resultColumn();
	}

	public function getTotalPaymentsByLoan($id) {
		$this->db->query("CALL get_total_payments_by_loan($id, @total_payments)");
		$this->db->execute();

		$this->db->query("SELECT @total_payments");
		return $this->db->resultColumn();
	}

	public function getAllPrincipalPayments() {
		$this->db->query("
			SELECT
				`borrower_id`,
				`guarantor_id`,
				`principal_payment`.`amount`,
				`date_time_paid`
			FROM
				`principal_payment`
			INNER JOIN `loan`
				USING (`loan_id`)
			WHERE
				`loan`.`cycle_id` = $this->cycle
		");
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
			UPDATE newly inserted loan record to include the documents.
			Add advance interest, and processing fee.
			Add files to project repository. 
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
					$file_name = $loan_id.".".$extension;
					$file_dest = $target_dir.$file_name;

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
			$error = $e->getMessage();
			$this->db->logError($error);
			echo "<script>alert('$error');</script>";
			echo "<script>window.location.replace('../transactions.php');</script>";
		}
		finally {
			$this->db->confirmQuery("Loan disbursement was successfully recorded!", "../transactions.php");
		}
	}

	public function getLoanSummaryByGuarantor($id) {
		$this->db->query("
			SELECT
				`fname`,
				`mname`,
				`lname`,
				`loan_date_time`,
				`status`,
				total_payments_by_loan(`loan_id`) AS `paid`,
				total_receivables_by_loan(`loan_id`) AS `unpaid`
			FROM
				`loan`
			INNER JOIN `data_subject`
				ON `borrower_id` = `data_subject_id`
			WHERE
				`cycle_id` = $this->cycle AND
				`guarantor_id` = $id
		");
		return $this->db->resultSet();
	}

	public function getLoansByBorrower($id) {
		$this->db->query("SELECT * FROM `loan`  WHERE `borrower_id` = $id");
		return $this->db->resultSet();
	}

	public function getTotalLoansToday() {
		$this->db->query("
			SELECT
				COALESCE(COUNT(`loan_id`), 0)
			FROM
				`loan`
			WHERE
				DATE(`loan_date_time`) = CURDATE()
		");
		return $this->db->resultColumn();
	}

	public function getTotalUncollectedLoans() {
		$total = 0;
		$active_loans = $this->getActiveLoans();
		foreach ($active_loans as $loan_id) {
			$this->db->query("CALL get_principal_balance($loan_id, @balance)");
			$this->db->bind(1, $loan_id);
			$this->db->execute();

			$this->db->query("SELECT @balance");
			$total += $this->db->resultColumn();
		}
		return $total;
	}

	private function getActiveLoans() {
		$this->db->query("SELECT `loan_id` FROM `loan` WHERE `cycle_id` = $this->cycle AND `status` = 'Active'");
		return $this->db->resultSetOneColumn();
	}
}