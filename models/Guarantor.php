<?php
class Guarantor {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getCurrentGuarantors() {
		$this->db->query("SELECT * FROM `current_guarantors`");
		return $this->db->resultSet();
	}

	public function getSavings() {
		$this->db->query("SELECT * FROM `savings`");
		return $this->db->resultSet();
	}

	public function getNotCurrentGuarantors() {
		$this->db->query("SELECT * FROM `not_current_guarantors`");
		return $this->db->resultSet();
	}

	public function addNewGuarantor($data) {
		try {
			$this->db->startTransaction();
			$this->db->query("INSERT INTO `data_subject` (`fname`, `mname`, `lname`, `contact_no`, `bday`, `phase_block_lot`) VALUES (?, ?, ?, ?, ?, ?)");
			$this->db->bind(1, $data["fname"]);
			$this->db->bind(2, $data["mname"]);
			$this->db->bind(3, $data["lname"]);
			$this->db->bind(4, $data["contact-no"]);
			$this->db->bind(5, $data["bday"]);
			$this->db->bind(6, $data["address"]);
			$this->db->executeWithoutCatch();
			$id = $this->db->lastInsertId();

			$this->db->query("INSERT INTO `guarantor` VALUES (?)");
			$this->db->bind(1, $id);
			$this->db->executeWithoutCatch();

			$this->insertGurantorCycleMap($id, $data["number-of-share"]);
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		finally {
			$this->db->confirmQuery("Guarantor was successfully added!", "../members.php");
		}
	}

	public function addGuarantor($data) {
		try {
			$this->db->startTransaction();
			$this->insertGurantorCycleMap($data["data-subject-id"], $data["number-of-share"]);
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		finally {
			$this->db->confirmQuery("Guarantor was successfully added!", "../members.php");
		}
	}

	private function insertGurantorCycleMap($id, $numberOfShare) {
		$this->db->query("INSERT INTO `guarantor_cycle_map` (`guarantor_id`, `number_of_share`) VALUES (?, ?)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $numberOfShare);
		$this->db->executeWithoutCatch();
	}
}