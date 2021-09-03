<?php
session_start();
if (!isset($_SESSION["admin-verified"]))
	header("Location: index.php");
