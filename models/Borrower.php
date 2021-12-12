<?php
class Borrower {
	private $db;
	public $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	public function getTotalCurrentBorrowers() {
		$this->db->query("
			SELECT
				COUNT(`data_subject_id`)
			FROM
				`data_subject`
			INNER JOIN `loan` 
				ON `data_subject_id` = `borrower_id` 
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}
}