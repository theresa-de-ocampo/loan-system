<?php
if (isset($_POST["edit"])) {
	unset($_POST["edit"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/User.php";
	$user = new User();
	$user->editUser($_POST, $_FILES);
}