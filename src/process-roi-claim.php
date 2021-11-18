<?php
if (isset($_POST["accept"])) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Payroll.php";
	require_once "../models/Roi.php";
	session_start();
	
	$roi = new Roi();
	$roi->processRoiClaim($_POST["id"], $_FILES);
}