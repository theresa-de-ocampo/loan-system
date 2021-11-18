<?php
class Payroll {
	protected $db;
	protected $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	public function getProfits() {
		$this->db->query("
			SELECT
				COALESCE(SUM(`amount`), 0)
			FROM
				`interest_payment`
			WHERE
				`interest_id` IN (
					SELECT
						`interest_id`
					FROM
						`interest`
					INNER JOIN `loan`
						USING (`loan_id`)
					WHERE
						`cycle_id` = $this->cycle
				)
		");
		$interest = $this->db->resultColumn();

		$this->db->query("SELECT `interest_rate` FROM `cycle` WHERE `cycle_id` = $this->cycle");
		$rate = $this->db->resultColumn();

		$this->db->query("SELECT COALESCE(SUM(`number_of_share`), 0) FROM `guarantor_cycle_map` WHERE `cycle_id` = $this->cycle");
		$total_number_of_shares = $this->db->resultColumn();

		$ten_percent_return = $interest * $rate;
		$net_income = $interest - $ten_percent_return;
		$per_share = $net_income / $total_number_of_shares;

		return array(
			"interest" => $interest,
			"ten_percent_return" => $ten_percent_return,
			"net_income" => $net_income,
			"per_share" => $per_share,
			"rate" => $rate
		);
	}

	public function getProcessedFlag() {
		$this->db->query("SELECT `closing_id` FROM `closing` WHERE `closing_id` = $this->cycle");
		return $this->db->resultColumn();
	}

	public function addClosing($data) {
		$this->db->query("INSERT INTO `closing` (`interest`, `processing_fee`, `penalty`) VALUES (?, ?, ?)");
		$this->db->bind(1, $data["interest"]);
		$this->db->bind(2, $data["processing-fee"]);
		$this->db->bind(3, $data["penalty"]);
		$this->db->execute();
	}
}