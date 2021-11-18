<?php
if (isset($_POST["accept"])) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	require_once "../models/Salary.php";
	session_start();
	
	$salary = new Salary();
	$salary->processSalaryClaim($_POST["id"], $_FILES);
}