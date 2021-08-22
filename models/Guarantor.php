<?php
class Guarantor {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getGuarantors() {
		$this->db->query("SELECT * FROM `data_subject` INNER JOIN `guarantor` WHERE data_subject_id = guarantor_id");
		return $this->db->resultSet();
	}

	public function getSavings() {
		$this->db->query("SELECT * FROM `savings`");
		return $this->db->resultSet();
	}
}