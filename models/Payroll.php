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

	public function processRoiClaim($id, $files) {
		require_once "../lib/upload-file.php";
		$upload_file = new UploadFile();
		$file_error = $files["proof"]["error"];
		$file_tmp_name = $files["proof"]["tmp_name"];
		if ($file_error == UPLOAD_ERR_OK) {
			if ($upload_file->isImage($file_tmp_name)) {
				$path = $files["proof"]["name"];
				$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
				$target_dir = "../img/payroll/".$this->cycle."/roi/";
				if (!file_exists($target_dir))
					mkdir($target_dir, 0777, true);
				$file_name = $id.".".$extension;
				$file_dest = $target_dir.$file_name;
				$date_time_claimed = date("Y-m-d H:i:s");

				$this->db->query("
					UPDATE
						`roi`
					SET
						`status` = 'Claimed',
						`date_time_claimed` = ?,
						`proof` = ?
					WHERE
						`guarantor_id` = ? AND `closing_id` = ?
				");
				$this->db->bind(1, $date_time_claimed);
				$this->db->bind(2, $file_name);
				$this->db->bind(3, $id);
				$this->db->bind(4, $this->cycle);
				$this->db->execute("Year-end share was successfully claimed!", "../receipts/roi-claim.php?guarantor-id=$id");
				move_uploaded_file($file_tmp_name, $file_dest);
			}
		}
	}

	public function getRoiClaimReceiptData($id) {
		$converter = new Converter();
		$profits = $this->getProfits();
		$per_share = $converter->roundDown($profits["per_share"]);
		$rate = $profits["rate"];

		$guarantor = new Guarantor();
		$number_of_share = $guarantor->getNumberOfShares($id);
		$total_interest_collected = $guarantor->getTotalInterestCollected($id);
		$ten_percent_return = $total_interest_collected * $rate;
		$cut = $number_of_share * $per_share;
		$total = $ten_percent_return + $cut;
		$principal_returned = $guarantor->getTotalPrincipalReturned($id);
		$grand_total = $total + $principal_returned;

		$this->db->query("SELECT `date_time_claimed` FROM `roi` WHERE `guarantor_id` = ? AND `closing_id` = ?");
		$this->db->bind(1, $id);
		$this->db->bind(2, $this->cycle);
		$date_time_claimed = $converter->shortToLongDateTime($this->db->resultColumn());

		$custom_id = "G".$id." C".$this->cycle;
		return array(
			"per_share" => $per_share,
			"number_of_share" => $number_of_share,
			"total_interest_collected" => $total_interest_collected,
			"ten_percent_return" => $ten_percent_return,
			"cut" => $cut,
			"total" => $total,
			"principal_returned" => $principal_returned,
			"grand_total" => $grand_total,
			"date_time_claimed" => $date_time_claimed,
			"custom_id" => $custom_id
		);
	}
}