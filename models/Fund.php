<?php
class Fund extends Payroll {
	public function addFund($funds) {
		$this->db->query("INSERT INTO `fund` (`closing_id`, `amount`) VALUES (?, ?)");
		$this->db->bind(1, $this->cycle);
		$this->db->bind(2, $funds);
		$this->db->execute();
	}

	public function getFund() {
		$this->db->query("SELECT * FROM `fund` WHERE `closing_id` = $this->cycle");
		return $this->db->resultRecord();
	}
}