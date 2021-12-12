<?php
class Transaction {
	protected $db;
	protected $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	# Should not be moved to Loan.php because this is also used in getting the data for processing fee receipt
	public function getPrincipalBalanceByDateTime($id, $date_time) {
		$this->db->query("CALL get_principal_balance_by_date_time(?, ?, @balance)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $date_time);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	# Should not be moved to Interest.php because this is also used in getting the data for penalty receipt
	public function getInterestBalanceByDate($id, $penalty_date) {
		$this->db->query("CALL get_interest_balance_by_date(?, ?, @balance)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $penalty_date);
		$this->db->execute();

		$this->db->query("SELECT @balance");
		return $this->db->resultColumn();
	}

	public function getTotalPaymentsToday() {
		$types_of_payment = ["principal_payment", "interest_payment", "penalty_payment", "processing_fee_payment"];
		$total = 0;
		foreach ($types_of_payment as $table) {
			$this->db->query("
				SELECT
					COALESCE(SUM(`amount`), 0)
				FROM
					`$table`
				WHERE
					DATE(`date_time_paid`) = CURDATE()
			");
			$total += $this->db->resultColumn();
		}
		return $total;
	}

	# Used for receipts (will soon include name for treasurer)
	protected function getEntities($borrower_id, $guarantor_id) {
		$data_subject = new DataSubject();
		$bname = $data_subject->getName($borrower_id);
		$gname = $data_subject->getName($guarantor_id);
		$borrower = $bname->fname." ".$bname->mname[0].". ".$bname->lname;
		$guarantor = $gname->fname." ".$gname->mname[0].". ".$gname->lname;
		return array("borrower" => $borrower, "guarantor" => $guarantor);
	}
}