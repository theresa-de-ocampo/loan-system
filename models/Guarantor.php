<?php
class Guarantor {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getGuarantors() {
		$this->db->query("SELECT * FROM `data_subject` INNER JOIN `guarantor` USING (`data_subject_id`)");
		return $this->db->resultSet();
	}
}