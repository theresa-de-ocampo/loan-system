<?php
if (isset($_POST["submit"])) {
	session_start();
	require_once "../../../config/config.php";
	require_once "../../../lib/database-handler.php";
	require_once "../../../models/User.php";
	require_once "../../../models/Cycle.php";

	$email = $_POST["email"];
	$password = $_POST["password"];

	$cycle = new Cycle();
	$cycle_id = $cycle->getLatestPeriod();
	$user = new user();
	$account = $user->confirmUser($email);
	if ($account) {
		if (password_verify($password, $account->password)) {
			$_SESSION["generic-user-verified"] = $account->user_id;
			$_SESSION["cycle"] = $cycle_id;
			$path = "../loans.php";
		}
		else {
			$message = "The password you entered is incorrect!";
			$path = "../login.php";
		}
	}
	else {
		$message = "You do not have permission to access this website.";
		$path = "../login.php";
	}

	if (isset($message))
		echo "<script>alert('$message');</script>";
	echo "<script>window.location.replace('$path');</script>";
}