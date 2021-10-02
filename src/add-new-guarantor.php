<?php
if (isset($_POST["add"])) {
	unset($_POST["add"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Guarantor.php";
	$guarantor = new Guarantor();
	$guarantor->addNewGuarantor($_POST);
}