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
		foreach($data as $field)
			$this->db->bind($i, $field);
		$this->db->executeWithoutCatch();
	}

	public function updateDataSubject($data) {
		$this->db->query("UPDATE `data_subject` SET `fname` = ?, `mname` = ?, `lname` = ?, `contact_no` = ?, `bday` = ?, `phase_block_lot` = ? WHERE `data_subject_id` = ?");
		$i = 1;
		foreach ($data as $field)
			$this->db->bind($i++, $field);
		$this->db->execute("Data subject was updated successfully!", "../members.php");
	}
}