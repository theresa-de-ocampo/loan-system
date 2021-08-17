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
}