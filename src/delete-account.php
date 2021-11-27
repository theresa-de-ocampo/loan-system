<?php
if (isset($_GET["id"])) {
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/Cycle.php";
	require_once "../models/User.php";
	$id = $_GET["id"];
	$user = new User();
	$user->deleteUser($id);
}
else {
	echo "<script>alert('Sorry, something went wrong!');</script>";
	echo "<script>window.location.replace('../members.php#data-subjects');</script>";
}