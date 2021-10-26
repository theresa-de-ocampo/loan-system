<?php

class User {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function confirmAdmin($email) {
		$query = "
			SELECT
				`user_id`,
				`password`
			FROM
				`administrator`
			INNER JOIN `user`
				USING (`user_id`)
			WHERE
				`cycle_id` = ? AND
				`email` = ?
		";
		$this->db->query($query);
		$this->db->bind(1, date("Y"));
		$this->db->bind(2, $email);
		$admin = $this->db->resultRecord();
		if ($admin)
			return $admin;
		else {
			if ($this->sysAdminExists())
				return $admin;
			else {
				$this->db->query($query);
				$this->db->bind(1, date("Y") - 1);
				$this->db->bind(2, $email);
				return $this->db->resultRecord();
			}
		}
	}

	public function getAdmin($id) {
		$this->db->query("SELECT * FROM `administrator` INNER JOIN `user` USING (`user_id`) WHERE `user_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	private function sysAdminExists() {
		$this->db->query("
			SELECT
				`user_id`
			FROM
				`administrator`
			INNER JOIN `user`
				USING (`user_id`)
			WHERE
				`cycle_id` = ? AND
				`position` IN ('Auditor', 'Treasurer')
		");
		$this->db->bind(1, date("Y"));
		return $this->db->resultRecord();
	}
}