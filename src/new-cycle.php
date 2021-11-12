<?php
if (isset($_POST["submit"])) {
	unset($_POST["submit"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/User.php";
	require_once "../models/Administrator.php";
	require_once "../models/DataSubject.php";
	session_start();

	$cycle = new Cycle();
	$cycle->addCycle($_POST);
}