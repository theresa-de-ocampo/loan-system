<?php

class Administrator {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getAdmin($email) {
		$this->db->query("SELECT * FROM administrator WHERE `email` = ?");
		$this->db->bind(1, $email);
		return $this->db->resultRecord();
	}
}