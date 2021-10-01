<?php
class Guarantor {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getTotalCurrentGuarantors() {
		$this->db->query("SELECT COUNT(`data_subject_id`) FROM `current_guarantors`");
		return $this->db->resultColumn();
	}

	public function getCurrentGuarantors() {
		$this->db->query("SELECT * FROM current_guarantors");
		return $this->db->resultSet();
	}

	public function getSavings() {
		$this->db->query("SELECT * FROM savings");
		return $this->db->resultSet();
	}

	public function getTotalSavings() {
		$this->db->query("SELECT SUM(principal) FROM savings");
		return $this->db->resultColumn();
	}

	public function getNotCurrentGuarantors() {
		$this->db->query("SELECT * FROM not_current_guarantors");
		return $this->db->resultSet();
	}

	public function addNewGuarantor($data) {
		require_once "../models/DataSubject.php";
		try {
			$this->db->startTransaction();
			$data_subject = new DataSubject();
			$data_subject->addDataSubject([$data["fname"], $data["mname"], $data["lname"], $data["contact-no"], $data["bday"], $data["address"]]);
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