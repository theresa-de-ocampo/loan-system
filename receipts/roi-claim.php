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
	require_once "../models/Guarantor.php";
	require_once "../models/Payroll.php";
	require_once "../models/Roi.php";
	require_once "../inc/get-cashier.php";

	$roi = new Roi();
	$data = $roi->getRoiClaimReceiptData($guarantor_id);
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
	<link rel="stylesheet" type="text/css" href="../css/roi-print-size.css" media="print" />
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
	<h2>ROI Claim</h2>
	<table>
		<tr>
			<th><?php echo $position; ?></th>
			<td><?php echo $cashier; ?></td>
		</tr>
		<tr>
			<th>Claimer</th>
			<td><?php echo $data["claimer"]; ?></td>
		</tr>
		<tr>
			<th>Per Share</th>
			<td>&#8369; <?php echo $data["per_share"]; ?></td>
		</tr>
		<tr>
			<th>No. of Share</th>
			<td><?php echo $data["number_of_share"]; ?></td>
		</tr>
		<tr>
			<th>Interest Collected</th>
			<td>&#8369; <?php echo number_format($data["total_interest_collected"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>10% Return</th>
			<td>&#8369; <?php echo number_format($data["ten_percent_return"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Cut <span>(No. of Share * Per Share)</span></th>
			<td>&#8369; <?php echo number_format($data["cut"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Total <span>(10% Return + Cut)</span></th>
			<td>&#8369; <?php echo number_format($data["total"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Principal Returned</th>
			<td>&#8369; <?php echo number_format($data["principal_returned"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Grand Total <span>(Principal Returned + Total)</span></th>
			<td>&#8369; <?php echo number_format($data["grand_total"], 2, ".", ","); ?></td>
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
			window.location.href = "../payroll.php#shares";
		};
	</script>
</body>
</html>