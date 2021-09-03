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

	}

	public function getInterests($id) {
		$this->db->query("SELECT * FROM `interest` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}

	public function getInterestPayments($id) {
		$this->db->query("SELECT * FROM `interest_payment` INNER JOIN `interest` USING (`interest_id`) WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}
}