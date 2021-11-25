<?php
if (isset($_POST["create"])) {
	unset($_POST["add"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/User.php";
	$user = new User();
	$user->addUser($_POST);
}