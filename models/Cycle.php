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

	public function addCycle($admins) {
		$new_cycle = $this->getCycleId() + 1;
		$this->db->query("INSERT INTO `cycle` (`cycle_id`) VALUES (?)");
		$this->db->bind(1, $new_cycle);
		$this->db->executeWithoutCatch();

		$administrator = new Administrator();
		foreach ($admins as $position => $admin) {
			if ($position === "asst-treasurer")
				$position = "Asst. Treasurer";
			else
				$position = ucfirst($position);

			$administrator->addAdmin($position, $new_cycle, $admin);
		}
	}
}