<?php
	if (isset($_GET["processing-fee-id"]) && isset($_GET["balance"]) && isset($_GET["payment-id"])) {
		$processing_fee_id = $_GET["processing-fee-id"];
		$balance = $_GET["balance"];
		$payment_id = $_GET["payment-id"];
	}
	else {
		echo "<script>alert('Sorry, something went wrong!');</script>";
		echo "<script>window.location.replace('../transactions.php');</script>";
	}
	session_start();

	require_once "../config/config.php";
	require_once "../lib/database-handler.php";
	require_once "../lib/conversion-util.php";
	require_once "../models/Cycle.php";
	require_once "../models/DataSubject.php";
	require_once "../models/Transaction.php";
	require_once "../models/ProcessingFee.php";
	require_once "../inc/get-cashier.php";

	$converter = new Converter();
	$processing_fee = new ProcessingFee();
	$data = $processing_fee->getProcessingFeeReceiptData($processing_fee_id, $payment_id);
	$custom_id = "L".$data["loan_id"]." PF".$processing_fee_id." PFP".$payment_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="../css/long-print-size.css" media="print" />
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
	<h2>Processing Payment</h2>
	<table>
		<tr>
			<th><?php echo $position; ?></th>
			<td><?php echo $cashier; ?></td>
		</tr>
		<tr>
			<th>Borrower</th>
			<td><?php echo $data["borrower"]; ?></td>
		</tr>
		<tr>
			<th>Guarantor</th>
			<td><?php echo $data["guarantor"]; ?></td>
		</tr>
		<tr>
			<th>Loan Date & Time</th>
			<td><?php echo $converter->shortToLongDateTime($data["loan_date_time"]); ?></td>
		</tr>
		<tr>
			<th>Principal</th>
			<td>&#8369; <?php echo number_format($data["principal"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Principal Balance</th>
			<td>&#8369; <?php echo number_format($data["principal_balance"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Processing Fee Date</th>
			<td><?php echo $converter->shortToLongDate($data["processing_fee_date"]); ?></td>
		</tr>
		<tr>
			<th>Processing Fee</th>
			<td>&#8369; <?php echo number_format($data["processing_fee_amount"], 2, ".", ",") ?></td>
		</tr>
		<tr>
			<th>Processing Fee Balance</th>
			<td>&#8369; <?php echo number_format($balance, 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Amount Paid</th>
			<td>&#8369; <?php echo number_format($data["amount_paid"], 2, ".", ","); ?></td>
		</tr>
		<tr>
			<th>Date & Time Paid</th>
			<td><?php echo $converter->shortToLongDateTime($data["date_time_paid"]); ?></td>
		</tr>
	</table>
	
	<footer>
		<div id="barcode"><?php echo $custom_id; ?></div>
		&lowast;&lowast;&lowast; Thank you! &lowast;&lowast;&lowast;
	</footer>

	<script>
		window.print();
		window.onafterprint = function(event) {
			window.location.href = "../transactions.php";
		};
	</script>
</body>
</html>