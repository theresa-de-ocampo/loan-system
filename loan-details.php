<?php
	if (isset($_GET["id"]))
		$id = $_GET["id"];
	else {
		echo "<script>alert('Sorry, something went wrong!');</script>";
		echo "<script>window.location.replace('transactions.php');</script>";
	}

	require_once "config/config.php";
	require_once "lib/database-handler.php";
	require_once "lib/conversion-util.php";
	require_once "models/Transaction.php";
	require_once "models/DataSubject.php";

	$converter = new Converter();
	$transaction = new Transaction();
	$data_subject = new DataSubject();
	$loan = $transaction->getLoan($id);
	$loan_details = $transaction->getLoanDetails($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="author" content="Theresa De Ocampo" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/all.min.css" />
	<link rel="stylesheet" type="text/css" href="css/datatables.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="stylesheet" type="text/css" href="css/loan-details.css" />
	<link rel="stylesheet" type="text/css" href="css/media-queries.css" />
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png" />
	<title><?php echo COOPERATIVE; ?></title>
</head>
<body>
	<?php require_once "inc/vertical-nav-bar.html"; ?>
	<main>
		<header>
			<i class="fas fa-bars"></i>
			<h2 class="text">Transactions</h2>
		</header>

		<section id="summary">
			<h3>Summary</h3>
			<div class="grid-wrapper">
				<table>
					<tr>
						<th>Loan ID:</th>
						<td><?php echo $id; ?></td>
					</tr>
					<tr>
						<th>Borrower:</th>
						<td><?php echo $data_subject->getName($loan->borrower_id); ?></td>
					</tr>
					<tr>
						<th>Guarantor:</th>
						<td><?php echo $data_subject->getName($loan->guarantor_id); ?></td>
					</tr>
					<tr>
						<th>Loan Date:</th>
						<td><?php echo $converter->shortToLongDate($loan->loan_date_time); ?></td>
					</tr>
				</table>
				<div id="accrued-interest">
					<h4>Accrued Interest</h4>
					<p><span>&#8369;</span> 30,000</p>
				</div><!-- #accrued-interest -->
				<div id="total-receivables">
					<h4>Total Receivables</h4>
					<p><span>&#8369;</span> 2,500</p>
				</div>
			</div><!-- .grid-wrapper -->
		</section><!-- #summary -->

		<section id="loan-details">
			<h3>Loan Details</h3>
			<hr />
			<table id="loan-details-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th rowspan="2">ID</th>
						<th rowspan="2">DATE</th>
						<th colspan="2">PRINCIPAL</th>
						<th colspan="4">INTEREST</th>
						<th colspan="5">PENALTY</th>
						<th colspan="4">PROCESSING FEE</th>
					</tr>
					<tr>
						<th>Balance</th>
						<th>Payment</th>
						<th>Amount</th>
						<th>Status</th>
						<th>Date Paid</th>
						<th>Balance</th>
						<th>Amount</th>
						<th>From Interest Date</th>
						<th>Status</th>
						<th>Date Paid</th>
						<th>Balance</th>
						<th>Amount</th>
						<th>Status</th>
						<th>Date Paid</th>
						<th>Balance</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($loan_details as $ld): ?>
					<tr>
						<td><?php echo $ld->loan_detail_id; ?></td>
						<td><?php echo $converter->shortToLongDate($ld->transaction_date); ?></td>
						<td><?php echo $ld->principal_balance; ?></td>
						<td><?php echo $ld->principal_payment; ?></td>
						<td><?php echo $ld->interest_amount; ?></td>
						<td><?php echo $ld->interest_status; ?></td>
						<td><?php echo $converter->shortToLongDate($ld->interest_date_time_paid); ?></td>
						<td><?php echo $ld->interest_balance; ?></td>
						<td><?php echo $ld->penalty_amount; ?></td>
						<td><?php echo $converter->shortToLongDate($ld->penalty_from_interest_date); ?></td>
						<td><?php echo $ld->penalty_status; ?></td>
						<td><?php echo $converter->shortToLongDate($ld->penalty_date_time_paid); ?></td>
						<td><?php echo $ld->penalty_balance; ?></td>
						<td><?php echo $ld->processing_fee_amount; ?></td>
						<td><?php echo $ld->processing_fee_status; ?></td>
						<td><?php echo $converter->shortToLongDate($ld->processing_fee_date_time_paid); ?></td>
						<td><?php echo $ld->processing_fee_balance; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #loan-details-tbl -->
		</section><!-- #loan-details -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/loan-details.js"></script>
</body>
</html>