<?php
if (isset($_POST["submit"])) {
	unset($_POST["submit"]);
	unset($_POST["tab"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/Transaction.php";
	require_once "../models/Loan.php";

	$loan = new Loan();
	$loan->addNewLoan($_POST, $_FILES);
}