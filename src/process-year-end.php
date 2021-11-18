<?php
if (isset($_POST)) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	require_once "../models/Roi.php";
	require_once "../models/Salary.php";
	require_once "../models/Fund.php";
	session_start();

	$closing_data = array(
		"interest" => $_POST["interest"],
		"processing-fee" => $_POST["processing-fee"],
		"penalty" => $_POST["penalty"]
	);

	$guarantorIds = unserialize($_POST["g-ids"]);
	$guarantorTotals = unserialize($_POST["g-amount"]);
	$earnings = $_POST["salary"];
	$funds = $_POST["funds"];
	
	$payroll = new Payroll();
	$roi = new Roi();
	$salary = new Salary();
	$fund = new Fund();

	$payroll->addClosing($closing_data);
	$roi->addRoi($guarantorIds, $guarantorTotals);
	$salary->addSalary($earnings);
	$fund->addFund($funds);
	header("Location: ../payroll.php");
}