<?php
class Fund extends Payroll {
	public function addFund($funds) {
		$this->db->query("INSERT INTO `fund` (`closing_id`, `amount`) VALUES (?, ?)");
		$this->db->bind(1, $this->cycle);
		$this->db->bind(2, $funds);
		$this->db->executeWithoutCatch();
	}

	public function getFund() {
		$this->db->query("SELECT * FROM `fund` WHERE `closing_id` = $this->cycle");
		return $this->db->resultRecord();
	}

	public function processFundClaim($id, $purpose, $files) {
		require_once "../lib/upload-file.php";
		$upload_file = new UploadFile();
		$file_error = $files["proof"]["error"];
		$file_tmp_name = $files["proof"]["tmp_name"];
		if ($file_error == UPLOAD_ERR_OK) {
			if ($upload_file->isImage($file_tmp_name)) {
				$path = $files["proof"]["name"];
				$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
				$target_dir = "../img/payroll/".$this->cycle."/";
				if (!file_exists($target_dir))
					mkdir($target_dir, 0777, true);
				$file_name = "fund-".$id.".".$extension;
				$file_dest = $target_dir.$file_name;
				$date_time_claimed = date("Y-m-d H:i:s");

				$this->db->query("
					UPDATE
						`fund`
					SET
						`claimed_by` = ?,
						`date_time_claimed` = ?,
						`proof` = ?,
						`purpose` = ?
					WHERE
						`closing_id` = ?
				");
				$this->db->bind(1, $id);
				$this->db->bind(2, $date_time_claimed);
				$this->db->bind(3, $file_name);
				$this->db->bind(4, $purpose);
				$this->db->bind(5, $this->cycle);
				$this->db->execute("Year-end fund was successfully claimed!", "../receipts/fund-claim.php?guarantor-id=$id");
				move_uploaded_file($file_tmp_name, $file_dest);
			}
		}
	}

	public function getFundClaimReceiptData($id) {
		$penalty = new Penalty();
		$funds = $penalty->getTotalPenaltiesCollected(); /*Amount of funds for this cycle, not an array of `fund` records. */

		$this->db->query("SELECT `position` FROM `administrator` WHERE `user_id` = $id AND `cycle_id` = $this->cycle");
		$claimer_position = $this->db->resultColumn();

		$data_subject = new DataSubject();
		$name = $data_subject->getName($id);
		$claimer_name = $name->fname." ".$name->mname[0].". ".$name->lname;

		$converter = new Converter();
		$this->db->query("SELECT `date_time_claimed` FROM `fund` WHERE `closing_id` = $this->cycle");
		$date_time_claimed = $converter->shortToLongDateTime($this->db->resultColumn());

		return array(
			"funds" => $funds,
			"claimer_position" => $claimer_position,
			"claimer_name" => $claimer_name,
			"date_time_claimed" => $date_time_claimed,
			"custom_id" => "F".$id." C".$this->cycle
		);
	}

	public function getFundsByGuarantor($id) {
		$this->db->query("SELECT * FROM `fund` WHERE `claimed_by` = $id OR `claimed_by` IS NULL");
		return $this->db->resultSet();
	}
}