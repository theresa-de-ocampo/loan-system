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
}