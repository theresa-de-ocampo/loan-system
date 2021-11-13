<?php
session_start();
if (!isset($_SESSION["generic-user-verified"]))
	header("Location: index.php");