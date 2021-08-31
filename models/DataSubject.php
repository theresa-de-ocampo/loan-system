<?php

class DataSubject {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getName($id) {
		$this->db->query("SELECT CONCAT(`fname`, ' ', LEFT(`mname`, 1), '. ', `lname`) AS name FROM `data_subject` WHERE `data_subject_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultColumn();
	}

	public function getDataSubject($id) {
		$this->db->query("SELECT * FROM `data_subject` WHERE `data_subject_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function insertDataSubject($data) {
		$this->db->query("INSERT INTO `data_subject` (`fname`, `mname`, `lname`, `contact_no`, `bday`, `phase_block_lot`) VALUES (?, ?, ?, ?, ?, ?)");
		$i = 1;
		for ($i = 1; $i <= 6; $i++)
			$this->db->bind($i, $field);
		$this->db->executeWithoutCatch();
	}
}