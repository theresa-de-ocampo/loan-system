<?php
if (isset($_POST["submit"])) {
	unset($_POST["submit"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Guarantor.php";
	$guarantor = new Guarantor();
	$guarantor->addGuarantor($_POST);
}