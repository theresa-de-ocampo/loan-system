<?php
class Roi extends Payroll {
	public function addRoi($ids, $totals) {
		$i = 0;

		foreach ($ids as $id) {
			$this->db->query("INSERT INTO `roi` (`amount`, `guarantor_id`, `closing_id`) VALUES (?, ?, ?)");
			$this->db->bind(1, $totals[$i++]);
			$this->db->bind(2, $id);
			$this->db->bind(3, $this->cycle);
			$this->db->execute();
		}
	}

	public function getRoi($id) {
		$this->db->query("SELECT * FROM `roi` WHERE `guarantor_id` = $id AND `closing_id` = $this->cycle");
		return $this->db->resultRecord();
	}
}