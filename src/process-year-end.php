<?php
if (isset($_POST)) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	session_start();

	$closing_data = array(
		"interest" => $_POST["interest"],
		"processing-fee" => $_POST["processing-fee"],
		"penalty" => $_POST["penalty"]
	);

	$guarantorIds = unserialize($_POST["g-ids"]);
	$guarantorTotals = unserialize($_POST["g-amount"]);
	
	$payroll = new Payroll();
	$payroll->addClosing($closing_data);
	$payroll->addRoi($guarantorIds, $guarantorTotals);
	header("Location: ../payroll.php");
}