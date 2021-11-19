<?php
require_once "../models/User.php";
require_once "../models/Administrator.php";
$administrator = new Administrator();
$data_subject = new DataSubject();
$admin_verified_id = $_SESSION["admin-verified"];
$admin = $administrator->getAdmin($admin_verified_id);
$name = $data_subject->getName($admin_verified_id);
$cashier = $name->fname." ".$name->mname[0].". ".$name->lname;
$position = $admin->position;