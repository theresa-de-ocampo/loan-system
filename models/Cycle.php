<?php
class Cycle {
	private $db;

	public function __construct() {
		$this->db = new Database();
	}

	public function getCycles() {
		$this->db->query("SELECT * FROM `cycle`");
		return $this->db->resultSet();
	}

	public function getCycleId() {
		if (isset($_SESSION["cycle"])) {
			$session_cycle = $_SESSION["cycle"];
			$periods = $this->getPeriods();
			if (in_array($session_cycle, $periods))
				return $session_cycle;
			else
				return $this->getLatestPeriod();
		}
	}

	public function getPeriods() {
		$this->db->query("SELECT `cycle_id` FROM `cycle`");
		return $this->db->resultSetOneColumn();
	}

	public function getLatestPeriod() {
		$this->db->query("SELECT MAX(`cycle_id`) FROM `cycle`");
		return $this->db->resultColumn();
	}
}