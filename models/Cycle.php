<?php
class Cycle {
	private $db;
	private $cycleId;

	public function __construct() {
		$this->db = new Database();
		$this->setCycleId();
		$this->db->query("SET @session_cycle_id = $this->cycleId");
		$this->db->execute();
	}

	public function setCycleId() {
		if (isset($_SESSION["cycle"])) {
			$session_cycle = $_SESSION["cycle"];
			$periods = $this->getPeriods();
			if (in_array($session_cycle, $periods))
				$this->cycleId = $session_cycle;
			else
				$this->cycleId = $this->getLatestPeriod();
		}
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