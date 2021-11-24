<?php

class DataSubject {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getDataSubjects() {
		$this->db->query("SELECT * FROM data_subject");
		return $this->db->resultSet();
	}

	public function getName($id) {
		$this->db->query("
			SELECT
				`fname`,
				`mname`,
				`lname`
			FROM
				`data_subject`
			WHERE
				`data_subject_id` = ?
		");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function getDataSubject($id) {
		$this->db->query("SELECT * FROM `data_subject` WHERE `data_subject_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function addDataSubject($data) {
		$this->db->query("
			INSERT INTO
				`data_subject` (`fname`, `mname`, `lname`, `contact_no`, `bday`, `phase_block_lot`)
			VALUES
				(?, ?, ?, ?, ?, ?)
		");
		$i = 1;
		foreach($data as $field)
			$this->db->bind($i++, $field);
		$this->db->executeWithoutCatch();
	}

	public function editDataSubject($data) {
		$this->db->query("
			UPDATE
				`data_subject`
			SET
				`fname` = ?, `mname` = ?, `lname` = ?, `contact_no` = ?, `bday` = ?, `phase_block_lot` = ?
			WHERE
				`data_subject_id` = ?
		");
		$i = 1;
		foreach ($data as $field)
			$this->db->bind($i++, $field);
		$this->db->execute("Data subject was updated successfully!", "../members.php#data-subjects");
	}
}