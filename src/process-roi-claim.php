<?php
if (isset($_POST["accept"])) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	session_start();
	
	$payroll = new Payroll();
	$payroll->processRoiClaim($_POST["id"], $_FILES);
}