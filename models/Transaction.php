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

	protected function getEntities($borrower_id, $guarantor_id) {
		$data_subject = new DataSubject();
		$borrower = $data_subject->getName($borrower_id);
		$guarantor = $data_subject->getName($guarantor_id);
		return array("borrower" => $borrower, "guarantor" => $guarantor);
	}
}