<?php
class Administrator extends User {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function confirmAdmin($email, $cycle_id) {
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
		$this->db->bind(1, $cycle_id);
		$this->db->bind(2, $email);
		return $this->db->resultRecord();
	}

	public function getAdmin($id) {
		$this->db->query("SELECT * FROM `administrator` INNER JOIN `user` USING (`user_id`) WHERE `user_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function addAdmin($position, $cycle_id, $admin) {
		if ($admin["data-subject-id"] === "") { // If not an existing data subject
			$data_subject = new DataSubject();
			$data = [$admin["fname"], $admin["mname"], $admin["lname"], $admin["contact-no"], $admin["bday"], $admin["address"]];
			$data_subject->addDataSubject($data);
			$user_id = $this->db->lastInsertId();
		}
		else
			$user_id = $admin["data-subject-id"];

		if ($admin["email"] !== "") { // If admin does not have an account yet
			$user = new User();
			$new_user = ["user_id" => $user_id, "email" => $admin["email"], "password" => $admin["password"], "username" => $admin["username"]];
			$user->addUser($new_user);
		}

		$this->db->query("INSERT INTO `administrator` VALUES (?, ?, ?)");
		$this->db->bind(1, $position);
		$this->db->bind(2, $cycle_id);
		$this->db->bind(3, $user_id);
		$this->db->executeWithoutCatch();
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