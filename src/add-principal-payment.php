<?php
if (isset($_POST["add"])) {
	unset($_POST["add"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Transaction.php";
	require_once "../models/Loan.php";
	$loan = new Loan();
	$loan->insertPrincipalPayment($_POST);
}