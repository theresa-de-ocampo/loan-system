<?php

class DataSubject {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getDataSubject($id) {
		$this->db->query("SELECT * FROM `data_subject` WHERE `data_subject_id` = ?");
		$this->db->bind(1, $id);
	}

	public function insertDataSubject($data) {
		$this->db->query("INSERT INTO `data_subject` (`fname`, `mname`, `lname`, `contact_no`, `bday`, `phase_block_lot`) VALUES (?, ?, ?, ?, ?, ?)");
		$i = 1;
		for ($i = 1; $i <= 6; $i++)
			$this->db->bind($i, $field);
		$this->db->executeWithoutCatch();
	}
}