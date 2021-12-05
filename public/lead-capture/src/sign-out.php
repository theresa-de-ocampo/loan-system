<?php
session_start();
unset($_SESSION["generic-user-verified"]);
header("Location: ../index.php");