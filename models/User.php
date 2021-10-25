<?php

class User {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function confirmAdmin($email) {
		$this->db->query("
			SELECT
				`user_id`,
				`password`
			FROM
				`administrator`
			INNER JOIN `user`
				USING (`user_id`)
			WHERE
				`email` = ?
		");
		$this->db->bind(1, $email);
		return $this->db->resultRecord();
	}

	public function getAdmin($id) {
		$this->db->query("SELECT * FROM `administrator` INNER JOIN `user` USING (`user_id`) WHERE `user_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}
}