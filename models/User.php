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

	public function addUser($user, $new_cycle = false) {
		$this->db->query("INSERT INTO `user` VALUES (?, ?, ?, ?, DEFAULT)");
		$this->db->bind(1, $user["id"]);
		$this->db->bind(2, $user["email"]);
		$this->db->bind(3, password_hash($user["password"], PASSWORD_DEFAULT));
		$this->db->bind(4, $user["username"]);

		if ($new_cycle)
			$this->db->executeWithoutCatch();
		else
			$this->db->execute("New account was successfully created!", "../members#data-subjects");
	}

	public function deleteUser($id) {
		$this->db->query("DELETE FROM `user` WHERE `user_id` = $id");
		$this->db->execute("Account was successfully deleted!", "../members#data-subjects");
	}

	public function editUser($data, $files) {
		require_once "../lib/upload-file.php";
		$upload_file = new UploadFile();
		$profile_picture_file_error = $files["profile-picture"]["error"];
		$id = $data["id"];

		try {
			if ($profile_picture_file_error == UPLOAD_ERR_NO_FILE) {
				$this->db->query("UPDATE `user` SET `email` = ?, `password` = ?, `username` = ? WHERE `user_id` = ?");
				$this->db->bind(4, $id);
			}
			else {
				$profile_picture_file_tmp_name = $files["profile-picture"]["tmp_name"];
				if ($profile_picture_file_error == UPLOAD_ERR_OK)
					if ($upload_file->isImage($profile_picture_file_tmp_name)) {
						$path = $files["profile-picture"]["name"];
						$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
						$target_dir = "../img/profile-pictures/";
						$file_name = $id.".".$extension;
						$file_dest = $target_dir.$file_name;

						$this->db->query("
							UPDATE `user`
							SET `email` = ?, `password` = ?, `username` = ?, `profile_picture` = ?
							WHERE `user_id` = ?
						");
						$this->db->bind(4, $file_name);
						$this->db->bind(5, $id);
						move_uploaded_file($profile_picture_file_tmp_name, $file_dest);
					}
					else
						throw new Exception("Please upload image files only for proof of transaction.");
				else
					throw new Exception("[PROFILE PICTURE] ".$upload_file->codeToMessage($profile_picture_file_error));
			}

			$this->db->bind(1, $data["email"]);
			$this->db->bind(2, password_hash($data["password"], PASSWORD_DEFAULT));
			$this->db->bind(3, $data["username"]);
			$this->db->execute("Changes were successfully saved!", "../user-settings.php");
		}
		catch(PDOException $e) {
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		catch (Exception $e) {
			$error = $e->getMessage();
			$this->db->logError($error);
			echo "<script>alert('$error');</script>";
			echo "<script>window.location.replace('../user-settings.php');</script>";
		}
	}
}