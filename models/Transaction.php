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

	public function getLoanDetails($id) {
		$this->db->query("SELECT * FROM `loan_detail` WHERE `loan_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultSet();
	}
}