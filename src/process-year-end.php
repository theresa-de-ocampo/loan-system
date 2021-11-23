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
	);

	$guarantorIds = unserialize($_POST["g-ids"]);
	$guarantorTotals = unserialize($_POST["g-amount"]);
	$earnings = $_POST["salary"];
	$funds = $_POST["funds"];
	
	$payroll = new Payroll();
	$roi = new Roi();
	$salary = new Salary();
	$fund = new Fund();

	$payroll->processYearEnd($closing_data, $guarantorIds, $guarantorTotals, $earnings, $funds);
}