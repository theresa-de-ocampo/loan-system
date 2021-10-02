<?php
if (isset($_POST["submit"])) {
	if (isset($_POST["cycle-id"])) {
		session_start();
		$year = $_SESSION["cycle"] = $_POST["cycle-id"];
		echo "<script>alert('You are now at cycle $year!');</script>";
		echo "<script>window.location.replace('../cycle.php');</script>";
	}
}