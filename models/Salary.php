<?php
class Salary extends Payroll {
	public function getEmployees() {
		$this->db->query("
			SELECT
				*
			FROM
				`administrator`
			INNER JOIN `data_subject`
				ON `administrator`.`user_id` = `data_subject_id`
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultSet();
	}

	public function addSalary($earnings) {
		$this->db->query("SELECT `user_id` FROM `administrator` WHERE `cycle_id` = $this->cycle");
		$employee_ids = $this->db->resultSetOneColumn();

		foreach ($employee_ids as $id) {
			$this->db->query("INSERT INTO `salary` (`amount`, `guarantor_id`, `closing_id`) VALUES (?, ?, ?)");
			$this->db->bind(1, $earnings);
			$this->db->bind(2, $id);
			$this->db->bind(3, $this->cycle);
			$this->db->execute();
		}
	}

	public function getSalary($id) {
		$this->db->query("SELECT * FROM `salary` WHERE `guarantor_id` = $id AND `closing_id` = $this->cycle");
		return $this->db->resultRecord();
	}

	public function processSalaryClaim($id, $files) {
		require_once "../lib/upload-file.php";
		$upload_file = new UploadFile();
		$file_error = $files["proof"]["error"];
		$file_tmp_name = $files["proof"]["tmp_name"];
		if ($file_error == UPLOAD_ERR_OK) {
			if ($upload_file->isImage($file_tmp_name)) {
				$path = $files["proof"]["name"];
				$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
				$target_dir = "../img/payroll/".$this->cycle."/salary/";
				if (!file_exists($target_dir))
					mkdir($target_dir, 0777, true);
				$file_name = $id.".".$extension;
				$file_dest = $target_dir.$file_name;
				$date_time_claimed = date("Y-m-d H:i:s");

				$this->db->query("
					UPDATE
						`salary`
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
				$this->db->execute("Year-end share was successfully claimed!", "../receipts/salary-claim.php?guarantor-id=$id");
				move_uploaded_file($file_tmp_name, $file_dest);
			}
		}
	}

	public function getSalaryClaimReceiptData($id) {
		$processing_fee = new ProcessingFee();
		$total_processing_fee = $processing_fee->getTotalProcessingFeeCollected();

		$this->db->query("SELECT `position` FROM `administrator` WHERE `user_id` = $id AND `cycle_id` = $this->cycle");
		$claimer_position = $this->db->resultColumn();

		$data_subject = new DataSubject();
		$name = $data_subject->getName($id);
		$claimer_name = $name->fname." ".$name->mname[0].". ".$name->lname;

		$converter = new Converter();
		$this->db->query("SELECT `date_time_claimed` FROM `salary` WHERE `guarantor_id` = $id AND `closing_id` = $this->cycle");
		$date_time_claimed = $converter->shortToLongDateTime($this->db->resultColumn());

		return array(
			"total_processing_fee" => $total_processing_fee,
			"earnings" => $converter->roundDown($total_processing_fee / 3),
			"claimer_position" => $claimer_position,
			"claimer_name" => $claimer_name,
			"date_time_claimed" => $date_time_claimed,
			"custom_id" => "A".$id." C".$this->cycle
		);
	}
}