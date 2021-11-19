<?php
	if (isset($_GET["guarantor-id"]))
		$guarantor_id = $_GET["guarantor-id"];
	else {
		echo "<script>alert('Sorry, something went wrong!');</script>";
		echo "<script>window.location.replace('../payroll.php');</script>";
	}
	session_start();
	
	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../lib/conversion-util.php";
	require_once "../models/DataSubject.php";
	require_once "../models/Cycle.php";
	require_once "../models/Transaction.php";
	require_once "../models/ProcessingFee.php";
	require_once "../models/Payroll.php";
	require_once "../models/Salary.php";
	require_once "../inc/get-cashier.php";

	$salary = new Salary();
	$data = $salary->getSalaryClaimReceiptData($guarantor_id);
	$custom_id = $data["custom_id"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="../css/extra-short-print-size.css" media="print" />
	<link rel="stylesheet" type="text/css" href="../css/print.css" media="print" />
	<link rel="shortcut icon" type="image/x-icon" href="../img/favicon.png" />
	<title><?php echo $custom_id; ?></title>
</head>
<body>
	<header>
		<div class="fas fa-handshake"></div>
		<h1><?php echo COOPERATIVE; ?></h1>
		<address>
			<?php echo BARANGAY."<br />".TOWN.", ".PROVINCE; ?>
			<br />
			<?php echo LANDLINE." &#183; ".CP_NUMBER; ?>
		</address>
	</header>
	<hr />
	<h2>Salary Claim</h2>
	<table>
		<tr>
			<th><?php echo $position; ?></th>
			<td><?php echo $cashier; ?></td>
		</tr>
		<tr>
			<th>Total Processing Fee</th>
			<td>&#8369; <?php echo number_format($data["total_processing_fee"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Earnings</th>
			<td>&#8369; <?php echo number_format($data["earnings"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Claimer Position</th>
			<td><?php echo $data["claimer_position"]; ?></td>
		</tr>
		<tr>
			<th>Claimer Name</th>
			<td><?php echo $data["claimer_name"]; ?></td>
		</tr>
		<tr>
			<th>Date & Time Claimed</th>
			<td><?php echo $data["date_time_claimed"]; ?></td>
		</tr>
	</table>

	<footer>
		<div id="barcode"><?php echo $custom_id; ?></div>
		&lowast;&lowast;&lowast; Thank you! &lowast;&lowast;&lowast;
	</footer>

	<script>
		window.print();
		window.onafterprint = function(event) {
			window.location.href = "../payroll.php";
		};
	</script>
</body>
</html>