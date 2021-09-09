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
	$principal_payments = $transaction->getPrincipalPayments($id);
	$interests = $transaction->getInterests($id);
	$interest_payments = $transaction->getInterestPayments($id);
	$processing_fees = $transaction->getProcessingFees($id);
	$processing_fee_payments = $transaction->getProcessingFeePayments($id);
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
	<link rel="stylesheet" type="text/css" href="css/tingle.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/vertical-nav-bar.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
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

		<section id="principal-payments">
			<h3>Principal Payments</h3>
			<hr />
			<table id="principal-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Balance</th>
						<th>Payment</th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($principal_payments as $pp): ?>
					<tr>
						<td>0</td>
						<td><?php echo number_format($pp->amount, 2, ".", ","); ?></td>
						<td><?php echo $converter->shortToLongDate($pp->date_time_paid); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #principal-payments-tbl -->
		</section><!-- #principal-payments -->

		<section id="interests">
			<h3>Interests</h3>
			<hr />
			<table id="interests-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Interest Date</th>
						<th>Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($interests as $i): ?>
					<tr>
						<td><?php echo $i->interest_id; ?></td>
						<td><?php echo $converter->shortToLongDate($i->interest_date); ?></td>
						<td><?php echo number_format($i->amount, 2, ".", ","); ?></td>
						<?php if ($i->status == "Paid" || $i->status == "Late"): ?>
						<td><?php echo $i->status; ?></td>
						<?php else: ?>
						<td>
							<a 
								href="#" 
								data-loan-id="<?php echo $id; ?>" 
								data-interest-id="<?php echo $i->interest_id; ?>"
								data-interest-balance="<?php echo number_format($transaction->getInterestBalance($id, $i->interest_id), 2, ".", ","); ?>"
								>
								<?php echo $i->status; ?>
							</a>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #interests-tbl -->
		</section><!-- #interests -->

		<section id="interest-payments">
			<h3>Interest Payments</h3>
			<hr />
			<table id="interest-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Interest Date</th>
						<th>Amount</th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($interest_payments as $ip): ?>
					<tr>
						<td><?php echo $converter->shortToLongDate($ip->interest_date); ?></td>
						<td><?php echo number_format($ip->amount, 2, ".", ","); ?></td>
						<td><?php echo $converter->shortToLongDate($ip->date_time_paid); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #interest-payments-tbl -->
		</section><!-- #interest-payments -->

		<section id="processing-fees">
			<h3>Processing Fees</h3>
			<hr />
			<table id="processing-fees-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>ID</th>
						<th>Fee Date</th>
						<th>Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processing_fees as $pf): ?>
					<tr>
						<td><?php echo $pf->processing_fee_id; ?></td>
						<td><?php echo $converter->shortToLongDate($pf->processing_fee_date); ?></td>
						<td><?php echo number_format($pf->amount, 2, ".", ","); ?></td>
						<?php if ($pf->status == "Paid"): ?>
						<td><?php echo $pf->status; ?></td>
						<?php else: ?>
						<td>
							<a 
								href="#" 
								data-loan-id="<?php echo $id; ?>" 
								data-processing-fee-id="<?php echo $pf->processing_fee_id; ?>"
								>
								<?php echo $pf->status; ?>
							</a>
						</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #processing-fees-tbl -->
		</section><!-- #processing-fees -->

		<section id="processing-fee-payments">
			<h3>Processing Fee Payments</h3>
			<hr />
			<table id="processing-fee-payments-tbl" class="display cell-border" width="100%">
				<thead>
					<tr>
						<th>Fee Date</th>
						<th>Amount</th>
						<th>Date Paid</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($processing_fee_payments as $pfp): ?>
					<tr>
						<td><?php echo $converter->shortToLongDate($pfp->processing_fee_date); ?></td>
						<td><?php echo number_format($pfp->amount, 2, ".", ","); ?></td>
						<td><?php echo $converter->shortToLongDate($pfp->date_time_paid); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table><!-- #processing-fee-payments-tbl -->
		</section><!-- #processing-fee-payments -->
	</main>

	<script src="js/jquery-3.6.0.min.js"></script>
	<script src="js/vertical-nav-bar.js"></script>
	<script src="js/datatables.min.js"></script>
	<script src="js/tingle.min.js"></script>
	<script src="js/modal.js"></script>
	<script src="js/loan-details.js"></script>
</body>
</html>