<?php
class Guarantor {
	private $db;
	private $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	public function getTotalCurrentGuarantors() {
		$this->db->query("SELECT COUNT(`data_subject_id`) FROM `current_guarantors`");
		return $this->db->resultColumn();
	}

	public function getCurrentGuarantors() {
		$this->db->query(
			"SELECT * FROM `data_subject` INNER JOIN `guarantor_cycle_map` ON `data_subject_id` = `guarantor_id` WHERE `cycle_id` = $this->cycle"
		);
		return $this->db->resultSet();
	}

	public function getSavings() {
		$this->db->query(
			"SELECT `guarantor_id`, CONCAT(fname, ' ', LEFT(mname, 1), '. ', lname) AS `member`, `number_of_share`, `number_of_share` * `membership_fee` AS `principal` FROM `data_subject` INNER JOIN `guarantor_cycle_map` gcm ON `data_subject_id` = `guarantor_id` AND `cycle_id` = $this->cycle INNER JOIN `cycle` c ON c.`cycle_id` = gcm.`cycle_id`"
		);
		return $this->db->resultSet();
	}

	public function getTotalSavings() {
		$this->db->query("SELECT SUM(principal) FROM savings");
		return $this->db->resultColumn();
	}

	public function getNotCurrentGuarantors() {
		$this->db->query("SELECT * FROM `data_subject` WHERE `data_subject_id` NOT IN (SELECT `data_subject_id` FROM `data_subject` INNER JOIN `guarantor_cycle_map` ON `data_subject_id` = `guarantor_id` WHERE `cycle_id` = $this->cycle)");
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