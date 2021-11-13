<?php
class User {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function confirmUser($email) {
		$this->db->query("SELECT * FROM `user` WHERE `email` = ?");
		$this->db->bind(1, $email);
		return $this->db->resultRecord();
	}

	public function getUser($id) {
		$this->db->query("SELECT * FROM `user` WHERE `user_id` = ?");
		$this->db->bind(1, $id);
		return $this->db->resultRecord();
	}

	public function hasAccount($id) {
		$user = $this->getUser($id);
		if ($user)
			return $user->email;
		else
			return 0;
	}

	public function addUser($user) {
		$this->db->query("INSERT INTO `user` VALUES (?, ?, ?, ?, DEFAULT)");
		$this->db->bind(1, $user["user_id"]);
		$this->db->bind(2, $user["email"]);
		$this->db->bind(3, password_hash($user["password"], PASSWORD_DEFAULT));
		$this->db->bind(4, $user["username"]);
		$this->db->executeWithoutCatch();
	}
}