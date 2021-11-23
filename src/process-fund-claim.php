<?php
if (isset($_POST["accept"])) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	require_once "../models/Fund.php";
	session_start();
	
	$fund = new Fund();
	$fund->processFundClaim($_POST["claimer"], $_POST["purpose"], $_FILES);
}