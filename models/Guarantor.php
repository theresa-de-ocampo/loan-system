<?php
class Guarantor {
	private $db;
	public $cycle;

	public function __construct() {
		$this->db = new Database();
		$cycle = new Cycle();
		$this->cycle = $cycle->getCycleId();
	}

	public function getTotalCurrentGuarantors() {
		$this->db->query("
			SELECT
				COUNT(`data_subject_id`)
			FROM
				`data_subject`
			INNER JOIN `guarantor_cycle_map` 
				ON `data_subject_id` = `guarantor_id` 
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}

	public function getCurrentGuarantors() {
		$this->db->query("
			SELECT
				*
			FROM
				`data_subject`
			INNER JOIN `guarantor_cycle_map`
				ON `data_subject_id` = `guarantor_id`
			WHERE
				`cycle_id` = $this->cycle
		");
		return $this->db->resultSet();
	}

	public function getSavings() {
		$this->db->query("
			SELECT
				`guarantor_id`,
				`fname`,
				`mname`,
				`lname`,
				`number_of_share`,
				`number_of_share` * `membership_fee` AS `principal`
			FROM
				`data_subject`
			INNER JOIN `guarantor_cycle_map` gcm
				ON `data_subject_id` = `guarantor_id`
			INNER JOIN `cycle` c 
				ON c.`cycle_id` = gcm.`cycle_id`
			WHERE
				gcm.`cycle_id` = $this->cycle
			");
		return $this->db->resultSet();
	}

	public function getTotalSavings() {
		$this->db->query("
			SELECT
				COALESCE(SUM(`number_of_share` * `membership_fee`), 0)
			FROM
				`guarantor_cycle_map`
			INNER JOIN `cycle`
				USING (`cycle_id`)
			WHERE
				`cycle_id` = $this->cycle"
		);
		return $this->db->resultColumn();
	}

	public function getNotCurrentGuarantors() {
		$this->db->query("
			SELECT
				*
			FROM
				`data_subject`
			WHERE
				`data_subject_id` NOT IN (
					SELECT 
						`data_subject_id`
					FROM
						`data_subject`
					INNER JOIN `guarantor_cycle_map`
						ON `data_subject_id` = `guarantor_id`
					WHERE `cycle_id` = $this->cycle
				)
		");
		return $this->db->resultSet();
	}

	public function addNewGuarantor($data) {
		require_once "../models/DataSubject.php";
		try {
			$this->db->startTransaction();
			$data_subject = new DataSubject();
			$data_subject->addDataSubject(
				[$data["fname"], $data["mname"], $data["lname"], $data["contact-no"], $data["bday"], $data["address"]]
			);
			$id = $this->db->lastInsertId();

			$this->insertGuarantor($id);
			$this->insertGurantorCycleMap($id, $data["number-of-share"]);
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		finally {
			$this->db->confirmQuery("Guarantor was successfully added!", "../members.php");
		}
	}

	public function addGuarantor($data) {
		try {
			$id = $data["data-subject-id"];
			$this->db->query("SELECT `guarantor_id` FROM `guarantor` WHERE `guarantor_id` = $id");
			$exists = $this->db->resultColumn();

			// If selected person was never a guarantor (e.g. borrowers who were never a guarantor)
			if (!$exists)
				$this->insertGuarantor($id);

			$this->db->startTransaction();
			$this->insertGurantorCycleMap($id, $data["number-of-share"]);
			$this->db->commit();
		}
		catch (PDOException $e) {
			$this->db->rollBack();
			$error = $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine();
			$this->db->logError($error);
		}
		finally {
			$this->db->confirmQuery("Guarantor was successfully added!", "../members.php");
		}
	}

	public function getAppropriations() {
		/*You also place `cycle_id` = $this->cycle on a WHERE clause instead.*/
		$this->db->query("
			SELECT
				`guarantor_id`,
				`fname`,
				`mname`,
				`lname`,
				`principal`,
				`principal` - total_amount_lent(derived_table.`guarantor_id`, $this->cycle) AS `outstanding`
			FROM (
				SELECT
					`guarantor_id`,
					`fname`,
					`mname`,
					`lname`,
					`number_of_share` * `membership_fee` AS `principal`
				FROM
					`data_subject` 
				INNER JOIN `guarantor_cycle_map` gcm 
					ON `data_subject_id` = `guarantor_id`
					AND `cycle_id` = $this->cycle
				INNER JOIN `cycle` c 
					ON c.`cycle_id` = gcm.`cycle_id`
				) derived_table;
		");
		return $this->db->resultSet();
	}

	public function getOutstanding($id) {
		/*You could also place the gurantor_id on the join condition.*/
		$this->db->query("
			SELECT
				(`number_of_share` * `membership_fee`) - total_amount_lent($id, $this->cycle) AS `outstanding`
			FROM
				`guarantor_cycle_map`
			INNER JOIN `cycle`
				USING (`cycle_id`)
			WHERE
				`guarantor_id` = $id AND
				`guarantor_cycle_map`.`cycle_id` = $this->cycle
		");
		return $this->db->resultColumn();
	}

	public function getTotalAmountLent($id, $year = "") {
		if ($year == "")
			$year = $this->cycle;
		$this->db->query("SELECT total_amount_lent($id, $year)");
		return $this->db->resultColumn();
	}

	public function getTotalInterestCollected($id, $year = "") {
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
						`guarantor_id` = $id AND
						`cycle_id` = $year
				)
		");
		return $this->db->resultColumn();
	}

	public function getNumberOfShares($id, $year = "") {
		if ($year == "")
			$year = $this->cycle;

		$this->db->query("
			SELECT `number_of_share` 
			FROM `guarantor_cycle_map` 
			WHERE `cycle_id` = $year AND `guarantor_id` = $id
		");
		return $this->db->resultColumn();
	}

	public function getPrincipal($id, $year = "") {
		if ($year == "")
			$year = $this->cycle;

		$this->db->query("
			SELECT
				`number_of_share` * `membership_fee`
			FROM
				`guarantor_cycle_map`
			INNER JOIN `cycle`
				USING (`cycle_id`)
			WHERE
				`cycle_id` = $year AND
				`guarantor_id` = $id
		");
		return $this->db->resultColumn();
	}

	public function getTotalPrincipalReturned($id, $year = "") {
		if ($year == "")
			$year = $this->cycle;

		$this->db->query("
			SELECT
				COALESCE(SUM(`principal_payment`.`amount`), 0)
			FROM
				`principal_payment`
			INNER JOIN `loan`
				USING (`loan_id`)
			WHERE
				`guarantor_id` = $id AND
				`cycle_id` = $year
		");
		$principal_collected = $this->db->resultColumn();
		$total_amount_lent = $this->getTotalAmountLent($id, $year);
		$investment = $this->getPrincipal($id, $year);
		
		if ($total_amount_lent == $principal_collected)
			$principal_returned = $investment;
		else {
			$uncollected = $total_amount_lent - $principal_collected;
			$principal_returned = $investment - $uncollected;
		}

		return $principal_returned;
	}

	public function getTotalUncollectedPayments($id) {
		$this->db->query("
			SELECT `loan_id`
			FROM `loan`
			WHERE `cycle_id` = $this->cycle AND `guarantor_id` = $id AND `status` = 'Active'
		");
		$active_loans = $this->db->resultSetOneColumn();

		$total_uncollected_payments = 0;
		$loan = new Loan();
		foreach ($active_loans as $loan_id)
			$total_uncollected_payments += $loan->getTotalReceivablesByLoan($loan_id);
		return $total_uncollected_payments;
	}

	private function insertGuarantor($id) {
		$this->db->query("INSERT INTO `guarantor` VALUES (?)");
		$this->db->bind(1, $id);
		$this->db->executeWithoutCatch();
	}

	private function insertGurantorCycleMap($id, $numberOfShare) {
		$this->db->query("INSERT INTO `guarantor_cycle_map` (`guarantor_id`, `number_of_share`) VALUES (?, ?)");
		$this->db->bind(1, $id);
		$this->db->bind(2, $numberOfShare);
		$this->db->executeWithoutCatch();
	}
}