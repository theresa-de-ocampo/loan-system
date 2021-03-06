<?php
class Payroll {
	protected $db;
	protected $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	public function getProfits($year = "") {
		if ($year == "")
			$year = $this->cycle;

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
						`cycle_id` = $year
				)
		");
		$interest = $this->db->resultColumn();

		$this->db->query("SELECT `interest_rate` FROM `cycle` WHERE `cycle_id` = $year");
		$rate = $this->db->resultColumn();

		$this->db->query("SELECT COALESCE(SUM(`number_of_share`), 0) FROM `guarantor_cycle_map` WHERE `cycle_id` = $year");
		$total_number_of_shares = $this->db->resultColumn();

		$ten_percent_return = $interest * $rate;
		$net_income = $interest - $ten_percent_return;

		if ($total_number_of_shares != 0)
			$per_share = $net_income / $total_number_of_shares;
		else
			$per_share = 0;

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
		$this->db->query("INSERT INTO `closing` (`interest`, `processing_fee`) VALUES (?, ?)");
		$this->db->bind(1, $data["interest"]);
		$this->db->bind(2, $data["processing-fee"]);
		$this->db->executeWithoutCatch();

		/* Use this if you want to insert dummy data for a certain year.
		$this->db->query("INSERT INTO `closing` VALUES (?, ?, ?, ?)");
		$this->db->bind(1, '2020');
		$this->db->bind(2, '2020-11-30');
		$this->db->bind(3, $data["interest"]);
		$this->db->bind(4, $data["processing-fee"]);
		$this->db->executeWithoutCatch();
		*/
	}

	public function processYearEnd($closing_data, $guarantorIds, $guarantorTotals, $earnings, $funds) {
		try {
			$this->db->startTransaction();
			$roi = new Roi();
			$salary = new Salary();
			$fund = new Fund();

			$this->addClosing($closing_data);
			$roi->addRoi($guarantorIds, $guarantorTotals);
			$salary->addSalary($earnings);
			$fund->addFund($funds);
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
			echo "<script>alert('An unexpected error occurred with the year-end processing');</script>";
		}
		finally {
			header("Location: ../payroll.php");
		}
	}
}