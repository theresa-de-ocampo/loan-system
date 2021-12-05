<?php
session_start();
unset($_SESSION["admin-verified"]);
header("Location: ../index.php");