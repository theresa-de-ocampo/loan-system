<?php
if (isset($_POST["edit"])) {
	unset($_POST["edit"]);
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../models/DataSubject.php";
	$data_subject = new DataSubject();
	$data_subject->editDataSubject($_POST);
}